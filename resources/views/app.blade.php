<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>SIPENA — IDX</title>
    <script>
        (() => {
            try {
                const savedTheme = localStorage.getItem('sipena-theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.dataset.theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            } catch {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="h-full bg-slate-50 antialiased font-sans text-slate-800">
    @inertia
</body>
</html>
