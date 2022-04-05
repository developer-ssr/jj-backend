<?php

if (!function_exists('generator')) {
    function generator($array): Generator
    {
        foreach ($array as $key => $value) {
            yield $key => $value;
        }
    }
}

if (!function_exists('baselineVal')) {
    function baselineVal($url_data, $vars, $num)
    {
        $isset = false;
        $val = '-';
        $i = 0;
        do {
            if ($i > (count($vars) - 1)) {
                $isset = true;
            }else {
                if (isset($url_data[$vars[$i].$num])) {
                    $val = $url_data[$vars[$i].$num];
                    $isset = true;
                }
            }
            $i++;
        } while ($isset == false);
        return $val;
    }
}

if (!function_exists('baselineValInvert')) {
    function baselineValInvert($url_data, $vars, $num, $not, $num_choice)
    {
        $isset = false;
        $val = '-';
        $i = 0;
        do {
            if ($i > (count($vars) - 1)) {
                $isset = true;
            }else {
                if (isset($url_data[$vars[$i].$num])) {
                    if ($not == $vars[$i]) {
                        $val = $url_data[$vars[$i].$num];
                    }else {
                        $list = [0];
                        for ($j=$num_choice; $j > 0; $j--) { 
                            $list[] = $j;
                        }
                        if (isset($list[$url_data[$vars[$i].$num]])) {
                            $val = $list[$url_data[$vars[$i].$num]];
                        }else {
                            $val = $url_data[$vars[$i].$num];
                        }
                    }
                    $isset = true;
                }
            }
            $i++;
        } while ($isset == false);
        return $val;
    }
}

if (!function_exists('baselineSummary')) {
    function baselineSummary($records, $key, $row, $col, $q_summary)
    {
        $val = 0;
        if ($q_summary['type'] == 'single') {
            $val = $records->countBy(function ($url_data) use ($key, $row) {
                return $url_data[$key] == $row;
            });
        } elseif ($q_summary['type'] == 'average') {
            /* $count = $records->count();
            if ($count > 0) {
                $val = $records->sum($key) / $records->count();
            } */
        } else {
            $val = 0;
        }
        
        return $val;
    }
}

if (!function_exists('baselineVariables')) {
    function baselineVariables() {
        return [ //list of variables to process
            "T1" => [
                "Q_num" => 1,
                "USA" => ['h','a','c'],
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T2" => [
                "Q_num" => 2,
                "USA" => ['h','a','c'],
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T3" => [
                "Q_num" => 3,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h', 'a']
            ],
            "T4" => [
                "Q_num" => 4,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T5" => [
                "Q_num" => 5,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T6" => [
                "Q_num" => 6,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T7" => [
                "Q_num" => 7,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T8" => [
                "Q_num" => 8,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T9" => [
                "Q_num" => 9,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T9_wc" => [
                "Q_num" => '_wc',
                "USA" => ['h9'],//missing ,'a','c'
                "Singapore" => ['h9'],
                "Hongkong" => ['h9'],
                "Canada" => ['h9','a9']
            ],
            "T9_wc_RTs" => [
                "Q_num" => '_wc_RTs',
                "USA" => ['h9'],//missing ,'a','c'
                "Singapore" => ['h9'],
                "Hongkong" => ['h9'],
                "Canada" => ['h9','a9']
            ],
            "T10" => [
                "Q_num" => 10,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T10_wc" => [
                "Q_num" => '_wc',
                "USA" => ['h10'],//missing ,'a','c'
                "Singapore" => ['h10'],
                "Hongkong" => ['h10'],
                "Canada" => ['h10','a10']
            ],
            "T10_wc_RTs" => [
                "Q_num" => '_wc_RTs',
                "USA" => ['h10'],//missing ,'a','c'
                "Singapore" => ['h10'],
                "Hongkong" => ['h10'],
                "Canada" => ['h10','a10']
            ],
            "T11" => [
                "Q_num" => '',
                "USA" => ['h11', 'a3', 'c3'],
                "Singapore" => ['h11'],
                "Hongkong" => ['h11'],
                "Canada" => ['h11','a11']
            ],
            "T12" => [
                "Q_num" => '',
                "USA" => ['h12','a4','c4'],
                "Singapore" => ['h12'],
                "Hongkong" => ['h12'],
                "Canada" => ['h12','a12']
            ],
            "T13" => [
                "Q_num" => 13,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T14" => [
                "Q_num" => 14,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T15" => [
                "Q_num" => 15,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T16" => [
                "Q_num" => 16,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T17" => [
                "Q_num" => '',
                "USA" => ['h17','a5','c5'],
                "Singapore" => ['h17'],
                "Hongkong" => ['h17'],
                "Canada" => ['h17','a17']
            ],
            "T18" => [
                "Q_num" => '',
                "USA" => ['h18','a6','c6'],
                "Singapore" => ['h18'],
                "Hongkong" => ['h18'],
                "Canada" => ['h18','a18']
            ],
            "T19" => [
                "Q_num" => '',
                "USA" => ['h19','a7','c7'],
                "Singapore" => ['h19'],
                "Hongkong" => ['h19'],
                "Canada" => ['h19','a19']
            ],
            "T20" => [
                "Q_num" => 20,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T21" => [
                "Q_num" => '',
                "USA" => ['h21', 'a9', 'c9'],
                "Singapore" => ['h21'],
                "Hongkong" => ['h21'],
                "Canada" => ['h21','a21']
            ],
            "T21_wc" => [
                "Q_num" => '_wc',
                "USA" => ['h21'],//missing ,'a','c'
                "Singapore" => ['h21'],
                "Hongkong" => ['h21'],
                "Canada" => ['h21','a21']
            ],
            "T21_wc_RTs" => [
                "Q_num" => '_wc_RTs',
                "USA" => ['h21'],//missing ,'a','c'
                "Singapore" => ['h21'],
                "Hongkong" => ['h21'],
                "Canada" => ['h21','a21']
            ],
            "T22" => [
                "Q_num" => 22,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T23" => [
                "Q_num" => 23,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T24" => [
                "Q_num" => 24,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T25" => [
                "Q_num" => 25,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T26" => [
                "Q_num" => 26,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T27" => [
                "Q_num" => 27,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T27_6_wc" => [
                "Q_num" => '_6_wc',
                "USA" => ['h27'],//missing ,'a','c'
                "Singapore" => ['h27'],
                "Hongkong" => ['h27'],
                "Canada" => ['h27','a27']
            ],
            "T27_6_wc_RTs" => [
                "Q_num" => '_6_wc_RTs',
                "USA" => ['h27'],//missing ,'a','c'
                "Singapore" => ['h27'],
                "Hongkong" => ['h27'],
                "Canada" => ['h27','a27']
            ],
            "T27_7" => [
                "Q_num" => '_7',
                "USA" => ['h27'],//missing ,'a','c'
                "Singapore" => ['h27'],
                "Hongkong" => ['h27'],
                "Canada" => ['h27','a27']
            ],
            "T28" => [
                "Q_num" => 28,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T29" => [
                "Q_num" => '',
                "USA" => ['h29', 'a11', 'c11'],
                "Singapore" => ['h29'],
                "Hongkong" => ['h29'],
                "Canada" => ['h29','a29']
            ],
            "T30" => [
                "Q_num" => '',
                "USA" => ['h30', 'a12', 'c12'],
                "Singapore" => ['h30'],
                "Hongkong" => ['h30'],
                "Canada" => ['h30','a30']
            ],
            "T31" => [
                "Q_num" => 31,
                "USA" => ['h'],//missing ,'a','c'
                "Singapore" => ['h'],
                "Hongkong" => ['h'],
                "Canada" => ['h','a']
            ],
            "T32" => [ //B1 Q1
                "Q_num" => '',
                "USA" => ['ii1', 'a3', 'c3'],
                "Singapore" => ['ii1','i1'],
                "Hongkong" => ['ii1'],
                "Canada" => ['ii1','i1']
            ],
            "T33" => [ //B2 Q2
                "Q_num" => '',
                "USA" => ['ii2', 'a14', 'c14'],
                "Singapore" => ['ii2','i2'],
                "Hongkong" => ['ii2'],
                "Canada" => ['ii2','i2']
            ],
            "T34" => [ //B3 ACT
                "Q_num" => '',
                "USA" => ['b3', 'a15', 'c15'],
                "Singapore" => ['b3','i3'],
                "Hongkong" => ['b3'],
                "Canada" => ['b3','i3']
            ],
            "T35" => [ //B4 ACT
                "Q_num" => '',
                "USA" => ['b4', 'a16', 'c16'],
                "Singapore" => ['b4','i4'],
                "Hongkong" => ['b4'],
                "Canada" => ['b4','i4']
            ],
            "T36" => [ //B5 ACT
                "Q_num" => 22, //number of primes
                "Q_limit" => 7, //max value of targets
                "USA" => ['b5'], //explicit a17 single, 'a17', 'c17'
                "Singapore" => ['b5'],//explicit 'i5' multiple
                "Hongkong" => ['b5'],
                "Canada" => ['b5']//explicit 'i5' multiple
            ],
            "T37" => [ //B6 ACT
                "Q_num" => '',
                "Q_limit" => 9, //number of primes
                "USA" => ['b6', 'a18', 'c18'],
                "Singapore" => ['b6','i6'],
                "Hongkong" => ['b6'],
                "Canada" => ['b6','i6']
            ],
            "T38" => [ //B7 ACT
                "Q_num" => 25,//number of primes
                "Q_limit" => 10, //number of primes
                "USA" => ['b7'], //explicit single
                "Singapore" => ['b7'],//explicit 'i5' multiple
                "Hongkong" => ['b7'],
                "Canada" => ['b7']//explicit 'i5' multiple
            ],
            "T39" => [ //B8 ACT
                "Q_num" => '',
                "Q_limit" => 20, //number of primes
                "Q_not" => 'b8',
                "USA" => ['b8', 'a19', 'c19'],//a22 c22 inverted
                "Singapore" => ['b8', 'i7'], //i11 inverted
                "Hongkong" => ['b8'],
                "Canada" => ['b8', 'i8']//i11 inverted
            ],
            "T40" => [ //B9 ACT
                "Q_num" => '',
                "Q_limit" => 15, //number of primes
                "USA" => ['b9', 'a20', 'c20'],//a22 c22 inverted
                "Singapore" => ['b9', 'i8'], //i11 inverted
                "Hongkong" => ['b9'],
                "Canada" => ['b9', 'i9']//i11 inverted
            ],
            "T41" => [ //B10 ACT
                "Q_num" => '',
                "Q_limit" => 14, //number of primes
                "Q_not" => 'b10',
                "USA" => ['b10', 'a22', 'c22'],//a22 c22 inverted
                "Singapore" => ['b10', 'i10'], //i11 inverted
                "Hongkong" => ['b10'],
                "Canada" => ['b10', 'i11']//i11 inverted
            ],
            "T42" => [ //K1
                "Q_num" => '',
                "Q_limit" => 18,
                "USA" => ['k1', 'a23', 'c23'],
                "Singapore" => ['y1', 'i11'],
                "Hongkong" => ['k1'],
                "Canada" => ['u1', 'i12']
            ],
            "T43" => [ //K2
                "Q_num" => '',
                "Q_limit" => 16,
                "USA" => ['k2', 'a24', 'c24'],
                "Singapore" => ['y2', 'i12'],
                "Hongkong" => ['k2'],
                "Canada" => ['u2', 'i13']
            ],
            "T44" => [ //K3
                "Q_num" => '',
                "Q_limit" => 10,
                "USA" => ['k3', 'a25', 'c25'],
                "Singapore" => ['y3', 'i13'],
                "Hongkong" => ['k3'],
                "Canada" => ['u3', 'i14']
            ],
            "T45" => [ //K4
                "Q_num" => '',
                "USA" => ['k4', 'a26', 'c26'],
                "Singapore" => ['y4', 'i14'],
                "Hongkong" => ['k4'],
                "Canada" => ['u4', 'i15']
            ],
            "T46" => [ //K5
                "Q_num" => '',
                "USA" => ['k5', 'a27', 'c27'],
                "Singapore" => ['y5', 'i15'],
                "Hongkong" => ['k5'],
                "Canada" => ['u5', 'i16']
            ],
            "T47" => [ //K6
                "Q_num" => '',
                "USA" => ['k6', 'a28', 'c28'],
                "Singapore" => ['y6', 'i16'],
                "Hongkong" => ['k6'],
                "Canada" => ['u6', 'i17']
            ]
        ];
    }
}

if (!function_exists('summaryKeys')) {
    function summaryKeys() {
        return [ //list of variables to process
            "T1" => [
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T3" => [
                "type" => 'average',
                "row" => 0,
                "col" => 0
            ],
            "T4" => [
                "type" => 'single',
                "row" => 0,
                "col" => 0
            ],
            "T5" => [
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T6" => [
                "type" => 'single',
                "row" => 5,
                "col" => 0
            ],
            "T7" => [
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T8" => [
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T9" => [
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T10" => [
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T12" => [
                "type" => 'multiple',
                "row" => 7,
                "col" => 0
            ],
            "T13" => [
                "type" => 'single',
                "row" => 0,
                "col" => 0
            ],
            "T14" => [
                "type" => 'single',
                "row" => 3,
                "col" => 0
            ],
            "T15" => [
                "type" => 'single',
                "row" => 0,
                "col" => 0
            ],
            "T16" => [
                "type" => 'percent',
                "row" => 4,
                "col" => 0
            ],
            "T17" => [
                "type" => 'single',
                "row" => 5,
                "col" => 0
            ],
            "T18" => [
                "type" => 'percent',
                "row" => 8,
                "col" => 0
            ],
            "T19" => [
                "type" => 'single',
                "row" => 3,
                "col" => 0
            ],
            "T20" => [
                "type" => 'single',
                "row" => 5,
                "col" => 0
            ],
            "T21" => [
                "type" => 'single',
                "row" => 11,
                "col" => 0
            ],
            "T22" => [
                "type" => 'single',
                "row" => 4,
                "col" => 0
            ],
            "T23" => [
                "type" => 'single',
                "row" => 0,
                "col" => 0
            ],
            "T24" => [
                "type" => 'single',
                "row" => 5,
                "col" => 0
            ],
            "T25" => [
                "type" => 'single',
                "row" => 5,
                "col" => 0
            ],
            "T26" => [
                "type" => 'multiple',
                "row" => 8,
                "col" => 0
            ],
            "T27" => [
                "type" => 'multiple',
                "row" => 6,
                "col" => 0
            ],
            "T28" => [
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T29" => [
                "type" => 'single',
                "row" => 0,
                "col" => 0
            ],
            "T30" => [
                "type" => 'single',
                "row" => 0,
                "col" => 0
            ],
            "T31" => [
                "type" => 'single',
                "row" => 3,
                "col" => 0
            ],
            "T32" => [ //B1 Q1
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T33" => [ //B2 Q2
                "type" => 'single',
                "row" => 0,
                "col" => 0
            ],
            "T34" => [ //B3 ACT
                "type" => 'multiple',
                "row" => 7,
                "col" => 5
            ],
            "T35" => [ //B4 ACT
                "type" => 'single',
                "row" => 13,
                "col" => 6
            ],
            "T36" => [ //B5 ACT
                "type" => 'single',
                "row" => 22,
                "col" => 6
            ],
            "T37" => [ //B6 ACT
                "type" => 'single',
                "row" => 9,
                "col" => 6
            ],
            "T38" => [ //B7 ACT
                "type" => 'single',
                "row" => 25,
                "col" => 11
            ],
            "T39" => [ //B8 ACT
                "type" => 'single',
                "row" => 12,
                "col" => 5
            ],
            "T40" => [ //B9 ACT
                "type" => 'single',
                "row" => 15,
                "col" => 3
            ],
            "T41" => [ //B10 ACT
                "type" => 'single',
                "row" => 14,
                "col" => 5
            ],
            "T42" => [ //K1
                "type" => 'multiple',
                "row" => 18,
                "col" => 0
            ],
            "T43" => [ //K2
                "type" => 'multiple',
                "row" => 16,
                "col" => 0
            ],
            "T44" => [ //K3
                "type" => 'multiple',
                "row" => 10,
                "col" => 0
            ],
            "T46" => [ //K5
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ],
            "T47" => [ //K6
                "type" => 'single',
                "row" => 2,
                "col" => 0
            ]
        ];
    }
}

if (!function_exists('baselineQuestions')) {
    function baselineQuestions()
    {
        return [ //list of variables to process
            "T1" => [
                "Question" => "Thank you for agreeing to take part in this survey. We highly recommend taking this survey on your laptop or desktop computer.If you take the survey in a single session, it should last about 25 minutes. However, if you are interrupted or wish to take a break, you can return to it later by clicking the same link. It will return you back to the section of the survey from where you left off.This survey is being carried out by Split Second Research, an independent research agency on behalf of Johnson & Johnson. Your responses will help Johnson & Johnson understand how its services can be improved. Please use the NEXT button below to navigate through this survey and note that using the back button on your browser will invalidate your responses.Select 'Yes' and press NEXT to continue.",
                "choices" => [
                    "rows" => ["Yes","No"],
                    "columns" => []
                ]
            ],
            "T2" => [
                "Question" => "Please indicate your name and office address",
                "choices" => [
                    "rows" => ["Name:","Email address:", "Office Address:"],
                    "columns" => []
                ]
            ],
            "T3" => [
                "Question" => "How many years have you been in active clinical practice?",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T4" => [
                "Question" => "What percent of your professional time is spent in the clinical care of your patients vs. teaching, research, etc.?",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T5" => [
                "Question" => "Are you, or any member of your family, employed by, or acting as an advisor to a pharmaceutical company, vision care manufacturer, or market research firm?",
                "choices" => [
                    "rows" => ["Yes, please list company/organisation name","No"],
                    "columns" => []
                ]
            ],
            "T6" => [
                "Question" => "Please indicate which of the following best describes the type of practice in which you currently spend most of your time.",
                "choices" => [
                    "rows" => ["Associated with a retail/optical chain","Private office not associated with a retail/optical chain","HMO / Hospital / Clinic", "General Practitioners Office", "Other"],
                    "columns" => []
                ]
            ],
            "T7" => [
                "Question" => "Which of the following best describes you in the practice where you currently spend most of your time? Please select one.",
                "choices" => [
                    "rows" => ["Self-employed","Employed by someone else (either another doctor, a practice group, or a corporation)"],
                    "columns" => []
                ]
            ],
            "T8" => [
                "Question" => "Is there an Ophthalmologist (MD) in your practice?",
                "choices" => [
                    "rows" => ["Yes","No"],
                    "columns" => []
                ]
            ],
            "T9" => [
                "Question" => "Does your office have a topographer?",
                "choices" => [
                    "rows" => ["Yes, please provide the manufacturer brand","No"],
                    "columns" => []
                ]
            ],
            "T10" => [
                "Question" => "Does your office have an axial length measurement instrument (e.g., biometer) ?",
                "choices" => [
                    "rows" => ["Yes, please provide the manufacturer brand","No"],
                    "columns" => []
                ]
            ],
            "T11" => [
                "Question" => "Please note, when you see the term “myopia management”, please refer to the following definition:
                Myopia management are clinical strategies used by eye care practitioners to address a patient's immediate refractive condition (i.e., correcting myopia) as well as the management of their condition long term (i.e., reducing the axial elongation of the eye).  Several therapies have been investigated and shown efficacy of over 0.3 mm (around 0.75 D) over two to three years including orthokeratology (Ortho-K), soft multifocal contact lenses, myopia control spectacles, and atropine. Growing evidence supports that more time outdoors may also slow the progression of myopia.",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T12" => [
                "Question" => "Which of the following are included in your set of responsibilities for patients between 5-18 years at your current place of work?",
                "choices" => [
                    "rows" => ["Performing eye exams or refraction to determine the patient’s prescription","Performing axial length measurements","Consulting with patients and their parents on myopia management treatment plans","Fitting patients with myopia management on or off label products (ortho-K, multifocal soft contacts or glasses, myopia control soft contacts or glasses, Atropine)","Recommending or deciding which type or myopia management products to handle/offer in the optical clinic","Referring to another office for myopia management treatment.","None of the above"],
                    "columns" => []
                ]
            ],
            "T13" => [
                "Question" => "In a typical month, how many patients between the ages of 5-18 years do you personally see?",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T14" => [
                "Question" => "Does this number change around the start of school or school break?",
                "choices" => [
                    "rows" => ["Yes, it goes up","Yes, it goes down","No change"],
                    "columns" => []
                ]
            ],
            "T15" => [
                "Question" => "During a typical month around the time of school or school break, how many patients between the ages of 5-18 years do you personally see?",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T16" => [
                "Question" => "For your patients between 5-18 years of age, please estimate the percent of patients that fall within each category. Must total to 100%",
                "choices" => [
                    "rows" => ["No treatment","Refractive only treatment hyperopia","Refractive only treatment myopia","Myopia management treatment"],
                    "columns" => []
                ]
            ],
            "T17" => [
                "Question" => "For your patients between 5-18 years of age, please estimate the percent of parents/patients you approach on the first visit to discuss myopia, its long-term risks, and pro-active treatment.",
                "choices" => [
                    "rows" => ["None","About 25%","About 50%","About 75%","Virtually all of the patients"],
                    "columns" => []
                ]
            ],
            "T18" => [
                "Question" => "Please estimate the % of patients between 5-18 years that you are treating for myopia management that fall within each category.
                Can add to more than 100% if patient falls in multiple categories",
                "choices" => [
                    "rows" => ["Ortho-K","Multifocal soft contact lenses (off label )","Myopia control soft contacts (e.g., MiSight)","Multifocal/PALs spectacles (off label)","Myopia control spectacles (e.g., Zeiss MyoVision, Hoya MiyoSmart, etc.)","Atropine (with or without combination of other treatment options)","Recommend behavior modification (e.g., more outdoor time)","Recommend eye exercises"],
                    "columns" => []
                ]
            ],
            "T19" => [
                "Question" => "What is your experience with fitting Ortho-K?",
                "choices" => [
                    "rows" => ["Fit 1-4 patients per month","Fit 5-8 patients per month","Fit more than 8 patients per month"],
                    "columns" => []
                ]
            ],
            "T20" => [
                "Question" => "How open are you to consider recommending / prescribing Orthokeratology (Ortho-K) to children aged 5-18?",
                "choices" => [
                    "rows" => ["Definitely will not consider","Probably will not consider","Might or might not consider","Probably will consider","Definitely will consider"],
                    "columns" => []
                ]
            ],
            "T21" => [
                "Question" => "What is your preferred Ortho-K brand for treatment with children?
                Please select one Ortho-K brand for treatment with children and please put reasons why you prefer this brand.",
                "choices" => [
                    "rows" => ["Euclid Emerald","Paragon CRT","Global-OK Vision","Wave","DreamLens","MoonLens","iSee","Contex E-OK","Abiliti Overnight","Other (Please Specify)","I do not fit Ortho-K for myopia management"],
                    "columns" => []
                ]
            ],
            "T22" => [
                "Question" => "What is your preferred soft lens for myopia management?",
                "choices" => [
                    "rows" => ["MiSight","NaturalVue","Other off label MF (Please specify)","I do not fit soft lenses for myopia management"],
                    "columns" => []
                ]
            ],
            "T23" => [
                "Question" => "Please indicate why [pipe answer of Q22] is your overall preferred soft lens:",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T24" => [
                "Question" => "How likely are you to develop or continue to develop your myopia management practice?",
                "choices" => [
                    "rows" => ["Extremely unlikely ","Unlikely","Unsure at this time","Likely","Extremely Likely"],
                    "columns" => []
                ]
            ],
            "T25" => [
                "Question" => "How likely are you to add new products and brands to your myopia management practice?",
                "choices" => [
                    "rows" => ["Extremely unlikely ","Unlikely", "Unsure at this time", "Likely", "Extremely Likely"],
                    "columns" => []
                ]
            ],
            "T26" => [
                "Question" => "What type of activities are you doing to help drive pediatric patients to your office?
                Select all that apply.",
                "choices" => [
                    "rows" => ["Paid Advertising efforts (e.g., on web, social media, etc.)","Direct to consumer advertising ","Creating a network of referrals by other professionals that interact with the pediatric population (e.g., pediatricians, dentists, etc.)","Rely on word of mouth through other patients","Practice Web Site Advertising (have a section on Myopia Control)","Internal efforts:  approach patients in my practice who are also parents","Other (Please specify)","None of the above"],
                    "columns" => []
                ]
            ],
            "T27" => [
                "Question" => "Have you ever participated in a myopia management training session, either in person or online, from the following manufacturers?  Please select all that apply.",
                "choices" => [
                    "rows" => ["CooperVision MiSight","Paragon CRT","Euclid Emerald","GP Specialists","Other (please specify) ","I have not participated in any of the above training sessions"],
                    "columns" => []
                ]
            ],
            "T28" => [
                "Question" => "Are you MiSight certified?",
                "choices" => [
                    "rows" => ["Yes","No"],
                    "columns" => []
                ]
            ],
            "T29" => [
                "Question" => "How many optometrists are there in your office in total (including yourself)? ",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T30" => [
                "Question" => "How many optometrists in your office are offering myopia management (including yourself)?",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T31" => [
                "Question" => "Please indicate your gender.",
                "choices" => [
                    "rows" => ["Male","Female","Prefer not to say"],
                    "columns" => []
                ]
            ],
            "T32" => [ //B1 Q1
                "Question" => "As before, when you see the term “myopia management”, refer to the following definition:
                    Myopia management are clinical strategies used by eye care practitioners to address a patient's immediate refractive condition (i.e., correcting myopia) as well as the management of their condition long term (i.e., reducing the axial elongation of the eye).  Several therapies have been investigated and shown efficacy of over 0.3 mm (around 0.75 D) over two to three years including orthokeratology (Ortho-K), soft multifocal contact lenses, myopia control spectacles, and atropine. Growing evidence supports that more time outdoors may also slow the progression of myopia.",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T33" => [ //B2 Q2
                "Question" => "How many patients between 5-18 years did you treat for myopia management over the last two weeks?",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T34" => [ //B3 ACT
                "Question" => "When treating your myopia management patients between 5-18 years, please indicate your overall preference for each of the following treatment options:",
                "choices" => [
                    "rows" => ["Ortho-K","Atropine in combination with other treatment","Off label multifocal/PAL spectacles","Off label multifocal soft Contacts (i.e., naturalVue)","Myopia control soft contacts with indication (e.g., MiSight, Abiliti 1-Day)","Atropine alone","Myopia control Spectacles (i.e., Zeiss MyoVision, Hoya MiyoSmart, etc.)"],
                    "columns" => ["Very Low Preference","Low Preferenc","Neither High nor Low preference","High Preference","Very High Preference"]
                ]
            ],
            "T35" => [ //B4 ACT
                "Question" => "When thinking about your myopia management patients between 5-18 years, please indicate your level of overall satisfaction with each of the following treatment options:",
                "choices" => [
                    "rows" => ["MiSight","naturalVue", "Euclid Emerald", "Paragon CRT", "iSee", "Multifocal/PALs Spectacles", "Atropine", "Abiliti Overnight", "B&L MOONLENS", "Zeiss MyoVision", "MiyoSmart (Hoya)", "Wave NighLens", "Other (Please specify)"],
                    "columns" => ["Not at all satisfied", "Not very satisfied", "Somewhat satisfied", "Very satisfied", "Extremely satisfied", "I do not have enough experience with this product to evaluate"]
                ]
            ],
            "T36" => [ //B5 ACT
                "Question" => "Which myopia management treatment option do you strongly associate with each characteristic listed below?  Please select as many or as few of the lenses that you associate with each attribute. If you do not feel any of the lenses perform strongly on a given attribute, please select the “none” option. Please be sure to select at least one item in each row.",
                "choices" => [
                    "rows" => ["Provides superior visual acuity","Provides superior visual experience (no visual artifacts like halos and ghosting)", "Provides superior patient comfort", "Is affordable for my patients (including pricing and rebates)", "Promotes superior eye health", "Provides UV protection", "Offers favorable margins", "Increases likelihood of patient loyalty", "Increases likelihood of patient compliance with recommended wear schedule", "Provides superior efficacy (treatment effect for slowing myopia progression)", "Resists visible, on-eye deposits throughout the wear cycle", "Is one of the best options for my patients between 5-12 years", "Is one of the best options for my patients between 13-18 years", "Provides patients with easy lens insertion and removal", "Provides superior safety", "Offers acceptable product performance for a better value", "Is a lens with which I can achieve a high rate of fit success", "s excellent for patients experiencing dryness", "Has an innovative optical design ", "Is a good choice for patients in need of parental assistance", "Is a good choice for my active patients (i.e., in sports and activities)", "Is a good choice for patients who manage lenses on their own (without parental assistance)"],
                    "columns" => ["MiSight", "Paragon CRT", "Abiliti Overnight", "Euclid Emerald", "Natural Vue", "NONE"]
                ]
            ],
            "T37" => [ //B6 ACT
                "Question" => "When treating your myopia management patients between 5-18 years, please indicate your overall preference for each of the following manufacturers of myopia management products (both on and off label).",
                "choices" => [
                    "rows" => ["Johnson & Johnson Vision","CooperVision", "Euclid", "Bausch & Lomb", "Visioneering Technologies", "GP Specialists", "Paragon", "Hoya", "Zeiss"],
                    "columns" => ["Very Low Preference", "Low Preference", "Neither High nor Low preference", "High Preference", "Very High Preference", "I do not have enough experience with this manufacturer to evaluate"]
                ]
            ],
            "T38" => [ //B7 ACT
                "Question" => "Which contact lens manufacturers do you strongly associate with each characteristic listed below as it pertains to your myopia management business?  Please select as many or as few of the manufacturers that you associate with each attribute. If you do not feel any of the manufacturers perform strongly on a given attribute, please select the “none” option.
                Please be sure to select at least one item in each row.",
                "choices" => [
                    "rows" => ["Provides innovative new products with the most advanced technology","Provides exceptional customer service that provides support and adds value to my practice", "Allows me to offer attractive product pricing to my patients", "Cares about the quality of the conversation and relationship I have with my patients", "Training on the fitting process is effective and helps build my confidence", "Is a scientific leader in vision care", "Provides tools that help both me and the patient/parent be most effective across the entire treatment journey", "Provides tools / programs that make me more effective at business management", "Provide tools / programs that help me to train myself and/or my staff on inclusion of myopia management into my practice", "Provides tools / products that educate me on the science and condition", "Provides efficient product ordering", "Allows me to maximize my profitability", "Is invested in my practice success", "Has exceptional clinical knowledge and consulting skills that help me build my Myopia Management practice", "Has clinical consultants that provide support and motivation", "Drives traffic to my practice through consumer advertising", "Provides training to support high quality patient/provider conversations", "Provides a complete offering of myopia management treatments to meet my patients’ individual eyecare needs", "Provides practical tools for strong patient/provider conversations that can be implemented in everyday practice ", "Fully understands the different needs of my paediatric patients", "Proactively strives to protect my interests in the Vision Care industry", "Is a leader in advancing eye health", "Helps me provide personalized care to my myopia management paediatric patients", "Is raising the standard of care for myopia management in paediatric patients", "Helping to change patients’ lives "],
                    "columns" => ["Johnson & Johnson Vision", "CooperVision", "Euclid", "Bausch & Lomb", "Visioneering Technologies", "GP Specialists", "Paragon", "Hoya", "Zeiss", "None"]
                ]
            ],
            "T39" => [ //B8 ACT
                "Question" => "How likely would you be to recommend the following to your patients and their parents? Please select one answer per product/brand.",
                "choices" => [
                    "rows" => ["MiSight","naturalVue", "Paragon CRT", "ACUVUE®Abiliti", "Euclid Emerald", "B&L MOONLENS", "Wave NighLens", "iSee", "Zeiss MyoVision", "MiyoSmart (Hoya)", "Multifocal/PALs Spectacles", "Atropine"],
                    "columns" => ["Definitely would not recommend", "Probably would not recommend", "Might or might not recommend", "Probably would recommend", "Definitely would recommend"]
                ]
            ],
            "T40" => [ //B9 ACT
                "Question" => "Please indicate if you agree or disagree with the following statements regarding myopia and myopia management:",
                "choices" => [
                    "rows" => ["It is important to use axial length as a means to monitor myopia progression","It is important to treat before it progresses", "It is best to wait to see if a patient progresses prior to treating", "Every diopter we can slow the progression of myopia matters", "It is important to provide a variety of treatment options for myopia management in my practice", "Pre-myopic children 12 and younger should be closely monitored as myopia onset is highly likely", "I prefer to wait until the parent requests information or treatment for myopia progression", "There is no safe level of myopia", "Children 12 and younger with myopia will progress and should be treated", "Myopia is chronic", "Myopia is progressive", "Myopia is a disease", "The progression of Myopia can be slowed with treatment", "Genetics is a risk factor for Myopia", "Lifestyle factors are risk factors for Myopia (e.g., like lack of outdoor activities and extensive near work) "],
                    "columns" => ["AGREE", "DISAGREE", "UNCERTAIN"]
                ]
            ],
            "T41" => [ //B10 ACT
                "Question" => "In general, how frequently are you able to do the following with your myopia patients and/or their parents?",
                "choices" => [
                    "rows" => ["Assess parents’ values and motivations for their child before providing myopia management recommendations. ","Assess a child’s current eye health in relation to their existing treatments, behaviors, and challenges they’re experiencing.  ", "Discuss the child’s eye health and eye care practices using easy to understand terms.  ", "Confirm the parent’s understanding of their child’s eye health and eye care practices.  ", "Discuss personally relevant and specific recommendations to improve the child’s behaviors and outcomes relative to their eye health. ", "Discuss myopia management recommendations in relation to the parents’ values and motivations.", "Discuss parent/child questions and concerns before agreeing on next steps to manage their myopia.", "Collaborate with the parent/child to determine achievable goals regarding myopia progression and next steps.   ", "Discuss barriers which might prevent the parent/child from achieving goals or taking action.  ", "Assist the parent/child with strategies to overcome barriers.  ", "When possible, show the parent/child how to perform eye health behaviors versus using written or verbal instruction only.  ", "Help the parent/child create an action plan around their goals and agreed upon recommendations.  ", "Provide the parent/child with relevant feedback and support during myopia treatment.", "Follow up with the parent/child throughout their myopia management plan.  "],
                    "columns" => ["Never", "Rarely", "Sometimes", "Almost Always", "Always"]
                ]
            ],
            "T42" => [ //K1
                "Question" => "As part of the parent conversation, please indicate if you include the following in your discussion. Select all that apply",
                "choices" => [
                    "rows" => ["the long-term risk of potential visual impairment due to a variety of eye diseases (i.e., retinal detachment, macular degeneration)","that these eye diseases present later in life (myopia is a silent epidemic)", "that myopia is chronic ",
                    "that myopia is progressive or gets worse over time ",
                    "that myopia is a disease",
                    "the need to monitor the length of the eye",
                    "the progression of myopia can be slowed with treatment",
                    "myopia cannot be cured or reversed ",
                    "the potential of avoiding thicker glasses",
                    "avoid using words like “blindness” ",
                    "the potential of avoiding a worsening prescription over time ",
                    "the potential to reduce a child’s future prescription as an adult",
                    "the potential to reduce the chances of developing high myopia (-5.00D or more)",
                    "Genetics is a risk factor for myopia",
                    "Lifestyle factors like lack of outdoor activities and extensive near work are risk factors for myopia",
                    "Use the word “prescribe” when talking about the treatment option (i.e., I am “prescribing” product “X” to treat your child’s myopia)",
                    "Use the word “recommend” when talking about the treatment option (i.e., I am “recommending” product “X” to treat your child’s myopia)",
                    "None of the above"],
                    "columns" => []
                ]
            ],
            "T43" => [ //K2
                "Question" => "Please indicate the top 3 things that are most limiting your practice. Select only up to 3 items. Label them 1, 2, and 3.",
                "choices" => [
                    "rows" => [
                        "Cost of treatment for the patient is too high",
                        "Lack of interest in managing childhood myopia ",
                        "Minimal financial incentive",
                        "Insufficient time for professional development",
                        "Concern over medic-legal aspects of interventions " ,
                        "Need to purchase additional clinical equipment",
                        "Lack of experience in providing clinical care to children",
                        "Insufficient support from workplace",
                        "Lack of regulatory approval of interventions for slowing myopia progression",
                        "Insufficient consultation time",
                        "Lack of high-quality evidence to confirm safety of interventions",
                        "Lack of high-quality evidence to demonstrate efficacy of interventions",
                        "Absence of clinical guidelines to guide management",
                        "Consultation and chair time are too high",
                        "Difficulty finding and retaining staff to support the offices myopia management efforts",
                        "Lack of training from manufacturers"
                    ],
                    "columns" => []
                ]
            ],
            "T44" => [ //K3
                "Question" => "To better support your current/future myopia management practice, please rank order which tools would be most valuable for you. Rank 1 as the most valuable and 10 as the most unvaluable. ",
                "choices" => [
                    "rows" => [
                        "Parent facing brochures",
                        "Child-friendly brochures",
                        "In-office promotional video",
                        "Fitting guides",
                        "Time with fitting consultant",
                        "How to guide on managing conversation for myopia management with patients and parents",
                        "Communication tools to help patient/parent conversations",
                        "How to guide on structuring practice fees for your myopia management patients",
                        "Staff training education materials",
                        "Other myopia management practice tools",
                    ],
                    "columns" => []
                ]
            ],
            "T45" => [ //K4
                "Question" => "Here’s a reminder of the definition of Myopia.
                Please read the description thoroughly:
                Myopia is a chronic, progressive disease characterized by excessive eye elongation, risk of associated sight-threatening complications, such as retinal detachment and macular degeneration, and a negative refractive error, which means that vision correction is required.  
                Experts have identified Myopia as the biggest eye health threat of the 21st Century. By 2050, half of the world’s population will be Myopic, and 1 billion people worldwide will have high Myopia (-5.00 refractive error or higher). 70% of those with high Myopia will develop retinal disease with an increased risk of blindness. Those in the first wave of increased prevalence are just now starting to experience these complications.
                From an early age, measuring and monitoring both eye growth in length and refractive error is highly recommended in all children with even moderate risk of Myopia progression.  There are some treatments available that can slow the progression of Myopia.  For every diopter (-1.00) we can slow the progression of a child’s Myopia their chance of retinal disease decreases by 40%.",
                "choices" => [
                    "rows" => [],
                    "columns" => []
                ]
            ],
            "T46" => [ //K5
                "Question" => "Please indicate if you generally agree or disagree with the above definition",
                "choices" => [
                    "rows" => ["Agree with the definition","Disagree with the definition"],
                    "columns" => []
                ]
            ],
            "T47" => [ //K6
                "Question" => "Do you believe that the definition over exaggerates the problem?",
                "choices" => [
                    "rows" => ["Yes","No"],
                    "columns" => []
                ]
            ]
        ];
    }
}