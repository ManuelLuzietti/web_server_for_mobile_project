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

     function storeImgFromData($data,$fileName){
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
        file_put_contents("img/".$fileName, $data);
    }

    function generateImgName($id){
        return "img".uniqid('uploaded-', true).".jpeg";
    }
 
?>