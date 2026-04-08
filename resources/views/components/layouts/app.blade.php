<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-white text-gray-900 antialiased">
    {{ $slot }}
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/@vapi-ai/web@2/dist/vapi.umd.min.js"></script>
    <script>
        var vapiInstance = null;
        var vapiStarted = false;

        function startVapiCall() {
            var btn = document.getElementById('vapi-call-btn');
            var phoneIcon = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>';
            var hangupIcon = '<svg class="h-6 w-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75 18 6m0 0 2.25 2.25M18 6l2.25-2.25M18 6l-2.25 2.25m1.5 13.5a11.952 11.952 0 0 1-6.682-2.028m0 0a11.952 11.952 0 0 1-4.313-5.654m4.313 5.654-3.263 3.263a1.125 1.125 0 0 1-1.59 0l-.622-.621a3.28 3.28 0 0 1-.879-3.019l.168-.672" /></svg>';

            if (vapiStarted) {
                vapiInstance.stop();
                return;
            }

            if (!vapiInstance) {
                var VapiClass = window.Vapi?.default || window.Vapi;
                vapiInstance = new VapiClass("feaf70be-7b43-42d4-b4fd-8cc013b059a2");

                vapiInstance.on('call-start', function() {
                    btn.innerHTML = hangupIcon;
                    btn.classList.remove('bg-green-500');
                    btn.classList.add('bg-red-500');
                    vapiStarted = true;
                });

                vapiInstance.on('call-end', function() {
                    btn.innerHTML = phoneIcon;
                    btn.classList.remove('bg-red-500');
                    btn.classList.add('bg-green-500');
                    vapiStarted = false;
                });
            }

            vapiInstance.start("ae1fb1db-b24f-441a-a0ba-4c6bd17a740c");
        }
    </script>
</body>
</html>
