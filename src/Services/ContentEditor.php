<?php

namespace MarceliTo\Aicms\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ContentEditor
{
    protected array $editableFiles = [];
    protected ?ChangeHistory $changeHistory = null;

    public function __construct(?ChangeHistory $changeHistory = null)
    {
        $this->changeHistory = $changeHistory ?? app(ChangeHistory::class);
        $this->loadEditableFiles();
    }

    protected function loadEditableFiles(): void
    {
        $patterns = config('aicms.editable_paths', []);

        foreach ($patterns as $pattern) {
            $fullPattern = base_path($pattern);
            $files = glob($fullPattern, GLOB_BRACE);

            foreach ($files as $file) {
                if (is_file($file)) {
                    $relativePath = str_replace(base_path() . '/', '', $file);
                    $this->editableFiles[$relativePath] = $file;
                }
            }
        }
    }

    public function getEditableFiles(): array
    {
        return array_keys($this->editableFiles);
    }

    public function readFile(string $relativePath): ?string
    {
        if (!isset($this->editableFiles[$relativePath])) {
            return null;
        }

        return File::get($this->editableFiles[$relativePath]);
    }

    public function writeFile(string $relativePath, string $content, ?string $summary = null): bool
    {
        if (!isset($this->editableFiles[$relativePath])) {
            return false;
        }

        // Record change history
        $before = File::exists($this->editableFiles[$relativePath])
            ? File::get($this->editableFiles[$relativePath])
            : '';

        File::put($this->editableFiles[$relativePath], $content);

        // Save to history
        $this->changeHistory->record($relativePath, $before, $content, $summary);

        return true;
    }

    public function undoChange(int $changeId): bool
    {
        $revert = $this->changeHistory->revert($changeId);

        if (!$revert || !isset($this->editableFiles[$revert['file_path']])) {
            return false;
        }

        // Record this revert as a new change
        $currentContent = File::get($this->editableFiles[$revert['file_path']]);
        File::put($this->editableFiles[$revert['file_path']], $revert['content']);
        $this->changeHistory->record($revert['file_path'], $currentContent, $revert['content'], 'Undo');

        return true;
    }

    public function getHistory(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return $this->changeHistory->getHistory(null, $limit);
    }

    public function processMessage(string $message, array $conversationHistory): array
    {
        set_time_limit(120); // Allow up to 2 minutes for AI processing

        $systemPrompt = $this->buildSystemPrompt();
        $tools = $this->getTools();

        $messages = $this->formatMessages($conversationHistory);
        $messages[] = ['role' => 'user', 'content' => $message];

        $response = $this->callAnthropic($systemPrompt, $messages, $tools);

        return $this->handleResponse($response, $messages);
    }

    protected function buildSystemPrompt(): string
    {
        $files = $this->getEditableFiles();
        $fileList = implode("\n", array_map(fn($f) => "- {$f}", $files));

        return <<<PROMPT
You are an AI content editor for a website. Your job is to help users edit text content on their site.

## Available Files
These are the files you can read and edit:
{$fileList}

## Rules
1. Only edit files from the list above
2. When editing, preserve the file structure (HTML tags, Blade syntax, Markdown formatting)
3. Only change the text content the user asks about
4. Always read a file first before editing it
5. Show the user what you changed
6. Be concise and helpful

## Workflow
1. User asks to change something
2. Use read_file to see the current content
3. Use write_file to make the change
4. Confirm what you did
PROMPT;
    }

    protected function getTools(): array
    {
        return [
            [
                'name' => 'read_file',
                'description' => 'Read the contents of an editable file',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'path' => [
                            'type' => 'string',
                            'description' => 'Relative path to the file',
                        ],
                    ],
                    'required' => ['path'],
                ],
            ],
            [
                'name' => 'write_file',
                'description' => 'Write new content to an editable file',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'path' => [
                            'type' => 'string',
                            'description' => 'Relative path to the file',
                        ],
                        'content' => [
                            'type' => 'string',
                            'description' => 'New file content',
                        ],
                    ],
                    'required' => ['path', 'content'],
                ],
            ],
            [
                'name' => 'list_files',
                'description' => 'List all editable files',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
        ];
    }

    protected function formatMessages(array $history): array
    {
        $formatted = [];

        foreach ($history as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                $formatted[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content'],
                ];
            }
        }

        return $formatted;
    }

    protected function callAnthropic(string $system, array $messages, array $tools): array
    {
        $apiKey = config('aicms.anthropic_key');
        $model = config('aicms.model');

        if (!$apiKey) {
            throw new \Exception('Anthropic API key not configured. Set AICMS_ANTHROPIC_KEY in your .env');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
            'model' => $model,
            'max_tokens' => 4096,
            'system' => $system,
            'messages' => $messages,
            'tools' => $tools,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Anthropic API error: ' . $response->body());
        }

        return $response->json();
    }

    protected function handleResponse(array $response, array $messages): array
    {
        $changes = [];
        $textContent = '';
        $toolResults = [];

        // Process the response content
        foreach ($response['content'] ?? [] as $block) {
            if ($block['type'] === 'text') {
                $textContent .= $block['text'];
            } elseif ($block['type'] === 'tool_use') {
                $toolResult = $this->executeTool($block['name'], $block['input']);
                $toolResults[] = [
                    'type' => 'tool_result',
                    'tool_use_id' => $block['id'],
                    'content' => $toolResult['content'],
                ];

                if ($block['name'] === 'write_file' && $toolResult['success']) {
                    $changes[] = [
                        'file' => $block['input']['path'],
                        'action' => 'updated',
                    ];
                }
            }
        }

        // If there were tool uses, we need to continue the conversation
        if (!empty($toolResults) && $response['stop_reason'] === 'tool_use') {
            // Ensure tool_use inputs are proper objects for JSON encoding
            $assistantContent = array_map(function ($block) {
                if ($block['type'] === 'tool_use' && isset($block['input'])) {
                    $block['input'] = (object) $block['input'];
                }
                return $block;
            }, $response['content']);

            // Build continuation messages properly
            $messages[] = ['role' => 'assistant', 'content' => $assistantContent];
            $messages[] = ['role' => 'user', 'content' => $toolResults];

            $continueResponse = $this->callAnthropic(
                $this->buildSystemPrompt(),
                $messages,
                $this->getTools()
            );

            // Recursively handle in case of more tool calls
            $continued = $this->handleResponse($continueResponse, $messages);
            $textContent = $continued['message'];
            $changes = array_merge($changes, $continued['changes']);
        }

        return [
            'message' => $textContent ?: 'Done.',
            'changes' => $changes,
        ];
    }

    protected function executeTool(string $name, array $input): array
    {
        return match ($name) {
            'read_file' => $this->toolReadFile($input['path']),
            'write_file' => $this->toolWriteFile($input['path'], $input['content']),
            'list_files' => $this->toolListFiles(),
            default => ['success' => false, 'content' => 'Unknown tool'],
        };
    }

    protected function toolReadFile(string $path): array
    {
        $content = $this->readFile($path);

        if ($content === null) {
            return [
                'success' => false,
                'content' => "File not found or not editable: {$path}",
            ];
        }

        return [
            'success' => true,
            'content' => $content,
        ];
    }

    protected function toolWriteFile(string $path, string $content): array
    {
        $success = $this->writeFile($path, $content);

        return [
            'success' => $success,
            'content' => $success ? "File updated: {$path}" : "Failed to write: {$path}",
        ];
    }

    protected function toolListFiles(): array
    {
        return [
            'success' => true,
            'content' => implode("\n", $this->getEditableFiles()),
        ];
    }

}
