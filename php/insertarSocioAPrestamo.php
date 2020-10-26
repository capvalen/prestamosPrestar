<?php
require("conkarl.php"); 

date_default_timezone_set('America/Lima');

$sql="INSERT INTO `involucrados`(`idFila`, `idPrestamo`, `idCliente`, `idTipoCliente`) VALUES (null, {$_POST['prestamo']}, {$_POST['socio']}, 3)";
$resultado=$cadena->query($sql);
if($resultado){
	echo 'ok';
}else{
	echo "error";
}
?>