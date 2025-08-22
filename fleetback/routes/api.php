<?php

use App\Http\Controllers\API\params\FamilleVehiculeController;
use App\Http\Controllers\API\params\FournisseurController;
use App\Http\Controllers\API\params\TypeCarburantController;
use App\Http\Controllers\API\params\TypeCompteurController;
use App\Http\Controllers\API\parc\GammeController;
use App\Http\Controllers\API\parc\MarqueController;
use App\Http\Controllers\API\parc\ModeleController;
use App\Http\Controllers\API\parc\VehiculeController;
use App\Http\Controllers\API\user\PermissionController;
use App\Http\Controllers\API\user\RoleController;
use App\Http\Controllers\API\user\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Notif\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $user = $request->user();

    // Load permissions (and optionally roles)
    $user->load('permissions', 'roles');

    return response()->json([
        'user' => $user,
        'permissions' => $user->getAllPermissions()->pluck('name'), // Optional: as a flat list
        'roles' => $user->getRoleNames()->pluck('name'), // Optional: if you want the role names too
    ]);
});
Route::middleware(['auth:sanctum'])->post('/change-password', [NewPasswordController::class, 'update']);

Route::middleware(['auth:sanctum'])->post('/logout-others', [AuthenticatedSessionController::class, 'logoutFromOtherDevices']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/count', [NotificationController::class, 'countUnread']);
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/delete-old', [NotificationController::class, 'deleteOld']);
});

//************************************************* CRUD

require __DIR__ . '/auth.php';

/**********************************     ADMIN      ************************************/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('users', UserController::class)->middleware(PermissionMiddleware::using('users access'));
    Route::get('users-names', [UserController::class, 'listNames']);
    Route::post('users/restore/{user}', [UserController::class, 'restore']);
    Route::delete('users/force/{user}', [UserController::class, 'forceDelete']);

    /**********************************     ROLE      ************************************/
    Route::apiResource('roles', RoleController::class)->middleware(PermissionMiddleware::using('roles access'));
    Route::get('roles-names', [RoleController::class, 'listNames']);

    /**********************************     PERMISSION      ************************************/
    Route::apiResource('permissions', PermissionController::class);
    Route::get('permissions-names', [PermissionController::class, 'listNames']);
});

/**********************************     PARC      ************************************/
Route::middleware(['auth:sanctum'])->group(function () {

    /**********************************     MARQUE      ************************************/
    Route::post('/marques/{marque}', [MarqueController::class, 'update'])->middleware(PermissionMiddleware::using('params access'));
    Route::apiResource('marques', MarqueController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('marques-names', [MarqueController::class, 'listNames']);

    /**********************************     MODELE      ************************************/
    Route::apiResource('modeles', ModeleController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('modeles-names', [ModeleController::class, 'listNames']);

    /**********************************     GAMME      ************************************/
    Route::apiResource('gammes', GammeController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('gammes-names', [GammeController::class, 'listNames']);

    /**********************************     VEHICULE      ************************************/
    Route::apiResource('vehicules', VehiculeController::class)->middleware(PermissionMiddleware::using('vehicules access'));
    Route::get('vehicules-names', [VehiculeController::class, 'listImmatriculations']);
});

/**********************************     PARAM GENEREAUX      ************************************/
Route::middleware(['auth:sanctum'])->group(function () {

    /**********************************     COMPTEUR      ************************************/
    Route::apiResource('type-compteurs', TypeCompteurController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('type-compteurs-names', [TypeCompteurController::class, 'listNames']);

    /**********************************     CARBURANT      ************************************/
    Route::apiResource('type-carburants', TypeCarburantController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('type-carburants-names', [TypeCarburantController::class, 'listNames']);

    /**********************************     FAMILLE VEH      ************************************/
    Route::apiResource('famille-vehicules', FamilleVehiculeController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('famille-vehicules-names', [FamilleVehiculeController::class, 'listNames']);

    /**********************************     FOURNISSEUR    ************************************/
    Route::apiResource('fournisseurs', FournisseurController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('fournisseurs-names', [FournisseurController::class, 'listNames']);
});
