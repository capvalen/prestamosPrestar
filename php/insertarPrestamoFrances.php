<?php
header('Content-Type: text/html; charset=utf8');
date_default_timezone_set('America/Lima');
include 'conkarl.php';
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();
$feriados = include "feriadosProximos.php";


$hoy = new DateTime($_POST['fDesembolso']);
$diaProximo = $hoy->format('d');

$capital = $_POST['monto'];
$interes = $_POST['tasaInt']/100;
$periodo = $_POST['periodo'];

switch ($_POST['modo']){
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
		$plazo = $periodo*1;
		break;
	default:
	?> <tr><td>Datos inválidos</td></tr><?php
	break;
}

$capitalVivo = $capital;
$capitalAmortizado =0;
$sumInteres = 0; $sumAmortizacion=0; $sumCuotas=0; $sumPagoFinal=0; $sumComisiones =0;
$comServ = round(($capital * 0.01)/$plazo, 2);

$cuota = round( ( $interes* pow((1+$interes), $plazo) )* $capital / ( (pow(( 1 + $interes), $plazo)) -1), 2); //1, PHP_ROUND_HALF_UP
$cuotaReal = round($cuota + $comServ,1);

$sql="INSERT INTO `prestamo`(`idPrestamo`, `presFechaAutom`, `presFechaDesembolso`, `presPeriodo`, `preInteresPers`,`presMontoDesembolso`, `idTipoPrestamo`, `presActivo`, `idUsuario`, `preSaldoDebe`, `intSimple`) VALUES (null, CONVERT_TZ( NOW(),'US/Eastern','America/Lima' ) , '0000-00-00 00:00:00', {$_POST['periodo']}, {$_POST['tasaInt']}, {$_POST['monto']}, {$_POST['modo']}, 1, {$_COOKIE['ckidUsuario']}, {$capital}, b'1' );"; 


if($cadena->query($sql)){
	$idPrestamo = $cadena->insert_id;
}else{
	echo "hubo un error".$mysqli->errno;
}

 
$clientes=$_POST['clientes'];
$sqlClie='';
foreach ($clientes as $cliente ) {
	$sqlClie=$sqlClie . "INSERT INTO `involucrados`(`idPrestamo`, `idCliente`, `idTipoCliente`)
	VALUES ($idPrestamo, {$cliente['id']}, {$cliente['grado']});";
}

$esclavo->multi_query($sqlClie);

$sqlCuotas='';

$sqlCuotas=$sqlCuotas."INSERT INTO `prestamo_cuotas`(`idCuota`, `idPrestamo`, `cuotFechaPago`, `cuotCuota`, `cuotFechaCancelacion`, `cuotPago`, `cuotSaldo`, `cuotVo`, `cuotObservaciones`,`idTipoPrestamo`) VALUES (null,$idPrestamo,'{$hoy->format('Y-m-d')}',0,'0000-00-00',0, {$capital}, '0','', 43);";




for ($i=1; $i <= $plazo ; $i++) {
	$hoy->add($intervalo);
	if($_POST['fijar'] == 'si')
	$hoy->setdate($hoy->format('Y'), $hoy->format('m'), $diaProximo);

	$interesAmort = round($capitalVivo * $interes, 2, PHP_ROUND_HALF_UP );
	$amortizacion = round($cuota - $interesAmort, 2, PHP_ROUND_HALF_UP);
	$capitalVivo = round($capitalVivo - $amortizacion, 2, PHP_ROUND_HALF_UP );
	$capitalAmortizado += $amortizacion;
	$cuotaFinal = $cuota + $comServ;
	$sumCuotas+=$cuota;
	$sumInteres += $interesAmort;
	$sumAmortizacion += $amortizacion;
	$sumComisiones += $comServ;
	$sumPagoFinal += $cuotaFinal;


	$sqlCuotas=$sqlCuotas."INSERT INTO `prestamo_cuotas`(`idCuota`, `idPrestamo`, `cuotFechaPago`, `cuotCuota`, `cuotFechaCancelacion`, `cuotPago`, `cuotCapital`,`cuotInteres`, `cuotSaldo`, `cuotVo`, `cuotObservaciones`,`idTipoPrestamo`, `cuotSeg`) VALUES (null,$idPrestamo,'{$hoy->format('Y-m-d')}',{$cuota},'0000-00-00',0, {$capitalVivo}, {$interesAmort}, {$capitalAmortizado}, 0, '', 79, {$comServ} );";
}

//echo $sqlCuotas;
$cadena->multi_query($sqlCuotas);


ob_start();
	$_POST['idPrestamo'] = $idPrestamo;
	$_POST['usuario'] = $_COOKIE['ckidUsuario'];
	$_POST['regla'] = 2;
	include 'nuevaRegla.php';
ob_end_clean();

function esFeriado($feriados, $dia){
	foreach ($feriados as $llave => $valor) {
		if($valor["ferFecha"]==$dia){
			return $valor["ferDescripcion"]; break;
		}
	}
	return false;
}

$idEncrip = '000000'.$idPrestamo;
$idEncrip = substr($idEncrip, -7);
echo $base58->encode($idEncrip);

?>