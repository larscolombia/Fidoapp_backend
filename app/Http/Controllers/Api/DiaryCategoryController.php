<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiaryCategory;
use Illuminate\Http\Request;

class DiaryCategoryController extends Controller
{
    public function getCategory()
    {
        $diaryCategories = DiaryCategory::all();
        return response()->json([
            'status' => 'success',
            'data' => $diaryCategories
        ],200);
    }
}
