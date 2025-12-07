<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInUserRequest;
use App\Http\Requests\RegisterNewUserRequest;
use App\Models\Apartment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
   public function registerNewUser( RegisterNewUserRequest $request )
   {
     $data = $request->validated();
$data['dateOfBirth'] = Carbon::createFromFormat('d/m/Y', $data['dateOfBirth'])->format('Y-m-d');

        if($request->hasFile('personalPhoto') )
        {
           $path =  $request->file('personalPhoto')->store('personalPhotos','public');
          $data['personalPhoto'] = $path;
        }
        if($request->hasFile('IDPhoto') )
        {
           $path =  $request->file('IDPhoto')->store('IDPhotos','public');
          $data['IDPhoto'] = $path;
        }
     $user = User::create($data);
     return response()->json(['message'=>'User registered successfully'],201);
   }
     public function logInUser(LogInUserRequest $request)
        {

            if(!Auth::attempt($request->only('phone','password')))
              {
                return response()->json(['massege'=>'invalid login details'],401);
              }
                $user = User::where('phone',$request->phone)->firstOrFail();
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json(['message'=>'user logged in successfully','token'=>$token,'user'=>$user],200);
        }
                public function logOut(Request $request)
                {
                    $token = $request->user()->currentAccessToken();
                    if ($token instanceof PersonalAccessToken)
                    {
                        $token->delete();
                    }
                    return response()->json(['message'=>'user logged out successfully'],200);
                }
                public function allUserToAdmin(Request $request)
                {
                   $user = Auth::user();
                   if($user->role!='Admin')
                   {
                    return response()->json(['message'=>'unauthorized'],403);
                   }
                       $users = User::select(

            'firstName',
            'lastName',
            'phone',
            'role',
            'status',
            'dateOfBirth',
            'personalPhoto',
            'IDPhoto',
        )->get();

        return response()->json(['users' => $users], 200);
                }
                public function deleteUserByAdmin(Request $request)
                {
                    $user = Auth::user();
                    if($user->role!='Admin')
                    {
                        return response()->json(['message'=>'unauthorized'],403);
                    }
                    $user_delete = User::find($request->id);
                    if(!$user_delete)
                    {
                        return response()->json(['message'=>'user not found'],404);
                    }
                    $user_delete->delete();
                    return response()->json(['message'=>'user deleted successfully'],200);
                }
                public function approveUserByAdmin(Request $request)
                {
                    $user = Auth::user();
                    if($user->role!='Admin')
                    {
                        return response()->json(['message'=>'unauthorized'],403);
                    }
                    if($request->number !=1)
                    {
                        return response()->json(['message'=>'Your registeration request has been rejected'],200);
                    }
                    $user_check = User::find($request->id);
                    if(!$user_check)
                    {
                        return response()->json(['message'=>'user not found'],404);
                    }
                    $user_check->status = 'approved';
                    $user_check->save();
                    return response()->json(['message'=>'user approved successfully'],200);
                }
            public function pendingApartments(Request $request)
            {
        $user = Auth::user();
                    if($user->role!='Admin')
                    {
                        return response()->json(['message'=>'unauthorized'],403);
                    }
                      $apartments = Apartment::where('status','pending')
                      ->with('user')
                      ->get();
return response()->json(['apartments'=>$apartments],200);
 }
public function approvedRequest(Request $request)
{
     $user = Auth::user();
                    if($user->role!='Admin')
                    {
                        return response()->json(['message'=>'unauthorized'],403);
                    }
$apartment = Apartment::find($request->id);

    if (!$apartment) {
        return response()->json(['message' => 'Apartment not found'], 404);
    }

    if ($apartment->status !== 'pending') {
        return response()->json(['message' => 'Apartment already reviewed'], 400);
    }

    $apartment->status = 'approved';
    $apartment->save();

    return response()->json(['message' => 'Apartment approved successfully'], 200);
}
public function rejectApartment(Request $request)
{

   $user = Auth::user();
                    if($user->role!='Admin')
                    {
                        return response()->json(['message'=>'unauthorized'],403);
                    }   $user = Auth::user();
$reason = $request->validate([
    'reject_reason'=>'required|string'
]);
  $apartment = Apartment::find($request->id);

    if (!$apartment) {
        return response()->json(['message' => 'Apartment not found'], 404);
    }

    // 3) ما بصير نرفض شقة مو Pending
    if ($apartment->status !== 'pending') {
        return response()->json(['message' => 'Apartment already reviewed'], 400);
    }

$apartment->status = 'reject';
    $apartment->reject_reason = $reason;
    $apartment->save();

    return response()->json(['message' => 'Apartment rejected successfully','reason reject'=>$reason], 200);
}


}

