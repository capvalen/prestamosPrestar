<?php 
include "variablesGlobales.php";
$ruta= "http:".$serverLocal."vendor/response.php"; 
echo $ruta;
$is_ok = require ($ruta); // returns true only if http response code < 400 
echo $is_ok;
?>