<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class Case09_CheckOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_退勤ボタンが正しく機能する()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '出勤中']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'check_in' => Carbon::now()->subHours(8)->format('H:i:s'),
        ]);

        $response = $this->actingAs($user)->post('/attendance/check-out');

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'check_out' => Carbon::now()->format('H:i:s'),
        ]);

        $user->refresh();
        $this->assertEquals('退勤済', $user->status);
    }

    public function test_退勤した時刻が管理画面の勤怠一覧に正しく反映される()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '出勤中']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'check_in' => Carbon::now()->subHours(8)->format('H:i:s'),
        ]);

        $checkOutTime = Carbon::now()->setSeconds(0);
        $this->actingAs($user)->post('/attendance/check-out');

        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertSee($checkOutTime->format('H:i'));
    }
}
