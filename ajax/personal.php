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

        if (isset($_POST["numero_celular_institucional"]))
            $personal->setNumeroCelularInstitucional($_POST['numero_celular_institucional']);

        if (isset($_POST["numero_telefonico_webex"]))
            $personal->setNumeroTelefonicoWebex($_POST['numero_telefonico_webex']);

        if (isset($_POST["extension_webex"]))
            $personal->setExtensionWebex($_POST['extension_webex']);

        if (isset($_POST["fecha_promocion"])) {
            $fechaPromocion = $_POST['fecha_promocion'] ? date('Y-m-d', strtotime($_POST['fecha_promocion'])) : '';
            $personal->setFechaPromocion($fechaPromocion);
        }


        if (isset($_POST["numero_celular_institucional"]))
            $personal->setSkype($_POST['numero_celular_institucional']);
        if (isset($_POST["numero_telefono_webex"]))
            $personal->setSkype($_POST['numero_telefono_webex']);
        if (isset($_POST["numero_celular_institucional"]))
            $personal->setSkype($_POST['numero_celular_institucional']);

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

        if (isset($_POST["numero_celular_institucional"]))
            $personal->setNumeroCelularInstitucional($_POST['numero_celular_institucional']);

        if (isset($_POST["numero_telefonico_webex"]))
            $personal->setNumeroTelefonicoWebex($_POST['numero_telefonico_webex']);

        if (isset($_POST["extension_webex"]))
            $personal->setExtensionWebex($_POST['extension_webex']);

        if (isset($_POST["fecha_promocion"])) {
            $fechaPromocion = $_POST['fecha_promocion'] ? date('Y-m-d', strtotime($_POST['fecha_promocion'])) : null;
            $personal->setFechaPromocion($fechaPromocion);
        }

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
    case 'exportarExcel':
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('Listado de empleados');

        $row=1;
        $col = 0;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Nombre')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Empleador')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col,$row, 'Fecha de ingreso')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Telefono celular')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Telefono')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Extension')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Numero celular institucional')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Numero telefonico webex')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Extension webex')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Fecha de Promoción')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Sueldo')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Usuario computadora')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Password computadora')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col,$row, 'Email grupo')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col,$row, 'Lista distribucion')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col,$row, 'Numero maximo de empresa a cargo')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Email')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Password')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Cuenta inhouse')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Sistema aspel')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Usuario aspel')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col, $row, 'Password aspel')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col,$row, 'Tipo de contrato')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col,$row, 'Numero de seguro social')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col,$row, 'Fecha de nacimiento')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(++$col,$row, 'Sexo')
            ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);

        $row++;

        $sql = "select personalId codigo, 
                TRIM(SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name,' ',1))+1, LENGTH(name))) as nombre,
                fechaIngreso,
                celphone,
                phone,
                ext extension,
                numeroCelularInstitucional,
                numeroTelefonicoWebex,
                extensionWebex,
                extensionWebex,
                fechaPromocion,
                sueldo,
                userComputadora,
                passwordComputadora,
                mailGrupo,
                listaDistribucion,
                numberAccountsAllowed,
                email, 
                cuentaInhouse,
                systemAspel,
                userAspel,
                passwordAspel,
                passwd
                from personal
                where active = '1' 
                ORDER BY nombre
        ";

        $db->setQuery($sql);
        $results = $db->GetResult();

        foreach($results as $result) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['nombre']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, 'BRAUN HUERIN CONTADORES PUBLICOS');
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['fechaIngreso']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['celphone']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['phone']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['extension']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['numeroCelularInstitucional']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['numeroTelefonicoWebex']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['extensionWebex']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['fechaPromocion']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['sueldo']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['userComputadora']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['passwordComputadora']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['mailGrupo']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['listaDistribucion']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['numberAccountsAllowed']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['email']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['passwd']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['cuentaInhouse']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['systemAspel']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['userAspel']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $result['passwordAspel']);
            $sheet->setCellValueByColumnAndRow(++$col, $row, 'Contrato de trabajo por tiempo indeterminado');
            $sheet->setCellValueByColumnAndRow(++$col, $row, '');
            $sheet->setCellValueByColumnAndRow(++$col, $row, '');
            $sheet->setCellValueByColumnAndRow(++$col, $row, '');
            $row++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet1->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "Listado de empleados.xlsx";
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
        echo WEB_ROOT . "/download.php?file=" . WEB_ROOT . "/sendFiles/" . $nameFile;

        break;

}
?>
