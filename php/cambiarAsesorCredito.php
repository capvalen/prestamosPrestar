<?php 
header('Content-Type: text/html; charset=utf8');
require("conkarl.php");


$sql= "UPDATE `prestamo` SET `idUsuario`='{$_POST['idAsesor']}'
WHERE `idPrestamo`='{$_POST['codPrest']}'; ";
//echo $sql;

if ($conection->query($sql)) { //Ejecución mas compleja con retorno de dato de sql del procedure.
	echo true;
}else{ echo false; }



?>