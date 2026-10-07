<?php

use App\Http\Controllers\Admin as AdminCtrl;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User as UserCtrl;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest / Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Admin Login
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);

    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Protected Routes
Route::middleware('auth:web')->name('user.')->group(function () {
    Route::get('/dashboard', [UserCtrl\DashboardController::class, 'index'])->name('dashboard');

    // Kegiatan
    Route::get('/kegiatan', [UserCtrl\KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::get('/kegiatan/{kegiatan}', [UserCtrl\KegiatanController::class, 'show'])->name('kegiatan.show');
    Route::post('/kegiatan/{kegiatan}/daftar', [UserCtrl\KegiatanController::class, 'daftar'])->name('kegiatan.daftar');
    Route::delete('/kegiatan/{kegiatan}/batal', [UserCtrl\KegiatanController::class, 'batal'])->name('kegiatan.batal');

    // Kegiatan Saya
    Route::get('/kegiatan-saya', [UserCtrl\KegiatanSayaController::class, 'index'])->name('kegiatan-saya');

    // Laporan Kegiatan
    Route::get('/laporan', [UserCtrl\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{laporan}', [UserCtrl\LaporanController::class, 'show'])->name('laporan.show');

    // Notifikasi
    Route::get('/notifikasi', [UserCtrl\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::patch('/notifikasi/{notifikasi}/baca', [UserCtrl\NotifikasiController::class, 'markAsRead'])->name('notifikasi.baca');

    // Profil
    Route::get('/profil', [UserCtrl\ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [UserCtrl\ProfileController::class, 'update'])->name('profil.update');
});

// Admin Protected Routes
Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminCtrl\DashboardController::class, 'index'])->name('dashboard');

    // Perencanaan & Anggaran
    Route::get('anggaran', [AdminCtrl\RencanaKegiatanController::class, 'index'])->name('anggaran.index');
    Route::get('rencana-kegiatan/export', [AdminCtrl\RencanaKegiatanController::class, 'export'])->name('rencana-kegiatan.export');
    Route::post('rencana-kegiatan/{rencana_kegiatan}/ajukan', [AdminCtrl\RencanaKegiatanController::class, 'ajukan'])->name('rencana-kegiatan.ajukan');
    Route::resource('rencana-kegiatan', AdminCtrl\RencanaKegiatanController::class);
    Route::post('rencana-kegiatan/{rencana}/detail', [AdminCtrl\DetailKegiatanController::class, 'store'])->name('rencana-kegiatan.detail.store');
    Route::put('rencana-kegiatan/{rencana}/detail', [AdminCtrl\DetailKegiatanController::class, 'update'])->name('rencana-kegiatan.detail.update');

    // Pengajuan & Approval
    Route::get('approval', [AdminCtrl\PengajuanKegiatanController::class, 'index'])->name('approval.index');
    Route::get('pengajuan/export', [AdminCtrl\PengajuanKegiatanController::class, 'export'])->name('pengajuan.export');
    Route::get('pengajuan', [AdminCtrl\PengajuanKegiatanController::class, 'index'])->name('pengajuan.index');
    Route::get('pengajuan/{pengajuan}', [AdminCtrl\PengajuanKegiatanController::class, 'show'])->name('pengajuan.show');
    Route::post('pengajuan', [AdminCtrl\PengajuanKegiatanController::class, 'store'])->name('pengajuan.store');
    Route::patch('pengajuan/{pengajuan}/status', [AdminCtrl\PengajuanKegiatanController::class, 'updateStatus'])->name('pengajuan.status');

    // Kalender
    Route::get('kalender', [AdminCtrl\KalenderController::class, 'index'])->name('kalender.index');

    // Kegiatan & Agenda
    Route::resource('kegiatan', AdminCtrl\KegiatanController::class);
    Route::resource('kegiatan.agenda', AdminCtrl\AgendaController::class)->shallow();

    // Peserta
    Route::get('kegiatan/{kegiatan}/peserta', [AdminCtrl\PesertaController::class, 'index'])->name('kegiatan.peserta.index');
    Route::patch('peserta/{peserta}/status', [AdminCtrl\PesertaController::class, 'updateStatus'])->name('peserta.status');
    Route::delete('peserta/{peserta}', [AdminCtrl\PesertaController::class, 'destroy'])->name('peserta.destroy');

    // Pelaporan
    Route::resource('laporan-kegiatan', AdminCtrl\LaporanKegiatanController::class);

    // Komunikasi / Notifikasi
    Route::resource('notifikasi', AdminCtrl\NotifikasiController::class)->only(['index', 'create', 'store', 'destroy']);

    // Monitoring & Manajemen Pengguna
    Route::get('monitoring/export', [AdminCtrl\MonitoringController::class, 'export'])->name('monitoring.export');
    Route::get('monitoring', [AdminCtrl\MonitoringController::class, 'index'])->name('monitoring');
    Route::get('pengguna', [AdminCtrl\MonitoringController::class, 'index'])->name('pengguna.index');
});
