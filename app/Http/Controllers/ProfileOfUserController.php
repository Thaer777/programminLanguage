<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileToUserRequest;
use App\Models\ProfileOfUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\HttpCache\Store;

class ProfileOfUserController extends Controller
{
    public function createProfileOfUser(StoreProfileToUserRequest $request)
    {
        $id_user = Auth::id();
        $validated = $request->validated();
        $validated['user_id'] = $id_user;

        $profile = ProfileOfUser::create($validated);
        return response()->json([
            'message' => 'Profile created successfully',
            'profile' => $profile
        ], 201);
    }

}
