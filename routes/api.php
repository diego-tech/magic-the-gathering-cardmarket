<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\SaleController;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User Routes
Route::post('/register', [AuthController::class, 'registerUser']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/retrieve_password', [AuthController::class, 'retrieve_password']);

// Card Routes
Route::post('/registerCard', [CardController::class, 'registerCard']);

// Collection Routes
Route::post('/registerCollection', [CollectionController::class, 'registerCollection']);

// Deck Routes
Route::post('/addCardsToCollections', [DeckController::class, 'addCardsToCollections']);

// Sale Routes
Route::post('/saleCard', [SaleController::class, 'saleCard']);
Route::get('/searchEngine', [SaleController::class, 'searchEngine']);


/**
 * MIDDLEWARE NAMES
 * checkifadminuser
 * checkifadminnotuser
 * auth:sanctum
 */