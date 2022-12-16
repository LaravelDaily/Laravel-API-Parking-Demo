<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Zone;

class ZoneController extends Controller
{
    public function index()
    {
        return Zone::select('name', 'price_per_hour')->get();
    }
}
