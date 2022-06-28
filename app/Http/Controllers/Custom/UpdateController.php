<?php

namespace App\Http\Controllers\Custom;

use App\Models\Record;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function updatePhase2Null(Request $request) {
        $act_api = 'https://ast.splitsecondsurveys.co.uk/api/v1/record/?';
        $records = Record::where('id', '>=', 66)->where('id', '<=', 86)->get();
        dd($records);
    }
}
