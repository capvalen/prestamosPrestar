<table class="table">
<thead>
	<th>Interés</th>
	<th>Total</th>
	<th>Plazo</th>
	<th>Cuota</th>
	<th>Seguro</th>
</thead>
<tbody>
<tr>

<?php
header('Content-Type: text/html; charset=utf8');
date_default_timezone_set('America/Lima');
include 'conkarl.php';

/* $idCliente='';
$log = mysqli_query($conection,"SELECT idCliente from Cliente where cliDNI ='".$_POST['jCliente'][0]['dniCli']."';");
$row = mysqli_fetch_array($log, MYSQLI_ASSOC);

// Primero creamos o verificamos si el cliente ya se encuentra en las BD;
if( count($row)===1 ){
	$idCliente=$row['idCliente'];
}else{
	$newCliente= "INSERT INTO `cliente`(`idCliente`, `cliApellidos`, `cliNombres`, `cliDni`, `cliDireccion`, `cliCorreo`, `cliCelular`, `cliFijo`, `cliCalificacion`) VALUES (null,'".$_POST['jCliente'][0]['apellidosCli']."','".$_POST['jCliente'][0]['nombreCli']."','".$_POST['jCliente'][0]['dniCli']."','".$_POST['jCliente'][0]['direccionCli']."','".$_POST['jCliente'][0]['correoCli']."','".$_POST['jCliente'][0]['celularCli']."','".$_POST['jCliente'][0]['cotroCelularCli']."',0)";
	$conection->query($newCliente);
	
	$log2 = mysqli_query($conection,"SELECT idCliente from Cliente where cliDNI ='".$_POST['jCliente'][0]['dniCli']."';");
	$row2 = mysqli_fetch_array($log2, MYSQLI_ASSOC);
	$idCliente=$row2['idCliente'];
} */

$fecha = new DateTime($_POST['fDesembolso']);

$feriados = include "feriadosProximos.php";
$monto = $_POST['monto'];

$saldo = $_POST['monto'];
$saltoDia = new DateInterval('P1D'); //aumenta 1 día
$tasa = $_POST['tasaInt']/100;
$meses =  $_POST['periodo'];



//Para saber si es sábado(6) o domingo(0):  format('w') 


$lista1= '[{
	"numDia": 0,
	"fPago": "'.$fecha->format('Y-m-d').'",
	"razon": "Desembolso",
	"cuota": 0,
	"interes": 0,
	"amortizacion": 0,
	"saldo": '.$saldo.',
	"saldoReal": 0
	}]';
$jsonSimple= json_decode($lista1, true);
	

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
		$plazo = $_POST['periodo']*1;
		break;
	default:
	?> <tr><td>Datos inválidos</td></tr><?php
	break;
}

$interes = $saldo * $tasa * $meses;
$totalpago = $monto+ $interes;

$capitalPartido = round($saldo/$plazo,1, PHP_ROUND_HALF_UP);
$cuota = round( $totalpago/$plazo ,1, PHP_ROUND_HALF_UP);
$intGanado = round( $interes/ $plazo ,1, PHP_ROUND_HALF_UP);

/* ?> 
<tr><td class='grey-text text-darken-2'><strong>0</strong></td> <td><?= $fecha->format('d/m/Y'); ?></td> <td>-</td><td>-</td> <td>-</td> <td><?= number_format($saldo,2);?></td></tr><?php */

$interesSumado=0;
$fecha->add($intervalo);

//$cuota = round($monto*$interes/$plazo,2);
for ($i=0; $i < $plazo ; $i++) {
/* 	?> <tr><?php */
	
	$razon = esFeriado($feriados, $fecha->format('Y-m-d'));
	if($razon!=false ){
		//echo "si es feriado";

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
		if( $fecha->format('w')=='0' ){
			//No hacer nada es Domingo
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
			//$suma+=$cuota;
			//$saldo = $saldo*$interes;
			$interesVariable= round($saldo * $interes, 1, PHP_ROUND_HALF_UP);
			$amortizacion = round($cuota-$interesVariable, 1, PHP_ROUND_HALF_UP);
			$saldo = $saldo -$amortizacion;
			$interesSumado+=$interesVariable;

			//echo "Día #".($i+1).": ". $fecha->format('d/m/Y') . "<br>";
			/* ?><td class='grey-text text-darken-2'><strong><?= $i+1; ?></strong></td> <td class='grey-text text-darken-2'><?= $fecha->format('d/m/Y'); ?></td> <td class='grey-text text-darken-2'>S/ <?= number_format($cuota, 2); ?></td> <td class='grey-text text-darken-2'><?= number_format($interesVariable,2); ?></td> <td class='grey-text text-darken-2'><?= number_format($amortizacion,2); ?></td> <td class='grey-text text-darken-2'><?= number_format($saldo, 2);?></td> <?php */

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
			$fecha->add($intervalo);
		}
	}
/* ?></tr><?php */
}

?>
<td>S/ <?= number_format(round($interes,1, PHP_ROUND_HALF_UP),2); ?></td>
<td>S/ <?= number_format(round($totalpago,1, PHP_ROUND_HALF_UP),2);?></td>
<td><?= $plazo;?></td>
<td>S/ <?= number_format(round($cuota,1, PHP_ROUND_HALF_UP),2); ?></td>
<td>S/ <?= number_format(round($monto*0.015,1, PHP_ROUND_HALF_UP),2);?> </td>
</tr></tbody>
</table>
<table class="table table-hover">
<thead>
	<tr>
		<th>#</th>
					<th>Fecha</th>
					<th>Capital</th>
					<th>Interés</th>
					<th class="hidden">Amortización</th>
					<th>Cuota</th>
					<th class="hidden">Saldo Real</th>
	</tr>
</thead>
<tbody>
<?php
$jsonSimple[0]['saldoReal'] = round($monto * $interes, 1, PHP_ROUND_HALF_UP);
$dia=1;
for ($j=0; $j <  count($jsonSimple) ; $j++) { ?><tr><?php
	
	$nueva= new DateTime ($jsonSimple[$j]['fPago']);

	if($jsonSimple[$j]['razon']==='Desembolso'){
		?> <td class='grey-text text-darken-2'>-</td> <td class='grey-text text-darken-2'><?= $nueva->format('d/m/Y'); ?></td> <td class='grey-text text-darken-2'>Desembolso</td> <td></td> <td><? //number_format($jsonSimple[$j]['saldoReal'],2);?></td>
		<?php
	}else if($jsonSimple[$j]['razon']==='Domingo'){ $dia++;
		?> <td class='grey-text text-darken-2'>-</td> <td class='grey-text text-darken-2'><?= $nueva->format('d/m/Y'); ?></td> <td class='grey-text text-darken-2'>Domingo</td> <td></td> <td></td> 
		<?php
	}else if($jsonSimple[$j]['razon']==='Feriado'){ $dia++;
		?> <td class='grey-text text-darken-2'>-</td> <td class='grey-text text-darken-2'><?= $nueva->format('d/m/Y'); ?></td> <td class='grey-text text-darken-2'><?= $jsonSimple[$j]['cuota'];?> </td>  <td></td> <td></td> 
		<?php
	}else{
		if($j>=1){
			$jsonSimple[$j]['saldoReal'] = $jsonSimple[$j-$dia]['saldoReal']-$jsonSimple[$j]['cuota']; $dia=1;
		}

		?><td class='grey-text text-darken-2'><strong><?= $jsonSimple[$j]['numDia']; ?></strong></td>
		<td class='grey-text text-darken-2'><?= $nueva->format('d/m/Y'); ?></td>
		<td class='grey-text text-darken-2'><?= "S/ ".number_format($capitalPartido, 2); //$jsonSimple[$j]['cuota'] ?></td> 
		<td class='grey-text text-darken-2'><?= "S/ ". number_format($intGanado,2); ?></td> 
		<td class='grey-text text-darken-2 hidden'><?= number_format($jsonSimple[$j]['amortizacion'],2); ?></td> 
		<td class='grey-text text-darken-2 hidden'><?= number_format($jsonSimple[$j]['saldo'], 2);?></td> 
		<td><?= "S/ ".number_format($jsonSimple[$j]['cuota'], 2);?></td> <?php
	}
?></tr>
<?php
}


function esFeriado($feriados, $dia){
	foreach ($feriados as $llave => $valor) {
		if($valor["ferFecha"]==$dia){
			return $valor["ferDescripcion"]; break;
		}
	}
	return false;
}

?>
</tbody>
<tfoot>
<tr>
	<td></td>
	<td><strong>Total:</strong></td>
	<td><strong>S/ <?= $monto;?></strong></td>
	<td><strong>S/ <?= number_format($monto*$_POST['tasaInt']/100,2); ?></strong></td>
	<td><strong>S/ <?= number_format( $totalpago,2);?></strong></td>
</tr>
</tfoot>
</table>