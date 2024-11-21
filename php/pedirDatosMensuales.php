<?php
error_reporting(E_ALL); ini_set("display_errors", 1);
include 'conkarl.php';
$_POST = json_decode(file_get_contents('php://input'), true);

$salidas = []; $entradas = [];

$sqlSalidas = $db->prepare("SELECT
c.idCaja, ROUND(cajaValor,2) as pagoMonto, cajaFecha, replace(cajaObservacion, 'Ingreso extra: ', '') as cajaObservacion, 
u.usuNombres as usuNick, tp.tipoDescripcion, m.moneDescripcion, c.cajaActivo, c.cajaMoneda, c.idPrestamo, c.idTipoProceso, retornarDuenoDeCaja(c.idCaja) as cliNombres
FROM `caja` c
inner join tipoproceso tp on tp.idTipoProceso = c.idTipoProceso
LEFT JOIN usuario u on u.idUsuario=c.idUsuario
inner join moneda m on m.idMoneda = c.cajaMoneda
where month(`cajaFecha`) = ? and year(cajaFecha) = ?
and tp.idTipoProceso in (43, 40, 41, 78, 82, 83, 84, 85, 92, 93)
and cajaActivo=1;");
$sqlSalidas->execute([ $_POST['fecha']['mes'], $_POST['fecha']['año'] ]);
while($rowSalidas = $sqlSalidas->fetch(PDO::FETCH_ASSOC))
	$salidas[] = $rowSalidas;

/*
33 Pago parcial de interés
36 Penalización (Gasto Admin.)
80 Cuota pagada
81 Pago de mora
87 Pago de seguro
88 Com. y Serv.
89 Mora extraordinaria
*/
$sqlEntradas = $db->prepare("SELECT
c.idCaja, ROUND(cajaValor,2) as pagoMonto, cajaFecha, replace(cajaObservacion, 'Ingreso extra: ', '') as cajaObservacion, 
u.usuNombres as usuNick, tp.tipoDescripcion, m.moneDescripcion, c.cajaActivo, c.cajaMoneda, c.idPrestamo, c.idTipoProceso, retornarDuenoDeCaja(c.idCaja) as cliNombres
FROM `caja` c
inner join tipoproceso tp on tp.idTipoProceso = c.idTipoProceso
LEFT JOIN usuario u on u.idUsuario=c.idUsuario
inner join moneda m on m.idMoneda = c.cajaMoneda
where month(`cajaFecha`) = ? and year(cajaFecha) = ?
and tp.idTipoProceso in (33, 36, 80, 81, 87, 88, 89)
and cajaActivo=1;");
$sqlEntradas->execute([ $_POST['fecha']['mes'], $_POST['fecha']['año'] ]);
while($rowEntradas = $sqlEntradas->fetch(PDO::FETCH_ASSOC))
	$entradas[] = $rowEntradas;

echo json_encode(
	array('salidas' => $salidas, 'entradas' => $entradas)
);
?>