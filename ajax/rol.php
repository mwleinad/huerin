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
        $roles = $rol->Enumerate();
        //dd($modulos);exit;
        $smarty->assign('roles',$roles);
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
    case 'copyPermiso':
        $id = $_POST['id'];
        $baseId = $_POST['baseId'];
        $rol->setRolId($id);
        $role = $rol->Info();
        $rol->setRolId($baseId);
        $modulos = $rol->GetConfigRol();

        $roles = $rol->Enumerate();
        //dd($modulos);exit;
        $smarty->assign('roles',$roles);
        $smarty->assign('info',$role);
        $smarty->assign('modulos',$modulos);
        $smarty->display(DOC_ROOT.'/templates/forms/config-rol.tpl');
        break;
    case 'addRol':
        $smarty->assign('title','Agregar Rol');
        $smarty->display(DOC_ROOT.'/templates/boxes/add-rol-popup.tpl');
    break;
    case 'saveRol':
         $rol->setName($_POST['name']);
         if($rol->Save())
         {

             $roles = $rol->Enumerate();
             $smarty->assign('roles',$roles);
             echo "ok[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
             echo "[#]";
             $smarty->display(DOC_ROOT.'/templates/lists/rol.tpl');

         }
         else{
             echo "fail[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
         }
    break;
    case 'editRol':
        $rol->setRolId($_POST['id']);
        $post = $rol->Info();
        $smarty->assign('post',$post);
        $smarty->assign('title','Editar Rol');
        $smarty->display(DOC_ROOT.'/templates/boxes/add-rol-popup.tpl');
    break;
    case 'updateRol':
        $rol->setRolId($_POST['rolId']);
        $rol->setName($_POST['name']);
        if($rol->Update())
        {
            $roles = $rol->Enumerate();
            $smarty->assign('roles',$roles);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/lists/rol.tpl');

        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        break;
    case 'deleteRol':
        $rol->setRolId($_POST['id']);
        if($rol->Delete())
        {
            $roles = $rol->Enumerate();
            $smarty->assign('roles',$roles);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/lists/rol.tpl');

        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        break;
}