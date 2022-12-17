<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZoneTest extends TestCase
{
    use RefreshDatabase;

    public function testPublicUserCanGetAllZones()
    {
        $response = $this->getJson('/api/v1/zones');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                ['*' => 'name', 'price_per_hour'],
            ])
            ->assertJsonPath('0.name', 'Green Zone')
            ->assertJsonPath('0.price_per_hour', 100);
    }
}
