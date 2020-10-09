<?php 
require("conkarl.php");

switch ($_POST['modo']){
	case "1": //DIARIO
		$intervalo = new DateInterval('P1D'); //aumenta 1 día
		break;
	case "2": //SEMANAL
		$intervalo = new DateInterval('P1W'); //aumenta 1 semana
		break;
	case "4": //QUINCENAL
		$intervalo = new DateInterval('P15D'); //aumenta 15 días
		break;
	case "3": //MENSUAL
		$intervalo = new DateInterval('P1M'); //aumenta 30 día
		break;
	default:
		break;
}
$fechaNueva = new DateTime($_POST['fechaNueva']);
$fecha=$fechaNueva;
$sqlCuotasFechas='';


$sqlFaltan="SELECT * FROM `prestamo_cuotas` where idPrestamo='{$_POST['idPrestamo']}' and idTipoPrestamo in (33, 79)";
//echo $sqlFaltan."\n";
$resultadoFaltan=$cadena->query($sqlFaltan);

while($rowFaltan=$resultadoFaltan->fetch_assoc()){ 
	$sqlCuotasFechas=$sqlCuotasFechas."UPDATE `prestamo_cuotas` SET `cuotFechaPago`='". $fecha->format("Y-m-d") ."' WHERE `idCuota`=". $rowFaltan['idCuota'] ."; ";
	//echo $."\n";
	$fecha->add($intervalo);
}

$sqlCuotasFechas=$sqlCuotasFechas."UPDATE `prestamo` SET `idTipoPrestamo` = '{$_POST['modo']}' WHERE `prestamo`.`idPrestamo` = '{$_POST['idPrestamo']}';";

if($preferido->multi_query($sqlCuotasFechas)){
	echo 'ok';
}

?>