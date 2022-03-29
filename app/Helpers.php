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
                        if ($list[$url_data[$vars[$i].$num]]) {
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