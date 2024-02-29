<?php

require '../vendor/autoload.php';
require '../tool/tools.php';
require '../tool/DataBase.php';


use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\TablePosition;

try{
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

    setlocale(LC_TIME, 'es_ES.UTF-8');
    $fechaDoc = date('l jS \of F Y');
    $fechaHoraDoc = date('l jS \of F Y h:i:s A');
    $languageES = new Language(Language::ES_ES);

    $phpWord = new PhpWord();
    $phpWord->getSettings()->setThemeFontLang($languageES);

    $phpWord->addFontStyle('style_base', array('bold'=>false, 'italic'=>false, 'size'=>12));
    $phpWord->addFontStyle('style_base_table', array('bold'=>false, 'italic'=>false, 'size'=>10));
    $phpWord->addFontStyle('style_base_table_bold', array('bold'=>true, 'italic'=>false, 'size'=>10));
    $phpWord->addFontStyle('style_base_bold', array('bold'=>true, 'italic'=>false, 'size'=>12));
    $phpWord->addFontStyle('style_base_bold_cap', array('bold'=>true, 'italic'=>false, 'size'=>12, 'allCaps' => true));
    $phpWord->addFontStyle('style_base_bold_min', array('bold'=>true, 'italic'=>false, 'size'=>10.5));
    $phpWord->addParagraphStyle('style_justify', array('align'=>'both', 'spaceAfter'=>100));

    $section = $phpWord->addSection();
    $section->addText("Florencia, " . $fechaDoc, 'style_base');
    $section->addTextBreak(4);
    $section->addText('Profesional:', 'style_base');
    $section->addText($dataParam[1]->value, 'style_base_bold_cap');
    $section->addText('Coordinador Programa', 'style_base');
    $section->addText($nameProgram, 'style_base');
    $section->addText('Universidad de la Amazonia', 'style_base');
    $section->addTextBreak(3);
    $textrun = $section->addTextRun();
    $textrun->addText('Asunto: ', 'style_base_bold');
    $textrun->addText('Informe primera Autoevaluación Virtual ' . $dataParam[2]->value . '.', 'style_base');
    $section->addTextBreak(2);
    $section->addText('El proceso de estudio de la modalidad a distancia comprende una serie de actividades y tareas que, desarrolladas bajo el modelo pedagógico permite que docentes y estudiantes lleven a cabo actividades de enseñanza y aprendizaje enfocadas en la investigación pedagógica, en el auto e interaprendizaje y en la comunicación – interacción  como estrategias de formación.', 'style_base', 'style_justify');
    $section->addTextBreak();
    $section->addText('En el presente informe se dará a conocer los resultados de la primera Autoevaluación Virtual, donde se podrá evidenciar los intentos realizados por cada uno de los estudiantes.', 'style_base', 'style_justify');
    $section->addTextBreak();
    $section->addText('A continuación se listan los mencionados:', 'style_base');
    $section->addTextBreak(2);
    $section->addText($nameProgram, 'style_base_bold_cap');
    $wcell = 1550;
    $fancyTableStyleName = 'Fancy Table';
    $fancyTableStyle = array('width' => 5000, 'unit' => 'pct', 'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_AUTO, 'borderSize' => 1, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
    $fancyTableFirstRowStyle = array();
    $fancyTableCellStyle =  array('valign' => 'center', 'bgColor' => '000000', 'color' => '111111');
    $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
    $table = $section->addTable($fancyTableStyleName);
    $table->addRow(700);
    $table->addCell($wcell , $fancyTableCellStyle)->addText('Semestre', 'style_base_bold_min');
    $table->addCell($wcell , $fancyTableCellStyle)->addText('Curso', 'style_base_bold_min');
    $table->addCell($wcell , $fancyTableCellStyle)->addText('Matriculados', 'style_base_bold_min');
    $table->addCell($wcell , $fancyTableCellStyle)->addText('Intentos', 'style_base_bold_min');
    $table->addCell($wcell , $fancyTableCellStyle)->addText('Aprobadas', 'style_base_bold_min');
    $table->addCell($wcell , $fancyTableCellStyle)->addText('Reprobadas', 'style_base_bold_min');

    $sqlExcel = $dataReport["text_sql"];
    $stmtExcel = $conn->Query( $sqlExcel, "i", array(intval($dataParam[0]->value)));
    $resultExcel = $stmtExcel->get_result();
    if ($resultExcel->num_rows > 0) {
       $row = $rowTitle + 1;
       while($data = $resultExcel->fetch_assoc()){
            $table->addRow(500, array("exactHeight" => false));
            $table->addCell($wcell)->addText($data["name"], 'style_base_table');
            $table->addCell($wcell)->addText($data["fullname"], 'style_base_table');
            $table->addCell($wcell)->addText($data["matriculados"], 'style_base_table_bold');
            $table->addCell($wcell)->addText($data["totales"], 'style_base_table'); 
            $table->addCell($wcell)->addText($data["aprobados"], 'style_base_table');
            $table->addCell($wcell)->addText($data["noaprobados"], 'style_base_table'); 
        
       }
    }
    $stmtExcel->close();

    $section->addTextBreak(4);
    $section->addText("Este informe fue generado con fecha de corte a " . $fechaHoraDoc , 'style_base');
    $section->addTextBreak(2);
    $section->addText('Atentamente,', 'style_base');
    $section->addTextBreak(4);

    $tableSige = $section->addTable(array('width' => 5000, 'unit' => 'pct', 'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_AUTO, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER));
    $tableSige->addRow();
    $cell1 = $tableSige->addCell(2500); 
    $cell1->addText("DIEGO ALEXANDER ESPINOSA CARREÑO", 'style_base_bold');
    $cell1->addText("Coordinador de Medios y Recursos Tecnológicos", 'style_base');
    $cell1->addText("Departamento de Educación a Distancia Universidad de la Amazonia", 'style_base');

    $cell2 = $tableSige->addCell(2500);
    $cell2->addText("YHULLY ABILDARYS MARÍN CUELLAR", 'style_base_bold');
    $cell2->addText("Administradora de Plataforma Virtual", 'style_base');
    $cell2->addText("Departamento de Educación a Distancia Universidad de la Amazonia", 'style_base');

    $dataUser = getDataUser($conn, intval($_POST["idu"]));
    $hashReport = getHashReport($_POST["idu"], $_POST["idr"]);


    $nameReport = getNameFile($hashReport, "1ra_autoevaluacion_" . $nameProgram, "docx");


    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('../stores/'.$nameReport);

    $response = array('status' => 'ok', 'name' => $nameReport, 'hash' => $hashReport);
    echo json_encode($response);
    return;
}catch(Exception $e){
    echo $e->getMessage();
}
$response = array('status' => 'error');
echo json_encode($response);
