<?php

use App\Http\Controllers\Auth\API\AuthController;
use App\Http\Controllers\Backend\API\BranchController;
use App\Http\Controllers\Backend\API\DashboardController;
use App\Http\Controllers\Backend\API\NotificationsController;
use App\Http\Controllers\Backend\API\SettingController;
use App\Http\Controllers\Backend\API\UserApiController;
use App\Http\Controllers\Backend\API\AddressController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EBookController;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Pet\Http\Controllers\Backend\API\PetController;
use Modules\Pet\Http\Controllers\Backend\BreedController;

Route::get('branch-list', [BranchController::class, 'branchList']);
Route::get('user-detail', [AuthController::class, 'userDetails']);
Route::get('/user-list', [UserApiController::class, 'user_list'])->name('user_list');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('social-login', 'socialLogin');
    Route::post('forgot-password', 'forgotPassword');
    Route::get('logout', 'logout');
});

Route::get('dashboard-detail', [DashboardController::class, 'dashboardDetail']);


Route::get('pet-center-configuration', [BranchController::class, 'branchConfig']);
Route::get('pet-center-detail', [BranchController::class, 'branchDetails']);
Route::get('branch-service', [BranchController::class, 'branchService']);
Route::get('branch-review', [BranchController::class, 'branchReviews']);
Route::get('branch-employee', [BranchController::class, 'branchEmployee']);
Route::get('branch-gallery', [BranchController::class, 'branchGallery']);


Route::group(['middleware' => 'auth:sanctum'], function () {
Route::get('employee-dashboard', [DashboardController::class, 'employeeDashboard']);

    Route::post('branch/assign/{id}', [BranchController::class, 'assign_update']);
    Route::apiResource('branch', BranchController::class);
    Route::apiResource('user', UserApiController::class);
    Route::apiResource('setting', SettingController::class);
    Route::apiResource('notification', NotificationsController::class);
    Route::get('notification-list', [NotificationsController::class, 'notificationList']);
    Route::get('notification-remove', [NotificationsController::class, 'notificationRemove']);
    Route::get('notification-deleteall', [NotificationsController::class, 'deleteAll']);
    Route::get('gallery-list', [DashboardController::class, 'globalGallery']);
    Route::get('search-list', [DashboardController::class, 'searchList']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);

    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('delete-account', [AuthController::class, 'deleteAccount']);

    Route::post('add-address', [AddressController::class, 'store']);
    Route::get('address-list', [AddressController::class, 'AddressList']);
    Route::get('remove-address', [AddressController::class, 'RemoveAddress']);
    Route::post('edit-address', [AddressController::class, 'EditAddress']);

    Route::post('verify-slot', [BranchController::class, 'verifySlot']);

    /**
     * Obtener Todos los E-Books
     * Método HTTP: GET
     * Ruta: /e-books
     * Descripción: Recupera todos los e-books disponibles.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "E-Books recuperados con éxito",
     *     "data": [ Array de e-books ]
     * }
     */
    Route::get('/e-books', [EBookController::class, 'get']);
    /**
     * Obtener un E-Book por ID
     * Método HTTP: GET
     * Ruta: /e-books/{id}
     * Descripción: Recupera un e-book específico por su ID.
     * Parámetros:
     * - id: ID del e-book que se desea obtener.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "E-Book recuperado con éxito",
     *     "data": { Datos del e-book }
     * }
     * Respuesta de Error:
     * {
     *     "success": false,
     *     "message": "E-Book no encontrado"
     * }
     */
    Route::get('/e-books/{id}', [EBookController::class, 'getById']);

    /**
     * Obtener Todos los Cursos
     * Método HTTP: GET
     * Ruta: /courses
     * Descripción: Recupera todos los cursos disponibles.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Cursos recuperados con éxito",
     *     "data": [ Array de cursos ]
     * }
     */
    Route::get('/courses', [CourseController::class, 'get']);
    /**
     * Obtener un Curso por ID
     * Método HTTP: GET
     * Ruta: /courses/{id}
     * Descripción: Recupera un curso específico por su ID.
     * Parámetros:
     * - id: ID del curso que se desea obtener.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Curso recuperado con éxito",
     *     "data": { Datos del curso }
     * }
     * Respuesta de Error:
     * {
     *     "success": false,
     *     "message": "Curso no encontrado"
     * }
     */
    Route::get('/courses/{id}', [CourseController::class, 'getById']);

    Route::get('/breeds', [BreedController::class, 'get']);

    Route::get('/pets/{id}/ages', [PetController::class, 'getPetAndOwnerAge']);

    Route::get('/pets/breed-age-info', [PetController::class, 'getAllPetsWithBreedInfo']);

    /**
     * Obtener Todos los Usuarios con Información del Perfil
     * Método HTTP: GET
     * Ruta: /users/profiles
     * Descripción: Recupera todos los usuarios disponibles con su nombre y la información del perfil.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Users with profiles retrieved successfully",
     *     "data": [
     *         {
     *             "name": "User Name",
     *             "profile": {
     *                 "about_self": "About User",
     *                 "expert": "Expertise",
     *                 "facebook_link": "http://facebook.com/user",
     *                 "instagram_link": "http://instagram.com/user",
     *                 "twitter_link": "http://twitter.com/user",
     *                 "dribbble_link": "http://dribbble.com/user"
     *             }
     *         },
     *         ...
     *     ]
     * }
     */
    Route::get('/users/profiles', [UserController::class, 'getAllUsersWithProfiles']);
});
Route::get('app-configuration', [SettingController::class, 'appConfiguraton']);

