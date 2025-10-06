<?php

use App\Http\Controllers\AuthentificationController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\ParticipantsController;
use App\Models\Participants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthentificationController::class, 'register']);
Route::post('/login', [AuthentificationController::class, 'login']);

//formation
Route::post('/formations', [FormationController::class, 'store'])->middleware('auth:sanctum');
Route::get('/formations', [FormationController::class, 'index'])->middleware('auth:sanctum');
Route::get('/formations/{formationId}', [FormationController::class, 'show'])->middleware('auth:sanctum');

//certificat
Route::post('/certificats/{formationId}', [CertificatController::class, 'genererCertificates'])->middleware('auth:sanctum');
Route::get('/certificats/{formationId}', [CertificatController::class, 'getCertificatsbyFormation'])->middleware('auth:sanctum');

