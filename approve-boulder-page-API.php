<?php 

require_once("bootstrap.php");
if(isset($_GET["method"])){
    if($_GET["method"] == "deny_problem" && isset($_GET["fantaboulder_id"])){
        $img = $db->getFantaboulder($_GET["fantaboulder_id"])[0]["img"];
        unlink(IMG_DIR.$img);
        $db->removeFantaBoulder($_GET["fantaboulder_id"]);
        die;
    } else if($_GET["method"]=="approve_problem" && isset($_GET["fantaboulder_id"])){
        $db->promoveFantaboulderToBoulder($_GET["fantaboulder_id"]);
    } 
}
