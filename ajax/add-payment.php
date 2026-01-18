<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 12/02/2018
 * Time: 04:34 PM
 */
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries33.php');

session_start();
switch($_POST["type"])
{
     case 'saveAddPayment':
         if($cxc->AddPayment($_POST["comprobanteId"], $_POST["metodoDePago"], $_POST["amount"],$_POST['deposito'],$_POST["paymentDate"],$_POST['efectivo'],$_POST['generarComprobantePago'], $_POST['tipoDeMoneda'], $_POST['tipoCambio'], isset($_POST['confirmAmount']) ? $_POST['confirmAmount'] : null))
         {
             echo "ok[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
         }
         else{
             echo "fail[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
         }
     break;
    case 'saveAddPaymentFromXml':
        if($cxc->AddPaymentFromXml($_POST["file_xml"], $_POST["metodoDePago"], $_POST["amount"],$_POST['deposito'],$_POST["paymentDate"],$_POST['efectivo'],$_POST['generarComprobantePago']))
        {
            //get data current xml
            $fact =  $comprobante->getDataByXml($_POST['file_xml']);
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
        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;



}