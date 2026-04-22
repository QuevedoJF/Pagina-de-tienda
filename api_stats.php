<?php
require_once 'vendor/autoload.php';
include 'core/auth.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

try {
    $productos = DB::query("SELECT COUNT(*) as total FROM producto");
    $clientes = DB::query("SELECT COUNT(*) as total FROM cliente");
    $productosCantidad = DB::query("SELECT SUM(cantidad) as total FROM producto");
    $categorias = DB::query("SELECT categoria, SUM(cantidad) as cantidad FROM producto GROUP BY categoria");

    echo json_encode([
        'total_productos' => $productos[0]['total'],
        'total_clientes' => $clientes[0]['total'],
        'total_stock' => $productosCantidad[0]['total'] ?? 0,
        'categorias' => $categorias
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}