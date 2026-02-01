<div class="bg-white rounded-lg shadow-lg overflow-hidden">
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

        @if($isLoading)
            <div class="flex justify-start">
                <div class="bg-gray-100 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Input Area --}}
    <div class="border-t p-4">
        <form wire:submit.prevent="sendMessage" class="flex space-x-2">
            <input
                type="text"
                wire:model="message"
                placeholder="What would you like to change?"
                class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500"
                @if($isLoading) disabled @endif
            >
            <button
                type="submit"
                class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                @if($isLoading) disabled @endif
            >
                Send
            </button>
        </form>

        @if(count($messages) > 0)
            <div class="mt-2 text-right">
                <button
                    wire:click="clearHistory"
                    class="text-sm text-gray-500 hover:text-gray-700"
                >
                    Clear history
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    // Auto-scroll to bottom on new messages
    document.addEventListener('livewire:updated', () => {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    });
</script>
