<?php
require_once 'vendor/autoload.php';
include 'core/auth.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

try {
    $categorias = DB::query("SELECT categoria, COUNT(*) as cantidad FROM producto GROUP BY categoria");
    echo json_encode($categorias);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}