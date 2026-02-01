<div class="flex gap-4" x-data="{ pendingMessage: '' }" x-on:message-sent.window="pendingMessage = ''">
    {{-- Main Chat Panel --}}
    <div class="flex-1 bg-white rounded-lg shadow-lg overflow-hidden">
        {{-- Messages Area --}}
        <div class="h-[500px] overflow-y-auto p-4 space-y-4" id="messages-container">
            @forelse($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] rounded-lg p-3 {{ $msg['role'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800' }} {{ isset($msg['error']) && $msg['error'] ? 'bg-red-100 text-red-800' : '' }}">
                        <div class="whitespace-pre-wrap">{{ $msg['content'] }}</div>

                        @if(!empty($msg['changes']))
                            <div class="mt-2 pt-2 border-t {{ $msg['role'] === 'user' ? 'border-blue-400' : 'border-gray-200' }}">
                                <span class="text-sm font-medium">Changes made:</span>
                                <ul class="text-sm mt-1">
                                    @foreach($msg['changes'] as $change)
                                        <li>✓ {{ $change['file'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    <p class="text-lg mb-2">👋 Hello!</p>
                    <p>Ask me to edit any text on your website.</p>
                    <p class="text-sm mt-2">Try: "Change the homepage title to Welcome"</p>
                </div>
            @endforelse

            {{-- Pending user message - shows immediately on submit --}}
            <template x-if="pendingMessage">
                <div class="flex justify-end">
                    <div class="max-w-[80%] rounded-lg p-3 bg-blue-500 text-white">
                        <div class="whitespace-pre-wrap" x-text="pendingMessage"></div>
                    </div>
                </div>
            </template>

            {{-- Loading indicator - shows immediately on submit --}}
            <div x-show="pendingMessage" class="flex justify-start">
                <div class="bg-gray-100 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                        <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600 text-sm">Thinking...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="border-t p-4">
            <form 
                wire:submit="sendMessage" 
                x-on:submit="pendingMessage = $wire.message; setTimeout(() => { const c = document.getElementById('messages-container'); c.scrollTop = c.scrollHeight; }, 50)"
                x-on:livewire:navigated.window="pendingMessage = ''"
                class="flex space-x-2"
            >
                <input
                    type="text"
                    wire:model="message"
                    placeholder="What would you like to change?"
                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500 disabled:bg-gray-100"
                    x-bind:disabled="pendingMessage !== ''"
                >
                <button
                    type="submit"
                    class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed min-w-[100px]"
                    x-bind:disabled="pendingMessage !== ''"
                >
                    <span x-show="!pendingMessage">Send</span>
                    <span x-show="pendingMessage" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>

            <div class="mt-2 flex justify-between">
                @if(count($messages) > 0)
                    <button
                        wire:click="clearHistory"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        Clear chat
                    </button>
                @else
                    <span></span>
                @endif

                <button
                    wire:click="toggleHistory"
                    class="text-sm text-blue-500 hover:text-blue-700 flex items-center gap-1"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $showHistory ? 'Hide' : 'Show' }} History
                </button>
            </div>
        </div>
    </div>

    {{-- History Panel --}}
    @if($showHistory)
        <div class="w-80 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Change History</h3>
            </div>
            <div class="h-[500px] overflow-y-auto">
                @forelse($history as $change)
                    <div class="p-3 border-b hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $change['file'] }}</p>
                                <p class="text-xs text-gray-500">{{ $change['summary'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $change['date'] }}</p>
                            </div>
                            <button
                                wire:click="undoChange({{ $change['id'] }})"
                                class="ml-2 text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1 rounded"
                                title="Revert to before this change"
                            >
                                Undo
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500 text-sm">
                        No changes yet
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:updated', () => {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
