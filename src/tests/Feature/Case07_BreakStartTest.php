<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class Case07_BreakStartTest extends TestCase
{
    use RefreshDatabase;

    public function test_休憩入ボタンが正しく機能する()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '出勤中']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->post('/attendance/rest-start');

        $this->assertDatabaseHas('rests', [
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::now()->format('H:i:s'),
        ]);

        $user->refresh();
        $this->assertEquals('休憩中', $user->status);
    }

    public function test_休憩は1日何回でも可能である()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '出勤中']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->actingAs($user)->post('/attendance/rest-start');
        $this->actingAs($user)->post('/attendance/rest-end');

        $response = $this->actingAs($user)->post('/attendance/rest-start');

        $this->assertEquals(2, Rest::where('attendance_id', $attendance->id)->count());
    }

    public function test_休憩入した時刻が管理画面の勤怠一覧に正しく反映される()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '出勤中']);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $startTime = Carbon::now()->setSeconds(0);
        $this->actingAs($user)->post('/attendance/rest-start');

        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertSee($startTime->format('H:i'));
    }
}
