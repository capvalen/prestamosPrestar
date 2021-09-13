<?
require("conkarl.php");
//var_dump($_POST);
$filas = array();
$sql="SELECT * FROM `vistas`
where activo =1 and idUsuario = {$_POST['usuario']} order by idPrestamo asc; ";
$resultado=$cadena->query($sql);
while($row=$resultado->fetch_assoc()){ 
	$filas[] = $row;
}
echo json_encode($filas);