<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInUserRequest;
use App\Http\Requests\RegisterNewUserRequest;
use App\Models\Apartment;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    use ApiResponse;

    /* =======================
       REGISTER
    ======================= */
    public function registerNewUser(RegisterNewUserRequest $request)
    {
        $data = $request->validated();
        $data['dateOfBirth'] = Carbon::createFromFormat('d/m/Y', $data['dateOfBirth'])->format('Y-m-d');

        if ($request->hasFile('personalPhoto')) {
            $data['personalPhoto'] = $request->file('personalPhoto')->store('personalPhotos', 'public');
        }

        if ($request->hasFile('IDPhoto')) {
            $data['IDPhoto'] = $request->file('IDPhoto')->store('IDPhotos', 'public');
        }

        User::create($data);

        return $this->successResponse(
            null,
            'Registration request sent. Waiting for admin approval.',
            201
        );
    }

    /* =======================
       LOGIN
    ======================= */
    public function logInUser(LogInUserRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        if ($user->status === 'pending') {
            return $this->errorResponse('Your request is still pending', 403);
        }

        if ($user->status === 'rejected') {
            return $this->errorResponse('Your request has been rejected', 403);
        }

        if (!Auth::attempt($request->only('phone', 'password'))) {
            return $this->errorResponse('Invalid login details', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'name_user'=> $user->firstName.' '.$user->lastName,
            'personalPhoto'=> asset('storage/'.$user->personalPhoto)
        ], 'User logged in successfully');
    }

    /* =======================
       LOGOUT
    ======================= */
    public function logOut(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return $this->successResponse(null, 'User logged out successfully');
    }

    /* =======================
       ADMIN - USERS
    ======================= */
    public function allUserToAdmin()
    {
        if (Auth::user()->role !== 'Admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        $users = User::where('role', '!=', 'Admin')
            ->select('firstName','lastName','phone','role','status','dateOfBirth','personalPhoto','IDPhoto')
            ->get()
            ->map(function ($user) {
                return [
                    'firstName'     => $user->firstName,
                    'lastName'      => $user->lastName,
                    'phone'         => $user->phone,
                    'role'          => $user->role,
                    'status'        => $user->status,
                    'dateOfBirth'   => $user->dateOfBirth,
                    'personalPhoto' => asset('storage/' . $user->personalPhoto),
                    'IDPhoto'       => asset('storage/' . $user->IDPhoto),
                ];
            });

        return $this->successResponse($users, 'Users fetched successfully');
    }

    public function deleteUserByAdmin(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        $user = User::find($request->id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }

    public function approveUserByAdmin(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        $user = User::find($request->id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $user->status = 'approved';
        $user->save();

        return $this->successResponse(null, 'User approved successfully');
    }

    /* =======================
       ADMIN - APARTMENTS
    ======================= */
    public function pendingApartments()
    {
        if (Auth::user()->role !== 'Admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        $apartments = Apartment::where('status', 'pending')->with('user')->get();

        return $this->successResponse($apartments, 'Pending apartments fetched');
    }

    public function approvedRequest(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        $apartment = Apartment::find($request->id);

        if (!$apartment) {
            return $this->errorResponse('Apartment not found', 404);
        }

        if ($apartment->status !== 'pending') {
            return $this->errorResponse('Apartment already reviewed', 400);
        }

        $apartment->status = 'approved';
        $apartment->save();

        return $this->successResponse(null, 'Apartment approved successfully');
    }

    public function rejectApartment(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            return $this->errorResponse('Unauthorized', 403);
        }

        $data = $request->validate([
            'reject_reason' => 'required|string'
        ]);

        $apartment = Apartment::find($request->id);

        if (!$apartment) {
            return $this->errorResponse('Apartment not found', 404);
        }

        if ($apartment->status !== 'pending') {
            return $this->errorResponse('Apartment already reviewed', 400);
        }

        $apartment->status = 'rejected';
        $apartment->reject_reason = $data['reject_reason'];
        $apartment->save();

        return $this->successResponse(null, 'Apartment rejected successfully');
    }

    /* =======================
       NOTIFICATIONS
    ======================= */
    public function getUserNotifications()
    {
        return $this->successResponse(
            Auth::user()->notifications,
            'Notifications fetched'
        );
    }

    public function getNotReadNotifications()
    {
        return $this->successResponse(
            Auth::user()->unreadNotifications,
            'Unread notifications fetched'
        );
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if (!$notification) {
            return $this->errorResponse('Notification not found', 404);
        }

        $notification->markAsRead();

        return $this->successResponse(null, 'Notification marked as read');
    }
}
