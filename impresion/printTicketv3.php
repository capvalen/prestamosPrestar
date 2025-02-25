<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Solo se aceptan solicitudes POST.');

/* Change to the correct path if you copy this example! */
require '../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage; //librería de imagen

/**
 * Assuming your printer is available at LPT1,
 * simpy instantiate a WindowsPrintConnector to it.
 *
 * When troubleshooting, make sure you can send it
 * data from the command-line first:
 *  echo "Hello World" > LPT1
 */
 
    //$connector = new WindowsPrintConnector("smb://192.168.1.131/TM-U220");
$connectorV36 = new WindowsPrintConnector("smb://127.0.0.1/POS58");
try {
	if( isset($_POST['observacion'])){
		$obs =$_POST['observacion'];
	}else{
		$obs='';
	}

//	$tux = EscposImage::load("../images/empresaTicket.jpg", false);
    // A FilePrintConnector will also work, but on non-Windows systems, writes
    // to an actual file called 'LPT1' rather than giving a useful error.
    // $connector = new FilePrintConnector("LPT1");
    /* Print a "Hello world" receipt" */
		$printer = new Printer($connectorV36);
		
		$printer->setJustification(Printer::JUSTIFY_CENTER);
    $tux = EscposImage::load("logo.png", true);
		$printer -> bitImage($tux);

		$printer -> setEmphasis(true);
    $printer -> text("{$_POST['titulo']}\n");
		$printer -> setEmphasis(false);
    $printer -> text("{$_POST['direccion']}\n");
		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer -> text("Código: ".ucwords($_POST['codigo'])."\n");
		$printer -> text("Cliente: ".ucwords($_POST['cliente'])."\n");
		if($_POST['faltan']>0)
			$printer->text("Cuotas restantes: ".$_POST['faltan']."\n");
		$printer -> setEmphasis(true);
		$printer -> text("Monto: S/ {$_POST['monto']}\n");
				$printer -> setEmphasis(false);
		if($obs !=''){
			$printer -> text("Obs: {$obs}\n");
		}
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer -> text("-----------------------------\n");
		$printer -> text("{$_POST['fecha']}\n\n");
		$printer -> text("Usuario: {$_POST['usuario']}\n");
    //$printer -> text("Contacto:  933 667330\n");
		$printer -> text("Gracias por su preferencia\n\n\n");
		$printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer -> cut();
    /* Close printer */
    $printer -> close();
} catch (Exception $e) {
    echo "No se pudo imprimir en la impresora: " . $e -> getMessage() . "\n";
}
