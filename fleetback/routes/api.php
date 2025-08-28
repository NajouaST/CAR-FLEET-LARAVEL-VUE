<?php

use App\Http\Controllers\API\params\FamilleVehiculeController;
use App\Http\Controllers\API\params\FournisseurController;
use App\Http\Controllers\API\params\GradeController;
use App\Http\Controllers\API\params\RegionController;
use App\Http\Controllers\API\params\SiteController;
use App\Http\Controllers\API\params\TypeCarburantController;
use App\Http\Controllers\API\params\TypeCompteurController;
use App\Http\Controllers\API\params\ZoneController;
use App\Http\Controllers\API\parc\CarteCarburantController;
use App\Http\Controllers\API\parc\DocumentVehiculeController;
use App\Http\Controllers\API\parc\parcParams\GammeController;
use App\Http\Controllers\API\parc\parcParams\MarqueController;
use App\Http\Controllers\API\parc\parcParams\ModeleController;
use App\Http\Controllers\API\parc\VehiculeController;
use App\Http\Controllers\API\RH\PersonnelController;
use App\Http\Controllers\API\RH\RHParams\CentreCoutController;
use App\Http\Controllers\API\RH\RHParams\DepartementController;
use App\Http\Controllers\API\RH\RHParams\DirectionController;
use App\Http\Controllers\API\RH\RHParams\DivisionController;
use App\Http\Controllers\API\RH\RHParams\FonctionController;
use App\Http\Controllers\API\RH\RHParams\SocieteController;
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

require __DIR__ . '/auth.php';

//************************************************* CRUD

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

    /**********************************     VEHICULE      ************************************/
    Route::apiResource('vehicules', VehiculeController::class)->middleware(PermissionMiddleware::using('vehicules access'));
    Route::get('vehicules-names', [VehiculeController::class, 'listImmatriculations']);

    /**********************************     DOC VEHICULE      ************************************/
    Route::apiResource('document-vehicules', DocumentVehiculeController::class)->middleware(PermissionMiddleware::using('vehicules access'));

    /**********************************     CARTE CARBURANT      ************************************/
    Route::apiResource('carte-carburants', CarteCarburantController::class)->middleware(PermissionMiddleware::using('carte-carburants access'));
    Route::get('carte-carburants-names', [CarteCarburantController::class, 'listNames']);

    /**********************************     PARAMS      ************************************/
    Route::apiResource('marques', MarqueController::class)->middleware(PermissionMiddleware::using('parcparams access'));
    Route::apiResource('modeles', ModeleController::class)->middleware(PermissionMiddleware::using('parcparams access'));
    Route::get('modeles-names', [ModeleController::class, 'listNames']);
    Route::apiResource('gammes', GammeController::class)->middleware(PermissionMiddleware::using('parcparams access'));
    Route::get('gammes-names', [GammeController::class, 'listNames']);
});

/**********************************     RH      ************************************/
Route::middleware(['auth:sanctum'])->group(function () {

    /**********************************     PERSONNEL      ************************************/
    Route::apiResource('personnels', PersonnelController::class)->middleware(PermissionMiddleware::using('personnels access'));
    Route::get('personnels-names', [PersonnelController::class, 'listNames']);

    /**********************************     PARAMS      ************************************/
    Route::apiResource('societes', SocieteController::class)->middleware(PermissionMiddleware::using('rhparams access'));
    Route::get('societes-names', [SocieteController::class, 'listNames']);
    Route::apiResource('directions', DirectionController::class)->middleware(PermissionMiddleware::using('rhparams access'));
    Route::get('directions-names', [DirectionController::class, 'listNames']);
    Route::apiResource('fonctions', FonctionController::class)->middleware(PermissionMiddleware::using('rhparams access'));
    Route::get('fonctions-names', [FonctionController::class, 'listNames']);
    Route::apiResource('departements', DepartementController::class)->middleware(PermissionMiddleware::using('rhparams access'));
    Route::get('departements-names', [DepartementController::class, 'listNames']);
    Route::apiResource('divisions', DivisionController::class)->middleware(PermissionMiddleware::using('rhparams access'));
    Route::get('divisions-names', [DivisionController::class, 'listNames']);
    Route::apiResource('centre-couts', CentreCoutController::class)->middleware(PermissionMiddleware::using('rhparams access'));
    Route::get('centre-couts-names', [CentreCoutController::class, 'listNames']);
});

/**********************************     PARAM GENEREAUX      ************************************/
Route::middleware(['auth:sanctum'])->group(function () {

    /**********************************     REGION      ************************************/
    Route::apiResource('regions', RegionController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('regions-names', [RegionController::class, 'listNames']);

    /**********************************     ZONE      ************************************/
    Route::apiResource('zones', ZoneController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('zones-names', [ZoneController::class, 'listNames']);

    /**********************************     SITE      ************************************/
    Route::apiResource('sites', SiteController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('sites-names', [SiteController::class, 'listNames']);

    /**********************************     GRADE      ************************************/
    Route::apiResource('grades', GradeController::class)->middleware(PermissionMiddleware::using('params access'));
    Route::get('grades-names', [GradeController::class, 'listNames']);

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
