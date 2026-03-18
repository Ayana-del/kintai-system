@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <div class="attendance-list__title">
        勤務一覧
    </div>

    {{-- 月次ナビゲーション --}}
    <div class="attendance-list__nav">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="attendance-list__nav-btn">
            ← 前月
        </a>
        <div class="attendance-list__current-month">
            <img src="{{ asset('img/calendar.png') }}" alt="calendar" class="attendance-list__calendar-icon">
            <span class="attendance-list__month-text">{{ \Carbon\Carbon::parse($month)->format('Y/m') }}</span>
        </div>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="attendance-list__nav-btn">
            翌月 →
        </a>
    </div>

    {{-- 勤怠テーブル --}}
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
            <tr>
                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('m/d') }}({{ ['日','月','火','水','木','金','土'][\Carbon\Carbon::parse($attendance->date)->dayOfWeek] }})</td>
                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '' }}</td>
                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '' }}</td>
                <td>
                    @php
                    $totalRest = 0;
                    foreach($attendance->rests as $rest) {
                    if($rest->rest_start && $rest->rest_end) {
                    $totalRest += \Carbon\Carbon::parse($rest->rest_end)->diffInMinutes(\Carbon\Carbon::parse($rest->rest_start));
                    }
                    }
                    $hours = floor($totalRest / 60);
                    $minutes = $totalRest % 60;
                    @endphp
                    {{ $totalRest > 0 ? sprintf('%d:%02d', $hours, $minutes) : '' }}
                </td>
                <td>
                    @php
                    if($attendance->check_in && $attendance->check_out) {
                    $totalWork = \Carbon\Carbon::parse($attendance->check_out)->diffInMinutes(\Carbon\Carbon::parse($attendance->check_in)) - $totalRest;
                    $wHours = floor($totalWork / 60);
                    $wMinutes = $totalWork % 60;
                    echo sprintf('%d:%02d', $wHours, $wMinutes);
                    }
                    @endphp
                </td>
                <td>
                    <a href="{{ route('attendance.detail', ['id' => $attendance->id ?? 0, 'date' => $attendance->date]) }}" class="attendance-list__detail-link">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection