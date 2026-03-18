@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail__container">
        <h2 class="attendance-detail__heading">勤怠詳細</h2>

        @php
        // データが存在しない（IDがない）場合は承認待ちチェックをスキップ
        $isPending = isset($attendance->id) ? $attendance->isPending() : false;
        $pendingRequest = $isPending ? $attendance->latestPendingRequest : null;

        $pendingCheckIn = $isPending ? $pendingRequest->details->where('type', 'check_in')->first() : null;
        $pendingCheckOut = $isPending ? $pendingRequest->details->where('type', 'check_out')->first() : null;

        $pendingRestStarts = $isPending ? $pendingRequest->details->where('type', 'rest_start')->values() : collect();
        $pendingRestEnds = $isPending ? $pendingRequest->details->where('type', 'rest_end')->values() : collect();

        // 表示用の日付
        $displayDate = \Carbon\Carbon::parse($attendance->date);
        @endphp

        {{-- IDがない場合は 0 を渡す --}}
        <form action="{{ route('attendance.correction.store', ['id' => $attendance->id ?? 0]) }}" method="POST">
            @csrf
            {{-- 新規作成用に日付を隠しパラメータで保持 --}}
            <input type="hidden" name="date" value="{{ $attendance->date }}">

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
                                <span>{{ $displayDate->format('Y年') }}</span>
                                <span>{{ $displayDate->format('n月j日') }}</span>
                            </div>
                        </td>
                    </tr>

                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">出勤・退勤</th>
                        <td class="attendance-detail__data">
                            @if($isPending)
                            <div class="attendance-detail__input-group">
                                <span class="attendance-detail__text">{{ $pendingCheckIn ? \Carbon\Carbon::parse($pendingCheckIn->modified_time)->format('H:i') : '' }}</span>
                                <span class="attendance-detail__tilde">〜</span>
                                <span class="attendance-detail__text">{{ $pendingCheckOut ? \Carbon\Carbon::parse($pendingCheckOut->modified_time)->format('H:i') : '' }}</span>
                            </div>
                            @else
                            <div class="attendance-detail__input-group">
                                <div class="attendance-detail__input-box">
                                    {{-- check_in が null の場合を考慮して parse を回避 --}}
                                    <input type="text" name="check_in" class="attendance-detail__input" value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}">
                                    @error('check_in') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <span class="attendance-detail__tilde">〜</span>
                                <div class="attendance-detail__input-box">
                                    <input type="text" name="check_out" class="attendance-detail__input" value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}">
                                    @error('check_out') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>

                    {{-- 休憩1・2も同様に $attendance->rests->get(0) があるかチェックして表示 --}}
                    @for ($i = 0; $i < 2; $i++)
                        <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">休憩{{ $i + 1 }}</th>
                        <td class="attendance-detail__data">
                            @if($isPending)
                            @php
                            $pStart = $pendingRestStarts->get($i);
                            $pEnd = $pendingRestEnds->get($i);
                            @endphp
                            <div class="attendance-detail__input-group">
                                <span class="attendance-detail__text">{{ $pStart ? \Carbon\Carbon::parse($pStart->modified_time)->format('H:i') : '' }}</span>
                                <span class="attendance-detail__tilde">〜</span>
                                <span class="attendance-detail__text">{{ $pEnd ? \Carbon\Carbon::parse($pEnd->modified_time)->format('H:i') : '' }}</span>
                            </div>
                            @else
                            @php $rest = $attendance->rests->get($i); @endphp
                            <div class="attendance-detail__input-group">
                                <div class="attendance-detail__input-box">
                                    <input type="text" name="rest_start_times[]" class="attendance-detail__input" value="{{ old('rest_start_times.'.$i, $rest ? \Carbon\Carbon::parse($rest->rest_start)->format('H:i') : '') }}">
                                </div>
                                <span class="attendance-detail__tilde">〜</span>
                                <div class="attendance-detail__input-box">
                                    <input type="text" name="rest_end_times[]" class="attendance-detail__input" value="{{ old('rest_end_times.'.$i, ($rest && $rest->rest_end) ? \Carbon\Carbon::parse($rest->rest_end)->format('H:i') : '') }}">
                                </div>
                            </div>
                            @endif
                        </td>
                        </tr>
                        @endfor

                        <tr class="attendance-detail__row">
                            <th class="attendance-detail__label">備考</th>
                            <td class="attendance-detail__data">
                                @if($isPending)
                                <span class="attendance-detail__text">{{ $pendingRequest->reason }}</span>
                                @else
                                <textarea name="reason" class="attendance-detail__textarea">{{ old('reason', $attendance->remarks) }}</textarea>
                                @error('reason') <p class="error-message">{{ $message }}</p> @enderror
                                @endif
                            </td>
                        </tr>
                </table>
            </div>

            <div class="attendance-detail__actions">
                @if($isPending)
                <p class="attendance-detail__pending-msg">*承認待ちのため修正はできません。</p>
                @else
                <button type="submit" class="attendance-detail__submit">修正</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection