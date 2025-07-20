<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/
## route::ressource me permet de creer les routes pour les crud (create, read, update, delete)


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes d'authentification
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name("login");
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name("logout");

// Routes de ressources protégées necessite la connection 
Route::middleware('auth:sanctum')->group(function () {
    // Routes pour les élèves
    Route::get('/eleves', [\App\Http\Controllers\EleveController::class, 'index']);
    Route::post('/eleves', [\App\Http\Controllers\EleveController::class, 'store']);
    Route::get('/eleves/{id}', [\App\Http\Controllers\EleveController::class, 'show']);
    Route::put('/eleves', [\App\Http\Controllers\EleveController::class, 'update']);
    Route::delete('/eleves/{id}', [\App\Http\Controllers\EleveController::class, 'destroy']);

    // Routes pour les classes
    Route::get('/classes', [\App\Http\Controllers\ClasseController::class, 'index']);
    Route::post('/classes', [\App\Http\Controllers\ClasseController::class, 'store']);
    Route::get('/classes/{id}', [\App\Http\Controllers\ClasseController::class, 'show']);
    Route::put('/classes', [\App\Http\Controllers\ClasseController::class, 'update']);
    Route::delete('/classes/{id}', [\App\Http\Controllers\ClasseController::class, 'destroy']);

    // Routes pour les enseignants
    Route::get('/enseignants', [\App\Http\Controllers\EnseignantController::class, 'index']);
    Route::post('/enseignants', [\App\Http\Controllers\EnseignantController::class, 'store']);
    Route::get('/enseignants/{id}', [\App\Http\Controllers\EnseignantController::class, 'show']);
    Route::put('/enseignants', [\App\Http\Controllers\EnseignantController::class, 'update']);
    Route::delete('/enseignants/{id}', [\App\Http\Controllers\EnseignantController::class, 'destroy']);

    // Routes pour les matières
    Route::get('/matieres', [\App\Http\Controllers\MatiereController::class, 'index']);
    Route::post('/matieres', [\App\Http\Controllers\MatiereController::class, 'store']);
    Route::get('/matieres/{id}', [\App\Http\Controllers\MatiereController::class, 'show']);
    Route::put('/matieres', [\App\Http\Controllers\MatiereController::class, 'update']);
    Route::delete('/matieres/{id}', [\App\Http\Controllers\MatiereController::class, 'destroy']);

    // Routes pour les notes
    Route::get('/notes', [\App\Http\Controllers\NoteController::class, 'index']);
    Route::post('/notes', [\App\Http\Controllers\NoteController::class, 'store']);
    Route::get('/notes/{id}', [\App\Http\Controllers\NoteController::class, 'show']);
    Route::put('/notes', [\App\Http\Controllers\NoteController::class, 'update']);
    Route::delete('/notes/{id}', [\App\Http\Controllers\NoteController::class, 'destroy']);

    // Routes pour les bulletins
    Route::get('/bulletins', [\App\Http\Controllers\BulletinController::class, 'index']);
    Route::post('/bulletins', [\App\Http\Controllers\BulletinController::class, 'store']);
    Route::get('/bulletins/{id}', [\App\Http\Controllers\BulletinController::class, 'show']);
    Route::put('/bulletins', [\App\Http\Controllers\BulletinController::class, 'update']);
    Route::delete('/bulletins/{id}', [\App\Http\Controllers\BulletinController::class, 'destroy']);
    
    // Routes spéciales pour les bulletins
    Route::get('/mon-bulletin', [\App\Http\Controllers\BulletinController::class, 'monBulletin']);
    Route::post('/bulletins-classe', [\App\Http\Controllers\BulletinController::class, 'bulletinsClasse']);
    Route::get('/bulletins-statistiques', [\App\Http\Controllers\BulletinController::class, 'statistiques']);
    
    // Nouvelles routes pour les fonctionnalités bonus
    Route::get('/bulletins/{eleve_id}/pdf/{periode?}', [\App\Http\Controllers\BulletinController::class, 'telechargerBulletinPDF']);
    Route::post('/bulletins-groupe', [\App\Http\Controllers\BulletinController::class, 'telechargerBulletinsGroupe']);
    Route::get('/bulletins/{eleve_id}/historique/{annee?}', [\App\Http\Controllers\BulletinController::class, 'historiqueBulletins']);
    Route::post('/bulletins/{eleve_id}/notifier/{periode}', [\App\Http\Controllers\BulletinController::class, 'notifierBulletinDisponible']);
    

}); 