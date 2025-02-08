<?

if($_COOKIE['ckPower']=='1'){
	
	$linea = "SELECT presMontoDesembolso, presPeriodo, tpr.tpreDescipcion,
	u.usuNombres, preInteresPers, i.idCliente, pre.idPrestamo,
	case presFechaDesembolso when '0000-00-00 00:00:00' then 'Desembolso pendiente' else presFechaDesembolso end as `presFechaDesembolso`,
	case presAprobado when 0 then 'Sin aprobar' when 2 then 'Rechazado' else 'Aprobado' end as `presAprobado`,
	lower(concat (c.cliApellidoPaterno, ' ', c.cliApellidoMaterno, ' ', c.cliNombres)) as cliNombres, retornarCantidadCuotasVencidas(pre.idPrestamo) as cuotVencDias, retornarPrimeraFecha(pre.idPrestamo) as primeraFecha
	FROM `prestamo` pre
	inner join involucrados i on i.idPrestamo = pre.idPrestamo
	inner join cliente c on c.idCliente = i.idCliente
	inner join usuario u on u.idUsuario = pre.idUsuario
	inner join tipoprestamo tpr on tpr.idTipoPrestamo = pre.idTipoPrestamo
	where presActivo =1 and cast(presFechaDesembolso as char) <> '0000-00-00 00:00:00' and presAprobado =1 and i.idTipoCliente=1 {$permiso}
	order by pre.idPrestamo asc;";
}
else if($_COOKIE['ckPower']=='2'){
	$linea = "SELECT presMontoDesembolso, presPeriodo, tpr.tpreDescipcion,
u.usuNombres, preInteresPers, i.idCliente, pre.idPrestamo,
case presFechaDesembolso when '0000-00-00 00:00:00' then 'Desembolso pendiente' else presFechaDesembolso end as `presFechaDesembolso`,
case presAprobado when 0 then 'Sin aprobar' when 2 then 'Rechazado' else 'Aprobado' end as `presAprobado`,
	lower(concat (c.cliApellidoPaterno, ' ', c.cliApellidoMaterno, ' ', c.cliNombres)) as cliNombres, retornarCantidadCuotasVencidas(pre.idPrestamo) as cuotVencDias, retornarPrimeraFecha(pre.idPrestamo) as primeraFecha
	FROM `prestamo` pre
	inner join involucrados i on i.idPrestamo = pre.idPrestamo
	inner join cliente c on c.idCliente = i.idCliente
	inner join usuario u on u.idUsuario = pre.idUsuario
	inner join tipoprestamo tpr on tpr.idTipoPrestamo = pre.idTipoPrestamo
	inner join vistas v on v.idPrestamo = pre.idPrestamo
	where i.idTipoCliente=1 and v.activo=1 and v.ver in (1,2) and v.idUsuario={$_COOKIE['ckidUsuario']}
	order by pre.idPrestamo asc;";
}
else { //consideremos cajas
	//if( !in_array($_COOKIE['ckPower'], $soloCajas ))
	//$permiso = ' and pre.idUsuario = ' . $_COOKIE['ckidUsuario'];
	$linea = "SELECT * from cliente WHERE idCliente=-1;";
}

$sql=$linea;
//echo $sql;
$resultado=$cadena->query($sql);
//echo $resultado->num_rows; die();
if($resultado->num_rows>0){

	$hoy=new DateTime();

	$i=0;
	while($row=$resultado->fetch_assoc()){
		if($row['presFechaDesembolso']=='Desembolso pendiente')
			$fPendiente =$row['presFechaDesembolso'];
		else{
			$fecha = new DateTime($row['presFechaDesembolso']);
			$fPendiente = $fecha->format('d/m/Y');
		}
		?>
		<tr>
			<td><?=$i+1;?></td>
			<td><a href="creditos.php?credito=<?= $base58->encode($row['idPrestamo']);?>">CR-<?= $row['idPrestamo'];?></a></td>
			<td class="mayuscula"><a href="clientes.php?idCliente=<?= $base58->encode($row['idCliente']); ?>"><?= $row['cliNombres'];?></a></td>
			<td><?= $row['tpreDescipcion'];?></td>
			<td>S/ <?= number_format($row['presMontoDesembolso'],2);?></td>
			<td><?= $row['presPeriodo'];?></td>
			<td><?php if($row['cuotVencDias']>0 || $row['primeraFecha']==$hoy->format('Y-m-d')){ echo '<span class="red-text">'.$row['cuotVencDias'].'</span>';}else{ echo '<span class="indigo-text text-darken-4">'.$row['cuotVencDias'].'</span>'; } ?></td>
			<td><?= $row['preInteresPers'];?></td>
			<td><?= $fPendiente;?></td>
		</tr>
	<? $i++; }
}else{
?>
<tr>
	<td colspan="6">No hay Créditos asignados a tu usuario.</td>
</tr>
<?php } ?>