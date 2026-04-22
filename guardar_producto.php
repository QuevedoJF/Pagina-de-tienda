<?php
require_once 'vendor/autoload.php';
DB::$host = 'localhost'; 
DB::$user = 'root'; 
DB::$password = '1234'; 
DB::$dbName = 'tienda'; 
DB::$encoding = 'utf8';

$id = $_POST['id'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$cantidad = $_POST['cantidad'] ?? 0;
$precio = $_POST['precio'] ?? 0;

try {
    if ($id != '') {
        DB::update('producto', [
            'nombre' => $nombre,
            'categoria' => $categoria,
            'cantidad' => $cantidad,
            'precio' => $precio
        ], "id=%i", $id); 
    } else {
        DB::insert('producto', [
            'nombre' => $nombre,
            'categoria' => $categoria,
            'cantidad' => $cantidad,
            'precio' => $precio
        ]);
    }
    echo "Guardado con éxito";
} catch(Exception $e) {
    http_response_code(500);
    echo "Error al guardar";
}
?>