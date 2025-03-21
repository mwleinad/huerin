<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');
switch ($_POST["type"]) {
    case "updateCuentas":
        $cuentas = $contract->Enumerate($_POST["customerId"]);
        ?>
        <select id="cuenta" name="cuenta" class="largeInput">
            <?php foreach ($cuentas as $cuenta) { ?>
                <option value="<?php echo $cuenta["contractId"] ?>"><?php echo $cuenta["name"] ?></option>
            <?php } ?>
        </select>
        <?php

        break;
    case "buscarServiciosActivos":

        $User["userId"] = $_POST["responsableCuenta"];

        $personal->setPersonalId($_POST["responsableCuenta"]);
        $myUser = $personal->Info();
        $roleId = $personal->GetRoleId($myUser["tipoPersonal"]);
        if ($_POST["responsableCuenta"]) {
            $User["roleId"] = $roleId;
            $User["departamentoId"] = $myUser["departamentoId"];
        }

        $deep = $_POST['deep'];
        $servicios = $servicio->EnumerateActive($deep, $_POST["customerId"], $_POST["contractId"], $_POST["rfc"], $_POST["departamentoId"]);

        $smarty->assign("rfc", urlencode($_POST["rfc"]));
        $smarty->assign("servicios", $servicios);
        $smarty->display(DOC_ROOT . '/templates/lists/servicios_activos.tpl');
        echo "[#]";
        $smarty->assign("rfc", urlencode($_POST["rfc"]));
        $smarty->assign("servicios", $servicios);
        $smarty->display(DOC_ROOT . '/templates/lists/servicios_activos2.tpl');
        break;
    case "updateCosto":
        $tipoServicio->setTipoServicioId($_POST["id"]);
        $servicio = $tipoServicio->Info();

        if ($servicio["costo"] == 0) {
            echo $servicio["costoUnico"];
        } else {
            echo $servicio["costo"];
        }

        break;
    case "addServicio":
        if (isset($_SESSION["itemsServices"]))
            unset($_SESSION['itemsServices']);

        $tiposDeServicio = $tipoServicio->EnumerateOnly2025();
        $smarty->assign("tiposDeServicio", $tiposDeServicio);
        $smarty->assign("post", $_POST);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/boxes/add-servicio-popup.tpl');
        break;
    case "addItemService":
        $servicio->setContractId($_POST["contractId"]);
        $servicio->setTipoServicioId($_POST['tipoServicioId']);
        $servicio->setInicioFactura($_POST['inicioFactura']);
        $servicio->setInicioOperaciones($_POST['inicioOperaciones']);
        $servicio->setCosto($_POST['costo']);
        if ($servicio->saveItemInSession()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("itemsServices", $_SESSION['itemsServices']);
            $smarty->display(DOC_ROOT . '/templates/lists/multiple-services-items.tpl');
        } else {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }
        break;
    case "delItemService":
        $key = $_POST['id'];
        $servicio->deleteItemInSession($key);
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $smarty->assign("itemsServices", $_SESSION['itemsServices']);
        $smarty->display(DOC_ROOT . '/templates/lists/multiple-services-items.tpl');
        break;
    case "saveAddServicio":
        $servicio->setContractId($_POST['contractId']);
        $servicio->setTipoServicioId($_POST['tipoServicioId']);
        $servicio->setCosto($_POST['costo']);
        $servicio->setInicioFactura($_POST['inicioFactura']);
        $servicio->setInicioOperaciones($_POST['inicioOperaciones']);
        $servicioId = $servicio->Save();
        if (!$servicioId) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        } else {
            //Guardamos el Log de Eventos
            $servicio->setServicioId($servicioId);
            $newServicio = $servicio->InfoLog();

            $log->setPersonalId($User['userId']);
            $log->setFecha(date('Y-m-d H:i:s'));
            $log->setTabla('servicio');
            $log->setTablaId($servicioId);
            $log->setAction('Insert');
            $log->setOldValue('');
            $log->setNewValue(serialize($newServicio));
            $log->Save();

            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";

            //echo $_POST['contractId'];
            $contract->setContractId($_POST['contractId']);
            $info = $contract->Info();

            $contract->setCustomerId($info["customerId"]);
            $resContracts = $contract->Enumerate($info["customerId"]);

            $empleados = $personal->Enumerate();
            $empleados = $util->EncodeResult($empleados);
            $smarty->assign("empleados", $empleados);

            $smarty->assign("contracts", $resContracts);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/contract.tpl');
        }

        break;
    case "saveMultipleServicio":
        $servicio->setContractId($_POST['contractId']);
        if ($servicio->saveMultipleServicio()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            if($_POST["fromEvent"] == 'from-contract'){
                $contract->setContractId($_POST['contractId']);
                $info = $contract->Info();
                $departamentos = $departamentos->Enumerate();
                $smarty->assign("departamentos", $departamentos);
                $contract->setCustomerId($info["customerId"]);
                $resContracts = $contract->Enumerate($info["customerId"]);
                $smarty->assign("contracts", $resContracts);
                $smarty->assign("DOC_ROOT", DOC_ROOT);
                $smarty->display(DOC_ROOT . '/templates/lists/contract.tpl');
            }else{
                $servicio->setContractId($_POST['contractId']);
                $servicios = $servicio->Enumerate();
                $smarty->assign('servicios', $servicios);
                $smarty->display(DOC_ROOT . '/templates/lists/servicios.tpl');
            }

        } else {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }
        break;
    case "activateService":
        $servicioId = $_POST['servicioId'];
        $servicio->setServicioId($servicioId);
        if ($servicio->ActivateService()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status.tpl');
            echo "[#]";
            $servicio->setContractId($_POST['contractId']);
            $servicios = $servicio->Enumerate();
            $smarty->assign("servicios", $servicios);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/servicios.tpl');
        }

        break;

    case "editServicio":
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $servicio->setServicioId($_POST['servicioId']);
        $myServicio = $servicio->Info();

        $tiposDeServicio = $tipoServicio->EnumerateAll();
        $smarty->assign("tiposDeServicio", $tiposDeServicio);

        $info = $util->EncodeRow($myServicio);

        $smarty->assign("post", $info);
        $smarty->display(DOC_ROOT . '/templates/boxes/edit-servicio-popup.tpl');

        break;
    case "historial":
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $servicio->setServicioId($_POST['servicioId']);
        $historial = $servicio->Historial();

        $smarty->assign("historial", $historial);
        $smarty->display(DOC_ROOT . '/templates/boxes/historial-servicio-popup.tpl');

        break;
    case "saveEditServicio":
        $servicioId = $_POST['servicioId'];
        $servicio->setContractId($_POST['contractId']);
        $servicio->setServicioId($_POST['servicioId']);
        $servicio->setTipoServicioId($_POST['tipoServicioId']);
        $servicio->setCosto($_POST['costo']);
        $servicio->setInicioFactura($_POST['inicioFactura']);
        $servicio->setInicioOperaciones($_POST['inicioOperaciones']);
        $servicio->setServicioId($servicioId);
        $myServicio = $servicio->Info();

        if (!$servicio->Edit()) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        } else {
            //Guardamos el Log de Eventos
            $newServicio = $servicio->Info();
            $log->setPersonalId($User['userId']);
            $log->setFecha(date('Y-m-d H:i:s'));
            $log->setTabla('servicio');
            $log->setTablaId($servicioId);
            $log->setAction('Update');
            $log->setOldValue(serialize($myServicio));
            $log->setNewValue(serialize($newServicio));
            $log->Save();

            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $servicio->setContractId($myServicio['contractId']);
            $servicios = $servicio->Enumerate();

            $smarty->assign("servicios", $servicios);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/servicios.tpl');
        }
        break;
    case "cancelWorkFlow":
        $servicio->setInstanciaServicioId($_POST['instanciaServicioId']);
        $servicio->setStatus("baja");
        $servicio->CancelWorkFlow();
        echo "ok[#]";
        $_SESSION['msgOk'] = 1;
        break;
    case "activateWorkFlow":
        $servicio->setInstanciaServicioId($_POST['instanciaServicioId']);
        $servicio->setStatus("activa");
        $servicio->CancelWorkFlow();
        echo "ok[#]";
        $_SESSION['msgOk'] = 2;
        break;
    case 'changeDateWorkFlow':
        $workflowId = $_POST['id'];
        $servicio->setInstanciaServicioId($_POST['idWorkFlow']);
        $servicio->setFechaDoc($_POST['dateNew']);
        if ($servicio->ChangeDateWorkFlow()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');

        } else {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }
        break;
    case 'downService':
        $serviceId = $_POST['servicioId'];
        $smarty->assign('post', $_POST);
        $smarty->display(DOC_ROOT . '/templates/boxes/down-servicio-popup.tpl');

        break;
    case 'doDownServicio':
        $tipo = 'partial';
        $servicioId = $_POST['servicioId'];
        $servicio->setTipoBaja($tipo);
        $servicio->setServicioId($servicioId);
        if ($tipo == 'partial')
            $servicio->setLastDateWorkflow($_POST['lastDateWorkflow']);

        if ($servicio->DownServicio()) {
            //find info servicio
            $servicio->setServicioId($servicioId);
            $info = $servicio->Info();
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $servicio->setContractId($info['contractId']);
            $servicios = $servicio->Enumerate();

            $smarty->assign("servicios", $servicios);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/servicios.tpl');
        } else {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');

        }

        break;
    case 'multipleOperationPopUp':
        $datos = json_decode($_POST['datos']);
        $datos = json_decode($datos, true);
        $smarty->assign("servicios", $datos);
        $smarty->assign("contractId", $_POST['contractId']);
        $smarty->assign("title", "Multiples operaciones");
        $smarty->display(DOC_ROOT . '/templates/boxes/multiple-edit-services-popup.tpl');
        break;
    //editar multiples servicios
    case 'saveMultipleService':
        $servicio->setContractId($_POST['contractId']);
        if ($servicio->executeMultipleOperation()) {
            $servicio->setContractId($_POST['contractId']);
            $servicios = $servicio->Enumerate();
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign('servicios', $servicios);
            $smarty->display(DOC_ROOT . '/templates/lists/servicios.tpl');

        } else {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');

        }
        break;
}
?>
