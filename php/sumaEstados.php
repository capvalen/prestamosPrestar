<?php 

include 'conkarl.php';

$sql="SELECT  `idCaja`, c.`idPrestamo`, c.`idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, ifnull(pc.cuotCuota,0) as cuotCuota, cuotSeg, cuotPago, pc.cuotCapital, pc.cuotInteres
FROM `caja` c
	left join prestamo p on c.idPrestamo = p.idPrestamo
	inner join involucrados i on p.idPrestamo = i.idPrestamo
	inner join cliente cl on cl.idCliente = i.idCliente
	left join prestamo_cuotas pc on pc.idCuota = c.idCuota
where cajaActivo=1
	and date_format(cajaFecha, '%Y-%m') = '2020-07' and idTipoProceso not in (43, 86) and cajaFecha >= '2020-01-17' and not (idTipoProceso = 88 and cajaValor=0) and not (idTipoProceso = 88 and cajaFecha > '2020-05-01') and i.idTipoCliente=1
	order by cajaFecha asc;
	";
$resultado=$cadena->query($sql);

$sumaCapital=0;
$sumaIntereses=0;
$sumaComision=0;
$sumaMora=0;

while($row=$resultado->fetch_assoc()){ 
	
	switch ($row['idTipoProceso']) {
		case '80':
			
			if($row['cajaValor'] >= $row['cuotCuota'] + $row['cuotSeg']){
				$sumaCapital+= $row['cuotCapital'];
				$sumaIntereses+= $row['cuotInteres'];
				$sumaComision+= $row['cuotSeg'];
			}else if($row['cajaValor'] < $row['cuotCuota'] + $row['cuotSeg']){
				$proporcion = round($row['cajaValor'] / ( $row['cuotCuota'] + $row['cuotSeg'] ),4);
				$sumaCapital+= $row['cuotCapital'] * $proporcion;
				$sumaIntereses+= $row['cuotInteres'] * $proporcion;
				$sumaComision+= $row['cuotSeg'] * $proporcion;
			}

			break;
		case '33':
			$proporcion = round($row['cajaValor'] / ( $row['cuotCuota'] + $row['cuotSeg'] ),4);
			$sumaCapital+= $row['cuotCapital'] * $proporcion;
			$sumaIntereses+= $row['cuotInteres'] * $proporcion;
			$sumaComision+= $row['cuotSeg'] * $proporcion;
		break;
		case '81':
			$sumaMora+=$row['cajaValor'];
		break;
		
		default:
			# code...
			break;
	}
	

}
$sumaCapital = round($sumaCapital,2);
$sumaTodoCapitales = round($sumaCapital+$sumaComision+$sumaIntereses,2);
//$items []= array('todo' => $sumaCapital+$sumaComision+$sumaIntereses , 'soloCapital'=> $sumaCapital, 'soloMora'=> $sumaMora );
//return json_encode($items);

?>