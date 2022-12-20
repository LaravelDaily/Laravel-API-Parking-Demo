<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Auth
 */
class ProfileController extends Controller
{
    public function show()
    {
        return response()->json([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users')->ignore(auth()->user())]
        ]);

        auth()->user()->update($validatedData);

        return response()->json($validatedData);
    }
}
