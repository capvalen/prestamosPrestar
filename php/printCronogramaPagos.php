<?php 
header('Content-Type: text/html; charset=utf8');
date_default_timezone_set('America/Lima');
setlocale(LC_ALL,"es_ES");
include 'conkarl.php';
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();
$diassemanaN= array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
$mesesN=array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
if(!isset($_GET['pdf'])){
	$nomEmpresa = $_COOKIE['cknombreEmpresa'];
}else{ $nomEmpresa ="Prestar Huancayo"; }
$idPresPost = $base58->decode($_GET['prestamo']);
$sumaCapi =0; $sumaInteres=0; $sumaComi=0; $sumaCuota=0; $seguro=0; $sumaTodo=0; $todo =0;
$sql = "SELECT pre.*, lower(concat(TRIM(c.cliApellidoPaterno), ' ', TRIM(c.cliApellidoMaterno), ', ', TRIM(c.cliNombres))) as cliNombres, c.cliDni, tp.tpreDescipcion, lower( concat(a.addrDireccion, ' ', a.addrNumero, ' ', d.distrito, ' - ', p.provincia )) as `direccion`
FROM `prestamo` pre
inner join tipoprestamo tp on tp.idTipoPrestamo = pre.idTipoPrestamo
inner join involucrados i on i.idPrestamo = pre.idPrestamo
inner join cliente c on c.idCliente = i.idCliente
inner join address a on a.idAddress= c.cliDireccionNegocio 
inner join distrito d on d.idDistrito= a.idDistrito
inner join provincia p on p.idProvincia = a.idProvincia
WHERE pre.idPrestamo = {$idPresPost} and i.idTipoCliente = 1;";
if($llamado= $conection->query($sql)){
	$respuesta = $llamado->fetch_assoc();
	$saldo = $respuesta['presMontoDesembolso'];
	$monto = $saldo;
	$tasa = $respuesta['preInteresPers'];
	$meses = $respuesta['presPeriodo'];

	switch ($respuesta['idTipoPrestamo']){
		case "1": //DIARIO
			$plazo = $meses *30;
			break;
		case "2": //SEMANAL
			$plazo = $meses *4;
			break;
		case "4": //QUINCENAL
			$plazo = $meses *2;
			break;
		case "3": //MENSUAL
			$plazo = $meses *1;
			break;
		default: break;
	}

	$interes = $saldo * $tasa * $meses;
	$totalpago = $monto + $interes;

	$capitalPartido = round($saldo/$plazo,1, PHP_ROUND_HALF_UP);
	$cuota = round( $totalpago/$plazo ,1, PHP_ROUND_HALF_UP);
	$intGanado = round( $interes/ $plazo ,1, PHP_ROUND_HALF_UP);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Impresion de Cronograma Pagos</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
</head>
<body>
<style>
	h5{font-weight: 700;}
	.mayuscula{text-transform: capitalize}
	#rowCabeza p {margin: 0 0 5px;}
	.table>tbody>tr>td{padding: 4px 8px;}
</style> 
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-3"><img src="./../images/empresa.png" class="img-responsive"> </div>
			<div class="col-xs-8 text-center">
				<strong><h5><?= $nomEmpresa; ?></h5></strong>
				<strong><h5>Cronograma de pagos</h5></strong>
			</div>
		</div>
		<div class="row" id="rowCabeza">
			<div class="col-xs-6">
				<p><strong>N° Crédito:</strong> <span>CR-<?= $idPresPost; ?></span></p>
				<p><strong>Cliente:</strong> <span class="mayuscula"><?= $respuesta['cliNombres'];?></span></p>
				<p><strong>DNI:</strong> <span><?= $respuesta['cliDni'];?></span></p>
				<p><strong>Dirección:</strong> <span class='mayuscula'><?= $respuesta['direccion'];?></span></p>
			</div>
			<div class="col-xs-6">
			<?php if(!isset($_GET['pdf'])){ ?>
				<p><strong>Oficina:</strong> <span><?= $_COOKIE['cksucursalEmpresa'] ?></span></p>
				<p><strong>Asesor:</strong> <span class='mayuscula'><?= $_COOKIE['ckAtiende'] ?></span></p>
				<p><strong>Periodo:</strong> <span><?= $respuesta['tpreDescipcion'];?></span></p>
			<?php } ?>
				<p><strong>F. Desembolso:</strong> <span><? $fecha1= new DateTime( $respuesta['presFechaDesembolso']); echo $fecha1->format('d/m/Y h:m a');?></span></p>
			</div>
		</div>
		<div class="row">
			<?php if($respuesta['intSimple']==0): ?>
			<table class="table">

					<?php 

					 switch ($respuesta['idTipoPrestamo']) {
						case '1':
						case '2':
						case '3':
						case '4':
						?>
						<thead><tr><th>N° Cuota</th> <th>F. Programada</th> <th>Capital</th> <th>Interés</th> <th>Com. Serv.</th> <th>Cuota</th> </tr></thead>
						<tbody> <?php
						$i=0; 
						$sql2 = "SELECT * FROM `prestamo_cuotas` WHERE `idPrestamo` = {$idPresPost}";
						if($llamado2 = $cadena->query($sql2)){
							$totalRows = $llamado2->num_rows;
							
							while($respuesta2 = $llamado2->fetch_assoc()){
								if($i>=1){ 
									$seguro = round( $respuesta2['cuotSeg'] , 1, PHP_ROUND_HALF_UP); 
									
									?>
								<tr>
									<td><?= $i; ?></td>
									<td><?php $fecha = new DateTime($respuesta2['cuotFechaPago']); echo $diassemanaN[$fecha->format('w')].', '. $fecha->format('d').' de '. $mesesN[$fecha->format('n')-1]." de ".$fecha->format('Y');?></td>
									<td><?= number_format(round($capitalPartido,1, PHP_ROUND_HALF_UP),2); ?></td>
									<td><?= number_format(round($respuesta2['cuotCuota']- $capitalPartido,1, PHP_ROUND_HALF_UP),2); ?></td>
									<td><?= number_format( $seguro ,2);?></td>
									<td><?= number_format(round($respuesta2['cuotCuota']+ $seguro, 1, PHP_ROUND_HALF_UP),2);?></td>
								</tr>
								<?php $sumaInteres+= ($respuesta2['cuotCuota']- $capitalPartido);
								$sumaCapi+= $capitalPartido;  $sumaComi+=$seguro; $sumaCuota+=$respuesta2['cuotCuota']+ $seguro;
								}
								
								$i++;
							}
						}
						?> </tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th><?= number_format(round($sumaCapi,1, PHP_ROUND_HALF_UP),2); ?></th>
								<th><?= number_format(round($sumaInteres,1, PHP_ROUND_HALF_UP),2); ?></th>
								<th><?= number_format(round($sumaComi,1, PHP_ROUND_HALF_UP),2); ?></th>
								<th><?= number_format(round($sumaCuota,1, PHP_ROUND_HALF_UP),2); ?></th>
							</tr>
						</tfoot>
						 <?php
						
						
							break;
						case '99':
						$i=0;
						$sql2 = "SELECT * FROM `prestamo_cuotas` WHERE `idPrestamo` = {$idPresPost}";
							?> 
							<thead><tr><th>N°</th> <th>F. Pago</th> <th>Saldo de capital</th> <th>Amotización</th> <th>Interés</th> <th>SEG</th> <th>ITF</th> <th>Total Cuota</th> </tr></thead>
							<tbody>
							<?php 
							if($llamado2 = $cadena->query($sql2)){
							$totalRows = $llamado2->num_rows;
							while($respuesta2 = $llamado2->fetch_assoc()){ ?>
								<tr>
									<td><?= $i; ?></td>
									<td><?php $fecha = new DateTime($respuesta2['cuotFechaPago']); echo $fecha->format('d/m/Y');?></td>
									<td><?= number_format(round($respuesta2['cuotCuota'], 1, PHP_ROUND_HALF_UP),2);?></td>
									<td><?= number_format(round($respuesta2['cuotAmortizacion'], 1, PHP_ROUND_HALF_UP),2);?></td>
									<td><?= number_format(round($respuesta2['cuotInteres'], 1, PHP_ROUND_HALF_UP),2);?></td>
									<td><?= number_format(round($respuesta2['cuotSeg'], 1, PHP_ROUND_HALF_UP),2);?></td>
									<td><?= number_format(round($respuesta2['cuotItf'], 1, PHP_ROUND_HALF_UP),2);?></td>
									<td><?= number_format(round($respuesta2['cuotTotal'], 1, PHP_ROUND_HALF_UP),2);?></td>
								</tr>
							<?php 
								$i++;
								}
							} ?>
							</tbody> <?php
						default:
							# code...
							break;
					}
					?>
					<tfoot>
						<tr>

						</tr>
					</tfoot>
			</table>
			<?php else: 
				$codCredito = $idPresPost;
				include 'listaCuotaFrances.php';
			endif; ?>
		</div>
	</div>



<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script>
$(document).ready(function () {
window.print();	//Activa la impresion apenas cargo todo
});
/*Determina si se imprimio o se cancelo, para cerrar la pesataña activa*/
(function () {
	var afterPrint = function () {
	window.top.close();
	};
	if (window.matchMedia) {
		var mediaQueryList = window.matchMedia('print');
		mediaQueryList.addListener(function (mql) {
				//alert($(mediaQueryList).html());
				if (mql.matches) {
				} else { afterPrint(); }
		});
	}
	window.onafterprint = afterPrint;
}());
</script>
</body>
</html>