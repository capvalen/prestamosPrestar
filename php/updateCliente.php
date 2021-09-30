<?
header('Content-Type: text/html; charset=utf8');
include 'conkarl.php';

//var_dump($_POST['jcCliente']['idCliente']);
$idCli= $_POST['jcCliente']['idCliente'];

$sql= "UPDATE `cliente` SET 
`cliDni`='{$_POST['jcCliente']['dni']}', `cliNombres`='{$_POST['jcCliente']['nombres']}', `cliApellidoPaterno`='{$_POST['jcCliente']['apellidoPaterno']}', `cliApellidoMaterno`='{$_POST['jcCliente']['apellidoMaterno']}',
`cliSexo`={$_POST['jcCliente']['sexo']},`cliNumHijos`={$_POST['jcCliente']['hijos']},
`cliDireccionesIgual`={$_POST['jcCliente']['esCasa']},
`cliCelularPersonal`='{$_POST['jcCliente']['celPersonal']}', `cliCelularReferencia`='{$_POST['jcCliente']['celRefencia']}',`idEstadoCivil`={$_POST['jcCliente']['estadocivil']}
WHERE `idCliente`= '{$idCli}';";
//echo $sql;

$sqlDireccion = "UPDATE `address` set
idCalle =  '{$_POST['jcCliente']['calleCasa']}',
idDepartamento =  '{$_POST['jcCliente']['departamentoCasa']}',
idProvincia =  '{$_POST['jcCliente']['provinciaCasa']}',
idDistrito =  '{$_POST['jcCliente']['distritoCasa']}',
idZona =  '{$_POST['jcCliente']['calleCasa']}',
addrNumero =  '{$_POST['jcCliente']['numeroCasa']}',
addrDireccion =  '{$_POST['jcCliente']['direccionCasa']}',
addrReferencia = '{$_POST['jcCliente']['referenciaCasa']}'
where idAddress = (select c.cliDireccionCasa from cliente c where idCliente = '{$idCli}' ); ";
//echo $sqlDireccion;
$cadena->query($sqlDireccion);

if ($conection->query($sql)) { 
	echo true;
}else{echo false;}


?>