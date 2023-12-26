<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');
session_start();
//comprobar que se ha seleccionado un archivo y el tipo de importacion
if ($_POST['type'] == "")
    $util->setError(0, "error", 'Debe seleccionar ', 'Tipo');

if ($_FILES['file']['error'] === 4) {
    $util->setError(0, "error", 'No se ha seleccionado un archivo', 'Archivo');
} else {
    $name = $_FILES['file']['name'];
    $ext = end(explode(".", $name));

    if (strtoupper($ext) != "CSV" &&  !in_array($_POST['type'], ['recotizar-servicios','importar-inventario'])) {
        $util->setError(0, "error", 'Verificar extesion, solo se acepta CSV', 'Archivo');
    }
    if (strtoupper($ext) != "XLSX" && in_array($_POST['type'], ['recotizar-servicios','importar-inventario'])) {
        $util->setError(0, "error", 'Verificar extesion, solo se acepta XLSX', 'Archivo');
    }
}
if ($util->PrintErrors()) {
    echo "fail[#]";
    $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
    exit;
}
$opermiso = new Permiso();
$opcion = explode("#", $_POST['type']);
switch ($opcion[0]) {
    case 'update':
        $validates = $valida->validateLayout($_FILES, $opcion[1], "update");
        $db_connection = new DB(false);
        $actualizados = 0;
        $ignorados = 0;

        if (!$validates) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            echo $cad;
            exit;
        }

        foreach ($validates as $key => $var) {
            $changes = [];
            $table = $var['table'];
            $primary_key = $var['primary_key'];
            $value_primary_key = $var['value_primary_key'];
            $before_data = $log->GetCurrentData($table, $primary_key, $value_primary_key);
            $sql = "update $table set ";

            foreach ($var['fields_update'] as $field) {
                $name_field = $field['field'];
                $value_field = $field['value'];
                $sql .= " $name_field = '$value_field',";
            }
            $sql = substr($sql, 0, -1);

            if ($var['table'] === 'contract') {
                $resp_string = "";
                foreach ($var['responsables'] as $resp) {
                    $dep_id = $resp['dep_id'];
                    $personal_id = $resp['personal_id'];
                    $resp_string .= "$dep_id,$personal_id-";
                }
                $resp_string = substr($resp_string, 0, -1);
                if ($resp_string !== "")
                    $sql .= ", permisos = '$resp_string' ";
            }

            $sql .= " where $primary_key = '$value_primary_key' ";
            $db_connection->setQuery($sql);
            $actualizado = $db_connection->UpdateData();
            if ($actualizado > 0) {
                if ($table === "contract") {
                    $permiso->setContractId($value_primary_key);
                    $permiso->doPermiso();
                }
                $after_data = $log->GetCurrentData($table, $primary_key, $value_primary_key);
                $log->setFecha(date('Y-m-d H:i:s'));
                $log->setTabla($table);
                $log->setTablaId($value_primary_key);
                $log->setAction('Update');
                $log->setOldValue(serialize($before_data));
                $log->setNewValue(serialize($after_data));
                $log->SaveOnly();
                $changes = $log->FindOnlyChanges(serialize($before_data), serialize($after_data));
                if (!empty($changes['after'])) {
                    $logContractLocal .= "<p>Cambios realizados del registro con ID $value_primary_key de la tabla $table</p>";
                    $logContractLocal .= $log->PrintInFormatText($changes);
                    $actualizados++;
                } else {
                    $ignorados++;
                }
            } else {
                $ignorados++;
            }
        }

        if ($actualizados > 0)
            $log->sendPdfLogFromHtml($logContractLocal);

        $util->setError(0, "complete", "$actualizados registros actualizados.");
        $util->setError(0, "complete", "$ignorados registros ignorados por no tener modificaciones.");
        $util->PrintErrors();

        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'add':
        $validates = $valida->validateLayout($_FILES, $opcion[1], "add");
        $db_connection = new DB(false);
        $agregados = 0;
        $ignorados = 0;

        if (!$validates) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            echo $cad;
            exit;
        }

        foreach ($validates as $key => $var) {
            $changes = [];
            $table = $var['table'];
            $primary_key = $var['primary_key'];
            $value_primary_key = $var['value_primary_key'];
            $fields = array_column($var['fields_update'], 'field');
            $values = array_column($var['fields_update'], 'value');
            if(count($values) !== count($fields)) {
                $ignorados++;
                continue;
            }

            $fields_string = implode(',', $fields);
            $values_string = "";

            foreach ($values as $value)
                $values_string .= " '$value',";

            $values_string = substr($values_string, 0, -1);

            if ($var['table'] === 'contract') {
                $resp_string = "";
                foreach ($var['responsables'] as $resp) {
                    $dep_id = $resp['dep_id'];
                    $personal_id = $resp['personal_id'];
                    $resp_string .= "$dep_id,$personal_id-";
                }
                $resp_string = substr($resp_string, 0, -1);
                if ($resp_string !== "") {
                    $fields_string .= ", permisos";
                    $values_string .= ",'$resp_string' ";
                }

            }
            $sql = "insert into $table ($fields_string) VALUES ($values_string)";
            $db_connection->setQuery($sql);
            $id = $db_connection->InsertData();

            if ($id > 0) {
                if ($table === "contract") {
                    $permiso->setContractId($id);
                    $permiso->doPermiso();
                }
                $primary_key = $table . "Id";
                $after_data = $log->GetCurrentData($table, $primary_key, $id);
                $log->setFecha(date('Y-m-d H:i:s'));
                $log->setTabla($table);
                $log->setTablaId($id);
                $log->setAction('Insert');
                $log->setNewValue(serialize($after_data));
                $log->SaveOnly();
                $changes = $log->FindFieldDetail(serialize($after_data));
                if (!empty($changes)) {
                    $logContractLocal .= "<p>Se ha realizado el alta de registros en la tabla $table</p>";
                    $logContractLocal .= $log->PrintInFormatText($changes, 'simple');
                    $agregados++;
                }
            } else {
                $ignorados++;
            }
        }

        if ($agregados > 0)
            $log->sendPdfLogFromHtml($logContractLocal);

        $util->setError(0, "complete", "$agregados registros nuevos agregados.");
        $util->setError(0, "complete", "$ignorados registros ignorados.");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'update_comercial_activity':
        $db_connection = new DB(false);
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila = 1;
        $insertados = 0;
        $ignorados = 0;
        while(($row = fgetcsv($fp, 4096, ',' )) == true) {
            if($fila === 1) {
                $fila++;
                continue;
            }
            $sector_name = $row[0];
            $subsector_name = $row[1];
            $actividad_name =  $row[2];

            $db_connection->setQuery("select id from sector where name = '$sector_name' ");
            $row = $db_connection->GetRow();
            if(!$row) {
                $db_connection->setQuery("insert into sector(name) values ('$sector_name') ");
                $sector_id = $db_connection->InsertData();
            } else {
                $sector_id = $row['id'];
            }

            $db_connection->setQuery("select id from subsector where name = '$subsector_name' ");
            $row = $db_connection->GetRow();
            if(!$row) {
                $db_connection->setQuery("insert into subsector(name, sector_id) values ('$subsector_name', $sector_id) ");
                $subsector_id = $db_connection->InsertData();
            } else {
                $subsector_id = $row['id'];
            }

            $db_connection->setQuery("select id from actividad_comercial where name = '$actividad_name' ");
            $row = $db_connection->GetRow();
            if(!$row) {
                $db_connection->setQuery("insert into actividad_comercial(name, subsector_id) values ('$actividad_name', $subsector_id) ");
                $db_connection->InsertData();
                $insertados++;
            } else {
                $ignorados++;
            }
            $fila++;
        }
        $util->setError(0, "complete", "$insertados registros nuevos guardados.");
        $util->setError(0, "complete", "$ignorados registros ignorados.");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
    break;
    case 'update-only-encargado':
        $isValid = $valida->ValidateLayoutOnlyEncargado($_FILES);
        if (!$isValid) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            echo $cad;
            exit;
        }
        $contActualizado = 0;
        $contNoActualizado = 0;
        $contratoNoEncontrado = 0;
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        $fila = 1;
        $idsCustomer = array();
        $generalLog = "";
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            $logLocal = "";
            $permisos = "";
            //comprobar permisos
            if ($fila == 1) {
                $fila++;
                continue;
            }
            $contract->setContractId($row[1]);
            $encargados = array();
            $encargados = array($row[1], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10]);
            $permisos = $contract->ValidateEncargados($encargados);
            if ($permisos === false) {
                $contratoNoEncontrado++;
                echo $contratosIngorados .= "La razon social con ID=" . $row[1] . " de la fila " . $fila . " no se encuentra registrada";
                $fila++;
                continue;
            }
            $db->setQuery('SELECT permisos from contract WHERE contractId="' . $row[1] . '" ');
            $permisosActual = $db->GetRow();

            if (trim($permisosActual['permisos']) != trim($permisos)) {
                $changes = array();
                $contract->setContractId($row[1]);
                $beforeContract = $contract->Info();
                $strContract = "UPDATE contract SET permisos='" . $permisos . "' WHERE contractId='" . $row[1] . "' ";
                $db->setQuery($strContract);
                $upContract = $db->UpdateData();
                if ($upContract > 0) {
                    //si se actualizo la razon se debe actualizar los permisos en la tabla contractPermiso
                    $opermiso->setContractId($row[1]);
                    $opermiso->doPermiso();
                    $contract->setContractId($row[1]);
                    $afterContract = $contract->Info();
                    //guardar en log
                    $log->setPersonalId($_SESSION['User']['userId']);
                    $log->setFecha(date('Y-m-d H:i:s'));
                    $log->setTabla('contract');
                    $log->setTablaId($row[1]);
                    $log->setAction('Update');
                    $log->setOldValue(serialize($beforeContract));
                    $log->setNewValue(serialize($afterContract));
                    $log->SaveOnly();
                    $changes = $log->FindOnlyChanges(serialize($beforeContract), serialize($afterContract));
                    if (!empty($changes['after'])) {
                        $logLocal = "<p>La razon social " . $beforeContract['name'] . " del cliente " . $beforeContract['nameContact'] . " ha sido modificado</p>";
                        $logLocal .= $log->PrintInFormatText($changes);
                    }
                    $contActualizado++;
                }
            } else {
                $contNoActualizado++;
            }
            $generalLog .= $logLocal;
            $fila++;
        }
        fclose($fp);
        $file2 = "";
        $nameFile2 = "";
        if ($generalLog != "") {
            $nameFile2 = "BITACORA-CAMBIO-DE-ENCARGADOS.html";
            $file2 = DOC_ROOT . "/sendFiles/" . $nameFile2;
            $open = fopen($file2, "w");
            if ($open) {
                fwrite($open, $generalLog);
                fclose($open);
            }
        }
        $subject = 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $db->setQuery('SELECT name FROM personal WHERE personalId="' . $_SESSION['User']['userId'] . '" ');
        $who = $db->GetSingle();
        if ($_SESSION['User']['tipoPers'] == 'Admin')
            $who = "Administrador de sistema(desarrollador)";

        $body = "<p>Se han realizado cambios en los encargados de area en algunos contratos por el colaborador " . $who . ". </p>";
        $body .= "<p>Adjunto a este correo encontrara un archivo que detalla los cambios realizados, favor de descargar el documento y abrir en su navegador predeterminado. </p>";
        $encargadosEmail = array();
        $sendmail = new SendMail();
        if ($generalLog != "")
            $sendmail->PrepareMultipleNotice($subject, $body, $encargadosEmail, "", '', '', $file2, $nameFile2, 'sistema@braunhuerin.com.mx', 'Administrador de plataforma', true);
        if (is_file($file2))
            unlink($file2);
        $util->setError(0, 'complete', $contActualizado . " registros actualizados");
        $util->setError(0, 'complete', $contNoActualizado . " registros no actualizados por tener informacion correcta");
        $util->setError(0, 'complete', $contratoNoEncontrado . " registros no encontrados en el sistema");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');


        break;
    case 'cancelar-uuid':
        $logCancel = "";
        $fila = 1;
        $user1 = USER_PAC;
        $pw1 = PW_PAC;
        $pac = new Pac;
        $rfcObj = new Rfc();

        //get password
        $root = DOC_ROOT . "/empresas/21/certificados/30/password.txt";
        $fh = fopen($root, 'r');
        $password = fread($fh, filesize($root));
        fclose($fh);

        $path = DOC_ROOT . "/empresas/21/certificados/30/00001000000402946663.cer.pfx";
        if (!$password) {
            $util->setError('', "error", "Tienes que actualizar tu certificado para que podamos obtener el password");
            $util->PrintErrors();
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            exit;
        }
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            if ($fila == 1) {
                $fila++;
                continue;
            }
            //cancelar los cfdi
            $rfcObj->setRfcId(30);
            $nodoEmisorRfc = $rfcObj->InfoRfc();
            $response = $pac->CancelaCfdi($user1, $pw1, $nodoEmisorRfc["rfc"], $row[0], $path, $password);
            if (!$response["cancelaCFDiReturn"]["text"]) {
                $logCancel .= "Hubo un problema al cancelar la factura con FOLIO FISCAL" . $row[0] . ". Por favor permite que pasen al menos 24 horas antes de intentar de nuevo.<br>";
                $logCancel .= 'response fail : ' . print_r($response["cancelaCFDiReturn"]);
            } else {
                $logCancel .= " FOLIO FISCAL" . $row[0] . ". cancelado correctamente.<br>";
                $logCancel .= 'response : ' . print_r($response["cancelaCFDiReturn"]);
            }


            //cancelar uno ala vez
            break;
        }
        echo "ok[#]";
        echo $logCancel;
        break;
    case 'doPermiso':
        $opermiso->doPermisos(true);
        $util->setError(0, "complete", "Permisos actualizados.");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'killnotuse':

        $db->setQuery('select contractId from contract where activo="Si"  ');
        $result = $db->GetResult();

        foreach ($result as $key => $value) {
            $emailAdmin = "contactoadmin" . $key . "@aristasoluciones.com";
            $emailConta = "contactoconta" . $key . "@aristasoluciones.com";
            $emailDir = "contactodir" . $key . "@aristasoluciones.com";


            /*$db->setQuery("UPDATE contract SET emailContactoAdministrativo='".$emailAdmin."',
                                  emailContactoContabilidad='".$emailConta."',
                                  emailContactoDirectivo='".$emailDir."' WHERE contractId='".$value['contractId']."' ");
            $db->UpdateData();*/
        }
        break;
    case 'importar_empleados':
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            $sqlRow = "";
            //comprobar que el cliente exista
            $sqlc = "SELECT personalId FROM personal where personalId='" . $row[0] . "' ";
            $db->setQuery($sqlc);
            $personalId = $db->GetSingle();

            //encontrar departamento
            $sqldep = "SELECT departamentoId,departamento FROM departamentos where trim(departamento)='" . trim($row[11]) . "' ";
            $db->setQuery($sqldep);
            $row_dep = $db->GetRow();

            //encontrar rol
            $sqlrol = "SELECT rolId,name FROM roles where trim(name)='" . trim($row[10]) . "' ";
            $db->setQuery($sqlrol);
            $row_rol = $db->GetRow();

            //comprobar que el cliente exista
            if (!$personalId) {

                $sqlrazon = "insert into personal(personalId,name,celphone,email,skype,username,passwd,ext,celphone,puesto,computadora,tipoPersonal,roleId) values
                              (
                               '" . $row[0] . "',
                               '" . $row[1] . "',
                               '" . $row[3] . "',
                               '" . $row[4] . "',
                               '" . $row[0] . "'                               
                              ) 
                              ";
            }
            //comprobar por rfc y nombre la razon social si no se encuentra registrada
            $sqlrazon = "SELECT contractId FROM contract WHERE name='" . $row[1] . "' OR rfc='" . $row[2] . "' ";
            $db->setQuery($sqlrazon);
            $contractId = $db->GetSingle();
        }
        break;
    case 'importar_customer_rebuild':
        $clientes_control = [];
        $sqlLog = "";
        $alta = 0;
        $update = 0;
        $fila = 1;
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            if ($fila == 1) {
                $fila++;
                continue;
            }
            if (in_array($row[0], $clientes_control)) {
                $fila++;
                continue;
            }

            $sqlRow = "";
            //comprobar que el cliente exista
            $sqlc = "SELECT customerId FROM customer where customerId='" . $row[0] . "' ";
            $db->setQuery($sqlc);
            $customerId = $db->GetSingle();

            //comprobar que el cliente exista
            if ($customerId <= 0) {
                $insertInto = "insert into customer(customerId,name,phone,email,password,nameContact,fechaAlta,observacion,active) values
                              (
                               '" . $row[0] . "',
                               '" . $row[2] . "',
                               '" . $row[3] . "',
                               '" . $row[4] . "',
                               '" . $row[5] . "',
                               '" . $row[2] . "',
                               '" . $util->FormatDateMySqlSlash($row[7]) . "',
                               '" . $row[8] . "',
                               '1'                             
                              ) 
                              ";
                $db->setQuery($insertInto);
                $custId = $db->InsertData();
                array_push($clientes_control, $row[0]);
                $sqlLog .= "alta:: ," . $custId . "[" . $row[0] . "]" . chr(10) . chr(13);
                $alta++;
            } else {
                $updateSql = "update customer set
                               name='" . $row[2] . "',
                               phone='" . $row[3] . "',
                               email='" . $row[4] . "',
                               password='" . $row[5] . "',
                               nameContact='" . $row[2] . "',
                               fechaAlta='" . $util->FormatDateMySqlSlash($row[7]) . "',
                               observacion='" . $row[8] . "'
                               where customerId='" . $row[0] . "'
                              ";
                $db->setQuery($updateSql);
                $db->UpdateData();
                array_push($clientes_control, $row[0]);
                $sqlLog .= "update:: ," . $row[0] . chr(10) . chr(13);
                $update++;
            }
            $fila++;

        }
        echo $sqlLog;
        echo "total alta " . $alta . chr(10) . chr(13);
        echo "total update" . $update . chr(10) . chr(13);
        break;
    case 'importar_contrato_rebuild':
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        $cad = "";
        $isValid = $valida->ValidateLayoutContractRebuild($_FILES);
        if (!$isValid) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            echo $cad;
            exit;
        }
        $fila = 1;
        $log = "";
        $alta = 0;
        $up = 0;
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            if ($fila == 1) {
                $fila++;
                continue;
            }
            //comprobar si el contrato esta registrado sobre el cliente, si esta actualizar si no dar de alta.
            $sql1 = "SELECT contractId,customerId FROM contract where customerId='" . $row[0] . "' and contractId='" . $row[1] . "' ";
            $util->DB()->setQuery($sql1);
            $findData = $util->DB()->GetRow();
            //construir permisos
            $permisos = $contract->ConcatenarEncargadosRebuild([$row[38], $row[39], $row[40], $row[41], $row[42], $row[44]]);
            if (empty($findData)) {
                $db->setQuery("SELECT regimenId FROM  regimen WHERE lower(replace(nombreRegimen,' ',''))='" . strtolower(str_replace(' ', '', $row[14])) . "' and lower(replace(tipoDePersona,' ',''))='" . strtolower(str_replace(' ', '', $row[12])) . "' ");
                $regimenId = $db->GetSingle();
                $sqlInsert = "INSERT INTO contract(
                          contractId,
                          customerId,
                          type,
                          regimenId,
                          name,
                          nombreComercial,
                          direccionComercial,
                          nameContactoAdministrativo,
                          emailContactoAdministrativo,
                          telefonoContactoAdministrativo,
                          nameContactoContabilidad,
                          emailContactoContabilidad,
                          telefonoContactoContabilidad,
                          nameContactoDirectivo,
                          emailContactoDirectivo,
                          telefonoContactoDirectivo,
                          telefonoCelularDirectivo,
                          claveCiec,
                          claveFiel,
                          claveIdse,
                          claveIsn,
                          rfc,
                          facturador,
                          metodoDePago,
                          noCuenta,
                          activo,
                          permisos)
                          VALUES(
                          '" . $row[1] . "',
                          '" . $row[0] . "',
                          '" . $row[12] . "',
                          '" . $regimenId . "',
                          '" . $row[10] . "',
                          '" . $row[16] . "',
                          '" . str_replace("'", "", $row[17]) . "',
                          '" . str_replace("'", "", $row[19]) . "',
                          '" . str_replace("'", "", $row[20]) . "',
                          '" . str_replace("'", "", $row[21]) . "',
                          '" . str_replace("'", "", $row[22]) . "',
                          '" . str_replace("'", "", $row[23]) . "',
                          '" . str_replace("'", "", $row[24]) . "',
                          '" . str_replace("'", "", $row[25]) . "',
                          '" . str_replace("'", "", $row[26]) . "',
                          '" . $row[27] . "',
                          '" . $row[28] . "',
                          '" . $row[29] . "',
                          '" . ($row[30]) . "',
                          '" . $row[31] . "',
                          '" . $row[32] . "',
                          '" . ($row[13]) . "',
                          '" . $row[33] . "',
                          '" . $row[34] . "',
                          '" . $row[35] . "',
                          '" . $row[15] . "',
                          '" . $permisos . "'
                          )";
                $db->setQuery($sqlInsert);
                $registroId = $db->InsertData();
                if ($registroId > 0) {
                    $log .= "in customerId, " . $row[0] . ", contract: " . $registroId . " : [" . $row[1] . "]" . chr(13);
                    //si se actualizo la razon se debe actualizar los permisos en la tabla
                    $opermiso->setContractId($registroId);
                    $opermiso->doPermiso();
                    $alta++;
                }
            } else {
                $db->setQuery("SELECT regimenId FROM  regimen WHERE lower(replace(nombreRegimen,' ',''))='" . strtolower(str_replace(' ', '', $row[14])) . "' and lower(replace(tipoDePersona,' ',''))='" . strtolower(str_replace(' ', '', $row[12])) . "' ");
                $regimenId = $db->GetSingle();
                $strContract = "UPDATE contract SET 
                            permisos='" . $permisos . "',
                            type='" . $row[12] . "',
                            regimenId='" . $regimenId . "',
                            name='" . $row[10] . "',
                            nombreComercial='" . $row[16] . "',
                            direccionComercial='" . $row[17] . "',
                            nameContactoAdministrativo='" . str_replace("'", "", $row[19]) . "',
                            emailContactoAdministrativo='" . str_replace("'", "", $row[20]) . "',
                            telefonoContactoAdministrativo='" . str_replace("'", "", $row[21]) . "',
                            nameContactoContabilidad='" . str_replace("'", "", $row[22]) . "',
                            emailContactoContabilidad='" . str_replace("'", "", $row[23]) . "',
                            telefonoContactoContabilidad='" . str_replace("'", "", $row[24]) . "',
                            nameContactoDirectivo='" . str_replace("'", "", $row[25]) . "',
                            emailContactoDirectivo='" . str_replace("'", "", $row[26]) . "',
                            telefonoContactoDirectivo='" . str_replace("'", "", $row[27]) . "',
                            telefonoCelularDirectivo='" . str_replace("'", "", $row[28]) . "',
                            claveCiec='" . $row[29] . "',
                            claveFiel='" . $row[30] . "',
                            claveIdse='" . $row[31] . "',
                            claveIsn='" . $row[32] . "',
                            rfc='" . $row[13] . "',
                            facturador='" . $row[33] . "',
                            metodoDePago='" . $row[34] . "',
                            noCuenta='" . $row[35] . "',
                            activo='" . $row[15] . "'
                            WHERE contractId='" . $row[1] . "' ";
                $db->setQuery($strContract);
                $upContract = $db->UpdateData();
                $log .= "up customerId, " . $row[0] . ", contract," . $row[1] . chr(13);
                //si se actualizo la razon se debe actualizar los permisos en la tabla
                $opermiso->setContractId($row[1]);
                $opermiso->doPermiso();
                // echo "si exist ".$permisos.chr(10).chr(13);
                $up++;
            }
        }
        $util->setError(0, 'complete', "Total altas " . $alta);
        $util->setError(0, 'complete', "Total actualizadas " . $up);
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        echo $log;
        break;
    case 'importar_empleados_rebuild':
        exit;
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        $find = 0;
        $nofind = 0;
        $fila = 1;
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            $sql = "select personalId from personal where personalId='" . $row[0] . "' ";
            $db->setQuery($sql);
            $isFind = $db->GetSingle();
            $sqlDep = "select departamento from departamentos where departamento='" . $row[11] . "' ";
            $db->setQuery($sqlDep);
            $depId = $db->GetSingle();
            if ($isFind) {
                $inserPer = "insert into personal(personalId,name,celphone,email,skype,computadora,aspel,username,passwd,fechaIngreso,departamentoId,puesto)
                              VALUES(
                              '" . $row[0] . "',
                              '" . $row[1] . "',
                              '" . $row[2] . "',
                              '" . $row[3] . "',
                              '" . $row[4] . "',
                              '" . $row[5] . "',
                              '" . $row[6] . "',
                              '" . $row[7] . "',
                              '" . $row[8] . "',
                              '" . $row[9] . "',
                              '" . $depId . "',
                              '" . $row[12] . "'
                              )";

                $find++;
            } else {
                $nofind++;
            }
        }
        echo "encontrados " . $find . chr(13);
        echo "no encontrados " . $nofind . chr(13);
        break;
    case 'importar_servicios_rebuild':
        $isValid = $valida->ValidateLayoutImportServicio($_FILES);
        if (!$isValid) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            exit;
        }
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        $fila = 1;
        $encontrados = 0;
        $noencontrados = 0;
        $masdeuno = 0;
        $losdeuno = 0;
        $daralta = 0;
        $sinInicioOp = 0;
        $mas_de_uno = [];
        $servicios_ids = [];
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            if ($fila == 1) {
                $fila++;
                continue;
            }
            if ($row[5] == "0000-00-00") {
                $sinInicioOp++;
                $fila++;
                continue;
            }

            //encontrar el contrato
            //$sql= "select max(contractId) from contract where lower(replace(name,' ',''))='".mb_strtolower(str_replace(' ','',utf8_encode($row[1])))."' ";
            $sql = "select max(a.contractId) from contract a 
                  inner join customer b on a.customerId=b.customerId and lower(replace(b.nameContact,' ',''))='" . mb_strtolower(str_replace(' ', '', utf8_encode($row[0]))) . "' and b.active='1' 
                  where lower(replace(a.name,' ',''))='" . mb_strtolower(str_replace(' ', '', utf8_encode($row[1]))) . "' and a.activo='Si'
                  ";
            $db->setQuery($sql);
            $conId = $db->GetSingle();

            //encontrar el servicio
            $sql2 = "select tipoServicioId from tipoServicio where lower(replace(nombreServicio,' ',''))='" . mb_strtolower(str_replace(' ', '', utf8_encode($row[2]))) . "' ";
            $db->setQuery($sql2);
            $tipoServicioId = $db->GetSingle();

            if ($row[4] == '0000-00-00')
                $fechaFacturacion = $row[4];
            else
                $fechaFacturacion = $util->FormatDateMySqlSlash($row[4]);

            $fechaInicioOperacion = $util->FormatDateMySqlSlash($row[5]);
            $inOp = explode("-", $fechaInicioOperacion);

            //si el contractId y tipoServicioId estan dados de alta se debe comprobar que no exista un registro en la tabla servicio
            $sqlServ = "SELECT servicioId from servicio where contractId='" . $conId . "' and tipoServicioId='" . $tipoServicioId . "' and year(inicioOperaciones)='" . $inOp[0] . "' and month(inicioOperaciones)='" . $inOp[1] . "' ";
            $db->setQuery($sqlServ);
            $servicesFind = $db->GetResult();
            /*if($conId==2429 || $row[1]=="EDNA CHOMSTEIN SZTAJER" || $fila==613){
                echo $sqlServ.chr(13);
                echo $sql.chr(13);
            }*/
            if (count($servicesFind) > 1) {
                //si tiene mas de uno el mismo tipo de servicio actualizar el mas reciente.
                $sqlmax = "SELECT max(servicioId) from servicio where contractId='" . $conId . "' and tipoServicioId='" . $tipoServicioId . "' and year(inicioOperaciones)='" . $inOp[0] . "' and month(inicioOperaciones)='" . $inOp[1] . "' ";
                $db->setQuery($sqlmax);
                $maxId = $db->GetSingle();
                if ($maxId > 0) {
                    $sqlUpMax = "update servicio set inicioOperaciones='" . $util->FormatDateMySqlSlash($row[5]) . "',inicioFactura='" . $fechaFacturacion . "',costo='" . $row[7] . "',status='activo' where servicioId='" . $maxId . "' ";
                    $db->setQuery($sqlUpMax);
                    $db->UpdateData();
                    array_push($servicios_ids, $maxId);
                }
                $mas_de_uno[$conId][] = $row[2];
                $masdeuno++;
            } elseif (count($servicesFind) == 1) {
                $sqlRow = "SELECT servicioId from servicio where contractId='" . $conId . "' and tipoServicioId='" . $tipoServicioId . "' and year(inicioOperaciones)='" . $inOp[0] . "' and month(inicioOperaciones)='" . $inOp[1] . "'";
                $db->setQuery($sqlRow);
                $serviceId = $db->GetSingle();
                if ($serviceId > 0) {
                    $sqlUp = "update servicio set inicioOperaciones='" . $util->FormatDateMySqlSlash($row[5]) . "',inicioFactura='" . $fechaFacturacion . "',costo='" . $row[7] . "',status='activo' where servicioId='" . $serviceId . "' ";
                    $db->setQuery($sqlUp);
                    $db->UpdateData();
                    array_push($servicios_ids, $serviceId);
                    $losdeuno++;
                }
            } else {
                // no existe hay que dar de alta
                $sqlInser = "insert into 
                              servicio(
                              contractId,
                              tipoServicioId,
                              inicioOperaciones,
                              inicioFactura,
                              costo
                              )values(
                              '" . $conId . "',
                              '" . $tipoServicioId . "',
                              '" . $util->FormatDateMySqlSlash($row[5]) . "',
                              '" . $fechaFacturacion . "',
                              '" . $row[7] . "'
                              )";
                $db->setQuery($sqlInser);
                $id = $db->InsertData();
                array_push($servicios_ids, $id);
                $daralta++;
            }
            $fila++;
        }
        echo "servicios " . count($servicios_ids) . chr(13);
        //todos los servicios que no esten dentro de $servicios_ids, se deben de dar de baja, es mejor para que no se creeen workflow esto ya se puede
        //activar en caso de necesitarlo.
        $sqlBaja = " update servicio set status='baja'  where servicioId not in(" . implode(',', $servicios_ids) . ")";
        $db->setQuery($sqlBaja);
        $db->UpdateData();

        $util->setError(0, 'complete', "sin inicio de operaciones " . $sinInicioOp);
        $util->setError(0, 'complete', "encontrados " . $encontrados . " , no encontrados " . $noencontrados);
        $util->setError(0, 'complete', "Con mas de uno " . $masdeuno . " ,de a uno " . $losdeuno . ", a dar de alta " . $daralta);
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'importar_servicios_nominas':
        $isValid = $valida->ValidateLayoutImportServicio($_FILES);
        if (!$isValid) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            exit;
        }
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        $fila = 1;
        $daralta = 0;
        $actualizados = 0;
        $altas_log = "";
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            if ($fila == 1) {
                $fila++;
                continue;
            }
            //encontrar el contrato
            //$sql= "select max(contractId) from contract where lower(replace(name,' ',''))='".mb_strtolower(str_replace(' ','',utf8_encode($row[1])))."' ";
            $sql = "select max(a.contractId) from contract a 
                  inner join customer b on a.customerId=b.customerId and lower(replace(b.nameContact,' ',''))='" . mb_strtolower(str_replace(' ', '', utf8_encode($row[0]))) . "' and b.active='1' 
                  where lower(replace(a.name,' ',''))='" . mb_strtolower(str_replace(' ', '', utf8_encode($row[1]))) . "' and a.activo='Si'
                  ";
            $db->setQuery($sql);
            $conId = $db->GetSingle();

            //encontrar el servicio
            $sql2 = "select tipoServicioId from tipoServicio where lower(replace(nombreServicio,' ',''))='" . mb_strtolower(str_replace(' ', '', utf8_encode($row[2]))) . "' ";
            $db->setQuery($sql2);
            $tipoServicioId = $db->GetSingle();

            if ($row[4] == '0000-00-00')
                $fechaFacturacion = $row[4];
            else
                $fechaFacturacion = $util->FormatDateMySqlSlash($row[4]);

            if ($row[5] == '0000-00-00')
                $fechaInicioOperacion = $row[5];
            else
                $fechaInicioOperacion = $util->FormatDateMySqlSlash($row[5]);

            //encontrar el servicio mas actual, sobre ello actualizar fecha de facturacion y costo.
            $sqlmax = "SELECT max(servicioId) from servicio where contractId='" . $conId . "' and tipoServicioId='" . $tipoServicioId . "' ";
            $db->setQuery($sqlmax);
            $maxId = $db->GetSingle();
            if ($maxId > 0) {
                $sqlUpMax = "update servicio set inicioFactura='" . $fechaFacturacion . "',costo='" . $row[7] . "',status='activo' where servicioId='" . $maxId . "' ";
                $db->setQuery($sqlUpMax);
                $db->UpdateData();
                $actualizados++;
            } else {
                // no existe hay que dar de alta
                $sqlInser = "insert into 
                              servicio(
                              contractId,
                              tipoServicioId,
                              inicioOperaciones,
                              inicioFactura,
                              costo
                              )values(
                              '" . $conId . "',
                              '" . $tipoServicioId . "',
                              '" . $fechaInicioOperacion . "',
                              '" . $fechaFacturacion . "',
                              '" . $row[7] . "'
                              )";
                $db->setQuery($sqlInser);
                $id = $db->InsertData();
                $daralta++;
                $altas_log .= $row[0] . "," . $row[1] . "," . $row[2] . "," . $row[4] . "," . $row[5] . chr(13);
            }
            $fila++;
        }
        echo $altas_log;
        $util->setError(0, 'complete', "Total actualizados " . $actualizados);
        $util->setError(0, 'complete', "Total altas " . $daralta);
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'importar_servicios_nuevos':
        //primero se valida el layaout
        $isValid = $valida->ValidateLayoutImportServicio($_FILES, true);
        if (!$isValid) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            exit;
        }
        //si paso la validacion ya solo basta insertar los registros
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        $fila = 1;
        $nuevos = 0;
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            if ($fila == 1) {
                $fila++;
                continue;
            }
            //encontrar el id de la razon ya se ha validado pero se vuelve obtener, lo mismo pasa con el id del servicio
            $sql = "select max(a.contractId) from contract a 
                  inner join customer b on a.customerId=b.customerId and replace(trim(lower(replace(replace(b.nameContact,' ',''), '&amp;', '&'))), char(9), '')='" . mb_strtolower(str_replace(' ', '', $row[0])) . "' and b.active='1' 
                  where replace(trim(lower(replace(replace(a.name,' ',''), '&amp;', '&'))), char(9), '')='" . mb_strtolower(str_replace(' ', '', $row[1])) . "' and a.activo='Si'
                  ";
            $db->setQuery($sql);
            $conId = $db->GetSingle();
            //encontrar el servicio
            $sql2 = "select tipoServicioId from tipoServicio where replace(trim(lower(replace(replace(nombreServicio,' ',''), '&amp;', '&'))), char(9), '')='" . mb_strtolower(str_replace(' ', '', $row[2])) . "' ";
            $db->setQuery($sql2);
            $tipoServicioId = $db->GetSingle();

            $fechaFacturacion = $row[3] == '0000-00-00' ? $row[3] : $util->FormatDateMySqlSlash($row[3]);
            $fechaInicioOperacion = $row[4] == '0000-00-00' ? $row[4] : $util->FormatDateMySqlSlash($row[4]);

            $sqlInser = "insert into 
                              servicio(
                              contractId,
                              tipoServicioId,
                              inicioOperaciones,
                              inicioFactura,
                              costo,
                              status
                              )values(
                              '" . $conId . "',
                              '" . $tipoServicioId . "',
                              '" . $fechaInicioOperacion . "',
                              '" . $fechaFacturacion . "',
                              '" . $row[5] . "',
                              'activo'
                              )";
            $db->setQuery($sqlInser);
            $lastId = $db->InsertData();
            if ($lastId) {
                //encontrar quien lo esta guardando
                $db->setQuery('SELECT name FROM personal WHERE personalId="' . $_SESSION['User']['userId'] . '" ');
                $who = $db->GetSingle();
                if ($_SESSION['User']['tipoPers'] == 'Admin')
                    $who = "Administrador de sistema(desarrollador)";
                //guardar el log del registro
                $log->saveHistoryChangesServicios($lastId, $fechaFacturacion, 'activo', $row[5], $_SESSION['User']['userId'], $fechaInicioOperacion, $who);
                $nuevos++;
            }
            $fila++;
        }
        $util->setError(0, 'complete', "Se registraron " . $nuevos . " nuevos servicios correctamente.");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'update_direccion_fiscal':
        $isValid = $valida->ValidateLayoutUpdateDireccionFiscal($_FILES);
        if (!$isValid) {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            exit;
        }
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        $fila = 1;
        $actualizados = 0;
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            if ($fila == 1) {
                $fila++;
                continue;
            }
            $dir_explode = explode("|", $row[2]);
            $dir_explode[6] = trim($dir_explode[6]) == "" ? "MEXICO" : trim($dir_explode[6]);
            $updateSql = "update contract set
                               address='" . trim($dir_explode[0]) . "',
                               noExtAddress='" . trim($dir_explode[1]) . "',
                               noIntAddress='" . trim($dir_explode[2]) . "',
                               coloniaAddress='" . trim($dir_explode[3]) . "',
                               municipioAddress='" . trim($dir_explode[4]) . "',
                               estadoAddress='" . trim($dir_explode[5]) . "',
                               paisAddress='" . trim($dir_explode[6]) . "',
                               cpAddress='" . trim($dir_explode[7]) . "'
                               where contractId='" . $row[1] . "'
                              ";
            $db->setQuery($updateSql);
            $db->UpdateData();
            $actualizados++;
        }
        $util->setError(0, 'complete', "Se actualizo " . $actualizados . " registros correctamente.");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'update_extensiones_tasks':
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        while (($row = fgetcsv($fp, 4096, ",")) == true) {
            if (count($row) != 9)
                continue;
            $updateSql = "update task set
                               extensiones ='" . trim($row[8]) . "'
                               where taskId='" . $row[0] . "'
                              ";
            $db->setQuery($updateSql);
            $db->UpdateData();
        }
        break;
    /*Actualizar servicios existente desde layout descargable que solo toma en cuenta servicios activos o bajas temporales
     *Actualiza costo,inicio de operaciones, inicio factura,status,fecha ultimo workflow etc.
     *Envia un reporte siempre y cuando se realizaron cambios.
     */
    case 'update-servicios':
        $file_temp = $_FILES['file']['tmp_name'];
        $jsonParam =  $util->csvToJson($file_temp, [], ['nombre_empresa']);
        $pUsuario = $_SESSION['User']['name'];
        $store =  "call sp_actualizar_servicio('".$jsonParam."', '".$pUsuario."', @pData)";
        $db->setQuery($store);
        $res = 0;
        if($res = $db->ExcuteConsulta()) {
            $db->setQuery('select @pData');
            $data= $db->GetSingle();
            $data_explode = explode('|', $data);
            $util->setError(0, 'complete', "Se han actualizado " . $data_explode[1] . " registros correctamente.");
        } else {
            $util->setError(0, 'error','Error al actualizar registros');
        }
        echo $res ? 'ok[#]' : 'fail[#]';
        $util->PrintErrors();
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'recotizar-servicios':
        include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
        $archivo = $_FILES['file']['tmp_name'];
        $inputFileType = PHPExcel_IOFactory::identify($archivo);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($archivo);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1');
        $keys = [];
        $keysExcludes = ['razon_social','cliente'];
        $indexExclude = [];
        foreach($headers[0] as $kh => $header) {
            $header =  str_replace(' ', '_', $header);
            $header =  str_replace('%', 'porcentaje', $header);
            $header =  $util->cleanString($header);
            $header =  strtolower($header);
            if(in_array($header, $keysExcludes)) {
               array_push($indexExclude, $kh);
               continue;
            }
            array_push($keys, $header);
        }
        $jsonData = [];
        for ($row = 2; $row <= $highestRow; $row++){
            $currentRows = $sheet->rangeToArray('A'.$row. ":" . $sheet->getHighestColumn() . $row);
            foreach ($indexExclude as $kex)
                unset($currentRows[0][$kex]);
            if($currentRows[0][1] <= 0)
                continue;
            $jsonData[] = array_combine($keys, $currentRows[0]);
        }
        $jsonParam = json_encode($jsonData,JSON_UNESCAPED_UNICODE);
        $pUsuario = $_SESSION['User']['name'];
        $store =  "call sp_actualizar_recotizacion_servicio('".$jsonParam."', '".$_SESSION['User']['userId']."', '".$pUsuario."', @pData)";
        $db->setQuery($store);
        $res = 0;
        if($res = $db->ExcuteConsulta()) {
            $db->setQuery('select @pData');
            $data= $db->GetSingle();
            $data_explode = explode('|', $data);

            if($data_explode[0] == 'ERROR') {
                $res = 0;
                $util->setError(0, 'error', "Error en SP$data_explode[1] al actualizar.");
            }
            else {

                $mensaje = $data_explode[1] > 0
                    ? "Proceso completado: $data_explode[1] registros actualizados correctamente."
                    : "Proceso completado: no se han modificado registros por tener información correcta.";
                $util->setError(0, 'complete', $mensaje);
            }
        } else {
            $util->setError(0, 'error','Error al actualizar registros');
        }
        echo $res ? 'ok[#]' : 'fail[#]';
        $util->PrintErrors();
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
    case 'importar-inventario':
        include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
        $archivo = $_FILES['file']['tmp_name'];
        $inputFileType = PHPExcel_IOFactory::identify($archivo);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($archivo);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1');
        $keys = [];
        $keysExcludes = [];
        $indexExclude = [];
        foreach($headers[0] as $kh => $header) {
            $header =  str_replace(' ', '_', $header);
            $header =  str_replace('%', 'porcentaje', $header);
            $header =  strtolower($header);
            if(in_array($header, $keysExcludes)) {
                array_push($indexExclude, $kh);
                continue;
            }
            array_push($keys, $header);
        }

        $jsonData = [];
        for ($row = 2; $row <= $highestRow; $row++){
            $currentRows = $sheet->rangeToArray('A'.$row. ":" . $sheet->getHighestColumn() . $row);
            foreach ($indexExclude as $kex)
                unset($currentRows[0][$kex]);
            if(is_null($currentRows[0][1]))
                continue;
            $jsonData[] = array_combine($keys, $currentRows[0]);
        }
        $jsonParam = json_encode($jsonData,JSON_UNESCAPED_SLASHES);

        $pUsuario = $_SESSION['User']['name'];
        $store =  "call sp_importar_inventario('".$jsonParam."', '".$pUsuario."', @pData)";
        $db->setQuery($store);
        $res = 0;
        if($res = $db->ExcuteConsulta()) {
            $db->setQuery('select @pData');
            $data= $db->GetSingle();
            $data_explode = explode('|', $data);

            if($data_explode[0] == 'ERROR') {
                $res = 0;
                $util->setError(0, 'error', "Error en SP$data_explode[1] al importar.");
            }
            else {

                $mensaje = $data_explode[1] > 0
                    ? "Proceso completado: $data_explode[1] registros importados correctamente."
                    : "Proceso completado: no se ha registrado información.";
                $util->setError(0, 'complete', $mensaje);
            }
        } else {
            $util->setError(0, 'error','Error al importar registros');
        }
        echo $res ? 'ok[#]' : 'fail[#]';
        $util->PrintErrors();
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        break;
}
