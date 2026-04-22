<?php
require_once 'vendor/autoload.php';
include 'auth.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $doc = DB::queryFirstRow("SELECT * FROM documento WHERE id = %d", $id);
    if ($doc) {
        echo json_encode($doc);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Documento no encontrado']);
    }
}
?>