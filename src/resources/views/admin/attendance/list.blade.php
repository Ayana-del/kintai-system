@extends('layouts.admin')

@section('title', '勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <h2 class="attendance-list__title">
        <span>{{ $displayDate->format('Y年n月j日') }}の勤怠</span>
    </h2>

    <div class="date-nav">
        <a href="{{ route('admin.attendance.list', ['date' => $displayDate->copy()->subDay()->format('Y-m-d')]) }}" class="date-nav__link">← 前日</a>

        <div class="date-nav__current">
            <img src="{{ asset('img/calendar.png') }}" alt="calendar icon" class="calendar-icon">
            <span>{{ $displayDate->format('Y/m/d') }}</span>
        </div>

        <a href="{{ route('admin.attendance.list', ['date' => $displayDate->copy()->addDay()->format('Y-m-d')]) }}" class="date-nav__link">翌日 →</a>
    </div>

    <div class="table-card">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
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
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->formatted_check_in }}</td>
                    <td>{{ $attendance->formatted_check_out }}</td>
                    <td>{{ $attendance->total_rest_time }}</td>
                    <td>{{ $attendance->total_work_time }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection