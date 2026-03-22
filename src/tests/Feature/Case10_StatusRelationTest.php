<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class Case10_StatusRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_勤務外の場合、出勤ボタンのみが活性化される()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '勤務外']);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤');
        $response->assertDontSee('退勤');
        $response->assertDontSee('休憩入');
        $response->assertDontSee('休憩戻');
    }

    public function test_出勤中の場合、退勤と休憩入ボタンが活性化される()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '出勤中']);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertDontSee('出勤');
        $response->assertSee('退勤');
        $response->assertSee('休憩入');
        $response->assertDontSee('休憩戻');
    }

    public function test_休憩中の場合、休憩戻ボタンのみが活性化される()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '休憩中']);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertDontSee('出勤');
        $response->assertDontSee('退勤');
        $response->assertDontSee('休憩入');
        $response->assertSee('休憩戻');
    }

    public function test_退勤済の場合、全てのボタンが非活性化される()
    {
        /** @var User $user */
        $user = User::factory()->create(['status' => '退勤済']);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertDontSee('出勤');
        $response->assertDontSee('退勤');
        $response->assertDontSee('休憩入');
        $response->assertDontSee('休憩戻');
    }
}
