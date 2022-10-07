<?php
require_once("bootstrap.php");
$boulders = $db->getFantaBouldersAndTracciatore();
$body =  '<div class="container"><div class="row justify-content-between">';
foreach($boulders as $b){
    $elem = "
    <div class='col-3'>
        <div id=card_${b["id"]} class='card' style='width: 18rem; border-color: black; border-radius:5px;'>
            <img src='img/${b["img"]}' class='card-img-top w-50 align-self-center' alt='...'>    
            <div class='card-body'>
                <h5 class='card-title'>${b["name"]}</h5>
                <p class='card-text'>Grade: ${b["grade"]} </p>
                <p class='card-text'>Date: ${b["date"]} </p>
                <p class='card-text'>Setter: ${b["tracciatore"]} </p>
                
                <div class='d-flex'>
                    <button type='button'  onclick=approve(${b["id"]}) class='btn btn-primary mb-2 '>Approve Boulder</button>
                    <button type='button' onclick= deny(${b["id"]}) class='btn btn-danger mb-2'>Don't Approve</button>
                </div>
            </div>
        </div>
    </div>
    ";
    $body = $body    . $elem;

}
$body = $body . "</div></div>";
$template_param["body"] = $body;
require("./template/approve-boulder-page-template.php");

