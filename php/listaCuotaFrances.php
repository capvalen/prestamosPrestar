<table class="table table-hover" id="tableSubIds">
	<thead>
	<tr>
		<th>N°</th>
		<th class="hidden-print">Sub-ID</th>
		<th>Fecha programada</th>
		<th>Capital</th>
		<th>Interés</th>
		<th>Com. y Serv.</th>
		<th>Cuota</th>
		<th class="hidden-print">Cancelación</th>
		<th class="hidden-print">Pago</th>
		<th class="hidden">Saldo</th>
		<th class="hidden-print">@</th>
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
				<td>SP-<?= $rowCuot['idCuota']; ?></td>
				<td><?php $fechaCu= new DateTime($rowCuot['cuotFechaPago']); echo $fechaCu->format('d/m/Y'); ?></td>
				<td><?php if( $k>=1 ){ echo number_format($amortizacion ,2); } ?></td>
				<td><?php if( $k>=1 ){ echo number_format($interesAmort, 2); } ?></td>
				<td><?php if( $k>=1 ){ echo number_format($rowCuot['cuotSeg'],2); } ?></td>
				<td><?php if( $k>=1 ){ echo number_format($cuota + $rowCuot['cuotSeg'],2); } ?></td>
				<td class="hidden-print"><?php if($rowCuot['cuotCuota']=='0.00' && $rowCuot['cuotPago']=='0.00'): echo "Desembolso"; elseif($rowCuot['cuotFechaCancelacion']=='0000-00-00'): echo 'Pendiente'; else: echo $rowCuot['cuotFechaCancelacion']; endif;  ?></td>
				<td class="tdPagoCli hidden-print" data-pago="<?= number_format($rowCuot['cuotPago'],2); ?>"><? if($k>=1) {echo number_format($rowCuot['cuotPago'],2);} ?></td>
				<td class="hidden"><?= number_format($rowCuot['cuotSaldo'],2); ?></td>
				<td class="hidden-print"><?php if(   $rowCuot['idTipoPrestamo']=='79' && $rowCr['presFechaDesembolso']<>'Desembolso pendiente' && $k>=1):
				$diasDebe2=$fechaHoy ->diff($fechaCu);

				if( $rowCr['presAprobado']== "Rechazado" ){ ?>
					<p class="red-text text-darken-1">Rechazado</p>
				<?php } else{
						if( floatval($diasDebe2->format('%R%a')) < 0 ){
						?> <p class="red-text text-darken-1">Cuota fuera de fecha (<?= $diasDebe2->format('%a').' días';?>)</p>
						<!-- <button class="btn btn-primary btn-outline btn-sm btnPagarCuota"><i class="icofont-money"></i> Pagar</button> -->
						<?php }else if( floatval($diasDebe2->format('%R%a')) == 0 ){ ?>
							<p class="red-text text-darken-1">Cuota se vence hoy (<?= $diasDebe2->format('%R%a')?>)</p>
						<?php
						}else{
							?> <p class="blue-text text-accent-2">Cuota en buena fecha</p><?php
						}
					}
					endif;

					if($rowCuot['cuotPago']<>'0.00' && $rowCr['presFechaDesembolso']<>'Desembolso pendiente'): 
						if( $rowCuot['idTipoPrestamo'] ==33 ){ ?>
							<span class="mitoolTip spanIcono" data-toggle="tooltip" title="Pago parcial"><i class="icofont-warning-alt"></i></span>
							<span class="amber-text text-darken-2 mitoolTip spanIcono spanPrint" data-print="parcial" data-toggle="tooltip" title="Imprimir"><i class="icofont-print"></i></span>
						<? }

						if($rowCuot['idTipoPrestamo'] ==80){ ?>
							<p><span class="mitoolTip spanIcono" data-toggle="tooltip" title="Pago completo"><i class="icofont-verification-check"></i> </span>
							<span class="amber-text text-darken-2 mitoolTip spanIcono spanPrint" data-print="completo" data-toggle="tooltip" title="Imprimir"><i class="icofont-print"></i></span>
							<span>Pagado</span>
							<?php if( $_COOKIE['ckPower']=='1' ):?>
								<span class="spanIcono text-danger" data-toggle="tooltip" title="Limpiar pago" onclick="limpiarPago(<?= $rowCuot['idCuota']?>, <?=$k?>)"><i class="icofont-brush"></i></span>
							<?php endif;?>
						</p>
						<?php }
					endif;?>
				</td>
			</tr>
		<?php $k++; }
		} ?>

	</tbody>
	<tfoot>
		<tr>
			<th></th> <th></th> <th></th>
			<th>S/ <?= number_format( round($sumAmortizacion, 1), 2); ?></th>
			<th>S/ <?= number_format( round($sumInteres, 1), 2); ?></th>
			<th>S/ <?= number_format( round($sumComisiones, 1), 2); ?></th>
			<th>S/ <?= number_format( round($sumCuotas + $sumComisiones, 1), 2); ?></th>
			<th></th> <th></th><th> </th>
		</tr>
	</tfoot>
</table>