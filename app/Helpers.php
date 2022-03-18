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
            dd($vars);
            if (isset($url_data[$vars[$i].$num])) {
                $val = $url_data[$vars[$i].$num];
                $isset = true;
            }
            $i++;
        } while ($isset == false || $i < count($vars));
        return $val;
    }
}