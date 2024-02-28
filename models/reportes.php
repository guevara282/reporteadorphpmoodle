<?php

if ($_SESSION["idprograma"]!=0) {
    $CONDI = true;
}else{
    $CONDI = false;
}

if ($CONDI == TRUE) {
    // URL de la API
    $apiUrl = 'http://localhost/reportphp/controllers/programas.php/?iduser=' . $_SESSION["iduser"] . '&idprogra=' . $_SESSION["idprograma"];

    //echo $_SESSION["iduser"];
    //echo $_SESSION["idprograma"];

    // Inicializar cURL
    $ch = curl_init($apiUrl);

    // Configurar opciones de cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Realizar la solicitud a la API
    $response = curl_exec($ch);

    // Verificar si la solicitud fue exitosa
    if ($response === false) {
        die('Error al obtener la respuesta de la API: ' . curl_error($ch));
    }

    // Cerrar la conexión cURL
    curl_close($ch);

    // Decodificar la respuesta JSON
    $data = json_decode($response, true);

    //mostrar respuesta
    //echo $response;

    // Verificar si la decodificación fue exitosa
    if ($data === null) {
        die('Error al decodificar la respuesta JSON');
    }
    echo '<table class="table table-hover table-responsive">';
    echo '<thead><tr><th>Reportes</th></tr></thead>';
    echo '<tbody>';
    foreach ($data as $item) {
        echo '<tr><td class="fs-2">' . $item['NCORTO'] . '</td></tr>';
        // Reemplaza 'nombre_del_campo' con el nombre real de los campos en tu respuesta JSON
    }
    echo '</tbody>';
    echo '</table>';
} elseif ($CONDI == FALSE) {

    echo "Seleccione un programa";
}
