<?php

// URL de la API
$apiUrl = 'http://localhost/reportphp/controllers/programas.php/?id=' . $_SESSION["iduser"];

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

// Verificar si la decodificación fue exitosa
if ($data === null) {
    die('Error al decodificar la respuesta JSON');
}
$primerElemento = $data[0];
// Accede a la propiedad específica (en este caso, 'PROGRAMA') del primer elemento
$primerDato = $primerElemento['IDPROGRAMA'];
//$_SESSION["idprograma"] = $primerDato;

echo '<table class="table table-hover tabla-programas">';
echo '<thead><tr><th>Programa</th></tr></thead>';
echo '<tbody>';
foreach ($data as $item) {
    
    $IdProgra = $item['IDPROGRAMA'];
    echo '<tr data-id="' . $IdProgra . '">';
   
    echo '<td class=fs-5>' . $item['PROGRAMA'] . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
