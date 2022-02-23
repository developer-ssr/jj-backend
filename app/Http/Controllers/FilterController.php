<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Office;
use App\Models\Chart;
use Illuminate\Http\Request;

class FilterController extends Controller
{

    public function primes(Office $office)
    {
        /* 124: 'ca',
        344: 'hk',
        702: 'sg',
        840: 'us' */
        switch ($office->code) {
            case 840: //us
                $legends = [
                    "t2" => [0,1,2],
                    "t3" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19],
                    "t4" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17],
                    "t5" => [0,1,2,3,4,5,6,7,8,9,10,11],
                    "t6" => [0,1,2],
                    "t7" => [0,1,2,3,4],
                    "t8" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
                    "t9" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
                    "t10" => [0,1,2,3,4,5,6],
                    "t11" => [0],
                    "t12" => [0]
                ];
                break;
            case 702: //sg
                $legends = [
                    "t2" => [0,1,2],
                    "t3" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19],
                    "t4" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17],
                    "t5" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15],
                    "t6" => [0,1,2],
                    "t7" => [0,1,2,3,4],
                    "t8" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
                    "t9" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
                    "t10" => [0,1,2,3,4,5,6],
                    "t11" => [0],
                    "t12" => [0]
                ];
                break;
            case 124: //ca
                $legends = [
                    "t2" => [0,1,2],
                    "t3" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19],
                    "t4" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17],
                    "t5" => [0,1,2,3,4,5,6,7,8,9,10,11],
                    "t6" => [0,1,2],
                    "t7" => [0,1,2,3,4],
                    "t8" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
                    "t9" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
                    "t10" => [0,1,2,3,4,5,6],
                    "t11" => [0],
                    "t12" => [0]
                ];
                break;
            case 344: //hk
                $legends = [
                    "t2" => [0,1,2],
                    "t3" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19],
                    "t4" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17],
                    "t5" => [0,1,2,3,4,5,6,8,10,12,13,14,15,16,17,18,19,20,21,22],
                    "t6" => [0,1,2],
                    "t7" => [0,1,2,3,4],
                    "t8" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
                    "t9" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
                    "t10" => [0,1,2,3,4,5,6],
                    "t11" => [0],
                    "t12" => [0]
                ];
                break;
            default:
                $legends = [
                    "t2" => [0,1,2],
                    "t3" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19],
                    "t4" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17],
                    "t5" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22],
                    "t6" => [0,1,2],
                    "t7" => [0,1,2,3,4],
                    "t8" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
                    "t9" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
                    "t10" => [0,1,2,3,4,5,6],
                    "t11" => [0],
                    "t12" => [0]
                ];
                break;
        }
        $items = Chart::getPrimes($legends);

        return response()->json($items);
    }

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
            'data' => 'required',
        ]);
        $filter = Filter::create([
            'name' => $request->name,
            'data' => $request->data,
            'office_id' => $request->office_id,
            'user_id' => $request->user()->id
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
