<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $date = Carbon::parse($attendance->date);
        $attendance->year = $date->format('Y');
        $attendance->month = $date->format('n');
        $attendance->day = $date->format('j');

        $attendance->formatted_check_in = $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '';
        $attendance->formatted_check_out = $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '';

        foreach ($attendance->rests as $rest) {
            $rest->formatted_start = $rest->rest_start ? Carbon::parse($rest->rest_start)->format('H:i') : '';
            $rest->formatted_end = $rest->rest_end ? Carbon::parse($rest->rest_end)->format('H:i') : '';
        }

        return view('admin.attendance.detail', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $date = $attendance->date;

        $request->validate([
            'check_in' => 'required',
            'check_out' => 'required',
            'remarks' => 'required',
        ], [
            'remarks.required' => '備考を記入してください。',
        ]);

        $checkIn = Carbon::parse($date . ' ' . $request->check_in);
        $checkOut = Carbon::parse($date . ' ' . $request->check_out);

        if ($checkIn->gt($checkOut)) {
            return back()->withInput()->withErrors(['check_in' => '出勤時間もしくは退勤時間が不適切な値です。']);
        }

        if ($request->has('rests')) {
            foreach ($request->rests as $restData) {
                if ($restData['start'] && $restData['end']) {
                    $restStart = Carbon::parse($date . ' ' . $restData['start']);
                    $restEnd = Carbon::parse($date . ' ' . $restData['end']);

                    if ($restStart->lt($checkIn) || $restEnd->gt($checkOut)) {
                        return back()->withInput()->withErrors(['rests' => '休憩時間が勤務時間外です。']);
                    }
                }
            }
        }

        DB::transaction(function () use ($request, $attendance, $date) {
            $attendance->update([
                'check_in' => $date . ' ' . $request->check_in . ':00',
                'check_out' => $date . ' ' . $request->check_out . ':00',
                'remarks' => $request->remarks,
            ]);

            if ($request->has('rests')) {
                foreach ($request->rests as $restId => $restData) {
                    $rest = Rest::findOrFail($restId);
                    $rest->update([
                        'rest_start' => $restData['start'] ? $date . ' ' . $restData['start'] . ':00' : null,
                        'rest_end' => $restData['end'] ? $date . ' ' . $restData['end'] . ':00' : null,
                    ]);
                }
            }
        });

        return redirect()->route('admin.attendance.list', ['date' => $date]);
    }

    public function staffList()
    {
        $users = User::where('role', 'staff')->get();

        return view('admin.staff.list', compact('users'));
    }

    public function staffAttendance(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $monthParam = $request->query('month');
        if ($monthParam) {
            $currentMonth = \Carbon\Carbon::parse($monthParam);
        } else {
            $currentMonth = \Carbon\Carbon::now();
        }

        $attendances = Attendance::where('user_id', $id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->get();

        return view('admin.staff/attendance', compact('user', 'attendances', 'currentMonth'));
    }

    public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $monthParam = $request->query('month');
        $currentMonth = $monthParam ? \Carbon\Carbon::parse($monthParam) : \Carbon\Carbon::now();

        $attendances = Attendance::where('user_id', $id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->orderBy('date', 'asc')
            ->get();

        $fileName = 'attendance_' . $user->name . '_' . $currentMonth->format('Ym') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($user, $currentMonth, $attendances) {
            $file = fopen('php://output', 'w');
            stream_filter_append($file, 'convert.iconv.UTF-8/CP932//TRANSLIT');

            fputcsv($file, ['日付', '出勤時間', '退勤時間', '休憩時間', '合計勤務時間']);

            $startOfMonth = $currentMonth->copy()->startOfMonth();
            $endOfMonth = $currentMonth->copy()->endOfMonth();

            for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
                $attendance = $attendances->firstWhere('date', $date->format('Y-m-d'));

                fputcsv($file, [
                    $date->format('m/d') . '(' . ['日', '月', '火', '水', '木', '金', '土'][$date->dayOfWeek] . ')',
                    $attendance && $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '',
                    $attendance && $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '',
                    $attendance && $attendance->total_rest_time ? substr($attendance->total_rest_time, 0, 5) : '',
                    $attendance && $attendance->total_work_time ? substr($attendance->total_work_time, 0, 5) : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}