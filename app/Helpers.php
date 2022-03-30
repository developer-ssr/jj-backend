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