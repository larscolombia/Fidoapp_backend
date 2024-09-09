<?php

use App\Http\Controllers\ActivityLevelController;
use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\BackupController;
use App\Http\Controllers\Backend\BranchController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\NotificationsController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\ChipsController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\ComandoController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoursePlatformConroller;
use App\Http\Controllers\CursoPlataformaController;
use App\Http\Controllers\DiarioController;
use App\Http\Controllers\EBookController;
use App\Http\Controllers\EjercicioController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\HerramientaController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermission;
use App\Http\Controllers\SaludController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VacunaController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\Backend\TrainingController;
use Modules\Pet\Http\Controllers\Backend\PetsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Auth Routes
require __DIR__.'/auth.php';
Route::get('/', function () {
    if (auth()->user()->hasRole('boarder')) {
        return redirect(RouteServiceProvider::BOARDER_LOGIN_REDIRECT);
    }else if(auth()->user()->hasRole('vet')){

        return redirect(RouteServiceProvider::VET_LOGIN_REDIRECT);
  
    }else if(auth()->user()->hasRole('groomer')){

        return redirect(RouteServiceProvider::GROOMER_LOGIN_REDIRECT);
  
    }else if(auth()->user()->hasRole('trainer')){

        return redirect(RouteServiceProvider::TRAINER_LOGIN_REDIRECT);
  
    }else if(auth()->user()->hasRole('walker')){

        return redirect(RouteServiceProvider::WALKER_LOGIN_REDIRECT);
  
    }else if(auth()->user()->hasRole('day_taker')){

        return redirect(RouteServiceProvider::DAYTAKER_LOGIN_REDIRECT);
  
    }else if(auth()->user()->hasRole('pet_sitter')){
        return redirect(RouteServiceProvider::PETSITTER_LOGIN_REDIRECT);
  
    } else {
        return redirect(RouteServiceProvider::HOME);
    }
})->middleware('auth');

Route::group(['middleware' => ['auth']], function () {
    Route::get('notification-list', [NotificationsController::class, 'notificationList'])->name('notification.list');
    Route::get('notification-counts', [NotificationsController::class, 'notificationCounts'])->name('notification.counts');
    Route::delete('notification-remove/{id}', [NotificationsController::class, 'notificationRemove'])->name('notification.remove');
});

Route::group(['prefix' => 'app'], function () {

    // Language Switch
    Route::get('language/{language}', [LanguageController::class, 'switch'])->name('language.switch');
    Route::post('set-user-setting', [BackendController::class, 'setUserSetting'])->name('backend.setUserSetting');

    Route::group(['as' => 'backend.', 'middleware' => ['auth']], function () {
        Route::post('update-player-id', [UserController::class, 'update_player_id'])->name('update-player-id');
        Route::get('get_search_data', [SearchController::class, 'get_search_data'])->name('get_search_data');

        // Sync Role & Permission
        Route::group(['middleware' => 'permission:view_permission'], function () {
        Route::get('/permission-role', [RolePermission::class, 'index'])->name('permission-role.list')->middleware('password.confirm');
        Route::post('/permission-role/store/{role_id}', [RolePermission::class, 'store'])->name('permission-role.store');
        Route::get('/permission-role/reset/{role_id}', [RolePermission::class, 'reset_permission'])->name('permission-role.reset');
        // Role & Permissions Crud
        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);

    });


    Route::group(['middleware' => 'permission:view_modules'], function () {
        Route::group(['prefix' => 'module', 'as' => 'module.'], function () {

            Route::get('index_data', [ModuleController::class, 'index_data'])->name('index_data');
            Route::post('update-status/{id}', [ModuleController::class, 'update_status'])->name('update_status');
        });

       Route::resource('module', ModuleController::class);

    });

   


        /*
          *
          *  Settings Routes
          *
          * ---------------------------------------------------------------------
          */
        Route::group(['middleware' => ['permission:edit_settings']], function () {
            Route::get('settings/{vue_capture?}', [SettingController::class, 'index'])->name('settings')->where('vue_capture', '^(?!storage).*$');
            Route::get('settings-data', [SettingController::class, 'index_data']);
            Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
            Route::post('setting-update', [SettingController::class, 'update'])->name('setting.update');
            Route::get('clear-cache', [SettingController::class, 'clear_cache'])->name('clear-cache');
            Route::get('reload-database', [SettingController::class, 'reload_database'])->name('reload-database');
            Route::post('verify-email', [SettingController::class, 'verify_email'])->name('verify-email');
            Route::get('get-service-price', [SettingController::class, 'get_service_price']);

            
        });

        /*
        *
        *  Notification Routes
        *
        * ---------------------------------------------------------------------
        */
        Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
            Route::get('/', [NotificationsController::class, 'index'])->name('index');
            Route::get('/markAllAsRead', [NotificationsController::class, 'markAllAsRead'])->name('markAllAsRead');
            Route::delete('/deleteAll', [NotificationsController::class, 'deleteAll'])->name('deleteAll');
            Route::get('/{id}', [NotificationsController::class, 'show'])->name('show');

        });

        /*
        *
        *  Backup Routes
        *
        * ---------------------------------------------------------------------
        */
        Route::group(['prefix' => 'backups', 'as' => 'backups.'], function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::get('/create', [BackupController::class, 'create'])->name('create');
            Route::get('/download/{file_name}', [BackupController::class, 'download'])->name('download');
            Route::get('/delete/{file_name}', [BackupController::class, 'delete'])->name('delete');
        });

        Route::group(['middleware' => 'permission:view_daily_bookings'], function () {

            Route::get('daily-booking-report', [ReportsController::class, 'daily_booking_report'])->name('reports.daily-booking-report');
            Route::get('daily-booking-report-index-data', [ReportsController::class, 'daily_booking_report_index_data'])->name('reports.daily-booking-report.index_data');
        });
        Route::group(['middleware' => 'permission:view_overall_bookings'], function () {
            Route::get('overall-booking-report', [ReportsController::class, 'overall_booking_report'])->name('reports.overall-booking-report');
            Route::get('overall-booking-report-index-data', [ReportsController::class, 'overall_booking_report_index_data'])->name('reports.overall-booking-report.index_data');
        });
        Route::group(['middleware' => 'permission:view_reports'], function () {
            Route::get('payout-report', [ReportsController::class, 'payout_report'])->name('reports.payout-report');
            Route::get('payout-report-index-data', [ReportsController::class, 'payout_report_index_data'])->name('reports.payout-report.index_data');
        });
        Route::group(['middleware' => 'permission:view_reports'], function () {
            Route::get('staff-report', [ReportsController::class, 'staff_report'])->name('reports.staff-report');
            Route::get('staff-report-index-data', [ReportsController::class, 'staff_report_index_data'])->name('reports.staff-report.index_data');
        });
        Route::group(['middleware' => 'permission:view_order_reports'], function () {
            Route::get('order-report', [ReportsController::class, 'order_report'])->name('reports.order-report');
            Route::get('order-report-index-data', [ReportsController::class, 'order_report_index_data'])->name('reports.order-report.index_data');
        });
    });

  

    /*
    *
    * Backend Routes
    * These routes need view-backend permission
    * --------------------------------------------------------------------
    */
    Route::group(['as' => 'backend.', 'middleware' => ['auth']], function () {

        /**
         * Backend Dashboard
         * Namespaces indicate folder structure.
         */
        Route::get('/', [BackendController::class, 'index'])->name('home');

        Route::get('/get_revnue_chart_data/{type}', [BackendController::class, 'getRevenuechartData']);
        Route::get('/get_booking_chart_data/{type}', [BackendController::class, 'getBookingchartData']);
        Route::get('/get_booking_status_chart_data/{type}', [BackendController::class, 'getStatusBookingchartData']);
        Route::get('/get_profit_chart_data/{type}', [BackendController::class, 'getProfitchartData']);
        

        Route::post('set-current-branch/{branch_id}', [BackendController::class, 'setCurrentBranch'])->name('set-current-branch');
        Route::post('reset-branch', [BackendController::class, 'resetBranch'])->name('reset-branch');

        Route::group(['prefix' => ''], function () {
            Route::get('dashboard', [BackendController::class, 'index'])->name('dashboard');

            /**
             * Branch Routes
             */

             Route::group(['middleware' => ['permission:view_branch']], function () {
            Route::group(['prefix' => 'branch', 'as' => 'branch.'], function () {
                Route::get('index_list', [BranchController::class, 'index_list'])->name('index_list');
                Route::get('assign/{id}', [BranchController::class, 'assign_list'])->name('assign_list');
                Route::post('assign/{id}', [BranchController::class, 'assign_update'])->name('assign_update');
                Route::get('index_data', [BranchController::class, 'index_data'])->name('index_data');
                Route::get('trashed', [BranchController::class, 'trashed'])->name('trashed');
                Route::patch('trashed/{id}', [BranchController::class, 'restore'])->name('restore');
                // Branch Gallery Images
                Route::get('gallery-images/{id}', [BranchController::class, 'getGalleryImages']);
                Route::post('gallery-images/{id}', [BranchController::class, 'uploadGalleryImages']);
                Route::post('bulk-action', [BranchController::class, 'bulk_action'])->name('bulk_action');
                Route::post('update-status/{id}', [BranchController::class, 'update_status'])->name('update_status');
                Route::post('update-select-value/{id}/{action_type}', [BranchController::class, 'update_select'])->name('update_select');
            });
            Route::resource('branch', BranchController::class);

        });

            /*
            *
            *  Users Routes
            *
            * ---------------------------------------------------------------------
            */
            Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                
                Route::get('/user-list', [UserController::class, 'user_list'])->name('user_list');
                Route::get('/profile/{id}', [UserController::class, 'profile'])->name('profile');
                Route::get('/profile/{id}/edit', [UserController::class, 'profileEdit'])->name('profileEdit');
                Route::patch('/profile/{id}/edit', [UserController::class, 'profileUpdate'])->name('profileUpdate');
                Route::get('/emailConfirmationResend/{id}', [UserController::class, 'emailConfirmationResend'])->name('emailConfirmationResend');
                Route::delete('/userProviderDestroy', [UserController::class, 'userProviderDestroy'])->name('userProviderDestroy');
                Route::get('/profile/changeProfilePassword/{id}', [UserController::class, 'changeProfilePassword'])->name('changeProfilePassword');
                Route::patch('/profile/changeProfilePassword/{id}', [UserController::class, 'changeProfilePasswordUpdate'])->name('changeProfilePasswordUpdate');
                Route::get('/changePassword/{id}', [UserController::class, 'changePassword'])->name('changePassword');
                Route::patch('/changePassword/{id}', [UserController::class, 'changePasswordUpdate'])->name('changePasswordUpdate');
                Route::get('/trashed', [UserController::class, 'trashed'])->name('trashed');
                Route::patch('/trashed/{id}', [UserController::class, 'restore'])->name('restore');
                Route::get('customer', [CustomerController::class, 'index'])->name('customer');
                Route::get('/index_data/{role}', [UserController::class, 'index_data'])->name('index_data');
                Route::get('/index_list', [UserController::class, 'index_list'])->name('index_list');
                Route::get('/owner_list', [UserController::class, 'owner_list'])->name('owner_list');
                Route::get('/organizer_list', [UserController::class, 'organizer_list'])->name('organizer_list');
                Route::post('/create-customer', [UserController::class, 'create_customer'])->name('create_customer');
                Route::patch('/{id}/block', [UserController::class, 'block', 'middleware' => ['permission:block_users']])->name('block');
                Route::patch('/{id}/unblock', [UserController::class, 'unblock', 'middleware' => ['permission:block_users']])->name('unblock');
                Route::post('information', [UserController::class, 'updateData'])->name('information');

                Route::post('change-password', [UserController::class, 'change_password'])->name('change_password');
            });
            Route::resource('users', UserController::class);
        });

        Route::get('my-profile/{vue_capture?}', [UserController::class, 'myProfile'])->name('my-profile')->where('vue_capture', '^(?!storage).*$');
        Route::get('my-info', [UserController::class, 'authData'])->name('authData');

        Route::resource('e-books', EBookController::class);
        Route::get('e-books-index-data', [EBookController::class, 'index_data'])->name('ebooks.index_data');
        Route::resource('courses', CourseController::class);
        Route::get('courses-index-data', [CourseController::class, 'index_data'])->name('courses.index_data');
        Route::get('/curso-plataforma/data', [CursoPlataformaController::class, 'index_data'])->name('course_platform.index_data');
        Route::get('curso-plataforma/{course}/clases/data', [ClaseController::class, 'index_data'])->name('course_platform.clases.index_data');
        Route::get('clases/{clase}ejercicios/data', [EjercicioController::class, 'index_data'])->name('clases.ejercicios.index_data');


        Route::resource('comandos', ComandoController::class);
        Route::get('comandos-index-data', [ComandoController::class, 'index_data'])->name('comandos.index_data');
        Route::post('/categories/comandos', [ComandoController::class, 'storeCategory'])->name('categories_comando.store');
        Route::post('backend/comandos/toggle_favorite', [ComandoController::class, 'toggleFavorite'])->name('comandos.toggle_favorite');

        Route::get('herramientas_entrenamiento/icon', [HerramientaController::class, 'icon'])->name('herramientas_entrenamiento.icon');
        Route::get('herramientas_entrenamiento/icon/index-data', [HerramientaController::class, 'icon_index_data'])->name('herramientas_entrenamiento.icon_index_data');
        Route::get('herramientas_entrenamiento/icon/edit/{herramientas_entrenamiento_type}', [HerramientaController::class, 'edit_type'])->name('herramientas_entrenamiento_type.edit');
        Route::put('herramientas_entrenamiento/icon/{herramientas_entrenamiento_type}', [HerramientaController::class, 'update_type'])->name('herramientas_entrenamiento_type.update');


        Route::resource('herramientas_entrenamiento', HerramientaController::class);
        Route::get('herramientas-entrenamiento-index-data', [HerramientaController::class, 'index_data'])->name('herramientas_entrenamiento.index_data');

        Route::get('/mascotas/diarios', [DiarioController::class, 'mascotas'])->name('mascotas.diarios');
        Route::get('diarios/mascotas_data', [DiarioController::class, 'mascotas_data'])->name('diarios.mascotas_data');
        Route::prefix('/mascotas/diarios/{pet}')->name('mascotas.diarios.')->group(function () {
            Route::get('/', [DiarioController::class, 'index'])->name('index');
            Route::get('/create', [DiarioController::class, 'create'])->name('create');
            Route::post('/store', [DiarioController::class, 'store'])->name('store');
            Route::get('/show/{diario}', [DiarioController::class, 'show'])->name('show');
            Route::get('/edit/{diario}', [DiarioController::class, 'edit'])->name('edit');
            Route::put('/update/{diario}', [DiarioController::class, 'update'])->name('update');
            Route::delete('/destroy/{diario}', [DiarioController::class, 'destroy'])->name('destroy');

            Route::get('/diarios_data', [DiarioController::class, 'diarios_data'])->name('diarios_data');
        });

        Route::resource('/curso-plataforma', CursoPlataformaController::class)
            ->names([
                'index' => 'course_platform.index',
                'create' => 'course_platform.create',
                'store' => 'course_platform.store',
                'show' => 'course_platform.show',
                'edit' => 'course_platform.edit',
                'update' => 'course_platform.update',
                'destroy' => 'course_platform.destroy',
            ]);

        // Clase routes
        Route::prefix('curso-plataforma/{course}')->name('course_platform.')->group(function() {
            Route::resource('clases', ClaseController::class);
        });

        // Ejercicio routes
        Route::prefix('clases/{clase}')->name('clases.')->group(function() {
            Route::resource('ejercicios', EjercicioController::class);
        });

        Route::get('auth/google', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.redirect');
        Route::get('auth/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback']);
        Route::post('events/google-calendar', [GoogleCalendarController::class, 'createEvent'])->name('google.calendar.create');
    
        Route::get('training-index-data', [TrainingController::class, 'index_data'])->name('training.index_data');

        Route::get('/mascotas/qr_code', [QRCodeController::class, 'mascotas'])->name('mascotas.qr_code');
        Route::get('qr_code/mascotas_data', [QRCodeController::class, 'mascotas_data'])->name('qr_code.mascotas_data');
        Route::get('qr_code/edit/{id}', [QRCodeController::class, 'edit'])->name('qr_code.edit');
        Route::put('qr_code/{id}', [QRCodeController::class, 'update'])->name('qr_code.update');

        Route::put('pet/{id}/shared-owner', [PetsController::class, 'sharedOwner'])->name('pet.shared-owner');
    
        Route::get('/mascotas/chips', [ChipsController::class, 'mascotas'])->name('mascotas.chips');
        Route::get('chips/mascotas_data', [ChipsController::class, 'mascotas_data'])->name('chips.mascotas_data');
        Route::resource('chips', ChipsController::class);
        Route::get('chips/index_data', [ChipsController::class, 'index_data'])->name('chips.index_data');

        Route::post('/fabricantes/store', [FabricanteController::class, 'store'])->name('fabricantes.store');
   
        Route::get('/mascotas/activity_levels', [ActivityLevelController::class, 'mascotas'])->name('mascotas.activity_levels');
        Route::get('/pets/{pet_id}/activity_levels/index_data', [ActivityLevelController::class, 'index_data'])->name('activity_levels.index_data');

        Route::get('activity_levels/mascotas_data', [ActivityLevelController::class, 'mascotas_data'])->name('activity_levels.mascotas_data');
        // Ruta para mostrar los niveles de actividad de una mascota específica
        Route::get('/pets/{pet_id}/activity-levels', [ActivityLevelController::class, 'index'])->name('activity-levels.index');
        
        // Ruta para mostrar el formulario de creación de un nuevo nivel de actividad para una mascota específica
        Route::get('/pets/{pet_id}/activity-levels/create', [ActivityLevelController::class, 'create'])->name('activity-levels.create');
        
        // Ruta para almacenar un nuevo nivel de actividad para una mascota específica
        Route::post('/pets/{pet_id}/activity-levels', [ActivityLevelController::class, 'store'])->name('activity-levels.store');
        
        // Ruta para mostrar el formulario de edición de un nivel de actividad específico
        Route::get('/activity-levels/{id}/edit', [ActivityLevelController::class, 'edit'])->name('activity-levels.edit');
        
        // Ruta para actualizar un nivel de actividad específico
        Route::put('/activity-levels/{id}', [ActivityLevelController::class, 'update'])->name('activity-levels.update');
        
        // Ruta para eliminar un nivel de actividad específico
        Route::delete('/activity-levels/{id}', [ActivityLevelController::class, 'destroy'])->name('activity-levels.destroy');
    
        Route::put('/activity-levels/{id}/update-steps', [ActivityLevelController::class, 'update_steps'])->name('activity-levels.update_steps');
        Route::put('/activity-levels/{id}/update-distance', [ActivityLevelController::class, 'update_distance'])->name('activity-levels.update_distance');
        Route::put('/activity-levels/{id}/update-calories', [ActivityLevelController::class, 'update_calories'])->name('activity-levels.update_calories');
        Route::put('/activity-levels/{id}/update-minutes', [ActivityLevelController::class, 'update_minutes'])->name('activity-levels.update_minutes');

        Route::post('/activity-levels/{pet_id}/store-steps', [ActivityLevelController::class, 'store_steps'])->name('activity-levels.store_steps');
        Route::post('/activity-levels/{pet_id}/store-distance', [ActivityLevelController::class, 'store_distance'])->name('activity-levels.store_distance');
        Route::post('/activity-levels/{pet_id}/store-calories', [ActivityLevelController::class, 'store_calories'])->name('activity-levels.store_calories');
        Route::post('/activity-levels/{pet_id}/store-minutes', [ActivityLevelController::class, 'store_minutes'])->name('activity-levels.store_minutes');
    
        Route::resource('salud', SaludController::class);
        Route::get('/mascotas/salud', [SaludController::class, 'mascotas'])->name('mascotas.salud');

        Route::resource('vacunas', VacunaController::class);
        Route::get('/mascotas/vacunas', [VacunaController::class, 'mascotas'])->name('mascotas.vacunas');
        Route::get('/vacunas/mascotas_data', [VacunaController::class, 'mascotas_data'])->name('vacunas.mascotas_data');
        Route::prefix('/mascotas/vacunas/{pet}')->name('mascotas.vacunas.')->group(function () {
            Route::get('/', [VacunaController::class, 'index'])->name('index');
            Route::get('/create', [VacunaController::class, 'create'])->name('create');
            Route::post('/store', [VacunaController::class, 'store'])->name('store');
            Route::get('/show/{vacuna}', [VacunaController::class, 'show'])->name('show');
            Route::get('/edit/{vacuna}', [VacunaController::class, 'edit'])->name('edit');
            Route::put('/update/{vacuna}', [VacunaController::class, 'update'])->name('update');
            
            Route::get('/vacunas_data', [VacunaController::class, 'vacunas_data'])->name('vacunas_data');
        });    
        Route::delete('/destroy/{vacuna}', [VacunaController::class, 'destroy'])->name('destroy');

        Route::resource('antigarrapatas', VacunaController::class);

        Route::resource('antiparasitantes', VacunaController::class);
    });
});
