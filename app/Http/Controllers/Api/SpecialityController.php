<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Speciality;
use Illuminate\Http\Request;

class SpecialityController extends Controller
{
    public function index(Request $request)
    {
        if(!$request->role){
            $specialities = Speciality::all();
        }else{
            $role = $request->role;
            $specialities = Speciality::with('role')->whereHas('role',function($q) use ($role){
                return $q->where('name',$role);
            })->get();
        }

        return response()->json([
            'success' =>true,
            'data' => $specialities
        ],200);
    }
}
