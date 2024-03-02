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
  
    echo '<table id="tablareport" class="table table-hover table-responsive display">';
    echo '<thead><tr><th>Reportes '.$data[0]['PROGRAMA'].'</th><th>Descargas</th></tr></thead>';
    echo '<tbody>';
 
    foreach ($data as $item) {
        echo '<tr>';
        echo '<td class="fs-2">' . $item['NCORTO'] . '</td>';
        echo '<td><a href="./controllers/selector.php?id=' . $item['IDTREPORTE'] . '" class="btn btn-primary">Descargar</a></td>';
        echo '</tr>';
       
    }
    
    echo '</tbody>';
    echo '</table>';
     
} elseif ($CONDI == FALSE) {

    echo "<h1>Seleccione un programa</h1>";
}
