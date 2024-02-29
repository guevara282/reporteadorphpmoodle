<?php

require '../vendor/autoload.php';
require '../tool/tools.php';
require '../tool/DataBase.php';
require '../tool/toolsExcel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
// hola Mundo

try{
    

    $spreadsheet = new Spreadsheet();    
    loadStyleFont($spreadsheet);




    $sheet = $spreadsheet->getActiveSheet();

    $conn = new DataBase();
    $nameProgram = "";
    $dataParam = json_decode($_GET["params"], false);
    $dataReport = getDataReport($conn, intval($_GET["idr"]));
    

   

    $sqlProgram = "SELECT id, name FROM `mdl_course_categories` WHERE id = ?";
    $stmtProgram = $conn->Query( $sqlProgram, "i", array(intval($dataParam[0]->value)));
   
    
    $resultProgram = $stmtProgram->get_result();
    if ($resultProgram->num_rows > 0) {
       if($data = $resultProgram->fetch_assoc()){
           $nameProgram = $data["name"];
       }
    }
   
    $stmtProgram->close();

    
   

    $rowTitle = 5;

    $sheet->setCellValue('B2', "Programa:");
    $sheet->setCellValue('C2', $nameProgram);
    $sheet->mergeCells("C2:D2");

    $sheet->setCellValue('B3', "Actividad:");
    $sheet->setCellValue('C3', $dataReport["nombre_corto"]);
    $sheet->mergeCells("C3:D3");

    columnAutoSize($sheet , 'B');
    columnAutoSize($sheet , 'C');
    columnAutoSize($sheet , 'D');
    columnAutoSize($sheet , 'E');
    columnAutoSize($sheet , 'F');
    columnAutoSize($sheet , 'G');
    columnAutoSize($sheet , 'H');
    columnAutoSize($sheet , 'I');
    columnAutoSize($sheet , 'J');
    columnAutoSize($sheet , 'K');
    columnAutoSize($sheet , 'L');

    $sheet->setCellValue('B'.$rowTitle, "Semestre");
    $sheet->setCellValue('C'.$rowTitle, "Curso");
    $sheet->setCellValue('D'.$rowTitle, "Fecha Entrega");
    $sheet->setCellValue('E'.$rowTitle, "Estado");
    $sheet->setCellValue('F'.$rowTitle, "Participantes");
    $sheet->setCellValue('G'.$rowTitle, "Tarea no enviadas");
    $sheet->setCellValue('H'.$rowTitle, "Tarea enviadas");
    $sheet->setCellValue('I'.$rowTitle, "Por calificar");
    $sheet->setCellValue('J'.$rowTitle, "Aprobados");
    $sheet->setCellValue('K'.$rowTitle, "No aprobados");
    $sheet->setCellValue('L'.$rowTitle, "Promedio");

    $sqlExcel = $dataReport["text_sql"];

   
    $stmtExcel = $conn->Query( $sqlExcel, "i", array(intval($dataParam[0]->value)));
    $resultExcel = $stmtExcel->get_result();
    if ($resultExcel->num_rows > 0) {
       $row = $rowTitle + 1;
       while($data = $resultExcel->fetch_assoc()){
            $sheet->setCellValue('B'.$row , $data["sem"]);
            $sheet->setCellValue('C'.$row , $data["cur"]);
            $sheet->setCellValue('D'.$row , $data["fechaentrega"]);
            $sheet->setCellValue('E'.$row , $data["estado"]);
            $sheet->setCellValue('F'.$row , $data["participantes"]);
            $sheet->setCellValue('G'.$row , $data["noenviado"]);            
            $sheet->setCellValue('H'.$row , $data["enviados"]);
            $sheet->setCellValue('I'.$row , $data["porcalificar"]);
            $sheet->setCellValue('J'.$row , $data["aprobados"]);
            $sheet->setCellValue('K'.$row , $data["noaprobados"]);
            $sheet->setCellValue('L'.$row , $data["promedio"]);
            $row++;
       }
    }
    $stmtExcel->close();

    $sheet->getStyle('B2:C2')->getFont()->setBold(true);
    $sheet->getStyle('B3:C3')->getFont()->setBold(true);
    $sheet->getStyle('B5:L5')->getFont()->setBold(true);
    $sheet->getStyle('B5:L5')->getAlignment()->applyFromArray( [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' =>  \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]);

    loadNameSheet($sheet, $dataReport["nombre_corto"]);
    $dataUser = getDataUser($conn, intval($_POST["idu"]));
    $hashReport = getHashReport($_POST["idu"], $_POST["idr"]);

    loadMetaData($spreadsheet, array(
        "name" => $dataUser["usuario"],
        "title" => $dataReport["nombre_corto"], 
        "subject" => $dataReport["nombre_corto"] . " " . $nameProgram,
        "descripcion" => $dataReport["descripcion"] . " " . $nameProgram . " Creado por " . $dataUser["nombre"],
        "key" => $hashReport
    ));

    $nameReport = getNameFile($hashReport, "2daInterAprendizaje" . $nameProgram, "xlsx");
    $writer = new Xlsx($spreadsheet);
    $writer->save('../stores/'.$nameReport);
    $response = array('status' => 'ok', 'name' => $nameReport, 'hash' => $hashReport);
    echo json_encode($response);
    return;
}catch(Exception $e){
    echo $e->getMessage();
}
$response = array('status' => 'error');
echo json_encode($response);
