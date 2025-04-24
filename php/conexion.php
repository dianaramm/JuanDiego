<?php
$servidor = "localhost";
$usuario_db = "root";
$contraseña_db = "diana";
$nombre_db = "juandiego";

$conexion = new mysqli($servidor, $usuario_db, $contraseña_db, $nombre_db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");


?>