<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Record;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function addresses()
    {
        return Record::all()->pluck('meta')->map(fn ($v) => $v['office'])->toArray();
    }

    public function index()
    {
        $offices = Office::all();
        return response()->json($offices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required'
        ]);
        $office = Office::create([
            'name' => $request->name,
            'address' => $request->address,
            'user_id' => 1
        ]);
        return response()->json($office);
    }

    public function update(Request $request, Office $office)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required'
        ]);
        $office->update([
            'name' => $request->name,
            'address' => $request->address
        ]);
        return response()->json($office);
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return response('ok');
    }
}
