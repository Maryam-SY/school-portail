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
Route::post('/register-eleve-parent', [\App\Http\Controllers\AuthController::class, 'registerEleveParent']);

// Routes de ressources protégées necessite la connection 
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('eleves', \App\Http\Controllers\EleveController::class);
    Route::apiResource('classes', \App\Http\Controllers\ClasseController::class);
    Route::apiResource('enseignants', \App\Http\Controllers\EnseignantController::class);
    Route::apiResource('matieres', \App\Http\Controllers\MatiereController::class);
    Route::apiResource('notes', \App\Http\Controllers\NoteController::class);

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
    Route::get('/bulletins/{eleve_id}/pdf/{periode?}', [\App\Http\Controllers\BulletinController::class, 'telechargerBulletinPDF']);
    Route::post('/bulletins-groupe', [\App\Http\Controllers\BulletinController::class, 'telechargerBulletinsGroupe']);
    Route::get('/bulletins/{eleve_id}/historique/{annee?}', [\App\Http\Controllers\BulletinController::class, 'historiqueBulletins']);
    Route::post('/bulletins/{eleve_id}/notifier/{periode}', [\App\Http\Controllers\BulletinController::class, 'notifierBulletinDisponible']);
    Route::get('/parent/bulletins', [\App\Http\Controllers\BulletinController::class, 'bulletinsParent']);

    Route::post('/notes', [\App\Http\Controllers\NoteController::class, 'store']);
    Route::get('/enseignant/notes', [\App\Http\Controllers\NoteController::class, 'mesNotes']);
    Route::get('/enseignant/classes', [\App\Http\Controllers\EnseignantController::class, 'mesClasses']);
    Route::get('/enseignant/matieres', [\App\Http\Controllers\EnseignantController::class, 'mesMatieres']);
    Route::get('/enseignant/classes/{classe_id}/eleves', [\App\Http\Controllers\EnseignantController::class, 'elevesDeMaClasse']);
    Route::get('/enseignants/{id}/classes', [\App\Http\Controllers\EnseignantController::class, 'classesById']);
    Route::get('/enseignants/{id}/matieres', [\App\Http\Controllers\EnseignantController::class, 'matieresById']);
    Route::get('/enseignants/{id}/classes/{classe_id}/eleves', [\App\Http\Controllers\EnseignantController::class, 'elevesByClasse']);
    Route::get('/enseignants/{id}/matieres/{classe_id}', [\App\Http\Controllers\EnseignantController::class, 'matieresByClasse']);
    Route::get('/parent/enfants', [\App\Http\Controllers\ParentController::class, 'enfants']);
    Route::get('/parent/bulletins/{enfant_id}', [\App\Http\Controllers\ParentController::class, 'bulletins']);
    Route::get('/eleve/infos', [\App\Http\Controllers\EleveController::class, 'infos']);
    Route::get('/eleve/bulletins', [\App\Http\Controllers\EleveController::class, 'mesBulletins']);
}); 