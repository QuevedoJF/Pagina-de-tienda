<?php
require_once 'vendor/autoload.php';
DB::$host = 'localhost'; DB::$user = 'root'; DB::$password = '1234'; DB::$dbName = 'tienda'; DB::$encoding = 'utf8';

if (isset($_GET['id'])) {
    try {
        $cliente = DB::queryFirstRow("SELECT * FROM cliente WHERE id = %i", $_GET['id']);
        
        if ($cliente) {
            echo json_encode($cliente);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Cliente no encontrado"]);
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo $e->getMessage();
    }
}
?>