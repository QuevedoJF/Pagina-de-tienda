<?php
require_once 'vendor/autoload.php';
DB::$host = 'localhost'; 
DB::$user = 'root'; 
DB::$password = '1234'; 
DB::$dbName = 'tienda'; 
DB::$encoding = 'utf8';

$id = $_POST['id'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$ap_paterno = $_POST['ap_paterno'] ?? '';
$ap_materno = $_POST['ap_materno'] ?? '';
$rfc = $_POST['rfc'] ?? '';

try {
    if ($id != '') {
        DB::update('cliente', [
            'nombre' => $nombre,
            'apellido_paterno' => $ap_paterno,
            'apellido_materno' => $ap_materno,
            'rfc' => $rfc
        ], "id=%i", $id); 
        
        echo "Cliente actualizado correctamente";
    } else {
        DB::insert('cliente', [
            'nombre' => $nombre,
            'apellido_paterno' => $ap_paterno,
            'apellido_materno' => $ap_materno,
            'rfc' => $rfc
        ]);
        
        echo "Cliente registrado correctamente";
    }
} catch(Exception $e) {
    http_response_code(500);
    echo "Error en la base de datos: " . $e->getMessage();
}
?>