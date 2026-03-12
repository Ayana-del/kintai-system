<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorrectionRequest;
use App\Models\Attendance;
use App\Models\CorrectionRequest as CorrectionModel;
use App\Models\CorrectionDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function store(CorrectionRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            // 1. 修正申請（親）を作成
            $correctionRequest = CorrectionModel::create([
                'attendance_id' => $id,
                'user_id' => Auth::id(),
                'status' => '承認待ち',
                'remarks' => $request->remarks,
            ]);

            // 2. 分割された「年」と「月日」を結合して日付データを作成
            $dateString = $request->year . $request->date;
            // 「2026年3月12日」のような形式をCarbonでパース
            $formattedDate = Carbon::createFromFormat('Y年n月j日', $dateString)->format('Y-m-d');

            // 3. 修正後の出退勤・日付（子）を保存
            CorrectionDetail::create([
                'correction_request_id' => $correctionRequest->id,
                'date' => $formattedDate,
                'check_in' => $request->start_time,
                'check_out' => $request->end_time,
            ]);

            // 4. 休憩時間の保存
            $restStarts = $request->rest_start_times ?? [];
            $restEnds = $request->rest_end_times ?? [];

            foreach ($restStarts as $index => $startTime) {
                if (!empty($startTime)) {
                    CorrectionDetail::create([
                        'correction_request_id' => $correctionRequest->id,
                        'rest_start' => $startTime,
                        'rest_end' => $restEnds[$index] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('attendance.list');
    }

    public function userList()
    {
        $requests = CorrectionModel::where('user_id', Auth::id())
            ->with('attendance')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance.requests', compact('requests'));
    }
}
