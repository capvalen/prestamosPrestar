<?php

function contarRecurrentes($idCli, $cadena){
	$sql="SELECT count(p.idPrestamo) as recurrente FROM `prestamo` p
	inner join involucrados i on i.idPrestamo = p.idPrestamo
	inner join cliente c on c.idCliente = i.idCliente
	where i.idTipoCliente = 1 and fechaFinPrestamo<>'' and i.idCliente = {$idCli}";
	$resultado=$cadena->query($sql);
	$row=$resultado->fetch_row();
	return $row[0];

}

?>