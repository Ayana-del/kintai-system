<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CorrectionDetail;
use App\Models\CorrectionRequest;
use App\Http\Requests\CorrectionRequest as CorrectionValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function store(CorrectionValidation $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        DB::transaction(function () use ($request, $attendance) {
            $correctionRequest = CorrectionRequest::create([
                'attendance_id' => $attendance->id,
                'user_id' => Auth::id(),
                'date' => $attendance->date,
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
            ->orderBy('created_at', 'asc')
            ->get();

        return view('attendances.request.list', compact('requests', 'status'));
    }

    public function adminList(Request $request)
    {
        $status = $request->query('status', '0');

        $requests = CorrectionRequest::with(['user', 'attendance'])
            ->where('status', $status)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.requests.list', compact('requests', 'status'));
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

            $attendance = $correctionRequest->attendance;

            foreach ($correctionRequest->details as $detail) {
                if ($detail->type === 'check_in') {
                    $attendance->update(['check_in' => $detail->modified_time]);
                } elseif ($detail->type === 'check_out') {
                    $attendance->update(['check_out' => $detail->modified_time]);
                }
            }
        });

        return redirect()->route('admin.request.list');
    }
}
