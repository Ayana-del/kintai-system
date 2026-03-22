@extends('layouts.admin')

@section('title', '申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/requests/list.css') }}">
@endsection

@section('content')
<div class="requests">
    <div class="requests__header">
        <h1 class="requests__title">申請一覧</h1>
    </div>

    <div class="requests__tabs">
        <a href="{{ route('admin.request.list', ['status' => '0']) }}" class="requests__tab {{ $status == '0' ? 'is-active' : '' }}">承認待ち</a>
        <a href="{{ route('admin.request.list', ['status' => '1']) }}" class="requests__tab {{ $status == '1' ? 'is-active' : '' }}">承認済み</a>
    </div>

    <div class="requests__inner">
        <table class="requests__table">
            <thead>
                <tr class="requests__row--header">
                    <th class="requests__label">状態</th>
                    <th class="requests__label">名前</th>
                    <th class="requests__label">対象日時</th>
                    <th class="requests__label">申請理由</th>
                    <th class="requests__label">申請日時</th>
                    <th class="requests__label">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                <tr class="requests__row">
                    <td class="requests__data">
                        <span class="requests__status">{{ $request->status == 1 ? '承認済み' : '承認待ち' }}</span>
                    </td>
                    <td class="requests__data">{{ $request->user->name }}</td>
                    <td class="requests__data">{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>
                    <td class="requests__data">{{ $request->reason }}</td>
                    <td class="requests__data">{{ $request->created_at->format('Y/m/d') }}</td>
                    <td class="requests__data">
                        <a href="{{ route('admin.request.approve', ['attendance_correct_request_id' => $request->id]) }}" class="requests__link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection