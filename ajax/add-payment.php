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
         if($cxc->AddPayment($_POST["comprobanteId"], $_POST["metodoDePago"], $_POST["amount"],$_POST['deposito'],$_POST["paymentDate"],$_POST['efectivo'],$_POST['generarComprobantePago']))
         {
             echo "ok[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
         }
         else{
             echo "fail[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
         }
     break;



}