<?php 
session_start();
define("UPLOAD_DIR", "upload/");
define("IMG_DIR", "img/");

require_once("db/database.php");
$db = new DtabaseHelper("localhost","root","","climbing_app_db");
require_once("utils/functions.php");
