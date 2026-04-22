<?php
require_once 'vendor/autoload.php';
include 'core/auth.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

try {
    $clientes = DB::query("SELECT COUNT(*) as total FROM cliente");
    echo json_encode(['total' => $clientes[0]['total']]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}