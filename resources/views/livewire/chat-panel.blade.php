<div class="flex gap-6" x-data="{ pendingMessage: '', isLoading: false }">
    {{-- Main Chat Panel --}}
    <div class="flex-1 bg-white/60 backdrop-blur-sm rounded-2xl border border-gray-200/40 overflow-hidden shadow-sm">
        {{-- Messages Area --}}
        <div class="h-[500px] overflow-y-auto p-6 space-y-4" id="messages-container">
            @forelse($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] rounded-2xl px-4 py-3 {{ $msg['role'] === 'user' ? 'bg-jarvis-500 text-white' : 'bg-white border border-gray-200/60 text-gray-800' }} {{ isset($msg['error']) && $msg['error'] ? '!bg-red-50 !text-red-700 !border-red-200' : '' }}">
                        <div class="whitespace-pre-wrap">{{ $msg['content'] }}</div>

                        @if(!empty($msg['changes']))
                            <div class="mt-3 pt-3 border-t {{ $msg['role'] === 'user' ? 'border-jarvis-400' : 'border-gray-100' }}">
                                <span class="text-sm font-medium">Changes made:</span>
                                <ul class="text-sm mt-1 space-y-1">
                                    @foreach($msg['changes'] as $change)
                                        <li class="flex items-center gap-1">
                                            <svg class="w-4 h-4 {{ $msg['role'] === 'user' ? 'text-jarvis-200' : 'text-jarvis-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ $change['file'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div x-show="!pendingMessage" class="h-full flex flex-col items-center justify-center text-center text-gray-500">
                    <div class="text-5xl mb-4">✨</div>
                    <p class="text-lg font-medium text-gray-700 mb-2">Ready to edit</p>
                    <p class="text-gray-500 mb-4">Ask me to change any text on your website.</p>
                    <p class="text-sm text-gray-400">Try: "Change the homepage title to Welcome"</p>
                </div>
            @endforelse

            {{-- Optimistic user message (shown immediately) --}}
            <div x-show="pendingMessage" x-cloak class="flex justify-end">
                <div class="max-w-[80%] rounded-2xl px-4 py-3 bg-jarvis-500 text-white">
                    <div class="whitespace-pre-wrap" x-text="pendingMessage"></div>
                </div>
            </div>

            {{-- Loading indicator (Alpine-controlled) --}}
            <div x-show="isLoading" x-cloak class="flex justify-start">
                <div class="bg-white border border-gray-200/60 rounded-2xl px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-jarvis-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 bg-jarvis-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 bg-jarvis-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                        <span class="text-gray-500 text-sm">Thinking...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="border-t border-gray-200/60 p-4 bg-white/40">
            <form
                x-on:submit.prevent="
                    if (!$wire.message.trim()) return;
                    pendingMessage = $wire.message;
                    isLoading = true;
                    $nextTick(() => {
                        const container = document.getElementById('messages-container');
                        if (container) container.scrollTop = container.scrollHeight;
                    });
                    $wire.sendMessage().then(() => {
                        pendingMessage = '';
                        isLoading = false;
                    });
                "
                class="flex gap-3"
            >
                <input
                    type="text"
                    wire:model="message"
                    placeholder="What would you like to change?"
                    class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-jarvis-500/20 focus:border-jarvis-500 transition-all"
                    x-bind:disabled="isLoading"
                >
                <button
                    type="submit"
                    class="bg-jarvis-500 hover:bg-jarvis-600 text-white px-6 py-3 rounded-xl font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed min-w-[100px] flex items-center justify-center"
                    x-bind:disabled="isLoading"
                >
                    <span x-show="!isLoading">Send</span>
                    <span x-show="isLoading" x-cloak>
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>

            <div class="mt-3 flex justify-between items-center">
                @if(count($messages) > 0)
                    <button
                        wire:click="clearHistory"
                        class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        Clear chat
                    </button>
                @else
                    <span></span>
                @endif

                <button
                    wire:click="toggleHistory"
                    class="text-sm text-jarvis-600 hover:text-jarvis-700 flex items-center gap-1.5 transition-colors"
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
        <div class="w-80 bg-white/60 backdrop-blur-sm rounded-2xl border border-gray-200/40 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-gray-200/60 bg-white/40">
                <h3 class="font-semibold text-gray-800">Change History</h3>
            </div>
            <div class="h-[500px] overflow-y-auto">
                @forelse($history as $change)
                    <div class="p-4 border-b border-gray-100 hover:bg-white/60 transition-colors">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $change['file'] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $change['summary'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $change['date'] }}</p>
                            </div>
                            <button
                                wire:click="undoChange({{ $change['id'] }})"
                                class="text-xs bg-gray-100 hover:bg-jarvis-50 hover:text-jarvis-700 text-gray-600 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap"
                                title="Revert to before this change"
                            >
                                Undo
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <div class="text-3xl mb-2">📝</div>
                        <p class="text-gray-500 text-sm">No changes yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('message-sent', () => {
        setTimeout(() => {
            const container = document.getElementById('messages-container');
            if (container) container.scrollTop = container.scrollHeight;
        }, 100);
    });
</script>
@endscript
