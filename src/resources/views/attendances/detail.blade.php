@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail__container">
        <h2 class="attendance-detail__heading">勤怠詳細</h2>

        <form action="{{ route('attendance.correction.store', ['id' => $attendance->id]) }}" method="POST">
            @csrf
            <div class="attendance-detail__card">
                <table class="attendance-detail__table">
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">名前</th>
                        <td class="attendance-detail__data">
                            <span class="attendance-detail__text">{{ $attendance->user->name }}</span>
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">日付</th>
                        <td class="attendance-detail__data">
                            <div class="attendance-detail__date-group">
                                <span>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</span>
                                <span>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">出勤・退勤</th>
                        <td class="attendance-detail__data">
                            <div class="attendance-detail__input-group">
                                <input type="text" name="start_time" class="attendance-detail__input" value="{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}">
                                <span class="attendance-detail__tilde">〜</span>
                                <input type="text" name="end_time" class="attendance-detail__input" value="{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '' }}">
                            </div>
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">休憩</th>
                        <td class="attendance-detail__data">
                            <div class="attendance-detail__input-group">
                                @php $rest1 = $attendance->rests->get(0); @endphp
                                <input type="text" name="rest_start_times[]" class="attendance-detail__input" value="{{ $rest1 ? \Carbon\Carbon::parse($rest1->rest_start)->format('H:i') : '' }}">
                                <span class="attendance-detail__tilde">〜</span>
                                <input type="text" name="rest_end_times[]" class="attendance-detail__input" value="{{ $rest1 && $rest1->rest_end ? \Carbon\Carbon::parse($rest1->rest_end)->format('H:i') : '' }}">
                            </div>
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">休憩2</th>
                        <td class="attendance-detail__data">
                            <div class="attendance-detail__input-group">
                                @php $rest2 = $attendance->rests->get(1); @endphp
                                <input type="text" name="rest_start_times[]" class="attendance-detail__input" value="{{ $rest2 ? \Carbon\Carbon::parse($rest2->rest_start)->format('H:i') : '' }}">
                                <span class="attendance-detail__tilde">〜</span>
                                <input type="text" name="rest_end_times[]" class="attendance-detail__input" value="{{ $rest2 && $rest2->rest_end ? \Carbon\Carbon::parse($rest2->rest_end)->format('H:i') : '' }}">
                            </div>
                        </td>
                    </tr>
                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">備考</th>
                        <td class="attendance-detail__data">
                            <textarea name="remarks" class="attendance-detail__textarea">{{ old('remarks', $attendance->remarks) }}</textarea>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="attendance-detail__actions">
                <button type="submit" class="attendance-detail__submit">修正</button>
            </div>
        </form>
    </div>
</div>
@endsection