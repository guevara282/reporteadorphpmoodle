<?php
$servidor = "localhost";
$usuario = "root";
$contrasenia = "";
$nombreBaseDatos = "report";
$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);
if ($conexionBD->connect_error) {
    die("Error de conexión a la base de datos: " . $mysqli->connect_error);
}