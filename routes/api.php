<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EBookController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Api\ChipsController;
use App\Http\Controllers\Api\ClaseController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\VacunaController;
use App\Http\Controllers\Api\ComandoController;
use App\Http\Controllers\Api\TrainerController;
use App\Http\Controllers\Api\AntiTickController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Api\EjercicioController;
use App\Http\Controllers\Auth\API\AuthController;
use App\Http\Controllers\Api\PetHistoryController;
use App\Http\Controllers\Api\VeterinaryController;
use App\Http\Controllers\Api\AntiWormersController;
use App\Http\Controllers\Api\HerramientaController;
use App\Http\Controllers\Api\SharedOwnerController;
use App\Http\Controllers\Api\ActivityLevelController;
use App\Http\Controllers\Api\TrainingDiaryController;
use App\Http\Controllers\Api\GoogleCalendarController;
use App\Http\Controllers\Backend\API\BranchController;
use App\Http\Controllers\Api\CursoPlataformaController;
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
use App\Http\Controllers\Backend\API\AddressController;
use App\Http\Controllers\Backend\API\SettingController;
use App\Http\Controllers\Backend\API\UserApiController;
use App\Http\Controllers\Api\SpecialConditionController;
use App\Http\Controllers\Api\UserNotificationController;
use Modules\Pet\Http\Controllers\Backend\PetsController;
use App\Http\Controllers\Backend\API\DashboardController;
use Modules\Pet\Http\Controllers\Backend\BreedController;
use App\Http\Controllers\Api\ComandoEquivalenteController;
use App\Http\Controllers\Api\CoursePlatformUserController;
use Modules\Pet\Http\Controllers\Backend\API\PetController;
use App\Http\Controllers\Backend\API\NotificationsController;
use App\Http\Controllers\Api\HerramientasEntrenamientoController;
use Modules\Booking\Http\Controllers\Backend\API\BookingsController;
use Modules\Category\Http\Controllers\Backend\API\CategoryController;
use Modules\Service\Http\Controllers\Backend\API\ServiceTrainingController;

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
     * Obtener todos los usuarios por el user_type
     * Método HTTP: GET
     * Ruta: /get-user-by-type
     * Descripción: Recupera todos los usuarios en base al user_type
     * Parametro: user_type: string, ejemplo vet
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "data": [ Array de usuarios ]
     * }
     */
    Route::get('get-user-by-type', [UserController::class, 'getUserByType']);

    Route::get('user-notification',[UserNotificationController::class,'getNotification']);

    Route::put('user-notification/{id}',[UserNotificationController::class,'updateRead']);
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
     * Calificar libro
     * Método HTTP: POST
     * Ruta: /e-book-rating
     * Descripción: Agrega una calificacion a un libro en especifico
     * Parámetros:
     * - e_book_id: ID del e-book.
     * - user_id: ID del usuario
     * - review_msg: Mensaje o comentario
     * - rating: calificacion del libro
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "data": { Datos del BookRating }
     * }
     * Respuesta de Error:
     * {
     *     "success": false,
     *     "message": "mensaje de error"
     * }
     */
    Route::post('e-book-rating',[EBookController::class, 'bookRating']);

    /**
     * Eliminar calificacion del libro
     * Método HTTP: DELETE
     * Ruta: /e-book-rating/{id}
     * Descripción: Elimina una calificacion del libro
     * Parámetros:
     * - id: ID del BookRating.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "book rating deleted successfully"
     * }
     * Respuesta de Error:
     * {
     *     "success": false,
     *     "message": "mensaje de error"
     * }
     */
    Route::delete('e-book-rating/{id}',[EBookController::class, 'deleteBookRating']);


    /**
     * Obtener el rating de un e-book en especifico de manera paginada
     * Método HTTP: GET
     * Ruta: /get-book-rating-by-id-ebook
     * Descripción: Obtener el rating de un e-book en especifico de manera paginada
     * Parámetros:
     * - e_book_id: ID del e_book.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "data": [BookRating]
     * }
     * Respuesta de Error:
     * {
     *     "success": false,
     *     "message": "mensaje de error"
     * }
     */
    Route::get('get-book-rating-by-id-ebook',[EBookController::class,'getBookRatingByIdEbook']);
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
     * Ruta: /users-list
     * Descripción: Recupera todos los usuarios disponibles con su nombre y la información del perfil.
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Lista de Usuarios",
     *     "data": [
     *       {
     *         "id": 1,
     *         "full_name": "Super Admin",
     *         "email": "admin@pawlly.com",
     *         "mobile": "44-5289568745",
     *         "profile_image": "http://localhost:8001/storage/1/DbsVx3hU6fl5ZfCBcCC4oX6jlhI9ATgVp7tcxHBn.png",
     *         "created_at": "2024-08-12T06:58:57.000000Z"
     *       }
     *       ...
     *     ]
     * }
     */
    Route::get('/users-list', [UserApiController::class, 'user_list']);

    /**
     * Obtener Todos los Usuarios con Información del Perfil
     * Método HTTP: GET
     * Ruta: /users-list
     * Descripción: Recupera todos los usuarios disponibles (menos el que este autenticado o el que se restrinja) con su nombre y la información del perfil.
     * Datos de Solicitud:
     * {
     *     "user_id" (Integer, opcional): Usuario que se desea excluir de la lista.,
     * }
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Lista de Usuarios",
     *     "data": [
     *       {
     *         "id": 1,
     *         "full_name": "Super Admin",
     *         "email": "admin@pawlly.com",
     *         "mobile": "44-5289568745",
     *         "profile_image": "http://localhost:8001/storage/1/DbsVx3hU6fl5ZfCBcCC4oX6jlhI9ATgVp7tcxHBn.png",
     *         "created_at": "2024-08-12T06:58:57.000000Z"
     *       }
     *       ...
     *     ]
     * }
     */
    Route::get('/users-list-without-auth/{user_id?}', [UserApiController::class, 'user_list_without_auth']);

    /**
     * Obtener Todos los Usuarios con Información del Perfil
     * Método HTTP: GET
     * Ruta: /users-list
     * Descripción: Recupera todos los usuarios disponibles y separa los dueños que estan seleccionados.
     * Datos de Solicitud:
     * {
     *     "petId" (Integer, opcional): Mascota que se va a conseguir los dueños y el resto de usuarios de la lista.,
     * }
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Lista de Usuarios con dueños",
     *     "data": [
     *       {
     *         "users": {},
     *         "sharedOwners": {},
     *       }
     *       ...
     *     ]
     * }
     */
    Route::get('/users-and-owners/{petId?}', [PetsController::class, 'users_and_owners']);

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
         *             "difficulty":1,
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
         *         "difficulty": 1
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
         *         "difficulty": 1
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::get('{course_platform}', [CursoPlataformaController::class, 'show'])->name('course_platform.show');
        /**
         * Buscar cursos por temas o palabras claves
         * Método HTTP: GET
         * Ruta: /api/course-platform/search/palabra clave
         * Descripción: Recupera el listado de cursos por un tema o palabras claves
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Cursos de la plataforma recuperados exitosamente",
         *     "data": {
         *         "id": 1,
         *         "name": "Curso de la Plataforma 1",
         *         "description": "Descripción 1",
         *         "url": "http://example.com",
         *         "price": 100.00,
         *         "difficulty": 1
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::get('search/{search?}', [CursoPlataformaController::class, 'search'])->name('course_platform.search');
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
         *     "price": 100.00,
         *     "difficulty": 1
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
         *         "difficulty": 1,
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
   /**
         * Eliminar todos los videos de un curso
         * Método HTTP: DELETE
         * Ruta: /api/course-platform/delete-all-videos
         * Descripción: Elimina todos los videos de un curso
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Videos del Curso de la plataforma eliminado exitosamente"
         * }
         */
        Route::delete('{course_platform}/delete-all-videos',[CursoPlataformaController::class, 'deleteAllVideos']);
         /**
         * Eliminar un video de un curso
         * Método HTTP: DELETE
         * Ruta: /api/course-platform/delete-video/{video}
         * Descripción: Eliminar un video de un curso
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Video del Curso de la plataforma eliminado exitosamente"
         * }
         */
        Route::delete('{course_platform}/delete-video/{video}',[CursoPlataformaController::class, 'deleteVideo']);

            /**
         * Suscribirse a un curso de la plataforma
         * Método HTTP: POST
         * Ruta: /api/course-platform/subscribe
         * Descripción: Suscribirse a un curso de la plataforma
         * Parametros:
         * user_id: Id del usuario
         * course_platform_id: id del curso
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": "data de la suscripcion"
         * }
         * Respuesta fallida:
         * {
         *     "success": false,
         *     "message": "mensaje de error"
         * }
         */
        Route::post('subscribe',[CoursePlatformUserController::class,'subscribe']);
            /**
         * Actualizar progreso
         * Método HTTP: PUT
         * Ruta: /api/course-platform/subscribe/mark-video-as-watched
         * Descripción: Actualizar progreso
         * Parametros:
         * user_id: Id del usuario
         * course_platform_id: id del curso
         * course_platform_video_id: Id del video del curso
         * watched: valor booleano para el progreso
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": "data de la suscripcion"
         * }
         * Respuesta fallida:
         * {
         *     "success": false,
         *     "message": "mensaje de error"
         * }
         */
        Route::put('subscribe/mark-video-as-watched',[CoursePlatformUserController::class,'markVideoAsWatched']);
         /**
         * Obtener todos los cursos por user_id
         * Método HTTP: GET
         * Ruta: /api/course-platform/subscribe/all-courses-user
         * Descripción: Obtener todos los cursos por user_id
         * Parametros:
         * user_id: Id del usuario
         * per_page: numero para paginacion
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": "data de la suscripcion"
         * }
         */
        Route::get('subscribe/all-courses-user',[CoursePlatformUserController::class,'allCoursesUser']);

            /**
         * Obtener el rating por video del curso
         * Método HTTP: GET
         * Ruta: /api/course-platform/subscribe/get-rating-course-video
         * Descripción: Obtener el rating por video del curso
         * Parametros:
         * user_id: Id del usuario
         * course_platform_video_id: Id del video
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": "data del rating"
         * }
         * Respuesta Fallida:
         * {
         *  "success":false,
         *  "message": "No ratings found for the specified user"
         * }
         */
        Route::get('subscribe/get-rating-course-video',[CursoPlataformaController::class,'getRatingCourseVideo']);

             /**
         * Calificar el video del curso
         * Método HTTP: POST
         * Ruta: /api/course-platform/subscribe/get-rating-course-video
         * Descripción: Calificar el video del curso
         * Parametros:
         * user_id: Id del usuario
         * review_msg: mensaje o comentario
         * rating: calificacion del video
         * course_platform_video_id: Id del video
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": "data del rating"
         * }
         * Respuesta Fallida:
         * {
         *  "success":false,
         *  "message": "No ratings found for the specified user"
         * }
         */
        Route::post('subscribe/rating-course-video',[CursoPlataformaController::class,'ratingCoursePlatformVideo']);
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
         *             "difficulty":1,
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
     * Obtener eventos por user_id.
     * Método HTTP: GET
     * Ruta: /api/events/user/{user_id}
     * Descripción: Recupera los eventos disponibles para un usuario específico.
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
    Route::get('events/user/{user_id}', [EventController::class, 'getEventsByUser']);

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
     *     "end_date":"2024-07-13T00:00:00.000000Z",
     *     "event_time": "10:00:00",
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
     * Aceptar o rechazar el evento.
     * Método HTTP: PUT
     * Ruta: /api/accept-or-reject-event
     * Descripción: Acepta o rechaza el evento el usuario invitado a dicho evento.
     * Parametros:
     * confirm: bool
     * user_id: Id del usuario invitado
     * event_id: Id del evento
     * Respuesta Exitosa:
     * {
     *     'success' => true,
     *     'message' => 'Evento actualizado exitosamente',
     *     'data' =>  [
        *    'event'       => $eventDetail->event,
        *    'detail_event' => $eventDetail,
     *      ],
     * }
     *
     * Respuesta fallida:
     * {
     *  'success' => false,
     *  'message' => 'No se encontró el detalle del evento o ya ha sido actualizado.',
     * }
     */
    Route::put('accept-or-reject-event', [EventController::class, 'acceptOrRejectEvent']);
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

    /**
     * Crear un evento en Google Calendar.
     * Método HTTP: POST
     * Ruta: /api/events/calendar/create
     * Descripción: Crea un evento en Google Calendar utilizando los datos proporcionados y almacena la URL del evento en la base de datos.
     *
     * Parámetros de Solicitud:
     * - id_event: int (Requerido) - ID del evento en tu base de datos que contiene los detalles del evento (nombre, ubicación, fecha de inicio, fecha de fin).
     *
     * Requisitos:
     * - El usuario debe tener un token de acceso válido de Google Calendar almacenado en la sesión.
     *
     * Respuesta Exitosa:
     * {
     *     "success": true,
     *     "message": "Event added to Google Calendar",
     *     "event": object // Detalles del evento creado en Google Calendar, incluyendo el enlace al evento.
     * }
     *
     * Respuesta de Error:
     * {
     *     "error": "Error al crear el evento: [detalle del error]",
     *     "status": false
     * }
     */
    Route::post('/events/calendar/create', [GoogleCalendarController::class, 'createEvent'])->name('events.create');

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


    Route::prefix('herramientas')->group(function () {
        /**
         * Crear una nueva herramienta.
         * Método HTTP: POST
         * Ruta: /api/herramientas
         * Descripción: Crea una nueva herramienta de entrenamiento.
         * Datos de Solicitud:
         * {
         *     "name": "Nombre de la herramienta",
         *     "description": "Descripción de la herramienta",
         *     "type": "Silbato", // Opcional si se proporciona type_id
         *     "type_id": 1, // Opcional si se proporciona type
         *     "audio": "ruta/al/audio.mp3",
         *     "status": "active"
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": {
         *         "id": 1,
         *         "name": "Nombre de la herramienta",
         *         "description": "Descripción de la herramienta",
         *         "type_id": 1,
         *         "audio": "ruta/al/audio.mp3",
         *         "status": "active",
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::post('/', [HerramientaController::class, 'store']);

        /**
         * Mostrar una herramienta específica por ID.
         * Método HTTP: GET
         * Ruta: /api/herramientas/{id}
         * Descripción: Obtiene los detalles de una herramienta específica por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": {
         *         "id": 1,
         *         "name": "Nombre de la herramienta",
         *         "description": "Descripción de la herramienta",
         *         "type_id": 1,
         *         "audio": "ruta/al/audio.mp3",
         *         "status": "active",
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::get('/{id}', [HerramientaController::class, 'show']);

        /**
         * Listar todas las herramientas.
         * Método HTTP: GET
         * Ruta: /api/herramientas
         * Descripción: Obtiene una lista de todas las herramientas de entrenamiento.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": [
         *         {
         *             "id": 1,
         *             "name": "Nombre de la herramienta 1",
         *             "description": "Descripción de la herramienta 1",
         *             "type_id": 1,
         *             "audio": "ruta/al/audio1.mp3",
         *             "status": "active",
         *             "created_at": "2024-07-13T00:00:00.000000Z",
         *             "updated_at": "2024-07-13T00:00:00.000000Z"
         *         },
         *         ...
         *     ]
         * }
         */
        Route::get('/', [HerramientaController::class, 'index']);

        /**
         * Actualizar una herramienta específica por ID.
         * Método HTTP: PUT
         * Ruta: /api/herramientas/{id}
         * Descripción: Actualiza los datos de una herramienta específica por ID.
         * Datos de Solicitud:
         * {
         *     "name": "Nombre de la herramienta",
         *     "description": "Descripción de la herramienta",
         *     "type": "Silbato", // Opcional si se proporciona type_id
         *     "type_id": 1, // Opcional si se proporciona type
         *     "audio": "ruta/al/audio.mp3",
         *     "status": "active"
         * }
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "data": {
         *         "id": 1,
         *         "name": "Nombre de la herramienta",
         *         "description": "Descripción de la herramienta",
         *         "type_id": 1,
         *         "audio": "ruta/al/audio.mp3",
         *         "status": "active",
         *         "created_at": "2024-07-13T00:00:00.000000Z",
         *         "updated_at": "2024-07-13T00:00:00.000000Z"
         *     }
         * }
         */
        Route::put('/{id}', [HerramientaController::class, 'update']);

        /**
         * Eliminar una herramienta por ID.
         * Método HTTP: DELETE
         * Ruta: /api/herramientas/{id}
         * Descripción: Elimina una herramienta específica por ID.
         * Respuesta Exitosa:
         * {
         *     "success": true,
         *     "message": "Herramienta eliminada exitosamente"
         * }
         */
        Route::delete('/{id}', [HerramientaController::class, 'destroy']);
    });


    /**
     * Agregar un dueño compartido a una mascota.
     * Método HTTP: POST
     * Ruta: /api/pets/{petId}/shared-owners
     * Descripción: Asocia un dueño secundario a una mascota específica utilizando el ID de la mascota y del usuario.
     * Parámetros de Solicitud:
     * - user_id (integer): El ID del usuario que se va a agregar como dueño compartido.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Shared owner added successfully"
     * }
     */
    Route::post('/pets/{petId}/shared-owners', [SharedOwnerController::class, 'addSharedOwner']);

    /**
     * Agregar un dueño compartido a una mascota usando el email.
     * Método HTTP: POST
     * Ruta: /api/pets/{petId}/shared-owners-with-email
     * Descripción: Asocia un dueño secundario a una mascota específica utilizando el ID de la mascota y el correo del usuario.
     * Parámetros de Solicitud:
     * - email (string): El correo del usuario que se usara para buscar el id del mismo que se va a agregar como dueño compartido.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Shared owner added successfully"
     * }
     */
    Route::post('/pets/{petId}/shared-owners-with-email', [SharedOwnerController::class, 'addSharedOwnerWithEmail']);

    /**
     * Eliminar un dueño compartido de una mascota.
     * Método HTTP: DELETE
     * Ruta: /api/pets/{petId}/shared-owners
     * Descripción: Elimina la asociación de un dueño secundario con una mascota específica.
     * Parámetros de Solicitud:
     * - user_id (integer): El ID del usuario que se va a eliminar como dueño compartido.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Shared owner removed successfully"
     * }
     */
    Route::delete('/pets/{petId}/shared-owners', [SharedOwnerController::class, 'removeSharedOwner']);

    /**
     * Obtener los dueños de una mascota.
     * Método HTTP: GET
     * Ruta: /api/pets/{petId}/owners
     * Descripción: Recupera el dueño principal y los dueños compartidos de una mascota específica.
     * Respuesta Exitosa:
     * {
     *     "primary_owner": { ... },
     *     "shared_owners": [ ... ]
     * }
     */
    Route::get('/pets/{petId}/owners', [SharedOwnerController::class, 'getOwners']);

    // Retorna una lista paginada de tipos de mascotas, filtrada por estado activo y una búsqueda opcional.
    /**
     * Obtener una lista de tipos de mascotas.
     * Método HTTP: GET
     * Ruta: /api/pet-types
     * Descripción: Recupera una lista de tipos de mascotas, con soporte para paginación y búsqueda opcional.
     * Parámetros de Solicitud:
     * - per_page (integer, opcional): Número de resultados por página. Por defecto 10.
     * - search (string, opcional): Texto de búsqueda para filtrar tipos de mascotas por nombre.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [ ... ],
     *     "message": "Lista de tipos de mascotas recuperada con éxito"
     * }
     */
    Route::get('/pet-types', [PetController::class, 'petTypeList']);

    // Retorna una lista paginada de mascotas de un usuario específico, filtrada por estado, tipo de mascota y búsqueda opcional.
    /**
     * Obtener una lista de mascotas.
     * Método HTTP: GET
     * Ruta: /api/pets
     * Descripción: Recupera una lista de mascotas asociadas a un usuario, con soporte para paginación, filtrado por tipo de mascota y búsqueda opcional.
     * Parámetros de Solicitud:
     * - per_page (integer, opcional): Número de resultados por página. Por defecto 10.
     * - search (string, opcional): Texto de búsqueda para filtrar mascotas por nombre.
     * - user_id (integer, opcional): ID del usuario para filtrar las mascotas por dueño. Por defecto, el usuario autenticado.
     * - pettype_id (integer, opcional): ID del tipo de mascota para filtrar las mascotas.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [ ... ],
     *     "message": "Lista de mascotas recuperada con éxito"
     * }
     */
    Route::get('/pets', [PetController::class, 'petList']);

    // Retorna una lista paginada de razas, filtrada por estado, tipo de mascota y búsqueda opcional en el nombre o descripción.
    /**
     * Obtener una lista de razas de mascotas.
     * Método HTTP: GET
     * Ruta: /api/breeds
     * Descripción: Recupera una lista de razas, con soporte para paginación, filtrado por tipo de mascota y búsqueda opcional en nombre o descripción.
     * Parámetros de Solicitud:
     * - per_page (integer, opcional): Número de resultados por página. Por defecto 10.
     * - search (string, opcional): Texto de búsqueda para filtrar razas por nombre o descripción.
     * - pettype_id (integer, opcional): ID del tipo de mascota para filtrar las razas.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [ ... ],
     *     "message": "Lista de razas recuperada con éxito"
     * }
     */
    Route::get('/breeds', [PetController::class, 'breedList']);

    // Retorna una lista paginada de notas de mascotas, filtrada por estado, privacidad y tipo de usuario (usuario o administrador).
    /**
     * Obtener una lista de notas de mascotas.
     * Método HTTP: GET
     * Ruta: /api/pet-notes
     * Descripción: Recupera una lista de notas de mascotas, con soporte para paginación, filtrado por estado, privacidad y tipo de usuario.
     * Parámetros de Solicitud:
     * - per_page (integer, opcional): Número de resultados por página. Por defecto 10.
     * - search (string, opcional): Texto de búsqueda para filtrar notas de mascotas por nombre.
     * - pet_id (integer, opcional): ID de la mascota para filtrar las notas.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [ ... ],
     *     "message": "Lista de notas de mascotas recuperada con éxito"
     * }
     */
    Route::get('/pet-notes', [PetController::class, 'petNoteList']);


    /**
     * Metodo: POST
     * Ruta: '/api/special-conditions
     * Parametros:
     * "pet_id": ID de la mascota (Requerido),
     *  "allergies": Descripcion de la alergia (opcional),
     *   "chronic_diseases": Descripcion de la enfermedad cronica (opcional),
     *  "disabilities": Descripcion de la discapacidad (opcional),
     *   "food_needs": Descripcion de la necesidad de alimento (opcional),
     *   "medications": Descripcion de los medicamentos (opcional),
     * Respuesta exitosa
     * {
     *"success": true,
     *"message": "record updated successfully",
     *"data": {
     *  "id": 1,
     *  "pet_id": 1,
     *  "allergies": "Posee alergias",
     *  "chronic_diseases": null,
     *  "disabilities": null,
     *  "food_needs": null,
     *  "medications": null,
     *  "created_at": "2024-11-13T16:03:55.000000Z",
     *  "updated_at": "2024-11-13T16:10:38.000000Z"
     *}
     *}
     * Respuesta fallida
     * {
     *  "success":false,
     *  "message": "Mensaje de error"
     * }
     * Metodo: PUT
     * Ruta: '/api/special-conditions/{id}
     * Similar al metodo anterior en parametros y la respuesta
     *
     * Metodo: DELETE
     * Ruta:/api/special-conditions/{id}
     * Respuesta exitosa
     * {
     *  "success":true,
     *  "message": "Registro eliminado con exito"
     * }
     * Respuesta fallida:
     * {
     *  "success":false,
     *  "message": "Mensaje de error"
     * }
     * Metodo: GET
     * Ruta: '/api/special-conditions/{id}
     * Respuesta exitosa
     * {
     *  "success":true,
     *  "data": {
     *"id": 1,
     *"pet_id": 1,
     *"allergies": "Posee alergias",
     *"chronic_diseases": null,
     *"disabilities": null,
     *"food_needs": null,
     *"medications": null,
     *"created_at": "2024-11-13T16:03:55.000000Z",
     *"updated_at": "2024-11-13T16:10:38.000000Z"
     *}
     * Respuesta fallida
     * {
     *  "success":false,
     *  "message": "Mensaje de error"
     * }
     *
     */

    Route::resource('special-conditions', SpecialConditionController::class);

    // Retorna una lista paginada de dueños y sus mascotas, basándose en un employee_id y los datos de reserva.
    /**
     * Obtener una lista de dueños y sus mascotas.
     * Método HTTP: GET
     * Ruta: /api/owner-pets
     * Descripción: Recupera una lista de dueños y sus mascotas, basándose en un ID de empleado y los datos de reserva, con soporte para paginación.
     * Parámetros de Solicitud:
     * - per_page (integer, opcional): Número de resultados por página. Por defecto 10.
     * - employee_id (integer, opcional): ID del empleado para filtrar las reservas. Por defecto, el empleado autenticado.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [ ... ],
     *     "message": "Lista de dueños y mascotas recuperada con éxito"
     * }
     */
    Route::get('/owner-pets', [PetController::class, 'OwnerPetList']);

    // Retorna los detalles completos de una mascota específica, incluyendo su tipo, raza y notas asociadas.
    /**
     * Obtener detalles de una mascota específica.
     * Método HTTP: GET
     * Ruta: /api/pet-details
     * Descripción: Recupera los detalles completos de una mascota específica, incluyendo su tipo, raza y notas asociadas.
     * Parámetros de Solicitud:
     * - pet_id (integer, opcional): ID de la mascota para recuperar los detalles. Obligatorio si no se pasa por URL.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": { ... },
     *     "message": "Detalles de la mascota recuperados con éxito"
     * }
     */
    Route::get('/pet-details', [PetController::class, 'PetDetails']);

    /**
     * Crear una nueva mascota.
     * Método HTTP: POST
     * Ruta: /api/pets
     * Descripción: Crea una nueva mascota con los datos proporcionados. El campo 'breed_id' se valida primero, y si no es válido, se intenta validar 'breed_name'. Si ambos son inválidos, se devuelve un error.
     * Parámetros de Solicitud:
     * - name (string): Nombre de la mascota (requerido).
     * - breed_id (integer, opcional): ID de la raza de la mascota. Si se proporciona y es inválido, se prueba con 'breed_name'.
     * - breed_name (string, opcional): Nombre de la raza de la mascota. Se utiliza si 'breed_id' no es válido o no se proporciona.
     * - size (string, opcional): Tamaño de la mascota.
     * - date_of_birth (date, opcional): Fecha de nacimiento de la mascota. Formato: 'YYYY-MM-DD'.
     * - age (string, opcional): Edad de la mascota.
     * - gender (string, opcional): Género de la mascota. Valores permitidos: 'male', 'female'.
     * - weight (numeric, opcional): Peso de la mascota.
     * - height (numeric, opcional): Altura de la mascota.
     * - weight_unit (string, opcional): Unidad de peso.
     * - height_unit (string, opcional): Unidad de altura.
     * - user_id (integer): ID del dueño de la mascota (requerido).
     * - additional_info (string, opcional): Información adicional sobre la mascota.
     * - status (boolean, opcional): Estado activo o inactivo de la mascota. Valores permitidos: 1 o 0, true o false. Por defecto es 1 (activo).
     * - pet_image (string): Url de la magen de la mascota.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Mascota creada exitosamente",
     *     "data": { ... }
     * }
     *
     * Respuesta de Error (422):
     * {
     *     "message": "El breed_id o breed_name proporcionado no es válido."
     *     // o
     *     "message": "El usuario especificado no existe."
     * }
     */
    Route::post('/pets', [PetController::class, 'store']);

    /**
     * Editar una mascota.
     * Método HTTP: POST
     * Ruta: /api/pets/{id}
     * Descripción: Actualiza los datos de una mascota específica. Se puede enviar cualquier campo para ser actualizado.
     * Parámetros de Solicitud (opcional):
     * - name (string): Nombre de la mascota.
     * - breed_id (integer): ID de la raza de la mascota.
     * - breed_name (string): Nombre de la raza de la mascota.
     * - size (string): Tamaño de la mascota.
     * - date_of_birth (date): Fecha de nacimiento de la mascota. Formato: 'YYYY-MM-DD'.
     * - age (string): Edad de la mascota.
     * - gender (string): Género de la mascota. Valores permitidos: 'male', 'female'.
     * - weight (numeric): Peso de la mascota.
     * - height (numeric): Altura de la mascota.
     * - weight_unit (string): Unidad de peso.
     * - height_unit (string): Unidad de altura.
     * - user_id (integer): ID del dueño de la mascota.
     * - additional_info (string): Información adicional sobre la mascota.
     * - status (boolean): Estado activo o inactivo de la mascota. Valores permitidos: 1 o 0, true o false.
     * - pet_image (string): Url de la magen de la mascota.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Mascota actualizada exitosamente",
     *     "data": { ... }
     * }
     *
     * Respuesta de Error (422):
     * {
     *     "message": "El breed_id o breed_name proporcionado no es válido."
     *     // o
     *     "message": "El usuario especificado no existe."
     * }
     */
    Route::post('/pets/{id}', [PetController::class, 'update']);

    /**
     * Ver detalles de una mascota.
     * Método HTTP: GET
     * Ruta: /api/pets/{id}
     * Descripción: Recupera los detalles de una mascota específica por su ID.
     * Parámetros de Solicitud: Ninguno
     *
     * Respuesta Exitosa:
     * {
     *     "data": { ... },
     *     "message": "Detalles de la mascota recuperados con éxito"
     * }
     *
     * Respuesta de Error (404):
     * {
     *     "message": "Mascota no encontrada."
     * }
     */
    Route::get('/pets/{id}', [PetController::class, 'show']);

    /**
     * Eliminar una mascota.
     * Método HTTP: DELETE
     * Ruta: /api/pets/{id}
     * Descripción: Elimina una mascota específica por su ID.
     * Parámetros de Solicitud: Ninguno
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Mascota eliminada exitosamente"
     * }
     *
     * Respuesta de Error (404):
     * {
     *     "message": "Mascota no encontrada."
     * }
     */
    Route::delete('/pets/{id}', [PetController::class, 'destroy']);

    /**
     * Rutas para trabajar con el diario de entrenamiento
     * Metodo: POST
     * Ruta:/api/training-diaries
     * Parametros:{
     *  pet_id: id de la mascota (requerido),
     * category_id: Id de la categoria (requerido),
     *   date: fecha del diario (requerido),
     *   actividad: descripcion de la actidad realizada por la mascota (requerido),
     *   notas: Comentarios adicionales (opcional),
     *   "image": campo imagen de dicha actividad (opcional)
     * }
     *
     *    * Respuesta Exitosa:
     * {
     *     "message": "Diario registrado exitosamente"
     * }
     *
     * Respuesta de Error
     * {
     *     "message": "Mensaje de error."
     * }
     *
     * Metodo: PUT
     * Ruta:/api/training-diaries/1
     * descripcion: Actualizar un diario
     *  pet_id: id de la mascota (requerido)
     *  category_id: Id de la categoria (requerido),
     *   date: fecha del diario (requerido),
     *   actividad: descripcion de la actidad realizada por la mascota (requerido),
     *   notas: Comentarios adicionales (opcional),
     *   "image": campo imagen de dicha actividad (opcional)
     * }
     *
     *   Respuesta Exitosa:
     * {
     *      "success": true,
     *     "message": "Diario Actualizado exitosamente"
     * }
     *
     * Respuesta de Error
     * {
     *     "success": false,
     *     "message": "Mensaje de error."
     * }
     * Metodo: DELETE
     * Ruta:/api/training-diaries/1
     * descripcion: Eliminar un diario
     * Parametros: id: ID del diario (requerido)
     *
     *   Respuesta Exitosa:
     * {
     *      "success": true,
     *     "message": "Diario eliminado exitosamente"
     * }
     *
     * Respuesta de Error
     * {
     *      "success": false,
     *     "message": "mensaje de error."
     * }

     * Metodo: GET
     * Ruta:/api/training-diaries/1
     * descripcion: ver el detalle de un diario
     * paramentro: id: campo requerido para ver el diario
     * Respuesta Exitosa:
     * {
     * "success": true,
     *"data": {
     *   "id": 1,
     *   "date": "2024-11-12 16:17:20",
     *   "actividad": "Texto de prueba 2",
     *   "notas": "Esto es una prueba 2",
     *   "pet_id": 2,
     *   "image": "https://placecats.com/neo_2/300/400",
     *   "created_at": "2024-11-12T19:21:01.000000Z",
     *   "updated_at": "2024-11-12T20:17:20.000000Z"
     * }
     *}
     * Respuesta de Error
     * {
     *      "success": false,
     *     "message": "mensaje de error."
     * }
     */

    Route::resource('training-diaries', TrainingDiaryController::class);
    /* Metodo: GET
    * Ruta:/api/get-diary
    * descripcion: Obtener todo el listado del diario de una mascota
    * paramentro: pet_id: ID de la mascota
    * Respuesta Exitosa:
    * {
    * "success": true,
    *"data": {
    *   "id": 1,
    *   "date": "2024-11-12 16:17:20",
    *   "actividad": "Texto de prueba 2",
    *   "notas": "Esto es una prueba 2",
    *   "pet_id": 2,
    *   "image": "https://placecats.com/neo_2/300/400",
    *   "created_at": "2024-11-12T19:21:01.000000Z",
    *   "updated_at": "2024-11-12T20:17:20.000000Z"
    * }
    *}
    * Respuesta de Error
    * {
    *      "success": false,
    *     "message": "mensaje de error."
    * }
    */
    Route::get('get-diary', [TrainingDiaryController::class, 'getDiario']);
    /**
     * Obtener la lista de reservas.
     * Método HTTP: GET
     * Ruta: /api/bookings/get
     * Descripción: Obtiene una lista de reservas filtradas según los parámetros proporcionados.
     *
     * Parámetros de Solicitud:
     * - user_id: int (Opcional) - ID del usuario (En caso, de no estar colocado, recibira las reservaciones del usuario que este autenticado).
     * - booking_type: string (Opcional) - Tipo de reserva (e.g., 'training', 'veterinary').
     * - nearby_booking: int (Opcional) - Si es 1, obtiene reservas cercanas.
     * - system_service_name: string (Opcional) - Nombres de servicios del sistema, separados por comas.
     * - status: string (Opcional) - Estados de reserva, separados por comas.
     * - per_page: int|string (Opcional) - Número de resultados por página o 'all' para todos los resultados.
     * - order_by: string (Opcional) - Orden de los resultados ('asc' o 'desc').
     * - search: string (Opcional) - Término de búsqueda para filtrar reservas por ID, nombre de mascota, nombre de empleado o nombre de usuario.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [
     *         // Datos de las reservas
     *     ],
     *     "message": "Lista de reservas obtenida exitosamente."
     * }
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Error message"
     * }
     */
    Route::get('/bookings/get', [BookingsController::class, 'bookingList'])->name('bookings.list');

    /**
     * Obtener la lista de reservas.
     * Método HTTP: GET
     * Ruta: /api/bookings/trainings/get
     * Descripción: Obtiene una lista de reservas de entrenamientos filtradas según los parámetros proporcionados.
     *
     * Parámetros de Solicitud:
     * - user_id: int (Opcional) - ID del usuario (En caso, de no estar colocado, recibira las reservaciones del usuario que este autenticado).
     * - nearby_booking: int (Opcional) - Si es 1, obtiene reservas cercanas.
     * - system_service_name: string (Opcional) - Nombres de servicios del sistema, separados por comas.
     * - status: string (Opcional) - Estados de reserva, separados por comas.
     * - per_page: int|string (Opcional) - Número de resultados por página o 'all' para todos los resultados.
     * - order_by: string (Opcional) - Orden de los resultados ('asc' o 'desc').
     * - search: string (Opcional) - Término de búsqueda para filtrar reservas por ID, nombre de mascota, nombre de empleado o nombre de usuario.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [
     *         // Datos de las reservas
     *     ],
     *     "message": "Lista de reservas de entrenamiento obtenidas exitosamente."
     * }
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Error message"
     * }
     */
    Route::get('/bookings/training/get', [BookingsController::class, 'bookingListTraining'])->name('bookings.list');

    /**
     * Crear o actualizar una reserva.
     * Método HTTP: POST
     * Ruta: /api/bookings/store
     * Descripción: Crea o actualiza una reserva dependiendo de si se proporciona un ID en la solicitud.
     *
     * Parámetros de Solicitud:
     * - booking_type: string (Requerido) - Tipo de reserva (e.g., 'veterinary', 'training')
     * - date_time: string (Requerido) - Fecha y hora de la reserva
     * - duration: string (Opcional) - Duración de la reserva
     * - service_id: int (Opcional) - ID del servicio (solo para 'veterinary')
     * - service_name: string (Opcional) - Nombre del servicio (solo para 'veterinary')
     * - employee_id: int (Requerido) - ID del empleado (entrenador o veterinario)
     * - reason: string (Opcional) - Razón de la consulta (solo para 'veterinary')
     * - start_video_link: string (Opcional) - Enlace para iniciar la videollamada (solo para 'veterinary')
     * - join_video_link: string (Opcional) - Enlace para unirse a la videollamada (solo para 'veterinary')
     * - medical_report: file (Opcional) - Archivo del reporte médico (solo para 'veterinary')
     * - training_id: int (Opcional) - ID del entrenamiento (solo para 'training')
     * - user_id: int (Opcional) - ID del usuario (Se tomará el usuario autenticado, en caso de no estar definido)
     * - service_amount: float - Monto del servicio
     * - price: float - Precio del servicio
     * - latitude: float (Opcional) - Para la notificación por ubicación
     * - longitude: float (Opcional) - Para la notificación por ubicación
     *
     * Respuesta Exitosa:
     * {
     *     "message": "New Booking Added", // O "Booking Updated" si se actualizó una reserva existente
     *     "status": true,
     *     "data": object // Detalles de la reserva
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error message",
     *     "status": false
     * }
     */
    Route::post('/bookings/store', [BookingsController::class, 'store'])->name('bookings.store');

    /**
     * Actualizar el estado de una reserva.
     * Método HTTP: PUT
     * Ruta: /api/bookings/status
     * Descripción: Actualiza el estado de una reserva existente por su ID.
     *
     * Parámetros de Solicitud:
     * - id: int (Requerido) - ID de la reserva.
     * - status: string (Requerido) - Nuevo estado de la reserva (completed, cancelled, in-progress, rejected, pending, confirmed).
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Estado de la reserva actualizado exitosamente",
     *     "status": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error message",
     *     "status": false
     * }
     */
    Route::put('/bookings/status', [BookingsController::class, 'updateStatus'])->name('bookings.updateStatus');

    /**
     * Aceptar una reserva.
     * Método HTTP: PUT
     * Ruta: /api/bookings/status/confirmed
     * Descripción: Acepta el estado de una reserva existente por su ID.
     *
     * Parámetros de Solicitud:
     * - id: int (Requerido) - ID de la reserva.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Estado de la reserva actualizado exitosamente",
     *     "status": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error message",
     *     "status": false
     * }
     */
    Route::put('/bookings/status/confirmed', [BookingsController::class, 'updateStatusConfirmed'])->name('bookings.updateStatusConfirmed');

    /**
     * Actualizar una reserva existente.
     * Método HTTP: PUT
     * Ruta: /api/bookings/{id}
     * Descripción: Actualiza una reserva existente por su ID.
     *
     * Parámetros de Solicitud:
     * - Cualquier parámetro que sea necesario para actualizar la reserva.
     * - services: array (Opcional) - Servicios asociados a la reserva.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Reserva actualizada exitosamente",
     *     "data": {
     *         // Datos de la reserva actualizada
     *     },
     *     "status": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error message",
     *     "status": false
     * }
     */
    Route::put('/bookings/{id}', [BookingsController::class, 'update'])->name('bookings.update');

    /**
     * Obtener los detalles de una reserva.
     * Método HTTP: GET
     * Ruta: /api/bookings/detail
     * Descripción: Obtiene los detalles de una reserva específica por su ID.
     *
     * Parámetros de Solicitud:
     * - id: int (Requerido) - ID de la reserva.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": {
     *         // Datos de la reserva
     *     },
     *     "customer_review": {
     *         // Reseña del cliente
     *     },
     *     "message": "Detalles de la reserva obtenidos exitosamente."
     * }
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Reserva no encontrada."
     * }
     */
    Route::get('/bookings/detail', [BookingsController::class, 'bookingDetail'])->name('bookings.detail');

    /**
     * Obtener la lista de estados de reservas.
     * Método HTTP: GET
     * Ruta: /api/bookings/status-list
     * Descripción: Obtiene la lista de todos los estados de reservas.
     *
     * Parámetros de Solicitud: Ninguno
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [
     *         {
     *             "status": "estado",
     *             "title": "título",
     *             "is_disabled": boolean,
     *             "next_status": "siguiente_estado" (opcional)
     *         },
     *         // Otros estados...
     *     ],
     *     "message": "Lista de estados de reservas obtenida exitosamente."
     * }
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Error message"
     * }
     */
    Route::get('/bookings/status-list', [BookingsController::class, 'statusList'])->name('bookings.statusList');

    /**
     * Aceptar una reserva.
     * Método HTTP: PUT
     * Ruta: /api/bookings/accept/{id}
     * Descripción: Permite que un empleado acepte una reserva, asignando su ID a la reserva y actualizando el estado de la solicitud.
     *
     * Parámetros de Solicitud:
     * - {id}: int (Requerido) - ID de la reserva a aceptar.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Reserva aceptada exitosamente.",
     *     "status": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "La reserva ya ha sido aceptada."
     * }
     */
    Route::put('/bookings/accept/{id}', [BookingsController::class, 'accept_booking'])->name('bookings.accept');
    /**
     * Agregar calificacion y raiting
     * Método HTTP: POST
     * Ruta: /api/raiting-user
     * Descripción: Agregar calificacion y raiting al usuario
     *
     * Parámetros de Solicitud:
     * - user_id: int (Requerido) - ID del usuario.
     * - employee_id: int (Requerido) -ID del empleado
     * -review_msg: string (opcional) Comentario
     * -rating: int (opcional) calificacion
     *
     * Respuesta Exitosa:
     * {
     *  "success": true,
     *  "data": [
     *  {
     *   Datos de la calificacion
     * }
     * ]
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Mensaje de error"
     * }
     */
    Route::post('raiting-user', [EmployeeController::class, 'ratingUserStore']);
    /**
     * Actualizar calificacion y raiting
     * Método HTTP: PUT
     * Ruta: /api/raiting-user/{id}
     * Descripción: Actualizar calificacion y raiting al usuario
     *
     * Parámetros de Solicitud:
     * -id: int (Requerido) -ID de la calificacion
     * - user_id: int (Requerido) - ID del usuario.
     * - employee_id: int (Requerido) -ID del empleado
     * -review_msg: string (opcional) Comentario
     * -rating: int (opcional) calificacion
     *
     * Respuesta Exitosa:
     * {
     *  "success": true,
     *  "data": [
     *  {
     *   Datos de la calificacion
     * }
     * ]
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Mensaje de error"
     * }
     */
    Route::put('raiting-user/{id}', [EmployeeController::class, 'updateRaiting']);
    /**
     * Listado de raiting
     * Método HTTP: GET
     * Ruta: /api/raiting-user
     * Descripción: Listado de raiting del usuario
     *
     * Parámetros de Solicitud:
     * - user_id: int (opcional) - ID del usuario.
     * - employee_id: int (opcional) -ID del empleado
     *
     * Respuesta Exitosa:
     * {
     *  "success": true,
     *  "data": [
     *  {
     *   Datos de la calificacion
     * }
     * ]
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Mensaje de error"
     * }
     */
    Route::get('raiting-user', [EmployeeController::class, 'getRating']);
    /**
     * Eliminar calificacion
     * Método HTTP: DELETE
     * Ruta: /api/raiting-user/{id}
     * Descripción: Eliminar calificacion del usuario
     *
     * Parámetros de Solicitud:
     * - id: int (Requerido) - ID de la calificacion.
     *
     * Respuesta Exitosa:
     * {
     *  "success": true,
     *  "message":"calificacion eliminada"
     * ]
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Mensaje de error"
     * }
     */
    Route::delete('raiting-user/{id}', [EmployeeController::class, 'destroyRaiting']);
    /**
     * Listado de veterinarios y entrenadores que atendieron a una mascota.
     * Método HTTP: GET
     * Ruta: /api/get-who-cared-for-my-pet
     * Descripción: Permite obtener el listado de veterinarios y entrenadores que atendieron a una mascota.
     *
     * Parámetros de Solicitud:
     * - pet_id: int (Requerido) - ID de la mascota.
     *
     * Respuesta Exitosa:
     * {
     *  "success": true,
     *  "message": "Query completed successfully",
     *  "data": [
     *  {
     *   "first_name": "Wade",
     *   "last_name": "Allen",
     *   "specialty": "veterinary",
     *   "date": "01/08/2023",
     *   "note": null,
     *   "booking_extra_info": ""
     * }
     * ]
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Mensaje de error"
     * }
     */
    Route::get('get-who-cared-for-my-pet', [BookingsController::class, 'getWhoCaredForMyPet']);
    /**
     * Crear un nuevo servicio de entrenamiento.
     * Método HTTP: POST
     * Ruta: /api/service-training
     * Descripción: Crea un nuevo servicio de entrenamiento y genera automáticamente un slug único a partir del nombre.
     *
     * Parámetros de Solicitud:
     * - name: string (Requerido) - El nombre del servicio de entrenamiento.
     * - description: string (Opcional) - La descripción del servicio de entrenamiento.
     * - status: boolean (Opcional) - El estado del servicio de entrenamiento.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Formulario de creación de servicio enviado con éxito.",
     *     "status": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Mensaje de error",
     *     "status": false
     * }
     */
    Route::post('/service-training', [ServiceTrainingController::class, 'store'])->name('service-training.store');

    /**
     * Obtener la lista de servicios de entrenamiento.
     * Método HTTP: GET
     * Ruta: /api/service-training/get
     * Descripción: Obtiene una lista paginada de servicios de entrenamiento activos con opción de búsqueda.
     *
     * Parámetros de Solicitud:
     * - per_page: int (Opcional) - Número de resultados por página. Predeterminado es 10.
     * - search: string (Opcional) - Término de búsqueda para filtrar servicios de entrenamiento por nombre o descripción.
     *
     * Respuesta Exitosa:
     * {
     *     "status": true,
     *     "data": [
     *         {
     *             "id": 1,
     *             "slug": "entrenamiento-basico",
     *             "name": "Entrenamiento Básico",
     *             "description": "Un curso de entrenamiento básico para perros.",
     *             "status": 1,
     *             "created_by": null,
     *             "updated_by": null,
     *             "deleted_by": null,
     *             "created_at": "2023-01-01T00:00:00.000000Z",
     *             "updated_at": "2023-01-01T00:00:00.000000Z",
     *             "deleted_at": null
     *         },
     *         // Otros servicios de entrenamiento...
     *     ],
     *     "message": "Lista de servicios de entrenamiento obtenida con éxito."
     * }
     *
     * Respuesta de Error:
     * {
     *     "status": false,
     *     "message": "Mensaje de error"
     * }
     */
    Route::get('/service-training/get', [ServiceTrainingController::class, 'trainingList'])->name('service-training.list');

    /**
     * Obtener la lista de veterinarios para una mascota.
     * Método HTTP: GET
     * Ruta: /api/list-veterinaries/{petId}
     * Descripción: Devuelve una lista de consultas veterinarias completadas para una mascota específica.
     *
     * Parámetros de Ruta:
     * - petId: int (Requerido) - ID de la mascota para la cual se desean obtener las consultas veterinarias.
     *
     * Respuesta Exitosa:
     * {
     *     "data": [
     *         {
     *             "id": int, // ID de la reserva
     *             "pet_id": int, // ID de la mascota
     *             "status": string, // Estado de la reserva ('completed')
     *             "booking_type": string, // Tipo de reserva ('veterinary')
     *             // Otros detalles adicionales de la reserva...
     *         },
     *         // Más objetos de reserva...
     *     ],
     *     "message": "Lista de veterinarios.",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "error": "Error message",
     *     "status": false
     * }
     */
    Route::get('/list-veterinaries/{petId}', [VeterinaryController::class, 'listVeterinaries']);

    /**
     * Obtener la lista de mascotas asignadas al veterinario.
     * Método HTTP: GET
     * Ruta: /api/pet-list-by-veterinarian
     * Descripción: Obtener la lista de mascotas asignadas al veterinario.
     *
     * Parámetros de Ruta:
     * - user_id: int (Requerido) - ID del veterinario
     * - most_recent: string (opcional) - mostrar los mas recientes
     * - sort_asc_alphabetically: string (opcional) - ordenar de la A-Z
     * - sort_desc_alphabetically: string (opcional) - ordenar de la Z-A
     * - category: array int (opcional) - array te ID de categorias
     * - date: date (opcional)  fecha de asignacion
     * Respuesta Exitosa:
     * {
     *     "data": [
     *         {
     *          "informacion de la mascota"
     *         },
     *
     *     ],
     *
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "error": "Error message",
     *     "status": false
     * }
     */
    Route::get('pet-list-by-veterinarian', [VeterinaryController::class, 'petListByVeterinarian']);
    /**
     * Obtener la lista de entrenadores para una mascota.
     * Método HTTP: GET
     * Ruta: /api/list-trainers/{petId}
     * Descripción: Devuelve una lista de sesiones de entrenamiento completadas para una mascota específica.
     *
     * Parámetros de Ruta:
     * - petId: int (Requerido) - ID de la mascota para la cual se desean obtener las sesiones de entrenamiento.
     *
     * Respuesta Exitosa:
     * {
     *     "data": [
     *         {
     *             "id": int, // ID de la reserva de entrenamiento
     *             "pet_id": int, // ID de la mascota
     *             "status": string, // Estado de la reserva ('completed')
     *             "booking_type": string, // Tipo de reserva ('training')
     *             // Otros detalles adicionales de la reserva...
     *         },
     *         // Más objetos de reserva...
     *     ],
     *     "message": "List of trainers.",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "error": "Error message",
     *     "status": false
     * }
     */
    Route::get('/list-trainers/{petId}', [TrainerController::class, 'listTrainers']);

    /**
     * Obtener la lista combinada de reservas completadas de entrenadores y veterinarios para una mascota.
     *
     * Método HTTP: GET
     * Ruta: /api/list-trainers-veterinaries/{petId}
     * Descripción: Devuelve una lista combinada de reservas completadas de tipo 'training' y 'veterinary' para una mascota específica.
     *
     * Parámetros de Ruta:
     * - petId: int (Requerido) - ID de la mascota para la cual se desean obtener las reservas.
     *
     * Respuesta Exitosa:
     * {
     *     "data": [
     *         {
     *             "id": int, // ID de la reserva
     *             "pet_id": int, // ID de la mascota
     *             "status": string, // Estado de la reserva ('completed')
     *             "booking_type": string, // Tipo de reserva ('training' o 'veterinary')
     *             // Otros detalles adicionales de la reserva...
     *         },
     *         // Más objetos de reserva...
     *     ],
     *     "message": "Lista de reservaciones de entrenadores y veterinarios",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error message",
     *     "success": false
     * }
     */
    Route::get('/list-trainers-veterinaries/{petId}', [TrainerController::class, 'listTrainersVeterinaries']);

    Route::post('request-permission', [AuthController::class, 'requestPermission']);

    Route::put('request-permission/{id}', [AuthController::class, 'respondToRequest']);
    Route::get('get-user-social-network', [TrainerController::class, 'getUserSocialNetwork']);
    Route::get('list-category', [CategoryController::class, 'categoryList']);
    /**
     * Historial de mascota por el dueño
     *
     * Método HTTP: GET
     * Ruta: /api/pet-clinical-history-for-owner
     * Descripción: Historial de mascota por el dueño
     *
     * Parámetros de Ruta:
     * - user_id: int (Requerido) - ID del usuario
     * - pet_id: int (Requerido)  -ID de la mascota
     *
     * Respuesta Exitosa:
     *{
     *"success": true,
     *"data": [
     *{
     * "id": 1,
     * "pet_id": 2,
     * "vacuna_id": null,
     * "antidesparasitante_id": null,
     * "antigarrapata_id": 1,
     * "veterinarian_id": 28,
     * "medical_conditions": "Demo",
     * "test_results": null,
     * "vet_visits": 5,
     * "created_at": "2024-11-07T15:18:23.000000Z",
     * "updated_at": "2024-11-07T15:18:23.000000Z",
     *}
     *  ]
     * }
     * Respuesta de Error:
     * {
     *      "success": false
     *     "message": "Error message",
     *
     * }
     */
    Route::get('pet-clinical-history-for-owner', [PetHistoryController::class, 'petClinicalHistoryForOwner']);
    /**
     * Historial de mascota por id
     *
     * Método HTTP: GET
     * Ruta: /api/medical-history-per-pet
     * Descripción: Historial de mascota por veterinario
     *
     * Parámetros de Ruta:
     * - pet_id: int (Requerido) - ID de la mascota
     * -search: string (opcional) - parametro de busqueda
     *
     * Respuesta Exitosa:
     *{
     *"success": true,
     *"data": [
     *{
     * "id": 1,
     * "pet_id": 2,
     * "vacuna_id": null,
     * "antidesparasitante_id": null,
     * "antigarrapata_id": 1,
     * "veterinarian_id": 28,
     * "medical_conditions": "Demo",
     * "test_results": null,
     * "vet_visits": 5,
     * "created_at": "2024-11-07T15:18:23.000000Z",
     * "updated_at": "2024-11-07T15:18:23.000000Z",
     *}
     *  ]
     * }
     * Respuesta de Error:
     * {
     *      "success": false
     *     "message": "Error message",
     *
     * }
     */
    Route::get('medical-history-per-pet', [PetHistoryController::class, 'medicalHistoryPerPet']);
    route::middleware(['check_vet'])->group(function () {
        /**
         * Crear historial clinico de la mascota
         * Método HTTP: POST
         * Ruta: /api/pet-histories
         * Descripción: Crear el historial clinico de la mascota
         * Parametros:
         * - pet_id: int (Requerido) - ID de la mascota
         * report_type: int (Requerido) -Tipo de reporte (1.Vacuna, 2.Antiparasitante, 3.Antigarrapata)
         * veterinarian_id: int (Requerido) - ID del veterinario
         * application_date: date (opcional) - fecha de la solicitud
         * medical_conditions: string (opcional) - Condicion medica
         * test_results: string (opcional) - Resultado de los examenes
         * vet_visits: int (opcional) - numero de visitas del veterinario
         * category: int (opcional) - ID de la categoria
         * report_name: string (Requerido) - Nombre del Historial
         * file: string (opcional) - Archivo subido
         * image: string (opcional) - Imagen subida
         * name: string (Requerido) - Nombre del tipo de reporte(Si es vacuna, antiparasitante o antigarrapata)
         * fecha_aplicacion: date (Requerido) - Fecha aplicada del punto anterior.
         * fecha_refuerzo: date (Requerido) - Fecha del refuero (vacuna, antiparasitante o antigarrapata)
         * weight: string (opcional) - Peso de la mascota
         * notes: string (opcional) - Notas adicionales
         *
         * Respuesta Exitosa:
         * {
         *  'success' => true,
         *  'data' => $history
         * }
         *
         * Respuesta fallida:
         * {
         *  'success' => false,
         *  'message' => 'Mensaje de error'
         * }
         */
        Route::apiResource('/pet-histories', PetHistoryController::class);

        /**
         * Historial de mascota por veterinario
         *
         * Método HTTP: GET
         * Ruta: /api/pet-history-list-by-veterinarian/{id}
         * Descripción: Historial de mascota por veterinario
         *
         * Parámetros de Ruta:
         * - id: int (Requerido) - ID del usuario con rol veterinario
         *
         * Respuesta Exitosa:
         *{
         *"success": true,
         *"data": [
         *{
         * "id": 1,
         * "pet_id": 2,
         * "vacuna_id": null,
         * "antidesparasitante_id": null,
         * "antigarrapata_id": 1,
         * "veterinarian_id": 28,
         * "medical_conditions": "Demo",
         * "test_results": null,
         * "vet_visits": 5,
         * "created_at": "2024-11-07T15:18:23.000000Z",
         * "updated_at": "2024-11-07T15:18:23.000000Z",
         *}
         *  ]
         * }
         * Respuesta de Error:
         * {
         *      "success": false
         *     "message": "Error message",
         *
         * }
         */
        Route::get('pet-history-list-by-veterinarian/{id}', [VeterinaryController::class, 'petHistoryListByVeterinarian']);

        /**
         * Información de contacto del dueño de la mascota
         *
         * Método HTTP: GET
         * Ruta: /api/pet-owner-information
         * Descripción: Información de contacto del dueño de la mascota
         *
         * Parámetros de Ruta:
         * - pet_id: int (Requerido) - ID de la mascota
         * - veterinarian_id: int (Requerido) - ID del veterinario
         *
         * Respuesta Exitosa:
         *{
         *"success": true,
         * "data": {
         * "user_info": {
         * "id": 3,
         * "first_name": "Robert",
         * "last_name": "Martin",
         * "email": "robert@gmail.com",
         * "mobile": "1-7485961545",
         * "full_name": "Robert Martin",
         * "profile_image": "http://localhost/balance/storage/4/Md9nTd4DwKavmjnYPICwtabl74Pnype06ZVUmaYB.png",
         * }
         *}
         *
         * Respuesta de Error:
         * {
         *      "success": false
         *     "message": "Error message",
         *
         * }
         */
        Route::get('pet-owner-information', [VeterinaryController::class, 'petOwnerInformation']);
    });
    Route::apiResource('/vacunas', VacunaController::class);
    /**
     * Listado de vacunas que tiene una mascota
     *
     * Método HTTP: GET
     * Ruta: /api/vaccines-given-to-pet
     * Descripción: Listado de vacunas que tiene una mascota
     *
     * Parámetros de Ruta:
     * - pet_id: int (Requerido) - ID de la mascota
     *
     * Respuesta Exitosa:
     *{
     *"success": true,
     *"data": [
     * {
     *  "id": 2,
     *  "pet_id": 1,
     *  "vacuna_name": "Antirabica",
     *  "fecha_aplicacion": "2024-11-07",
     *  "fecha_refuerzo_vacuna": "2024-12-07",
     *  "created_at": "2024-11-06T18:44:35.000000Z",
     *  "updated_at": "2024-11-06T18:44:35.000000Z",
     *  "pet": {
     *    "id": 1,
     *    "name": "Beau",
     *    "slug": "beau",
     *    "pettype_id": 1,
     *    "breed_id": 1,
     *    "size": null,
     *    "date_of_birth": null,
     *    "age": "13 year",
     *    "gender": "male",
     *    "weight": 31,
     *    "height": 60,
     *    "weight_unit": "kg",
     *    "height_unit": "cm",
     *    "user_id": 2,
     *    "additional_info": null,
     *    "status": 1,
     *    "created_by": null,
     *    "updated_by": null,
     *    "deleted_by": null,
     *    "created_at": "2024-08-14T05:52:08.000000Z",
     *    "updated_at": "2024-08-14T05:52:08.000000Z",
     *    "deleted_at": null,
     *    "qr_code": "images/qr_codes/qr_code_1723614728.png",
     *    "pet_image": "http://localhost/balance/storage/170/lqxRTGxYmMyuLBXh7MOUSf9jeHLyKnR5BbSUHPkX.png",
     *   }
     *  }
     * ]
     *}
     *
     * Respuesta de Error:
     * {
     *      "success": false
     *     "message": "Error message",
     *
     * }
     */
    Route::get('vaccines-given-to-pet', [VacunaController::class, 'vaccinesGivenToPet']);
    Route::apiResource('/anti-wormers', AntiWormersController::class);
    /**
     * Listado de antigarrapatas que tiene una mascota
     *
     * Método HTTP: GET
     * Ruta: /api/anti-wormers-given-pet
     * Descripción: Listado de antigarrapatas que tiene una mascota
     *
     * Parámetros de Ruta:
     * - pet_id: int (Requerido) - ID de la mascota
     *
     * Respuesta Exitosa:
     * {
     *  "success": true,
     *  "data": [
     *    {
     *      "id": 1,
     *      "pet_id": 1,
     *      "antigarrapata_name": "Antigarrapatas",
     *      "fecha_aplicacion": "2024-11-07",
     *      "fecha_refuerzo_antigarrapata": "2024-12-07",
     *      "created_at": "2024-11-06T20:59:13.000000Z",
     *      "updated_at": "2024-11-06T20:59:13.000000Z",
     *      "pet": {
     *        "id": 1,
     *        "name": "Beau",
     *        "slug": "beau",
     *        "pettype_id": 1,
     *        "breed_id": 1,
     *        "size": null,
     *        "date_of_birth": null,
     *        "age": "13 year",
     *        "gender": "male",
     *        "weight": 31,
     *        "height": 60,
     *        "weight_unit": "kg",
     *        "height_unit": "cm",
     *        "user_id": 2,
     *        "additional_info": null,
     *        "status": 1,
     *        "created_by": null,
     *        "updated_by": null,
     *        "deleted_by": null,
     *        "created_at": "2024-08-14T05:52:08.000000Z",
     *        "updated_at": "2024-08-14T05:52:08.000000Z",
     *        "deleted_at": null,
     *        "qr_code": "images/qr_codes/qr_code_1723614728.png",
     *        "pet_image": "http://localhost/balance/storage/170/lqxRTGxYmMyuLBXh7MOUSf9jeHLyKnR5BbSUHPkX.png",
     *      }
     *    }
     *  ]
     *}
     *
     * Respuesta de Error:
     * {
     *      "success": false
     *     "message": "Error message",
     *
     * }
     */
    Route::get('/anti-wormers-given-pet', [AntiWormersController::class, 'antiWormersGivenToPet']);
    Route::apiResource('/anti-ticks', AntiTickController::class);
    /**
     * Listado de antidesparasitante que tiene una mascota
     *
     * Método HTTP: GET
     * Ruta: /api/anti-tick-given-pet
     * Descripción: Listado de antidesparasitante que tiene una mascota
     *
     * Parámetros de Ruta:
     * - pet_id: int (Requerido) - ID de la mascota
     *
     * Respuesta Exitosa:
     * {
     *  "success": true,
     *  "data": [
     *    {
     *      "id": 1,
     *      "pet_id": 1,
     *      "antidesparasitante_name": "Antidesparasitante",
     *      "fecha_aplicacion": "2024-11-07",
     *      "fecha_refuerzo_antidesparasitante": "2024-12-07",
     *      "created_at": "2024-11-06T20:59:13.000000Z",
     *      "updated_at": "2024-11-06T20:59:13.000000Z",
     *      "pet": {
     *        "id": 1,
     *        "name": "Beau",
     *        "slug": "beau",
     *        "pettype_id": 1,
     *        "breed_id": 1,
     *        "size": null,
     *        "date_of_birth": null,
     *        "age": "13 year",
     *        "gender": "male",
     *        "weight": 31,
     *        "height": 60,
     *        "weight_unit": "kg",
     *        "height_unit": "cm",
     *        "user_id": 2,
     *        "additional_info": null,
     *        "status": 1,
     *        "created_by": null,
     *        "updated_by": null,
     *        "deleted_by": null,
     *        "created_at": "2024-08-14T05:52:08.000000Z",
     *        "updated_at": "2024-08-14T05:52:08.000000Z",
     *        "deleted_at": null,
     *        "qr_code": "images/qr_codes/qr_code_1723614728.png",
     *        "pet_image": "http://localhost/balance/storage/170/lqxRTGxYmMyuLBXh7MOUSf9jeHLyKnR5BbSUHPkX.png",
     *      }
     *    }
     *  ]
     *}
     *
     * Respuesta de Error:
     * {
     *      "success": false
     *     "message": "Error message",
     *
     * }
     */
    Route::get('/anti-tick-given-pet', [AntiTickController::class, 'antiTickGivenToPet']);

    /**
     * Acceder a la lista de mascotas asignadas para organizar sesiones de entrenamiento.
     *
     * Método HTTP: GET
     * Ruta: /api/get-pets-assigned-to-the-trainer
     * Descripción: Acceder a la lista de mascotas asignadas para organizar sesiones de entrenamiento.
     *
     * Parámetros de Ruta:
     * - user_id: int (Requerido) - ID del entrenador
     *
     * Respuesta Exitosa:
     * {
     *     "data": [
     *       {
     *       "name": "Daisy",
     *       "breed": "Bulldog",
     *       "age": "9 year",
     *       "status": 1,
     *       "pet_image": "http://localhost/balance-dog-admin-panel/public/img/default.png",
     *       "bookings": [],
     *       "media": []
     *       },
     *         // Más mascostas...
     *     ],
     *     "messages": "success"
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error message",
     *     "success": false
     * }
     */

    Route::get('/get-pets-assigned-to-the-trainer', [TrainerController::class, 'getPetsAssignedToTheTrainer']);
    /**
     * Obtener la lista de todos los chips.
     *
     * Método HTTP: GET
     * Ruta: /api/chips
     * Descripción: Devuelve una lista de todos los chips disponibles en la base de datos, incluyendo los detalles de la mascota y el fabricante asociados.
     *
     * Respuesta Exitosa:
     * {
     *     "data": [
     *         {
     *             "id": int, // ID del chip
     *             "num_identificacion": int, // Número de identificación del chip
     *             "pet_id": int, // ID de la mascota asociada
     *             "fecha_implantacion": string, // Fecha de implantación del chip
     *             "fabricante_id": int, // ID del fabricante del chip
     *             "num_contacto": string, // Número de contacto asociado al chip
     *             "pet": { // Detalles de la mascota
     *                 "id": int,
     *                 "name": string,
     *                 // Otros detalles de la mascota...
     *             },
     *             "fabricante": { // Detalles del fabricante
     *                 "id": int,
     *                 "nombre": string,
     *                 // Otros detalles del fabricante...
     *             }
     *         },
     *         // Más objetos de chip...
     *     ],
     *     "message": "Lista de todos los chips",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error message",
     *     "success": false
     * }
     */
    Route::get('/chips', [ChipsController::class, 'index']);

    /**
     * Obtener los detalles de un chip específico.
     *
     * Método HTTP: GET
     * Ruta: /api/chips/{id}
     * Descripción: Devuelve los detalles de un chip específico identificado por su ID.
     *
     * Parámetros de Ruta:
     * - id: int (Requerido) - ID del chip que se desea consultar.
     *
     * Respuesta Exitosa:
     * {
     *     "data": {
     *         "id": int, // ID del chip
     *         "num_identificacion": int, // Número de identificación del chip
     *         "pet_id": int, // ID de la mascota asociada
     *         "fecha_implantacion": string, // Fecha de implantación del chip
     *         "fabricante_id": int, // ID del fabricante del chip
     *         "num_contacto": string, // Número de contacto asociado al chip
     *         "pet": { // Detalles de la mascota
     *             "id": int,
     *             "name": string,
     *             // Otros detalles de la mascota...
     *         },
     *         "fabricante": { // Detalles del fabricante
     *             "id": int,
     *             "nombre": string,
     *             // Otros detalles del fabricante...
     *         }
     *     },
     *     "message": "Detalles del chip",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Chip no encontrado",
     *     "success": false
     * }
     */
    Route::get('/chips/{id}', [ChipsController::class, 'show']);

    /**
     * Crear un nuevo chip.
     *
     * Método HTTP: POST
     * Ruta: /api/chips
     * Descripción: Permite crear un nuevo chip en la base de datos.
     *
     * Parámetros de Solicitud (Body):
     * - num_identificacion: int (Requerido) - Número de identificación único para el chip.
     * - pet_id: int (Requerido) - ID de la mascota asociada al chip.
     * - fecha_implantacion: date (Requerido) - Fecha de implantación del chip.
     * - fabricante_id: int (Requerido) - ID del fabricante del chip.
     * - num_contacto: string (Requerido) - Número de contacto asociado al chip.
     *
     * Respuesta Exitosa:
     * {
     *     "data": {
     *         "id": int, // ID del chip recién creado
     *         "num_identificacion": int, // Número de identificación del chip
     *         "pet_id": int, // ID de la mascota asociada
     *         "fecha_implantacion": string, // Fecha de implantación del chip
     *         "fabricante_id": int, // ID del fabricante del chip
     *         "num_contacto": string, // Número de contacto asociado al chip
     *     },
     *     "message": "Chip creado exitosamente",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error al crear el chip",
     *     "success": false
     * }
     */
    Route::post('/chips', [ChipsController::class, 'store']);

    /**
     * Actualizar un chip existente.
     *
     * Método HTTP: PUT
     * Ruta: /api/chips/{id}
     * Descripción: Permite actualizar los detalles de un chip específico identificado por su ID.
     *
     * Parámetros de Ruta:
     * - id: int (Requerido) - ID del chip que se desea actualizar.
     *
     * Parámetros de Solicitud (Body):
     * - num_identificacion: int (Requerido) - Número de identificación único para el chip.
     * - fecha_implantacion: date (Requerido) - Fecha de implantación del chip.
     * - fabricante_id: int (Requerido) - ID del fabricante del chip.
     * - num_contacto: string (Requerido) - Número de contacto asociado al chip.
     *
     * Respuesta Exitosa:
     * {
     *     "data": {
     *         "id": int, // ID del chip actualizado
     *         "num_identificacion": int, // Número de identificación del chip
     *         "pet_id": int, // ID de la mascota asociada
     *         "fecha_implantacion": string, // Fecha de implantación del chip
     *         "fabricante_id": int, // ID del fabricante del chip
     *         "num_contacto": string, // Número de contacto asociado al chip
     *     },
     *     "message": "Chip actualizado exitosamente",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error al actualizar el chip",
     *     "success": false
     * }
     */
    Route::put('/chips/{id}', [ChipsController::class, 'update']);

    /**
     * Eliminar un chip.
     *
     * Método HTTP: DELETE
     * Ruta: /api/chips/{id}
     * Descripción: Permite eliminar un chip específico identificado por su ID.
     *
     * Parámetros de Ruta:
     * - id: int (Requerido) - ID del chip que se desea eliminar.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Chip eliminado exitosamente",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error al eliminar el chip",
     *     "success": false
     * }
     */
    Route::delete('/chips/{id}', [ChipsController::class, 'destroy']);

    /**
     * Obtener el chip asociado a una mascota específica.
     *
     * Método HTTP: GET
     * Ruta: /api/pets/{pet_id}/chip
     * Descripción: Devuelve el chip asociado a la mascota identificada por su `pet_id`.
     *
     * Parámetros de Ruta:
     * - pet_id: int (Requerido) - ID de la mascota para la cual se desea obtener el chip.
     *
     * Respuesta Exitosa:
     * {
     *     "data": {
     *         "id": int, // ID del chip
     *         "num_identificacion": int, // Número de identificación del chip
     *         "pet_id": int, // ID de la mascota asociada
     *         "fecha_implantacion": string, // Fecha de implantación del chip
     *         "fabricante_id": int, // ID del fabricante del chip
     *         "num_contacto": string, // Número de contacto asociado al chip
     *         "pet": { // Detalles de la mascota
     *             "id": int,
     *             "name": string,
     *             // Otros detalles de la mascota...
     *         },
     *         "fabricante": { // Detalles del fabricante
     *             "id": int,
     *             "nombre": string,
     *             // Otros detalles del fabricante...
     *         }
     *     },
     *     "message": "Detalles del chip asociado a la mascota",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Chip no encontrado para la mascota especificada",
     *     "success": false
     * }
     */
    Route::get('/pets/{pet_id}/chip', [ChipsController::class, 'getChipByPet']);

    /**
     * Obtener los niveles de actividad de una mascota específica.
     *
     * Método HTTP: GET
     * Ruta: /api/pets/{pet_id}/activity-levels
     * Descripción: Devuelve todos los registros de niveles de actividad asociados a la mascota identificada por su `pet_id`.
     *
     * Parámetros de Ruta:
     * - pet_id: int (Requerido) - ID de la mascota para la cual se desean obtener los niveles de actividad.
     *
     * Respuesta Exitosa:
     * {
     *     "data": [
     *         {
     *             "id": int, // ID del nivel de actividad
     *             "pet_id": int, // ID de la mascota
     *             "daily_steps": int, // Número de pasos diarios
     *             "distance_covered": float, // Distancia recorrida en kilómetros
     *             "calories_burned": int, // Calorías quemadas
     *             "active_minutes": int, // Minutos de actividad física
     *             "goal_steps": int, // Meta de pasos diarios
     *             "goal_distance": float, // Meta de distancia recorrida en kilómetros
     *             "goal_calories": int, // Meta de calorías quemadas
     *             "goal_active_minutes": int, // Meta de minutos de actividad física
     *         },
     *         // Más registros de niveles de actividad...
     *     ],
     *     "message": "Lista de niveles de actividad de la mascota",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Niveles de actividad no encontrados para la mascota especificada",
     *     "success": false
     * }
     */
    Route::get('/pets/{pet_id}/activity-levels', [ActivityLevelController::class, 'index']);

    /**
     * Crear un nuevo nivel de actividad para una mascota específica.
     *
     * Método HTTP: POST
     * Ruta: /api/pets/{pet_id}/activity-levels
     * Descripción: Crea un nuevo registro de nivel de actividad para la mascota identificada por su `pet_id`.
     *
     * Parámetros de Ruta:
     * - pet_id: int (Requerido) - ID de la mascota para la cual se va a crear el nivel de actividad.
     *
     * Parámetros del Cuerpo (Body):
     * - daily_steps: int (Opcional) - Número de pasos diarios.
     * - distance_covered: float (Opcional) - Distancia recorrida en kilómetros.
     * - calories_burned: int (Opcional) - Calorías quemadas.
     * - active_minutes: int (Opcional) - Minutos de actividad física.
     * - goal_steps: int (Opcional) - Meta de pasos diarios.
     * - goal_distance: float (Opcional) - Meta de distancia recorrida en kilómetros.
     * - goal_calories: int (Opcional) - Meta de calorías quemadas.
     * - goal_active_minutes: int (Opcional) - Meta de minutos de actividad física.
     *
     * Respuesta Exitosa:
     * {
     *     "data": {
     *         "id": int, // ID del nivel de actividad creado
     *         "pet_id": int, // ID de la mascota
     *         "daily_steps": int, // Número de pasos diarios
     *         "distance_covered": float, // Distancia recorrida en kilómetros
     *         "calories_burned": int, // Calorías quemadas
     *         "active_minutes": int, // Minutos de actividad física
     *         "goal_steps": int, // Meta de pasos diarios
     *         "goal_distance": float, // Meta de distancia recorrida en kilómetros
     *         "goal_calories": int, // Meta de calorías quemadas
     *         "goal_active_minutes": int, // Meta de minutos de actividad física
     *     },
     *     "message": "Nivel de actividad creado exitosamente",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error al crear el nivel de actividad",
     *     "success": false
     * }
     */
    Route::post('/pets/{pet_id}/activity-levels', [ActivityLevelController::class, 'store']);

    /**
     * Actualizar un nivel de actividad existente para una mascota específica.
     *
     * Método HTTP: PUT
     * Ruta: /api/activity-levels/{id}
     * Descripción: Actualiza un registro de nivel de actividad para la mascota identificada por su `id`.
     *
     * Parámetros de Ruta:
     * - id: int (Requerido) - ID del nivel de actividad que se va a actualizar.
     *
     * Parámetros del Cuerpo (Body):
     * - daily_steps: int (Opcional) - Número de pasos diarios.
     * - distance_covered: float (Opcional) - Distancia recorrida en kilómetros.
     * - calories_burned: int (Opcional) - Calorías quemadas.
     * - active_minutes: int (Opcional) - Minutos de actividad física.
     * - goal_steps: int (Opcional) - Meta de pasos diarios.
     * - goal_distance: float (Opcional) - Meta de distancia recorrida en kilómetros.
     * - goal_calories: int (Opcional) - Meta de calorías quemadas.
     * - goal_active_minutes: int (Opcional) - Meta de minutos de actividad física.
     *
     * Respuesta Exitosa:
     * {
     *     "data": {
     *         "id": int, // ID del nivel de actividad actualizado
     *         "pet_id": int, // ID de la mascota
     *         "daily_steps": int, // Número de pasos diarios
     *         "distance_covered": float, // Distancia recorrida en kilómetros
     *         "calories_burned": int, // Calorías quemadas
     *         "active_minutes": int, // Minutos de actividad física
     *         "goal_steps": int, // Meta de pasos diarios
     *         "goal_distance": float, // Meta de distancia recorrida en kilómetros
     *         "goal_calories": int, // Meta de calorías quemadas
     *         "goal_active_minutes": int, // Meta de minutos de actividad física
     *     },
     *     "message": "Nivel de actividad actualizado exitosamente",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error al actualizar el nivel de actividad",
     *     "success": false
     * }
     */
    Route::put('/activity-levels/{id}', [ActivityLevelController::class, 'update']);

    /**
     * Eliminar un nivel de actividad para una mascota específica.
     *
     * Método HTTP: DELETE
     * Ruta: /api/activity-levels/{id}
     * Descripción: Elimina un registro de nivel de actividad identificado por su `id`.
     *
     * Parámetros de Ruta:
     * - id: int (Requerido) - ID del nivel de actividad que se va a eliminar.
     *
     * Respuesta Exitosa:
     * {
     *     "message": "Nivel de actividad eliminado exitosamente",
     *     "success": true
     * }
     *
     * Respuesta de Error:
     * {
     *     "message": "Error al eliminar el nivel de actividad",
     *     "success": false
     * }
     */
    Route::delete('/activity-levels/{id}', [ActivityLevelController::class, 'destroy']);
});
Route::get('app-configuration', [SettingController::class, 'appConfiguraton']);
