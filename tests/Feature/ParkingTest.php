<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanStartParking()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $response = $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'start_time'  => now()->toDateTimeString(),
                    'stop_time'   => null,
                    'total_price' => 0,
                ],
            ]);

        $this->assertDatabaseCount('parkings', '1');
    }
//
//    public function testUserCanStopParking()
//    {
//        Sanctum::actingAs(
//            $user = User::factory()->create(),
//            ['*']
//        );
//
//        $parking = Parking::factory()
//            ->for($user)
//            ->create([
//                'start_time'  => Carbon::now()->subHours(2),
//                'stop_time'   => null,
//                'zone_id'     => 1,
//                'total_price' => null,
//            ]);
//
//        $this->travel(2)->hours();
//
//        $response = $this->actingAs($user)->patchJson('/api/parking/' . $parking->id . '/stop');
//
//        $result = Parking::findOrFail($parking->id);
//
//        $response->assertOk()
//            ->assertJsonStructure(['data'])
//            ->assertJsonCount(4, 'data')
//            ->assertExactJson([
//                'data' => [
//                    'start_time'  => $result->start_time->toDateTimeString(),
//                    'stop_time'   => $result->stop_time->toDateTimeString(),
//                    'duration'    => $result->durationInSeconds,
//                    'total_price' => $result->total_price,
//                ],
//            ]);
//
//        $this->assertDatabaseCount('parkings', '1');
//    }
}
