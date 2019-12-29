<?php

// use Illuminate\Http\Request;

use App\Http\Controllers\AppointmentController;

Route::post('/login', 'AuthController@login');

//public resources
Route::get('/specialties', 'SpecialtyController@index');
Route::get('/specialties/{specialty}/doctors', 'SpecialtyController@doctors');
Route::get('/schedule/hours', 'ScheduleController@hours');


Route::middleware('auth:api')->group(function () {
    Route::get('/user', 'UserController@show');
    Route::post('/logout','AuthController@logout');

    //appoitments
    Route::post('/appointments', 'AppointmentController@store');
    Route::get('/appointments', 'AppointmentController@index');
});
