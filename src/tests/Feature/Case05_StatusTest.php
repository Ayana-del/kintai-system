<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class Case05_StatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_勤務外の場合、勤怠ステータスが正しく表示される()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'status' => '勤務外'
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    public function test_出勤中の場合、勤怠ステータスが正しく表示される()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'status' => '出勤中'
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    public function test_休憩中の場合、勤怠ステータスが正しく表示される()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'status' => '休憩中'
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    public function test_退勤済の場合、勤怠ステータスが正しく表示される()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'status' => '退勤済'
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
