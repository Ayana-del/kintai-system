@extends('layouts.admin')

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail__header">
        <h1 class="attendance-detail__title">勤怠詳細</h1>
    </div>

    @if ($errors->any())
    <div class="error-message">
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('admin.attendance.update', ['id' => $attendance->id]) }}" method="POST">
        @csrf
        <div class="attendance-detail__inner">
            <table class="attendance-detail__table">
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__label">名前</th>
                    <td class="attendance-detail__data">
                        <span class="attendance-detail__text--name">{{ $attendance->user->name }}</span>
                    </td>
                </tr>
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__label">日付</th>
                    <td class="attendance-detail__data">
                        <div class="attendance-detail__date-display">
                            <span class="attendance-detail__date-year">{{ $attendance->year }}年</span>
                            <span class="attendance-detail__date-day">{{ $attendance->month }}月{{ $attendance->day }}日</span>
                        </div>
                    </td>
                </tr>
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__label">出勤・退勤</th>
                    <td class="attendance-detail__data">
                        <div class="attendance-detail__input-group">
                            <input type="text" name="check_in" value="{{ old('check_in', $attendance->formatted_check_in) }}" class="attendance-detail__input">
                            <span class="attendance-detail__separator">～</span>
                            <input type="text" name="check_out" value="{{ old('check_out', $attendance->formatted_check_out) }}" class="attendance-detail__input">
                        </div>
                    </td>
                </tr>

                @foreach($attendance->rests as $index => $rest)
                <tr class="attendance-detail__row">
                    <th class="attendance-detail__label">休憩{{ $index > 0 ? $index + 1 : '' }}</th>
                    <td class="attendance-detail__data">
                        <div class="attendance-detail__input-group">
                            <input type="text" name="rests[{{ $rest->id }}][start]" value="{{ old('rests.'.$rest->id.'.start', $rest->formatted_start) }}" class="attendance-detail__input">
                            <span class="attendance-detail__separator">～</span>
                            <input type="text" name="rests[{{ $rest->id }}][end]" value="{{ old('rests.'.$rest->id.'.end', $rest->formatted_end) }}" class="attendance-detail__input">
                        </div>
                    </td>
                </tr>
                @endforeach

                <tr class="attendance-detail__row">
                    <th class="attendance-detail__label">備考</th>
                    <td class="attendance-detail__data">
                        <textarea name="remarks" class="attendance-detail__textarea">{{ old('remarks', $attendance->remarks) }}</textarea>
                    </td>
                </tr>
            </table>
        </div>

        <div class="attendance-detail__actions">
            <button type="submit" class="attendance-detail__button">修正</button>
        </div>
    </form>
</div>
@endsection