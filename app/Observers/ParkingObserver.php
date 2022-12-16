<?php

namespace App\Observers;

use App\Models\Parking;

class ParkingObserver
{
    public function creating(Parking $parking)
    {
        $parking->user_id = auth()->id() ?? NULL;
        $parking->start_time = now();
    }
}
