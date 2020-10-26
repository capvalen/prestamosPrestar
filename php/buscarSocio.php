<?php
require("conkarl.php"); 

date_default_timezone_set('America/Lima');

$sql="CALL buscarCliente('".$_POST['dni']."');";
$resultado=$cadena->query($sql);
$numSocios = $resultado->num_rows;
if( $numSocios>1){
	echo 'Existen DNIs duplicados';
}else if( $numSocios==0){
	echo 'No existe ese DNI en la Base de datos';
}else if($numSocios==1){

	$row=$resultado->fetch_assoc();
	//Comprobamos si está repetido en el prestamo

	$sqlDuplicado="SELECT * FROM `involucrados`
	where idCliente ='{$row['idCliente']}'
	and idPrestamo = '{$_POST['idPrestamo']}'; ";
	$resultadoDuplicado=$esclavo->query($sqlDuplicado);
	$cantDuplicado = $resultadoDuplicado->num_rows;	
	if($cantDuplicado==0){
		//libre
		echo "Libre: {$row['cliApellidoPaterno']} {$row['cliApellidoMaterno']} {$row['cliNombres']} //-//".$row['idCliente'];
	}else{
		echo 'El DNI ingreso ya se encuentra asociado a este préstamo';
	}
}



?>