<?php
date_default_timezone_set('America/Lima');
/* Change to the correct path if you copy this example! */
require '../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
/**
 * Assuming your printer is available at LPT1,
 * simpy instantiate a WindowsPrintConnector to it.
 *
 * When troubleshooting, make sure you can send it
 * data from the command-line first:
 *  echo "Hello World" > LPT1
 */
 
    //$connector = new WindowsPrintConnector("smb://192.168.1.131/TM-U220");
$connectorV31 = new WindowsPrintConnector("smb://127.0.0.1/POS58");
try {
    $cierreVirtual= $_POST['apertura']+$_POST['efectivoEntrada']-$_POST['efectivoSalida']-$_POST['tarjetaSalida'];
    $sobra=$cierreVirtual-$_POST['cierre'];
    if($sobra==0){ $resto = 'Exacto';}
    else if($sobra>0){$resto = 'Falta ';}
    else if($sobra<0){$resto = 'Sobra ';}
    // A FilePrintConnector will also work, but on non-Windows systems, writes
    // to an actual file called 'LPT1' rather than giving a useful error.
    // $connector = new FilePrintConnector("LPT1");
    /* Print a "Hello world" receipt" */
    $printer = new Printer($connectorV31);
    $printer -> text("         Prestar Huancayo\n");
    $printer -> setEmphasis(true);    
    $printer -> text("             Cierre de caja \n");
    $printer -> text("   ----------------------------------\n");
    $printer -> text(date("d/m/Y, g:i a")."\n");
    $printer -> setEmphasis(false);
    $printer -> text("Apertura: S/ ".number_format($_POST['apertura'], 2)."\n");
    $printer -> text("   ------  Entradas  ----\n");
    $printer -> text("Efectivo: S/ ".number_format($_POST['efectivoEntrada'], 2)."\n");
    $printer -> text("Tarjetas: S/ ".number_format($_POST['tarjetaEntrada'], 2)."\n");
    $printer -> text("Depositos bancarios: S/ ".number_format($_POST['bancos'], 2)."\n");
    $printer -> text("   ------  Salidas  ----\n");
    $printer -> text("Efectivo: S/ (".number_format($_POST['efectivoSalida'], 2).")\n");
    $printer -> text("Tarjetas: S/ (".number_format($_POST['tarjetaSalida'], 2).")\n");
    $printer -> text("   ----------------------------------\n");
    $printer -> text("Cierre manual: S/ ".number_format($_POST['cierre'], 2)."\n");
    $printer -> text("TOTAL: S/ ".number_format($cierreVirtual, 2)."\n");
    $printer -> text($resto." S/ ".number_format($sobra,2)."\n");
    $printer -> text("Usuario: ".$_POST['usuario']."\n");
    $printer -> cut();
    /* Close printer */
    $printer -> close();
} catch (Exception $e) {
    echo "No se pudo imprimir en la impresora: " . $e -> getMessage() . "\n";
}