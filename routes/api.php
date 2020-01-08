<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes to get pokemon data.
Route::get('/pokemon', 'PokemonController@index');  // Get paginated pokemon list.
Route::get('/pokemon/{id}', 'PokemonController@show');  // Get specific pokemon data.

// Routes to register and login users (trainers).
Route::post('register', 'PassportController@register');  // Takes new trainer information(name, email, password) and creates a new user.
Route::post('login', 'PassportController@login');  // Takes user information and returns an access token for that user.
 
// Authorization protected routes.
Route::middleware('auth:api')->group(function () {
    Route::get('user', 'PassportController@details'); // Get user data.
 
    Route::resource('captures', 'CaptureController'); // Get all, get one, create new, or delete user pokemon captures.

    Route::get('evaluation', 'CaptureController@evaluation'); // Get pokedex evaluation information.
});