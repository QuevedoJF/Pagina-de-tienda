<?php
require_once 'vendor/autoload.php';
DB::$host = 'localhost'; DB::$user = 'root'; DB::$password = '1234'; DB::$dbName = 'tienda';
$cols = DB::query("SHOW COLUMNS FROM cliente");
foreach($cols as $c) {
    echo $c['Field'] . ' | ' . $c['Type'] . PHP_EOL;
}
?>