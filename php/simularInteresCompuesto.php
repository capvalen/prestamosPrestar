<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<title>Document</title>
</head>
<body class="container">
	

<?php 
$hoy = new DateTime();
$capital = 1000;
$interes = 0.02;
$periodo = $_GET['periodo'];

switch ($_GET['modo']){
	case "1": //DIARIO
		$intervalo = new DateInterval('P1D'); //aumenta 1 día
		$plazo = $periodo*30; echo '<p>Caso Diario</p>';
		break;
	case "2": //SEMANAL
		$intervalo = new DateInterval('P1W'); //aumenta 1 semana
		$plazo = $periodo*4; echo '<p>Caso Semanal</p>';
		break;
	case "4": //QUINCENAL
		$intervalo = new DateInterval('P14D'); //aumenta 15 días
		$plazo = $periodo*2; echo '<p>Caso Quincenal</p>';
		break;
	case "3": //MENSUAL
		$intervalo = new DateInterval('P30D'); //aumenta 30 día
		$plazo = $periodo*1; echo '<p>Caso Mensual</p>';
		break;
	default:
		echo 'Datos inválidos';
	break;
}

$capitalVivo = $capital;
$capitalAmortizado =0;
$sumInteres = 0; $sumAmortizacion=0; $sumCuotas=0; $sumPagoFinal=0; $sumComisiones =0;
$comServ = round(($capital * 0.01)/$plazo, 2);

$cuota = round( ( $interes* pow((1+$interes), $plazo) )* $capital / ( (pow(( 1 + $interes), $plazo)) -1), 1);
echo '<p>Desembolso:  '. $hoy->format('Y-m-d') ."   - Capital: S/ {$capital}     - Interés ". $interes*100 ."% </p>";
echo "<p>Plazo: {$plazo} </p>";
echo "<p>Cuota: {$cuota} </p>";
echo '<p>********************* </p>';


?>
<table class="table table">
	<thead class="">
		<tr>
			<th>Día #</th>
			<th>Fecha</th>
			<th>Cuota</th>
			<th>Interés</th>
			<th>Amortización</th>
			<th>Capital Vivo</th>
			<th>Capital Amortizado</th>
			<th>Com. & Serv.</th>
			<th>PAGO MENSUAL</th>

		</tr>
	</thead>
	<tbody>
		

<?php 
for ($i=1; $i <= $plazo ; $i++) {
	$hoy->add($intervalo);
	$interesAmort = round($capitalVivo * $interes,1);
	$amortizacion = $cuota - $interesAmort;
	$capitalVivo = round($capitalVivo - $amortizacion,1);
	$capitalAmortizado += $amortizacion;
	$cuotaFinal = $cuota + $comServ;
	$sumCuotas+=$cuota;
	$sumInteres += $interesAmort;
	$sumAmortizacion += $amortizacion;
	$sumComisiones += $comServ;
	$sumPagoFinal += $cuotaFinal;
	?>
	<tr>
		<td><?= $i; ?></td>
		<td><?= $hoy->format('Y-m-d'); ?></td>
		<td><?= $cuota; ?></td>
		<td><?= $interesAmort; ?></td>
		<td><?= $amortizacion; ?></td>
		<td><?= $capitalVivo; ?></td>
		<td><?= $capitalAmortizado; ?></td>
		<td><?= $comServ; ?></td>
		<td><?= $cuotaFinal; ?></td>
	</tr>
	 <?php
}
?>
	</tbody>
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th><?= $sumCuotas; ?></th>
			<th><?= $sumInteres; ?></th>
			<th><?= $sumAmortizacion; ?></th>
			<th></th>
			<th></th>
			<th><?= $sumComisiones; ?></th>
			<th><?= $sumPagoFinal; ?></th>
		</tr>
	</tfoot>
</table>
</body>
</html>