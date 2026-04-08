<div>
    {{-- Navigation --}}
    <nav class="fixed top-0 z-40 w-full border-b border-gray-100/50 bg-white/80 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
            <span class="text-lg font-semibold tracking-tight">{{ $companyName }}</span>
            <div class="hidden items-center gap-8 md:flex">
                <a href="#diensten" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Diensten</a>
                <a href="#over-ons" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Over ons</a>
                <a href="#contact" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Contact</a>
            </div>
            @if($companyPhone)
                <a href="tel:{{ $companyPhone }}" class="rounded-full bg-black px-5 py-2 text-sm font-medium text-white transition-transform hover:scale-105">
                    Bel ons
                </a>
            @endif
        </div>
    </nav>

    {{-- Hero --}}
    <section class="flex min-h-[85vh] items-center pt-16">
        <div class="mx-auto max-w-6xl px-6">
            <div class="max-w-2xl">
                <h1 class="text-5xl font-bold leading-tight tracking-tight md:text-7xl">
                    Uw installatie<br>
                    <span class="text-gray-400">in vertrouwde<br>handen.</span>
                </h1>
                <p class="mt-6 text-lg leading-relaxed text-gray-500">
                    Van CV-ketel onderhoud tot airconditioning en zonnepanelen.
                    Wij staan voor u klaar — 24/7 bereikbaar via onze AI-assistent.
                </p>
                <div class="mt-10 flex items-center gap-4">
                    <button
                        onclick="document.querySelector('[wire\\:click=toggle]')?.click()"
                        class="rounded-full bg-black px-8 py-3.5 text-sm font-medium text-white transition-transform hover:scale-105"
                    >
                        Stel uw vraag
                    </button>
                    @if($companyPhone)
                        <a href="tel:{{ $companyPhone }}" class="rounded-full border border-gray-200 px-8 py-3.5 text-sm font-medium text-gray-700 transition-colors hover:border-gray-400">
                            {{ $companyPhone }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Diensten --}}
    @if(count($services) > 0)
        <section id="diensten" class="border-t border-gray-100 py-24">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mb-16">
                    <p class="text-sm font-medium uppercase tracking-widest text-gray-400">Wat wij doen</p>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight">Onze diensten</h2>
                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($services as $service)
                        <div class="rounded-2xl border border-gray-100 p-8 transition-shadow hover:shadow-lg">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50">
                                <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold">{{ $service['question'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-500">{!! strip_tags($service['answer']) !!}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Over ons --}}
    @if(count($aboutInfo) > 0)
        <section id="over-ons" class="border-t border-gray-100 bg-gray-50/50 py-24">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mb-16">
                    <p class="text-sm font-medium uppercase tracking-widest text-gray-400">Wie wij zijn</p>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight">Over ons</h2>
                </div>
                <div class="max-w-3xl space-y-6">
                    @foreach($aboutInfo as $info)
                        <div>
                            <h3 class="text-lg font-semibold">{{ $info['question'] }}</h3>
                            <p class="mt-2 leading-relaxed text-gray-600">{!! strip_tags($info['answer']) !!}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Contact --}}
    <section id="contact" class="border-t border-gray-100 py-24">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mb-16">
                <p class="text-sm font-medium uppercase tracking-widest text-gray-400">Neem contact op</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight">Contact</h2>
            </div>
            <div class="grid gap-12 md:grid-cols-3">
                @if($companyPhone)
                    <div>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50">
                            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold">Telefoon</h3>
                        <a href="tel:{{ $companyPhone }}" class="mt-1 block text-gray-500 transition-colors hover:text-gray-900">{{ $companyPhone }}</a>
                        <p class="mt-1 text-sm text-gray-400">AI-assistent 24/7 bereikbaar</p>
                    </div>
                @endif
                @if($companyEmail)
                    <div>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50">
                            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <h3 class="font-semibold">E-mail</h3>
                        <a href="mailto:{{ $companyEmail }}" class="mt-1 block text-gray-500 transition-colors hover:text-gray-900">{{ $companyEmail }}</a>
                    </div>
                @endif
                @if($companyAddress)
                    <div>
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50">
                            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold">Adres</h3>
                        <p class="mt-1 text-gray-500">{{ $companyAddress }}</p>
                    </div>
                @endif
            </div>

            @if(count($workArea) > 0)
                <div class="mt-16 rounded-2xl border border-gray-100 p-8">
                    <h3 class="font-semibold">Werkgebied</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($workArea as $area)
                            <span class="rounded-full bg-gray-50 px-4 py-1.5 text-sm text-gray-600">{{ $area['question'] }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-100 py-8">
        <div class="mx-auto max-w-6xl px-6 text-center">
            <p class="text-sm text-gray-400">&copy; {{ date('Y') }} {{ $companyName }}. Alle rechten voorbehouden.</p>
        </div>
    </footer>

    {{-- Chat Widget --}}
    <livewire:chat-widget />
</div>
