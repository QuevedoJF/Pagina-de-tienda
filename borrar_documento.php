<?php
require_once 'vendor/autoload.php';
include 'auth.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $doc = DB::queryFirstRow("SELECT ruta FROM documento WHERE id = %d", $id);

    if ($doc) {
        $rutaArchivo = $doc['ruta'];
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
        DB::delete('documento', 'id = %d', $id);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Documento no encontrado']);
    }
}
?>