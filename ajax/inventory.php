<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST['type']){
    case 'search':
        echo "ok[#]";
        $smarty->assign("registros",$inventory->searchResource());
        $smarty->display(DOC_ROOT."/templates/lists/resource-office.tpl");
    break;
    case "openAddResource":
        $data['title'] =  "Agregar recurso a inventario";
        $data["form"] = "frm-resource-to-inventory";
        $smarty->assign("data",$data);
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
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
        $data['title'] =  "Editar recurso";
        $data["form"] = "frm-resource-to-inventory";
        $smarty->assign("data",$data);

        $inventory->setId($_POST["id"]);
        $info = $inventory->infoResource();
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
    case 'openModalResponsable':
        $inventory->setId($_POST["id"]);
        $info = $inventory->infoResource();
        $data['title'] =  $_POST['rs_id']?"Editar responsable":"Agregar responsable";
        $data["form"] = "frm-responsable-to-resource";
        $inventory->setResponsableResourceId($_POST['rs_id']);
        $smarty->assign("data",$data);
        $smarty->assign("resource",$info);
        $smarty->assign("post",$inventory->infoResponsableResource());
        $smarty->assign("empleados",$personal->EnumerateAll());
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
    break;
    case 'saveResponsableResource':
        $inventory->setId($_POST['office_resource_id']);
        if($_POST['rs_id'])
            $inventory->setResponsableResourceId($_POST['rs_id']);

        $inventory->setPersonalId($_POST['personalId']);
        $inventory->setFechaEntregaResponsable($_POST['fecha_entrega']);
        $inventory->setTipoResponsable($_POST['tipo_responsable']);
        $inventory->validateFileResponsiva(!isset($_POST['rs_id']));
        if($inventory->saveResponsablesResource()){
            $smarty->assign("responsables",$inventory->getListResponsablesResource($_POST['office_resource_id'],true));
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT."/templates/lists/responsables-resources.tpl");

        }else{

            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        break;

    case 'openDeleteResponsable':
        $data['title'] = "Baja de responsable";
        $data["form"] = "frm-baja-responsable";
        $inventory->setResponsableResourceId($_POST['rs_id']);
        $smarty->assign("data",$data);
        $smarty->assign("post",$inventory->infoResponsableResource());
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
        break;

    case 'saveBajaResponsable':
        $inventory->setMotivoBaja($_POST['motivo_baja']);
        $inventory->validateFileResponsiva();
        $inventory->setResponsableResourceId($_POST['rs_id']);
        $inventory->setId($_POST['office_resource_id']);
        if($inventory->saveDeleteResponsable()){
            $smarty->assign("responsables",$inventory->getListResponsablesResource($_POST['office_resource_id'],true));
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT."/templates/lists/responsables-resources.tpl");

        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
    break;
    case 'openModalUpkeep':
        $data['title'] =  $_POST['upk_id']?"Editar mantenimiento":"Registrar mantenimiento";
        $data["form"] = "frm-upkeep-to-resource";
        $inventory->setUpkeepId($_POST['upk_id']);
        $inventory->setId($_POST['id']);
        $info = $inventory->infoResource();
        $smarty->assign("data",$data);
        $smarty->assign("resource",$info);
        $smarty->assign("post",$inventory->infoUpkeep());
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
        break;

    case 'saveUpkeepResource':
        $inventory->setId($_POST['office_resource_id']);
        if($_POST['upk_id'])
            $inventory->setUpkeepId($_POST['upk_id']);

        $inventory->setUpkeepResponsable($_POST['upkeep_responsable']);
        $inventory->setUpkeepDate($_POST['upkeep_date']);
        $inventory->setUpkeepDescription($_POST['upkeep_description']);
        if($inventory->saveUpkeepResource()){
            $smarty->assign("upkeeps",$inventory->enumerateUpKeeps());
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT."/templates/lists/upkeeps-resource.tpl");

        }else{

            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        break;

    case 'deleteUpkeep':
        $inventory->setUpkeepId($_POST['upk_id']);
        $inventory->setId($_POST['id']);
        if($inventory->deleteUpkeep()){
            $smarty->assign("upkeeps",$inventory->enumerateUpKeeps());
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->display(DOC_ROOT."/templates/lists/upkeeps-resource.tpl");

        }else{

            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }

        break;

}
