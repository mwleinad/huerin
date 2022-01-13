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
        $smarty->assign('data', $data);
        $smarty->assign('post', $_POST);
        $smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');
        break;
    case 3:
        $bitacora->enviarRecotizacion($_POST['id']);
        echo 'ok[#]';
        $smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');

    break;
    case 4:
        $pdf = $bitacora->descargarBitacora($_POST['id']);
        echo WEB_ROOT."/download.php?file=".$pdf;
    break;
}