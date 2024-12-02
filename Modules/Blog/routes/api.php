<?php
use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\Backend\API\BlogController;

Route::get('blog-list', [BlogController::class, 'blogList']);
Route::get('blog-list/{blog}', [BlogController::class, 'show']);
Route::post('blog-list/rating', [BlogController::class, 'rating']);
Route::get('get-blog-rating', [BlogController::class, 'getBlogRating']);
Route::put('blogs/{id}/visualization',[BlogController::class, 'updateVisualization']);
?>


