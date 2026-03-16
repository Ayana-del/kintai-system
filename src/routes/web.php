<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AdminController;

// 管理者ログイン画面の表示
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

// 一般ユーザー用ルート
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/rest-start', [AttendanceController::class, 'restStart'])->name('attendance.rest-start');
    Route::post('/attendance/rest-end', [AttendanceController::class, 'restEnd'])->name('attendance.rest-end');

    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.detail');
    Route::post('/attendance/detail/{id}', [RequestController::class, 'store'])->name('attendance.correction.store');

    Route::get('/stamp_correction_request/list', [RequestController::class, 'userList'])->name('stamp_correction_request.list');
});

// 管理者用ルート（AdminMiddlewareでrole=1をチェック）
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // 勤怠一覧画面
    Route::get('/attendance/list', [AdminController::class, 'list'])->name('admin.attendance.list');

    // 勤怠詳細画面
    Route::get('/attendance/{id}', [AdminController::class, 'show'])->name('admin.attendance.show');
    Route::post('/attendance/{id}', [AdminController::class, 'update'])->name('admin.attendance.update');

    // スタッフ一覧画面
    Route::get('/staff/list', [AdminController::class, 'staffList'])->name('admin.staff.list');

    // スタッフ別勤怠一覧画面
    Route::get('/attendance/staff/{id}', [AdminController::class, 'staffAttendance'])->name('admin.staff.attendance');
    Route::get('/attendance/staff/{id}/csv', [AdminController::class, 'exportCsv'])->name('admin.staff.csv');

    // 申請一覧画面
    Route::get('/stamp_correction_request/list', [RequestController::class, 'adminList'])->name('admin.request.list');

    // 修正申請承認画面
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [RequestController::class, 'approveDetail'])->name('admin.request.approve');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [RequestController::class, 'approve'])->name('admin.request.approve.post');
});
