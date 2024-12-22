<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script>
        if (localStorage.getItem('dark-mode') === 'true' || (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.querySelector('html').classList.add('dark');
        } else {
            document.querySelector('html').classList.remove('dark');
        }
    </script>

    <script>
        // Define the default import map and the Opera-specific import map
        const isOpera = navigator.userAgent.includes("OPR");

        const defaultImportMap = {
            imports: {
                "https://esm.sh/v135/prosemirror-model@1.21.0/es2022/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2022/prosemirror-model.mjs",
                "https://esm.sh/v135/prosemirror-model@1.22.1/es2022/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2022/prosemirror-model.mjs",
                "https://esm.sh/v135/prosemirror-model@1.22.2/es2022/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2022/prosemirror-model.mjs",
                "https://esm.sh/v135/prosemirror-model@1.22.3/es2022/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2022/prosemirror-model.mjs",
                "https://esm.sh/v135/prosemirror-model@1.23.0/es2022/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2022/prosemirror-model.mjs"
            }
        };

        const operaImportMap = {
            imports: {
                "https://esm.sh/v135/prosemirror-model@1.21.3/es2021/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2021/prosemirror-model.mjs",
                "https://esm.sh/v135/prosemirror-model@1.22.1/es2021/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2021/prosemirror-model.mjs",
                "https://esm.sh/v135/prosemirror-model@1.22.3/es2021/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2021/prosemirror-model.mjs",
                "https://esm.sh/v135/prosemirror-model@1.23.0/es2021/prosemirror-model.mjs": "https://esm.sh/v135/prosemirror-model@1.19.3/es2021/prosemirror-model.mjs"
            }
        };

        const importMap = isOpera ? operaImportMap : defaultImportMap;

        const script = document.createElement('script');
        script.type = 'importmap';
        script.textContent = JSON.stringify(importMap);
        document.head.appendChild(script);
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>