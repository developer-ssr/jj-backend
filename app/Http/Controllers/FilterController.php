<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Office;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function index(Request $request, Office $office)
    {
        $filters = Filter::where('user_id', $request->user()->id)->where('office_id', $office->id)->get();
        return response()->json($filters);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'office_id' => 'required',
            'data' => 'required'
        ]);
        $filter = Filter::create([
            'name' => $request->name,
            'data' => $request->data,
            'office_id' => $request->office_id
        ]);
        return response()->json($filter);
    }

    public function update(Request $request, Filter $filter)
    {
        $request->validate([
            'name' => 'required',
            'office_id' => 'required',
            'data' => 'required'
        ]);
        $filter->update([
            'name' => $request->name,
            'data' => $request->data,
            'office_id' => $request->office_id
        ]);
        return response()->json($filter, 200);
    }

    public function destroy(Filter $filter)
    {
        $filter->delete();
        return response('ok');
    }
}
