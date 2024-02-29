<?php

require '../vendor/autoload.php';
require '../tool/tools.php';
require '../tool/DataBase.php';
require '../tool/toolsExcel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

try{
    $reader = IOFactory::createReader("Xlsx");
    $spreadsheet = $reader->load("../template/template1.xlsx");
    loadMetaData($spreadsheet);
    loadStyleFont($spreadsheet);
    $sheet = $spreadsheet->getActiveSheet();


    $conn = new DataBase();
    $sql = "SELECT id, name FROM `mdl_course_categories` WHERE `parent` IN(?, ?)";
    $stmt = $conn->Query( $sql, "ii", array(4, 9) );
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = 15;
        while ($data = $result->fetch_assoc()){
            $sheet->setCellValue('A'.$row , $data["id"]);
            $sheet->setCellValue('B'.$row , $data["name"]);
             $row++;
        }
    }
    $stmt->close();

    //$sheet->setCellValue('A8', $_POST["name"]);

    loadNameSheet($sheet, "Matriculados");

    $writer = new Xlsx($spreadsheet);
    $hashReport = getHashReport($_POST["idu"], $_POST["idr"]);
    $nameReport = getNameFile($hashReport, "Matriculados", "xlsx");
    $writer->save('../stores/'.$nameReport);

    $response = array('status' => 'ok', 'name' => "$nameReport", 'hash' => "$hashReport");
    echo json_encode($response);
    return;
}catch(Exception $e){
    echo $e->getMessage();
}
$response = array('status' => 'error');
echo json_encode($response);
