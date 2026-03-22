@extends('layouts.admin')

@section('title', 'スタッフ一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff/list.css') }}">
@endsection

@section('content')
<div class="staff-list">
    <div class="staff-list__header">
        <h1 class="staff-list__title">スタッフ一覧</h1>
    </div>

    <div class="staff-list__inner">
        <table class="staff-list__table">
            <thead>
                <tr class="staff-list__row--header">
                    <th class="staff-list__label--name">名前</th>
                    <th class="staff-list__label--email">メールアドレス</th>
                    <th class="staff-list__label--link">月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="staff-list__row">
                    <td class="staff-list__data--name">
                        <span class="staff-list__text">{{ $user->name }}</span>
                    </td>
                    <td class="staff-list__data--email">
                        <span class="staff-list__text">{{ $user->email }}</span>
                    </td>
                    <td class="staff-list__data--link">
                        <a href="{{ route('admin.staff.attendance', ['id' => $user->id]) }}" class="staff-list__link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection