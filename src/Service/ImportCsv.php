<?php


namespace App\Service;


class ImportCsv
{
    public function import($str, $imperative, $facultative = [])
    {
        $result = [];
        $array = explode("\r\n", $str);
        $array[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $array[0]);
        $arr_0 = explode(",", $array[0]);

        foreach ($imperative as $key => $item) {
            $name = $item;
            $$name = array_search($item, $arr_0);
        }
        foreach ($facultative as $key => $item) {
            $name = $item;
            $$name = array_search($item, $arr_0);
        }
        foreach ($array as $str) {
            $insert = [];
            $e = true;
            $arr = explode(",", $str);
            foreach ($imperative as $item) {
                if (isset($arr[${$item}]) && strlen($arr[${$item}])) {
                    $insert[$item] = $arr[${$item}];
                } else {
                    $e = false;
                    break;
                }
            }
            if ($e) {
                foreach ($facultative as $item) {
                    if (isset($arr[${$item}]) && strlen($arr[${$item}])) {
                        $insert[$item] = $arr[${$item}];
                    }
                }
                $result[] = $insert;

            }
        }
        $reverse_result = array_reverse($result);
        array_pop($reverse_result);
        return array_reverse($reverse_result);
    }
}