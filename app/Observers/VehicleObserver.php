<?php

namespace App\Observers;

use App\Models\Vehicle;

class VehicleObserver
{
    public function creating(Vehicle $vehicle)
    {
        if (auth()->check()) {
            $vehicle->user_id = auth()->id();
        }
    }
}
