<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST['type']){
    case 'open_config':
        $id = $_POST['id'];
        $rol->setRolId($id);
        $role = $rol->Info();
        $modulos = $rol->GetConfigRol();
        //dd($modulos);exit;
        $smarty->assign('info',$role);
        $smarty->assign('modulos',$modulos);
        $smarty->display(DOC_ROOT.'/templates/boxes/config-rol-popup.tpl');
    break;
    case 'save_config':
        $rol->setRolId($_POST['id']);
        if(!$rol->SaveConfigRol())
        {
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        else
        {
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }

        break;
}