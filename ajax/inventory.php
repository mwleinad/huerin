<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
include(DOC_ROOT.'/libs/excel/PHPExcel.php');
switch($_POST['type']){
    case 'search':
        $inventory->generateReport();
        $nameFile = $inventory->getNameReport();
        echo "ok[#]";
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
    break;
    case "openAddResource":
        if (isset($_SESSION['device_resource']))
           unset($_SESSION['device_resource']);


        $data['title'] =  "Agregar recurso a inventario";
        $data["form"] = "frm-resource-to-inventory";
        $smarty->assign("data",$data);
        $smarty->assign("consecutive",$inventory->getConsecutiveIdResource());
        $smarty->assign("devices",$inventory->listResourceInStock());
        $smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
    break;
    case 'saveResource':
        $inventory->setTipoRecurso($_POST['tipo_recurso']);
       // $inventory->setNombre($_POST['nombre']);
        if($_POST['tipo_recurso'] !== 'dispositivo')
            $inventory->setDescripcion($_POST['descripcion']);

        $inventory->setFechaCompra($_POST['fecha_compra']);
        /*$inventory->withMouse(isset($_POST['con_mouse']) ? 1 : 0);
        $inventory->withKeyboard(isset($_POST['con_teclado']) ? 1 : 0);
        $inventory->withMousepad(isset($_POST['con_mousepad']) ? 1 : 0);
        $inventory->withVentilador(isset($_POST['con_ventilador']) ? 1 : 0);
        $inventory->withMonitor(isset($_POST['con_monitor']) ? 1 : 0);
        $inventory->withHdmi(isset($_POST['con_hdmi']) ? 1 : 0);
        $inventory->withEthernet(isset($_POST['con_ethernet']) ? 1 : 0);
        $inventory->withHubUsb(isset($_POST['hub_usb']) ? 1 : 0);
        $inventory->withNobreak(isset($_POST['no_break']) ? 1 : 0);*/
        $inventory->setNoLicencia($_POST['no_licencia']);
        $inventory->setCodigoActivacion($_POST['cod_activacion']);
        $inventory->setMarca($_POST['marca']);
        $inventory->setModelo($_POST['modelo']);
        $inventory->setProcesador($_POST['procesador']);
        if($_POST['tipo_recurso'] === 'equipo_computo') {
            $inventory->setNoInventario($_POST['no_inventario']);
            $inventory->setTipoEquipo($_POST['tipo_equipo']);
        }


        if($_POST['tipo_recurso'] === 'dispositivo')
            $inventory->setTipoDispositivo($_POST['tipo_dispositivo']);

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
        //$inventory->setNombre($_POST['nombre']);
        if($_POST['tipo_recurso'] !== 'dispositivo')
            $inventory->setDescripcion($_POST['descripcion']);

        $inventory->setFechaCompra($_POST['fecha_compra']);
        /*$inventory->withMouse(isset($_POST['con_mouse']) ? 1 : 0);
        $inventory->withKeyboard(isset($_POST['con_teclado']) ? 1 : 0);
        $inventory->withMousepad(isset($_POST['con_mousepad']) ? 1 : 0);
        $inventory->withVentilador(isset($_POST['con_ventilador']) ? 1 : 0);
        $inventory->withMonitor(isset($_POST['con_monitor']) ? 1 : 0);
        $inventory->withHdmi(isset($_POST['con_hdmi']) ? 1 : 0);
        $inventory->withEthernet(isset($_POST['con_ethernet']) ? 1 : 0);
        $inventory->withHubUsb(isset($_POST['hub_usb']) ? 1 : 0);
        $inventory->withNobreak(isset($_POST['no_break']) ? 1 : 0);*/
        $inventory->setNoLicencia($_POST['no_licencia']);
        $inventory->setCodigoActivacion($_POST['cod_activacion']);
        $inventory->setMarca($_POST['marca']);
        $inventory->setModelo($_POST['modelo']);
        $inventory->setProcesador($_POST['procesador']);

        if($_POST['tipo_recurso'] === 'equipo_computo') {
            $inventory->setNoInventario($_POST['no_inventario']);
            $inventory->setTipoEquipo($_POST['tipo_equipo']);
        }

        if($_POST['tipo_recurso'] === 'dispositivo')
            $inventory->setTipoDispositivo($_POST['tipo_dispositivo']);

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

        if (isset($_SESSION['device_resource']))
            unset($_SESSION['device_resource']);

        $data['title'] =  "Editar recurso";
        $data["form"] = "frm-resource-to-inventory";
        $smarty->assign("data",$data);

        $inventory->setId($_POST["id"]);
        $info = $inventory->infoResource();
        $_SESSION['device_resource'] = $info['device_resource'];
        $smarty->assign("post",$info);
        $smarty->assign("devices",$inventory->listResourceInStock());
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

    case 'addDeviceToKit':
        if (!isset($_SESSION['device_resource']))
            $_SESSION['device_resource'] = [];

        if(in_array($_POST['device_id'], array_column($_SESSION['device_resource'],'office_resource_id')))
            $util->setError(0, "error", "El dispositivo ya se encuentra vinculado.");

        if((!isset($_POST['device_id']) || $_POST['device_id'] === ''))
            $util->setError(0, "error", "Seleccione un dispositivo disponible.");

        if($util->PrintErrors()) {
            $json['status'] = 'fail';
            $json['message'] = $smarty->fetch(DOC_ROOT. "/templates/boxes/status_on_popup.tpl");
        } else {

            $inventory->setId($_POST['device_id']);
            $resource = $inventory->basicInfoResource();

            end($_SESSION['device_resource']);
            $key = isset($_POST['key']) ? $_POST['key'] : key($_SESSION['device_resource']) + 1;
            $_SESSION['device_resource'][$key] =  $resource;
            $_SESSION['device_resource'][$key]['id'] = !$_POST['key'] ? null : $_SESSION['device_resource'][$key]['id'];
            $smarty->assign('listDevices', $_SESSION['device_resource']);
            $json['status'] = 'ok';
            $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/lists/computo_device.tpl");
            $json['listDevices'] = $_SESSION['device_resource'];
        }
        echo json_encode($json);
    break;
    case "deleteFromResource":
        //
        $item = $_SESSION['device_resource'][$_POST['key']];
        if($item['id']) {
            $_SESSION['device_resource'][$_POST['key']]['deleteAction'] = true;
            $_SESSION['device_resource'][$_POST['key']]['typeDelete'] = 'deleteFromResource';
        }
        else
            unset($_SESSION['device_resource'][$_POST['key']]);

        $smarty->assign('listDevices', $_SESSION['device_resource']);
        $json['template'] = $smarty->fetch(DOC_ROOT."/templates/lists/computo_device.tpl");
        $json['listDevices'] = $_SESSION['device_resource'];
        echo json_encode($json);
    break;
    case "deleteFromStock":
        $_SESSION['device_resource'][$_POST['key']]['deleteAction'] = true;
        $_SESSION['device_resource'][$_POST['key']]['typeDelete'] = 'deleteFromStock';
        $smarty->assign('listDevices', $_SESSION['device_resource']);
        $json['template'] = $smarty->fetch(DOC_ROOT."/templates/lists/computo_device.tpl");
        $json['listDevices'] = $_SESSION['device_resource'];
        echo json_encode($json);
        break;
}
