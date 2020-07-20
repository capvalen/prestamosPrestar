<?php 
include 'conkarl.php';

//echo json_encode($_POST['listaMaquinarias']);
if( json_encode( $_POST['listaMaquinarias'] )=='"[]"'){ $maquinarias ='[]'; }else{ $maquinarias= json_encode($_POST['listaMaquinarias']); }
if( json_encode( $_POST['listaPrestamosMenor'] )=='"[]"'){ $menor ='[]'; }else{ $menor= json_encode($_POST['listaPrestamosMenor']); }
if( json_encode( $_POST['listaPrestamos'] )=='"[]"'){ $mayor ='[]'; }else{ $mayor= json_encode($_POST['listaPrestamos']); }


$sql="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A111Disponible']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A111Disponible' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A1111Caja']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A1111Caja' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A112Bancos']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A112Bancos' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A112CuentasPorCobrar']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A112CuentasPorCobrar' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A113OtrasCuentas']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A113OtrasCuentas' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A114Adelantos']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A114Adelantos' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A115Mercaderia']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A115Mercaderia' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A121Inmueble']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A121Inmueble' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A1211Terrenos']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A1211Terrenos' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A1213Unidad']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A1213Unidad' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A211Proveedores']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A211Proveedores' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A212OrasCuentas']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A212OrasCuentas' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaValor`= '{$_POST['A31Patrimonio']}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'A31Patrimonio' ; ";

$sql.="UPDATE `balanceGeneral` SET `balaJson`= '{$maquinarias}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'listaMaquinarias' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaJson`= '{$menor}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'listaPrestamosMenor' ; ";
$sql.="UPDATE `balanceGeneral` SET `balaJson`= '{$mayor}' WHERE `balaAño` = {$_POST['año']} and `balaMes`={$_POST['mes']} and balaVariable = 'listaPrestamos' ; ";
//echo $sql;
$resultado=$cadena->multi_query($sql);


if($cadena->errno){ die($cadena->error); }
echo "todo ok";
?>