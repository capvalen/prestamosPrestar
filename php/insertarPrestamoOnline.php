<?php
header('Content-Type: text/html; charset=utf8');
date_default_timezone_set('America/Lima');
include 'conkarl.php';
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();


$fecha = new DateTime($_POST['fDesembolso']);

$feriados = include "feriadosProximos.php";
$monto = $_POST['monto'];

$saldo = $_POST['monto'];
$saltoDia = new DateInterval('P1D'); //aumenta 1 día
$tasa = $_POST['tasaInt']/100;
$meses =  $_POST['periodo'];

switch ($_POST['modo']){
	case "1": //DIARIO
		$intervalo = new DateInterval('P1D'); //aumenta 1 día
		$plazo = $_POST['periodo']*30;
		break;
	case "2": //SEMANAL
		$intervalo = new DateInterval('P1W'); //aumenta 1 semana
		$plazo = $_POST['periodo']*4;
		break;
	case "4": //QUINCENAL
		$intervalo = new DateInterval('P14D'); //aumenta 15 días
		$plazo = $_POST['periodo']*2;
		break;
	case "3": //MENSUAL
		$intervalo = new DateInterval('P30D'); //aumenta 30 día
		$plazo = $_POST['periodo']*1	;
		break;
	default:
	?> <tr><td>Datos inválidos</td></tr><?php
	break;
}
$interes = $monto * $tasa * $meses;
$pagoTotal  = $monto+ $interes;

$capitalPartido = round($monto/$plazo,1, PHP_ROUND_HALF_UP);
$cuota = round( $pagoTotal/$plazo ,1, PHP_ROUND_HALF_UP);
$intGanado = round( $interes/ $plazo ,1, PHP_ROUND_HALF_UP);


$sql="INSERT INTO `prestamo`(`idPrestamo`, `presFechaAutom`, `presFechaDesembolso`, `presPeriodo`, `preInteresPers`,`presMontoDesembolso`, `idTipoPrestamo`, `presActivo`, `idUsuario`, `preSaldoDebe`) VALUES (null, now(), '0000-00-00 00:00:00', {$_POST['periodo']}, {$_POST['tasaInt']},{$_POST['monto']}, {$_POST['modo']}, 1, {$_COOKIE['ckidUsuario']}, {$pagoTotal} );"; 

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

//Para saber si es sábado(6) o domingo(0):  format('w') 

$lista1= '[{
	"numDia": 0,
	"fPago": "'.$fecha->format('Y-m-d').'",
	"razon": "Desembolso",
	"cuota": 0,
	"interes": 0,
	"amortizacion": 0,
	"saldo": '.$monto.',
	"saldoReal": 0
	}]';
$jsonSimple= json_decode($lista1, true);



$interesSumado=0;
$fecha->add($intervalo);

//$cuota = round($monto*$interes/$plazo,2);
for ($i=0; $i < $plazo ; $i++) {

	
	$razon = esFeriado($feriados, $fecha->format('Y-m-d'));
	if($razon!=false  && $_POST['modo']!=3 ){
		//echo "si es feriado";
		//echo "Feriado ".": ". $fecha->format('d/m/Y'). "<br>";
		$i--;
		$jsonSimple[]=array(
			"numDia"=>'-',
			"fPago" => $fecha->format('Y-m-d'),
			"razon" => 'Feriado',
			"cuota" => $razon,
			"interes"=> '',
			"amortizacion"=> '',
			"saldo" => '',
			"saldoReal"=> ''
		);
		$fecha->add($saltoDia);
	}else{
		//echo "no es feriado";
		if( $fecha->format('w')=='0' && $_POST['modo']!=3 ){
			//No hacer nada
			//echo "\nDomingo ".": ". $fecha->format('d/m/Y'). "<br>\n";
			$i--;
			$jsonSimple[]=array(
				"numDia"=>'-',
				"fPago" => $fecha->format('Y-m-d'),
				"razon" =>'Domingo',
				"cuota" => '',
				"interes"=> '',
				"amortizacion"=> '',
				"saldo" => '',
				"saldoReal"=> ''
			);
			$fecha->add($saltoDia);
		// }else if($fecha->format('w')=='6'){ 
		// 	//echo "Sábado ".": ". $fecha->format('d/m/Y'). "<br>";  ---------SI SE CUENTAN SABADOS EN ESTE SISTEMA---------
		// 	$i--;
		}else{

			$interesVariable= round($saldo * $interes, 1, PHP_ROUND_HALF_UP);
			$amortizacion = round($cuota-$interesVariable, 1, PHP_ROUND_HALF_UP);
			$saldo = $saldo - $cuota;
			$interesSumado+=$interesVariable;

			$jsonSimple[]=array(
				"numDia"=>$i+1,
				"fPago" => $fecha->format('Y-m-d'),
				"razon" =>'',
				"cuota" => $cuota,
				"interes"=> $interesVariable,
				"amortizacion"=> $amortizacion,
				"saldo" => $saldo,
				"saldoReal"=> 0
			);
			//echo "Día #".($i+1).": ". $fecha->format('d/m/Y') . "<br>";
			$fecha->add($intervalo);
			//echo $sql;
			
			//unset($conection);
			
		
		}
	}

}
//echo $sqlCuotas;



$saldo= round($pagoTotal, 1, PHP_ROUND_HALF_UP);
$dia=1;
for ($j=0; $j <  count($jsonSimple) ; $j++) {
	
	$nueva= new DateTime ($jsonSimple[$j]['fPago']);

	if($jsonSimple[$j]['razon']==='Desembolso'){
		$sqlCuotas=$sqlCuotas."INSERT INTO `prestamo_cuotas`(`idCuota`, `idPrestamo`, `cuotFechaPago`, `cuotCuota`, `cuotFechaCancelacion`, `cuotPago`, `cuotSaldo`, `cuotVo`, `cuotObservaciones`,`idTipoPrestamo`) VALUES (null,$idPrestamo,'{$nueva->format('Y-m-d')}',0,'0000-00-00',0,{$saldo},'0','', 43);";
	}else if($jsonSimple[$j]['razon']==='Domingo'){ $dia++;
	}else if($jsonSimple[$j]['razon']==='Feriado'){ $dia++;
	}else{
		if($j>=1){
			$saldo = $saldo-$cuota; $dia=1;
		}
		$sqlCuotas=$sqlCuotas."INSERT INTO `prestamo_cuotas`(`idCuota`, `idPrestamo`, `cuotFechaPago`, `cuotCuota`, `cuotFechaCancelacion`, `cuotPago`, `cuotSaldo`, `cuotVo`, `cuotObservaciones`,`idTipoPrestamo`) VALUES (null,$idPrestamo,'{$nueva->format('Y-m-d')}',{$cuota},'0000-00-00',0,{$saldo},'0','', 79);";
	}

}

//echo $sqlCuotas;
$cadena->multi_query($sqlCuotas);


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