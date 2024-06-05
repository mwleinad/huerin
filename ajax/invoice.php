<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries33.php');
$data = json_decode(file_get_contents('php://input'), true);
$_POST = $data;
switch ($_POST['type']) {
    case 1:
        $id = $_POST['id'];
        $dataInvoice = [];
        $dataCurrentInvoice =  $invoiceService->getInfoInvoice($id);
        $dataInvoice =  $invoiceService->sustituirFactura($dataCurrentInvoice);
        if (!$dataInvoice['res']) {
            $jsondata['result'] = 0;
            $jsondata['message'] = $smarty->fetch(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            $jsondata['uuid'] = $dataInvoice["uuid"];
        } else  {
            $dataResult = $dataInvoice['result'];
            $jsondata['result'] = 1;
            $jsondata['message'] = $smarty->fetch(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            $timbreFiscal = unserialize($dataResult['timbreFiscal']);
            $jsondata['uuid'] = $timbreFiscal["UUID"];
        }
        echo json_encode($jsondata);
    break;
    case 2:
        $empresa->setComprobanteId($_POST['id']);
        $empresa->validarPagosAplicados($_POST['id']);
        $empresa->setMotivoCancelacionSat($_POST['clave_sat']);
        if(in_array($_POST['clave_sat'], ['01']))
            $empresa->setUuidSustitucion($_POST['uuid_sustitucion']);
        $empresa->setMotivoCancelacion($_POST['motivo']);


        if(!$empresa->CancelarComprobante()){
            $jsondata['result'] =  0;
            $jsondata['message'] = $smarty->fetch(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }else{

            $jsondata['result'] =  1;
            $comprobantes = [];
            $comprobante->setPage(0);
            $values["comprobante"] = 0;
            $values["responsableCuenta"] = 0;
            $comprobantes = $comprobante->SearchComprobantesByRfc($values);
            $total = 0;

            if($comprobantes["items"])
            {
                foreach($comprobantes["items"] as $res){
                    if(in_array($res['tiposComprobanteId'], [1,2,3,4]))
                    {
                        $total += $res['total'];
                        $subtotal += $res['subTotal'];
                        $iva += $res['ivaTotal'];
                        $isr += $res['isrRet'];
                    }
                }
            }

            $total = number_format($total,2,'.',',');
            $subtotal = number_format($subtotal,2,'.',',');
            $iva = number_format($iva,2,'.',',');
            $isr = number_format($isr,2,'.',',');

            $smarty->assign('comprobantes',$comprobantes);
            $smarty->assign('totalFacturas',$totalFacturas);
            $smarty->assign('total',$total);
            $smarty->assign('subtotal',$subtotal);
            $smarty->assign('iva',$iva);
            $smarty->assign('isr',$isr);
            $jsondata['message'] = $smarty->fetch(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            $jsondata['resumen'] = $smarty->fetch(DOC_ROOT.'/templates/boxes/resumen-facturas.tpl');
            $jsondata['lista_invoice']   = $smarty->fetch(DOC_ROOT.'/templates/lists/facturas.tpl');
        }
        echo json_encode($jsondata);
    break;
}
