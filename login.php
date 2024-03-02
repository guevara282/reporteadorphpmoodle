<?php

require_once "./config/conexion.php";

// Verificar si está definido el parámetro "id" en la URL
if (isset($_GET["id"])) {
    // Obtener el valor del parámetro "id"
    $id = $_GET["id"];

    // Consulta preparada para obtener rpu_id y rpu_user de la tabla report_user
    $query = "SELECT rpu_id, rpu_user FROM report_user WHERE rpu_estado = 1";
    $stmt = $conexionBD->prepare($query);

    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt) {
        // Ejecutar la consulta preparada
        $stmt->execute();

        // Vincular variables para almacenar los resultados
        $stmt->bind_result($rpu_id, $rpu_user);

        // Inicializar un array para almacenar los resultados
        $resultados = array();

        // Recorrer los resultados
        while ($stmt->fetch()) {
            $resultados[] = array('rpu_id' => $rpu_id, 'rpu_user' => $rpu_user);
        }
        // print_r($resultados);
        // Verificar si el id proporcionado está en la lista de resultados
        $idEncontrado = false;
        foreach ($resultados as $resultado) {

            if ($resultado['rpu_id'] == $id) {
                $idEncontrado = true;
                break;
            }
        }

        // Mostrar el resultado
        if ($idEncontrado) {
            //require_once "./controllers/session_start.php";
            $_SESSION['iduser'] = $id;
          session_start();
            header("location: /reportphp/");
        } else {
            header("location: /reportphp/logout.php");
        }

        // Cerrar la consulta preparada
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $conexionBD->error;
    }

    // Cerrar la conexión a la base de datos
    $conexionBD->close();
} else {
    echo "El parámetro 'id' no está definido en la URL.";
}
