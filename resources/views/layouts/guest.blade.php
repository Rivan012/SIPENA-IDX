<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIPENA-IDX' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-md border border-slate-200 p-6 sm:p-8">
        {{ $slot }}
    </div>
</body>
</html>
