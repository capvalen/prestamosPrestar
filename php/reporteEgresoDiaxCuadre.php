<?php
require("conkarl.php");
require_once('vendor/autoload.php');
$base58 = new StephenHill\Base58();

//echo "call reporteEgresoDiaxCuadre('".$_GET['cuadre']."');";
$sql = mysqli_query($conection,"call reporteEgresoDiaxCuadre('".$_GET['cuadre']."');");
$totalRow= mysqli_num_rows($sql);
$sumaIngr=0;
$boton='';
 
$i=0;
$efectivo=0; $banco=0; $tarjeta=0;

if($totalRow==0){
	echo "<tr> <th scope='row'></th> <td >No se encontraron resultados en ésta fecha.</td> <td class='mayuscula'></td>  <td>S/ <span id='strSumaSalida' id='strSumaSalida' data-efectivo ='{$efectivo}' data-banco ='$banco' data-tarjeta ='{$tarjeta}'>0.00</span></td></tr>";
}else{
	while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
	{
		$i++;
		$sumaIngr+=floatval($row['pagoMonto']);
		switch($row['cajaMoneda']){
			case '1': 
				$efectivo = $efectivo + $row['pagoMonto'];
			break;
			case '2': 
				$banco = $banco + $row['pagoMonto'];
			break;
			case '3':
			case '4':
				$tarjeta = $tarjeta + $row['pagoMonto'];
			break;
			default:
			break;
		}
		if($row['idPrestamo']<>0){$codTemp= $base58->encode('00'.$row['idPrestamo']);}

		if($_COOKIE['ckPower']==1 ):
			$boton = "<button class='btn btn-sm btn-negro btn-outline btnEditarCajaMaestra'><i class='icofont icofont-edit'></i></button> <button class='btn btn-sm btn-azul btn-outline btnPrintCajaEsp' data-caja='{$row["idCaja"]}' data-boton='{$row["idTipoProceso"]}'><i class='icofont icofont-print'></i></button> ";
		elseif( $_COOKIE['ckPower']==4 ):
			$boton = "<button class='btn btn-sm btn-azul btn-outline btnPrintCajaEsp' data-caja='{$row["idCaja"]}' data-boton='{$row["idTipoProceso"]}'><i class='icofont icofont-print'></i></button> <button class='btn btn-sm btn-success btn-outline btnChangeMonedaEsp' data-caja='{$row["idCaja"]}' ><i class='icofont icofont-pen-alt-3'></i></button> ";
		else: $boton='';
		endif;
		$hora = new DateTime($row['cajaFecha']);
		?>

		<tr data-id="<?= $row['idCaja']; ?>" data-activo="<?= $row['cajaActivo']; ?>" data-faltan="<?= $row['toFin']?>">
			<th scope='row' class="aCode">
				<a href="<?= ($row['idPrestamo']>0) ? 'creditos.php?credito='.$codTemp : '#!'; ?>" > <?= ($row['idPrestamo']>0) ? 'CR-'.$row['idPrestamo'] : 'CR-0'; ?>
				</a>
			</th>
			<td class="mayuscula aCliente"><?= $row['cliNombres'];?></td>
			<td class='mayuscula tpIdDescripcion'><?= $row['tipoDescripcion'];?></td>
			<td><i class="icofont icofont-bubble-right"></i> <em class="emRegistra"><?= $row['usuNick'];?></em></td>
			<td>S/ <span class='spanCantv3'><?= $row['pagoMonto'];?></span></td>
			<td class='mayuscula tdMoneda' data-id="<?= $row['cajaMoneda'];?>"><?= $row['moneDescripcion'];?> [<?= $hora->format('h:i a');?>]</td>
			<td class='mayuscula tdObservacion'><?= $row['cajaObservacion'];?></td>
			<td class="tdBotones"><span class="sr-only fechaPagov3"><?= $row['cajaFecha'];  ?></span> <?= $boton;?></td>
		</tr>
		<?php 
		if($totalRow==$i){
			echo '<tr> <th scope="row"  style="border-top: transparent;"></th>  <td style="border-top: transparent;"></td> <td style="border-top: transparent;"></td> <td class="text-center" style="border-top: 1px solid #989898; color: #636363"><strong >Total</strong></td> <td style="border-top: 1px solid #989898; color: #636363"><strong >S/ <span id="strSumaSalida" data-efectivo ="'.$efectivo.'" data-banco ="'.$banco.'" data-tarjeta ="'.$tarjeta.'">'.number_format(round($sumaIngr,1,PHP_ROUND_HALF_UP),2, ',', '').'</span></strong></td><tr>';
		}
	}
}
mysqli_close($conection); //desconectamos la base de datos

?>