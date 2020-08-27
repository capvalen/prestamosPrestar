<?php 
include 'conkarl.php';

$sql="UPDATE `prestamo` SET 
`preMoraFija`= {$_POST['mora']},
`preMoraFecha` = CURRENT_DATE()
where `idPrestamo` = {$_POST['idPrestamo']} ";
$resultado=$cadena->query($sql);
echo "ok";
?>