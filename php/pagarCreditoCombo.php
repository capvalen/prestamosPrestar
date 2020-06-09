<?php 
require 'variablesGlobales.php';
require("conkarl.php");
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();

$filas=array();

$k=0;
$diasMora=0; $moraTotal=0;
$dinero= floatval($_POST['dinero']);
$idPrestamo = $base58->decode($_POST['credito']);
$sql= "SELECT * FROM prestamo_cuotas
where  idPrestamo = {$idPrestamo} and idTipoPrestamo in (33, 79) and idTipoPrestamo <>43
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

if( $_POST['exonerar']=='true' && $diasMora>0 ): // -> Se exonera de mora
   /* HACER INSERT a CAJA por MORA por sólo lo que el cliente diga */
	$sqlMora="INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
	VALUES (null,{$idPrestamo},0,86,now(),0,'Se condonó {$diasMora} días por el periodo {$primFecha} y {$ultFecha}',1,1,{$_COOKIE['ckidUsuario']});";
	//echo $sqlMora;
	
	$resultadoMora=$esclavo->query($sqlMora);
else: // -> Se paga la mora
	if($diasMora>0){ //$diasMora-=1;
		$moraTotal = $diasMora*$mora;
		/* HACER INSERT a CAJA por MORA por X días*/
		$sqlMora="INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
		VALUES (null,{$idPrestamo},0,81,now(),{$moraTotal},'Mora por {$diasMora} días por el periodo {$primFecha} y {$ultFecha}',1,1,{$_COOKIE['ckidUsuario']});";
	//echo "mora pagada ".$moraTotal."\n";
	$dinero -= $moraTotal;
	$resultadoMora=$esclavo->query($sqlMora);
	}
endif;

$filas[] = array('sumaMora' => $moraTotal, 'diasMora' => $diasMora, 'queEs'=> 'Pago mora', 'montoCuota' => $moraTotal,  );

$sentenciaLarga ='';
while($row2=$resultado->fetch_assoc()){
	if( $row2['idTipoPrestamo']==33 ){ //Ya no cobramos seguro
		$debePendiente = floatval($row2['cuotCuota'] - $row2['cuotPago']);
	}else{ //cobramos el seguro
		$debePendiente = floatval( $row2['cuotCuota'] + $row2['cuotSeg'] -$row2['cuotPago'] ); //145+1.3
	}
	//|echo 'dinero '. $dinero . "\n";
	//echo 'pendiente '. $debePendiente . "\n";
	if($dinero >= $debePendiente){
		//echo 'Pago completo delinterés;
		if( $row2['idTipoPrestamo']==33 ){
			$soloCuota = floatval($row2['cuotCuota']-$row2['cuotPago']);
			
			$sentenciaLarga = $sentenciaLarga. "UPDATE `prestamo_cuotas` SET 
			`cuotFechaCancelacion`= now(),
			`cuotPago` = `cuotPago`+ {$debePendiente},
			`idTipoPrestamo` = 80
			WHERE `idCuota` = {$row2['idCuota']};
			INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
				VALUES (null,{$idPrestamo},{$row2['idCuota']},80,now(),{$soloCuota},'',1,1,{$_COOKIE['ckidUsuario']});";

		}else{
			$soloCuota = floatval($row2['cuotCuota'] + $row2['cuotSeg'] - $row2['cuotPago']);
			
			$sentenciaLarga = $sentenciaLarga. "UPDATE `prestamo_cuotas` SET 
			`cuotFechaCancelacion`= now(),
			`cuotPago` = `cuotPago`+ {$debePendiente},
			`idTipoPrestamo` = 80
			WHERE `idCuota` = {$row2['idCuota']};
			INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
				VALUES (null,{$idPrestamo},0,88,now(),{$row2['cuotSeg']},'',1,1,{$_COOKIE['ckidUsuario']});
			INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
				VALUES (null,{$idPrestamo},{$row2['idCuota']},80,now(),{$soloCuota},'',1,1,{$_COOKIE['ckidUsuario']});";
		}

		
			$filas[] = array('cuota' => $row2['idCuota'], 'montoCuota' => $debePendiente, 'queEs'=> 'Cuota cancelada' );
	}else{  //145+1.3-0 = 146.3 -100 = 46.3
		if( $dinero <= 0){
			break;
		}else{
			//echo 'Pagar un pedazo en id: '.$row2['idCuota']." solo adelanto ".$dinero."\n";
			//$dinero-=$row2['cuotSeg'];

			$sentenciaLarga = $sentenciaLarga. "UPDATE `prestamo_cuotas` SET 
			`cuotFechaCancelacion`= now(),
			`cuotPago` = `cuotPago`+ {$dinero},
			`idTipoPrestamo` = 33
			WHERE `idCuota` = {$row2['idCuota']};
			INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
			VALUES (null,{$idPrestamo},{$row2['idCuota']},33,now(),{$dinero},'',1,1,{$_COOKIE['ckidUsuario']});
			INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`)
				VALUES (null,{$idPrestamo},0,88,now(),{$row2['cuotSeg']},'',1,1,{$_COOKIE['ckidUsuario']});";
			$filas[] = array('cuota' => $row2['idCuota'], 'montoCuota' => $dinero, 'queEs'=> 'Adelanto cuota' );

		} 
	}
	$dinero= floatval(round($dinero - $debePendiente ,2)); 
}





//------------------

//echo $sentenciaLarga;
$respuLargo=$prisionero->multi_query($sentenciaLarga);


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

if($numLineas==0){
	$sqlUpdFin="UPDATE `prestamo` SET 
	`presActivo`=2, `fechaFinPrestamo`=now()
	where `idPrestamo`={$idPrestamo};";
	$cadena->query($sqlUpdFin);
}

if($respuLargo){
	//echo true;
	echo json_encode($filas);
}

?>