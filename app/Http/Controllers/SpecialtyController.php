<?php

namespace App\Http\Controllers;

use App\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $specialties = Specialty::all();
        return view('specialties.index', \compact('specialties'));
    }

    public function create()
    {
        return view('specialties.create');
    }

    public function store(Request $request)
    {
        $this->performValidation($request);
        // dd($request->all());
        $specialty = new Specialty();
        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        $notification = 'La especialidad se ha registrado correctamente.';

        return redirect('/specialties')->with(\compact('notification'));
    }

    public function edit(Specialty $specialty)
    {

        return view('specialties.edit', \compact('specialty'));
    }

    public function update(Request $request, Specialty $specialty)
    {


        $this->performValidation($request);
        // dd($request->all());
        // $specialty = new Specialty();
        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        $notification = 'La especialidad se ha actualizado correctamente.';

        return redirect('/specialties')->with(\compact('notification'));
    }

    public function destroy(Specialty $specialty)
    {
        $deletedName = $specialty->name;
        $specialty->delete();

        $notification = 'La especialidad ' . $deletedName . ' se ha eliminado correctamente.';

        return redirect('/specialties')->with(\compact('notification'));
    }

    private function performValidation(Request $request)
    {
        $rules = [
            'name' => 'required|min:3'
        ];

        $messages = [
            'name.required' => 'Es necesario ingresar un nombre',
            'name.min' => 'Como mínomo el nombre debe tener 3 caracteres'
        ];

        $this->validate($request, $rules, $messages);
    }
}
