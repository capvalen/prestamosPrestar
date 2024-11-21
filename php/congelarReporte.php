<?
include 'conkarl.php';
$_POST = json_decode(file_get_contents('php://input'), true);

if($_POST['pedir'] =='congelar' ){
	$sql = $db->prepare("INSERT INTO `vistacongelada`(`campos`, `año`, `mes`) VALUES (?, ?, ?);");
	$resultado = $sql->execute([ $_POST['campos'], $_POST['fecha']['año'], $_POST['fecha']['mes'] ]);
	//$sql->debugDumpParams();
	if($resultado)
		echo 'ok';
	else echo 'error';
}

if($_POST['pedir']=='listar'){
	$sql = $db->prepare("SELECT campos FROM `vistacongelada` where año = ? and mes = ?;");
	$sql->execute([$_POST['fecha']['año'], $_POST['fecha']['mes']]);
	$row = $sql->fetch(PDO::FETCH_ASSOC);
	echo $row['campos'];
}