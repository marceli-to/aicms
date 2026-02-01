<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('aicms.title', 'AI CMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto max-w-4xl p-4">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ config('aicms.title', 'AI CMS') }}</h1>
            <p class="text-gray-600">Edit your website content using natural language</p>
        </header>

        <livewire:aicms-chat-panel />
    </div>

    @livewireScripts
</body>
</html>
