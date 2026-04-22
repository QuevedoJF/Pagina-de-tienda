<?php
require_once '../vendor/autoload.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $contrasena = $_POST['contrasena'];
    $confirmar = $_POST['confirmar'];

    if (empty($nombre) || empty($contrasena)) {
        header("Location: ../components/login.html?error=2");
        exit();
    }

    if ($contrasena !== $confirmar) {
        header("Location: ../components/login.html?error=3");
        exit();
    }

    if (strlen($contrasena) < 6) {
        header("Location: ../components/login.html?error=4");
        exit();
    }

    $existe = DB::queryFirstRow("SELECT id FROM usuario WHERE nombre = %s", $nombre);
    if ($existe) {
        header("Location: ../components/login.html?error=5");
        exit();
    }

    DB::insert('usuario', [
        'nombre' => $nombre,
        'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT)
    ]);

    header("Location: ../components/login.html?registered=1");
    exit();
}

header("Location: ../components/login.html");
?>