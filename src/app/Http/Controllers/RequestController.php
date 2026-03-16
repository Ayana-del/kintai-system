<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CorrectionDetail;
use App\Models\CorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function store(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        DB::transaction(function () use ($request, $attendance) {
            $correctionRequest = CorrectionRequest::create([
                'attendance_id' => $attendance->id,
                'user_id' => Auth::id(),
                'status' => 0,
                'reason' => $request->reason,
            ]);

            CorrectionDetail::create([
                'correction_request_id' => $correctionRequest->id,
                'type' => 'check_in',
                'modified_time' => $request->check_in,
            ]);

            CorrectionDetail::create([
                'correction_request_id' => $correctionRequest->id,
                'type' => 'check_out',
                'modified_time' => $request->check_out,
            ]);

            $restStarts = $request->rest_start_times ?? [];
            $restEnds = $request->rest_end_times ?? [];

            foreach ($restStarts as $index => $startTime) {
                if (!empty($startTime)) {
                    $existingRest = $attendance->rests->get($index);

                    CorrectionDetail::create([
                        'correction_request_id' => $correctionRequest->id,
                        'type' => 'rest_start',
                        'modified_time' => $startTime,
                        'rest_id' => $existingRest ? $existingRest->id : null,
                    ]);

                    if (!empty($restEnds[$index])) {
                        CorrectionDetail::create([
                            'correction_request_id' => $correctionRequest->id,
                            'type' => 'rest_end',
                            'modified_time' => $restEnds[$index],
                            'rest_id' => $existingRest ? $existingRest->id : null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('stamp_correction_request.list');
    }

    public function userList(Request $request)
    {
        $status = $request->query('status', '0');

        $requests = CorrectionRequest::where('user_id', Auth::id())
            ->where('status', $status)
            ->with('attendance')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendances.request', compact('requests', 'status'));
    }

    public function adminList()
    {
        $requests = CorrectionRequest::with(['user', 'attendance'])
            ->where('status', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.requests', compact('requests'));
    }

    public function approveDetail($attendance_correct_request_id)
    {
        $requestData = CorrectionRequest::with(['user', 'attendance', 'details'])
            ->findOrFail($attendance_correct_request_id);

        return view('admin.approve', compact('requestData'));
    }

    public function approve($attendance_correct_request_id)
    {
        DB::transaction(function () use ($attendance_correct_request_id) {
            $correctionRequest = CorrectionRequest::with('details')->findOrFail($attendance_correct_request_id);

            $correctionRequest->update(['status' => 1]);
        });

        return redirect()->route('admin.stamp_correction_request.list');
    }
}
