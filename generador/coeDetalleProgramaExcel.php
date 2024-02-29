
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

$sql = mysqli_query($conexionBD, "select ccp.name as programa, cc.name AS sem, c.fullname AS cur,ma.name, FROM_UNIXTIME(ma.duedate) AS fechaentrega, case WHEN (FROM_UNIXTIME(ma.duedate) < CURRENT_DATE()) THEN 'Tarea vencida' else 'Tarea activa' end AS estado, countEnRolCourse(ma.course, 5) as participantes, (countEnRolCourse(ma.course, 5)-countSubmissionCourse(ma.course, ma.name))  as noenviado, countSubmissionCourse(ma.course , ma.name) as enviados, countNotNoteCourse(ma.course, ma.name) as porcalificar,  countNotePassCourse(ma.course, ma.name) as aprobados, countNoteNotPassCourse(ma.course, ma.name) as noaprobados, avgNoteCourse(ma.course, ma.name) as promedio,urlActivity(ma.course, ma.name) as url, nombresDocenteCurso(ma.course) as docente from mdl_course_categories as ccp inner join mdl_course_categories as cc on ccp.id = cc.parent inner join mdl_course as c ON cc.id = c.category AND cc.parent in(" . $idPrograma . ") AND c.summary <> 'TIF' inner join mdl_assign ma on c.id = ma.course and ma.name like '%Coevaluación Virtual%' and c.visible =1 ORDER BY cc.name, c.id asc;");

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


$sheet->setCellValue('A1', "Semestre");
$sheet->setCellValue('B1', "Curso");
$sheet->setCellValue('C1', "Fecha Entrega");
$sheet->setCellValue('D1', "Estado");
$sheet->setCellValue('E1', "Participantes");
$sheet->setCellValue('F1', "Tarea no enviadas");
$sheet->setCellValue('G1', "Tarea enviadas");
$sheet->setCellValue('H1', "Por calificar");
$sheet->setCellValue('I1', "Aprobados");
$sheet->setCellValue('J1', "No aprobados");
$sheet->setCellValue('K1', "Promedio");
$sheet->setCellValue('L1', "Docente");

// Llenar la hoja de calculo con los datos 
$row = 2; // Comenzar desde la fila 2
foreach ($resultados as $datafila) {

            $sheet->setCellValue('A'.$row , $datafila["sem"]);
            $sheet->setCellValue('B'.$row , $datafila["cur"]);
            $sheet->setCellValue('C'.$row , $datafila["fechaentrega"]);
            $sheet->setCellValue('D'.$row , $datafila["estado"]);
            $sheet->setCellValue('E'.$row , $datafila["participantes"]);
            $sheet->setCellValue('F'.$row , $datafila["noenviado"]);            
            $sheet->setCellValue('G'.$row , $datafila["enviados"]);
            $sheet->setCellValue('H'.$row , $datafila["porcalificar"]);
            $sheet->setCellValue('I'.$row , $datafila["aprobados"]);
            $sheet->setCellValue('J'.$row , $datafila["noaprobados"]);
            $sheet->setCellValue('K'.$row , $datafila["promedio"]);
            $sheet->setCellValue('L'.$row , $datafila["docente"]);
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
$filename = 'coevaluacion.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// se guarda la hoja de calculo
$writer = new Xlsx($spreadsheet);
ob_end_clean();
$writer->save('php://output');




