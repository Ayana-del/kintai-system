@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/index.css') }}">
@endsection

@section('content')
<div class="attendance__container">
    <div class="attendance__main">
        <div class="attendance__status">
            @if(!$attendance) 勤務外
            @elseif($attendance->status == 2) 出勤中
            @elseif($attendance->status == 3) 休憩中
            @elseif($attendance->status == 4) 退勤済
            @endif
        </div>

        <p id="realtime-date" class="attendance__date"></p>
        <p id="realtime-clock" class="attendance__clock"></p>

        @if (session('message'))
        <div class="attendance__alert">
            {{ session('message') }}
        </div>
        @endif

        <div class="attendance__panel">
            @if(!$attendance)
            <form action="{{ route('attendance.check-in') }}" method="post">
                @csrf
                <button class="attendance__button-submit" type="submit">出勤</button>
            </form>
            @endif

            @if($attendance && $attendance->status == 2)
            <form action="{{ route('attendance.check-out') }}" method="post">
                @csrf
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                <button class="attendance__button-submit" type="submit">退勤</button>
            </form>
            <form action="{{ route('attendance.rest-start') }}" method="post">
                @csrf
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                <button class="attendance__button-rest" type="submit">休憩入</button>
            </form>
            @endif

            @if($attendance && $attendance->status == 3)
            <form action="{{ route('attendance.rest-end') }}" method="post">
                @csrf
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                <button class="attendance__button-rest" type="submit">休憩戻</button>
            </form>
            @endif
        </div>
    </div>
</div>

<script>
    function updateDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const date = String(now.getDate()).padStart(2, '0');
        const days = ["日", "月", "火", "水", "木", "金", "土"];
        document.getElementById('realtime-date').textContent = `${year}年${month}月${date}日(${days[now.getDay()]})`;
        document.getElementById('realtime-clock').textContent = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>
@endsection