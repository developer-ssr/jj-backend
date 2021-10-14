<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Record;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function addresses()
    {
        return Record::all()->pluck('meta')->map(fn ($v) => $v['office'])->unique()->toArray();
    }

    public function index()
    {
        $offices = Office::all();
        return response()->json($offices);
    }

    public function show(Office $office)
    {
        return response()->json($office);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable',
            'type' => 'required',
            'code' => 'required'
        ]);
        $office = Office::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_id' => $request->user()->id,
            'type' => strtolower($request->type),
            'code' => $request->code
        ]);
        return response()->json($office);
    }

    public function update(Request $request, Office $office)
    {
        $request->validate([
            'name' => 'required',
            //'address' => 'required',
            'type' => 'required',
            'code' => 'required',
            'email' => 'nullable',
            
        ]);
        $office->update([
            'name' => $request->name,
            //'address' => $request->address,
            'type' => $request->type,
            'email' => $request->email,
            'code' => $request->code
        ]);
        return response()->json($office);
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return response('ok');
    }
}
