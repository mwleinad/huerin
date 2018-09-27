<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();
//comprobar que se ha seleccionado un archivo y el tipo de importacion
if($_POST['type']=="")
    $util->setError(0,"error",'Debe seleccionar ','Tipo');

if($_FILES['file']['error']===4){
    $util->setError(0,"error",'No se ha seleccionado un archivo','Archivo');
}else{
    $name =  $_FILES['file']['name'];
    $ext = end(explode(".",$name));

    if(strtoupper($ext)!="CSV") {
        $util->setError(0, "error", 'Verificar extesion, solo se acepta CSV', 'Archivo');
    }
}
if($util->PrintErrors()){
    echo "fail[#]";
    $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
    exit;
}
$opermiso =  new Permiso();
//tratar el archivo

switch($_POST['type']){
    case 'update-customer-contract':
        $cad = "";
        $isValid = $valida->ValidateLayoutCustomerRazon($_FILES);
        if(!$isValid){
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo"[#]";
            echo $cad;
            exit;
        }
        $contActualizado=0;
        $contNoActualizado=0;
        $contratoNoEncontrado=0;
        $sqlCustomer ="UPDATE customer SET";
        $strNameContact="";$strTelContact="";$strEmailContact="";$strPassContact="";$strAltaCustomer="";
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila=1;
        $idsCustomer=array();
        $generalCustomerLog="";
        $generalContractLog="";
        while(($row=fgetcsv($fp,4096,","))==true){
           $strCust ="";
           $strContract="";
           $logCustLocal ="";
           $logContractLocal ="";
            if($fila==1)
            {
                $fila++;
                continue;
            }
            $total =count($row);
            //dejar esto por si se actualizara masivo
            /*$strNameContact .=sprintf(" WHEN %d  THEN '%s' ",$row[0],$row[2]);
            $strTelContact .=sprintf(" WHEN %d  THEN '%s' ",$row[0],$row[3]);
            $strEmailContact .=sprintf(" WHEN %d  THEN '%s' ",$row[0],$row[4]);
            $strPassContact .=sprintf(" WHEN %d  THEN '%s' ",$row[0],$row[5]);
            $strAltaCustomer .=sprintf(" WHEN %d  THEN '%s' ",$row[0],$util->FormatDateMySqlSlash($row[7]));
            $idsCustomer[] =$row[0];*/

            $customer->setCustomerId($row[0]);
            $beforeCustomer = $customer->Info();
            $strCust ="UPDATE customer SET nameContact ='".$row[2]."', phone='".$row[3]."',email='".$row[4]."', password='".$row[5]."',fechaAlta='".$util->FormatDateMySqlSlash($row[7])."' where customerId ='".$row[0]."'";
            $db->setQuery($strCust);
            $upCustomer =  $db->UpdateData();
            if($upCustomer>0){
                $customer->setCustomerId($row[0]);
                $afterCustomer = $customer->Info();
                //guardar en log
                $log->setPersonalId($_SESSION['User']['userId']);
                $log->setFecha(date('Y-m-d H:i:s'));
                $log->setTabla('customer');
                $log->setTablaId($row[0]);
                $log->setAction('Update');
                $log->setOldValue(serialize($beforeCustomer));
                $log->setNewValue(serialize($afterCustomer));
                $log->SaveOnly();

                $changes = $log->FindOnlyChanges(serialize($beforeCustomer),serialize($afterCustomer));
                if(!empty($changes['after'])){
                    $logCustLocal ="<p>El cliente ".$beforeCustomer['nameContact']." ha sido modificado</p>";
                    $logCustLocal .=$log->PrintInFormatText($changes);
                }
                $contActualizado++;
            }else{
                $contNoActualizado++;
            }
            //concatenar log de updates de clientes
            $generalCustomerLog .=$logCustLocal;
            //comprobar si se actualizara los datos del contrato.
            //encontrar cambios en encargados
            $contract->setContractId($row[1]);
            $encargados = array();
            $encargados = array($row[1],$row[38],$row[39],$row[40],$row[41],$row[42],$row[43],$row[44]);
            $permisos= $contract->ValidateEncargados($encargados);
            if($permisos===false)
            {
                $contratoNoEncontrado++;
                $contratosIngorados .="La razon social con ID=".$row[1]." de la fila ".$fila." no se encuentra registrada";
                $fila++;
                continue;
            }
            //contrato antes de actualizar
            $changes=array();
            $contract->setContractId($row[1]);
            $beforeContract = $contract->Info();
            //encontrar regimen
            $db->setQuery("SELECT regimenId FROM  regimen WHERE lower(replace(nombreRegimen,' ',''))='".strtolower(str_replace(' ','',$row[14]))."' and lower(replace(tipoDePersona,' ',''))='".strtolower(str_replace(' ','',$row[12]))."' ");
            $regimenId=$db->GetSingle();
            $strContract ="UPDATE contract SET 
                            permisos='".$permisos."',
                            type='".$row[12]."',
                            regimenId='".$regimenId."',
                            name='".$row[10]."',
                            nombreComercial='".$row[16]."',
                            direccionComercial='".$row[17]."',
                            nameContactoAdministrativo='".$row[19]."',
                            emailContactoAdministrativo='".$row[20]."',
                            telefonoContactoAdministrativo='".$row[21]."',
                            nameContactoContabilidad='".$row[22]."',
                            emailContactoContabilidad='".$row[23]."',
                            telefonoContactoContabilidad='".$row[24]."',
                            nameContactoDirectivo='".$row[25]."',
                            emailContactoDirectivo='".$row[26]."',
                            telefonoContactoDirectivo='".$row[27]."',
                            telefonoCelularDirectivo='".$row[28]."',
                            claveCiec='".$row[29]."',
                            claveFiel='".$row[30]."',
                            claveIdse='".$row[31]."',
                            claveIsn='".$row[32]."',
                            rfc='".$row[13]."',
                            facturador='".$row[33]."',
                            metodoDePago='".$row[34]."',
                            noCuenta='".$row[35]."'
                            WHERE contractId='".$row[1]."' ";
            $db->setQuery($strContract);
            $upContract =  $db->UpdateData();
            if($upContract>0){
                //si se actualizo la razon se debe actualizar los permisos en la tabla
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
                $changes = $log->FindOnlyChanges(serialize($beforeContract),serialize($afterContract));
                if(!empty($changes['after'])){
                    $logContractLocal ="<p>La razon social ".$beforeContract['name']." del cliente ".$beforeContract['nameContact']." ha sido modificado</p>";
                    $logContractLocal .=$log->PrintInFormatText($changes);
                }
                $contActualizado++;
            }else{
                $contNoActualizado++;
            }
            $generalContractLog .=$logContractLocal;
            $fila++;
        }
        //concatenar sql para cliente para una sola consulta
        /* $sqlCustomer."  nameContact=CASE customerId ".$strNameContact." END,
                             phone=CASE customerId ".$strTelContact." END, 
                             email=CASE customerId ".$strEmailContact." END,
                             password=CASE customerId ".$strPassContact." END,
                             fechaAlta=CASE customerId ".$strAltaCustomer." END WHERE customerId IN(".implode(',',array_unique($idsCustomer)).")";*/
        $file1="";
        $nameFile1="";
        if($generalCustomerLog!="") {
            $nameFile1 = "BITACORA CLIENTES.html";
            $file1 = DOC_ROOT . "/sendFiles/".$nameFile1;
            $open = fopen($file1, "w");
            if ($open) {
                fwrite($open, $generalCustomerLog);
                fclose($open);
            }
        }
        $file2="";
        $nameFile2="";
        if($generalContractLog!="") {
            $nameFile2 = "BITACORA RAZONESSOCIALES.html";
            $file2 = DOC_ROOT."/sendFiles/".$nameFile2;
            $open = fopen($file2,"w");
            if ( $open ) {
                fwrite($open, $generalContractLog);
                fclose($open);
            }
        }

        $subject = 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $db->setQuery('SELECT name FROM personal WHERE personalId="'.$_SESSION['User']['userId'].'" ');
        $who = $db->GetSingle();
        if($_SESSION['User']['tipoPers']=='Admin')
            $who="Administrador de sistema(desarrollador)";

        $body ="<p>Se han realizado cambios en informacion de cliente y razones sociales por el colaborador ".$who.". </p>";
        $body .="<p>En los archivos adjuntos encontrara de manera detallada los cambios realizados por el usuario, favor de descargar el documento y abrir en su navegador predeterminado. </p>";
        $encargadosEmail=array();
        $sendmail = new SendMail();

        if($generalContractLog!=""||$generalCustomerLog!="")
            $sendmail->PrepareMultipleNotice($subject,$body,$encargadosEmail,"",$file1,$nameFile1,$file2,$nameFile2,'sistema@braunhuerin.com.mx','Administrador de plataforma',true);

        if(is_file($file1))
            unlink($file1);
        if(is_file($file2))
            unlink($file2);
        fclose($fp);
        $util->setError(0,'complete',$contActualizado." registros actualizados");
        $util->setError(0,'complete',$contNoActualizado." registros no actualizados por tener informacion correcta");
        $util->setError(0,'complete',$contratoNoEncontrado." registros no encontrados en el sistema");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

    break;
    case 'imp-new-contract':
        $isValid = $valida->ValidateLayoutNewContract($_FILES);
        if(!$isValid){
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo"[#]";
            exit;
        }
        $generalContractLog="";
        $fila=1;
        $conAgregado=0;
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        while(($row=fgetcsv($fp,4096,","))==true) {
            $sociedadId ="";
            $strCust = "";
            $strContract = "";
            $logContractLocal = "";
            if ($fila == 1) {
                $fila++;
                continue;
            }

            $sql = "SELECT customerId FROM customer WHERE nameContact='".$row[0]."' ";
            $db->setQuery($sql);
            $customerId = $db->GetSingle();

            if(strtolower($row[2])=="persona moral"){
                $db->setQuery("SELECT sociedadId FROM  sociedad WHERE lower(replace(nombreSociedad,' ',''))='".strtolower(str_replace(' ','',$row[40]))."' ");
                $sociedadId=$db->GetSingle();
            }
            $db->setQuery("SELECT regimenId FROM  regimen WHERE lower(replace(nombreRegimen,' ',''))='".strtolower(str_replace(' ','',$row[39]))."' and lower(replace(tipoDePersona,' ',''))='".strtolower(str_replace(' ','',$row[2]))."' ");
            $regimenId=$db->GetSingle();
            $permisos = $contract->ConcatenarEncargados($row);
            $sqlInsert = "INSERT INTO contract(
                          customerId,
                          type,
                          regimenId,
                          name,
                          telefono,
                          nombreComercial,
                          direccionComercial,
                          address,
                          noExtAddress,
                          noIntAddress,
                          coloniaAddress,
                          municipioAddress,
                          estadoAddress,
                          paisAddress,
                          cpAddress,
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
                          claveSip,
                          sociedadId,
                          rfc,
                          facturador,
                          permisos,
                          metodoDePago,
                          noCuenta)
                          VALUES(
                          '".$customerId."',
                          '".$row[2]."',
                          '".$regimenId."',
                          '".utf8_encode($row[1])."',
                          '',
                          '".utf8_encode($row[5])."',
                          '".utf8_encode($row[17])."',
                          '".utf8_encode($row[6])."',
                          '".utf8_encode($row[7])."',
                          '".utf8_encode($row[8])."',
                          '".utf8_encode($row[9])."',
                          '".utf8_encode($row[10])."',
                          '".utf8_encode($row[11])."',
                          '".utf8_encode($row[12])."',
                          '".$row[13]."',
                          '".utf8_encode($row[18])."',
                          '".$row[19]."',
                          '".$row[20]."',
                          '".utf8_encode($row[21])."',
                          '".$row[22]."',
                          '".$row[23]."',
                          '".utf8_encode($row[24])."',
                          '".$row[25]."',
                          '".$row[26]."',
                          '".$row[27]."',
                          '".$row[28]."',
                          '".$row[29]."',
                          '".$row[30]."',
                          '".$row[31]."',
                          '".$row[16]."',
                          '".$sociedadId."',
                          '".$row[4]."',
                          '".$row[3]."',
                          '".$permisos."',
                          '".$row[14]."',
                          '".$row[15]."'
                          )";
            $db->setQuery($sqlInsert);
            $registroId =  $db->InsertData();
            if($registroId>0)
            {
                //si se actualizo la razon se debe actualizar los permisos en la tabla
                $opermiso->setContractId($registroId);
                $opermiso->doPermiso();

                $contract->setContractId($registroId);
                $infoReg = $contract->Info();
                //guardar en log
                $log->setPersonalId($_SESSION['User']['userId']);
                $log->setFecha(date('Y-m-d H:i:s'));
                $log->setTabla('contract');
                $log->setTablaId($registroId);
                $log->setAction('Insert');
                $log->setOldValue('');
                $log->setNewValue(serialize($infoReg));
                $log->SaveOnly();
                $changes = $log->FindFieldDetail(serialize($infoReg));
                if(!empty($changes)){
                    $logContractLocal ="<p>Alta de la razon social ".utf8_decode($infoReg['name'])." del cliente ".$row['0']."</p>";
                    $logContractLocal .=$log->PrintInFormatText($changes,'simple');
                }
                $conAgregado++;
                $generalContractLog .=$logContractLocal;
            }

        }
        fclose($fp);
        $file="";
        $nameFile="";
        if($generalContractLog!="") {
            $nameFile= "BITACORA ALTARAZON.html";
            $file = DOC_ROOT."/sendFiles/".$nameFile;
            $open = fopen($file,"w");
            if ( $open ) {
                fwrite($open, $generalContractLog);
                fclose($open);
            }
        }
        $subject = 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $db->setQuery('SELECT name FROM personal WHERE personalId="'.$_SESSION['User']['userId'].'" ');
        $who = $db->GetSingle();
        if($_SESSION['User']['tipoPers']=='Admin')
            $who="Administrador de sistema(desarrollador)";

        $body ="<p>Se han agregado razones sociales en plataforma por el colaborador ".$who.". </p>";
        $body .="<p>En el archivo adjunto se detallan los movimientos realizados, favor de descargar el documento y abrir en su navegador predeterminado. </p>";
        $encargados=array();
        $sendmail = new SendMail();
        if($generalContractLog!="")
            $sendmail->PrepareMultipleNotice($subject,$body,$encargados,"",$file,$nameFile,"","",'sistema@braunhuerin.com.mx','Administrador de plataforma',true);

        if(is_file($file))
            unlink($file);

        $util->setError(0,'complete',$conAgregado." registros agregados a plataforma.");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

    break;
    case 'update-only-encargado':
        $isValid = $valida->ValidateLayoutOnlyEncargado($_FILES);
        if(!$isValid){
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo"[#]";
            echo $cad;
            exit;
        }
        $contActualizado=0;
        $contNoActualizado=0;
        $contratoNoEncontrado=0;
        $file_temp = $_FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila=1;
        $idsCustomer=array();
        $generalLog="";
        while(($row=fgetcsv($fp,4096,","))==true) {
            $logLocal = "";
            $permisos="";
            //comprobar permisos
            if($fila==1)
            {
                $fila++;
                continue;
            }
            $contract->setContractId($row[1]);
            $encargados=array();
            $encargados = array($row[1],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10]);
            $permisos= $contract->ValidateEncargados($encargados);
            if($permisos===false)
            {
                $contratoNoEncontrado++;
                echo $contratosIngorados .="La razon social con ID=".$row[1]." de la fila ".$fila." no se encuentra registrada";
                $fila++;
                continue;
            }
            $db->setQuery('SELECT permisos from contract WHERE contractId="'.$row[1].'" ');
            $permisosActual = $db->GetRow();

            if(trim($permisosActual['permisos'])!=trim($permisos))
            {
                $changes=array();
                $contract->setContractId($row[1]);
                $beforeContract = $contract->Info();
                $strContract ="UPDATE contract SET permisos='".$permisos."' WHERE contractId='".$row[1]."' ";
                $db->setQuery($strContract);
                $upContract =  $db->UpdateData();
                if($upContract>0) {
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
            }else{
                $contNoActualizado++;
            }
            $generalLog .=$logLocal;
            $fila++;
        }
        fclose($fp);
        $file2="";
        $nameFile2="";
        if($generalLog!="") {
            $nameFile2 = "BITACORA-CAMBIO-DE-ENCARGADOS.html";
            $file2 = DOC_ROOT."/sendFiles/".$nameFile2;
            $open = fopen($file2,"w");
            if ( $open ) {
                fwrite($open, $generalLog);
                fclose($open);
            }
        }
        $subject = 'NOTIFICACION DE CAMBIOS EN PLATAFORMA';
        $db->setQuery('SELECT name FROM personal WHERE personalId="'.$_SESSION['User']['userId'].'" ');
        $who = $db->GetSingle();
        if($_SESSION['User']['tipoPers']=='Admin')
            $who="Administrador de sistema(desarrollador)";

        $body ="<p>Se han realizado cambios en los encargados de area en algunos contratos por el colaborador ".$who.". </p>";
        $body .="<p>Adjunto a este correo encontrara un archivo que detalla los cambios realizados, favor de descargar el documento y abrir en su navegador predeterminado. </p>";
        $encargadosEmail=array();
        $sendmail = new SendMail();
        if($generalLog!="")
            $sendmail->PrepareMultipleNotice($subject,$body,$encargadosEmail,"",'','',$file2,$nameFile2,'sistema@braunhuerin.com.mx','Administrador de plataforma',true);
        if(is_file($file2))
            unlink($file2);
        $util->setError(0,'complete',$contActualizado." registros actualizados");
        $util->setError(0,'complete',$contNoActualizado." registros no actualizados por tener informacion correcta");
        $util->setError(0,'complete',$contratoNoEncontrado." registros no encontrados en el sistema");
        $util->PrintErrors();
        echo "ok[#]";
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');


    break;
    case 'imp-new-customer':
        $logFileGlobal="";
        $contCustomer=0;
        $contContract =0;
        $ejecutar = false;
        $allSql = "";
        while(($row=fgetcsv($fp,4096,","))==true) {
            $sqlRow = "";
          //comprobar que el cliente exista
            $sqlc ="SELECT customerId FROM customer where nameContact='".$row[0]."' ";
            $db->setQuery($sqlc);
            $customerId = $db->GetSingle();
            if(!$customerId){
                echo "ok[#]";
                echo "Proceso interrumpido";
                echo 'El cliente '.$row[0]." no se encuntra registrado";
                exit;
            }
            //comprobar por rfc y nombre la razon social si no se encuentra registrada
            $sqlrazon =  "SELECT contractId FROM contract WHERE name='".$row[1]."' OR rfc='".$row[2]."' ";
            $db->setQuery($sqlrazon);
            $contractId = $db->GetSingle();
        }
    break;
    case 'cancelar-uuid':
        $logCancel = "";
        $fila=1;
        $user1 = USER_PAC;
        $pw1 = PW_PAC;
        $pac = new Pac;
        $rfcObj =new Rfc();

        //get password
        $root = DOC_ROOT."/empresas/21/certificados/30/password.txt";
        $fh = fopen($root, 'r');
        $password = fread($fh, filesize($root));
        fclose($fh);

        $path = DOC_ROOT."/empresas/21/certificados/30/00001000000402946663.cer.pfx";
        if(!$password)
        {
            $util->setError('', "error", "Tienes que actualizar tu certificado para que podamos obtener el password");
            $util->PrintErrors();
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            exit;
        }
        while(($row=fgetcsv($fp,4096,","))==true) {
            if ($fila == 1) {
                $fila++;
                continue;
            }
            //cancelar los cfdi
            $rfcObj->setRfcId(30);
            $nodoEmisorRfc = $rfcObj->InfoRfc();
            $response = $pac->CancelaCfdi($user1, $pw1, $nodoEmisorRfc["rfc"], $row[0], $path, $password);
            if(!$response["cancelaCFDiReturn"]["text"])
            {
               $logCancel .="Hubo un problema al cancelar la factura con FOLIO FISCAL".$row[0].". Por favor permite que pasen al menos 24 horas antes de intentar de nuevo.<br>";
               $logCancel .='response fail : '.print_r($response["cancelaCFDiReturn"]);
            }else{
                $logCancel .=" FOLIO FISCAL".$row[0].". cancelado correctamente.<br>";
                $logCancel .='response : '.print_r($response["cancelaCFDiReturn"]);
            }


            //cancelar uno ala vez
            break;
        }
        echo "ok[#]";
        echo $logCancel;
    break;
    case 'killnotuse':

        $db->setQuery('select contractId from contract where activo="Si"  ');
        $result = $db->GetResult();

        foreach($result as $key =>$value){
            $emailAdmin ="contactoadmin".$key."@aristasoluciones.com";
            $emailConta ="contactoconta".$key."@aristasoluciones.com";
            $emailDir ="contactodir".$key."@aristasoluciones.com";


            /*$db->setQuery("UPDATE contract SET emailContactoAdministrativo='".$emailAdmin."',
                                  emailContactoContabilidad='".$emailConta."',
                                  emailContactoDirectivo='".$emailDir."' WHERE contractId='".$value['contractId']."' ");
            $db->UpdateData();*/
        }
      break;
}
