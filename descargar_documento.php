<?php
require_once 'vendor/autoload.php';
include 'auth.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

$mimeTypes = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'txt' => 'text/plain',
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed'
];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $doc = DB::queryFirstRow("SELECT nombre, ruta, tipo FROM documento WHERE id = %d", $id);

    if ($doc && file_exists($doc['ruta'])) {
        DB::query("UPDATE documento SET descargas = descargas + 1 WHERE id = %d", $id);
        $ext = strtolower($doc['tipo']);
        $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $doc['nombre'] . '"');
        readfile($doc['ruta']);
        exit();
    }
}
header("Location: documentos.php");
?>