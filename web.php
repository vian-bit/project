<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\UserTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// WhatsApp webhook - bebas CSRF, dipanggil WA server (Baileys)
Route::post('/wa-webhook', [\App\Http\Controllers\WhatsAppController::class, 'webhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('whatsapp.webhook');

// Download Excel via token pendek (dari link WA bot, tanpa login)
Route::get('/dl/{token}', [AttendanceController::class, 'exportViaToken'])
    ->name('wa.export.xlsx');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Superuser & Admin
    Route::middleware(['role:superuser,admin'])->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::resource('shifts', ShiftController::class);
        Route::resource('schedules', ScheduleController::class);
        Route::post('schedules/bulk-store', [\App\Http\Controllers\ScheduleCalendarController::class, 'bulkStore'])->name('schedules.bulk-store');
        Route::delete('schedules/delete-schedule', [\App\Http\Controllers\ScheduleCalendarController::class, 'deleteSchedule'])->name('schedules.delete-schedule');
        Route::get('attendances/export', [AttendanceController::class, 'export'])->name('attendances.export');

        // Early checkout
        Route::get('early-checkout-requests', [AttendanceController::class, 'earlyCheckoutRequests'])->name('early-checkout.index');
        Route::post('early-checkout/{earlyCheckoutRequest}/approve', [AttendanceController::class, 'approveEarlyCheckout'])->name('early-checkout.approve');
        Route::post('early-checkout/{earlyCheckoutRequest}/reject', [AttendanceController::class, 'rejectEarlyCheckout'])->name('early-checkout.reject');

        // Manual attendance
        Route::get('manual-attendance', [AttendanceController::class, 'manualAttendance'])->name('manual-attendance.index');
        Route::post('manual-attendance/check-in', [AttendanceController::class, 'manualCheckIn'])->name('manual-attendance.checkin');
        Route::post('manual-attendance/check-out', [AttendanceController::class, 'manualCheckOut'])->name('manual-attendance.checkout');
        Route::post('manual-attendance/bulk-check-in', [AttendanceController::class, 'bulkCheckIn'])->name('manual-attendance.bulk-checkin');
        Route::post('manual-attendance/bulk-check-out', [AttendanceController::class, 'bulkCheckOut'])->name('manual-attendance.bulk-checkout');

        // WhatsApp
        Route::get('whatsapp/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('whatsapp.status');
        Route::post('whatsapp/send-report', [\App\Http\Controllers\WhatsAppController::class, 'sendReport'])->name('whatsapp.send-report');
    });

    // Superuser only
    Route::middleware(['role:superuser'])->group(function () {
        Route::resource('departments', DepartmentController::class);
    });

    // Superuser & Admin
    Route::middleware(['role:superuser,admin'])->group(function () {
        Route::resource('user-types', UserTypeController::class);
    });

    // Semua user login
    Route::get('schedules-calendar', [\App\Http\Controllers\ScheduleCalendarController::class, 'index'])->name('schedules.calendar');
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('attendances/check-in', [AttendanceController::class, 'checkIn'])->name('attendances.checkin');
    Route::post('attendances/check-out', [AttendanceController::class, 'checkOut'])->name('attendances.checkout');
    Route::post('profile/change-password', [AuthController::class, 'changePassword'])->name('profile.change-password');

    // Profile - semua role
    Route::get('profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/change-password', [\App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');
});
