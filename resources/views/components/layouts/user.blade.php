<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SIPENA-IDX' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-16 items-center">
            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}" class="font-bold text-lg text-slate-900 tracking-tight">SIPENA-IDX</a>
                <nav class="hidden md:flex space-x-4 text-sm font-medium text-slate-600">
                    <a href="{{ route('user.dashboard') }}" class="hover:text-slate-900">Dashboard</a>
                    <a href="{{ route('user.kegiatan.index') }}" class="hover:text-slate-900">Kegiatan</a>
                    <a href="{{ route('user.kegiatan-saya') }}" class="hover:text-slate-900">Kegiatan Saya</a>
                    <a href="{{ route('user.laporan.index') }}" class="hover:text-slate-900">Laporan</a>
                    <a href="{{ route('user.notifikasi.index') }}" class="hover:text-slate-900">Notifikasi</a>
                </nav>
            </div>
            <div class="flex items-center space-x-4 text-sm">
                <a href="{{ route('user.profil.edit') }}" class="font-medium text-slate-700 hover:text-slate-900">{{ auth('web')->user()?->name ?? 'Profil' }}</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{ $slot }}
    </main>
</body>
</html>
