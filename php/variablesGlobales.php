<?php
include 'conkarl.php';
$admis=array(1,);

$soloAdmis=array(1);
$soloDios=array(1);
$soloCaja=array(4);
$soloEspecial=array(8);
$soloAutorizados=array(1,4);

$i=0;

$sql='SELECT * FROM `datosgenerales`;';
$resultado=$cadena->query($sql);
while($row=$resultado->fetch_row()){
  $variablesG[$i]= $row;
  $i++;
}

$mora= floatval($variablesG[6][2]);



?>
