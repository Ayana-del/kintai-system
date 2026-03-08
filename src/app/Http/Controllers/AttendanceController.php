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

        $attendances = Attendance::where('user_id', $user->id)
            ->with('rests')
            ->orderBy('date', 'desc')
            ->get();

        return view('attendances.index', compact('attendance', 'attendances'));
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

    public function list()
    {
        $user = Auth::user();
        $attendances = Attendance::where('user_id', $user->id)
            ->with('rests')
            ->orderBy('date', 'desc')
            ->get();

        return view('attendances.list', compact('attendances'));
    }
}
