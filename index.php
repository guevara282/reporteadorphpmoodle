<?php
if (session_status() == PHP_SESSION_NONE) {
    // Inicia la sesión si no está iniciada
    session_start();
}

if (!isset($_SESSION['iduser'])) {
    // Redirigir a la página de inicio de sesión
    header("location: /reportphp/logout.php");
    exit();
}

require_once "./config/conexion.php";
require_once "./config/app.php";
require_once "./autoload.php";

?>
<!DOCTYPE html>
<html lang="es">

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