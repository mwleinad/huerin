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
    case 'openFormCheckStatusInvoice':
        $smarty->display(DOC_ROOT.'/templates/boxes/check-status-invoice-popup.tpl');
    break;
    case 'checkStatusInvoiceInSat':
         $utileriaInvoice->checkStatusInSat($_POST["serie"],$_POST["folio"]);
         echo "ok[#]";
         $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
    break;
}