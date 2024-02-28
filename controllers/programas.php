<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conecta a la base de datos  con usuario, contraseña y nombre de la BD
$servidor = "localhost";
$usuario = "root";
$contrasenia = "";
$nombreBaseDatos = "report";
$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);


// Consulta datos y recepciona una clave para consultar dichos datos con dicha clave
if (isset($_GET["iduser"]) && isset($_GET["idprogra"])) {
    $sqlreport = mysqli_query($conexionBD, "SELECT report_permisos.RPP_ID AS IDPREPORTE,report_user.RPU_ID AS IDUSER, report_user.RPU_USER AS USUARIO, report_programa.RPPRO_ID AS IDPROGRAMA, report_programa.RPPRO_NOMBRE AS PROGRAMA,report_treporte.RPT_ID AS IDTREPORTE ,report_treporte.RPT_NOMBRE AS REPORTE, report_treporte.RPT_NCORTO AS NCORTO
    FROM report_permisos
    INNER JOIN report_user ON report_permisos.RPP_IDU = report_user.RPU_ID
    INNER JOIN report_programa ON report_permisos.RPP_IDP = report_programa.RPPRO_ID
    INNER JOIN report_treporte ON report_permisos.RPP_TREPORT = report_treporte.RPT_ID
    WHERE report_user.RPU_ESTADO=1 AND report_programa.RPPRO_EST=1 AND report_permisos.RPP_EST=1 AND report_treporte.RPT_EST=1 AND report_user.RPU_ID=" . $_GET["iduser"] . " AND report_programa.RPPRO_ID=" . $_GET["idprogra"] . " ORDER BY RPT_NCORTO ASC");

    if (mysqli_num_rows($sqlreport) > 0) {
        $empleados = mysqli_fetch_all($sqlreport, MYSQLI_ASSOC);
        echo json_encode($empleados);
        exit();
    } else {
        echo json_encode(["success" => 0]);
    }
}
//borrar pero se le debe de enviar una clave ( para borrado )
if (isset($_GET["borrar"])) {
    $sqlreport = mysqli_query($conexionBD, "DELETE FROM empleados WHERE id=" . $_GET["borrar"]);
    if ($sqlreport) {
        echo json_encode(["success" => 1]);
        exit();
    } else {
        echo json_encode(["success" => 0]);
    }
}
//Inserta un nuevo registro y recepciona en método post los datos de nombre y correo
if (isset($_GET["insertar"])) {
    $data = json_decode(file_get_contents("php://input"));
    $nombre = $data->nombre;
    $correo = $data->correo;
    if (($correo != "") && ($nombre != "")) {

        $sqlreport = mysqli_query($conexionBD, "INSERT INTO empleados(nombre,correo) VALUES('$nombre','$correo') ");
        echo json_encode(["success" => 1]);
    }
    exit();
}
// Actualiza datos pero recepciona datos de nombre, correo y una clave para realizar la actualización
if (isset($_GET["actualizar"])) {

    $data = json_decode(file_get_contents("php://input"));

    $id = (isset($data->id)) ? $data->id : $_GET["actualizar"];
    $nombre = $data->nombre;
    $correo = $data->correo;

    $sqlreport = mysqli_query($conexionBD, "UPDATE empleados SET nombre='$nombre',correo='$correo' WHERE id='$id'");
    echo json_encode(["success" => 1]);
    exit();
}
// Consulta todos los registros de la tabla empleados
if (isset($_GET["id"])) {
    $sqlreport = mysqli_query($conexionBD, "SELECT 
DISTINCT report_programa.RPPRO_ID AS IDPROGRAMA,
report_programa.RPPRO_NOMBRE AS PROGRAMA, report_programa.RPPRO_URL AS URL
FROM 
report_permisos
INNER JOIN 
report_user ON report_permisos.RPP_IDU = report_user.RPU_ID
INNER JOIN 
report_programa ON report_permisos.RPP_IDP = report_programa.RPPRO_ID
WHERE 
report_user.RPU_ESTADO = 1 
AND report_programa.RPPRO_EST = 1 
AND report_permisos.RPP_EST = 1
AND report_user.RPU_ID ="  . $_GET["id"] . " ORDER BY PROGRAMA ASC;");

if (mysqli_num_rows($sqlreport) > 0) {
    $empleaados = mysqli_fetch_all($sqlreport, MYSQLI_ASSOC);
    echo json_encode($empleaados);
} else {
    echo json_encode([["success" => 0]]);
}
}

