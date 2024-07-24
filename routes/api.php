<?php

use App\Http\Controllers\Api\ClaseController;
use App\Http\Controllers\Api\ComandoController;
use App\Http\Controllers\Api\ComandoEquivalenteController;
use App\Http\Controllers\Api\CursoPlataformaController;
use App\Http\Controllers\Api\EjercicioController;
use App\Http\Controllers\Api\EventController;
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

    /**
     * Obtener Todas las Razas
     * Método HTTP: GET
     * Ruta: /breeds
     * Descripción: Recupera todas las razas disponibles.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Breeds retrieved successfully",
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Breed Name",
     *             "description": "Breed Description"
     *         },
     *         ...
     *     ]
     * }
     */
    Route::get('/breeds', [BreedController::class, 'get']);

    // Rutas para Mascota con Información sobre Edad

    /**
     * Obtener la Edad de una Mascota y su Dueño por ID de Mascota
     * Método HTTP: GET
     * Ruta: /pets/{id}/ages
     * Descripción: Recupera la edad de una mascota específica y la edad de su dueño.
     * Parámetros:
     * - id: ID de la mascota que se desea obtener.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Pet and owner ages retrieved successfully",
     *     "data": {
     *         "pet_age": 3,
     *         "owner_age": 35
     *     }
     * }
     * Respuesta de Error:
     * {
     *     "success": false,
     *     "message": "Pet not found"
     * }
     */
    Route::get('/pets/{id}/ages', [PetController::class, 'getPetAndOwnerAge']);


    // Rutas para Mascotas con Información sobre Razas y Edad

    /**
     * Obtener Información de Todas las Mascotas con Edad y Raza
     * Método HTTP: GET
     * Ruta: /pets/breed-age-info
     * Descripción: Recupera todas las mascotas disponibles con su nombre, edad, género, peso, unidad de peso, altura, unidad de altura, raza y la información de la raza.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Pets retrieved successfully",
     *     "data": [
     *         {
     *             "name": "Pet Name",
     *             "age": 3,
     *             "gender": "Male",
     *             "weight": 10.5,
     *             "weight_unit": "kg",
     *             "height": 30,
     *             "height_unit": "cm",
     *             "breed": {
     *                 "name": "Breed Name",
     *                 "description": "Breed Description"
     *             }
     *         },
     *         ...
     *     ]
     * }
     */
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

    /**
     * Rutas para la gestión de cursos de la plataforma
     */
    Route::prefix('course-platform')->group(function () {
        /**
         * Obtener Todos los Cursos de la Plataforma
         * Método HTTP: GET
         * Ruta: /api/course-platform
         * Descripción: Recupera todos los cursos de la plataforma disponibles.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Cursos de la plataforma recuperados exitosamente",
         *     "data": [
         *         {
         *             "id": 1,
         *             "name": "Curso de la Plataforma 1",
         *             "description": "Descripción 1",
         *             "url": "http://example.com",
         *             "price": 100.00,
         *             "created_at": "2024-07-13T00:00:00.000000Z",
         *             "updated_at": "2024-07-13T00:00:00.000000Z"
         *         },
         *         ...
         *     ]
         * }
         */
        Route::get('/', [CursoPlataformaController::class, 'index'])->name('course_platform.index');

        /**
         * Crear un Nuevo Curso de la Plataforma
         * Método HTTP: POST
         * Ruta: /api/course-platform
         * Descripción: Crea un nuevo curso de la plataforma con los datos proporcionados.
         * Datos de Solicitud:
         * {
         *     "name": "Curso de la Plataforma 1",
         *     "description": "Descripción 1",
         *     "url": "http://example.com",
         *     "price": 100.00
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Curso de la plataforma creado exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Curso de la Plataforma 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "price": 100.00,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::post('/', [CursoPlataformaController::class, 'store'])->name('course_platform.store');

        /**
         * Obtener un Curso de la Plataforma por ID
         * Método HTTP: GET
         * Ruta: /api/course-platform/{course_platform}
         * Descripción: Recupera la información de un curso de la plataforma específico por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Curso de la plataforma recuperado exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Curso de la Plataforma 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "price": 100.00,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::get('{course_platform}', [CursoPlataformaController::class, 'show'])->name('course_platform.show');
        /**
         * Actualizar un Curso de la Plataforma por ID
         * Método HTTP: PUT
         * Ruta: /api/course-platform/{course_platform}
         * Descripción: Actualiza los datos de un curso de la plataforma específico por ID.
         * Datos de Solicitud:
         * {
         *     "name": "Curso de la Plataforma 1",
         *     "description": "Descripción 1",
         *     "url": "http://example.com",
         *     "price": 100.00
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Curso de la plataforma actualizado exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Curso de la Plataforma 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "price": 100.00,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         * Respuesta de Error (Validación):
         * {
         *     "success": false,
         *     "message": "La URL del video no es válida o el video no está disponible.",
         * }
         */
        Route::put('{course_platform}', [CursoPlataformaController::class, 'update'])->name('course_platform.update');

        /**
         * Eliminar un Curso de la Plataforma por ID
         * Método HTTP: DELETE
         * Ruta: /api/course-platform/{course_platform}
         * Descripción: Elimina un curso de la plataforma específico por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Curso de la plataforma eliminado exitosamente"
         * }
         */
        Route::delete('{course_platform}', [CursoPlataformaController::class, 'destroy'])->name('course_platform.destroy');
    });

    /**
     * Rutas para la gestión de clases dentro de un curso de la plataforma
     */
    Route::prefix('course-platform/{course_platform}/clases')->group(function () {
        /**
         * Obtener Todas las Clases de un Curso de la Plataforma
         * Método HTTP: GET
         * Ruta: /api/course-platform/{course_platform}/clases
         * Descripción: Recupera todas las clases de un curso de la plataforma específico.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Clases recuperadas exitosamente",
         *     "data": [
         *         {
         *             "id": 1,
         *             "name": "Clase 1",
         *             "description": "Descripción 1",
         *             "url": "http://example.com",
         *             "price": 50.00,
         *             "course_id": 1,
         *             "created_at": "2024-07-13T00:00:00.000000Z",
         *             "updated_at": "2024-07-13T00:00:00.000000Z"
         *         },
         *         ...
         *     ]
         * }
         */
        Route::get('/', [ClaseController::class, 'index']);

        /**
         * Crear una Nueva Clase para un Curso de la Plataforma
         * Método HTTP: POST
         * Ruta: /api/course-platform/{course_platform}/clases
         * Descripción: Crea una nueva clase en un curso de la plataforma específico.
         * Datos de Solicitud:
         * {
         *     "name": "Clase 1",
         *     "description": "Descripción 1",
         *     "url": "http://example.com",
         *     "price": 50.00
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Clase creada exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Clase 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "price": 50.00,
         *         "course_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::post('/', [ClaseController::class, 'store']);

        /**
         * Obtener una Clase por ID
         * Método HTTP: GET
         * Ruta: /api/course-platform/{course_platform}/clases/{clase}
         * Descripción: Recupera la información de una clase específica por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Clase recuperada exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Clase 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "price": 50.00,
         *         "course_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::get('{clase}', [ClaseController::class, 'show']);

        /**
         * Actualizar una Clase por ID
         * Método HTTP: PUT
         * Ruta: /api/course-platform/{course_platform}/clases/{clase}
         * Descripción: Actualiza los datos de una clase específica por ID.
         * Datos de Solicitud:
         * {
         *     "name": "Clase 1",
         *     "description": "Descripción 1",
         *     "url": "http://example.com",
         *     "price": 50.00
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Clase actualizada exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Clase 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "price": 50.00,
         *         "course_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::put('{clase}', [ClaseController::class, 'update']);

        /**
         * Eliminar una Clase por ID
         * Método HTTP: DELETE
         * Ruta: /api/course-platform/{course_platform}/clases/{clase}
         * Descripción: Elimina una clase específica por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Clase eliminada exitosamente"
         * }
         */
        Route::delete('{clase}', [ClaseController::class, 'destroy']);
    });

    /**
     * Rutas para la gestión de ejercicios dentro de una clase
     */
    Route::prefix('clases/{clase}/ejercicios')->group(function () {
        /**
         * Obtener Todos los Ejercicios de una Clase
         * Método HTTP: GET
         * Ruta: /api/clases/{clase}/ejercicios
         * Descripción: Recupera todos los ejercicios de una clase específica.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Ejercicios recuperados exitosamente",
         *     "data": [
         *         {
         *             "id": 1,
         *             "name": "Ejercicio 1",
         *             "description": "Descripción 1",
         *             "url": "http://example.com",
         *             "clase_id": 1,
         *             "created_at": "2024-07-13T00:00:00.000000Z",
         *             "updated_at": "2024-07-13T00:00:00.000000Z"
         *         },
         *         ...
         *     ]
         * }
         */
        Route::get('/', [EjercicioController::class, 'index'])->name('ejercicios.index');

        /**
         * Crear un Nuevo Ejercicio para una Clase
         * Método HTTP: POST
         * Ruta: /api/clases/{clase}/ejercicios
         * Descripción: Crea un nuevo ejercicio en una clase específica.
         * Datos de Solicitud:
         * {
         *     "name": "Ejercicio 1",
         *     "description": "Descripción 1",
         *     "url": "http://example.com"
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Ejercicio creado exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Ejercicio 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "clase_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::post('/', [EjercicioController::class, 'store'])->name('ejercicios.store');

        /**
         * Obtener un Ejercicio por ID
         * Método HTTP: GET
         * Ruta: /api/clases/{clase}/ejercicios/{ejercicio}
         * Descripción: Recupera la información de un ejercicio específico por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Ejercicio recuperado exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Ejercicio 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "clase_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::get('{ejercicio}', [EjercicioController::class, 'show'])->name('ejercicios.show');

        /**
         * Actualizar un Ejercicio por ID
         * Método HTTP: PUT
         * Ruta: /api/clases/{clase}/ejercicios/{ejercicio}
         * Descripción: Actualiza los datos de un ejercicio específico por ID.
         * Datos de Solicitud:
         * {
         *     "name": "Ejercicio 1",
         *     "description": "Descripción 1",
         *     "url": "http://example.com"
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Ejercicio actualizado exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Ejercicio 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "clase_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::put('{ejercicio}', [EjercicioController::class, 'update'])->name('ejercicios.update');

        /**
         * Eliminar un Ejercicio por ID
         * Método HTTP: DELETE
         * Ruta: /api/clases/{clase}/ejercicios/{ejercicio}
         * Descripción: Elimina un ejercicio específico por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Ejercicio eliminado exitosamente"
         * }
         */
        Route::delete('{ejercicio}', [EjercicioController::class, 'destroy'])->name('ejercicios.destroy');
    });

    /**
     * Obtener todos los eventos.
     * Método HTTP: GET
     * Ruta: /api/events
     * Descripción: Recupera todos los eventos disponibles.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Eventos recuperados exitosamente",
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Evento 1",
     *             "date": "2024-07-13T00:00:00.000000Z",
     *             "slug": "evento-1",
     *             "user_id": 1,
     *             "description": "Descripción del evento 1",
     *             "location": "Ubicación del evento 1",
     *             "tipo": "salud",
     *             "status": true,
     *             "created_by": null,
     *             "updated_by": null,
     *             "deleted_by": null,
     *             "created_at": "2024-07-13T00:00:00.000000Z",
     *             "updated_at": "2024-07-13T00:00:00.000000Z"
     *         },
     *         ...
     *     ]
     * }
     */
    Route::get('events', [EventController::class, 'index']);

    /**
     * Obtener un evento por ID.
     * Método HTTP: GET
     * Ruta: /api/events/{id}
     * Descripción: Recupera un evento específico por ID.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Evento recuperado exitosamente",
     *     "data": {
     *         "id": 1,
     *         "name": "Evento 1",
     *         "date": "2024-07-13T00:00:00.000000Z",
     *         "slug": "evento-1",
     *         "user_id": 1,
     *         "description": "Descripción del evento 1",
     *         "location": "Ubicación del evento 1",
     *         "tipo": "salud",
     *         "status": true,
     *         "created_by": null,
     *         "updated_by": null,
     *         "deleted_by": null,
     *         "created_at": "2024-07-13T00:00:00.000000Z",
     *         "updated_at": "2024-07-13T00:00:00.000000Z"
     *     }
     * }
     */
    Route::get('events/{id}', [EventController::class, 'show'])->name('events.show');

     /**
     * Crear un nuevo evento.
     * Método HTTP: POST
     * Ruta: /api/events
     * Descripción: Crea un nuevo evento.
     * Datos de Solicitud:
     * {
     *     "name": "Evento 1",
     *     "date": "2024-07-13T00:00:00.000000Z",
     *     "slug": "evento-1",
     *     "user_id": 1,
     *     "description": "Descripción del evento 1",
     *     "location": "Ubicación del evento 1",
     *     "tipo": "salud",
     *     "status": true
     * }
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Evento creado exitosamente",
     *     "data": {
     *         "id": 1,
     *         "name": "Evento 1",
     *         "date": "2024-07-13T00:00:00.000000Z",
     *         "slug": "evento-1",
     *         "user_id": 1,
     *         "description": "Descripción del evento 1",
     *         "location": "Ubicación del evento 1",
     *         "tipo": "salud",
     *         "status": true,
     *         "created_by": null,
     *         "updated_by": null,
     *         "deleted_by": null,
     *         "created_at": "2024-07-13T00:00:00.000000Z",
     *         "updated_at": "2024-07-13T00:00:00.000000Z"
     *     }
     * }
     */
    Route::post('events', [EventController::class, 'store'])->name('events.store');

    /**
     * Actualizar un evento por ID.
     * Método HTTP: PUT
     * Ruta: /api/events/{id}
     * Descripción: Actualiza los datos de un evento específico por ID.
     * Datos de Solicitud:
     * {
     *     "name": "Evento 1",
     *     "date": "2024-07-13T00:00:00.000000Z",
     *     "slug": "evento-1",
     *     "user_id": 1,
     *     "description": "Descripción del evento 1",
     *     "location": "Ubicación del evento 1",
     *     "tipo": "salud",
     *     "status": true
     * }
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Evento actualizado exitosamente",
     *     "data": {
     *         "id": 1,
     *         "name": "Evento 1",
     *         "date": "2024-07-13T00:00:00.000000Z",
     *         "slug": "evento-1",
     *         "user_id": 1,
     *         "description": "Descripción del evento 1",
     *         "location": "Ubicación del evento 1",
     *         "tipo": "salud",
     *         "status": true,
     *         "created_by": null,
     *         "updated_by": null,
     *         "deleted_by": null,
     *         "created_at": "2024-07-13T00:00:00.000000Z",
     *         "updated_at": "2024-07-13T00:00:00.000000Z"
     *     }
     * }
     */
    Route::put('events/{id}', [EventController::class, 'update'])->name('events.update');

    /**
     * Eliminar un evento por ID.
     * Método HTTP: DELETE
     * Ruta: /api/events/{id}
     * Descripción: Elimina un evento específico por ID.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Evento eliminado exitosamente"
     * }
     */
    Route::delete('events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    
    Route::prefix('comando-equivalente')->group(function () {
        /**
         * Mostrar todos los registros de comando equivalente.
         * 
         * Método HTTP: GET
         * Ruta: /api/comando-equivalente
         * Descripción: Devuelve una lista de todos los comandos equivalentes, incluyendo el nombre del usuario relacionado.
         * 
         * Respuesta Exitosa:
         * {
         *     "data": [
         *         {
         *             "id": 1,
         *             "comando_id": 2,
         *             "name": "Comando Equivalente 1",
         *             "user_name": "Nombre del Usuario",
         *             "created_at": "2024-07-13T00:00:00.000000Z",
         *             "updated_at": "2024-07-13T00:00:00.000000Z"
         *         },
         *         ...
         *     ]
         * }
         */
        Route::get('/', [ComandoEquivalenteController::class, 'index']);


        /**
         * Crear un nuevo comando equivalente.
         * Método HTTP: POST
         * Ruta: /api/comando-equivalente
         * Descripción: Crea un nuevo comando equivalente con los datos proporcionados.
         * Datos de Solicitud:
         * {
         *     "comando_id": 1,
         *     "name": "Nombre del comando equivalente",
         *     "user_id": 1
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Comando equivalente creado exitosamente",
         *     "data": {
         *         "id": 1,
         *         "comando_id": 1,
         *         "name": "Nombre del comando equivalente",
         *         "user_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::post('/', [ComandoEquivalenteController::class, 'store']);

        /**
         * Obtener los detalles de un comando equivalente por ID.
         * Método HTTP: GET
         * Ruta: /api/comando-equivalente/{id}
         * Descripción: Retorna los detalles de un comando equivalente específico por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": {
         *         "id": 1,
         *         "comando_id": 1,
         *         "name": "Nombre del comando equivalente",
         *         "user_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::get('/{id}', [ComandoEquivalenteController::class, 'show']);

        /**
         * Obtener todos los comandos equivalentes de un usuario.
         * Método HTTP: GET
         * Ruta: /api/comando-equivalente/user/{user_id}
         * Descripción: Retorna una lista de todos los comandos equivalentes asociados a un usuario específico.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": [
         *         {
         *             "id": 1,
         *             "comando_id": 1,
         *             "name": "Nombre del comando equivalente 1",
         *             "user_id": 1,
         *             "created_at": "2024-07-13T00:00:00.000000Z",
         *             "updated_at": "2024-07-13T00:00:00.000000Z"
         *         },
         *         {
         *             "id": 2,
         *             "comando_id": 2,
         *             "name": "Nombre del comando equivalente 2",
         *             "user_id": 1,
         *             "created_at": "2024-07-13T00:00:00.000000Z",
         *             "updated_at": "2024-07-13T00:00:00.000000Z"
         *         }
         *     ]
         * }
         */
        Route::get('/user/{user_id}', [ComandoEquivalenteController::class, 'getByUserId']);

        /**
         * Actualizar un comando equivalente por ID.
         * Método HTTP: PUT
         * Ruta: /api/comando-equivalente/{id}
         * Descripción: Actualiza los datos de un comando equivalente específico por ID.
         * Datos de Solicitud:
         * {
         *     "comando_id": 1,
         *     "name": "Nombre actualizado del comando equivalente",
         *     "user_id": 1
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Comando equivalente actualizado exitosamente",
         *     "data": {
         *         "id": 1,
         *         "comando_id": 1,
         *         "name": "Nombre actualizado del comando equivalente",
         *         "user_id": 1,
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::put('/{id}', [ComandoEquivalenteController::class, 'update']);

        /**
         * Eliminar un comando equivalente por ID.
         * Método HTTP: DELETE
         * Ruta: /api/comando-equivalente/{id}
         * Descripción: Elimina un comando equivalente específico por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Comando equivalente eliminado exitosamente"
         * }
         */
        Route::delete('/{id}', [ComandoEquivalenteController::class, 'destroy']);
    });

    Route::prefix('comandos')->group(function () {
        /**
         * Listar todos los comandos.
         * Método HTTP: GET
         * Ruta: /api/comandos
         * Descripción: Obtiene una lista de todos los comandos de entrenamiento.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": [
         *         {
         *             "id": 1,
         *             "name": "Comando 1",
         *             "description": "Descripción del comando 1",
         *             "type": "especializado",
         *             "is_favorite": true,
         *             "category_id": 1,
         *             "voz_comando": "Voz de comando 1",
         *             "instructions": "Instrucciones del comando 1",
         *             "created_at": "2024-07-13T00:00:00.000000Z",
         *             "updated_at": "2024-07-13T00:00:00.000000Z"
         *         },
         *         ...
         *     ]
         * }
         */
        Route::get('/', [ComandoController::class, 'index']);

        /**
         * Mostrar un comando por ID.
         * Método HTTP: GET
         * Ruta: /api/comandos/{id}
         * Descripción: Obtiene los detalles de un comando específico por su ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": {
         *         "id": 1,
         *         "name": "Comando 1",
         *         "description": "Descripción del comando 1",
         *         "type": "especializado",
         *         "is_favorite": true,
         *         "category_id": 1,
         *         "voz_comando": "Voz de comando 1",
         *         "instructions": "Instrucciones del comando 1",
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::get('/{id}', [ComandoController::class, 'show']);

        /**
         * Crear un nuevo comando.
         * Método HTTP: POST
         * Ruta: /api/comandos
         * Descripción: Crea un nuevo comando de entrenamiento.
         * Datos de Solicitud:
         * {
         *     "name": "Comando 1",
         *     "description": "Descripción del comando 1",
         *     "type": "especializado",
         *     "is_favorite": true,
         *     "category_id": 1,
         *     "voz_comando": "Voz de comando 1",
         *     "instructions": "Instrucciones del comando 1"
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": {
         *         "id": 1,
         *         "name": "Comando 1",
         *         "description": "Descripción del comando 1",
         *         "type": "especializado",
         *         "is_favorite": true,
         *         "category_id": 1,
         *         "voz_comando": "Voz de comando 1",
         *         "instructions": "Instrucciones del comando 1",
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::post('/', [ComandoController::class, 'store']);

        /**
         * Actualizar un comando por ID.
         * Método HTTP: PUT
         * Ruta: /api/comandos/{id}
         * Descripción: Actualiza los datos de un comando específico por ID.
         * Datos de Solicitud:
         * {
         *     "name": "Comando Actualizado",
         *     "description": "Descripción actualizada",
         *     "type": "basico",
         *     "is_favorite": false,
         *     "category_id": 2,
         *     "voz_comando": "Voz de comando actualizada",
         *     "instructions": "Instrucciones actualizadas"
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": {
         *         "id": 1,
         *         "name": "Comando Actualizado",
         *         "description": "Descripción actualizada",
         *         "type": "basico",
         *         "is_favorite": false,
         *         "category_id": 2,
         *         "voz_comando": "Voz de comando actualizada",
         *         "instructions": "Instrucciones actualizadas",
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::put('/{id}', [ComandoController::class, 'update']);

        /**
         * Eliminar un comando por ID.
         * Método HTTP: DELETE
         * Ruta: /api/comandos/{id}
         * Descripción: Elimina un comando específico por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Comando eliminado exitosamente"
         * }
         */
        Route::delete('/{id}', [ComandoController::class, 'destroy']);
    });
});
Route::get('app-configuration', [SettingController::class, 'appConfiguraton']);

