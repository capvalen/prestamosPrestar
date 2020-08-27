<?php 
include 'conkarl.php';

if( $_POST['mora']=='' ){
	$sql="UPDATE `prestamo` SET 
	`preMoraFija`= 0,
	`preMoraFecha` = null
	where `idPrestamo` = {$_POST['idPrestamo']} ";
}else{
	$sql="UPDATE `prestamo` SET 
	`preMoraFija`= {$_POST['mora']},
	`preMoraFecha` = CURRENT_DATE()
	where `idPrestamo` = {$_POST['idPrestamo']} ";
}

$resultado=$cadena->query($sql);
echo "ok";
?>