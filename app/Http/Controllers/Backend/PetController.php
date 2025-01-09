<?php

namespace App\Http\Controllers\Backend;

use Modules\Pet\Models\Pet;
use Illuminate\Http\Request;
use Modules\Pet\Models\Breed;
use App\Http\Controllers\Controller;

class PetController extends Controller
{
    public function profilePublic($slug)
    {
        $pet = Pet::where('slug',$slug)->first();
        $color = null;
        if(count($pet->diario) > 0 && count($pet->histories) > 0){
            $color = '#28a745';
        }elseif(count($pet->diario) > 0){
            $color = '#007bff';
        }elseif(count($pet->histories) > 0){
            $color = '#ffc107';
        }else{
            $color = '#dc3545';
        }
        if($pet){
            return view('backend.pet.index',compact('pet','color'));
        }
    }
}
