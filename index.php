<?php
session_start();

if (!isset($_SESSION)) {
    // Redirigir a la página de inicio de sesión
    echo'no encontrada';
    exit();
}
require_once "./config/conexion.php";
require_once "./config/app.php";
require_once "./autoload.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "./views/include/head.php"; ?>
</head>

<body>
    <?php require_once "./views/include/header.php"; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 " id="sidebar">
                <?php require_once "./models/carreras.php"; ?>
            </div>
            <div class="col-md-10 main-content">
                <?php require_once "./models/reportes.php"; ?>
            </div>
        </div>
    </div>
    <?php require_once "./views/include/script.php"; ?>
</body>

</html>