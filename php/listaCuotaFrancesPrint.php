<table class="table table-hover" id="tableSubIds">
	<thead>
	<tr>
		<th>N°</th>
		
		<th>Fecha programada</th>
		<th>Capital</th>
		<th>Interés</th>
		<th>Com. y Serv.</th>
		<th>Cuota</th>
		
	</tr>
	</thead>
	<tbody>

<?php 
	$fechaHoy = new DateTime();
		$sqlPrim = "SELECT `presMontoDesembolso`, `presPeriodo`,`preInteresPers`, `idTipoPrestamo`
		from prestamo where `idPrestamo` = {$codCredito}";

		$resultadoPrim=$esclavo->query($sqlPrim);
		$rowPrim=$resultadoPrim->fetch_assoc();

		$monto = $rowPrim['presMontoDesembolso'];

		$tasa = $rowPrim['preInteresPers']/100;
		$interes = $tasa;
		$meses =  $rowPrim['presPeriodo'];
		$capital = $monto;
		$capitalAmortizado =0;

		switch ($rowPrim['idTipoPrestamo']){
			case "1": //DIARIO
				$plazo = $rowPrim['presPeriodo']*30;
				break;
			case "2": //SEMANAL
				$plazo = $rowPrim['presPeriodo']*4;
				break;
			case "4": //QUINCENAL
				$plazo = $rowPrim['presPeriodo']*2;
				break;
			case "3": //MENSUAL
				$plazo = $rowPrim['presPeriodo']*1	;
				break;
			default: break;
		}

		$capitalVivo = $capital;
		$sumInteres = 0; $sumAmortizacion=0; $sumCuotas=0; $sumPagoFinal=0; $sumComisiones =0;
		$comServ = round(($capital * 0.01)/$plazo, 2);

		$cuota = round( ( $interes* pow((1+$interes), $plazo) )* $capital / ( (pow(( 1 + $interes), $plazo)) -1), 2 ); //1, PHP_ROUND_HALF_UP
		$cuotaReal = round($cuota + $comServ,1);



		$sqlCuot= "SELECT prc.*, pre.preInteresPers, pre.presMontoDesembolso, pre.presPeriodo FROM prestamo_cuotas prc
		inner join prestamo pre on pre.idPrestamo = prc.idPrestamo
		where prc.idPrestamo = {$codCredito}
		order by cuotFechaPago asc";


		if($respCuot = $cadena->query($sqlCuot)){ $k=0;
			$sumCapital = 0; $sumInteres =0; $sumCuota =0;  $sumSeguros = 0;
			while($rowCuot = $respCuot->fetch_assoc()){
				if($k>=1){
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
				}
				?>

			<tr>
				<td><?= $k; ?></td>
				
				<td><?php $fechaCu= new DateTime($rowCuot['cuotFechaPago']); echo $fechaCu->format('d/m/Y'); ?></td>
				<td><?php if( $k>=1 ){ echo number_format($amortizacion ,2); } ?></td>
				<td><?php if( $k>=1 ){ echo number_format($interesAmort, 2); } ?></td>
				<td><?php if( $k>=1 ){ echo number_format($rowCuot['cuotSeg'],2); } ?></td>
				<td><?php if( $k>=1 ){ echo number_format($cuota + $rowCuot['cuotSeg'],2); } ?></td>
				
			</tr>
		<?php $k++; }
		} ?>

	</tbody>
	<tfoot>
		<tr>
			<th></th> <th></th>
			<th>S/ <?= number_format( round($sumAmortizacion, 1), 2); ?></th>
			<th>S/ <?= number_format( round($sumInteres, 1), 2); ?></th>
			<th>S/ <?= number_format( round($sumComisiones, 1), 2); ?></th>
			<th>S/ <?= number_format( round($sumCuotas + $sumComisiones, 1), 2); ?></th>
			<th></th> 
		</tr>
	</tfoot>
</table>

<?php 
$sql = "SELECT a.idArticulo, a.idCategoria, c.Nombre as catNombre, 
a.Codigo, a.Stock, a.Descripcion, a.Imagen, a.Condicion 
From Articulo a 
inner join Categoria c on a.idCategoria = c.idArticulo ";
?>
