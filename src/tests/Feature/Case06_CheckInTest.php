<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class Case06_CheckInTest extends TestCase
{
    use RefreshDatabase;

    public function test_出勤ボタンが正しく機能する()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '勤務外']);

        $response = $this->actingAs($user)->post('/attendance/check-in');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $user->refresh();
        $this->assertEquals('出勤中', $user->status);
    }

    public function test_出勤は1日1回のみ可能である()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '出勤中']);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'check_in' => Carbon::now()->subHours(1),
        ]);

        $response = $this->actingAs($user)->post('/attendance/check-in');

        $this->assertEquals(1, Attendance::where('user_id', $user->id)->count());
    }

    public function test_出勤した時刻が管理画面の勤怠一覧に正しく反映される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $checkInTime = Carbon::now()->setSeconds(0);

        $this->actingAs($user)->post('/attendance/check-in');

        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertSee($checkInTime->format('H:i'));
    }
}
