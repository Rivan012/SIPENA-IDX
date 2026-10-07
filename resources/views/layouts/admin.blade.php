<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin SIPENA-IDX' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-slate-900 text-slate-100 flex-shrink-0 flex flex-col justify-between">
        <div>
            <div class="p-4 border-b border-slate-800">
                <span class="text-xl font-bold tracking-tight text-white">SIPENA-IDX</span>
                <span class="block text-xs text-slate-400">Admin Portal</span>
            </div>
            <nav class="p-3 space-y-1 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200">Dashboard</a>
                <div class="pt-2 text-xs uppercase font-semibold text-slate-500 px-3">Perencanaan</div>
                <a href="{{ route('admin.rencana-kegiatan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200">Rencana Kegiatan</a>
                <a href="{{ route('admin.pengajuan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200">Pengajuan</a>
                <a href="{{ route('admin.kegiatan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200">Kegiatan & Agenda</a>
                <div class="pt-2 text-xs uppercase font-semibold text-slate-500 px-3">Pelaksanaan</div>
                <a href="{{ route('admin.monitoring') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200">Monitoring</a>
                <a href="{{ route('admin.laporan-kegiatan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200">Laporan</a>
                <a href="{{ route('admin.notifikasi.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200">Notifikasi</a>
            </nav>
        </div>
        <div class="p-4 border-t border-slate-800 flex items-center justify-between text-xs">
            <span class="text-slate-300">{{ auth('admin')->user()?->name ?? 'Admin' }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300 font-medium">Keluar</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-8">
        {{ $slot }}
    </main>
</body>
</html>
