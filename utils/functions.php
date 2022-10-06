<?php

    function dataToJson($data){
        // $json_row = "{";
        // foreach ($data as $row){
        //     foreach($row as $key => $val){
        //         $json_row = $json_row . strval($key) . ":" . strval($val) ;
        //     }
        // }
        // $json_row = $json_row . "}";
        // return $json_row;
        $json = json_encode($data);
        return $json;
    }

 
?>