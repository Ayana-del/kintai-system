<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        return view('attendances.index', compact('attendance'));
    }

    public function list(Request $request)
    {
        $user = Auth::user();

        $latestAttendance = Attendance::where('user_id', $user->id)->orderBy('date', 'desc')->first();
        $defaultMonth = $latestAttendance
            ? Carbon::parse($latestAttendance->date)->format('Y-m')
            : Carbon::now()->format('Y-m');

        $month = $request->query('month', $defaultMonth);
        $currentMonth = Carbon::parse($month);

        $attendancesInMonth = Attendance::where('user_id', $user->id)
            ->where('date', 'like', $month . '%')
            ->with('rests')
            ->get()
            ->keyBy('date');

        $daysInMonth = $currentMonth->daysInMonth;
        $attendances = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = $currentMonth->copy()->day($i)->format('Y-m-d');

            if (isset($attendancesInMonth[$date])) {
                $attendances[] = $attendancesInMonth[$date];
            } else {
                $attendances[] = new Attendance([
                    'user_id' => $user->id,
                    'date' => $date,
                    'check_in' => null,
                    'check_out' => null,
                ]);
            }
        }

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        return view('attendances.list', compact('attendances', 'month', 'prevMonth', 'nextMonth'));
    }

    public function checkIn()
    {
        $user = Auth::user();
        $now = Carbon::now();

        Attendance::create([
            'user_id' => $user->id,
            'date' => $now->format('Y-m-d'),
            'status' => 2,
            'check_in' => $now->format('H:i:s'),
        ]);

        return redirect()->back();
    }

    public function checkOut(Request $request)
    {
        $attendance = Attendance::find($request->attendance_id);

        if ($attendance) {
            $attendance->update([
                'status' => 4,
                'check_out' => Carbon::now()->format('H:i:s'),
            ]);
        }

        return redirect()->back()->with('message', 'お疲れ様でした。');
    }

    public function restStart(Request $request)
    {
        $attendance = Attendance::find($request->attendance_id);

        if ($attendance && $attendance->status == 2) {
            $attendance->update(['status' => 3]);

            Rest::create([
                'attendance_id' => $attendance->id,
                'rest_start' => Carbon::now()->format('H:i:s'),
            ]);
        }

        return redirect()->back();
    }

    public function restEnd(Request $request)
    {
        $attendance = Attendance::find($request->attendance_id);

        if ($attendance && $attendance->status == 3) {
            $attendance->update(['status' => 2]);

            $rest = Rest::where('attendance_id', $attendance->id)
                ->whereNull('rest_end')
                ->latest()
                ->first();

            if ($rest) {
                $rest->update([
                    'rest_end' => Carbon::now()->format('H:i:s'),
                ]);
            }
        }

        return redirect()->back();
    }

    public function show($id = null, Request $request)
    {
        $user = Auth::user();
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));

        if ($id && $id != 0) {
            $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);
        } else {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->first();

            if (!$attendance) {
                $attendance = new Attendance([
                    'user_id' => $user->id,
                    'date' => $date,
                ]);
            }
        }

        return view('attendances.detail', compact('attendance'));
    }
}
