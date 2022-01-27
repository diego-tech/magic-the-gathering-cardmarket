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

// User Routes
Route::post('/register', [AuthController::class, 'registerUser']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/retrieve_password', [AuthController::class, 'retrieve_password']);

Route::middleware(['auth:sanctum', 'checkifadminuser'])->group(function () {
    // Card Routes
    Route::post('/registerCard', [CardController::class, 'registerCard']);

    // Collection Routes
    Route::post('/registerCollection', [CollectionController::class, 'registerCollection']);

    // Deck Routes
    Route::post('/addCardsToCollections', [DeckController::class, 'addCardsToCollections']);
});

Route::middleware(['auth:sanctum', 'checkifadminnotuser'])->group(function () {
    // Sale Routes
    Route::post('/saleCard', [SaleController::class, 'saleCard']);
    Route::get('/searchEngine', [SaleController::class, 'searchEngine']);
});

Route::get('/purchaseManagement', [SaleController::class, 'purchaseManagement']);