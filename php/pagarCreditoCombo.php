<?php 
date_default_timezone_set('America/Lima');

require 'variablesGlobales.php';
require("conkarl.php");
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();

$filas=array();

$k=0;
$diasMora=0; $moraTotal=0;
$dinero= floatval($_POST['dinero']);
$idPrestamo = $base58->decode($_POST['credito']);
$sql= "SELECT pc.*, intSimple FROM prestamo_cuotas pc
inner join prestamo p on p.idPrestamo = pc.idPrestamo
where pc.idPrestamo = {$idPrestamo} and pc.idTipoPrestamo in (33, 79) and pc.idTipoPrestamo <>43
order by cuotFechaPago asc;"; //cuotFechaPago <=curdate() and
$resultado=$esclavo->query($sql);
$fechaHoy = new DateTime();

while($row=$resultado->fetch_assoc()){
	$fechaCuota = new DateTime($row['cuotFechaPago']);
	$diasDebe=$fechaCuota ->diff($fechaHoy);
	$restaDias= floatval($diasDebe->format('%R%a'));
	
	if($restaDias>0){
		//sumar Dia y Mora
		if($k==0){
			$diasMora = $restaDias;
			$primFecha = $fechaCuota->format('d/m/Y');
		}
	}
	$ultFecha = $fechaCuota->format('d/m/Y');
	$k++;
	
} //fin de while

$resultado->data_seek(0);
//echo $_POST['exonerar']=='true';

/* if( $_POST['exonerar']=='true' && $diasMora>0 ): // -> Se exonera de mora
  // HACER INSERT a CAJA por MORA por sólo lo que el cliente diga
	$sqlMora="INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
	VALUES (null,{$idPrestamo},0,86,now(),0,'Se condonó {$diasMora} días por el periodo {$primFecha} y {$ultFecha}',1,1,{$_COOKIE['ckidUsuario']});";
	//echo $sqlMora;
	
	$resultadoMora=$esclavo->query($sqlMora); */
if($diasMora>0): // -> Se paga la mora $diasMora-=1;
		$moraTotal = $diasMora*$mora;
		/* HACER INSERT a CAJA por MORA por X días*/
		if ( $_POST['cliMora']< $moraTotal ){ $extra=" de un total de S/ ".$moraTotal; }else{$extra='';}
		$sqlMora="INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
		VALUES (null,{$idPrestamo},0,81, CONVERT_TZ( NOW(),'US/Eastern','America/Lima' ) ,{$_POST['cliMora']},'Mora por el periodo {$primFecha} y {$ultFecha}{$extra}', {$_POST['moneda']},1,{$_COOKIE['ckidUsuario']});";
	//echo "mora pagada ".$moraTotal."\n";
	$resultadoMora=$esclavo->query($sqlMora);
endif;

$filas[] = array('sumaMora' => $_POST['cliMora'], 'diasMora' => $diasMora, 'queEs'=> 'Pago mora', 'montoCuota' => $_POST['cliMora']  );



$sentenciaLarga ='';
$dinero -=floatval($_POST['cliMora']);


while($row2=$resultado->fetch_assoc()){
	if( $row2['idTipoPrestamo']==33 ){ //Ya no cobramos seguro
		$debePendiente = floatval($row2['cuotCuota'] + $row2['cuotSeg'] - $row2['cuotPago']);
	}else{ //cobramos el seguro
		$debePendiente = floatval( $row2['cuotCuota'] + $row2['cuotSeg'] -$row2['cuotPago'] ); //
	}
	//echo 'dinero '. $dinero . "\n";
	//echo 'pendiente '. $debePendiente . "\n";
	
	if( floatval(round($dinero,1)) >= floatval(round($debePendiente,1)) ){
		//echo 'Pago completo delinterés;
		if( $row2['idTipoPrestamo']==33 ){
			$soloCuota = floatval($row2['cuotCuota'] + $row2['cuotSeg'] - $row2['cuotPago']);
			
			$sentenciaLarga = $sentenciaLarga. "UPDATE `prestamo_cuotas` SET 
			`cuotFechaCancelacion`= CONVERT_TZ( NOW(),'US/Eastern','America/Lima' ),
			`cuotPago` = `cuotPago`+ {$debePendiente},
			`idTipoPrestamo` = 80
			WHERE `idCuota` = {$row2['idCuota']};
			INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
				VALUES (null,{$idPrestamo},{$row2['idCuota']},80, CONVERT_TZ( NOW(),'US/Eastern','America/Lima' ) ,{$soloCuota},'', {$_POST['moneda']},1,{$_COOKIE['ckidUsuario']});";

		}else{
			$soloCuota = floatval($row2['cuotCuota'] + $row2['cuotSeg'] - $row2['cuotPago']);
			
			$sentenciaLarga = $sentenciaLarga. "UPDATE `prestamo_cuotas` SET 
			`cuotFechaCancelacion`= CONVERT_TZ( NOW(),'US/Eastern','America/Lima' ),
			`cuotPago` = `cuotPago`+ {$debePendiente},
			`idTipoPrestamo` = 80
			WHERE `idCuota` = {$row2['idCuota']};
			INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
				VALUES (null,{$idPrestamo},{$row2['idCuota']},80, CONVERT_TZ( NOW(),'US/Eastern','America/Lima' ) ,{$soloCuota},'', {$_POST['moneda']},1,{$_COOKIE['ckidUsuario']});";
		}
		
			$filas[] = array('cuota' => $row2['idCuota'], 'montoCuota' => $debePendiente, 'queEs'=> 'Cuota cancelada' );
	}else{  //
		if( $dinero <= 0){
			break;
		}else{
			//echo 'Pagar un pedazo en id: '.$row2['idCuota']." solo adelanto ".$dinero."\n";
			//$dinero-=$row2['cuotSeg'];

			$sentenciaLarga = $sentenciaLarga. "UPDATE `prestamo_cuotas` SET 
			`cuotFechaCancelacion`= CONVERT_TZ( NOW(),'US/Eastern','America/Lima' ),
			`cuotPago` = `cuotPago`+ {$dinero},
			`idTipoPrestamo` = 33
			WHERE `idCuota` = {$row2['idCuota']};
			INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
			VALUES (null,{$idPrestamo},{$row2['idCuota']},33, CONVERT_TZ( NOW(),'US/Eastern','America/Lima' ) ,{$dinero},'', {$_POST['moneda']},1,{$_COOKIE['ckidUsuario']});";

			$filas[] = array('cuota' => $row2['idCuota'], 'montoCuota' => $dinero, 'queEs'=> 'Adelanto cuota' );

		} 
	}
	$dinero = floatval(round($dinero - $debePendiente, 2)); 
}

 


//------------------

//echo $sentenciaLarga;

if( $prisionero->multi_query($sentenciaLarga) ){ //$prisionero-> next_result()
	//echo true;

	sleep(1);
	$sqlFaltan="SELECT retornarNumCuotasFaltanToFin({$idPrestamo}) as faltan";
	//echo $sqlFaltan;
	$resultadoFaltan=$preferido->query($sqlFaltan);
	$rowFaltan=$resultadoFaltan->fetch_assoc();
	//$datoFaltan = $row['faltan'];
	$filas[0]['faltan']= $rowFaltan['faltan']; //= array('faltan'=> );
	


	//------- Verificamos que el crédito ya está pagado
	$sqlComprobar= "SELECT idPrestamo FROM prestamo_cuotas
	where  idPrestamo = {$idPrestamo} and idTipoPrestamo in (33, 79) and idTipoPrestamo <>43;"; //cuotFechaPago <=curdate() and
	$resultadoComprobar=$esclavo->query($sqlComprobar);
	$numLineas=$resultadoComprobar->num_rows;
	//echo "faltan pagar " . $numLineas;

	if($numLineas==0){
		$sqlUpdFin="UPDATE `prestamo` SET 
		`presActivo`=2, `fechaFinPrestamo`= CONVERT_TZ( NOW(),'US/Eastern','America/Lima' )
		where `idPrestamo`={$idPrestamo};";
		$cadena->query($sqlUpdFin);
	}

	echo json_encode($filas);
}else{
	echo "error en pagar cuentas";
} 

?>