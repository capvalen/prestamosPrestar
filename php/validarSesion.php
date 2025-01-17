<?php 
// ini_set("session.cookie_lifetime","7200");
// ini_set("session.gc_maxlifetime","7200");
session_start();
header('Content-Type: text/html; charset=utf8');
include 'conkarl.php';
$clavePrivada= 'Es sencillo hacer que las cosas sean complicadas, pero difícil hacer que sean sencillas. Friedrich Nietzsche';
$path = '/';
$dominio = '.infocatsoluciones.com';

$fila = array(); $i=0;
//echo "select * from  usuario u inner join sucursal s on s.idSucursal=u.idSucursal where usuNick = '".$_POST['user']."' and usuPass='".md5($_POST['pws'])."' and usuActivo=1;";

$log = mysqli_query($conection,"select * from  usuario u inner join sucursal s on s.idSucursal=u.idSucursal where usuNick = '".$_POST['user']."' and usuPass='".md5($_POST['pws'])."' and usuActivo=1;");
//echo "select * from  usuario u inner join sucursal s on s.idSucursal=u.idSucursal where usuNick = '".$_POST['user']."' and usuPass='".md5($_POST['pws'])."' and usuActivo=1;";
$row = mysqli_fetch_array($log, MYSQLI_ASSOC);
if ($row['idUsuario']>=1){
	
	$expira=time()+60*60*3; //3 horas de cookie
	setcookie('ckidSucursal', $row['idSucursal'], $expira, $path, $dominio);
	setcookie('ckSucursal', $row['sucLugar'], $expira, $path, $dominio);
	setcookie('ckAtiende', $row['usuNombres'], $expira, $path, $dominio);
	setcookie('cknomCompleto', $row['usuNombres'].', '.$row['usuApellido'], $expira, $path, $dominio);
	setcookie('ckPower', $row['usuPoder'], $expira, $path, $dominio);
	setcookie('ckidUsuario', $row['idUsuario'], $expira, $path, $dominio);
	setcookie('ckCorreo', $row['usuEMail'], $expira, $path, $dominio);
	
	$sqlConf = mysqli_query( $conection,  "SELECT * FROM `datosgenerales`");
	while($rowConf = mysqli_fetch_array($sqlConf,MYSQLI_ASSOC)){
		$fila[$i] = $rowConf;
		$i++;
	}
	//var_dump( $fila);

	setcookie('cknombreEmpresa', $fila[0]['datoValor'], $expira, $path, $dominio);
	setcookie('ckrucEmpresa', $fila[1]['datoValor'], $expira, $path, $dominio);
	setcookie('ckdireccionEmpresa', $fila[2]['datoValor'], $expira, $path, $dominio);
	setcookie('cktelefonoEmpresa', $fila[3]['datoValor'], $expira, $path, $dominio);
	setcookie('cksucursalEmpresa', $fila[4]['datoValor'], $expira, $path, $dominio);
	setcookie('ckLemaEmpresa', $fila[7]['datoValor'], $expira, $path, $dominio);
	setcookie('ckcelularEmpresa', $fila[8]['datoValor'], $expira, $path, $dominio);

	echo $row['idUsuario'];
}

/* liberar la serie de resultados */
mysqli_free_result($log);

/* cerrar la conexión */
mysqli_close($conection);

?>