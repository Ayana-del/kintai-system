@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/request.css') }}">
@endsection

@section('content')
<div class="requests">
    <div class="requests__container">
        <h2 class="requests__heading">申請一覧</h2>

        <div class="requests__tabs">
            <a href="{{ route('stamp_correction_request.list', ['status' => '0']) }}"
                class="requests__tab {{ $status == '0' ? 'requests__tab--active' : '' }}">承認待ち</a>

            <a href="{{ route('stamp_correction_request.list', ['status' => '1']) }}"
                class="requests__tab {{ $status == '1' ? 'requests__tab--active' : '' }}">承認済み</a>
        </div>

        <div class="requests__line"></div>

        <div class="requests__card">
            <table class="requests__table">
                <thead>
                    <tr class="requests__row">
                        <th class="requests__label">状態</th>
                        <th class="requests__label">名前</th>
                        <th class="requests__label">対象日時</th>
                        <th class="requests__label">申請理由</th>
                        <th class="requests__label">申請日時</th>
                        <th class="requests__label">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $item)
                    <tr class="requests__row">
                        <td class="requests__data">
                            {{ $item->status == 0 ? '承認待ち' : '承認済み' }}
                        </td>
                        <td class="requests__data">{{ Auth::user()->name }}</td>
                        <td class="requests__data">
                            {{ \Carbon\Carbon::parse($item->attendance->date)->format('Y/m/d') }}
                        </td>
                        <td class="requests__data">{{ $item->reason }}</td>
                        <td class="requests__data">
                            {{ $item->created_at->format('Y/m/d') }}
                        </td>
                        <td class="requests__data">
                            <a href="{{ route('attendance.detail', ['id' => $item->attendance_id]) }}" class="requests__link">詳細</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection