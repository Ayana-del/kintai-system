<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class Case08_BreakEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_休憩戻ボタンが正しく機能する()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '休憩中']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        Rest::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::now()->subMinutes(30)->format('H:i:s'),
            'end_time' => null,
        ]);

        $response = $this->actingAs($user)->post('/attendance/rest-end');

        $this->assertDatabaseHas('rests', [
            'attendance_id' => $attendance->id,
            'end_time' => Carbon::now()->format('H:i:s'),
        ]);

        $user->refresh();
        $this->assertEquals('出勤中', $user->status);
    }

    public function test_休憩戻した時刻が管理画面の勤怠一覧に正しく反映される()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '休憩中']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        Rest::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::now()->subMinutes(30)->format('H:i:s'),
            'end_time' => null,
        ]);

        $endTime = Carbon::now()->setSeconds(0);
        $this->actingAs($user)->post('/attendance/rest-end');

        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertSee($endTime->format('H:i'));
    }
}
