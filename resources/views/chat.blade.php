<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('aicms.title', 'AI CMS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        jarvis: {
                            50: '#e0f7fa',
                            100: '#b2ebf2',
                            200: '#80deea',
                            300: '#4dd0e1',
                            400: '#26c6da',
                            500: '#00bcd4',
                            600: '#00acc1',
                            700: '#0097a7',
                            800: '#00838f',
                            900: '#006064',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .orb {
            position: absolute;
            border-radius: 50%;
            opacity: 0.15;
            filter: blur(120px);
            pointer-events: none;
        }
        .orb-1 {
            width: 500px;
            height: 500px;
            top: -150px;
            left: -150px;
            background: linear-gradient(135deg, #00bcd4, #0097a7);
            animation: float1 25s ease-in-out infinite;
        }
        .orb-2 {
            width: 400px;
            height: 400px;
            top: 50%;
            right: -100px;
            background: linear-gradient(135deg, #4dd0e1, #80deea);
            animation: float2 30s ease-in-out infinite;
        }
        .orb-3 {
            width: 300px;
            height: 300px;
            bottom: 10%;
            left: 20%;
            background: linear-gradient(135deg, #26c6da, #00acc1);
            animation: float3 20s ease-in-out infinite;
        }
        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(80px, 40px) scale(1.1); }
            66% { transform: translate(40px, 80px) scale(0.95); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-60px, 40px) scale(1.05); }
            66% { transform: translate(-30px, -30px) scale(0.9); }
        }
        @keyframes float3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(40px, -30px) scale(1.08); }
            66% { transform: translate(-20px, 20px) scale(0.92); }
        }
    </style>
    @livewireStyles
</head>
<body class="antialiased font-sans bg-gray-100 text-gray-900 min-h-screen">
    {{-- Background orbs --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="py-6">
            <div class="max-w-5xl mx-auto px-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">✨</span>
                    <h1 class="text-xl font-bold text-gray-900">{{ config('aicms.title', 'AI CMS') }}</h1>
                </div>
                <div class="flex items-center gap-2 px-3 py-1 bg-white/60 backdrop-blur-sm rounded-full text-sm text-gray-600 border border-gray-200/40">
                    <span class="w-2 h-2 bg-jarvis-500 rounded-full animate-pulse"></span>
                    Ready
                </div>
            </div>
        </header>

        {{-- Main content --}}
        <main class="flex-1 py-6">
            <div class="max-w-5xl mx-auto px-6">
                <livewire:aicms-chat-panel />
            </div>
        </main>

        {{-- Footer --}}
        <footer class="py-6">
            <div class="max-w-5xl mx-auto px-6 text-center text-sm text-gray-500">
                Powered by AI
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
