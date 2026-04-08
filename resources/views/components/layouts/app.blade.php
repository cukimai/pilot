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
    <script>
        var vapiBtn = document.getElementById('vapi-call-btn');
        var vapiWidget = document.createElement('script');
        vapiWidget.src = "https://cdn.jsdelivr.net/gh/VapiAI/html-script-tag@latest/dist/assets/index.js";
        vapiWidget.onload = function() {
            window.vapiSDK.run({
                apiKey: "feaf70be-7b43-42d4-b4fd-8cc013b059a2",
                assistant: "ae1fb1db-b24f-441a-a0ba-4c6bd17a740c",
                config: {
                    position: "bottom-left",
                    offset: "20px",
                    width: "50px",
                    height: "50px",
                    idle: {
                        color: "rgb(34, 197, 94)",
                        type: "pill",
                        title: "Bel ons",
                        subtitle: "AI-assistent",
                        icon: "https://unpkg.com/lucide-static@0.321.0/icons/phone.svg",
                    },
                    loading: {
                        color: "rgb(234, 179, 8)",
                        type: "pill",
                        title: "Verbinden...",
                        subtitle: "Even geduld",
                        icon: "https://unpkg.com/lucide-static@0.321.0/icons/loader-2.svg",
                    },
                    active: {
                        color: "rgb(239, 68, 68)",
                        type: "pill",
                        title: "Gesprek actief",
                        subtitle: "Klik om op te hangen",
                        icon: "https://unpkg.com/lucide-static@0.321.0/icons/phone-off.svg",
                    },
                },
            });
        };
        document.body.appendChild(vapiWidget);
    </script>
</body>
</html>
