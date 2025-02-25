<?php
require('../vendor/autoload.php'); // Asegúrate de incluir la ruta correcta a fpdf.php

// Crear una instancia de FPDF
$pdf = new FPDF('P', 'mm', array(58, 200)); // Tamaño personalizado para un ticket
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 8);

//Quitando margenes
$pdf->SetMargins(0, 0, 0); // Izquierda, arriba, derecha
$pdf->SetAutoPageBreak(false); // Desactivar el salto de página automático

// Agregar contenido al PDF
$pdf->Image('logo.jpg', 3, 0, 50);
$pdf->SetXY(0,20);

$pdf->Cell(0, 0, 'Ticket de Compra',0, 1, 'C');
$pdf->Ln(4); // Salto de línea
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(40, 0, 'Producto: Camiseta');
$pdf->Ln(4);
$pdf->Cell(40, 0, 'Precio: $20.00');
$pdf->Ln(4);
$pdf->Cell(40, 0, 'Fecha: ' . date('Y-m-d'));

// Salida del PDF al navegador
$pdf->Output('I', 'ticket.pdf');
?>