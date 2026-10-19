<?php

use App\Http\Controllers\ActivityDescriptionImageController;
use App\Http\Controllers\Admin as AdminCtrl;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\User as UserCtrl;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', LandingPageController::class)->name('home');

// Guest / Authentication
Route::get('/captcha/{action}', [AuthController::class, 'getCaptcha'])->name('captcha');

Route::middleware('guest:web,admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Admin Login
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);

    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Email Verification (Link Aktivasi Akun)
Route::get('/verify-email', [AuthController::class, 'showVerifyEmailForm'])
    ->middleware('auth:web')
    ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmailLink'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationLink'])
    ->middleware(['auth:web', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/description-images/{ownerType}/{ownerId}/{filename}', [ActivityDescriptionImageController::class, 'show'])
    ->middleware('auth:web,admin')
    ->where(['ownerType' => 'users|admins', 'ownerId' => '[0-9]+', 'filename' => '[A-Fa-f0-9-]+\.(jpg|png|webp)'])
    ->name('description-images.show');

Route::get('/profile-photos/{ownerType}/{ownerId}/{filename}', [ProfilePhotoController::class, 'show'])
    ->middleware('auth:web,admin')
    ->where(['ownerType' => 'users|admins', 'ownerId' => '[0-9]+', 'filename' => '[A-Fa-f0-9-]+\.(jpg|png|webp)'])
    ->name('profile-photos.show');

// User Protected Routes
Route::middleware(['auth:web', 'verified'])->name('user.')->group(function () {
    Route::post('/description-images/users', [ActivityDescriptionImageController::class, 'storeForUser'])->name('description-images.store');
    Route::get('/dashboard', [UserCtrl\DashboardController::class, 'index'])->name('dashboard');

    // Kegiatan
    Route::get('/kegiatan/ajukan', [UserCtrl\KegiatanController::class, 'create'])->name('kegiatan.create');
    Route::post('/kegiatan/ajukan', [UserCtrl\KegiatanController::class, 'storeProposal'])->name('kegiatan.store');
    Route::get('/kegiatan', [UserCtrl\KegiatanSayaController::class, 'index'])->name('kegiatan.index');
    Route::get('/kegiatan/{kegiatan}/edit', [UserCtrl\KegiatanController::class, 'edit'])->name('kegiatan.edit');
    Route::get('/kegiatan/{kegiatan}/proposal', [UserCtrl\KegiatanController::class, 'downloadProposal'])->name('kegiatan.proposal.download');
    Route::put('/kegiatan/{kegiatan}', [UserCtrl\KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::delete('/kegiatan/{kegiatan}', [UserCtrl\KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
    Route::patch('/kegiatan/{kegiatan}/batalkan', [UserCtrl\KegiatanController::class, 'cancel'])->name('kegiatan.cancel');
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

    // Riwayat Aktivitas
    Route::get('/riwayat-aktivitas', [UserCtrl\AktivitasController::class, 'index'])->name('aktivitas.index');

    // Profil
    Route::get('/profil', [UserCtrl\ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [UserCtrl\ProfileController::class, 'update'])->name('profil.update');
});

// Admin Protected Routes
Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminCtrl\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AdminCtrl\ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [AdminCtrl\ProfileController::class, 'update'])->name('profil.update');
    Route::post('/description-images/admins', [ActivityDescriptionImageController::class, 'storeForAdmin'])->name('description-images.store');

    // Approval proposal mitra
    Route::get('approval/export', [AdminCtrl\ApprovalController::class, 'export'])->name('approval.export');
    Route::get('approval', [AdminCtrl\ApprovalController::class, 'index'])->name('approval.index');
    Route::patch('approval/kegiatan/{kegiatan}/status', [AdminCtrl\ApprovalController::class, 'updateMakerProposalStatus'])->name('approval.kegiatan.status');

    // Kalender
    Route::get('kalender', [AdminCtrl\KalenderController::class, 'index'])->name('kalender.index');
    Route::post('kalender/{kegiatan}/kirim-pengingat', [AdminCtrl\KalenderController::class, 'sendReminder'])->name('kalender.send-reminder');

    // Kegiatan & Agenda
    Route::patch('kegiatan/{kegiatan}/status', [AdminCtrl\KegiatanController::class, 'updateStatus'])->name('kegiatan.status');
    Route::get('kegiatan/{kegiatan}/proposal', [AdminCtrl\KegiatanController::class, 'downloadProposal'])->name('kegiatan.proposal.download');
    Route::get('kegiatan/{kegiatan}/presensi', [AdminCtrl\KegiatanController::class, 'downloadAttendance'])->name('kegiatan.presensi.download');
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

    // Riwayat Aktivitas
    Route::get('aktivitas/export', [AdminCtrl\AktivitasController::class, 'export'])->name('aktivitas.export');
    Route::get('aktivitas', [AdminCtrl\AktivitasController::class, 'index'])->name('aktivitas.index');
});
