<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
    case "reportPendingPopUp":
        $data["tittle"]="Agregar pendiente";
        $data["nameForm"]="frmAddPending";
        $data["nameType"]="savePending";
        $data["nameBtn"]="Guardar";
        $smarty->assign("data",$data);
        $smarty->display(DOC_ROOT.'/templates/boxes/report-pending-popup.tpl');
    break;

    case "savePending";
        $change->setDescripcion($_POST["descripcion"]);
        $change->setModulo($_POST["modulo"]);
        $change->setFechaSolicitud($_POST["fsolicitud"]);
        $change->setFechaRevision($_POST["frevision"]);
        $change->setFechaEntrega($_POST["fentrega"]);
        if($change->savePending()){
            echo "ok";
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $result = $change->Enumerate();
            $smarty->assign("result",$result);
            $smarty->display(DOC_ROOT.'/templates/lists/report-pending.tpl');
        }else{
            echo "fail";
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }

    break;
    case 'editPendingPopUp':
        $data["tittle"]="Editar";
        $data["nameForm"]="frmEditPending";
        $data["nameType"]="updatePending";
        $data["nameBtn"]="Actualizar";
        $change->setId($_POST["id"]);
        $info = $change->Info();
        $smarty->assign("data",$data);
        $smarty->assign("post",$info);
        $smarty->display(DOC_ROOT.'/templates/boxes/report-pending-popup.tpl');

    break;
    case "updatePending";
        $change->setId($_POST["changeId"]);
        $change->setDescripcion($_POST["descripcion"]);
        $change->setModulo($_POST["modulo"]);
        $change->setFechaSolicitud($_POST["fsolicitud"]);
        $change->setFechaRevision($_POST["frevision"]);
        $change->setFechaEntrega($_POST["fentrega"]);
        if($change->updatePending()){
            echo "ok";
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $result = $change->Enumerate();
            $smarty->assign("result",$result);
            $smarty->display(DOC_ROOT.'/templates/lists/report-pending.tpl');
        }else{
            echo "fail";
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }

     break;
    case 'deletePending':
        $change->setId($_POST["id"]);
        if($change->deletePending()){
            echo "ok";
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $result = $change->Enumerate();
            $smarty->assign("result",$result);
            $smarty->display(DOC_ROOT.'/templates/lists/report-pending.tpl');
        }else{
            echo "fail";
            echo "[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;

}
?>