<?php
 require_once("bootstrap.php");
// function storeImgFromData($data){
//     $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
//     file_put_contents("img/img.jpeg", $data);
// }
// $json = file_get_contents('php://input');
// $data = json_decode($json,true);
// storeImgFromData($data["img"]);

// die;



if(isset($_GET["img"]) ){
    $src = IMG_DIR . $_GET['img'];
    if(file_exists($src)){
        header("Content-type: application/json");
        //readfile(IMG_DIR . $_GET['img']);
        //$base64img = "data:image/jpeg;base64,".base64_encode(file_get_contents($src));
        $base64img = base64_encode(file_get_contents($src));
        echo json_encode(array("img" => $base64img),JSON_UNESCAPED_SLASHES);
    } else {
        echo "not found";
    }
    die;
} 

if(isset($_GET["method"]) || isset($_POST["method"])){
    if($_GET["method"]=="dump"){
        echo  $db->dumpDbToJson();
        die;
    }
    if(($_GET["method"] == "login") && isset($_GET["username"]) && isset($_GET["password"])){
       $res =  $db->checkUser($_GET["username"],$_GET["password"]);
       if(sizeof($res) == 1){
            echo json_encode(array("status"=>"login success","id"=>$res[0]["id"]),JSON_UNESCAPED_SLASHES);
       }else {
            echo json_encode(array("status"=>"login failure"),JSON_UNESCAPED_SLASHES);
        };
        die;
    } 
    if(($_GET["method"] == "registration") && isset($_GET["username"]) && isset($_GET["password"]) && isset($_GET["firstname"]) && isset($_GET["lastname"])){
        $db->registerUser($_GET["username"],$_GET["password"],$_GET["firstname"],$_GET["lastname"]);
        die;
    }
    
    if( $_GET["method"]=="debug"){
        $db->insertFantaBoulder("fasfafasf","7b","2022-12-06",0,"immaginasae",1);
    }

    die;
}


?>