<?php

require '../vendor/autoload.php';
require '../tool/tools.php';
require '../tool/DataBase.php';
require '../tool/toolsExcel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

try{
   $spreadsheet = new Spreadsheet();           
   $spreadsheet->addSheet( new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, '1'), 1 );
   $spreadsheet->addSheet( new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, '2'), 2 );
   $spreadsheet->addSheet( new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, '3'), 3 );

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


    /*  Quiz */
    
    /* Foro */
    $spreadsheet->setActiveSheetIndex(1);
    $sheetForo =  $spreadsheet->getActiveSheet();

    $rowTitle = 5;

    $sheetForo->setCellValue('B2', "Programa:");
    $sheetForo->setCellValue('C2', $nameProgram);
    $sheetForo->mergeCells("C2:D2");

    $sheetForo->setCellValue('B3', "Actividad:");
    $sheetForo->setCellValue('C3', " foro de discusiones 1er. Mto Aprendizaje Autónomo");
    $sheetForo->mergeCells("C3:D3");

    columnAutoSize($sheetForo , 'B');
    columnAutoSize($sheetForo , 'C');
    columnAutoSize($sheetForo , 'D');
    columnAutoSize($sheetForo , 'E');
    columnAutoSize($sheetForo , 'F');
    columnAutoSize($sheetForo , 'G');
    columnAutoSize($sheetForo , 'H');
    columnAutoSize($sheetForo , 'I');
    columnAutoSize($sheetForo , 'J');
    columnAutoSize($sheetForo , 'K');
    columnAutoSize($sheetForo , 'L');
    columnAutoSize($sheetForo , 'M');

    $sheetForo->setCellValue('B'.$rowTitle, "Semestre");
    $sheetForo->setCellValue('C'.$rowTitle, "Curso");
    $sheetForo->setCellValue('D'.$rowTitle, "Foro");
    $sheetForo->setCellValue('E'.$rowTitle, "Discusión");
    $sheetForo->setCellValue('F'.$rowTitle, "Creador");
    $sheetForo->setCellValue('G'.$rowTitle, "Fecha Creacion");
    $sheetForo->setCellValue('H'.$rowTitle, "Replicas");
    $sheetForo->setCellValue('I'.$rowTitle, "Replicas Docente");
    $sheetForo->setCellValue('J'.$rowTitle, "Replicas Estudiante");
    $sheetForo->setCellValue('K'.$rowTitle, "Ultima Replica Docente");
    $sheetForo->setCellValue('L'.$rowTitle, "Ultima Replica Estudiante");

    $sqlForo = "SELECT  cc.name AS sem, c.fullname AS cur,  fr.name AS forum,  IFNULL(fd.name, '') AS discu,  IFNULL(FROM_UNIXTIME(fp.created), '') AS fechacreated, IFNULL(CONCAT_WS(' ', us.firstname,us.lastname), '') AS creador, (SELECT COUNT(*) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id) AS replicas, (SELECT COUNT(*) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id AND fpx.userid = fd.userid) AS replicasdoc, (SELECT COUNT(*) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id  AND fpx.userid <> fd.userid) AS replicasest, IFNULL((SELECT FROM_UNIXTIME(MAX(fpx.created)) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id  AND fpx.userid = fd.userid), '') AS datelastdoc, IFNULL((SELECT FROM_UNIXTIME(MAX(fpx.created)) FROM mdl_forum_posts AS fpx WHERE fpx.discussion = fd.id  AND fpx.userid <> fd.userid), '') AS datelastest FROM mdl_course_categories AS cc  INNER JOIN  mdl_course AS  c ON cc.id = c.category AND cc.parent = ? AND c.visible = 1 INNER JOIN  mdl_forum AS fr ON c.id = fr.course  LEFT JOIN mdl_forum_discussions AS fd  ON fr.id = fd.forum  LEFT JOIN mdl_forum_posts AS fp ON fd.firstpost = fp.id LEFT JOIN mdl_user AS us ON fd.userid = us.id where fr.name like '%Foro de discusiones 1er. Mto Aprendizaje Autónomo%' ORDER BY cc.name, c.id asc;";
    $stmtExcel = $conn->Query( $sqlForo, "i", array(intval($dataParam[0]->value)));
    $resultExcel = $stmtExcel->get_result();
    if ($resultExcel->num_rows > 0) {
       $row = $rowTitle + 1;
       while($data = $resultExcel->fetch_assoc()){
            $sheetForo->setCellValue('B'.$row , $data["sem"]);
            $sheetForo->setCellValue('C'.$row , $data["cur"]);
            $sheetForo->setCellValue('D'.$row , $data["forum"]);        
            $sheetForo->setCellValue('E'.$row , $data["discu"]);
            $sheetForo->setCellValue('F'.$row , $data["creador"]);
            $sheetForo->setCellValue('G'.$row , $data["fechacreated"]);
            $sheetForo->setCellValue('H'.$row , $data["replicas"]);            
            $sheetForo->setCellValue('I'.$row , $data["replicasdoc"]);
            $sheetForo->setCellValue('J'.$row , $data["replicasest"]);
            $sheetForo->setCellValue('K'.$row , $data["datelastdoc"]);
            $sheetForo->setCellValue('L'.$row , $data["datelastest"]);
            $row++;
       }
    }
    $stmtExcel->close();

    $sheetForo->getStyle('B2:C2')->getFont()->setBold(true);
    $sheetForo->getStyle('B3:C3')->getFont()->setBold(true);
    $sheetForo->getStyle('B5:L5')->getFont()->setBold(true);
    $sheetForo->getStyle('B5:L5')->getAlignment()->applyFromArray( [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' =>  \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]);

    loadNameSheet($sheetForo, "Foro 1er Autono");

    /* TIF */
   

    $dataUser = getDataUser($conn, intval($_POST["idu"]));
    $hashReport = getHashReport($_POST["idu"], $_POST["idr"]);

    loadMetaData($spreadsheet, array(
        "name" => $dataUser["usuario"],
        "title" => $dataReport["nombre_corto"], 
        "subject" => $dataReport["nombre_corto"] . " " . $nameProgram,
        "descripcion" => $dataReport["descripcion"] . " " . $nameProgram . " Creado por " . $dataUser["nombre"],
        "key" => $hashReport
    ));

    $nameReport = getNameFile($hashReport, "Foro_1er_autónomo" . $nameProgram, "xlsx");
    $writer = new Xlsx($spreadsheet);
    $writer->save('../stores/'.$nameReport);
    $response = array('status' => 'ok', 'name' => $nameReport, 'hash' => $hashReport);
    echo json_encode($response);
    return;
}catch(Exception $e){
   file_put_contents('logs.log', $e->getMessage(), FILE_APPEND);
    echo $e->getMessage();
}
$response = array('status' => 'error');
echo json_encode($response);
