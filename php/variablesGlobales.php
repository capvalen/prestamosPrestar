<?php
include 'conkarl.php';
$admis=array(1,);

$soloAdmis=array(1);
$soloDios=array(1);
$soloCaja=array(4);
$soloCajas=array(1,4);
$soloAnalistas =array(1,2);

$serverLocal= "//192.168.0.3/prestamosPrestar/";
$servidorLocal = $serverLocal;

$i=0;

$sql='SELECT * FROM `datosgenerales`;';
$resultado=$cadena->query($sql);
while($row=$resultado->fetch_row()){
  $variablesG[$i]= $row;
  $i++;
}

$mora= floatval($variablesG[6][2]);



?>
