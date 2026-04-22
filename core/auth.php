<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $depth = substr_count(dirname($scriptName), '/');
    $redirectPath = str_repeat('../', $depth) . 'components/login.html';
    header("Location: $redirectPath");
    exit();
}
?>