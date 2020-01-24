<?php 
require 'variablesGlobales.php';
require("conkarl.php");
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();

$idPrestamo = $base58->decode($_POST['credito']);

$sql= "INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
VALUES (null,{$idPrestamo},0,89,now(),{$_POST['mora']},'',1,1,{$_COOKIE['ckidUsuario']});";
//echo $sql;
if ($conection->query($sql)) { //Ejecución mas compleja con retorno de dato de sql del procedure.

	echo true;
}else{echo mysql_error( $conection);}


 ?>