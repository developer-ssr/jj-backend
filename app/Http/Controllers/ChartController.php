<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Office;
use App\Models\Chart;
use App\Models\Record;
use App\Models\Link;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ChartController extends Controller
{
    private $tops;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $office = Office::find($request->office_id);
        if ($request->has('filter_ids')) {
            $filters = Filter::whereIn('id', json_decode($request->filter_ids, true))->get();
            Cache::put($office->id, $filters->pluck('id')->toArray());
        } else {
            if (Cache::has($office->id)) {
                $filters = Filter::where('office_id', $request->office_id)->whereIn('id', Cache::get($office->id))->get();
            } else {
                $filters = Filter::where('office_id', $request->office_id)->orderBy('id', 'desc')->limit(1)->get();
            }
            
        }
        if ($filters->isEmpty()) {
            return response('No data', 500);
        }
        
        $links = Link::where('office_id', $office->id)->get();
        // $filters = Filter::all();
        $data = [];
        $do_update = true;
        $matched = false; // for cache, check records count
        foreach ($filters as $key => $filter) {
            $qry = Chart::where('filter_id',$filter->id);
            if(!$qry->exists()){
                $chart = Chart::create([
                    'title' => $office->name,
                    'filter_id' => $filter->id
                ]);
                $do_update = true;
            }else {
                $chart = $qry->first();
                if ($matched) {
                    $do_update = false;
                }else {
                    $do_update = true;
                }
            }

            if ($do_update == true) {
                $series = [];
                // $records = [];//Record::all();
                $categories = [];
                $country = '';
                foreach ($filter->data['legends'] as $legend) {
                    foreach ($legend['primes'] as $prime) {
                        $code = Str::of($legend['name'].'_'. $prime)->ucfirst();
                        $series_data = [];
                        $records = [];
                        $segment = 0;
                        $this->tops = [
                            "highest" => [
                                "value" => 0,
                                "colour" => 'green'
                            ]
                        ];
                        if ($office->type == 'country' || $office->type == 'global') {
                            $res_ids = Link::where('country_code', $office->code)->get()->pluck('link_id')->toArray();
                            info($res_ids);
                            foreach ($filter->data['segments'] as $s_key => $segments):
                                if (!isset($records[$s_key])) {
                                    //$records[$s_key] = Record::whereBetween('created_at', [date($segments['from']), date($segments['to'])])->get();
                                    if ($office->type == 'country') {
                                        // country
                                        $records[$s_key] = Record::whereDate('created_at', ">=", date($segments['from']))
                                                                ->whereDate('created_at', "<=", date($segments['to']))
                                                                ->whereIn('participant_id', $res_ids)
                                                                ->get();
                                    } else {
                                        // global
                                        $records[$s_key] = Record::whereDate('created_at', ">=", date($segments['from']))
                                                                ->whereDate('created_at', "<=", date($segments['to']))
                                                                ->get();
                                    }
                                    if (count($records[$s_key]) > 0) {
                                        $country = $records[$s_key][0]->country;
                                    }
                                    
                                }
                                $tcount = count($records[$s_key]);
                                $date = Carbon::parse($segments['from'])->format('d M Y');
                                if (!isset($categories[$s_key])) {
                                    $categories[$s_key] = $date;//'Segment '.($segment + 1);
                                }
                                $score = $this->getScore($records[$s_key], $legend['name'], $prime);
                                
                                $series_data[] = [
                                    'question' => $score['question'],
                                    'code' => $code,
                                    'prime' => $score['prime'],
                                    'segment' => ($s_key + 1),
                                    'date' => $date,
                                    'tcount' => $tcount,
                                    'gscore' => $score['gscore'],
                                    'percentage' => $score['percentage'],
                                    'record_ids' => $records[$s_key]->pluck('id'),
                                    'dimension' => $score['dimension'],
                                    'targets' => $score['targets']
                                ];
                                $segment++;
                            endforeach;
                        }else {
                            foreach ($links as $s_key => $link) {
                                $records[$s_key] = Record::where('participant_id', $link->link_id)->get();  
                                $tcount = count($records[$s_key]);
                                if ($tcount > 0) {
                                    if (!isset($categories[$segment])) {
                                        $categories[$segment] = $records[$s_key][0]->created_at->format("d M Y");//'Segment '.($segment + 1);
                                        $country = $records[$s_key][0]->country;
                                    }
                                    $score = $this->getScore($records[$s_key], $legend['name'], $prime);
                                    $date = $link->created_at->format("d M Y");
                                    $series_data[] = [
                                        'question' => $score['question'],
                                        'code' => $code,
                                        'prime' => $score['prime'],
                                        'segment' => ($segment + 1),
                                        'date' => $date,
                                        'tcount' => $tcount,
                                        'gscore' => $score['gscore'],
                                        'percentage' => $score['percentage'],
                                        'record_ids' => $records[$s_key]->pluck('id'),
                                        'dimension' => $score['dimension'],
                                        'targets' => $score['targets']
                                    ];
                                    $segment++;
                                }
                            }
                        }
                        
                        if (!isset($score)) {
                            return response('No links or segments', 500);
                        }
                        foreach ($score['percentage'] as $colour =>  $percent) {
                            if ($percent['value'] >= $this->tops['highest']['value']) {
                                $this->tops['highest']['value'] = $percent['value'];
                                $this->tops['highest']['colour'] = $colour;
                            }
                        }
                        /* foreach ($this->tops['colours'] as $colour => $top) {
                            if ($top > $this->tops['highest']['value']) {
                                $this->tops['highest']['value'] = $top;
                                $this->tops['highest']['colour'] = $colour;
                            }
                        } */
                        $this->tops['segment'] = last($series_data);
                        $series[] = [
                            'name' => $code,
                            'question' => $score['question'],
                            'data' => $series_data,
                            'tops' => $this->tops
                        ];
                    }
                }
                $chart->update([
                    'series' => $series,
                    'categories' => $categories,
                    'title' => $office->name,
                    'office_type' => $office->type,
                    'country' => Chart::getCountry($country)
                ]);
            }

            $data[] = $chart->only(['id', 'title', 'series', 'categories', 'office_type', 'country']);
        }
        return response()->json($data[0]);
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
        $max_value = 0;
        $points = 0;
        $percentage = [
            'green' => [
                'colour' => 'green',
                'value' => 0,
                'count' => 0,
                'name' => 'Green Box %'
            ],
            'orange' => [
                'colour' => 'orange',
                'value' => 0,
                'count' => 0,
                'name' => 'Amber Box %'
            ],
            'red' => [
                'colour' => 'red',
                'value' => 0,
                'count' => 0,
                'name' => 'Red Box %'
            ],
        ];
        if ($legend == 't6') {
            unset($percentage['red']);
            unset($percentage['orange']);
            $colour = 'green';
            /* if ($prime == 1) {
                unset($percentage['orange']);
                unset($percentage['green']);
                $colour = 'red';
            }else if ($prime == 2) {
                unset($percentage['red']);
                unset($percentage['green']);
                $colour = 'orange';
            }else {
                unset($percentage['red']);
                unset($percentage['orange']);
                $colour = 'green';
            } */
            
        } elseif ($legend == 't7') {
            unset($percentage['red']);
            unset($percentage['orange']);
            $colour = 'green';
            /* if ($prime <= 2) {
                unset($percentage['orange']);
                unset($percentage['green']);
                $colour = 'red';
            }else if ($prime == 3) {
                unset($percentage['red']);
                unset($percentage['green']);
                $colour = 'orange';
            }else {
                unset($percentage['red']);
                unset($percentage['orange']);
                $colour = 'green';
            } */
        } elseif ($legend == 't2'|| $legend == 't11' || $legend == 't12') {
            unset($percentage['red']);
            unset($percentage['orange']);
            $colour = 'green';
        } 
        $tcount = count($records);
        $tmp_data = [];
        foreach ($records as $record) {
            switch ($legend) {
                case 't2':
                case 't6':
                case 't7':
                case 't11':
                case 't12':
                    $tmp_data = Chart::getExpData($legend, $record, $prime);
                    break;
                default:
                    if (isset($record->data[$legend]['responses'])) {
                        $tmp_data = collect($record->data[$legend]['responses'][0]['primes'])->firstWhere('index', $prime);
                    }else {
                        $tmp_data = null;
                    }                    
                    break;
            }

            if ($tmp_data != null) {
                if ($max_value == 0) {
                    if (count($tmp_data['data']) > 2) {
                        $max_value = $tcount * count($tmp_data['data']);
                    }else {
                        $max_value = $tcount;
                    }
                }
                foreach ($tmp_data['data'] as $t_key => $tmp) {
                    /* if (!isset($points[$t_key])) {
                        $points[$t_key] = 0;
                    } */
                    if ($tmp['selected']) {
                        // $points+=($t_key + 1);
                        $points+=$tmp['value'];
                        switch ($legend) {
                            case 't3':
                            case 't5':
                            case 't8':
                                if ($t_key <= 1) {
                                    $percentage['red']['count'] += 1;
                                }elseif ($t_key == 2) {
                                    $percentage['orange']['count'] += 1;
                                }else  {
                                    $percentage['green']['count'] += 1;
                                }
                                break;
                            case 't4':
                            case 't9':
                                if ($prime == 19) {
                                    if ($t_key == 0) {
                                        $percentage['green']['count'] += 1; //change 1/14/2021
                                    }else  {
                                        $percentage['red']['count'] += 1; //change 1/14/2021
                                    }
                                }else {
                                    if ($t_key == 0) {
                                        $percentage['red']['count'] += 1;
                                    }else  {
                                        $percentage['green']['count'] += 1;
                                    }
                                }
                                
                                unset($percentage['orange']);
                                break;
                            case 't10':
                                if ($t_key == 0) {
                                    $percentage['red']['count'] += 1;
                                }elseif ($t_key == 3) {
                                    $percentage['green']['count'] += 1;
                                }else  {
                                    $percentage['orange']['count'] += 1;
                                }
                                break;
                            default:
                                # t2 t6 t7 t11 t12
                                $percentage[$colour]['count'] += 1;
                                break;
                        }
                    }
                }
            }
            
        }
        
        if ($tcount > 0) {
            foreach ($percentage as $key =>  $percent) {
                $percentage[$key]['value'] = ceil(($percent['count'] / $tcount) * 100);
                // $percent['value'] = ceil($percent['count'] / $tcount);
                /* if ($percentage[$key]['value'] > $this->tops['colours'][$key]) {
                    $this->tops['colours'][$key] = $percentage[$key]['value'];
                } */
            }
        }
        
        $score = $max_value > 0 ? (($points/$max_value) * 100) : null;
        $question = $this->getQuestion($legend);
        $equivalent = $tmp_data['prime'] ?? null;
        if ($legend == 't2' || $legend == 't6' || $legend == 't7' || $legend == 't11' || $legend == 't12') {
            $targets = [''];
        }else {
            $targets = $tmp_data != null ? collect($tmp_data['data'])->pluck('equivalent'): [];
        }
        
        return [
            'gscore' => ceil($score),
            'prime' => $legend == 't5' ? ($tmp_data['equivalent'] ?? '').' '.$equivalent : $equivalent,
            'percentage' => $percentage,
            'question' => $question['question'],
            'dimension' => $question['dimension'],
            'targets' => $targets
        ];
    }    

    public function getQuestion($legend) 
    {
        $choices = [];
        switch ($legend) {
            case 't2':
                $dimension = '';
                $question = 'For your patients between 5-18 years of age, please estimate the percent of patients that fall within each category.
                The total must sum to 100%';
                $choices = [
                    'No treatment recommended',
                    'Refractive only treatment: you fit only with single vision solutions (glasses or contact lenses)',
                    'Myopia management treatment: you fit with myopia management treatments (Ortho-K, multifocal soft contacts or glasses, myopia control soft contacts or glasses, atropine)'
                ];
                break;
            case 't3':
                $dimension = 'Satisfaction';
                $question = 'Please indicate your satisfaction level with Abiliti in the following areas:';
                break;
            case 't4':
                $dimension = 'Value';
                $question = 'Please indicate which of the following areas from Abiliti are effective and providing value to your myopia management practice.';
                break;
            case 't5':
                $dimension = 'Brand';
                $question = 'How likely would you be to recommend the following to your patients and their parents?';
                break;
            case 't6':
                $dimension = '';
                $question = 'Now we are going to ask you a little about the conversations you may have with the parents of your patients between 5-18 years. Please indicate if you agree with the following statement:  Abiliti HealthCaring Conversations, ISIGHT Model, provides me with usable strategies for engaging in high quality conversations with my patients.';
                $choices = [
                    'I DO NOT USE the HealthCaringTM Conversations ISIGHT Model',
                    'NO, I DO NOT agree with HealthCaringTM Conversations ISIGHT Model',
                    'YES, I agree with HealthCaringTM Conversations ISIGHT Model'
                ];
                break;
            case 't7':
                $dimension = '';
                $question = 'For your patients between 5-18 years of age, please estimate the percent of parents/patients you approach to discuss myopia management as a treatment option during the first visit.';
                $choices = [
                    'None',
                    'About 25%',
                    'About 50%',
                    'About 75%',
                    'Virtually all of my patients'
                ];
                break;
            case 't8':
                $dimension = 'HC2 Behavior freq.';
                $question = 'In general, how frequently are you able to do the following with your myopia patients and/or their parents? ';
                break;
            case 't9':
                $dimension = 'HC2 Content';
                $question = 'As part of the parent conversation, please indicate if you include the following information or language in your discussion:';
                break;
            case 't10':
                $dimension = 'Tool use/reco';
                $question = 'Please indicate how often you use/recommend the following tools from Abiliti™:';
                break;
            case 't11':
                $dimension = '';
                $question = 'Please indicate what you like about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:';
                $choices = [
                    'Please indicate what you like about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:'
                ];
                break;
            case 't12':
                $dimension = '';
                $question = 'Please indicate what you dislike about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:';
                $choices = [
                    'Please indicate what you dislike about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:'
                ];
                break;
            default:
                $dimension = 'Dimension';
                $question = 'How likely would you be to recommend the following to your patients and their parents?';
                break;
        }
        return ['question' => $question, 'dimension' => $dimension, 'choices' => $choices];
    }
}
