<?php
include 'conkarl.php';

$idPrestamo = intval($_POST['idPrestamo']);
$proximaFecha = new DateTime($_POST['nuevaFecha']);
$proximoDia = $proximaFecha->format('d'); //solo extramos el día


$sqlPeriodos="SELECT * FROM `prestamo` where idPrestamo = {$idPrestamo} ; "; //
$resultadoPeriodos=$cadena->query($sqlPeriodos);
$rowPeriodos=$resultadoPeriodos->fetch_assoc();

$modo = $rowPeriodos['idTipoPrestamo'];
$periodo = $rowPeriodos['presPeriodo'];

switch ( $modo ){
	case "1": //DIARIO
		$intervalo = new DateInterval('P1D'); //aumenta 1 día
		$plazo = $periodo*30;
		break;
	case "2": //SEMANAL
		$intervalo = new DateInterval('P1W'); //aumenta 1 semana
		$plazo = $periodo*4;
		break;
	case "4": //QUINCENAL
		$intervalo = new DateInterval('P14D'); //aumenta 15 días
		$plazo = $periodo*2;
		break;
	case "3": //MENSUAL
		$intervalo = new DateInterval('P30D'); //aumenta 30 día
		$plazo = $periodo*1	;
		break;
	default:
		return "datos erroneos";
	break;

}

$i=0;


//consultando cuotas que faltan pagar:

$sqlPendiente="SELECT * FROM `prestamo_cuotas` where idPrestamo= {$idPrestamo}
and idTipoPrestamo in(33, 79)";
$resultadoPendiente=$cadena->query($sqlPendiente);
while($rowPendiente=$resultadoPendiente->fetch_assoc()){ 
	$fechita = $proximaFecha;
	
	$idCuota = $rowPendiente['idCuota'];

	$sql="UPDATE `prestamo_cuotas` SET `cuotFechaPago` = '{$fechita->format('Y-m-')}{$proximoDia}' WHERE `prestamo_cuotas`.`idCuota` = {$idCuota}; ";
	$resultado=$esclavo->query($sql);
	
	$i++;
	//echo "iteracion ".$i ;
	$proximaFecha = $fechita->add( $intervalo ) ;
}

echo json_encode(array('respuesta' => 'ok', 'mensaje' => "Reprogramado " . $i . " cuotas"));

?>