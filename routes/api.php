<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // Import the AdminController

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

// Define a route for fetching live-K1 data
Route::get('/live-k1', [AdminController::class, 'getLiveK1Data']);

// Example of other potential API routes that might be defined in this file
// Route::get('/live-k2', [AdminController::class, 'getLiveK2Data']);
// Route::post('/save-data', [AdminController::class, 'saveData']);
// Route::put('/update-record/{id}', [AdminController::class, 'updateRecord']);
// Route::delete('/delete-record/{id}', [AdminController::class, 'deleteRecord']);

// You can add more API routes as needed
