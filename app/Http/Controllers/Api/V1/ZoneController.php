<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ZoneResource;
use App\Models\Zone;

/**
 * @group Parking Zones
 */
class ZoneController extends Controller
{
    public function index()
    {
        return ZoneResource::collection(Zone::all());
    }
}
