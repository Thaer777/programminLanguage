<?php

use App\Http\Controllers\AmenitieController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileOfUserController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/user/register',[UserController::class,'registerNewUser']);//register a new user
Route::post('/user/login',[UserController::class,'logInUser']);//log in user
Route::middleware('auth:sanctum')->group(function () {
Route::post('/user/Profile',[ProfileOfUserController::class,'createProfileOfUser']);//create profile of user
Route::post('/user/logout',[UserController::class,'logOut']);//log out user
Route::get('/amenities',[AmenitieController::class,'showAllAmenities']);//show all amenities
Route::post('/apartment/create',[ApartmentController::class,'createNewApartment']);//create new apartment
Route::get('/apartments/allApartment',[ApartmentController::class,'showAllApartments']);//show all apartments
Route::post('/apartments/searhByFilters',[ApartmentController::class,'searchByFilters']);//search apartments by filters
Route::post('/apartment/booking',[BookingController::class,'storeBookingApartment']);//booking apartment
Route::post('/apartment/modifyBooking',[BookingController::class,'modifyBooking']);//modify booking
Route::post('/apartment/cancelBooking',[BookingController::class,'cancelBooking']);//cancel booking
Route::post('/apartment/renterBookings',[BookingController::class,'renterBookings']);//show renter bookings
Route::post('/apartment/addRating',[RatingController::class,'addRating']);//add rating to booking
Route::post('/apartment/ratings',[RatingController::class,'showAllRatingsToApartment']);//show all ratings to apartment
Route::post('/user/allUsers',[UserController::class,'allUserToAdmin']);//show all users to admin
Route::post('/user/delete',[UserController::class,'deleteUserByAdmin']);//delete user by admin
Route::post('/user/approveUser',[UserController::class,'approveUserByAdmin']);//approve user by admin
Route::post('/user/allPending',[UserController::class,'pendingApartments']);//show all pending apartments to admin
Route::post('user/approvedApartment',[UserController::class,'approvedRequest']);//approved apartment by admin
Route::post('user/rejectApartment',[UserController::class,'rejectApartment']);//reject apartment by admin



});
