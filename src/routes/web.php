<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AdminController;

// 【重要】ログイン画面の表示(GET)のみ定義。POSTはFortifyが自動で行います。
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 一般ユーザー用
Route::middleware(['auth'])->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/rest-start', [AttendanceController::class, 'restStart'])->name('attendance.rest-start');
    Route::post('/attendance/rest-end', [AttendanceController::class, 'restEnd'])->name('attendance.rest-end');
    Route::get('/attendances', [AttendanceController::class, 'list'])->name('attendance.list');

    // 【修正】打刻忘れ対応のため ID を任意 {id?} に変更
    Route::get('/attendance/detail/{id?}', [AttendanceController::class, 'show'])->name('attendance.detail');
    Route::post('/attendance/detail/{id?}', [RequestController::class, 'store'])->name('attendance.correction.store');

    Route::get('/stamp_correction_request/list', [RequestController::class, 'userList'])->name('stamp_correction_request.list');
});

// 管理者専用（adminミドルウェアを適用）
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::post('/logout', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
    Route::get('/attendance/list', [AdminController::class, 'list'])->name('admin.attendance.list');
    Route::get('/attendance/{id}', [AdminController::class, 'show'])->name('admin.attendance.show');
    Route::post('/attendance/{id}', [AdminController::class, 'update'])->name('admin.attendance.update');
    Route::get('/staff/list', [AdminController::class, 'staffList'])->name('admin.staff.list');
    Route::get('/attendance/staff/{id}', [AdminController::class, 'staffAttendance'])->name('admin.staff.attendance');
    Route::get('/attendance/staff/{id}/csv', [AdminController::class, 'exportCsv'])->name('admin.staff.csv');
    Route::get('/stamp_correction_request/list', [RequestController::class, 'adminList'])->name('admin.request.list');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [RequestController::class, 'approveDetail'])->name('admin.request.approve');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [RequestController::class, 'approve'])->name('admin.request.approve.post');
});
