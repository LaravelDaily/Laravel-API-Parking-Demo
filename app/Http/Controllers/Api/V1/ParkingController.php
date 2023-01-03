<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParkingResource;
use App\Models\Parking;
use App\Services\ParkingPriceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Parking
 */
class ParkingController extends Controller
{
    public function index()
    {
        $activeParkings = Parking::active()->latest('start_time')->get();

        return ParkingResource::collection($activeParkings);
    }

    public function history()
    {
        $stoppedParkings = Parking::stopped()
            ->with(['vehicle' => fn ($q) => $q->withTrashed()])
            ->latest('stop_time')
            ->get();

        return ParkingResource::collection($stoppedParkings);
    }

    public function start(Request $request)
    {
        $parkingData = $request->validate([
            'vehicle_id' => [
                'required',
                'integer',
                'exists:vehicles,id,deleted_at,NULL,user_id,'.auth()->id(),
            ],
            'zone_id'    => ['required', 'integer', 'exists:zones,id'],
        ]);

        if (Parking::active()->where('vehicle_id', $request->vehicle_id)->exists()) {
            return response()->json([
                'errors' => ['general' => ['Can\'t start parking twice using same vehicle. Please stop currently active parking.']],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $parking = Parking::create($parkingData);
        $parking->load('vehicle', 'zone');

        return ParkingResource::make($parking);
    }

    public function show(Parking $parking)
    {
        $parking->load(['vehicle' => fn ($q) => $q->withTrashed()]);

        return ParkingResource::make($parking);
    }

    public function stop(Parking $parking)
    {
        $parking->update([
            'stop_time'   => now(),
            'total_price' => ParkingPriceService::calculatePrice($parking->zone_id, $parking->start_time),
        ]);

        return ParkingResource::make($parking);
    }
}
