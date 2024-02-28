<?php 
//metodo para generar reporte
//para generar el reporte es necesario requeir el archivo autoload.php
require 'vendor/autoload.php';
require_once("./config/conexion.php");
//se deben de usar las librerias Spreadsheet y Xlsx
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// se verifica lo que llega desde el boton crear
if (isset($_POST['crear'])) {
     
    $datafilas = mysqli_query($conexion, "SELECT * FROM tu_tabla");

    // se crea un nuevo objeto Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // se agrega encabezados a la hoja de calculo
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Objeto');
    $sheet->setCellValue('C1', 'Actividad');
    $sheet->setCellValue('D1', 'Descripción');
    $sheet->setCellValue('E1', 'Moneda');
    $sheet->setCellValue('F1', 'Presupuesto');
    $sheet->setCellValue('G1', 'Fecha Inicio');
    $sheet->setCellValue('H1', 'Hora Inicio');
    $sheet->setCellValue('I1', 'Fecha Cierre');
    $sheet->setCellValue('J1', 'Estado');

    // Llenar la hoja de calculo con los datos 
    $row = 2; // Comenzar desde la fila 2
    foreach ($datafilas as $datafila) {
        $sheet->setCellValue('A' . $row, $datafila['id']);
        $sheet->setCellValue('B' . $row, $datafila['objeto']);
        $sheet->setCellValue('C' . $row, $datafila['actividad']);
        $sheet->setCellValue('D' . $row, $datafila['descripcion']);
        $sheet->setCellValue('E' . $row, $datafila['moneda_tipo']);
        $sheet->setCellValue('F' . $row, $datafila['presupuesto']);
        $sheet->setCellValue('G' . $row, $datafila['fechainicio']);
        $sheet->setCellValue('H' . $row, $datafila['horainicio']);
        $sheet->setCellValue('I' . $row, $datafila['fechacierre']);
        $sheet->setCellValue('J' . $row, $datafila['estado_tipo']);
        $row++;
    }

    // se configura la respuesta HTTP para descargar el archivo Excel
    $filename = 'reporte_datafilas.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // se guarda la hoja de calculo
    $writer = new Xlsx($spreadsheet);
    ob_end_clean();
    $writer->save('php://output');
  
}
?>