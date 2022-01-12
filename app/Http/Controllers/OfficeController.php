<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\Office;
use App\Models\Record;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function addresses()
    {
        return Record::all()->pluck('meta')->map(fn ($v) => $v['office'])->unique()->toArray();
    }

    public function index(Request $request)
    {
        if ($request->user()->type === "user") {
            $offices = Office::where('id', $request->user()->office_id)->get();
        } else {
            $offices = Office::with('links')->all()->toArray();
            $offices = collect($offices)->map(function($values) {
                $email = Email::where('email', $values['email'])->orderBy('created_at', 'desc')->first();
                $values['emails'] = $email;
                $taken = collect($values['links'])->filter(fn($v) => $v['taken'] === 'YES')->count();
                $values['links'] = $taken . '/' . count($values['links']);
                return $values;
            });
        }
        
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
            'code' => $request->code,
            'sequence' => $request->sequence
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
            'code' => $request->code,
            'sequence' => $request->sequence
        ]);
        return response()->json($office);
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return response('ok');
    }
}
