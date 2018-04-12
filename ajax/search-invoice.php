<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch ($_POST['type']){
    case 'search':
         $filter['status_activo'] = $_POST['status'];
         $filter['anio'] = $_POST['year'];
         $filter['mes'] = $_POST['mes'];
         $filter['serie'] = $_POST['serie'];
         $filter['rfc'] = $_POST['rfc'];
         $filter['nombre'] = $_POST['name'];
         $filter['generateby'] = $_POST['generateby'];
         $filter['addComplemento'] = $_POST['addComplemento'];
         $comprobantes=array();
         $comprobantes = $comprobante->SearchComprobantesByRfc($filter);
         $smarty->assign("comprobantes", $comprobantes);
         $html =  $smarty->fetch(DOC_ROOT.'/templates/lists/report-invoice.tpl');
         //$html = str_replace('$','', $html);
         $html = str_replace(',','', $html);
         $excel->ConvertToExcel($html,'xlsx',false,'rep-facturas',true,500);
         echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/rep-facturas.xlsx";
    break;
}