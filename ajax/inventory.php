<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST['type']){
    case 'saveResourceToInventory':

    break;
    case "openAddResource":
        $inventory->CleanResponsables();
        $data['title'] =  "Agregar recurso a inventario";
        $data["form"] = "frm-resource-to-inventory";
        $smarty->assign("data",$data);
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
    break;
    case 'addResponsableToArray':
        $inventory->setNombreResponsable($_POST['nombre_responsable']);
        $inventory->setFechaEntregaResponsable($_POST['fecha_entrega']);
        $inventory->setTipoResponsable($_POST['tipo_responsable']);
        if($inventory->addResponsablesToArray()){
            $smarty->assign("responsables",$_SESSION['responsables_resource']);
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT."/templates/lists/responsables-resources.tpl");

        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;
    case 'deleteResponsable':
        if($inventory->deleteResponsableFromArray($_POST['id'])){
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("responsables",$_SESSION['responsables_resource']);
            $smarty->display(DOC_ROOT."/templates/lists/responsables-resources.tpl");

        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;
    case 'saveResource':
        $inventory->setTipoRecurso($_POST['tipo_recurso']);
        $inventory->setNombre($_POST['nombre']);
        $inventory->setDescripcion($_POST['descripcion']);
        $inventory->setFechaCompra($_POST['fecha_compra']);
        if($_POST['tipo_recurso']=='equipo_computo'){
            $hub_usb =  isset($_POST['hub_usb']);
            $no_break = isset($_POST['no_break']);
            $inventory->withHubUsb($hub_usb);
            $inventory->withNobreak($no_break);
            $inventory->setNoLicencia($_POST['no_licencia']);
            $inventory->setCodigoActivacion($_POST['cod_activacion']);
            $inventory->setTipoEquipo($_POST['tipo_equipo']);
        }
        $inventory->setNoSerie($_POST['no_serie']);
        if($inventory->saveResource()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("registros",$inventory->enumerateResource());
            $smarty->display(DOC_ROOT."/templates/lists/resource-office.tpl");

        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;
    case 'updateResource':
        $inventory->setId($_POST['office_resource_id']);
        $inventory->setTipoRecurso($_POST['tipo_recurso']);
        $inventory->setNombre($_POST['nombre']);
        $inventory->setDescripcion($_POST['descripcion']);
        $inventory->setFechaCompra($_POST['fecha_compra']);
        if($_POST['tipo_recurso']=='equipo_computo'){
            $hub_usb =  isset($_POST['hub_usb']);
            $no_break = isset($_POST['no_break']);
            $inventory->withHubUsb($hub_usb);
            $inventory->withNobreak($no_break);
            $inventory->setNoLicencia($_POST['no_licencia']);
            $inventory->setCodigoActivacion($_POST['cod_activacion']);
            $inventory->setTipoEquipo($_POST['tipo_equipo']);
        }
        $inventory->setNoSerie($_POST['no_serie']);
        if($inventory->updateResource()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("registros",$inventory->enumerateResource());
            $smarty->display(DOC_ROOT."/templates/lists/resource-office.tpl");

        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        break;
    case 'openEditResource':
        $inventory->CleanResponsables();
        $data['title'] =  "Editar recurso";
        $data["form"] = "frm-resource-to-inventory";
        $smarty->assign("data",$data);

        $inventory->setId($_POST["id"]);
        $info = $inventory->infoResource();

        if(isset($info["responsables"]))
            if(count($info["responsables"])>0){
                $_SESSION["responsables_resource"] = $info["responsables"];
                $smarty->assign('responsables',$_SESSION["responsables_resource"]);
            }
        $smarty->assign("post",$info);
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
    break;
    case 'openDeleteResource':
        $data['title'] =  "Realizar baja";
        $data["form"] = "frm-baja-resource";
        $smarty->assign("data",$data);

        $inventory->setId($_POST["id"]);
        $info = $inventory->infoResource();

        $smarty->assign("post",$info);
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
    break;
    case 'downResource':
        $inventory->setId($_POST["office_resource_id"]);
        $inventory->setMotivoBaja($_POST["motivo_baja"]);
        if($inventory->makeDownResource()){
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("registros",$inventory->enumerateResource());
            $smarty->display(DOC_ROOT."/templates/lists/resource-office.tpl");

        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;

}