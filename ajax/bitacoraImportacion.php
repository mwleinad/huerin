<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();
switch($_POST["type"]) {
    case 1:
       $bitacora->setPage($_POST['page']);
       $data = $bitacora->enumerate();
       $smarty->assign('data', $data);
       $smarty->display(DOC_ROOT.'/templates/lists/bitacora-importacion.tpl');
    break;
    case 2:
        $data['title'] = 'Enviar recotización a clientes';
        $data['form'] =  'frm-correo-recotizacion';
        $departamentos = $personal->ListDepartamentos();

        $smarty->assign("departamentos", $departamentos);
        $smarty->assign('data', $data);
        $smarty->assign('post', $_POST);
        $smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');
        break;
    case 3:
        
        if($bitacora->enviarRecotizacion($_POST['id'], $_POST["mes_inicio"] ?? 'enero', $_POST["departamento"] ?? 'Cuentas por cobrar') ) {
            echo 'ok[#]';
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        } else {
            echo 'fail[#]';
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        
    break;
    case 4:
        $pdf = $bitacora->descargarBitacora($_POST['id']);
        echo WEB_ROOT."/download.php?file=".$pdf;
    break;
}