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
        $filters = Filter::whereIn('id', $request->filter_ids)->get();
        $data = [];
        foreach ($filters as $key => $filter) {
            $qry = Chart::where('filter_id',$filter->id);
            if(!$qry->exists()){
                $chart = Chart::create([
                    'title' => $filter->name,
                    'filter_id' => $filter->id
                ]);
            }else {
                $chart = $qry->first();
            }

            // if ($request->update_series == true) {
                $series_data = [];
                $segment_num = 1;
                foreach ($filter->segments as $segment) {
                    $records = Record::whereBetween('created_at', [date($segment['from']), date($segment['to'])])->get();
                    $result = $this->getResult($records, $filter->prime);
                    $series_data[] = ['x' => $segment_num, 'y' => $result];
                }

                $chart->update([
                    'name' => $filter->prime,
                    'series' => $series_data
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

    public function getResult($records, $prime)
    {
        $records = Record::all();
        $result = 68;
        return $result;
    }
}
