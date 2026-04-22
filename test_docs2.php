<?php
include 'vendor/autoload.php';
DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

$q = DB::query('SELECT d.*, u.nombre as usuario_nombre FROM documento d LEFT JOIN usuario u ON d.fk_user_id = u.id ORDER BY d.created DESC');
echo "Count: " . count($q) . "\n";
foreach ($q as $doc) {
    echo $doc['nombre'] . " - " . ($doc['usuario_nombre'] ?? 'NULL') . "\n";
}