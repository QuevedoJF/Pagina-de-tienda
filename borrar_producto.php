<?php
require_once 'vendor/autoload.php';
DB::$host = 'localhost'; DB::$user = 'root'; DB::$password = '1234'; DB::$dbName = 'tienda'; DB::$encoding = 'utf8';

if (isset($_POST['id'])) {
    try {
        DB::delete('producto', "id=%i", $_POST['id']);
        echo "ok";
    } catch(Exception $e) {
        http_response_code(500);
        echo $e->getMessage(); 
    }
} else {
    http_response_code(400);
    echo "No se envió ningún ID";
}
?>