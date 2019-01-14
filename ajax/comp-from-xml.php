<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();
switch($_POST["type"])
{
     case 'openAddPaymentFromXml':
          //encontrar datos de la factura
         $cad = $comprobante->getDataByXml($_POST['name_xml']);
         $payments_xml = $comprobante->getPaymentsFromXml($cad);
         $smarty->assign('factura',$cad);
         $smarty->assign('payments',$payments_xml);
         $smarty->assign('fecha',date('d-m-Y'));
         $smarty->assign('title',"Agregar pago usando xml");
         $smarty->display(DOC_ROOT.'/templates/boxes/add-payment-from-xml-popup.tpl');

     break;
    case 'searchFacturaFromXml':
        $facturas = $comprobante->searchFacturasFromXml($_POST);
        echo "ok[#]";
        $smarty->assign('facturas',$facturas);
        $smarty->display(DOC_ROOT.'/templates/lists/comp-from-xml.tpl');

    break;
    case "deletePaymentFromXml":
        $payment_from_xml = $cxc->PaymentInfoFromXml($_POST["payment_id"]);
        $cxc->DeletePaymentFromXml($_POST["payment_id"]);
        //get data current xml
        $fact =  $comprobante->getDataByXml($payment_from_xml['name_xml']);
        $payments_xml = $comprobante->getPaymentsFromXml($fact);
        //buscar
        $filtro =  json_decode($_POST['frmFiltro'],true);
        $facturas =  $comprobante->searchFacturasFromXml($filtro);

        echo "ok[#]";
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $smarty->assign('facturas',$facturas);
        $smarty->display(DOC_ROOT.'/templates/lists/comp-from-xml.tpl');
        echo "[#]";
        $smarty->assign('payments',$payments_xml);
        $smarty->display(DOC_ROOT.'/templates/lists/payments-from-xml.tpl');
        echo "[#]";
        echo number_format($fact['saldo'],2,".",",");
     break;



}