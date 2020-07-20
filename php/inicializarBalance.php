<?php 

include 'conkarl.php';

$sql="INSERT INTO `balanceGeneral` (`idBalance`, `balaAño`, `balaMes`, `balaVariable`, `balaValor`, `balaJson`) VALUES (NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A111Disponible', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A1111Caja', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A112Bancos', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A112CuentasPorCobrar', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A113OtrasCuentas', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A114Adelantos', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A115Mercaderia', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A121Inmueble', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A1211Terrenos', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A1213Unidad', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A211Proveedores', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A212OrasCuentas', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'A31Patrimonio', '0', ''),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'listaMaquinarias', '0', '[]'),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'listaPrestamosMenor', '0', '[]'),
(NULL, '{$_POST['año']}', '{$_POST['mes']}', 'listaPrestamos', '0', '[]');
";
$resultado=$cadena->query($sql);
echo "todo ok";
?>