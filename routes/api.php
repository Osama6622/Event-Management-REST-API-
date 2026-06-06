<?php

use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('events', EventController::class);

// scoped is a method that allows you to scope the route to the event
// so that the attendee is always associated with the event
Route::apiResource('events.attendees', AttendeeController::class)
    // except(['update']) is a method that allows you to exclude the update method from the scoped route
    ->scoped()->except(['update']);
