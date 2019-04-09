<?php 
header('Content-Type: text/html; charset=utf8');
require("conkarl.php");


$sql= "UPDATE `caja` SET `cajaMoneda` = '{$_POST['moneda']}' WHERE `caja`.`idCaja` = {$_POST['caja']};";
//echo $sql;

if ($conection->query($sql)) { //Ejecución mas compleja con retorno de dato de sql del procedure.
	/* obtener el array de objetos */
	echo true;
}else{echo false;}



?>