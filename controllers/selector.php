<?php
echo isset($_GET['id']);

if (isset($_GET['id'])) {

    $report = $_GET['id'];

    switch ($report) {

        case 1:
            require_once "../generador/1foroeauto.php";
            break;
        case 2:
            require_once "../generador/Foro1ercolavorativo.php";
            break;
        case 3:
            require_once "../generador/Foro2erauto.php";
            break;
        case 4:
            require_once "../generador/Foro2ercolavorativo.php";
            break;
        case 5:
            require_once "../generador/1ProductoAutoAprendizajeProgramaExcel.php";
            break;
        case 6:
            require_once "../generador/2ProductoAutoAprendizajeProgramaExcel.php";
            break;
        case 7:
            require_once "../generador/1HeteroEvaluacionProgramaExcel.php";
            break;
        case 8:
            require_once "../generador/2HeteroEvaluacionProgramaExcel.php";
            break;
        case 9:
            require_once "../generador/coeDetalleProgramaExcel.php";
            break;
        case 12:
            require_once "../generador/ForoInformativoProgramaExcel.php";
            break;
        default:
            header("location: /reportphp/error.php");
            break;
    }
}
