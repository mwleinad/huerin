<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();

switch($_POST["type"]){
    case 'cancel_invoice_active_in_sat':
        $utileriaInvoice->findInvoicesActiveInSat();
        if($utileriaInvoice->handleCancelationInvoiceActiveInSat()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;
    case 'find_invoice_active_in_sat':
        $utileriaInvoice->findInvoicesActiveInSat();
        $utileriaInvoice->countInvoicesActiveInSat();
        echo "ok[#]";
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
    break;
    case 'resend_cancel_from_pending':
        $utileriaInvoice->resendCancelFromPending();
        if($utileriaInvoice->handleCancelationInvoiceActiveInSat()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    case 'openFormCheckStatusInvoice':
        $smarty->display(DOC_ROOT.'/templates/boxes/check-status-invoice-popup.tpl');
    break;
    case 'checkStatusInvoiceInSat':
        switch($_POST["accion"]){
            case 'check':
                $utileriaInvoice->checkStatusInSat($_POST["serie"],$_POST["folio"]);
            break;
            case 'cancel':
                $utileriaInvoice->cancelInvoiceInSatByFolio($_POST["serie"],$_POST["folio"]);
            break;
        }
         echo "ok[#]";
         $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
    break;
    case 'openGetSalario':
        $smarty->assign('empleados', $personal->EnumerateAll());
        $smarty->display(DOC_ROOT.'/templates/boxes/get-salario-popup.tpl');
        break;
    case 'getSalario':
        $subs = [];
        if(isset($_POST['deep'])) {
            $personal->setPersonalId($_POST['personalId']);
            $subs = $personal->Subordinados();
        }
        $subs = count($subs)>0 ? array_column($subs, 'personalId') : [];
        array_push($subs, $_POST['personalId']);
        $total = $personal->getTotalSalarioByMultipleId($subs);
        $total = number_format($total, 2, '.', "," );
        $util->setError(0, 'complete', "salario total mensual  = $total ");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        break;
    case 'open_cancel_cfdi_from_csv':
        $data['title'] ="Cancelar CFDI´S con archivo CSV";
        $data['form'] ="frm-cancel-cfdi-from-csv";
        $smarty->assign("data", $data);
        $smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');
    break;
    case 'cancel_cfdi_from_csv':
        echo $utileriaInvoice->cancelCfdiFromCsv() ? "ok[#]" : "fail[#]";
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        $nameFile = $utileriaInvoice->getNameFile();
        $acuse_exist = is_file(DOC_ROOT."/sendFiles/". $nameFile) ?  1: 0;
        echo "[#]";
        echo $acuse_exist;
        echo "[#]";
        echo WEB_ROOT."/download.php?file=/sendFiles/".$nameFile;
    break;
    case 'open_cancel_cfdi_from_excel':
        $data['title'] = "Cancelación masiva de comprobantes con archivo Excel";
        $data['form'] = "frm-cancel-cfdi-from-excel";
        $smarty->assign("data", $data);
        $smarty->display(DOC_ROOT.'/templates/boxes/cancel-cfdi-excel-popup.tpl');
    break;
    case 'uploadExcel':
        include_once(DOC_ROOT.'/classes/cancelacion_masiva.class.php');
        $cancelacion = new CancelacionMasiva();
        
        if (!isset($_FILES['archivo_excel']) || $_FILES['archivo_excel']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(array(
                'success' => false,
                'error' => 'No se ha seleccionado un archivo válido'
            ));
            break;
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
            break;
        }
        
        $resultado = $cancelacion->procesarArchivoExcel($archivoTemporal, $_SESSION['empresaId']);
        
        if ($resultado['success']) {
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
            
            $comprobantesActivos = array_filter($validados, function($item) {
                return $item['datos'] && $item['datos']['status'] === 'activo';
            });
            
            echo json_encode(array(
                'success' => true,
                'total_encontrados' => count($resultado['resultados']),
                'total_validados' => count($validados),
                'total_activos' => count($comprobantesActivos),
                'total_errores' => count($errores),
                'validados' => $validados,
                'errores' => $errores
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'error' => $resultado['error']
            ));
        }
    break;
    case 'solicitarCancelacionExcel':
        include_once(DOC_ROOT.'/classes/cancelacion_masiva.class.php');
        $cancelacion = new CancelacionMasiva();
        
        // Limpiar y validar la cadena JSON
        $jsonString = trim($_POST['comprobantes_ids']);
        // Remover comillas dobles adicionales si existen
        $jsonString = trim($jsonString, '"');
       
        $comprobantesIds = json_decode($jsonString, true);
  
        // Si falla el json_decode, verificar si es un error de formato
        if ($comprobantesIds === null && json_last_error() !== JSON_ERROR_NONE) {
            $util->setError(10094, 'error', 'Error en formato de datos: ' . json_last_error_msg());
            $util->PrintErrors();
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            break;
        }
        
        
        if (!is_array($comprobantesIds) || empty($comprobantesIds)) {
            $util->setError(10093, 'error', 'No se han seleccionado comprobantes para cancelar');
            $util->PrintErrors();
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            break;
        }
        
        $motivo = $_POST['motivo'] ?: '02';
        
        $resultado = $cancelacion->solicitarCancelacion(
            $comprobantesIds, 
            $motivo
        );
        
        if ($resultado['success']) {
            $mensaje = "Cancelación procesada exitosamente\n";
            $mensaje .= "Total procesados: " . $resultado['total_procesados'] . "\n";
            $mensaje .= "Cancelaciones exitosas: " . $resultado['total_cancelados'];
            
            if (!empty($resultado['errores'])) {
                $mensaje .= "\n\nErrores encontrados:\n";
                foreach ($resultado['errores'] as $error) {
                    if (is_array($error)) {
                        $mensaje .= "• Comprobante ID " . $error['comprobanteId'] . 
                                   " (UUID: " . $error['uuid'] . "): " . $error['mensaje'] . "\n";
                    } else {
                        $mensaje .= "• " . $error . "\n";
                    }
                }
            }
            
            if (!empty($resultado['cancelados'])) {
                $mensaje .= "\n\nComprobantes cancelados exitosamente:\n";
                foreach ($resultado['cancelados'] as $cancelado) {
                    if (is_array($cancelado)) {
                        $mensaje .= "✓ Comprobante ID " . $cancelado['comprobanteId'] . 
                                   " (UUID: " . $cancelado['uuid'] . "): " . $cancelado['mensaje'] . "\n";
                    } else {
                        $mensaje .= "✓ Comprobante ID: " . $cancelado . "\n";
                    }
                }
            }
            
            $util->setError(0, 'complete', $mensaje);
            $util->PrintErrors();
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        } else {
            $util->setError(10094, 'error', $resultado['error']);
            $util->PrintErrors();
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;
}
