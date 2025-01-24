<style>
.tdPagoATiempo, .tdVigente{
	color: #284cff;
}
.tdPagoFueraTiempo, .tdVigenteDebe{
	color: #ff2828;
}
</style>
<?php
require("conkarl.php");
date_default_timezone_set('America/Lima');
//RecotrantCuantoFaltaPagar(___, pre.intSimple)
$sql="SELECT pre.idPrestamo, presMontoDesembolso, pc.cuotCuota + + pc.cuotSeg as cuotCuota, presFechaDesembolso, fechaFinPrestamo,  tpe.tpreDescipcion, presPeriodo, u.usuNick, retornarFaltaCapital(pre.idPrestamo, pre.intSimple) as faltaSaldo
FROM `prestamo` pre
inner join prestamo_cuotas pc on pc.idPrestamo = pre.idPrestamo
inner join tipoprestamo tpe on tpe.idTipoPrestamo = pre.idTipoPrestamo
inner join involucrados i on i.idPrestamo = pc.idPrestamo 
inner join usuario u on u.idUsuario = pre.idUsuario
where i.idCliente={$base58->decode($_GET['idCliente'])}  and cuotCuota<>0 and pre.presAprobado<>2
group by pre.idPrestamo
order by cuotfechaPago desc;";


$resultado=$cadena->query($sql);
while($row=$resultado->fetch_assoc()){ ?>
	<tr>
		<td>Huancayo</td>
		<td><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>"><?= $row['idPrestamo']; ?></a></td>
		<td><?= number_format($row['presMontoDesembolso'],2); ?></td>
		<td><?= number_format($row['cuotCuota'],2); ?></td>
		<?php 
		if(!is_null($row['fechaFinPrestamo'])){
			?> <td>0.00</td> <?php
		}else{

		if( $row['faltaSaldo']>0 ){ ?>
			<td class="text-danger"><?= number_format($row['faltaSaldo'],2); ?></td>
		<?php }else{ ?>
			<td class="text-primary"><?= number_format($row['faltaSaldo'],2); ?></td>
			
		<?php }
		} ?>
		<td><?php if($row['presFechaDesembolso'] <>'0000-00-00 00:00:00'){ $fechaJ= new DateTime( $row['presFechaDesembolso']); echo $fechaJ->format('d/m/Y'); }else{echo 'Pendiente';}?></td>
		<td><?php if(is_null($row['fechaFinPrestamo'])){ echo '<span class="text-success">Vigente</span>';}else{echo '<span class="text-primary">Cancelado</span>'; } ?></td>
		<td><?= $row['tpreDescipcion']."(".$row['presPeriodo'].")"; ?></td>

		<?php 
		$idPres= $row['idPrestamo'];
		$sqlCuot="SELECT i.idCliente, cuotFechaPago, cuotFechaCancelacion FROM `prestamo_cuotas` pc
		inner join involucrados i on i.idPrestamo = pc.idPrestamo 
		where i.idTipoCliente=1 and pc.idPrestamo = {$idPres} and cuotCuota>0";

		$fechaHoy= new DateTime(date("Y-m-d"));
		$i=1;
		$resultadoCuot=$esclavo->query($sqlCuot);
		while($rowCuot=$resultadoCuot->fetch_assoc()){ 
			if($i<=15){ $i++; }else{
				if($i==16){ echo '<tr><td colspan=8>'; }
				$i=2;
			}
			if($rowCuot['cuotFechaCancelacion']=='0000-00-00'):
				$fechaCuota= new DateTime($rowCuot['cuotFechaPago']);
				$interval = $fechaCuota->diff($fechaHoy);
				if( $interval->format('%r%a') >= 0 ){
					//Esta en buena fecha
					echo "<td><span class='tdVigente'>". $interval->format('%R%a')."</span></td>";
				}else{
					//Esta con mora
					echo "<td><span class='tdVigenteDebe'>". $interval->format('%R%a')."</span></td>";
				}
				
				
			else:
				$fechaCuota= new DateTime($rowCuot['cuotFechaCancelacion']);
				$fechaPago= new DateTime($rowCuot['cuotFechaPago']);
				$interval = $fechaPago->diff($fechaCuota);
				

				if( $interval->format('%r%a') >= 0 ){
					//Esta en buena fecha
					echo "<td><span class='tdPagoATiempo'><ins>". $interval->format('%R%a')."</ins></span></td>";
				}else{
					//Esta con mora
					echo "<td><span class='tdPagoFueraTiempo'><ins>". $interval->format('%R%a')."</ins></span></td>";
				}

			endif;
		}
		for ($j=$i; $j <=15 ; $j++) { 
			echo "<td></td>";
			if($i==15){
				echo '</tr>';
			}
		}
		
		?>

	</tr>
<?php }

?>
