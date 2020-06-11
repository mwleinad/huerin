<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST['type']){
    case 'openModalActivity':

        $data['title'] = $_POST['id'] ?  'Agregar' : 'Editar';
        $data['form'] =  'frm-activity';
        if($_POST['id']) {
            $activity->setId($_POST['id']);
            $info = $activity->info();
        }
        $smarty->assign('sectores', $catalogue->ListSectores());
        $smarty->assign('subsectores', $catalogue->ListSubsectores($info['sector_id'] ? $info['sector_id'] : 0));
        $smarty->assign('data',$data);
        $smarty->assign('post',$info);
        $smarty->display(DOC_ROOT.'/templates/boxes/general-popup.tpl');
    break;
    case 'save':
         if($_POST['id'])
             $activity->setId($_POST['id']);
         $activity->setName($_POST['name']);
         $activity->setSubsectorId($_POST['subsector']);
         if($activity->save())
         {
             $data = $activity->enumerate();
             $smarty->assign('data',$data);
             echo "ok[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
             echo "[#]";
             $smarty->display(DOC_ROOT.'/templates/lists/activity.tpl');
         }
         else{
             echo "fail[#]";
             $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
         }
    break;
    case 'delete':

        $activity->setId($_POST['id']);
        if($activity->delete())
        {
            $data = $activity->enumerate();
            $smarty->assign('data',$data);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/lists/activity.tpl');
        }
        else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        break;
}