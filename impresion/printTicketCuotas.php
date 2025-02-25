<?php

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
 
    $connector4 = new WindowsPrintConnector("smb://127.0.0.1/POS58");
try {

    $printer = new Printer($connector4);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $tux = EscposImage::load("logo.png", true);
    $printer -> bitImage($tux);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer -> setEmphasis(true);
    $printer -> text("* Pagos *\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer -> setEmphasis(false);
    $printer -> text("{$_POST['direccion']}\n");
    $printer -> text("Código: CR-".$_POST['codPrest']."\n");
    $printer -> text("Cliente: ".ucwords($_POST['cliente'])."\n");
    $printer -> text("Pagos realizados: \n");
    $printer -> text(""."{$_POST['queMichiEs']} \n");
    $printer -> text("-------------------------------\n");
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text("".$_POST['hora']."\n");
    $printer -> text("Usuario: ".ucwords($_POST['usuario'])."\n");
    //$printer -> text("Contacto: {$_POST['ckcelularEmpresa']} \n");
    $printer -> text("Gracias por su preferencia\n\n\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);

    $printer -> cut();
    /* Close printer */
    $printer -> close();
} catch (Exception $e) {
    echo "No se pudo imprimir en la impresora: " . $e -> getMessage() . "\n";
}