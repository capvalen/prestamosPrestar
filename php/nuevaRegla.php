<?php 
require("conkarl.php");

$sql="INSERT INTO `vistas`(`id`, `idPrestamo`, `idUsuario`, `ver`, `activo`) VALUES (null, {$_POST['idPrestamo']}, {$_POST['usuario']}, {$_POST['regla']}, 1)";
//echo $sql;
if($resultado=$cadena->query($sql)){
	echo 'ok';
}else{
	echo 'error';
}

?>