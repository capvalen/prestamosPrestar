<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

include 'conkarl.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') $_POST = json_decode(file_get_contents("php://input"), true);
else return false;

//var_dump($_POST); die();

switch ($_POST['pedir']) {
	case 'listar': listar($db); break;
	case 'actualizar': actualizar($db); break;
	case 'updateHeader': updateHeader($db); break;
	default: break;
}



function listar($db){

	$cliente = [];
	$ingresos = [];
	$deudas = [];
	$gastos = [];

	$sql = $db->prepare("SELECT c.*,
	ca.calDescripcion, lower(a.addrDireccion) as addrDireccion, lower(a.addrReferencia) as addrReferencia, a.addrNumero,
	lower(di.distrito) as distrito, lower(pro.provincia) as provincia, lower(de.departamento) as departamento				
	FROM `cliente` c 
	inner join involucrados i on i.idCliente = c.idCliente
	inner join address a on a.idAddress = c.cliDireccionCasa
	inner join distrito di on di.idDistrito = a.idDistrito
	inner join provincia pro on pro.idProvincia = a.idProvincia
	inner join departamento de on de.idDepartamento = a.idDepartamento
	inner join calles ca on ca.idCalle = a.idCalle
	where i.idTipoCliente = 1 and i.idPrestamo = ?;");
	if($sql->execute([ $_POST['idPrestamo'] ])){
		$row = $sql->fetch(PDO::FETCH_ASSOC);

		$cliente = array(
			'idCliente' => $row['idCliente'],
			'nombres' => $row['cliApellidoPaterno'] . ' ' . $row['cliApellidoMaterno'] . ' ' . $row['cliNombres'],
			'dni' => $row['cliDni'],
			'direccion' => "{$row['calDescripcion']} {$row['addrDireccion']} {$row['addrNumero']} Ref: {$row['addrReferencia']} - {$row['distrito']} - {$row['provincia']} - {$row['departamento']}",
			'giro' => $row['giroNegocio']
		);
	}


	if($_POST['idEvaluacion']<>'no'):
		$sqlEvaluacion = $db->prepare("SELECT * from evaluacion where id = ? and activo = 1;");
		$sqlEvaluacion->execute([ $_POST['idEvaluacion'] ]);
		$rowEvaluacion = $sqlEvaluacion->fetch(PDO::FETCH_ASSOC);
		$contenido = json_decode($rowEvaluacion['contenido'], true);
		$ingresos = $contenido['ingresos'];
		$deudas = $contenido['deudas'];
		$gastos = $contenido['gastos'];
	endif;

	echo json_encode(array(
		'cliente' => $cliente,
		'ingresos' => $ingresos,
		'deudas' => $deudas,
		'gastos' => $gastos
	));

}

function crearIdEvaluacion($db){
	$sqlCrear = $db->prepare("INSERT INTO `evaluacion`(`idPrestamo`, `contenido`) VALUES (?, '[]')");
	if($sqlCrear->execute([$_POST['idPrestamo']])):
		$idEvaluacion = $db->lastInsertId();
	else: $idEvaluacion = -1;
	endif;

	$_POST['idEvaluacion'] = $idEvaluacion;
	return $idEvaluacion;
}

function actualizar($db){
	if($_POST['idEvaluacion'] == 'no') crearIdEvaluacion($db);
	//var_dump($_POST['deudas'], $_POST['idEvaluacion'] );

	$contenido = array(
		'ingresos' => $_POST['ingresos'],
		'deudas' => $_POST['deudas'],
		'gastos' => $_POST['gastos'],
	);
	
	$sqlAdd= $db->prepare("UPDATE `evaluacion` SET `contenido`= ? WHERE id = ? and activo = 1");
	if($sqlAdd->execute([ json_encode($contenido), $_POST['idEvaluacion'] ])):
		echo json_encode(array(
			'idEvaluacion' => $_POST['idEvaluacion'],
			'accion' => 'ok'
		));
	else:
		echo json_encode(array(
			'accion' => 'error'
		));
	endif;
	
}

function updateHeader($db){
	$sqlAdd= $db->prepare("UPDATE `cliente` SET `giroNegocio`= ? WHERE idCliente = ? ;");
	if($sqlAdd->execute([ $_POST['giro'], $_POST['idCliente'] ])):
		echo 'ok';
	else:
		echo 'error';
	endif;
	
}


?>