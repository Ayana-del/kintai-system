<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class Case11_AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_勤怠情報が一覧で表示される()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $user = User::factory()->create(['name' => 'テスト太郎']);
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
    }

    public function test_前日の勤怠情報が表示される()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $user = User::factory()->create(['name' => '昨日の一郎']);
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $yesterday,
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=' . $yesterday);

        $response->assertSee('昨日の一郎');
    }

    public function test_翌日の勤怠情報が表示される()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $user = User::factory()->create(['name' => '明日のみどり']);
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $tomorrow,
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=' . $tomorrow);

        $response->assertSee('明日のみどり');
    }
}
