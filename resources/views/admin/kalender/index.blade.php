<x-layouts.admin title="Kalender — SIPENA IDX">
    <script>
        function kalenderApp(config) {
            return {
                currentTab: config.currentTab || 'bulan',
                selectedDate: config.initialDate,
                selectedEvent: config.initialEvent,
                events: config.events || [],
                eventsByDate: config.eventsByDate || {},
                createUrl: config.createUrl || '#',

                selectDate(dateStr) {
                    this.selectedDate = dateStr;
                    const list = this.eventsByDate[dateStr] || [];
                    if (list.length > 0) {
                        this.selectedEvent = list[0];
                    } else {
                        this.selectedEvent = null;
                    }
                },

                selectEvent(ev) {
                    if (!ev) return;
                    this.selectedEvent = ev;
                    this.selectedDate = ev.tanggal;
                },

                getDateTitle(dateStr) {
                    if (!dateStr) return 'Pilih Tanggal';
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const parts = dateStr.split('-');
                    if (parts.length !== 3) return dateStr;
                    const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    return `${days[d.getDay()]}, ${parseInt(parts[2])} ${months[d.getMonth()]} ${parts[0]}`;
                }
            };
        }
    </script>

    <div class="space-y-4"
         x-data="kalenderApp({
            initialDate: '{{ $selectedDateString }}',
            initialEvent: {{ Js::from($eventsClient->firstWhere('id', $selectedKegiatan?->id) ?: ($eventsClient->first() ?: null)) }},
            events: {{ Js::from($eventsClient) }},
            eventsByDate: {{ Js::from($eventsByDateClient) }},
            createUrl: '{{ route('admin.kegiatan.create') }}',
            currentTab: '{{ $view }}'
         })">
        {{-- Page Header --}}
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider" aria-label="Breadcrumb">
                    <span>SIPENA</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span>KALENDER</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-700">KALENDER AGENDA EDUKASI</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">
                    Kalender Perencanaan &amp; Jadwal Edukasi
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 max-w-3xl leading-relaxed">
                    Distribusi jadwal sosialisasi pasar modal, aktivasi Galeri Investasi, dan pengingat agenda H-1 terintegrasi seluruh Kantor Perwakilan BEI.
                </p>
            </div>

            {{-- Right Actions: View Tabs + Button Tambah --}}
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                {{-- View Tabs: Bulan / Minggu / List --}}
                <div class="inline-flex p-1 bg-slate-200/70 rounded-lg text-xs font-semibold text-slate-600">
                    <button type="button"
                            @click="currentTab = 'bulan'"
                            :class="currentTab === 'bulan' ? 'bg-[#0B63E5] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-md transition text-xs font-semibold">
                        Bulan
                    </button>
                    <button type="button"
                            @click="currentTab = 'minggu'"
                            :class="currentTab === 'minggu' ? 'bg-[#0B63E5] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-md transition text-xs font-semibold">
                        Minggu
                    </button>
                    <button type="button"
                            @click="currentTab = 'list'"
                            :class="currentTab === 'list' ? 'bg-[#0B63E5] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-md transition text-xs font-semibold">
                        List
                    </button>
                </div>

                {{-- Primary Button: + Tambah Jadwal Baru --}}
                <a href="{{ route('admin.kegiatan.create') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#0B63E5] hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Tambah Jadwal Baru</span>
                </a>
            </div>
        </div>

        {{-- Controls Bar: Month Navigator, Filter Pills, Alerts --}}
        <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 shadow-xs flex flex-col xl:flex-row xl:items-center justify-between gap-3">
            {{-- Left: Month Switcher & "Hari Ini" --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.kalender.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month, 'status' => request('status'), 'view' => request('view')]) }}"
                   class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition"
                   title="Bulan Sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span class="text-sm sm:text-base font-bold text-slate-800 min-w-[140px] text-center">
                    {{ $currentMonthName }}
                </span>
                <a href="{{ route('admin.kalender.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month, 'status' => request('status'), 'view' => request('view')]) }}"
                   class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition"
                   title="Bulan Berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('admin.kalender.index', ['year' => now()->year, 'month' => now()->month]) }}"
                   class="ml-1 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Hari Ini
                </a>
            </div>

            {{-- Center: Status Filter Badges with Counts --}}
            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                @php $activeStatus = request('status', ''); @endphp
                {{-- Semua --}}
                <a href="{{ route('admin.kalender.index', ['year' => $year, 'month' => $month, 'view' => request('view')]) }}"
                   class="px-2.5 py-1 rounded-full font-semibold transition {{ $activeStatus === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua {{ $stats['total'] }}
                </a>

                {{-- Dipublikasikan --}}
                <a href="{{ route('admin.kalender.index', ['year' => $year, 'month' => $month, 'status' => 'dipublikasikan', 'view' => request('view')]) }}"
                   class="px-2.5 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $activeStatus === 'dipublikasikan' ? 'bg-[#0B63E5] text-white' : 'bg-sky-50 text-sky-700 hover:bg-sky-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $activeStatus === 'dipublikasikan' ? 'bg-white' : 'bg-[#0B63E5]' }}"></span>
                    Dipublikasikan {{ $stats['dipublikasikan'] }}
                </a>

                {{-- Approval --}}
                <a href="{{ route('admin.kalender.index', ['year' => $year, 'month' => $month, 'status' => 'approval', 'view' => request('view')]) }}"
                   class="px-2.5 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $activeStatus === 'approval' ? 'bg-red-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $activeStatus === 'approval' ? 'bg-white' : 'bg-red-600' }}"></span>
                    Approval {{ $stats['approval'] }}
                </a>

                {{-- Selesai --}}
                <a href="{{ route('admin.kalender.index', ['year' => $year, 'month' => $month, 'status' => 'selesai', 'view' => request('view')]) }}"
                   class="px-2.5 py-1 rounded-full font-semibold inline-flex items-center gap-1.5 transition {{ $activeStatus === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $activeStatus === 'selesai' ? 'bg-white' : 'bg-emerald-600' }}"></span>
                    Selesai {{ $stats['selesai'] }}
                </a>
            </div>

            {{-- Right: Alerts Pills --}}
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 font-medium border border-blue-100">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ $stats['pekan_ini'] }} Agenda Pekan Ini</span>
                </div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-800 font-medium border border-rose-100">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                    <span>{{ $stats['h_minus_1'] }} H-1 Perlu Konfirmasi</span>
                </div>
            </div>
        </div>

        {{-- Main Layout: 60% Calendar Grid (Left) + 40% Selected Event Details (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            {{-- Left Column: Calendar (col-span-7) --}}
            <div class="lg:col-span-7 flex flex-col space-y-3">
                {{-- TAB: BULAN --}}
                <div x-show="currentTab === 'bulan'" class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between">
                    {{-- 6 Columns Grid: SENIN, SELASA, RABU, KAMIS, JUMAT, SABTU --}}
                    <div class="overflow-x-auto">
                        <div class="min-w-[560px]">
                            {{-- Day Header --}}
                            <div class="grid grid-cols-6 border-b border-slate-200 bg-slate-50/70 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <div class="py-2.5 px-1 border-r border-slate-200">SENIN</div>
                                <div class="py-2.5 px-1 border-r border-slate-200">SELASA</div>
                                <div class="py-2.5 px-1 border-r border-slate-200">RABU</div>
                                <div class="py-2.5 px-1 border-r border-slate-200">KAMIS</div>
                                <div class="py-2.5 px-1 border-r border-slate-200 text-red-600 font-extrabold">JUMAT</div>
                                <div class="py-2.5 px-1">SABTU</div>
                            </div>

                            {{-- Calendar Week Rows --}}
                            <div class="divide-y divide-slate-200">
                                @foreach($calendarWeeks as $week)
                                    <div class="grid grid-cols-6 divide-x divide-slate-200 min-h-[92px]">
                                        @foreach($week as $dayData)
                                            @php
                                                $isCurrentMonth = $dayData['is_current_month'];
                                                $dateString = $dayData['date_string'];
                                                $dayEvents = $dayData['events'];
                                                $hasH1 = $dayEvents->contains(fn($e) => $e->agenda->contains('is_h_minus_1', true));
                                            @endphp

                                            <div @click="selectDate('{{ $dateString }}')"
                                                 class="p-1.5 flex flex-col justify-between transition relative cursor-pointer select-none
                                                        {{ !$isCurrentMonth ? 'bg-rose-50/40 text-slate-400' : 'bg-white hover:bg-slate-50/60' }}"
                                                 :class="{
                                                     'border-2 border-[#0B63E5] shadow-xs bg-blue-50/20 z-10': selectedDate === '{{ $dateString }}'
                                                 }"
                                                 data-date="{{ $dateString }}">

                                                {{-- Cell Top: Date Number & Alert Icon --}}
                                                <div class="flex items-center justify-between leading-none mb-1">
                                                    <span :class="selectedDate === '{{ $dateString }}' ? 'px-1.5 py-0.5 rounded bg-[#0B63E5] text-white font-bold text-[10px]' : 'text-[11px] font-semibold {{ $isCurrentMonth ? 'text-slate-700' : 'text-slate-400' }}'">
                                                        {{ $dayData['day'] }}
                                                        @if(!$isCurrentMonth && $dayData['day'] <= 7)
                                                            <span class="text-[9px] uppercase font-normal text-slate-400">{{ $dayData['date']->format('M') }}</span>
                                                        @elseif(!$isCurrentMonth && $dayData['day'] >= 24)
                                                            <span class="text-[9px] uppercase font-normal text-slate-400">{{ $dayData['date']->format('M') }}</span>
                                                        @endif
                                                    </span>

                                                    @if ($hasH1)
                                                        <svg class="w-3 h-3 text-red-600 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                                        </svg>
                                                    @endif
                                                </div>

                                                {{-- Event Chips in Cell --}}
                                                <div class="space-y-1 my-auto">
                                                    @foreach($dayEvents->take(2) as $ev)
                                                        @php
                                                            $isApproval = ($ev->status === 'menunggu_approval');
                                                            $isSelesai = ($ev->status === 'selesai');
                                                            $hasHMinus1 = $ev->agenda->contains('is_h_minus_1', true) || $ev->tanggal->isTomorrow();
                                                        @endphp
                                                        <button type="button"
                                                                @click.stop="selectEvent(events.find(e => e.id === {{ $ev->id }}))"
                                                                class="w-full text-left block px-1.5 py-0.5 rounded text-[10px] font-semibold leading-tight truncate transition
                                                                {{ $isApproval ? 'bg-rose-100 text-rose-800 border border-rose-200' :
                                                                    ($isSelesai ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' :
                                                                    'bg-white border border-blue-400 text-blue-900 hover:bg-blue-50') }}"
                                                                title="{{ $ev->nama_kegiatan }}">
                                                            {{ $ev->nama_kegiatan }}
                                                            @if($hasHMinus1)
                                                                <span class="text-[9px] text-red-600 font-bold">(H-1)</span>
                                                            @endif
                                                        </button>
                                                        @if($loop->first && $ev->waktu_mulai)
                                                            <div class="text-[9px] text-slate-500 font-medium truncate">
                                                                {{ substr($ev->waktu_mulai, 0, 5) }} - {{ substr($ev->waktu_selesai ?? '15:30', 0, 5) }} WIB
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                    @if($dayEvents->count() > 2)
                                                        <span class="text-[9px] text-slate-400 font-bold block leading-none">
                                                            +{{ $dayEvents->count() - 2 }} lainnya
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Calendar Footer: Legend & Last Sync --}}
                    <div class="p-3 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-sm bg-blue-600 border border-blue-700"></span>
                                <span>Dipublikasi</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-sm bg-red-500 border border-red-600"></span>
                                <span>Menunggu Approval</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-sm bg-emerald-600 border border-emerald-700"></span>
                                <span>Terlaksana / Selesai</span>
                            </span>
                        </div>
                        <div class="text-slate-400 text-[10px]">
                            Sinkronisasi Terakhir: {{ now()->translatedFormat('d M Y') }} &bull; {{ now()->format('H:i') }} WIB
                        </div>
                    </div>
                </div>

                {{-- TAB: MINGGU --}}
                <div x-show="currentTab === 'minggu'" x-cloak class="bg-white rounded-xl border border-slate-200 shadow-xs p-4">
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Agenda Pekan Ini</h2>
                    <div class="space-y-2">
                        @php
                            $weekEvents = $events->whereBetween('tanggal', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
                        @endphp
                        @forelse($weekEvents as $we)
                            <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50 flex items-center justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-mono font-semibold text-slate-500">{{ $we->kode }}</span>
                                    <h3 class="text-xs font-bold text-slate-900">{{ $we->nama_kegiatan }}</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $we->tanggal->translatedFormat('l, d F Y') }} &bull; {{ substr($we->waktu_mulai, 0, 5) }} WIB &bull; {{ $we->lokasi }}
                                    </p>
                                </div>
                                <button type="button"
                                        @click="selectEvent(events.find(e => e.id === {{ $we->id }}))"
                                        class="px-2.5 py-1 bg-white border border-slate-200 rounded-md text-[11px] font-semibold text-[#0B63E5] hover:bg-blue-50 transition">
                                    Pilih &rarr;
                                </button>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">Tidak ada agenda pada pekan berjalan.</p>
                        @endforelse
                    </div>
                </div>

                {{-- TAB: LIST --}}
                <div x-show="currentTab === 'list'" x-cloak class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Agenda Bulan {{ $currentMonthName }}</h2>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                        @forelse($events as $ev)
                            <div class="p-3.5 hover:bg-slate-50 transition flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-mono text-slate-400">{{ $ev->kode }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $ev->status_class }}">{{ $ev->status_label }}</span>
                                    </div>
                                    <h3 class="text-xs font-bold text-slate-900 mt-1">{{ $ev->nama_kegiatan }}</h3>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $ev->tanggal->translatedFormat('d M Y') }} &bull; {{ substr($ev->waktu_mulai, 0, 5) }} WIB &bull; {{ $ev->lokasi }}
                                    </div>
                                </div>
                                <button type="button"
                                        @click="selectEvent(events.find(e => e.id === {{ $ev->id }}))"
                                        class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-[#0B63E5] hover:text-white text-xs font-medium text-slate-700 transition">
                                    Lihat
                                </button>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-8 text-center">Belum ada agenda pada bulan ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right Column: Selected Event Details Panel (col-span-5) --}}
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    {{-- Header: DETAIL AGENDA TERPILIH + Date Badge --}}
                    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-blue-50 text-[#0B63E5] flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h2 class="text-xs font-bold text-slate-900 tracking-wider uppercase">DETAIL AGENDA TERPILIH</h2>
                        </div>
                        <span x-text="selectedEvent ? selectedEvent.tanggal_formatted : getDateTitle(selectedDate)"
                              class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 font-bold text-[10px]">
                            {{ $selectedDateFormatted }}
                        </span>
                    </div>

                    {{-- When an event is selected --}}
                    <div x-show="selectedEvent" class="p-4 space-y-4">
                        {{-- Featured Image Box with Location Overlay Badge --}}
                        <div class="relative rounded-lg overflow-hidden h-36 bg-gradient-to-br from-slate-800 to-slate-900 flex items-end p-3 shadow-inner">
                            <div class="absolute inset-0 bg-cover bg-center opacity-40"
                                 style="background-image: radial-gradient(#1D4ED8 1px, transparent 1px); background-size: 16px 16px;"></div>
                            <div class="relative z-10 w-full flex items-center justify-between">
                                <span x-text="selectedEvent ? selectedEvent.lokasi : ''"
                                      class="px-2.5 py-1 rounded-md bg-black/60 backdrop-blur-sm text-white font-medium text-[10px] border border-white/20 truncate max-w-full">
                                    {{ $selectedKegiatan?->lokasi ?? 'Galeri Investasi Universitas Padjadjaran' }}
                                </span>
                            </div>
                        </div>

                        {{-- Event Title & Status Pills --}}
                        <div>
                            <h3 x-text="selectedEvent ? selectedEvent.nama_kegiatan : ''"
                                class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                                {{ $selectedKegiatan?->nama_kegiatan ?? 'Sekolah Pasar Modal (SPM) Level 1 & 2 - GI BEI UNIB' }}
                            </h3>

                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                {{-- Status Badge --}}
                                <span x-text="selectedEvent ? selectedEvent.status_label : ''"
                                      :class="selectedEvent ? selectedEvent.status_class : ''"
                                      class="px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                </span>

                                {{-- Region / KP BEI Pill --}}
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-700">
                                    KP Bengkulu
                                </span>

                                {{-- H-1 Alert Pill --}}
                                <template x-if="selectedEvent && selectedEvent.is_h1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800 inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>H-1 Pengingat Aktif</span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- 5 Metadata Information Cards --}}
                        <div class="space-y-2.5">
                            {{-- 1. WAKTU PELAKSANAAN --}}
                            <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/40 flex items-start gap-3">
                                <div class="w-7 h-7 rounded-md bg-blue-50 text-[#0B63E5] flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">WAKTU PELAKSANAAN</div>
                                    <div x-text="selectedEvent ? (selectedEvent.waktu_mulai + ' - ' + selectedEvent.waktu_selesai + ' WIB (Penuh Waktu)') : ''"
                                         class="text-xs font-semibold text-slate-800 mt-0.5">
                                    </div>
                                </div>
                            </div>

                            {{-- 2. TEMPAT / RUANGAN --}}
                            <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/40 flex items-start gap-3">
                                <div class="w-7 h-7 rounded-md bg-blue-50 text-[#0B63E5] flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TEMPAT / RUANGAN</div>
                                    <div x-text="selectedEvent ? selectedEvent.lokasi : ''"
                                         class="text-xs font-semibold text-slate-800 mt-0.5 truncate" :title="selectedEvent ? selectedEvent.lokasi : ''">
                                    </div>
                                </div>
                            </div>

                            {{-- 3. PIC PEMATERI & MITRA ANGGOTA BURSA --}}
                            <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/40 flex items-start gap-3">
                                <div class="w-7 h-7 rounded-md bg-blue-50 text-[#0B63E5] flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PIC PEMATERI &amp; MITRA ANGGOTA BURSA</div>
                                    <div x-text="selectedEvent ? selectedEvent.pic : ''"
                                         class="text-xs font-semibold text-slate-800 mt-0.5">
                                    </div>
                                </div>
                            </div>

                            {{-- 4. REALISASI & POS ANGGARAN --}}
                            <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/40 flex items-start gap-3">
                                <div class="w-7 h-7 rounded-md bg-blue-50 text-[#0B63E5] flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">REALISASI &amp; POS ANGGARAN</div>
                                    <div class="flex items-center justify-between mt-0.5">
                                        <span x-text="selectedEvent ? selectedEvent.anggaran : ''"
                                              class="text-xs font-bold text-slate-900"></span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-700">
                                            SPK Verified
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- 5. KONFIRMASI PESERTA (Capacity Progress Bar) --}}
                            <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/40 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-md bg-blue-50 text-[#0B63E5] flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">KONFIRMASI PESERTA</div>
                                            <div x-text="selectedEvent ? (selectedEvent.peserta_count + ' Mahasiswa & Dosen Pendamping') : ''"
                                                 class="text-xs font-semibold text-slate-800">
                                            </div>
                                        </div>
                                    </div>
                                    <span x-text="selectedEvent ? (selectedEvent.persen_peserta + '% Kuota Terisi') : ''"
                                          class="text-xs font-extrabold text-red-600">
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-[#0B63E5] h-1.5 rounded-full transition-all duration-300"
                                         :style="selectedEvent ? ('width: ' + selectedEvent.persen_peserta + '%') : 'width: 0%'"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom Button / Action Link --}}
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <a :href="selectedEvent ? selectedEvent.detail_url : '#'"
                               class="text-xs font-semibold text-[#680A10] hover:underline inline-flex items-center gap-1">
                                <span>Buka Rincian Lengkap Kegiatan</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>

                    {{-- When NO event on selected date --}}
                    <div x-show="!selectedEvent" x-cloak class="p-8 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Tidak Ada Agenda Edukasi</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                                Belum ada agenda edukasi pasar modal atau sosialisasi yang dijadwalkan pada tanggal ini.
                            </p>
                        </div>
                        <a :href="createUrl + '?tanggal=' + selectedDate"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0B63E5] hover:bg-blue-700 text-white font-semibold text-xs transition shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>+ Jadwalkan Kegiatan Baru</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>