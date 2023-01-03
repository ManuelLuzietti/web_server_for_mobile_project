<?php
 require_once("bootstrap.php");
// function storeImgFromData($data){
//     $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
//     file_put_contents("img/img.jpeg", $data);
// }
 $json = file_get_contents('php://input');
 $data = json_decode($json,true);
 if(isset($data["method"]) ){
    if($data["method"]=="upload_fanta_boulder" && isset($data["name"]) && isset($data["grade"])&&isset($data["date"])&&$data["img"]&&isset($data["user_id"])){
       // echo json_encode(array("status"=>"ok"),JSON_UNESCAPED_SLASHES);
       $imgName = generateImgName($data["user_id"]);
       storeImgFromData($data["img"],$imgName);
       $db->insertFantaBoulder($data["name"],$data["grade"],$data["date"],$imgName,$data["user_id"]);
       echo json_encode(array("result"=>"success"));
       die;
    }
 }  
 //
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
            echo json_encode(array("status"=>"login success","id"=>$res[0]["id"],"first_name"=>$res[0]["first_name"],"last_name"=>$res[0]["last_name"],"username"=>$res[0]["username"]),JSON_UNESCAPED_SLASHES);
       }else {
            echo json_encode(array("status"=>"login failure"),JSON_UNESCAPED_SLASHES);
        };
        die;
    } 
    if(($_GET["method"] == "registration") && isset($_GET["username"]) && isset($_GET["password"]) && isset($_GET["firstname"]) && isset($_GET["lastname"])){
        echo $db->registerUser($_GET["username"],$_GET["password"],$_GET["firstname"],$_GET["lastname"]);
        die;
    }
    
    if($_GET["method"]=="check_boulder_name" && isset($_GET["boulder_name"])){
        echo $db->checkBoulderName($_GET["boulder_name"]);
        die;
    }

    if($_GET["method"]=="update_firstname" && isset($_GET['firstname']) && isset($_GET['id'])){
        $result = $db->updateFirstname($_GET["firstname"],$_GET['id']);
        if($result){
            echo "update success firstname";
            
        } else {
            echo "update fail firstname";
        }
        die;
    }
    if($_GET["method"]=="update_lastname" && isset($_GET['lastname']) && isset($_GET['id'])){
        $result = $db->updateLastname($_GET["lastname"],$_GET['id']);
        if($result){
            echo "update success lastname";
            
        } else {
            echo "update fail lastname";
        }
        die;
    }
    if($_GET["method"]=="update_username" && isset($_GET['username']) && isset($_GET['id'])){
        if(!$db->checkUsername($_GET["username"])){
            echo "username already taken";
            die;
        }
        $result = $db->updateUsername($_GET["username"],$_GET['id']);
        if($result){
            echo "update success username";
            
        } else {
            echo "update fail username";
        }
        die;
    }

    if($_GET["method"]=="insert_completedboulder" && isset($_GET["text"]) && isset($_GET["rating"]) && isset($_GET["grade"]) && isset($_GET["user_id"]) && isset($_GET["date"]) && isset($_GET["boulder_id"]) && isset($_GET["number_of_tries"])){
        $commentId = $db->insertComment($_GET["text"],$_GET['rating'],$_GET["grade"],$_GET["user_id"]);
        $res = $db->insertCompletedBoulder($commentId,$_GET["date"],$_GET['user_id'],$_GET['boulder_id'],$_GET["number_of_tries"]);
        if($res){
            echo "success";
        } else {
            $db->deleteComment($commentId);
            echo "failure";
        }
        die;
    }


    //if($_GET["method"]=="upload_fanta_boulder" ){
    //    return  json_encode(array("status"=>"prova"),JSON_UNESCAPED_SLASHES);
    //    die;
    //}   
    
    if( $_GET["method"]=="debug"){
        $db->insertFantaBoulder("fasfafasf","7b","2022-12-06",0,"immaginasae",1);
        die;
    }

    die;
}



?>