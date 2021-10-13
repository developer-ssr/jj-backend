<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Office;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    //

    public function index(Office $office)
    {
        $links = Link::where('office_id', $office->id)->get();
        return response()->json($links);
    }

    public function store(Request $request, Office $office)
    {
        $l = Link::create([
            'office_id' => $office->id,
            'country_code' => $office->code,
            'uid' => random_int(10000000, 99999999),
            // 'created_at' => now()->addMonth(),
            // 'updated_at' => now()->addMonth()
        ]);
        return response()->json(Link::find($l->id));
    }

    public function destroy(Link $link)
    {
        $link->delete();
        return response('ok');
    }
}
