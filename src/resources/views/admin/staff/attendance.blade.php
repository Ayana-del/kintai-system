@extends('layouts.admin')

@section('title', 'スタッフ別勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff/attendance.css') }}">
@endsection

@section('content')
<div class="staff-attendance">
    <div class="staff-attendance__header">
        <h1 class="staff-attendance__title">{{ $user->name }}さんの勤怠</h1>
    </div>

    <div class="staff-attendance__nav">
        <a href="{{ route('admin.staff.attendance', ['id' => $user->id, 'month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="staff-attendance__month-link">
            <img src="{{ asset('img/yajirushi.png') }}" alt="" class="staff-attendance__arrow-icon">
            <span>前月</span>
        </a>

        <div class="staff-attendance__current-month">
            <img src="{{ asset('img/calendar.png') }}" alt="" class="staff-attendance__calendar-icon">
            <span class="staff-attendance__month-text">{{ $currentMonth->format('Y/m') }}</span>
        </div>

        <a href="{{ route('admin.staff.attendance', ['id' => $user->id, 'month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="staff-attendance__month-link">
            <span>翌月</span>
            <img src="{{ asset('img/yajirushi.png') }}" alt="" class="staff-attendance__arrow-icon staff-attendance__arrow-icon--next">
        </a>
    </div>

    <div class="staff-attendance__inner">
        <table class="staff-attendance__table">
            <thead>
                <tr class="staff-attendance__row--header">
                    <th class="staff-attendance__label">日付</th>
                    <th class="staff-attendance__label">出勤</th>
                    <th class="staff-attendance__label">退勤</th>
                    <th class="staff-attendance__label">休憩</th>
                    <th class="staff-attendance__label">合計</th>
                    <th class="staff-attendance__label">詳細</th>
                </tr>
            </thead>
            <tbody>
                @php
                $startOfMonth = $currentMonth->copy()->startOfMonth();
                $endOfMonth = $currentMonth->copy()->endOfMonth();
                @endphp
                @for($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay())
                @php
                $attendance = $attendances->firstWhere('date', $date->format('Y-m-d'));
                @endphp
                <tr class="staff-attendance__row">
                    <td class="staff-attendance__data">
                        {{ $date->format('m/d') }}({{ ['日', '月', '火', '水', '木', '金', '土'][$date->dayOfWeek] }})
                    </td>
                    <td class="staff-attendance__data">
                        {{ $attendance && $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '' }}
                    </td>
                    <td class="staff-attendance__data">
                        {{ $attendance && $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '' }}
                    </td>
                    <td class="staff-attendance__data">
                        {{ $attendance && $attendance->total_rest_time ? substr($attendance->total_rest_time, 0, 5) : '' }}
                    </td>
                    <td class="staff-attendance__data">
                        {{ $attendance && $attendance->total_work_time ? substr($attendance->total_work_time, 0, 5) : '' }}
                    </td>
                    <td class="staff-attendance__data">
                        @if($attendance)
                        <a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}" class="staff-attendance__link">詳細</a>
                        @endif
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <div class="staff-attendance__export">
        <a href="{{ route('admin.staff.csv', ['id' => $user->id, 'month' => $currentMonth->format('Y-m')]) }}" class="staff-attendance__csv-btn">CSV出力</a>
    </div>
</div>
@endsection