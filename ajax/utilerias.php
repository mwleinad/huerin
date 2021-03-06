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
}
