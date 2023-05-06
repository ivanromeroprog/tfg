<?php

namespace App\Helpers;

class ArraysHelper
{
    public static function shuffle(&$array, $semilla) {
        mt_srand($semilla);
        $keys = array_keys($array);
        shuffle($keys);
        
        $result = array();
        foreach ($keys as $key) {
            $result[$key] = $array[$key];
        }
        // dump($result);
        // array_multisort($result);
        $array = $result;
    }
}