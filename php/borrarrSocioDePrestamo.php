<?php
require("conkarl.php"); 

date_default_timezone_set('America/Lima');

$sql="DELETE FROM `involucrados` WHERE 
`idPrestamo` = {$_POST['prestamo']} and `idCliente` = {$_POST['socio']}; ";
$resultado=$cadena->query($sql);
if($resultado){
	echo 'ok';
}else{
	echo "error";
}
?>