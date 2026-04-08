<div class="fixed bottom-6 right-6 z-50" x-data="{ scrollToBottom() { $nextTick(() => { const el = document.getElementById('chat-messages'); if (el) el.scrollTop = el.scrollHeight; }) } }">
    {{-- Chat Button --}}
    @unless($isOpen)
        <button
            wire:click="toggle"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-black text-white shadow-lg transition-transform hover:scale-105"
        >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
            </svg>
        </button>
    @endunless

    {{-- Chat Window --}}
    @if($isOpen)
        <div class="flex h-[500px] w-[380px] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Klantenservice</h3>
                    <p class="text-xs text-gray-500">Wij helpen u graag</p>
                </div>
                <button wire:click="toggle" class="rounded-full p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Messages --}}
            <div
                id="chat-messages"
                class="flex-1 space-y-3 overflow-y-auto px-5 py-4"
                x-init="scrollToBottom()"
                wire:poll.2s
            >
                @foreach($messages as $message)
                    <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed {{ $message['role'] === 'user' ? 'bg-black text-white' : 'bg-gray-100 text-gray-800' }}">
                            {!! nl2br(e($message['content'])) !!}
                        </div>
                    </div>
                @endforeach

                @if($isTyping)
                    <div class="flex justify-start">
                        <div class="rounded-2xl bg-gray-100 px-4 py-3">
                            <div class="flex space-x-1.5">
                                <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 0ms"></div>
                                <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 150ms"></div>
                                <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 300ms"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Input --}}
            <div class="border-t border-gray-100 px-4 py-3">
                <form wire:submit="sendMessage" class="flex items-center gap-2">
                    <input
                        wire:model="input"
                        type="text"
                        placeholder="Typ uw bericht..."
                        class="flex-1 rounded-full border border-gray-200 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 outline-none transition-colors focus:border-gray-400 focus:ring-0"
                        autocomplete="off"
                    />
                    <button
                        type="submit"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-black text-white transition-transform hover:scale-105 disabled:opacity-50"
                        @if($isTyping) disabled @endif
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-50 px-4 py-2 text-center">
                <span class="text-[10px] tracking-wide text-gray-300">Powered by Pilot AI</span>
            </div>
        </div>
    @endif
</div>
