<?php 
include 'conkarl.php';
header('Content-Type: text/html; charset=utf8');

if( isset($_COOKIE['ckidUsuario'])) {
	if( $_POST['tipo']== 90 || $_POST['tipo'] == 91 ){
		$sql= "call insertarProcesoOmiso2 (".$_POST['tipo'].", ".$_POST['valor'].", '".$_POST['obs']."', ".$_COOKIE['ckidUsuario'].", ".$_POST['moneda'].", ".$_POST['porInteres'].", '".$_POST['socio']."' )";
	}else{
		$sql= "call insertarProcesoOmiso (".$_POST['tipo'].", ".$_POST['valor'].", '".$_POST['obs']."', ".$_COOKIE['ckidUsuario'].", ".$_POST['moneda']." )";
	}
	//echo $sql;
	if ($conection->query($sql)) { //Ejecución mas compleja con retorno de dato de sql del procedure.

		echo true;
	}else{echo mysql_error( $conection);}
}else{
	echo "El tiempo de sesión de su usuario, terminó, actualice la página y vuelva a acceder, por favor";
}


 ?>