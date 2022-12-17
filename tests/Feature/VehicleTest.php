<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetTheirOwnVehicles()
    {
        $john = User::factory()->create();
        $vehicleForJohn = Vehicle::factory()->create([
            'user_id' => $john->id
        ]);

        $adam = User::factory()->create();
        $vehicleForAdam = Vehicle::factory()->create([
            'user_id' => $adam->id
        ]);

        $response = $this->actingAs($john)->getJson('/api/v1/vehicles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.plate_number', $vehicleForJohn->plate_number)
            ->assertJsonMissing($vehicleForAdam->toArray());
    }

    public function testUserCanCreateVehicle()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/vehicles', [
            'plate_number' => 'AAA111',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => ['0' => 'plate_number'],
            ])
            ->assertJsonPath('data.plate_number', 'AAA111');

        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA111',
        ]);
    }

    public function testUserCanUpdateTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson('/api/v1/vehicles/' . $vehicle->id, [
            'plate_number' => 'AAA123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => ['0' => 'plate_number'],
            ])
            ->assertJsonPath('data.plate_number', 'AAA123');

        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA123',
        ]);
    }

    public function testUserCanDeleteTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson('/api/v1/vehicles/' . $vehicle->id);

        $response->assertNoContent();

        $this->assertModelMissing($vehicle)
            ->assertDatabaseCount('vehicles', 0);
    }
}
