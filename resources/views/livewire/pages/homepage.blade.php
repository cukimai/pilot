<div>
    {{-- Navigation --}}
    <nav class="fixed top-0 z-40 w-full border-b border-white/10 bg-white/80 backdrop-blur-xl">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between px-6 lg:px-8">
            <div class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-black">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                    </svg>
                </div>
                <span class="text-lg font-semibold tracking-tight">{{ $companyName }}</span>
            </div>
            <div class="hidden items-center gap-8 md:flex">
                <a href="#diensten" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Diensten</a>
                <a href="#over-ons" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Over ons</a>
                <a href="#faq" class="text-sm text-gray-500 transition-colors hover:text-gray-900">FAQ</a>
                <a href="#contact" class="text-sm text-gray-500 transition-colors hover:text-gray-900">Contact</a>
            </div>
            <div class="flex items-center gap-3">
                @if($companyPhone)
                    <a href="tel:{{ $companyPhone }}" class="hidden items-center gap-2 text-sm text-gray-600 transition-colors hover:text-gray-900 sm:flex">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                        {{ $companyPhone }}
                    </a>
                @endif
                <button
                    onclick="document.querySelector('[wire\\:click=toggle]')?.click()"
                    class="rounded-full bg-black px-5 py-2.5 text-sm font-medium text-white transition-all hover:bg-gray-800 hover:shadow-lg"
                >
                    Chat met ons
                </button>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative overflow-hidden pb-16 pt-32 lg:pb-24 lg:pt-40">
        {{-- Background gradient --}}
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-slate-50 via-white to-blue-50"></div>
        <div class="absolute right-0 top-0 z-0 h-full w-1/2 bg-gradient-to-l from-blue-100/40 to-transparent"></div>
        <div class="absolute -right-40 -top-40 z-0 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-amber-100/30 to-orange-100/20 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 z-0 h-[400px] w-[400px] rounded-full bg-gradient-to-tr from-blue-100/20 to-cyan-100/10 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-2xl py-20">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white/80 px-4 py-1.5 text-sm text-gray-600 backdrop-blur-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                    </span>
                    24/7 bereikbaar via onze AI-assistent
                </div>

                <h1 class="text-5xl font-bold leading-[1.1] tracking-tight text-gray-900 md:text-7xl">
                    Uw installatie<br>
                    <span class="bg-gradient-to-r from-gray-900 to-gray-500 bg-clip-text text-transparent">in vertrouwde<br>handen.</span>
                </h1>

                <p class="mt-6 max-w-lg text-lg leading-relaxed text-gray-600">
                    Van CV-ketel onderhoud tot airconditioning en zonnepanelen.
                    Wij staan voor u klaar met vakmanschap en een persoonlijke aanpak.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <button
                        onclick="document.querySelector('[wire\\:click=toggle]')?.click()"
                        class="group inline-flex items-center gap-2 rounded-full bg-black px-8 py-4 text-sm font-medium text-white transition-all hover:bg-gray-800 hover:shadow-xl"
                    >
                        Stel uw vraag
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                    @if($companyPhone)
                        <a href="tel:{{ $companyPhone }}" class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-8 py-4 text-sm font-medium text-gray-700 transition-all hover:border-gray-400 hover:shadow-md">
                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            {{ $companyPhone }}
                        </a>
                    @endif
                </div>

                {{-- Trust indicators --}}
                <div class="mt-14 flex flex-wrap items-center gap-8 border-t border-gray-200 pt-8">
                    <div class="flex items-center gap-2">
                        <div class="flex -space-x-1">
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-400 text-[10px]">&#9733;</div>
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-400 text-[10px]">&#9733;</div>
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-400 text-[10px]">&#9733;</div>
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-400 text-[10px]">&#9733;</div>
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-yellow-400 text-[10px]">&#9733;</div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">4.9/5</span>
                        <span class="text-sm text-gray-400">Google Reviews</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                        Erkend installateur
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        15+ jaar ervaring
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Diensten --}}
    @if(count($services) > 0)
        <section id="diensten" class="bg-white py-24 lg:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-semibold uppercase tracking-widest text-gray-400">Wat wij doen</p>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 lg:text-5xl">Onze diensten</h2>
                    <p class="mt-4 text-lg text-gray-500">Vakmanschap voor al uw installatiebehoefte</p>
                </div>

                @php
                    $serviceGradients = [
                        'from-orange-500 to-red-600',
                        'from-cyan-500 to-blue-600',
                        'from-yellow-400 to-orange-500',
                        'from-emerald-500 to-teal-600',
                    ];
                    $serviceIcons = [
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />',
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />',
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />',
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />',
                    ];
                @endphp

                <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    @foreach($services as $index => $service)
                        <div class="group overflow-hidden rounded-2xl border border-gray-100 bg-white transition-all hover:-translate-y-1 hover:shadow-xl">
                            <div class="flex aspect-[5/3] items-center justify-center bg-gradient-to-br {{ $serviceGradients[$index % count($serviceGradients)] }}">
                                <svg class="h-16 w-16 text-white/90" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    {!! $serviceIcons[$index % count($serviceIcons)] !!}
                                </svg>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $service['question'] }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-gray-500">{!! strip_tags($service['answer']) !!}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Over ons --}}
    <section id="over-ons" class="overflow-hidden bg-gray-50 py-24 lg:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid items-center gap-16 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-gray-400">Wie wij zijn</p>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 lg:text-5xl">Over ons</h2>

                    @if(count($aboutInfo) > 0)
                        <div class="mt-8 space-y-6">
                            @foreach($aboutInfo as $info)
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $info['question'] }}</h3>
                                    <p class="mt-2 leading-relaxed text-gray-600">{!! strip_tags($info['answer']) !!}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-6 text-lg leading-relaxed text-gray-600">
                            Met jarenlange ervaring in de installatietechniek staan wij garant voor kwaliteit en betrouwbaarheid.
                            Ons team van gecertificeerde monteurs staat altijd voor u klaar.
                        </p>
                    @endif

                    <div class="mt-10 grid grid-cols-3 gap-8">
                        <div>
                            <div class="text-3xl font-bold text-gray-900">15+</div>
                            <div class="mt-1 text-sm text-gray-500">Jaar ervaring</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-900">2500+</div>
                            <div class="mt-1 text-sm text-gray-500">Tevreden klanten</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-900">24/7</div>
                            <div class="mt-1 text-sm text-gray-500">Bereikbaar</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="flex aspect-[4/5] items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900">
                        <svg class="h-32 w-32 text-white/10" fill="none" viewBox="0 0 24 24" stroke-width="0.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                        </svg>
                    </div>
                    <div class="absolute -bottom-6 -left-6 rounded-2xl bg-white p-6 shadow-xl">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Erkend & gecertificeerd</div>
                                <div class="text-sm text-gray-500">Vakbekwaam installateur</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Waarom wij --}}
    <section class="bg-black py-24 lg:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-4xl font-bold tracking-tight text-white lg:text-5xl">Waarom kiezen voor ons?</h2>
                <p class="mt-4 text-lg text-gray-400">Wij combineren vakmanschap met moderne technologie</p>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-8 backdrop-blur-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-white/10">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Snelle response</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-400">Bij urgente storingen streven wij naar een bezoek binnen 4 uur. Onze AI-assistent is 24/7 bereikbaar.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-8 backdrop-blur-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-white/10">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Garantie op werk</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-400">Op al onze werkzaamheden geven wij garantie. Kwaliteit en duurzaamheid staan voorop.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-8 backdrop-blur-sm">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-white/10">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Transparante prijzen</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-400">Geen verrassingen achteraf. U ontvangt altijd vooraf een duidelijke offerte met vaste prijsafspraken.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    @if(count($faq) > 0)
        <section id="faq" class="bg-white py-24 lg:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-16 lg:grid-cols-2">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-gray-400">Veelgestelde vragen</p>
                        <h2 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 lg:text-5xl">FAQ</h2>
                        <p class="mt-4 text-lg text-gray-500">Heeft u een andere vraag? Start een chat met onze AI-assistent.</p>
                        <button
                            onclick="document.querySelector('[wire\\:click=toggle]')?.click()"
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition-all hover:bg-gray-800"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                            </svg>
                            Stel uw vraag
                        </button>
                    </div>
                    <div class="space-y-4" x-data="{ open: null }">
                        @foreach($faq as $index => $item)
                            <div class="rounded-2xl border border-gray-200 transition-colors" :class="open === {{ $index }} ? 'bg-gray-50' : 'bg-white'">
                                <button
                                    class="flex w-full items-center justify-between px-6 py-5 text-left"
                                    @click="open = open === {{ $index }} ? null : {{ $index }}"
                                >
                                    <span class="pr-4 font-medium text-gray-900">{{ $item['question'] }}</span>
                                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform" :class="open === {{ $index }} && 'rotate-45'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                                <div x-show="open === {{ $index }}" x-collapse>
                                    <div class="px-6 pb-5 text-sm leading-relaxed text-gray-600">
                                        {!! strip_tags($item['answer']) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- CTA Banner --}}
    <section class="relative overflow-hidden bg-gray-900 py-24">
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900"></div>
        <div class="absolute -left-40 -top-40 z-0 h-[500px] w-[500px] rounded-full bg-amber-500/5 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 z-0 h-[500px] w-[500px] rounded-full bg-blue-500/5 blur-3xl"></div>
        <div class="relative z-10 mx-auto max-w-7xl px-6 text-center lg:px-8">
            <h2 class="text-4xl font-bold tracking-tight text-white lg:text-5xl">Storing of onderhoud nodig?</h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-300">
                Neem direct contact op via onze chat of bel ons. Wij helpen u snel en vakkundig.
            </p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <button
                    onclick="document.querySelector('[wire\\:click=toggle]')?.click()"
                    class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-4 text-sm font-medium text-gray-900 transition-all hover:bg-gray-100 hover:shadow-xl"
                >
                    Chat starten
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>
                @if($companyPhone)
                    <a href="tel:{{ $companyPhone }}" class="inline-flex items-center gap-2 rounded-full border border-white/30 px-8 py-4 text-sm font-medium text-white transition-all hover:border-white/60 hover:bg-white/10">
                        Bel {{ $companyPhone }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Contact --}}
    <section id="contact" class="bg-white py-24 lg:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-semibold uppercase tracking-widest text-gray-400">Neem contact op</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 lg:text-5xl">Contact</h2>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-3">
                @if($companyPhone)
                    <a href="tel:{{ $companyPhone }}" class="group rounded-2xl border border-gray-200 p-8 text-center transition-all hover:-translate-y-1 hover:border-gray-300 hover:shadow-lg">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50 transition-colors group-hover:bg-black">
                            <svg class="h-6 w-6 text-gray-600 transition-colors group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Telefoon</h3>
                        <p class="mt-1 text-gray-500">{{ $companyPhone }}</p>
                        <p class="mt-1 text-xs text-gray-400">AI-assistent 24/7 bereikbaar</p>
                    </a>
                @endif
                @if($companyEmail)
                    <a href="mailto:{{ $companyEmail }}" class="group rounded-2xl border border-gray-200 p-8 text-center transition-all hover:-translate-y-1 hover:border-gray-300 hover:shadow-lg">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50 transition-colors group-hover:bg-black">
                            <svg class="h-6 w-6 text-gray-600 transition-colors group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">E-mail</h3>
                        <p class="mt-1 text-gray-500">{{ $companyEmail }}</p>
                    </a>
                @endif
                @if($companyAddress)
                    <div class="group rounded-2xl border border-gray-200 p-8 text-center transition-all hover:-translate-y-1 hover:border-gray-300 hover:shadow-lg">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50 transition-colors group-hover:bg-black">
                            <svg class="h-6 w-6 text-gray-600 transition-colors group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Adres</h3>
                        <p class="mt-1 text-gray-500">{{ $companyAddress }}</p>
                    </div>
                @endif
            </div>

            @if(count($workArea) > 0)
                <div class="mt-16 rounded-2xl bg-gray-50 p-8 text-center">
                    <h3 class="font-semibold text-gray-900">Werkgebied</h3>
                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        @foreach($workArea as $area)
                            <span class="rounded-full border border-gray-200 bg-white px-5 py-2 text-sm text-gray-600 shadow-sm">{{ $area['question'] }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-100 bg-white py-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-black">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">{{ $companyName }}</span>
                </div>
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} {{ $companyName }}. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>

    {{-- Voice Call Button --}}
    <button
        id="vapi-call-btn"
        onclick="startVapiCall()"
        class="fixed bottom-6 left-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-green-500 text-white shadow-lg transition-transform hover:scale-105"
        title="Bel met onze AI-assistent"
    >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
        </svg>
    </button>

    {{-- Chat Widget --}}
    <livewire:chat-widget />
</div>
