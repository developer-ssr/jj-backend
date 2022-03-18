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
                                        'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=us'
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
                                        'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=sg'
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
                                        'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=hk'
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
                                        'url' => 'https://fluent.splitsecondsurveys.co.uk/custom/jnj/baseline/download?country=ca'
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
                                            'label' => 'United States',
                                            'url' => url('/baseline/download/respondent').'?all=false&country=us'
                                        ]
                                    ]
                                ],
                                [
                                    'label' => 'Summary data',
                                    'url' => null, 
                                    'links' => [
                                        [
                                            'label' => 'All ',
                                            'url' => url('/baseline/download/summary').'?all=true'
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
