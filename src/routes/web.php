<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/rest-start', [AttendanceController::class, 'restStart'])->name('attendance.rest-start');
    Route::post('/attendance/rest-end', [AttendanceController::class, 'restEnd'])->name('attendance.rest-end');

    // 一覧画面 (http://localhost/attendances)
    Route::get('/attendances', [AttendanceController::class, 'list'])->name('attendance.list');

    // 詳細画面：Blade側で「attendance.detail」を呼んでいるため、nameを合わせました
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.detail');
    Route::post('/attendance/detail/{id}', [RequestController::class, 'store'])->name('attendance.correction.store');
});
