<?
include 'conkarl.php';
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();

$entradas = array(80,33);
$salidas = array(43);

$sumaTodo=0; $sumaRecup =0; $sumaGananc=0;

switch ($_POST['caso']) {
	case 'R1':
		$sql="SELECT `idCaja`,c.`idPrestamo`,`idCuota`, `cajaValor`, pre.presMontoDesembolso, pre.preInteresPers, pre.presPeriodo, tp.tipoDescripcion, c.idtipoProceso  FROM `caja` c left join prestamo pre on pre.idPrestamo = c.idPrestamo inner join tipoproceso tp on tp.idtipoproceso = c.idtipoProceso where cajaFecha between '{$_POST['fInicio']} 00:00' and '{$_POST['fFinal']} 23:59:59' and c.idTipoProceso in (31, 81, 80, 33, 87,88,89) and cajaActivo = 1 order by idPrestamo;";
		
		$resultado=$cadena->query($sql);
		?> 
		<thead>
			<tr>
				<th data-sort="int">Préstamo</th>
				<th data-sort="float">Cuota</th>
				<th data-sort="string">Proceso</th>
				<th data-sort="float">Monto Pagado</th>
				<th data-sort="float">Recuperación</th>
				<th data-sort="float">Ganancia</th>
			</tr>
		</thead>
		<tbody>
	<? while($row=$resultado->fetch_assoc()){ 
		$sumaTodo = $sumaTodo + $row['cajaValor']; ?>
			<tr>
				<td class="tableexport-string" data-sort-value="<?= $row['idPrestamo'];?>"><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo'];?></a></td>
				<td class="tableexport-string" data-sort-value="<?=$row['idCuota'];?>"><? if( $row['idCuota'] <>'0'){ echo 'SP-'.$row['idCuota']; } ?></td>
				<td class="tableexport-string"><?= $row['tipoDescripcion'];?></td>
				<td class="tableexport-string" data-sort-value="<?=$row['cajaValor'];?>">S/ <?= number_format($row['cajaValor'],2);?></td>
			<? if( in_array($row['idtipoProceso'], $entradas) ){
				$presto = $row['presMontoDesembolso'];
				$porcentaje = $row['preInteresPers'];
				$plazo = $row['presPeriodo'];

				$pagoTotal = $presto *(1+$porcentaje/100);
				$soloInteres = $presto*$porcentaje/100;
				$cuota = $pagoTotal / $plazo;

				$capitalUnit = $presto/$plazo;
				$interesUnit = $soloInteres /$plazo;

				

				if( round(floatval($row['cajaValor']),1, PHP_ROUND_HALF_UP)== round(floatval($cuota),1,PHP_ROUND_HALF_UP) ){ $sumaRecup = $sumaRecup + $capitalUnit; $sumaGananc = $sumaGananc + $interesUnit; ?>
				<td class="tableexport-string" data-sort-value="<?=$capitalUnit;?>">S/ <?= number_format($capitalUnit,2);?></td>
				<td class="tableexport-string" data-sort-value="<?=$interesUnit;?>">S/ <?= number_format($interesUnit,2);?></td>
		<?	} else if(round(floatval($row['cajaValor']),1, PHP_ROUND_HALF_UP)< round(floatval($cuota),1, PHP_ROUND_HALF_UP)){ $sumaRecup = $sumaRecup + ($cuota-$row['cajaValor']); ?>
				<td class="tableexport-string" data-sort-value="<?=$cuota-$row['cajaValor'];?>">S/ <?= number_format($cuota-$row['cajaValor'],2); /* .  ' cuota es '.$cuota; */?></td>
				<td class="tableexport-string" data-sort-value="0">S/ 0.00</td>
			<? }
      }else if($row['idtipoProceso']==81){  $sumaGananc = $sumaGananc + $row['cajaValor']; ?>
				<td class="tableexport-string" data-sort-value="0"></td>
				<td class="tableexport-string" data-sort-value="$row['cajaValor'];"><?= 'S/ '. number_format($row['cajaValor'],2);?></td>
			<? } else{ ?>
				<td class="tableexport-string" data-sort-value="0"></td><td data-sort-value="0"></td>
			<? } ?>
			</tr>
<? } //end de while ?> 
			</tbody>
			<tfoot>
				<td></td>
				<td></td>
				<td></td>
				
				<th>S/ <?= number_format($sumaTodo,2);?></th>
				<th>S/ <?= number_format($sumaRecup,2);?></th>
				<th>S/ <?= number_format($sumaGananc,2);?></th>	
			</tfoot>
		<?
		break;


		case 'R2':
		$sql="SELECT `idCaja`,c.`idPrestamo`,`idCuota`, `cajaValor`, pre.presMontoDesembolso, pre.preInteresPers, pre.presPeriodo, tp.tipoDescripcion, c.idtipoProceso, cajaObservacion  FROM `caja` c left join prestamo pre on pre.idPrestamo = c.idPrestamo inner join tipoproceso tp on tp.idtipoproceso = c.idtipoProceso where cajaFecha between '{$_POST['fInicio']} 00:00' and '{$_POST['fFinal']} 23:59:59' and c.idTipoProceso in (43,85,84,83,82,40,41) and cajaActivo = 1 order by idPrestamo;";
		
		$resultado=$cadena->query($sql);
		?> 
		<thead>
				<tr>
					<th>Préstamo</th>
					
					<th>Proceso</th>
					<th>Inversión</th>
					
				</tr>
			</thead>
			<tbody>
	<? while($row=$resultado->fetch_assoc()){ 
		$sumaTodo = $sumaTodo + $row['cajaValor']; ?>
			<tr>
				<td class="tableexport-string" ><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo'];?></a></td>
				<td class="tableexport-string" ><?= $row['tipoDescripcion']?> <? if($row['cajaObservacion']<>''){echo '<span class="mayucula">«'.$row['cajaObservacion'].'»</span>';}?></td>
				<td class="tableexport-string" >S/ <?= number_format($row['cajaValor'],2);?></td>
			</tr>
<? } //end de while ?> 
			</tbody>
			<tfoot>
				<td></td>
				<td></td>
				
				<th>S/ <?= number_format($sumaTodo,2);?></th>
			</tfoot>
		<?
		break;


		
		case 'R3':
		$sql="SELECT `idCaja`,c.`idPrestamo`,`idCuota`, `cajaValor`, `cajaFecha`, pre.presMontoDesembolso, pre.preInteresPers, pre.presPeriodo, tp.tipoDescripcion, c.idtipoProceso, cliApellidoPaterno, cliApellidoMaterno, cliNombres
		FROM `caja` c inner join prestamo pre on pre.idPrestamo = c.idPrestamo inner join tipoproceso tp on tp.idtipoproceso = c.idtipoProceso
		inner join involucrados i on i.idPrestamo = c.idPrestamo inner join cliente cl on i.idCliente = cl.idCliente
		where cajaFecha between '{$_POST['fInicio']} 00:00' and '{$_POST['fFinal']} 23:59:59' and c.idTipoProceso in (43) and cajaActivo = 1 and i.idTipoCliente=1
		group by c.`idPrestamo`
		order by idPrestamo;";
		$resultado=$cadena->query($sql);
		?> 
		<thead>
				<tr>
					<th>Préstamo</th>
					<th>Cliente</th>
					<th>Proceso</th>
					<th>Inversión</th>
					<th>Fecha</th>
					
				</tr>
			</thead>
			<tbody>
	<? while($row=$resultado->fetch_assoc()){ 
		$sumaTodo = $sumaTodo + $row['cajaValor']; ?>
			<tr>
				<td><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo'];?></a></td>
				<td class='mayuscula'><?= $row['cliApellidoPaterno'].' '.$row['cliApellidoMaterno'].', '.$row['cliNombres'];?></td>
				<td><?= $row['tipoDescripcion']?></td>
				<td>S/ <?= number_format($row['cajaValor'],2);?></td>
				<td><? $fechaCaj= new DateTime($row['cajaFecha']); echo $fechaCaj->format('d/m/Y h:m a');?></td>
			</tr>
	<? } //end de while ?> 
			</tbody>
			<tfoot>
				<td></td>
				<td></td>
				<td></td>
				<th>S/ <?= number_format($sumaTodo,2);?></th>
				<td></td>
			</tfoot>
		<?
		break;
	case 'R3':
		$sql="SELECT `idCaja`,c.`idPrestamo`,`idCuota`, `cajaValor`, `cajaFecha`, pre.presMontoDesembolso, pre.preInteresPers, pre.presPeriodo, tp.tipoDescripcion, c.idtipoProceso, cliApellidoPaterno, cliApellidoMaterno, cliNombres
		FROM `caja` c inner join prestamo pre on pre.idPrestamo = c.idPrestamo inner join tipoproceso tp on tp.idtipoproceso = c.idtipoProceso
		inner join involucrados i on i.idPrestamo = c.idPrestamo inner join cliente cl on i.idCliente = cl.idCliente
		where cajaFecha between '{$_POST['fInicio']} 00:00' and '{$_POST['fFinal']} 23:59:59' and c.idTipoProceso in (43) and cajaActivo = 1 and i.idTipoCliente=1
		group by c.`idPrestamo`
		order by idPrestamo;";
		$resultado=$cadena->query($sql);
		?> 
		<thead>
				<tr>
					<th>Préstamo</th>
					<th>Cliente</th>
					<th>Proceso</th>
					<th>Inversión</th>
					<th>Fecha</th>
					
				</tr>
			</thead>
			<tbody>
	<? while($row=$resultado->fetch_assoc()){ 
		$sumaTodo = $sumaTodo + $row['cajaValor']; ?>
			<tr>
				<td><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo'];?></a></td>
				<td class='mayuscula'><?= $row['cliApellidoPaterno'].' '.$row['cliApellidoMaterno'].', '.$row['cliNombres'];?></td>
				<td><?= $row['tipoDescripcion']?></td>
				<td>S/ <?= number_format($row['cajaValor'],2);?></td>
				<td><? $fechaCaj= new DateTime($row['cajaFecha']); echo $fechaCaj->format('d/m/Y h:m a');?></td>
			</tr>
	<? } //end de while ?> 
			</tbody>
			<tfoot>
				<td></td>
				<td></td>
				<td></td>
				<th>S/ <?= number_format($sumaTodo,2);?></th>
				<td></td>
			</tfoot>
		<?
		break;


		case 'R4':
		
		$sql="SELECT `idCaja`,c.`idPrestamo`,`idCuota`, `cajaValor`, `cajaFecha`, pre.presMontoDesembolso, pre.preInteresPers, pre.presPeriodo, tp.tipoDescripcion, c.idtipoProceso, cliApellidoPaterno, cliApellidoMaterno, cliNombres
		FROM `caja` c inner join prestamo pre on pre.idPrestamo = c.idPrestamo inner join tipoproceso tp on tp.idtipoproceso = c.idtipoProceso
		inner join involucrados i on i.idPrestamo = c.idPrestamo inner join cliente cl on i.idCliente = cl.idCliente
		where cajaFecha between '{$_POST['fInicio']} 00:00' and '{$_POST['fFinal']} 23:59:59' and c.idTipoProceso in (81, 86, 89) and cajaActivo = 1
		order by idPrestamo;";
		$resultado=$cadena->query($sql);
		?> 
		<thead>
				<tr>
					<th>Préstamo</th>
					<th>Cliente</th>
					<th>Proceso</th>
					<th>Inversión</th>
					<th>Fecha</th>
					
				</tr>
			</thead>
			<tbody>
	<? while($row=$resultado->fetch_assoc()){ 
		$sumaTodo = $sumaTodo + $row['cajaValor']; ?>
			<tr>
				<td class="tableexport-string"><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo'];?></a></td>
				<td class='mayuscula'><?= $row['cliApellidoPaterno'].' '.$row['cliApellidoMaterno'].', '.$row['cliNombres'];?></td>
				<td><?= $row['tipoDescripcion']?></td>
				<td>S/ <?= number_format($row['cajaValor'],2);?></td>
				<td><? $fechaCaj= new DateTime($row['cajaFecha']); echo $fechaCaj->format('d/m/Y h:m a');?></td>
			</tr>
	<? } //end de while ?> 
			</tbody>
			<tfoot>
				<td></td>
				<td></td>
				<td></td>
				<th>S/ <?= number_format($sumaTodo,2);?></th>
				<td></td>
			</tfoot>
		<?
		break;


		case 'R5':
		
		$sql="SELECT pre.`idPrestamo`, `fechaFinPrestamo`, presMontoDesembolso, preInteresPers, presPeriodo,
		cliApellidoPaterno, cliApellidoMaterno, cliNombres, 'Fin de préstamo' as `tipoDescripcion`
				FROM `prestamo` pre 
				inner join involucrados i on i.idPrestamo = pre.idPrestamo inner join cliente cl on i.idCliente = cl.idCliente
				where `fechaFinPrestamo` between '{$_POST['fInicio']} 00:00' and '{$_POST['fFinal']} 23:59:59'
				order by idPrestamo";
		$resultado=$cadena->query($sql);
		?> 
		<thead>
				<tr>
					<th>Préstamo</th>
					<th>Cliente</th>
					<th>Proceso</th>
					<th>Inversión</th>
					<th>Fecha</th>
					
				</tr>
			</thead>
			<tbody>
	<? while($row=$resultado->fetch_assoc()){ 
		$sumaTodo = $sumaTodo + $row['presMontoDesembolso']; ?>
			<tr>
				<td class="tableexport-string"><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo'];?></a></td>
				<td class='mayuscula'><?= $row['cliApellidoPaterno'].' '.$row['cliApellidoMaterno'].', '.$row['cliNombres'];?></td>
				<td><?= $row['tipoDescripcion']?></td>
				<td>S/ <?= number_format($row['presMontoDesembolso'],2);?></td>
				<td><? $fechaCaj= new DateTime($row['fechaFinPrestamo']); echo $fechaCaj->format('d/m/Y h:m a');?></td>
			</tr>
	<? } //end de while ?> 
			</tbody>
			<tfoot>
				<td></td>
				<td></td>
				<td></td>
				<th>S/ <?= number_format($sumaTodo,2);?></th>
				<td></td>
			</tfoot>
		<?
		break;
		case "R6":
			$sql="SELECT `cliDni`, lower( concat(trim(`cliApellidoPaterno`),' ', trim(`cliApellidoMaterno`), ' ', trim(`cliNombres`))) as 'cliNombre',
			pre.idPrestamo, preInteresPers, date_format(presFechaDesembolso, '%d/%m/%Y') as fechaDesembolso, presMontoDesembolso,
			idTipoPrestamo, presPeriodo, round(`retornarMontoCuota`(pre.idPrestamo),1) as montCuota, `retornarNumCuotasPagadas`(pre.idPrestamo) as pagados, retornarNumCuotasNoPagadas(pre.idPrestamo) as noPagados,
			round(retornarSumCuotasPagadas(pre.idPrestamo),1) as sumPagados, round(retornarSumCuotasNoPagadas(pre.idPrestamo),1) as sumNoPagados
			
			FROM cliente c
			inner join involucrados i on i.idCliente = c.idCliente
			inner join prestamo pre on pre.idPrestamo = i.idPrestamo
			WHERE pre.presActivo=1 and pre.presAprobado<>2  and pre.presFechaDesembolso <> '0000-00-00'; /* date_format(presFechaDesembolso,'%Y-%m-%d') between '{$_POST['fInicio']}' and '{$_POST['fFinal']}' */; ";
			$resultado=$cadena->query($sql);
			$sumMontos=0;  $sumCuotas=0; $sumSemana=0; $sumQuincena=0; $sumMensual=0;$sumDiario=0; $sumPagados=0; $sumPendientes=0; $numPagado=0; $numPendiente=0; $sumCobroTotal=0;
			?>
			<thead>
				<tr>
					<th>Préstamo</th>
					<th>D.N.I.</th>
					<th>Apellidos y Nombres</th>
					<th>Tasa</th>
					<th>Fecha de desembolso</th>
					<th>Monto</th>
					<th>Sem.</th>
					<th>Qui.</th>
					<th>Men.</th>
					<th>Dia.</th>
					<th>Cuota</th>
					<th>Pagados</th>
					<th>Pendiente</th>
					<th>Pagados</th>
					<th>Por cobrar</th>
					<th>Cobro total</th>
				</tr>
			</thead>
			<tbody>
			<?php
			while($row=$resultado->fetch_assoc()){ 
				$sumMontos+= $row['presMontoDesembolso']; $sumCuotas+=$row['montCuota']; 
				$sumPagados+=$row['sumPagados']; $sumPendientes+= $row['sumNoPagados'];
				$numPagado+=$row['pagados']; $numPendiente+=$row['noPagados'];
			?>
			<tr>
				<td><?= $row['idPrestamo']; ?></td>
				<td class="tableexport-string"><?= $row['cliDni']; ?></td>
				<td class="text-capitalize"><?= $row['cliNombre']; ?></td>
				<td><?= $row['preInteresPers']."%"; ?></td>
				<td class="tableexport-string"><?= $row['fechaDesembolso']; ?></td>
				<td class="tableexport-string">S/ <?= $row['presMontoDesembolso']; ?></td>
				<?php switch ($row['idTipoPrestamo']) {
					case '2':  $sumSemana++; ?><td><?= $row['presPeriodo']*4; ?></td> <td></td><td></td><td></td>
						<?php break;
					case '4':  $sumQuincena++; ?><td></td><td><?= $row['presPeriodo']*2; ?></td> <td></td><td></td>
					<?php break;
					case '3':  $sumMensual++; ?><td></td><td></td><td><?= $row['presPeriodo']; ?></td> <td></td>
					<?php break;
					case '1':  $sumDiario++; ?><td></td><td></td><td></td><td><?= $row['presPeriodo']*30; ?></td>
					<?php break;
					default:
						# code...
						break;
				} ?>
				<td class="tableexport-string">S/ <?= number_format($row['montCuota'],2); ?></td>
				<td><?= $row['pagados']; ?></td>
				<td><?= $row['noPagados']; ?></td>
				<td class="tableexport-string">S/ <?= number_format($row['sumPagados'],2); ?></td>
				<td class="tableexport-string">S/ <?= number_format($row['sumNoPagados'],2); ?></td>
				<td class="tableexport-string">S/ <?= number_format($row['sumPagados']+$row['sumNoPagados'],2); ?></td>
			</tr>
			<?php
			}?>
			</tbody>
			<tfoot>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<th id="tdCapital">S/ <?= number_format($sumMontos,2);?></th>
				<td><?=$sumSemana; ?></td>
				<td><?=$sumQuincena; ?></td>
				<td><?=$sumMensual; ?></td>
				<td><?=$sumDiario; ?></td>
				<th>S/ <?= number_format($sumCuotas,2);?></th>
				<td><?= $numPagado; ?></td>
				<td><?= $numPendiente; ?></td>
				<th id="tdPagados">S/ <?= number_format($sumPagados,2);?></th>
				<th id="tdPendientes">S/ <?= number_format($sumPendientes,2);?></th>
				<th id="tdTotal">S/ <?= number_format($sumPagados+$sumPendientes,2);?></th>
			</tfoot>
			<?php
			
			
		break;
		case "R7":
		?>
		<thead>
		<tr>
			<th>N°</th>
			<th>Código</th>
			<th>D.N.I.</th>
			<th>Apellidos y Nombres</th>
			<th>Desembolso</th>
			<th>Monto</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$sumMontos=0;
		$sql="SELECT pre.idPrestamo, `cliDni`, lower( concat(trim(`cliApellidoPaterno`),' ', trim(`cliApellidoMaterno`), ' ', trim(`cliNombres`))) as 'cliNombre',
		date_format(presFechaDesembolso, '%d/%m/%Y') as fechaDesembolso, presMontoDesembolso
		FROM cliente c
		inner join involucrados i on i.idCliente = c.idCliente
		inner join prestamo pre on pre.idPrestamo = i.idPrestamo
		where presAprobado=1; ";
		$resultado=$cadena->query($sql);
		$sumMontos=0;  $sumCuotas=0; $sumSemana=0; $sumQuincena=0; $sumMensual=0;$sumDiario=0; $sumPagados=0; $sumPendientes=0; $numPagado=0; $numPendiente=0; $sumCobroTotal=0;
		$i=1;
		while($row=$resultado->fetch_assoc()){ 
			$sumMontos+= $row['presMontoDesembolso']; ?>
		<tr>
			<td> <?= $i; ?> </td>
			<td> <?= $row['idPrestamo']; ?> </td>
			<td class="tableexport-string"> <?= $row['cliDni']; ?> </td>
			<td class="text-capitalize"> <?= $row['cliNombre']; ?> </td>
			<td class="tableexport-string"> <?= $row['fechaDesembolso']; ?> </td>
			<td class="tableexport-string">S/ <?= number_format($row['presMontoDesembolso'],2); ?> </td>
		</tr>
		<?php	
		$i++; }
		?>
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>S/ <?= number_format($sumMontos,2) ?></th>
			</tr>
		</tfoot>
		</tbody>
		<?php
		break;
		case "R8":
		?>
		<thead>
		<tr>
			<th>N°</th>
			<th>Apellidos y Nombres</th>
			<th>Tasa</th>
			<th>Desembolso</th>
			<th>Monto</th>
			<th>Semana</th>
			<th>Quincena</th>
			<th>Mensual</th>
			<th>Dia</th>
			<th>Cuota</th>
			<th>Pagados</th>
			<th>Pendiente</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$sumMontos=0;
		$sql="SELECT `cliDni`, lower( concat(trim(`cliApellidoPaterno`),' ', trim(`cliApellidoMaterno`), ' ', trim(`cliNombres`))) as 'cliNombre',
			pre.idPrestamo, preInteresPers, date_format(presFechaDesembolso, '%d/%m/%Y') as fechaDesembolso, presMontoDesembolso,
			idTipoPrestamo, presPeriodo, round(`retornarMontoCuota`(pre.idPrestamo),1) as montCuota, `retornarNumCuotasPagadas`(pre.idPrestamo) as pagados, retornarNumCuotasNoPagadas(pre.idPrestamo) as noPagados,
			round(retornarSumCuotasPagadas(pre.idPrestamo),1) as sumPagados, round(retornarSumCuotasNoPagadas(pre.idPrestamo),1) as sumNoPagados
			
			FROM cliente c
			inner join involucrados i on i.idCliente = c.idCliente
			inner join prestamo pre on pre.idPrestamo = i.idPrestamo
			WHERE pre.presActivo=1 and pre.presAprobado<>2  and pre.presFechaDesembolso <> '0000-00-00' and date_format(presFechaDesembolso,'%Y-%m') = '{$_POST['fMes']}' ; ";
		echo $sql;
		$resultado=$cadena->query($sql);
		$i=1;
		while($row=$resultado->fetch_assoc()){ 
			$sumMontos+= $row['presMontoDesembolso'];
			$sumPagados+=$row['sumPagados']; $sumPendientes+= $row['sumNoPagados'];
			$numPagado+=$row['pagados']; $numPendiente+=$row['noPagados']; ?>
		<tr>
			<td> <?= $i; ?> </td>
			<td class="text-capitalize"> <?= $row['cliNombre']; ?> </td>
			<td class="tableexport-string"> <?= $row['preInteresPers']."%"; ?> </td>
			<td class="tableexport-string"> <?= $row['fechaDesembolso']; ?> </td>
			<td class="tableexport-string">S/ <?= number_format($row['presMontoDesembolso'],2); ?> </td>
			<?php switch ($row['idTipoPrestamo']) {
					case '2':  $sumSemana++; ?><td><?= $row['presPeriodo']*4; ?></td> <td></td><td></td><td></td>
						<?php break;
					case '4':  $sumQuincena++; ?><td></td><td><?= $row['presPeriodo']*2; ?></td> <td></td><td></td>
					<?php break;
					case '3':  $sumMensual++; ?><td></td><td></td><td><?= $row['presPeriodo']; ?></td> <td></td>
					<?php break;
					case '1':  $sumDiario++; ?><td></td><td></td><td></td><td><?= $row['presPeriodo']*30; ?></td>
					<?php break;
					default:
						# code...
						break;
				} ?>
				<td class="tableexport-string">S/ <?= number_format($row['montCuota'],2); ?></td>
				<td><?= $row['pagados']; ?></td>
				<td><?= $row['noPagados']; ?></td>
		</tr>
		<?php	
		$i++; }
		?>
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>S/ <span id="thSumaMontosR8"><?= number_format($sumMontos,2); ?></span></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>S/ <?= number_format($sumPagados,2); ?></th>
			</tr>
		</tfoot>
		</tbody>
		<?php
		break;
		case "R9":
		?>
		<div class="row">
			<div class="col col-sm-6">
				<table class="table table-hover" id="tblBalanceGeneral">
				<thead>
					<tr>
						<th class="text-center" colspan="4">BALANCE GENERAL PRESTAR HUANCAYO</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="" colspan="3">Activo</th>
						<td class=""> S/ 41 426.45</td>
					</tr>
					<tr>
						<th class="" colspan="3">1.1 Activo Corriente</th>
						<td class="">  S/34 856.45</td>
					</tr>
					<tr>
						<td class="" colspan="3">A. Disponible</td>
						<td class="">  S/ 100.00</td>
					</tr>
					<tr>
						<td class="" colspan="3">B. Caja</td>
						<td class="">  </td>
					</tr>
					<tr>
						<td class="" colspan="3">C. Bancos</td>
						<td class="">  S/ 3000.00</td>
					</tr>
					<tr>
						<td class="" colspan="3">D. Cuentas por cobrar</td>
						<td class="">  </td>
					</tr>
					<tr>
						<td class="" colspan="3">F. Adelantos a proveedores</td>
						<td class="">  </td>
					</tr>
					<tr>
						<td class="" colspan="3">G. Suministros</td>
						<td class="">  </td>
					</tr>
					<tr>
						<th class="" colspan="3">1.2 Activo No Corriente</th>
						<td class=""> S/ 6570.00</td>
					</tr>
					<tr>
						<td class="" colspan="3">A. Inmueble Maquinaria Y Equipo</td>
						<td class="">  </td>
					</tr>
					<tr>
						<td class="" colspan="3">B. Terrenos Edificios Y Otras Construcciones</td>
						<td class="">  </td>
					</tr>
					<tr id="tdANCenseres">
						<td class="" colspan="3">C. Maquinaria Y Equipo Y Otros (Muebles Y Enseres) <button class="btn btn-primary btn-outline btn-sm" id="btnANCEnseres">+</button> </td>
						<td class="tdConsolidado"> S/ 0.00 </td>
					</tr>
					<!-- <tr class="hidden">
						<th>Cant.</th>
						<th>Descipción</th>
						<th>Prec. Unit.</th>
						<th>SubTotal</th>
					</tr> -->
					<tr id="trTotalActivos">
						<td colspan="3">Total</td>
						<td class="tdConsolidado">S/ 0.00</td>
					</tr>
					<tr>
						<td class="" colspan="3">D. Unidad de transporte</td>
						<td class=""> S/ 4500.00 </td>
					</tr>
					<tr>
						<th>Cant.</th>
						<th>Descipción</th>
						<th>Prec. Unit.</th>
						<th>SubTotal</th>
					</tr>
					<tr>
						<td>1</td>
						<td>Moto Honda 2013</td>
						<td>4500</td>
						<td>4500</td>
					</tr>
					<tr>
						<th class="" colspan="3">2. Pasivo</th>
						<td class=""> S/ 20 000.00 </td>
					</tr>
					<tr>
						<th class="" colspan="3">2.1 Pasivo Corriente</th>
						<td class=""> S/ 10 000.00 </td>
					</tr>
					<tr>
						<td class="" colspan="3">A. Pago de Proveedores</td>
						<td class=""> S/ 10 000.00 </td>
					</tr>
					<tr>
						<td class="" colspan="3">B. Otras cuentas por pagar</td>
						<td class="">  </td>
					</tr>
					<tr>
						<td class="" colspan="3">C. Préstamos a CP Menor a 12 meses</td>
						<td class=""> S/ 10 000.00 </td>
					</tr>
					<tr>
						<th class="" colspan="3">2.2 Pasivo No Corriente</th>
						<td class=""> S/ 10 000.00 </td>
					</tr>
					<tr>
						<td class="" colspan="3">C. Préstamos a LP Mayor a 12 meses</td>
						<td class=""> S/ 10 000.00 </td>
					</tr>
					<tr>
						<td class="" colspan="3">3. Patrimonio</td>
						<td class=""> S/ 21426.45 </td>
					</tr>
					<tr>
						<td class="" colspan="3">4. Pasivo + Patrimonio</td>
						<td class=""> S/ 41426.45 </td>
					</tr>
				</tbody>
				</table>
			</div>
		<div class="col col-sm-6">
			<table class="table table-hover">
				<thead>
					<tr>
						<th class="text-center" colspan="3">Estado de resultados</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="text-center" colspan="2">Capital + Interés</th>
						<td class="text-center"> S/ 41 426.45</td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>
		<!-- <thead>
			<tr>
				<th class="text-center" colspan="3">BALANCE GENERAL PRESTAR HUANCAYO</th>
				<th class="text-center">ESTADO DE RESULTADOS</th>
			</tr>
			
		</thead>
		<tbody>
			<tr>
				<th>Activo</th>
				<td colspan="2">S/ 0.00</td>
				<th>Capital + Interés</th>
				<td>S/ 0.00</td>
			</tr>
			<tr>
				<th>1.1 Activo Corriente</th>
				<td colspan="2">S/ 0.00</td>
				<th>Capital</th>
				<td>S/ 0.00</td>
			</tr>
			<tr>
				<th>A. Disponible</th>
				<td colspan="2">S/ 0.00</td>
				<th>Utilidad Bruta-Margen Bruto</th>
				<td>S/ 0.00</td>
			</tr>
			<tr>
				<th>B. Caja</th>
				<td colspan="2"></td>
				<th>Gastos operativos del negocio</th>
				<td>S/ 0.00</td>
			</tr>
			<tr>
				<th>C. Bancos</th>
				<td colspan="2">S/ 0.00</td>
				<th>Gastos de personal</th>
				<td></td>
			</tr>
		</tbody> -->
		<?php
		break;
		case "R10":
			$sumCapital=0; $sumInteres=0; $sumComision=0; $sumCuota=0; $sumMora=0; $sumTotal=0; $total=0;
		?> 
			<table class="table table-hover" id="resultadoReporte">
				<thead>
					<tr>
						<th>Apellidos y nombres de Cliente</th>
						<th>Capital</th>
						<th>Interés</th>
						<th>Com. y Ser.</th>
						<th>Cuota</th>
						<th>Mora</th>
						<th>Total pagado</th>
						<th>Fecha de pago</th>
					</tr>
				</thead>
				<tbody>
			<?php
			$casoCuotas=[33, 80];
			$casoEspec = [87, 88];
			$casoMoras = [81, 89];
			$sql="SELECT c.*, ifnull(pc.cuotCuota,0) as cuotCuota, retornarInteresDeCuota(p.idPrestamo) as cuotInteres , cl.idCliente, cl.cliApellidoPaterno, cl.cliApellidoMaterno, cl.cliNombres, cuotSeg, cuotPago, pc.cuotCapital, pc.cuotInteres FROM `caja` c
			left join prestamo p on c.idPrestamo = p.idPrestamo
			inner join involucrados i on p.idPrestamo = i.idPrestamo
			inner join cliente cl on cl.idCliente = i.idCliente
			left join prestamo_cuotas pc on pc.idCuota = c.idCuota
			where cajaActivo=1
			and date_format(cajaFecha, '%Y-%m') = '{$_POST['fMes']}' and idTipoProceso not in (43, 86) and cajaFecha >= '2020-01-17' and not (idTipoProceso = 88 and cajaValor=0) and not (idTipoProceso = 88 and cajaFecha > '2020-05-01') and i.idTipoCliente=1
			order by cajaFecha asc ; ";
			//echo $sql;
			$resultado=$cadena->query($sql);
			while ($row = $resultado->fetch_assoc() ) {
				if($row['cuotCuota']==0){ $porcPago=0; }else{ $porcPago = round($row['cajaValor'] / ($row['cuotCuota'] + $row['cuotSeg'] ), 5); }
				
				?>
				<tr data-id="<?= $row['idCaja'];?>" data-cliente="<?= $row['idCliente']; ?>" data-proceso="<?= $row['idTipoProceso']; ?>">
					<td class="tdApellidos"><?= ucwords($row['cliApellidoPaterno']." ".$row['cliApellidoMaterno']." ".$row['cliNombres']); ?></td>
					<td class="tdCapital">
						<?php if($porcPago==0){ echo 0; $tdCapital =0; }else{ $tdCapital = round($row['cuotCapital'] * $porcPago, 2); $sumCapital += $tdCapital; echo $tdCapital;} ?>
					</td>
					<td class="tdInteres">
						<?php if($porcPago==0){ echo 0; $tdInteres =0; }else{ $tdInteres = round($row['cuotInteres'] * $porcPago, 2); $sumInteres += $tdInteres; echo $tdInteres;} ?>
					</td>
					<td class="tdComision">
						<?php if($porcPago==0){ echo 0; $tdComision =0; }else{ $tdComision = round($row['cuotSeg'] * $porcPago, 2); $sumComision += $tdComision; echo $tdComision;} ?>
					</td>
					<td class="tdCuota">
						<?php if($porcPago==0){ echo 0; $tdCuota =0; }else{ $tdCuota = round(($row['cuotCuota'] + $row['cuotSeg']) * $porcPago, 2); $sumCuota += $tdCuota; echo $tdCuota;} ?>
					</td>
					<td class="tdMora"><?php if(in_array($row['idTipoProceso'], $casoMoras) ){ $sumMora+=floatval($row['cajaValor']); echo floatval($row['cajaValor']); }else{ echo 0;} ?></td>
					<td class="tdTotal"><?php /* $sumTotal+= floatval($row['cajaValor']); echo $row['cajaValor']; */
						if( $row['cajaFecha']<='2020-05-19'){
							if( $row['idTipoProceso'] ==33 ){
								$pagov3 = $row['cajaValor'];
							}else{
								$pagov3 = $row['cuotPago'];
							}
						}else{
							$pagov3 = $row['cajaValor'];
						}
						$sumTotal+= $pagov3;
						echo $pagov3;
					 ?></td>
					<td class="tdFecha"><?php $fecha= new DateTime($row['cajaFecha']); echo $fecha->format('d/m/Y'); ?></td>
				</tr>
							
			<?php }
			$i=1;
			 ?> </tbody>
			 <tfoot>
				 <tr>
					 <th></th>
					 <th>S/ <?= number_format(round($sumCapital,1), 2); ?></th>
					 <th>S/ <?= number_format(round($sumInteres,1), 2); ?></th>
					 <th>S/ <?= number_format(round($sumComision,1), 2); ?></th>
					 <th>S/ <?= number_format(round($sumCuota,1), 2); ?></th>
					 <th>S/ <?= number_format(round($sumMora,1), 2); ?></th>
					 <th>S/ <?= number_format(round($sumTotal,1), 2); ?></th>
				 </tr>
			 </tfoot>
				</table>
			
		<?php
		break;
		case 'R5':
		
			$sql="SELECT pre.`idPrestamo`, `fechaFinPrestamo`, presMontoDesembolso, preInteresPers, presPeriodo,
			cliApellidoPaterno, cliApellidoMaterno, cliNombres, 'Fin de préstamo' as `tipoDescripcion`
					FROM `prestamo` pre 
					inner join involucrados i on i.idPrestamo = pre.idPrestamo inner join cliente cl on i.idCliente = cl.idCliente
					where `fechaFinPrestamo` between '{$_POST['fInicio']} 00:00' and '{$_POST['fFinal']} 23:59:59'
					order by idPrestamo";
			$resultado=$cadena->query($sql);
			?> 
			<thead>
					<tr>
						<th>Préstamo</th>
						<th>Cliente</th>
						<th>Proceso</th>
						<th>Inversión</th>
						<th>Fecha</th>
						
					</tr>
				</thead>
				<tbody>
		<? while($row=$resultado->fetch_assoc()){ 
			$sumaTodo = $sumaTodo + $row['presMontoDesembolso']; ?>
				<tr>
					<td class="tableexport-string"><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo'];?></a></td>
					<td class='mayuscula'><?= $row['cliApellidoPaterno'].' '.$row['cliApellidoMaterno'].', '.$row['cliNombres'];?></td>
					<td><?= $row['tipoDescripcion']?></td>
					<td>S/ <?= number_format($row['presMontoDesembolso'],2);?></td>
					<td><? $fechaCaj= new DateTime($row['fechaFinPrestamo']); echo $fechaCaj->format('d/m/Y h:m a');?></td>
				</tr>
		<? } //end de while ?> 
				</tbody>
				<tfoot>
					<td></td>
					<td></td>
					<td></td>
					<th>S/ <?= number_format($sumaTodo,2);?></th>
					<td></td>
				</tfoot>
			<?
			break;
		case "R11":
				$sql="SELECT ca.*, tp.tipoDescripcion, date_format(cajaFecha, '%d/%m/%Y' ) as fecha FROM `caja` ca
				inner join tipoproceso tp on tp.idtipoproceso = ca.idtipoproceso
				where ca.idTipoProceso in (87,88) and cajaActivo=1 and date_format(cajaFecha,'%Y-%m-%d') between '{$_POST['fInicio']}' and '{$_POST['fFinal']}' order by cajaFecha; ";
				$resultado=$cadena->query($sql);
				$sumMontos=0; $k=0;
				?>
				<thead>
					<tr>
						<th>N°</th>
						<th>Préstamo</th>
						<th>Tipo</th>
						<th>Fecha</th>
						<th>Valor</th>
						<th>Observación</th>
						
					</tr>
				</thead>
				<tbody>
				<?php
				while($row=$resultado->fetch_assoc()){ 
					$sumMontos+= $row['cajaValor'];
				?>
				<tr>
					<td><?= $k+1; ?></td>
					<td class="tableexport-string"><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo']; ?></a></td>
					<td><?= $row['tipoDescripcion']; ?></td>
					<td class="tableexport-string"><?= $row['fecha']; ?></td>
					<td class="tableexport-string">S/ <?= number_format($row['cajaValor'],2); ?></td>
					<td class="tableexport-string"><?= $row['cajaObservacion']; ?></td>
				</tr>
				<?php
				$k++; }?>
				</tbody>
				<tfoot>
					<td></td>
					<td></td>
					<td></td>
					<th><?= number_format(round($sumMontos,1), 2); ?></th>
					<td></td>
					
				</tfoot>
				<?php
				
				
			break;
	default:
		# code...
		break;
}
?>