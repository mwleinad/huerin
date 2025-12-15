<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

$user->allow_access(8);

// Incluir la clase de cancelación masiva
include_once(DOC_ROOT.'/classes/cancelacion_masiva.class.php');

switch($_POST["type"]) {
    case "uploadExcel":
        $cancelacion = new CancelacionMasiva();
        
        if (!isset($_FILES['archivo_excel']) || $_FILES['archivo_excel']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(array(
                'success' => false,
                'error' => 'No se ha seleccionado un archivo válido'
            ));
            exit();
        }
        
        $archivoTemporal = $_FILES['archivo_excel']['tmp_name'];
        $nombreArchivo = $_FILES['archivo_excel']['name'];
        
        // Validar que sea un archivo Excel
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        if (!in_array($extension, ['xlsx', 'xls'])) {
            echo json_encode(array(
                'success' => false,
                'error' => 'Solo se permiten archivos Excel (.xlsx, .xls)'
            ));
            exit();
        }
        
        $resultado = $cancelacion->procesarArchivoExcel($archivoTemporal, $_SESSION['empresaId']);
        
        if ($resultado['success']) {
            // Guardar resultados en sesión para mostrarlos
            $_SESSION['resultados_cancelacion'] = $resultado['resultados'];
            
            $validados = array();
            $errores = array();
            
            foreach ($resultado['resultados'] as $item) {
                if ($item['valido']) {
                    $validados[] = $item;
                } else {
                    $errores[] = $item;
                }
            }
            
            echo json_encode(array(
                'success' => true,
                'total_encontrados' => count($resultado['resultados']),
                'total_validados' => count($validados),
                'total_errores' => count($errores),
                'validados' => $validados,
                'errores' => $errores
            ));
        } else {
            echo json_encode($resultado);
        }
        break;
        
    case "solicitarCancelacion":
        $cancelacion = new CancelacionMasiva();
        
        $comprobantesIds = json_decode($_POST['comprobantes_ids'], true);
        
        if (!is_array($comprobantesIds) || empty($comprobantesIds)) {
            echo json_encode(array(
                'success' => false,
                'error' => 'No se han seleccionado comprobantes para cancelar'
            ));
            exit();
        }
        
        $motivo = $_POST['motivo'] ?: '02'; // Motivo por defecto
        
        $resultado = $cancelacion->solicitarCancelacion(
            $comprobantesIds, 
            $_SESSION['empresaId'], 
            $_SESSION['User']['userId'], 
            $motivo
        );
        
        echo json_encode($resultado);
        break;
        
    default:
        echo json_encode(array(
            'success' => false,
            'error' => 'Tipo de operación no válida'
        ));
        break;
}
?>