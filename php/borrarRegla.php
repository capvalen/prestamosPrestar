<?php 
require("conkarl.php");

$sql="UPDATE `vistas` SET `activo`=0 WHERE idPrestamo={$_POST['idPrestamo']} and idUsuario = {$_POST['usuario']}; ";
//echo $sql;
if($resultado=$cadena->query($sql)){
	echo 'ok';
}else{
	echo 'error';
}

?>