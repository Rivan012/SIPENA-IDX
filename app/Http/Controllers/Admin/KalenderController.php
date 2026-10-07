<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public const BULAN_ID = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public const HARI_ID = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
    ];

    public function index(Request $request)
    {
        if (!$request->has('year') && !$request->has('month')) {
            $hasNow = Kegiatan::whereYear('tanggal', now()->year)->exists();
            if (!$hasNow && Kegiatan::whereYear('tanggal', 2025)->exists()) {
                $year = 2025;
                $month = 2;
            } else {
                $year = now()->year;
                $month = now()->month;
            }
        } else {
            $year = (int) $request->get('year', now()->year);
            $month = (int) $request->get('month', now()->month);
        }

        // Clamp values
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $currentDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // 6-day workweek grid: Monday (1) to Saturday (6)
        $gridStart = $startOfMonth->copy();
        if ($gridStart->dayOfWeekIso > 1) {
            $gridStart->subDays($gridStart->dayOfWeekIso - 1);
        }

        $gridEnd = $endOfMonth->copy();
        if ($gridEnd->dayOfWeekIso < 6) {
            $gridEnd->addDays(6 - $gridEnd->dayOfWeekIso);
        } elseif ($gridEnd->dayOfWeekIso === 7) {
            $gridEnd->addDays(6);
        }

        // Query events
        $statusFilter = $request->get('status');
        $eventsQuery = Kegiatan::query()
            ->with(['rencanaKegiatan.detailKegiatan', 'rencanaKegiatan.admin', 'peserta', 'agenda'])
            ->when($statusFilter, function ($q, $st) {
                if ($st === 'approval') {
                    $q->where('status', 'menunggu_approval');
                } else {
                    $q->where('status', $st);
                }
            });

        // Get events in grid window
        $events = (clone $eventsQuery)
            ->whereBetween('tanggal', [$gridStart->format('Y-m-d'), $gridEnd->format('Y-m-d')])
            ->orderBy('waktu_mulai')
            ->get();

        // Group by Y-m-d
        $eventsByDate = $events->groupBy(fn ($item) => $item->tanggal->format('Y-m-d'));

        // Build 6-day calendar weeks (Senin - Sabtu)
        $calendarWeeks = [];
        $cursor = $gridStart->copy();
        $week = [];

        while ($cursor->lte($gridEnd)) {
            if ($cursor->dayOfWeekIso <= 6) {
                $dateKey = $cursor->format('Y-m-d');
                $week[] = [
                    'date' => $cursor->copy(),
                    'date_string' => $dateKey,
                    'day' => $cursor->day,
                    'is_current_month' => $cursor->month === $month,
                    'is_today' => $cursor->isToday(),
                    'events' => $eventsByDate->get($dateKey, collect()),
                ];

                if (count($week) === 6) {
                    $calendarWeeks[] = $week;
                    $week = [];
                }
            }
            $cursor->addDay();
        }

        if (!empty($week)) {
            $calendarWeeks[] = $week;
        }

        // Summary Statistics for current year & month
        $allYearEvents = Kegiatan::whereYear('tanggal', $year)->get();

        $stats = [
            'total' => $allYearEvents->count(),
            'dipublikasikan' => $allYearEvents->where('status', 'dipublikasikan')->count(),
            'approval' => $allYearEvents->where('status', 'menunggu_approval')->count(),
            'selesai' => $allYearEvents->where('status', 'selesai')->count(),
            'pekan_ini' => Kegiatan::whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'h_minus_1' => Kegiatan::whereDate('tanggal', now()->addDay()->toDateString())
                ->orWhereHas('agenda', fn ($q) => $q->where('is_h_minus_1', true))
                ->count(),
        ];

        // Determine selected kegiatan for right panel
        $selectedId = $request->get('selected_id');
        $selectedKegiatan = null;

        if ($selectedId) {
            $selectedKegiatan = $events->firstWhere('id', (int) $selectedId);
        }

        if (!$selectedKegiatan) {
            $selectedKegiatan = $events->first(fn ($k) => $k->agenda->contains('is_h_minus_1', true) || $k->tanggal->isTomorrow())
                ?: $events->first(fn ($k) => $k->tanggal->gte(now()->startOfDay()))
                ?: $events->first();
        }

        $selectedDateString = $selectedKegiatan?->tanggal?->format('Y-m-d')
            ?? $request->get('date', now()->format('Y-m-d'));

        $currentMonthName = (self::BULAN_ID[$month] ?? $currentDate->format('F')) . ' ' . $year;

        $selectedDateFormatted = '28 Feb 2025';
        if ($selectedKegiatan?->tanggal) {
            $t = $selectedKegiatan->tanggal;
            $hari = self::HARI_ID[$t->format('l')] ?? $t->format('l');
            $bln = substr(self::BULAN_ID[$t->month] ?? $t->format('M'), 0, 3);
            $selectedDateFormatted = "{$hari}, {$t->day} {$bln} {$t->year}";
        }

        $eventsClient = $events->map(function ($ev) {
            $t = $ev->tanggal;
            $hari = self::HARI_ID[$t->format('l')] ?? $t->format('l');
            $bln = substr(self::BULAN_ID[$t->month] ?? $t->format('M'), 0, 3);
            $blnFull = self::BULAN_ID[$t->month] ?? $t->format('F');

            $hasH1 = $ev->agenda->contains('is_h_minus_1', true) || $t->isTomorrow();
            $picName = $ev->rencanaKegiatan?->admin?->name ?? 'Bpk. Kurniawan (KP Bengkulu) & Phintraco Sekuritas';
            $anggaranVal = $ev->rencanaKegiatan?->detailKegiatan?->anggaran ?? 14500000;
            $pesertaCount = $ev->peserta->count() ?: $ev->kuota_peserta;
            $persen = $ev->kuota_peserta > 0 ? min(100, round(($pesertaCount / $ev->kuota_peserta) * 100)) : 100;

            return [
                'id' => $ev->id,
                'nama_kegiatan' => $ev->nama_kegiatan,
                'jenis_kegiatan' => $ev->jenis_kegiatan,
                'tanggal' => $t->format('Y-m-d'),
                'tanggal_formatted' => "{$hari}, {$t->day} {$bln} {$t->year}",
                'tanggal_formatted_full' => "{$hari}, {$t->day} {$blnFull} {$t->year}",
                'waktu_mulai' => $ev->waktu_mulai ? substr($ev->waktu_mulai, 0, 5) : '09:00',
                'waktu_selesai' => $ev->waktu_selesai ? substr($ev->waktu_selesai, 0, 5) : '15:30',
                'lokasi' => $ev->lokasi ?: 'Gedung B Ruang Seminar GI UNIB Bengkulu',
                'kuota_peserta' => $ev->kuota_peserta ?: 100,
                'peserta_count' => $pesertaCount,
                'persen_peserta' => $persen,
                'status' => $ev->status,
                'status_label' => $ev->status_label,
                'status_class' => $ev->status_class,
                'is_h1' => $hasH1,
                'pic' => $picName,
                'anggaran' => 'Rp ' . number_format($anggaranVal, 0, ',', '.'),
                'detail_url' => route('admin.kegiatan.show', $ev),
            ];
        });

        $eventsByDateClient = $eventsClient->groupBy('tanggal');

        return view('admin.kalender.index', [
            'currentDate' => $currentDate,
            'currentMonthName' => $currentMonthName,
            'selectedDateFormatted' => $selectedDateFormatted,
            'year' => $year,
            'month' => $month,
            'prevMonth' => $currentDate->copy()->subMonth(),
            'nextMonth' => $currentDate->copy()->addMonth(),
            'calendarWeeks' => $calendarWeeks,
            'events' => $events,
            'eventsClient' => $eventsClient,
            'eventsByDateClient' => $eventsByDateClient,
            'stats' => $stats,
            'selectedKegiatan' => $selectedKegiatan,
            'selectedDateString' => $selectedDateString,
            'view' => $request->get('view', 'bulan'),
        ]);
    }
}
