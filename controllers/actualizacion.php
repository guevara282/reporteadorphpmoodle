<?php
// Verifica si se ha enviado el IDPROGRAMA
if (isset($_POST['idPrograma'])) {
    $idPrograma = $_POST['idPrograma'];

    // Realiza las acciones necesarias con el IDPROGRAMA, por ejemplo, cambiar una variable de sesión
    session_start();
    $_SESSION['idprograma'] = $idPrograma;
     
    // Puedes enviar una respuesta al cliente si es necesario
    echo "IDPROGRAMAss seleccionado:". $_SESSION['idprograma'];
    header("location: /reportphp/");
    
} else {
    echo "Error: IDPROGRAMA no especificado en la solicitud.";
}
?>