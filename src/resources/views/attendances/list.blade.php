@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">勤怠一覧</h1>

    <div class="attendance-list__nav">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="attendance-list__nav-btn">
            <img src="{{ asset('img/yajirushi.png') }}" alt="prev" class="nav-arrow-left">
            前月
        </a>

        <div class="attendance-list__current-month">
            <img src="{{ asset('img/calendar.png') }}" alt="calendar" class="attendance-list__calendar-icon">
            <span class="attendance-list__month-text">{{ \Carbon\Carbon::parse($month)->format('Y/m') }}</span>
        </div>

        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="attendance-list__nav-btn">
            翌月
            <img src="{{ asset('img/yajirushi.png') }}" alt="next" class="nav-arrow-right">
        </a>
    </div>

    <table class="attendance-list__table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
            @php
            $restMinutes = 0;
            foreach($attendance->rests as $rest) {
            if($rest->rest_start && $rest->rest_end) {
            $start = \Carbon\Carbon::parse($rest->rest_start)->second(0);
            $end = \Carbon\Carbon::parse($rest->rest_end)->second(0);
            $restMinutes += $start->diffInMinutes($end);
            }
            }

            $workMinutes = 0;
            if($attendance->check_in && $attendance->check_out) {
            $in = \Carbon\Carbon::parse($attendance->check_in)->second(0);
            $out = \Carbon\Carbon::parse($attendance->check_out)->second(0);
            $workMinutes = $in->diffInMinutes($out);
            }

            $actualMinutes = max(0, $workMinutes - $restMinutes);

            $restH = floor($restMinutes / 60);
            $restM = $restMinutes % 60;

            $workH = floor($actualMinutes / 60);
            $workM = $actualMinutes % 60;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($attendance->date)->isoFormat('MM/DD(ddd)') }}</td>
                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '' }}</td>
                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '' }}</td>
                <td>{{ $restH }}:{{ sprintf('%02d', $restM) }}</td>
                <td>{{ $workH }}:{{ sprintf('%02d', $workM) }}</td>
                <td>
                    <a href="{{ route('attendance.detail', $attendance->id) }}" class="attendance-list__detail-link">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection