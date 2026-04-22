<?php
session_start();
require_once '../vendor/autoload.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

$user = trim($_POST['user']);
$pass = $_POST['pass'];

if (empty($user) || empty($pass)) {
    header("Location: ../components/login.html?error=1");
    exit();
}

$usuario = DB::queryFirstRow("SELECT * FROM usuario WHERE nombre = %s", $user);

if ($usuario && password_verify($pass, $usuario['contrasena'])) {
    $_SESSION['usuario'] = $usuario['nombre'];
    $_SESSION['usuario_id'] = $usuario['id'];
    header("Location: ../index.php");
} else {
    header("Location: ../components/login.html?error=1");
}
exit();
?>