@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail__container">
        <h2 class="attendance-detail__heading">勤怠詳細</h2>

        @php
        $isPending = $attendance->isPending();
        $pendingRequest = $isPending ? $attendance->latestPendingRequest : null;

        $pendingCheckIn = $isPending ? $pendingRequest->details->where('type', 'check_in')->first() : null;
        $pendingCheckOut = $isPending ? $pendingRequest->details->where('type', 'check_out')->first() : null;

        $pendingRestStarts = $isPending ? $pendingRequest->details->where('type', 'rest_start')->values() : collect();
        $pendingRestEnds = $isPending ? $pendingRequest->details->where('type', 'rest_end')->values() : collect();
        @endphp

        <form action="{{ route('attendance.correction.store', ['id' => $attendance->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="year" value="{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}">
            <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}">

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
                            @if($isPending)
                            <div class="attendance-detail__input-group">
                                <span class="attendance-detail__text">{{ $pendingCheckIn ? \Carbon\Carbon::parse($pendingCheckIn->modified_time)->format('H:i') : '' }}</span>
                                <span class="attendance-detail__tilde">〜</span>
                                <span class="attendance-detail__text">{{ $pendingCheckOut ? \Carbon\Carbon::parse($pendingCheckOut->modified_time)->format('H:i') : '' }}</span>
                            </div>
                            @else
                            <div class="attendance-detail__input-group">
                                <div class="attendance-detail__input-box">
                                    <input type="text" name="check_in" class="attendance-detail__input" value="{{ old('check_in', \Carbon\Carbon::parse($attendance->check_in)->format('H:i')) }}">
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

                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">休憩</th>
                        <td class="attendance-detail__data">
                            @if($isPending)
                            @php
                            $pStart1 = $pendingRestStarts->get(0);
                            $pEnd1 = $pendingRestEnds->get(0);
                            @endphp
                            <div class="attendance-detail__input-group">
                                <span class="attendance-detail__text">{{ $pStart1 ? \Carbon\Carbon::parse($pStart1->modified_time)->format('H:i') : '' }}</span>
                                <span class="attendance-detail__tilde">〜</span>
                                <span class="attendance-detail__text">{{ $pEnd1 ? \Carbon\Carbon::parse($pEnd1->modified_time)->format('H:i') : '' }}</span>
                            </div>
                            @else
                            @php $rest1 = $attendance->rests->get(0); @endphp
                            <div class="attendance-detail__input-group">
                                <div class="attendance-detail__input-box">
                                    <input type="text" name="rest_start_times[]" class="attendance-detail__input" value="{{ old('rest_start_times.0', $rest1 ? \Carbon\Carbon::parse($rest1->rest_start)->format('H:i') : '') }}">
                                    @error('rest_start_times.0') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <span class="attendance-detail__tilde">〜</span>
                                <div class="attendance-detail__input-box">
                                    <input type="text" name="rest_end_times[]" class="attendance-detail__input" value="{{ old('rest_end_times.0', $rest1 && $rest1->rest_end ? \Carbon\Carbon::parse($rest1->rest_end)->format('H:i') : '') }}">
                                    @error('rest_end_times.0') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>

                    <tr class="attendance-detail__row">
                        <th class="attendance-detail__label">休憩2</th>
                        <td class="attendance-detail__data">
                            @if($isPending)
                            @php
                            $pStart2 = $pendingRestStarts->get(1);
                            $pEnd2 = $pendingRestEnds->get(1);
                            @endphp
                            <div class="attendance-detail__input-group">
                                <span class="attendance-detail__text">{{ $pStart2 ? \Carbon\Carbon::parse($pStart2->modified_time)->format('H:i') : '' }}</span>
                                <span class="attendance-detail__tilde">〜</span>
                                <span class="attendance-detail__text">{{ $pEnd2 ? \Carbon\Carbon::parse($pEnd2->modified_time)->format('H:i') : '' }}</span>
                            </div>
                            @else
                            @php $rest2 = $attendance->rests->get(1); @endphp
                            <div class="attendance-detail__input-group">
                                <div class="attendance-detail__input-box">
                                    <input type="text" name="rest_start_times[]" class="attendance-detail__input" value="{{ old('rest_start_times.1', $rest2 ? \Carbon\Carbon::parse($rest2->rest_start)->format('H:i') : '') }}">
                                    @error('rest_start_times.1') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <span class="attendance-detail__tilde">〜</span>
                                <div class="attendance-detail__input-box">
                                    <input type="text" name="rest_end_times[]" class="attendance-detail__input" value="{{ old('rest_end_times.1', $rest2 && $rest2->rest_end ? \Carbon\Carbon::parse($rest2->rest_end)->format('H:i') : '') }}">
                                    @error('rest_end_times.1') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>

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