<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function list(Request $request)
    {
        $latest = Attendance::orderBy('date', 'desc')->first();
        $defaultDate = $latest ? $latest->date : Carbon::today()->format('Y-m-d');

        $dateString = $request->query('date', $defaultDate);

        try {
            $displayDate = Carbon::parse($dateString);
        } catch (\Exception $e) {
            $displayDate = Carbon::today();
        }

        // データベース取得時に kana（ふりがな）で昇順ソートを実行
        $attendances = Attendance::select('attendances.*')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->with(['user', 'rests'])
            ->whereDate('attendances.date', $displayDate->format('Y-m-d'))
            ->orderBy('users.kana', 'asc')
            ->get();

        foreach ($attendances as $attendance) {
            $totalRestSeconds = 0;
            if ($attendance->rests) {
                foreach ($attendance->rests as $rest) {
                    if ($rest->rest_start && $rest->rest_end) {
                        $totalRestSeconds += Carbon::parse($rest->rest_start)->diffInSeconds(Carbon::parse($rest->rest_end));
                    }
                }
            }

            $attendance->total_rest_time = $totalRestSeconds > 0
                ? sprintf('%02d:%02d', floor($totalRestSeconds / 3600), floor(($totalRestSeconds % 3600) / 60))
                : '00:00';

            $attendance->formatted_check_in = $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '';
            $attendance->formatted_check_out = $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '';

            if ($attendance->check_in && $attendance->check_out) {
                $staySeconds = Carbon::parse($attendance->check_in)->diffInSeconds(Carbon::parse($attendance->check_out));
                $workSeconds = max(0, $staySeconds - $totalRestSeconds);
                $attendance->total_work_time = sprintf('%02d:%02d', floor($workSeconds / 3600), floor(($workSeconds % 3600) / 60));
            } else {
                $attendance->total_work_time = '';
            }
        }

        return view('admin.attendance.list', compact('attendances', 'displayDate'));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);

        $totalRestSeconds = 0;
        foreach ($attendance->rests as $rest) {
            if ($rest->rest_start && $rest->rest_end) {
                $totalRestSeconds += Carbon::parse($rest->rest_start)->diffInSeconds(Carbon::parse($rest->rest_end));
            }
        }

        $attendance->total_rest_time = sprintf('%02d:%02d', floor($totalRestSeconds / 3600), floor(($totalRestSeconds % 3600) / 60));

        return view('admin.attendance.detail', compact('attendance'));
    }
}
