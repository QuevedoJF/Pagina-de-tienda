<?php
date_default_timezone_set('America/Mexico_City');
require_once 'vendor/autoload.php';
DB::$host = 'localhost'; DB::$user = 'root'; DB::$password = '1234'; DB::$dbName = 'tienda'; DB::$encoding = 'utf8';

$clientes = DB::query("SELECT * FROM cliente ORDER BY id ASC");

$pdf = new TCPDF();
$pdf->SetCreator('Sistema de Gestión');
$pdf->SetAuthor('Sistema de Gestión');
$pdf->SetTitle('Reporte de Clientes');

$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Reporte de Clientes', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 8, 'Fecha de descarga: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(10, 8, 'ID', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Ap. Paterno', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Ap. Materno', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'RFC', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Fec. Registro', 1, 1, 'C', true);

$pdf->SetFont('helvetica', '', 9);
foreach ($clientes as $c) {
    $pdf->Cell(10, 7, $c['id'], 1, 0, 'C');
    $pdf->Cell(35, 7, $c['nombre'], 1, 0, 'C');
    $pdf->Cell(35, 7, $c['apellido_paterno'], 1, 0, 'C');
    $pdf->Cell(35, 7, $c['apellido_materno'], 1, 0, 'C');
    $pdf->Cell(30, 7, $c['rfc'], 1, 0, 'C');
    $pdf->Cell(35, 7, isset($c['fecha_registro']) ? date('d/m/Y', strtotime($c['fecha_registro'])) : 'N/A', 1, 1, 'C');
}

$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 7, 'Total de clientes: ' . count($clientes), 0, 1, 'R');

$pdf->Output('reporte_clientes_' . date('d-M-Y') . '.pdf', 'D');