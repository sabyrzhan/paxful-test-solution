<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiply_success()
    {
        $response = $this->get('/?n=2');

        $response->assertStatus(200);
        $response->assertJsonPath("result", 4);
    }

    public function test_no_param_bad_request()
    {
        $response = $this->get('/');

        $response->assertStatus(400);
        $response->assertJsonPath("error", "Please specify n integer param");
    }

    public function test_blacklisted() {
        $response = $this->get('/blacklisted');

        $response->assertStatus(444);
        $response->assertJsonPath("error", "Access denied");

        $count = DB::select('select count(*) c from user_log');
        self::assertTrue(count($count) > 0);
        self::assertEquals(1, $count[0]->c);
    }
}
