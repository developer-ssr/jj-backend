<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Office;
use App\Models\Chart;
use App\Models\Record;

use Illuminate\Http\Request;

class ChartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $office = Office::find($request->office_id);
        $country = $request->country ?? null;
        if ($request->has('filter_ids')) {
            $filters = Filter::whereIn('id', json_decode($request->filter_ids, true))->get();
        } else {
            $filters = Filter::where('office_id', $request->office_id)->orderBy('id', 'desc')->limit(1)->get();
        }
        if ($filters->isEmpty()) {
            return response('No data');
        }
        
        
        // $filters = Filter::all();
        $data = [];
        
        foreach ($filters as $key => $filter) {
            $qry = Chart::where('filter_id',$filter->id);
            if(!$qry->exists()){
                $chart = Chart::create([
                    'title' => $office->name,
                    'filter_id' => $filter->id
                ]);
            }else {
                $chart = $qry->first();
            }

            // if ($request->update_series == true) {
                $series = [];
                $records = [];//Record::all();
                
                foreach ($filter->data['legends'] as $legend) {
                    foreach ($legend['primes']  as $prime) {
                        $series_data = [];
                        foreach ($filter->data['segments'] as $s_key => $segment) {
                            if (!isset($records[$s_key])) {
                                $records[$s_key] = !$country ?
                                 Record::whereBetween('created_at', [date($segment['from']), date($segment['to'])])->where('meta->office', $office->address)->get() :
                                Record::where('country', $country)->get();
                            }
                            $score = $this->getScore($records[$s_key], $legend['name'], $prime);
                            $series_data[] = ['x' => ($s_key + 1), 'y' => $score];
                        }
                        $series[] = [
                            'name' => $legend['name'].'_'.$prime,
                            'data' => $series_data
                        ];
                    }
                }
                $chart->update([
                    'series' => $series,
                    'title' => $office->name
                ]);
            // }

            $data[] = $chart->only(['id', 'title', 'series']);
        }
        return response()->json($data);
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

    public function checkChartData($chart)
    {
        if ($chart->series == null) {
            $this->processkChartData($chart);
        }
    }

    public function processkChartData($chart)
    {
        $records = Record::all();
    }

    public function getScore($records, $legend, $prime)
    {
        $data = [];
        $max_value = 0;
        $points = 0;
        foreach ($records as $record) {
            $tmp_data = collect($record->data[$legend]['responses'][0]['primes'])->firstWhere('index', $prime)['data'];
            if ($max_value == 0) {
                $max_value = count($records) * count($tmp_data);
            }
            foreach ($tmp_data as $t_key => $tmp) {
                /* if (!isset($points[$t_key])) {
                    $points[$t_key] = 0;
                } */
                if ($tmp['selected']) {
                    $points++;
                }
            }
        }

        $score = $max_value > 0 ? (($points/$max_value) * 100) : null;
        return ceil($score);
    }
}
