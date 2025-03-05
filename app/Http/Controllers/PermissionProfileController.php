<?php

namespace App\Http\Controllers;

use App\Models\PermissionProfile;
use Illuminate\Http\Request;

class PermissionProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissionPetProfiles = PermissionProfile::all();
        return view('permission_pet_profiles.index', compact('permissionPetProfiles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permission_pet_profiles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);

        PermissionProfile::create($request->all());

        return redirect()->route('permission_pet_profiles.index')->with('success', 'Registro creado correctamente');
    }

    public function show($id)
    {
        $permissionProfile = PermissionProfile::find($id);
        return view('permission_pet_profiles.show', compact('permissionProfile'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permissionProfile = PermissionProfile::find($id);
        return view('permission_pet_profiles.edit', compact('permissionProfile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required',
        ]);

        $permissionProfile = PermissionProfile::find($id);
        $permissionProfile->update($request->all());

        return redirect()->route('permission_pet_profiles.index')->with('success', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PermissionProfile::destroy($id);
        return redirect()->route('permission_pet_profiles.index')->with('success', 'Registro eliminado correctamente');
    }
}
