<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\KegiatanReminderMail;
use App\Models\Kegiatan;
use App\Support\ActivityDescriptionSanitizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class KalenderController extends Controller
{
    public function __construct(private readonly ActivityDescriptionSanitizer $descriptionSanitizer) {}

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

    public function index(Request $request): Response
    {
        if (! $request->has('year') && ! $request->has('month')) {
            $hasNow = Kegiatan::whereYear('tanggal', now()->year)->exists();
            if (! $hasNow && Kegiatan::whereYear('tanggal', 2025)->exists()) {
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

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $currentDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        $gridStart = $startOfMonth->copy()->subDays($startOfMonth->dayOfWeek);
        $gridEnd = $endOfMonth->copy()->addDays(6 - $endOfMonth->dayOfWeek);

        $statusFilter = $request->get('status');
        $eventsQuery = Kegiatan::query()
            ->with(['peserta', 'agenda', 'user'])
            ->when($statusFilter, function ($q, $st) {
                if ($st === 'approval') {
                    $q->where('status', 'menunggu_approval');
                } else {
                    $q->where('status', $st);
                }
            });

        $events = (clone $eventsQuery)
            ->whereBetween('tanggal', [$gridStart->format('Y-m-d'), $gridEnd->format('Y-m-d')])
            ->orderBy('waktu_mulai')
            ->get();

        $eventsClient = $events->map(function ($ev) {
            $t = $ev->tanggal;
            $hari = self::HARI_ID[$t->format('l')] ?? $t->format('l');
            $bln = substr(self::BULAN_ID[$t->month] ?? $t->format('M'), 0, 3);
            $blnFull = self::BULAN_ID[$t->month] ?? $t->format('F');

            $hasH1 = $ev->agenda->contains('is_h_minus_1', true) || $t->isTomorrow();
            $picName = $ev->mitra ?: 'KP BEI Bengkulu';
            $pesertaCount = $ev->peserta->count() ?: $ev->kuota_peserta;
            $persen = $ev->kuota_peserta > 0 ? min(100, round(($pesertaCount / $ev->kuota_peserta) * 100)) : 100;
            $descriptionBanner = $this->descriptionSanitizer->firstImageUrl($ev->deskripsi);

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
                'description_banner' => $descriptionBanner,
                'pengusul_nama' => $ev->user?->name,
                'pengusul_email' => $ev->user?->email,
            ];
        });

        $eventsByDateClient = $eventsClient->groupBy('tanggal');

        $calendarWeeks = [];
        $cursor = $gridStart->copy();
        $week = [];

        while ($cursor->lte($gridEnd)) {
            $dateKey = $cursor->format('Y-m-d');
            $week[] = [
                'date_string' => $dateKey,
                'day' => $cursor->day,
                'is_current_month' => $cursor->month === $month,
                'is_today' => $cursor->isToday(),
                'events' => $eventsByDateClient->get($dateKey, collect())->values(),
            ];

            if (count($week) === 7) {
                $calendarWeeks[] = $week;
                $week = [];
            }

            $cursor->addDay();
        }

        if (! empty($week)) {
            $calendarWeeks[] = $week;
        }

        $allYearEvents = Kegiatan::whereYear('tanggal', $year)->get();
        $stats = [
            'total' => $allYearEvents->count(),
            'dipublikasikan' => $allYearEvents->where('status', 'dipublikasikan')->count(),
            'approval' => $allYearEvents->where('status', 'menunggu_approval')->count(),
            'selesai' => $allYearEvents->where('status', 'selesai')->count(),
        ];

        $currentMonthName = (self::BULAN_ID[$month] ?? $currentDate->format('F')).' '.$year;
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();

        return Inertia::render('Admin/Kalender/Index', [
            'year' => $year,
            'month' => $month,
            'currentMonthName' => $currentMonthName,
            'prevMonthParams' => ['year' => $prevDate->year, 'month' => $prevDate->month],
            'nextMonthParams' => ['year' => $nextDate->year, 'month' => $nextDate->month],
            'calendarWeeks' => $calendarWeeks,
            'eventsClient' => $eventsClient,
            'eventsByDate' => $eventsByDateClient,
            'stats' => $stats,
            'filters' => [
                'status' => $statusFilter,
                'view' => $request->get('view', 'bulan'),
            ],
        ]);
    }

    public function sendReminder(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'include_pengusul' => ['nullable', 'boolean'],
            'custom_emails' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $recipients = collect();

        // Kumpulkan email orang / mitra yang mengusulkan kegiatan
        if ($request->boolean('include_pengusul', true)) {
            $kegiatan->loadMissing('user');
            if (! empty($kegiatan->user?->email)) {
                $recipients->push([
                    'email' => $kegiatan->user->email,
                    'name' => $kegiatan->user->name ?? 'Mitra Pengusul',
                ]);
            }
        }

        // Kumpulkan email manual jika diisi
        if (! empty($validated['custom_emails'])) {
            $rawEmails = array_map('trim', explode(',', $validated['custom_emails']));
            foreach ($rawEmails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $recipients->push([
                        'email' => $email,
                        'name' => 'Mitra / Panitia Kegiatan',
                    ]);
                }
            }
        }

        $uniqueRecipients = $recipients->unique('email')->values();

        if ($uniqueRecipients->isEmpty()) {
            return back()->with('error', 'Tidak ada email penerima. Kegiatan ini tidak memiliki akun mitra pengusul, silakan isi kolom email tambahan.');
        }

        $customNote = $validated['catatan'] ?? null;
        $sentCount = 0;

        foreach ($uniqueRecipients as $r) {
            try {
                Mail::to($r['email'])->send(
                    new KegiatanReminderMail($kegiatan, $r['name'], $customNote)
                );
                $sentCount++;
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim email pengingat kegiatan ke {$r['email']}: ".$e->getMessage());
            }
        }

        if ($sentCount === 0) {
            return back()->with('error', 'Gagal mengirim email pengingat ke penerima. Periksa konfigurasi SMTP Google.');
        }

        return back()->with('success', "Pengingat email berhasil dikirim ke {$sentCount} mitra pengusul.");
    }
}
