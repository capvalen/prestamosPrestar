<?
require 'variablesGlobales.php';
include 'conkarl.php';
require_once('../vendor/autoload.php');
$base58 = new StephenHill\Base58();

$fechaHoy = new DateTime();
$deudaAHoy =0;
$filas=array();
$k=0;
$sumaSa=0;
$diasMora =0;
$precioCuota=0;
$seguro=0;
$onlyCapital =0;
$mora = 0;

$sql="SELECT idCuota, cuotFechaPago, cuotCuota, cuotPago, cuotSeg, presMontoDesembolso
FROM `prestamo_cuotas` pc 
inner join prestamo p on p.idPrestamo = pc.idPrestamo
where cuotFechaPago <=curdate() and cuotCuota<>0 and pc.idTipoPrestamo in (33, 79)
and pc.idPrestamo={$base58->decode($_POST['credito'])}
order by cuotFechaPago asc;";

$resultado=$cadena->query($sql);
while($row=$resultado->fetch_assoc()){
	$onlyCapital = $row['presMontoDesembolso'];
	$seguro = floatval($row['cuotSeg']);
	$precioCuota=floatval($row['cuotCuota']-$row['cuotPago']);
	$fechaCuota = new DateTime($row['cuotFechaPago']);
	$diasDebe=$fechaHoy ->diff($fechaCuota);
	$restaDias= floatval($diasDebe->format('%a'));

	$sumaSa+=floatval($precioCuota + $seguro );
//echo $restaDias."\n";
	if($restaDias>0){
		//sumar Dia y Mora
		if($k==0){
			$diasMora = $restaDias;
		}
		// array_push($filas, array(
		// 	cuotFechaPago=> $row['cuotFechaPago'],
		// 	cuotCuota=> floatval($row['cuotCuota']),
		// 	diasDebe=>$restaDias,
		// 	mora=>$mora
		// ));
		// $sumaSa+=(floatval($row['cuotCuota'])+$mora*$restaDias);
	}
	//else{
	//	$diasMora -= 1;
		//  sólo sumar día
		//$filas[$k]=
		// array_push($filas, array(
		// 	cuotFechaPago=> $row['cuotFechaPago'],
		// 	cuotCuota=> floatval($row['cuotCuota']),
		// 	diasDebe=>0,
		// 	mora=>0
		// ));
		// $sumaSa+=floatval($row['cuotCuota']);
	//}

	$k++;
}

switch(true):
	case (floatval($onlyCapital)<=299.99): $mora = 0; break;
	case (floatval($onlyCapital)<=1000) : $mora = 1; break;
	case (floatval($onlyCapital)<=2000) : $mora = 1.5; break;
	case (floatval($onlyCapital)<=3000) : $mora = 2; break;
	case (floatval($onlyCapital)>3000) : $mora = 3; break;
endswitch;
// echo "Total de días de mora: ". $diasMora;
// echo "Suma total: ".$sumaSa;
// echo "El cliente debe pagar para finalizar:".($sumaSa+ $diasMora*$mora );
//if($diasMora<>0){$diasMora-=1;}
$filas = array(
	'tantasCuotas'=> $k,
	'precioCuotas'=> round($precioCuota + $seguro,2),
	'diasMora' =>$diasMora,
	'deudaCuotas' => round($sumaSa,2),
	'precioMora' =>$diasMora*$mora,
	'seguro' => 0,
	'seg_nocuenta' => $seguro,
	'mora_neta' => $mora,
	'paraFinalizar' => round( ($precioCuota  + $seguro )*$k + $diasMora*$mora ,2), // round($sumaSa+ $diasMora*$mora ,2) //$seguro
	//146.3)*4
	'capital' => $onlyCapital
);

echo json_encode($filas);
?>