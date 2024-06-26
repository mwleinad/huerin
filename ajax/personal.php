<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');
switch ($_POST["type"]) {
    case "addPersonal":
        $departamentos = $personal->ListDepartamentos();
        $smarty->assign("departamentos", $departamentos);

        $personal->isShowAll();
        $miPersonal = $personal->Enumerate();
        $smarty->assign("personal", $miPersonal);

        $roles = $rol->GetListRoles();
        $smarty->assign("roles", $roles);

        $expedientes = $expediente->Enumerate();
        foreach ($expedientes as $key => $value) {
            if (!strpos(strtolower($value['name']), 'fonacot'))
                $expedientes[$key]['find'] = true;
        }
        $data["idBtn"] = "btnAddPersonal";
        $data["nameBtn"] = "Agregar";
        $data["title"] = "Agregar empleado";
        $smarty->assign("expedientes", $expedientes);
        $smarty->assign("resources", $inventory->listResource());
        $smarty->assign("data", $data);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/boxes/personal-popup.tpl');
        break;
    case "saveAddPersonal":

        $personal->setName($_POST['name']);
        if (isset($_POST["sueldo"]))
            $personal->setSueldo($_POST['sueldo']);
        if (isset($_POST["phone"]))
            $personal->setPhone($_POST['phone']);
        if (isset($_POST["ext"]))
            $personal->setExt($_POST['ext']);

        if (isset($_POST["celphone"]))
            $personal->setCelphone($_POST['celphone']);
        if (isset($_POST["email"]))
            $personal->setEmail($_POST['email']);
        if (isset($_POST["skype"]))
            $personal->setSkype($_POST['skype']);

        if (isset($_POST["systemAspel"]))
            $personal->setSystemAspel($_POST['systemAspel']);
        if (isset($_POST["userAspel"]))
            $personal->setUserAspel($_POST['userAspel']);
        if (isset($_POST["passwordAspel"]))
            $personal->setPasswordAspel($_POST['passwordAspel']);

        if (isset($_POST["horario"]))
            $personal->setHorario($_POST['horario']);
        if (isset($_POST["fechaIngreso"])) {
            $fechaIngreso = ($_POST['fechaIngreso'] == '') ? '' : date('Y-m-d', strtotime($_POST['fechaIngreso']));
            $personal->setFechaIngreso($fechaIngreso);
        }
        if (isset($_POST["grupo"]))
            $personal->setGrupo($_POST['grupo']);
        if (isset($_POST["mail_grupo"]))
            $personal->setMailGrupo($_POST['mail_grupo']);
        if (isset($_POST["lista_distribucion"]))
            $personal->setListaDistribucion($_POST['lista_distribucion']);
        if (isset($_POST["userComputadora"]))
            $personal->setUserComputadora($_POST['userComputadora']);
        if (isset($_POST["passwordComputadora"]))
            $personal->setPasswordComputadora($_POST['passwordComputadora']);
        if (isset($_POST["username"]))
            $personal->setUsername($_POST['username']);
        if (isset($_POST["passwd"]))
            $personal->setPasswd($_POST['passwd']);
        if (isset($_POST["puesto"]))
            $personal->setPuesto($_POST['puesto']);
        if (isset($_POST["tipoPersonal"])) {
            $personal->setTipoPersonal($_POST['tipoPersonal']);
            $rol->setTitulo($_POST['tipoPersonal']);
            $roleId = $rol->GetIdByName();
            $personal->setRole($roleId);
        }

        if(isset($_POST["nivel"]))
            $personal->setNivel($_POST['nivel']);

        if (isset($_POST["departamentoId"]))
            $personal->setDepartamentoId($_POST['departamentoId']);
        if (isset($_POST["jefeInmediato"]))
            $personal->setJefeInmediato($_POST['jefeInmediato']);

        if ($_POST['active'])
            $personal->setActive(1);
        else
            $personal->setActive(0);

        if (isset($_POST["resource_id"]))
            $personal->setResource($_POST['resource_id']);

        if (isset($_POST["numberAccountsAllowed"]))
            $personal->setNumberAccountsAllowed($_POST['numberAccountsAllowed']);

        if (isset($_POST["cuenta_inhouse"]))
            $personal->setCuentaInhouse($_POST['cuenta_inhouse']);

        if (!$personal->Save()) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        } else {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $resPersonals = $personal->Enumerate();
            //$personals = $util->EncodeResult($resPersonals);
            $smarty->assign("personals", $resPersonals);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/personal.tpl');
        }
        break;
    case "deletePersonal":
        $personal->setPersonalId($_POST['personalId']);
        if ($personal->Delete()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status.tpl');
            echo "[#]";
            $resPersonals = $personal->Enumerate();
            //$personals = $util->EncodeResult($resPersonals);

            $smarty->assign("personals", $resPersonals);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/personal.tpl');
        }
        break;
    case "editPersonal":
        $personal->isShowAll();
        $miPersonal = $personal->Enumerate();
        $smarty->assign("personal", $miPersonal);

        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $personal->setPersonalId($_POST['personalId']);
        $myPersonal = $personal->Info();

        $info = $myPersonal;

        if ($info['fechaIngreso'])
            $info['fechaIngreso'] = date('Y-m-d', strtotime($info['fechaIngreso']));

        $smarty->assign("post", $info);

        $departamentos = $personal->ListDepartamentos();
        $smarty->assign("departamentos", $departamentos);

        //comprobar si se encuentra configurado el empleado con sus expedientes
        $db->setQuery('select * from personalExpedientes where personalId="' . $myPersonal['personalId'] . '" ');
        $resExp = $db->GetResult();

        $roles = $rol->GetListRoles();
        $smarty->assign("roles", $roles);

        $expedientes = $expediente->Enumerate();
        if (empty($resExp)) {
            //si no se encuentra configurado se muestran chekeados todos.
            foreach ($expedientes as $key => $value) {
                if (!strpos(strtolower($value['name']), 'fonacot'))
                    $expedientes[$key]['find'] = true;
            }
            $smarty->assign("msgExp", 'Es necesario guardar cambios, para que los expedientes queden registrados.');
        } else {
            foreach ($expedientes as $key => $value) {
                $db->setQuery('select * from personalExpedientes where personalId="' . $myPersonal['personalId'] . '" AND expedienteId="' . $value['expedienteId'] . '"');
                $find = $db->GetRow();
                if (!empty($find)) {
                    $expedientes[$key]['find'] = true;
                } else
                    $expedientes[$key]['find'] = false;
            }
        }
        $data["idBtn"] = "editPersonal";
        $data["nameBtn"] = "Actualizar";
        $data["title"] = "Editar empleado";
        $smarty->assign("data", $data);
        $smarty->assign("resources", $inventory->listResource());
        $smarty->assign("expedientes", $expedientes);
        $smarty->display(DOC_ROOT . '/templates/boxes/personal-popup.tpl');
        break;
    case "saveEditPersonal":
        $personal->setPersonalId($_POST['personalId']);
        $personal->setName($_POST['name']);
        if (isset($_POST["sueldo"]))
            $personal->setSueldo($_POST['sueldo']);
        if (isset($_POST["phone"]))
            $personal->setPhone($_POST['phone']);

        if (isset($_POST["ext"]))
            $personal->setExt($_POST['ext']);

        if (isset($_POST["celphone"]))
            $personal->setCelphone($_POST['celphone']);

        if (isset($_POST["email"]))
            $personal->setEmail($_POST['email']);
        if (isset($_POST["skype"]))
            $personal->setSkype($_POST['skype']);
        if (isset($_POST["systemAspel"]))
            $personal->setSystemAspel($_POST['systemAspel']);
        if (isset($_POST["userAspel"]))
            $personal->setUserAspel($_POST['userAspel']);
        if (isset($_POST["passwordAspel"]))
            $personal->setPasswordAspel($_POST['passwordAspel']);
        if (isset($_POST["horario"]))
            $personal->setHorario($_POST['horario']);
        if (isset($_POST["fechaIngreso"])) {
            $fechaIngreso = ($_POST['fechaIngreso'] == '') ? '' : date('Y-m-d', strtotime($_POST['fechaIngreso']));
            $personal->setFechaIngreso($fechaIngreso);
        }
        if (isset($_POST["grupo"]))
            $personal->setGrupo($_POST['grupo']);

        if (isset($_POST["mail_grupo"]))
            $personal->setMailGrupo($_POST['mail_grupo']);

        if (isset($_POST["lista_distribucion"]))
            $personal->setListaDistribucion($_POST['lista_distribucion']);

        if (isset($_POST["userComputadora"]))
            $personal->setUserComputadora($_POST['userComputadora']);
        if (isset($_POST["passwordComputadora"]))
            $personal->setPasswordComputadora($_POST['passwordComputadora']);
        if (isset($_POST["username"]))
            $personal->setUsername($_POST['username']);
        if (isset($_POST["passwd"]))
            $personal->setPasswd($_POST['passwd']);
        if (isset($_POST["puesto"]))
            $personal->setPuesto($_POST['puesto']);

        if (isset($_POST["tipoPersonal"])) {
            $personal->setTipoPersonal($_POST['tipoPersonal']);
            $rol->setTitulo($_POST['tipoPersonal']);
            $roleId = $rol->GetIdByName();
            $personal->setRole($roleId);
        }

        if(isset($_POST["nivel"]))
            $personal->setNivel($_POST['nivel']);

        if (isset($_POST["departamentoId"]))
            $personal->setDepartamentoId($_POST['departamentoId']);
        if (isset($_POST["jefeInmediato"]))
            $personal->setJefeInmediato($_POST['jefeInmediato']);

        if (isset($_POST["resource_id"]))
            $personal->setResource($_POST['resource_id']);

        if (isset($_POST["numberAccountsAllowed"]))
            $personal->setNumberAccountsAllowed($_POST['numberAccountsAllowed']);

        if (isset($_POST["cuenta_inhouse"]))
            $personal->setCuentaInhouse($_POST['cuenta_inhouse']);


        if ($_POST['active'])
            $personal->setActive(1);
        else
            $personal->setActive(0);

        if (!$personal->Edit()) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        } else {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $resPersonals = $personal->Enumerate();
            $smarty->assign("personals", $resPersonals);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/personal.tpl');
        }
        break;
    case "showFile":
        $personal->setPersonalId($_POST['personalId']);
        $myPersonal = $personal->Info();
        $expedientes = $personal->GetExpedientes();
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->assign("info", $myPersonal);
        $smarty->assign("expedientes", $expedientes);
        $smarty->display(DOC_ROOT . '/templates/boxes/show-file-personal-popup.tpl');

        break;
    case "changePass":

        if ($personal->changePassword()) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $resPersonals = $personal->Enumerate();
            $smarty->assign("personals", $resPersonals);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/personal.tpl');

        }

        break;
    case "deleteExpediente":
        if ($personal->unlinkExpendiente($_POST['id'], $_POST['personalId'])) {
            echo "ok[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $personal->setPersonalId($_POST['personalId']);
            $myPersonal = $personal->Info();
            $expedientes = $personal->GetExpedientes();

            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->assign("info", $myPersonal);
            $smarty->assign("expedientes", $expedientes);
            $smarty->display(DOC_ROOT . '/templates/forms/show-file-personal.tpl');

        } else {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }
        break;
    case "generateReportExp":
        $personal->setPersonalId($_POST["responsableCuenta"]);
        $results = $personal->GenerateReportExpediente();
        $smarty->assign("results", $results);
        $smarty->display(DOC_ROOT . '/templates/lists/report-exp-employe.tpl');
        break;

}
?>
