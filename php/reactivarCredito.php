<?php 
require("conkarl.php");
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();

$idPrestamo = $base58->decode($_POST['idPrestamo']);

$sql="UPDATE `prestamo` SET `presAprobado` = '1' WHERE `idPrestamo` = '{$idPrestamo}';";
//echo $sql;

if($cadena->query($sql)){ 
	echo 'ok';
}
?>