<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Chart;
use App\Exports\CsvExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function download(Request $request, $id, $summary) 
    {
        $chart = Chart::find($id);
        $all = json_decode($request->all);
        $legends = json_decode($request->legends);
        if ($summary == 'summary') {
            $data = $this->exportSummary($chart, $all, $legends);
        }else {
            $data = $this->exportRespondent($chart, $all, $legends);
        }
        dd($data);
        
        $headers = ['Dimension','','','Question Text', 'SSR Platform', 'Question Types', 'Answer Type','','','Answer Value','','','Score'];
        $data = collect($data)->prepend($headers)->toArray(); 
        return Excel::download(CsvExport::new($data), "download.xlsx");
    }

    public function exportSummary($chart, $all, $legends) 
    {
        $results = [];
        if ($all) {
            # code...
        }else {
            foreach ($legends as $legend) {
                $tmp_data = collect($chart->series)->firstWhere('name', $legend);
                unset($tmp_data['data'][count($tmp_data['data']) - 1]['percentage']);
                // $results[] = $tmp_data['data'][count($tmp_data['data']) - 1];
                $results[] = Arr::flatten($tmp_data['data'][count($tmp_data['data']) - 1]);
            }
        }
        return $results;
    }

    public function exportRespondent($chart, $all, $legends) 
    {
        $results = [];
        if ($all) {
            # code...
        }else {
            $headers = [];
            foreach ($legends as $legend) {
                $tmp = Str::of($legend)->explode('_');
                $t = Str::lower($tmp[0]);
                $prime = $tmp[1];
                /* $results[$t] = [
                    [$this->getDimension($t), $tmp[0], $tmp[0]]
                ]; */
                
                $record_ids = collect([]);
                $serires = collect($chart->series)->firstWhere('name', $legend);
                foreach ($serires['data'] as $data) {
                    if (!isset($headers[$t])) {
                        $headers[$t] = collect([$data['dimension'], $tmp[0], $tmp[0], $data['question']])->merge($data['targets'])->toArray();
                    }
                    $record_ids = $record_ids->merge($data['record_ids']);
                }
                $records = Record::whereIn('id', $record_ids->unique()->toArray())->get();
                $data = $this->getData($records, $t, $prime);
                
                // $results[] = $tmp_data['data'][count($tmp_data['data']) - 1];
                $results[] = Arr::flatten($serires['data'][count($serires['data']) - 1]);
            }
            dd($headers);
        }
        
        return $results;
    }
    public function getData($records, $t, $prime) {
        $tmp_data = [];
        foreach ($records as $record) {
            $tmp_data = collect($record->data[$t]['responses'][0]['primes'])->firstWhere('index', $prime);
            
        }
    }
}
