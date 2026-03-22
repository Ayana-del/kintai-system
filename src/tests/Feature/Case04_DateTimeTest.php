<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class Case04_DateTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_現在の日時情報がUIと同じ形式で出力されている()
    {
        /** @var User $user */
        $user = User::factory()->create();

        Carbon::setTestNow(Carbon::create(2026, 3, 19, 20, 43));

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);

        $response->assertSee(Carbon::now()->format('Y年n月j日'));
        $response->assertSee(Carbon::now()->isoFormat('ddd'));
    }
}
