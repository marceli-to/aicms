<?php

namespace MarceliTo\Aicms\Http\Livewire;

use Livewire\Component;
use MarceliTo\Aicms\Services\ContentEditor;

class ChatPanel extends Component
{
    public string $message = '';
    public array $messages = [];
    public bool $isLoading = false;

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

        try {
            $editor = app(ContentEditor::class);
            $response = $editor->processMessage($userMessage, $this->messages);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response['message'],
                'changes' => $response['changes'] ?? [],
            ];
        } catch (\Exception $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Error: ' . $e->getMessage(),
                'error' => true,
            ];
        }

        $this->isLoading = false;
    }

    public function clearHistory(): void
    {
        $this->messages = [];
    }

    public function render()
    {
        return view('aicms::livewire.chat-panel');
    }
}
