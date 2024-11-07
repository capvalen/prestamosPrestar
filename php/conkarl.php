<?php
$server="localhost";

/* Net	*/
$username="root";
$password="*123456*";
$datab='prestamosprestar';

//global $conection;
global $cadena;

$conection= mysqli_connect($server,$username,$password)or die("No se ha podido establecer la conexion");
$sdb= mysqli_select_db($conection,$datab)or die("La base de datos no existe");
$conection->set_charset("utf8");
mysqli_set_charset($conection,"utf8");

$cadena= new mysqli($server, $username, $password, $datab);
$cadena->set_charset("utf8");

$esclavo= new mysqli($server, $username, $password, $datab);
$esclavo->set_charset("utf8");

$prisionero= new mysqli($server, $username, $password, $datab);
$prisionero->set_charset("utf8");

$preferido= new mysqli($server, $username, $password, $datab);
$preferido->set_charset("utf8");

//Con Objetos auxiliar:
try {
	$db = new PDO (
		'mysql:host=localhost;
		dbname='.$datab,
		$username,
		$password,
		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
	);
} catch (Exception $e) {
	echo "Problema con la conexion: ".$e->getMessage();
}


?>