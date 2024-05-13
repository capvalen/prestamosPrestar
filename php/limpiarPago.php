<?php
require("conkarl.php"); 

$sql="UPDATE `prestamo_cuotas` SET `cuotFechaCancelacion` = '0000-00-00', `cuotPago` = '0', `idTipoPrestamo` = '79' WHERE `idCuota` = {$_POST['idCuota']};
";
$resultado=$cadena->query($sql);
if($resultado){
	echo 'ok';
}else{
	echo "error";
}
?>