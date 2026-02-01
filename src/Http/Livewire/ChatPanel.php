<?php

namespace MarceliTo\Aicms\Http\Livewire;

use Livewire\Component;
use MarceliTo\Aicms\Services\ContentEditor;

class ChatPanel extends Component
{
    public string $message = '';
    public array $messages = [];
    public bool $isLoading = false;
    public string $loadingStatus = '';
    public bool $showHistory = false;
    public array $history = [];

    public function sendMessage(): void
    {
        if (empty(trim($this->message))) {
            return;
        }

        $userMessage = $this->message;
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];
        $this->message = '';
        $this->isLoading = true;
        $this->loadingStatus = 'Thinking...';

        try {
            $editor = app(ContentEditor::class);
            $response = $editor->processMessage($userMessage, $this->messages);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response['message'],
                'changes' => $response['changes'] ?? [],
            ];

            // Refresh history if we made changes
            if (!empty($response['changes'])) {
                $this->loadHistory();
            }
        } catch (\Exception $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Error: ' . $e->getMessage(),
                'error' => true,
            ];
        }

        $this->isLoading = false;
        $this->loadingStatus = '';
    }

    public function clearHistory(): void
    {
        $this->messages = [];
    }

    public function toggleHistory(): void
    {
        $this->showHistory = !$this->showHistory;
        if ($this->showHistory) {
            $this->loadHistory();
        }
    }

    public function loadHistory(): void
    {
        $editor = app(ContentEditor::class);
        $changes = $editor->getHistory(20);

        $this->history = $changes->map(fn($c) => [
            'id' => $c->id,
            'file' => $c->file_path,
            'summary' => $c->summary ?? 'Content updated',
            'date' => $c->created_at->diffForHumans(),
        ])->toArray();
    }

    public function undoChange(int $changeId): void
    {
        $editor = app(ContentEditor::class);

        if ($editor->undoChange($changeId)) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Change reverted successfully.',
                'changes' => [['file' => 'Reverted', 'action' => 'undo']],
            ];
            $this->loadHistory();
        } else {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Could not revert this change.',
                'error' => true,
            ];
        }
    }

    public function render()
    {
        return view('aicms::livewire.chat-panel');
    }
}
