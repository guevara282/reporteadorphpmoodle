

<?php
//metodo para generar reporte
//para generar el reporte es necesario requeir el archivo autoload.php
require '../vendor/autoload.php';
//require_once("../config/conexion.php");
//se deben de usar las librerias Spreadsheet y Xlsx
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
// se verifica lo que llega desde el boton crear
session_start();
$idPrograma = $_SESSION["idprograma"];

echo $idPrograma;
$servidor = "172.16.31.125";
$usuario = "di";
$contrasenia = "4Dm1n321";
$nombreBaseDatos = "di";
$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);

$sql = mysqli_query($conexionBD, "SELECT  cc.name AS sem, c.fullname AS cur,  fr.name AS forum,  IFNULL(fd.name, '') AS discu,  IFNULL(FROM_UNIXTIME(fp.created), '') AS fechacreated, IFNULL(CONCAT_WS(' ', us.firstname,us.lastname), '') AS creador, (SELECT COUNT(*) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id) AS replicas, (SELECT COUNT(*) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id AND fpx.userid = fd.userid) AS replicasdoc, (SELECT COUNT(*) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id  AND fpx.userid <> fd.userid) AS replicasest, IFNULL((SELECT FROM_UNIXTIME(MAX(fpx.created)) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id  AND fpx.userid = fd.userid), '') AS datelastdoc, IFNULL((SELECT FROM_UNIXTIME(MAX(fpx.created)) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id  AND fpx.userid <> fd.userid), '') AS datelastest FROM mdl_course_categories AS cc  INNER JOIN  mdl_course AS  c ON cc.id = c.category AND cc.parent =" . $idPrograma . " AND c.visible = 1 INNER JOIN  mdl_forum AS fr ON c.id = fr.course  LEFT JOIN mdl_forum_discussions AS fd  ON fr.id = fd.forum  LEFT JOIN mdl_forum_posts AS fp ON fd.firstpost = fp.id LEFT JOIN mdl_user AS us ON fd.userid = us.id where fr.name like '%Foro de discusiones 1er. Mto Aprendizaje Colaborativo%' ORDER BY cc.name, c.id asc;");

if (!$sql) {
    die('Error en la consulta SQL: ' . mysqli_error($conexionBD));
}
$resultados = array();
while ($fila = mysqli_fetch_assoc($sql)) {
    $resultados[] = $fila;
}
// se crea un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// se agrega encabezados a la hoja de calculo
$sheet->setCellValue('A1', 'Semestre');
$sheet->setCellValue('B1', 'Curso');
$sheet->setCellValue('C1', 'Foro');
$sheet->setCellValue('D1', 'Discusión');
$sheet->setCellValue('E1', 'Creador');
$sheet->setCellValue('F1', 'Fecha Creacion');
$sheet->setCellValue('G1', 'Replicas');
$sheet->setCellValue('H1', 'Replicas Docente');
$sheet->setCellValue('I1', 'Replicas Estudiante');
$sheet->setCellValue('J1', 'Ultima Replica Docente');
$sheet->setCellValue('k1', 'Ultima Replica Estudiante');

// Llenar la hoja de calculo con los datos 
$row = 2; // Comenzar desde la fila 2
foreach ($resultados as $datafila) {

    $sheet->setCellValue('A' . $row, $datafila["sem"]);
    $sheet->setCellValue('B' . $row, $datafila["cur"]);
    $sheet->setCellValue('C' . $row, $datafila["forum"]);
    $sheet->setCellValue('D' . $row, $datafila["discu"]);
    $sheet->setCellValue('E' . $row, $datafila["creador"]);
    $sheet->setCellValue('F' . $row, $datafila["fechacreated"]);
    $sheet->setCellValue('G' . $row, $datafila["replicas"]);
    $sheet->setCellValue('H' . $row, $datafila["replicasdoc"]);
    $sheet->setCellValue('I' . $row, $datafila["replicasest"]);
    $sheet->setCellValue('J' . $row, $datafila["datelastdoc"]);
    $sheet->setCellValue('K' . $row, $datafila["datelastest"]);
    $row++;
}

$sheet->getStyle('A1:l1')->getFont()->setBold(true);
// Establecer el tipo de relleno
$style = $sheet->getStyle('A1:L1');
$style->getFill()->setFillType(Fill::FILL_SOLID);
// Cambiar el color de fondo a amarillo, puedes ajustar el color según tus necesidades
$style->getFill()->getStartColor()->setARGB('C6E0B4');
$sheet->getStyle('A1:L1')->getAlignment()->applyFromArray(['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' =>  \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]);
foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

mysqli_close($conexionBD);
// se configura la respuesta HTTP para descargar el archivo Excel
$filename = 'Foro_1er_colaborativo.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// se guarda la hoja de calculo
$writer = new Xlsx($spreadsheet);
ob_end_clean();
$writer->save('php://output');
