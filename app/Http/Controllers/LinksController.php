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

    public function downloadLinks($div)
    {
        $links = [];
        switch ($div) {
            case 'sidebar':
                $links = [
                    [
                        'label' => 'Tracker KPI',
                        'url' => null,
                        'links' => [
                            /* [
                                'label' => 'All',
                                'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=all'
                            ], */
                            [
                                'label' => 'Global Result',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        'url' => url('/charts/download/'.env('CHART_GLOBAL', 67).'/tracker_kpi').'?all=true'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_Leaders&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_Believers&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_Leaders_Believers&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_Emerging&classifications=["Emerger"]'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'United States',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        'url' => url('/charts/download/86/tracker_kpi').'?all=true'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_US_Leaders&country=us&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_US_Believers&country=us&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_US_Leaders_Believers&country=us&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_US_Emerging&country=us&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Singapore',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        'url' => url('/charts/download/85/tracker_kpi').'?all=true'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_SG_Leaders&country=sg&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_SG_Believers&country=sg&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_SG_Leaders_Believers&country=sg&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_SG_Emerging&country=sg&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Hong Kong',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        'url' => url('/charts/download/78/tracker_kpi').'?all=true'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_HK_Leaders&country=hk&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_HK_Believers&country=hk&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_HK_Leaders_Believers&country=hk&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_HK_Emerging&country=hk&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Canada',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        'url' => url('/charts/download/88/tracker_kpi').'?all=true'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_CA_Leaders&country=ca&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_CA_Believers&country=ca&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_CA_Leaders_Believers&country=ca&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/tracker').'?all=false&title=Tracker_KPI_CA_Emerging&country=ca&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                        ]
                    ],
                    [
                        'label' => 'Baseline KPI',
                        'url' => null,
                        'links' => [
                            [
                                'label' => 'All',
                                'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=all'
                            ],
                            [
                                'label' => 'Global Result',
                                'url' => null,
                                'links' => [
                                    /* [
                                        'label' => 'All',
                                        'url' => 'https://jnj.splitsecondsurveys.co.uk/offices/download_office/baseline?all=false&title=Baseline_KPI_Global_Result&classifications=["Leader", "Believer", "Emerger"]'
                                    ], */
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_Leaders&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_Believers&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_Leaders_Believers&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_Emerging&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                            [
                                'label' => 'United States',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        /* 'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=us' */
                                        'url' => 'https://jnj.splitsecondsurveys.co.uk/offices/download_office/baseline?all=true&title=Baseline_KPI_US&country=us'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_US_Leaders&country=us&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_US_Believers&country=us&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_US_Leaders_Believers&country=us&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_US_Emerging&country=us&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Singapore',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        // 'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=sg'
                                        'url' => 'https://jnj.splitsecondsurveys.co.uk/offices/download_office/baseline?all=true&title=Baseline_KPI_SG&country=sg'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_SG_Leaders&country=sg&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_SG_Believers&country=sg&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_SG_Leaders_Believers&country=sg&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_SG_Emerging&country=sg&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Hong Kong',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        // 'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=hk'
                                        'url' => 'https://jnj.splitsecondsurveys.co.uk/offices/download_office/baseline?all=true&title=Baseline_KPI_HK&country=hk'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_HK_Leaders&country=hk&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_HK_Believers&country=hk&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_HK_Leaders_Believers&country=hk&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_HK_Emerging&country=hk&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Canada',
                                'url' => null,
                                'links' => [
                                    [
                                        'label' => 'KPI',
                                        // 'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=ca'
                                        'url' => 'https://jnj.splitsecondsurveys.co.uk/offices/download_office/baseline?all=true&title=Baseline_KPI_CA&country=ca'
                                    ],
                                    [
                                        'label' => 'Leaders',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_CA_Leaders&country=ca&classifications=["Leader"]'
                                    ],
                                    [
                                        'label' => 'Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_CA_Believers&country=ca&classifications=["Believer"]'
                                    ],
                                    [
                                        'label' => 'Leaders & Believers',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_CA_Leaders_Believers&country=ca&classifications=["Leader", "Believer"]'
                                    ],
                                    [
                                        'label' => 'Emerging ',
                                        'url' => url('/offices/download_office/baseline').'?all=false&title=Baseline_KPI_CA_Emerging&country=ca&classifications=["Emerger"]'
                                    ]
                                ]
                            ],
                        ]
                    ],
                    [
                        'label' => 'Baseline',
                        'url' => null,
                        'links' => [
                            [ 
                                'label' => 'Respondent level',
                                'url' => null, 
                                'links' => [
                                    [
                                        'label' => 'All',
                                        'url' => url('/baseline/download/respondent').'?all=true&title=raw_baseline_all'
                                    ],
                                    [
                                        'label' => 'United States',
                                        'url' => null,
                                        'links' => [
                                            [
                                                'label' => 'All',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=us&title=raw_baseline_us'
                                            ],
                                            [
                                                'label' => 'Leaders',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=us&title=raw_leaders_us&classifications=["Leader"]'
                                            ],
                                            [
                                                'label' => 'Believers',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=us&title=raw_believers_us&classifications=["Believer"]'
                                            ],
                                            [
                                                'label' => 'Leaders & Believers',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=us&title=raw_leaders_believers_us&classifications=["Leader", "Believer"]'
                                            ],
                                            [
                                                'label' => 'Emerging ',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=us&title=raw_emerger_us&classifications=["Emerger"]'
                                            ]
                                        ]
                                    ],
                                    [
                                        'label' => 'Singapore',
                                        'url' => null,
                                        'links' => [
                                            [
                                                'label' => 'All',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=sg&title=raw_baseline_sg'
                                            ],
                                            [
                                                'label' => 'Leaders',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=sg&title=raw_leaders_sg&classifications=["Leader"]'
                                            ],
                                            [
                                                'label' => 'Believers',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=sg&title=raw_believers_sg&classifications=["Believer"]'
                                            ],
                                            [
                                                'label' => 'Leaders & Believers',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=sg&title=raw_leaders_believers_sg&classifications=["Leader", "Believer"]'
                                            ],
                                            [
                                                'label' => 'Emerging ',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=sg&title=raw_emerger_sg&classifications=["Emerger"]'
                                            ]
                                        ]
                                    ],
                                    [
                                        'label' => 'Hong Kong',
                                        'url' => null,
                                        'links' => [
                                            [
                                                'label' => 'All',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=hk&title=raw_baseline_hk'
                                            ],
                                            [
                                                'label' => 'Leaders',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=hk&title=raw_leaders_hk&classifications=["Leader"]'
                                            ],
                                            [
                                                'label' => 'Believers',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=hk&title=raw_believers_hk&classifications=["Believer"]'
                                            ],
                                            [
                                                'label' => 'Leaders & Believers',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=hk&title=raw_leaders_believers_hk&classifications=["Leader", "Believer"]'
                                            ],
                                            [
                                                'label' => 'Emerging ',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=hk&title=raw_emerger_hk&classifications=["Emerger"]'
                                            ]
                                        ]
                                    ],
                                    [
                                        'label' => 'Canada',
                                        'url' => null,
                                        'links' => [
                                            [
                                                'label' => 'All',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=ca&title=raw_baseline_ca'
                                            ],
                                            [
                                                'label' => 'Leaders',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=ca&title=raw_leaders_ca&classifications=["Leader"]'
                                            ],
                                            [
                                                'label' => 'Believers',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=ca&title=raw_believers_ca&classifications=["Believer"]'
                                            ],
                                            [
                                                'label' => 'Leaders & Believers',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=ca&title=raw_leaders_believers_ca&classifications=["Leader", "Believer"]'
                                            ],
                                            [
                                                'label' => 'Emerging ',
                                                'url' => url('/baseline/download/respondent').'?all=false&country=ca&title=raw_emerger_ca&classifications=["Emerger"]'
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Summary data',
                                'url' => null, 
                                'links' => [
                                    [
                                        'label' => 'All ',
                                        'url' => url('/baseline/download/summary').'?all=true&title=summary_baseline_all'
                                    ],
                                    [
                                        'label' => 'United States',
                                        'url' => null,
                                        'links' => [
                                            [
                                                'label' => 'All',
                                                'url' => url('/baseline/download/summary').'?all=false&country=us&title=summary_baseline_us'
                                            ],
                                            [
                                                'label' => 'Leaders',
                                                'url' => url('/baseline/download/summary').'?all=false&country=us&title=summary_baseline_leaders_us&classifications=["Leader"]'
                                            ],
                                            [
                                                'label' => 'Believers',
                                                'url' => url('/baseline/download/summary').'?all=false&country=us&title=summary_baseline_believers_us&classifications=["Believer"]'
                                            ],
                                            [
                                                'label' => 'Leaders & Believers',
                                                'url' => url('/baseline/download/summary').'?all=false&country=us&title=summary_baseline_leaders_believers_us&classifications=["Leader", "Believer"]'
                                            ],
                                            [
                                                'label' => 'Emerging ',
                                                'url' => url('/baseline/download/summary').'?all=false&country=us&title=summary_baseline_emerger_us&classifications=["Emerger"]'
                                            ]
                                        ]
                                    ],
                                    [
                                        'label' => 'Singapore',
                                        'url' => null,
                                        'links' => [
                                            [
                                                'label' => 'All',
                                                'url' => url('/baseline/download/summary').'?all=false&country=sg&title=summary_baseline_sg'
                                            ],
                                            [
                                                'label' => 'Leaders',
                                                'url' => url('/baseline/download/summary').'?all=false&country=sg&title=summary_baseline_leaders_sg&classifications=["Leader"]'
                                            ],
                                            [
                                                'label' => 'Believers',
                                                'url' => url('/baseline/download/summary').'?all=false&country=sg&title=summary_baseline_believers_sg&classifications=["Believer"]'
                                            ],
                                            [
                                                'label' => 'Leaders & Believers',
                                                'url' => url('/baseline/download/summary').'?all=false&country=sg&title=summary_baseline_leaders_believers_sg&classifications=["Leader", "Believer"]'
                                            ],
                                            [
                                                'label' => 'Emerging ',
                                                'url' => url('/baseline/download/summary').'?all=false&country=sg&title=summary_baseline_emerger_sg&classifications=["Emerger"]'
                                            ]
                                        ]
                                    ],
                                    [
                                        'label' => 'Hong Kong',
                                        'url' => null,
                                        'links' => [
                                            [
                                                'label' => 'All',
                                                'url' => url('/baseline/download/summary').'?all=false&country=hk&title=summary_baseline_hk'
                                            ],
                                            [
                                                'label' => 'Leaders',
                                                'url' => url('/baseline/download/summary').'?all=false&country=hk&title=summary_baseline_leaders_hk&classifications=["Leader"]'
                                            ],
                                            [
                                                'label' => 'Believers',
                                                'url' => url('/baseline/download/summary').'?all=false&country=hk&title=summary_baseline_believers_hk&classifications=["Believer"]'
                                            ],
                                            [
                                                'label' => 'Leaders & Believers',
                                                'url' => url('/baseline/download/summary').'?all=false&country=hk&title=summary_baseline_leaders_believers_hk&classifications=["Leader", "Believer"]'
                                            ],
                                            [
                                                'label' => 'Emerging ',
                                                'url' => url('/baseline/download/summary').'?all=false&country=hk&title=summary_baseline_emerger_hk&classifications=["Emerger"]'
                                            ]
                                        ]
                                    ],
                                    [
                                        'label' => 'Canada',
                                        'url' => null,
                                        'links' => [
                                            [
                                                'label' => 'All',
                                                'url' => url('/baseline/download/summary').'?all=false&country=ca&title=summary_baseline_ca'
                                            ],
                                            [
                                                'label' => 'Leaders',
                                                'url' => url('/baseline/download/summary').'?all=false&country=ca&title=summary_baseline_leaders_ca&classifications=["Leader"]'
                                            ],
                                            [
                                                'label' => 'Believers',
                                                'url' => url('/baseline/download/summary').'?all=false&country=ca&title=summary_baseline_believers_ca&classifications=["Believer"]'
                                            ],
                                            [
                                                'label' => 'Leaders & Believers',
                                                'url' => url('/baseline/download/summary').'?all=false&country=ca&title=summary_baseline_leaders_believers_ca&classifications=["Leader", "Believer"]'
                                            ],
                                            [
                                                'label' => 'Emerging ',
                                                'url' => url('/baseline/download/summary').'?all=false&country=ca&title=summary_baseline_emerger_ca&classifications=["Emerger"]'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
                break;
            
            default:
                dd("Something went wrong");
                break;
        }
        return response()->json(['links' => $links], 200);
    }
}
