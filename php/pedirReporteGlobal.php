<?php
error_reporting(E_ALL); ini_set("display_errors", 1);
include 'conkarl.php';
$_POST = json_decode(file_get_contents('php://input'), true);

$intereses =[]; $moras =[]; $cuotas =[]; $otrosIngresos =[]; $bancos =[]; $servicios =[]; $sueldos =[]; $otrosGastos =[]; $falta =[];

//---------- ENTRADAS ----------

$sqlIntereses=$db->prepare("SELECT sum(`cajaValor`) as suma FROM `caja` WHERE 
`idTipoProceso` = 33
and year(`cajaFecha`) = ? and month(`cajaFecha`) ?;");

$sqlMoras=$db->prepare("SELECT sum(`cajaValor`) as suma FROM `caja` WHERE 
`idTipoProceso` = 81
and year(`cajaFecha`) = ? and month(`cajaFecha`) ?;");

$sqlCuotas = $db->prepare("SELECT c.idCuota, `cajaValor`, pc.cuotCapital, pc.cuotInteres, pc.cuotSeg, round(cajaValor / (pc.cuotCapital + pc.cuotInteres+ pc.cuotSeg ),2) as porcentaje
FROM `caja` c
inner join prestamo_cuotas pc on pc.idCuota = c.idCuota
WHERE c.`idTipoProceso` in (33, 80)
and year(`cajaFecha`) = ? and month(`cajaFecha`) ?;");
//Calcular, cuando sea ==1: la cuota se toma por defecto.
// sino debe sacar el porcentaje a partir del capital

//Otros ingresos
$sqlOtrosIngresos=$db->prepare("SELECT sum(`cajaValor`) as suma FROM `caja` WHERE 
`idTipoProceso` = 94
and year(`cajaFecha`) = ? and month(`cajaFecha`) ?;");


//---------- SALIDAS ----------

//Bancos:
$sqlBancos=$db->prepare("SELECT cajaValor, cajaObservacion  FROM `caja` WHERE 
`idTipoProceso`= 93
and year(`cajaFecha`) = ? and month(`cajaFecha`) ?;");


//Servicios:
$sqlServicios=$db->prepare("SELECT cajaValor, cajaObservacion FROM `caja` WHERE 
`idTipoProceso`= 92
and year(`cajaFecha`) = ? and month(`cajaFecha`) ?;");

//Sueldos:
$sqlSueldos=$db->prepare("SELECT cajaValor, cajaObservacion FROM `caja` WHERE 
`idTipoProceso`= 40
and year(`cajaFecha`) = ? and month(`cajaFecha`) ?;");

//Otros gastos:
$sqlOtrosGastos=$db->prepare("SELECT sum(`cajaValor`) as suma FROM `caja` WHERE 
`idTipoProceso`= 84
and year(`cajaFecha`) = ? and month(`cajaFecha`) ?;");


//---------- Por cobrar ----------
$sqlFalta = $db->prepare("SELECT idCuota, pc.cuotCapital, pc.cuotInteres, pc.cuotSeg, round(cuotPago / (pc.cuotCapital + pc.cuotInteres+ pc.cuotSeg ),2) as porcentaje FROM `prestamo_cuotas` pc
where idTipoPrestamo in (33, 79);");


//---------- Ejecución ----------
$sqlIntereses->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
$sqlMoras->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
$sqlCuotas ->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
$sqlOtrosIngresos ->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
$sqlBancos ->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
$sqlServicios ->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
$sqlSueldos ->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
$sqlOtrosGastos ->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
$sqlFalta ->execute([ $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);

while($rowIntereses = $sqlIntereses->fetch(PDO::FETCH_ASSOC))
	$intereses [] = $rowIntereses;
while($rowMoras = $sqlMoras->fetch(PDO::FETCH_ASSOC))
	$moras [] = $rowMoras;
while($rowCuotas = $sqlCuotas->fetch(PDO::FETCH_ASSOC))
	$cuotas [] = $rowCuotas;
while($rowOtrosIngresos = $sqlOtrosIngresos->fetch(PDO::FETCH_ASSOC))
	$otrosIngresos [] = $rowOtrosIngresos;
while($rowBancos = $sqlBancos->fetch(PDO::FETCH_ASSOC))
	$bancos [] = $rowBancos;
while($rowServicios = $sqlServicios->fetch(PDO::FETCH_ASSOC))
	$servicios [] = $rowServicios;
while($rowSueldos = $sqlSueldos->fetch(PDO::FETCH_ASSOC))
	$sueldos [] = $rowSueldos;
while($rowOtrosGastos = $sqlOtrosGastos->fetch(PDO::FETCH_ASSOC))
	$otrosGastos [] = $rowOtrosGastos;
while($rowFalta = $sqlFalta->fetch(PDO::FETCH_ASSOC))
	$falta [] = $rowFalta;

echo json_encode(
	array('intereses' => $intereses, 'moras' => $moras, 	'cuotas' => $cuotas, 	'otrosIngresos' => $otrosIngresos, 	'bancos' => $bancos, 	'servicios' => $servicios, 	'sueldos' => $sueldos, 	'otrosGastos' => $otrosGastos, 'falta' => $falta 	)
);
?>