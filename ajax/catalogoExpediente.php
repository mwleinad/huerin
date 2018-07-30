<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 30/07/2018
 * Time: 08:22 AM
 */

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST['type']){
    case 'addExpediente':
        $smarty->display(DOC_ROOT."/templates/boxes/expediente-popup.tpl");
    break;
    case 'editExpediente':
        $expediente->setExpedienteId($_POST['id']);
        $info = $expediente->Info();
        $smarty->assign('info',$info);
        $smarty->display(DOC_ROOT."/templates/boxes/expediente-popup.tpl");
    break;
    case 'deleteExpediente':
        $expediente->setExpedienteId($_POST['id']);
        if($expediente->Delete()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
            echo "[#]";
            $expedientes =  $expediente->Enumerate();
            $smarty->assign('expedientes',$expedientes);
            $smarty->display(DOC_ROOT.'/templates/lists/expediente.tpl');
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
        }
    break;
    case  'saveExpediente':
        $expediente->setName(trim($_POST['nombre']));
        if($expediente->Save()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
            echo "[#]";
            $expedientes =  $expediente->Enumerate();
            $smarty->assign('expedientes',$expedientes);
            $smarty->display(DOC_ROOT.'/templates/lists/expediente.tpl');
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
        }
    break;
    case  'updateExpediente':
        $expediente->setExpedienteId($_POST['id']);
        $expediente->setName(trim($_POST['nombre']));
        if($expediente->Update()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
            echo "[#]";
            $expedientes =  $expediente->Enumerate();
            $smarty->assign('expedientes',$expedientes);
            $smarty->display(DOC_ROOT.'/templates/lists/expediente.tpl');
        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
        }
    break;
}