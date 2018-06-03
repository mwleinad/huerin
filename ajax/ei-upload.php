<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();
switch($_POST['type']){
    case 'importar-datos':
//       dd($_FILES);
//        exit;
        //col 41 is respon-admin

        if($_POST['tipo-ei']=="")
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
        }
        else
        {
            $file_temp = $_FILES['file']['tmp_name'];
            $fp = fopen($file_temp,'r');
            $fila=1;
            switch($_POST['tipo-ei']){
                case 'imp-encargados':
                    $fila=1;
                    $upDo=0;
                    $logFileGlobal="";
                    $htmlglob ="";
                    $stringNoResp="";
                    $addRes=0;
                    $noUpdate=0;
                    $contratoNotFound=0;
                    while(($row=fgetcsv($fp,4096,","))==true){
                        $html="";
                        $logFil="";
                        $noResp ="";
                        if($fila==1)
                        {
                            $fila++;
                            continue;
                        }
                        $total =count($row);
                        $db->setQuery('SELECT * from contract WHERE contractId="'.$row[1].'"');
                        $contrato_actual = $db->GetRow();
                        $dptos=array();
                        $deptosNew =  array();
                        $permisos_actuales = explode("-",$contrato_actual['permisos']);
                        if(empty($contrato_actual))//||((trim($row[40])==""||trim($row[40])=="--")&&(trim($row[41])==""||trim($row[41])=="--"))
                        {
                            $logFil .="este no pasa ".$row[1]."<br>";
                            $fila++;
                            $contratoNotFound++;
                            continue;
                        }
                        foreach($permisos_actuales as $val) {
                            $dep = explode(',', $val);
                            $dptos[$dep[0]] = $dep[1];
                        }
                        //resetear permisos nuevos por cada iteracion
                        $deptosNew =array();
                        //encontrar id de responsables.
                        /*--------------------------------------------------------------------------------------*/
                        if(array_key_exists(1,$dptos)&&$dptos[1]>0){
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[38])."' ");
                            $respConId =  $db->GetSingle();
                            $logFil .="Contabiliad:".$row[38]."(".$respConId.") <-> ";
                            if($dptos[1]!=$respConId&&$respConId>0)
                                $deptosNew[1] = $respConId;
                            else
                                $deptosNew[1] =$dptos[1];

                        }else{
                            //no tiene reposable o el responsable es nulo
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[38])."' ");
                            $respConId =  $db->GetSingle();
                            //si el responsable existe se agrega
                            if($respConId){
                                $noResp .="res conta add => ".$row[38]."(".$respConId.") <->";
                                $logFil .="Contabiliad:".$row[38]."(".$respConId.") <-> ";
                                $deptosNew[1] =$respConId;
                            }


                        }
                        /*--------------------------------------------------------------------------------------*/
                        if(array_key_exists(8,$dptos)&&$dptos[8]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[39]) . "' ");
                            $respNomId = $db->GetSingle();
                            $logFil .="Nomina:".$row[39]."(".$respNomId.") <-> ";
                            if ($dptos[8] != $respNomId&&$respNomId>0)
                                $deptosNew[8] = $respNomId;
                            else
                                $deptosNew[8] =$dptos[8];
                        }else{
                            //no tiene reposable o el responsable es nulo
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[39])."' ");
                            $respNomId =  $db->GetSingle();
                            //si el responsable existe se agrega
                            if($respNomId){
                                $noResp .="res nomina add => ".$row[39]."(".$respNomId.") <->";
                                $logFil .="Nomina:".$row[39]."(".$respNomId.") <-> ";
                                $deptosNew[8] =$respNomId;
                            }


                        }
                        /*--------------------------------------------------------------------------------------*/
                        if(array_key_exists(21,$dptos)&&$dptos[21]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[40]) . "' ");
                            $respAdmId = $db->GetSingle();
                            $logFil .="Admin:".$row[40]."(".$respAdmId.") <-> ";
                            if ($dptos[21] != $respAdmId&&$respAdmId>0)
                                $deptosNew[21] = $respAdmId;
                            else
                                $deptosNew[21] =$dptos[21];
                        }else{
                            //no tiene reposable o el responsable es nulo
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[40])."' ");
                            $respAdmId =  $db->GetSingle();
                            //si el responsable existe se agrega
                            if($respAdmId){
                                $noResp .="res admin add => ".$row[40]."(".$respAdmId.") <->";
                                $logFil .="Admin:".$row[40]."(".$respAdmId.") <-> ";
                                $deptosNew[21]=$respAdmId;
                            }
                        }
                        /*--------------------------------------------------------------------------------------*/
                        if(array_key_exists(22,$dptos)&&$dptos[22]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[41]) . "' ");
                            $respJurId = $db->GetSingle();
                            $logFil .="Jurid:".$row[41]."(".$respJurId.") <-> ";
                            if ($dptos[22] != $respJurId&&$respJurId>0)
                                $deptosNew[22] = $respJurId;
                            else
                                $deptosNew[22] =$dptos[22];
                        }else{
                            //no tiene reposable o el responsable es nulo
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[41])."' ");
                            $respJurId =  $db->GetSingle();
                            //si el responsable existe se agrega
                            if($respJurId){
                                $noResp .="res juridi add => ".$row[41]."(".$respJurId.") <->";
                                $logFil .="Jurid:".$row[41]."(".$respJurId.") <-> ";
                                $deptosNew[22]=$respJurId;
                            }
                        }
                        /*--------------------------------------------------------------------------------------*/
                        if(array_key_exists(24,$dptos)&&$dptos[24]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[42]) . "' ");
                            $respImmId = $db->GetSingle();
                            $logFil .="Imms:".$row[42]."(".$respImmId.") <-> ";
                            if ($dptos[24] != $respImmId&&$respImmId>0)
                                $deptosNew[24] = $respImmId;
                            else
                                $deptosNew[24] =$dptos[24];
                        }else{
                            //no tiene reposable o el responsable es nulo
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[42])."' ");
                            $respImmId =  $db->GetSingle();
                            //si el responsable existe se agrega
                            if($respImmId){
                                $noResp .="res Imm add => ".$row[42]."(".$respImmId.") <->";
                                $logFil .="Jurid:".$row[42]."(".$respImmId.") <-> ";
                                $deptosNew[24]=$respImmId;
                            }
                        }
                        /*--------------------------------------------------------------------------------------*/
                        if(array_key_exists(26,$dptos)&&$dptos[26]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[43]) . "' ");
                            $respMsjId = $db->GetSingle();
                            $logFil .="Mensaje :".$row[43]."(".$respMsjId.") <-> ";
                            if ($dptos[26] != $respMsjId&&$respMsjId>0)
                                $deptosNew[26] = $respMsjId;
                            else
                                $deptosNew[26] =$dptos[26];
                        }else{
                            //no tiene reposable o el responsable es nulo
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[43])."' ");
                            $respMsjId =  $db->GetSingle();
                            //si el responsable existe se agrega
                            if($respMsjId){
                                $noResp .="res Imm add => ".$row[43]."(".$respMsjId.") <->";
                                $logFil .="Jurid:".$row[43]."(".$respMsjId.") <-> ";
                                $deptosNew[26]=$respMsjId;
                            }
                        }
                        /*--------------------------------------------------------------------------------------*/
                        if(array_key_exists(31,$dptos)&&$dptos[31]>0) {

                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[44]) . "' ");
                            $respAudId = $db->GetSingle();
                            $logFil .="Audit :".$row[44]."(".$respAudId.") <-> ";
                            if ($dptos[31] != $respAudId&&$respAudId>0)
                                $deptosNew[31] = $respAudId;
                            else
                                $deptosNew[31] =$dptos[31];
                        }else{
                            //no tiene reposable o el responsable es nulo
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[44])."' ");
                            $respAudId =  $db->GetSingle();
                            //si el responsable existe se agrega
                            if($respAudId){
                                $noResp .="res audit add => ".$row[44]."(".$respAudId.") <->";
                                $logFil .="Jurid:".$row[44]."(".$respAudId.") <-> ";
                                $deptosNew[31]=$respAudId;
                            }
                        }
                        //concatenar nuevos permisos
                        $per = array();
                        foreach($deptosNew as $kp=>$valp){
                            $cad= $kp.",".$valp;
                            array_push($per,$cad);
                        }
                      if($noResp!=""){
                          $addRes++;
                          $noResp .= ' ===> '.$row[1].'<br>';
                      }
                      $stringNoResp .=$noResp;
                      $logFil .= ' =|'.$row[1].'<br>';
                      $html.= $contrato_actual['permisos']."<=>".implode("-",$per);
                      $html.= "<br>";
                       if($contrato_actual['permisos']!=implode("-",$per)){

                           /*$logFilGlobal .=$logFil;
                           $html .='UPDATE contract SET permisos="'.implode('-',$per).'" WHERE contractId="'.$row[1].'" ';
                           $html .="<br><br>";
                           $upDo++;*/
                           $db->setQuery('UPDATE contract SET permisos="'.implode('-',$per).'" WHERE contractId="'.$row[1].'" ');
                           $up = $db->UpdateData();
                           if($up>0){
                               $upDo++;
                               $html .=$db->getQuery();
                               $html .="<br><br>";
                           }
                       }else{
                          $html="";
                          $noUpdate++;
                       }
                       $htmlglob .=$html;
                        unset($per);
                        unset($dptos);
                        unset($deptosNew);
                        $fila++;
                    }

                    $htmlglob .=" total de contratos actualizados : ".$upDo."<br>";
                    echo "ok[#]";
                    echo $htmlglob;
                    echo $logFilGlobal;
                    echo "<br>contratos que no tenian responsable en una area<br><br>";
                    echo $stringNoResp;
                    echo '<br>Total de contratos que no tenian un responsable en cualquiera de las areas '.$addRes."<br>";
                    echo 'Total de contratos que no se actualizaron por tener informacion correcta '.$noUpdate;
                    echo '<br>Total de contratos no encontrados '.$contratoNotFound.'<br>';
                    break;
                case 'imp-data-customer-contract':
                    $fila=1;
                    $upDo=0;
                    $logFileGlobal="";
                    $contCustomer=0;
                    $contContract =0;
                    while(($row=fgetcsv($fp,4096,","))==true) {
                        if ($fila == 1) {
                            $fila++;
                            continue;
                        }
                        //actualizamos los datos del cliente en la tabla customer
                        $sqlc = "UPDATE customer 
                                 SET nameContact='".trim($row[2])."',
                                 phone='".trim($row[3])."',
                                 email='".trim($row[4])."',
                                 password='".trim($row[5])."' 
                                 WHERE customerId='".$row[0]." '
                                 ";
                        $db->setQuery($sqlc);
                        $upc = $db->UpdateData();
                        if($upc>0){
                            $contCustomer++;
                            $logFileGlobal .="---<br>El cliente ".$row[2]." con ID=".$row[0]." ha sido actualizado.<br>";
                        }
                        else
                            $logFileGlobal .="--<br>Ningun cambio realizado para el cliente ".$row[2]." con ID=".$row[0].".<br>";
                        //actualizamos datos del contrato del cliente
                        $sqlr = "UPDATE contract 
                                 SET name='".trim($row[10])."',
                                 rfc='".trim($row[13])."',
                                 nombreComercial='".trim($row[16])."',
                                 direccionComercial='".trim($row[17])."',
                                 nameContactoAdministrativo='".trim($row[19])."',
                                 emailContactoAdministrativo='".trim($row[20])."',
                                 telefonoContactoAdministrativo='".trim($row[21])."',
                                 nameContactoContabilidad='".trim($row[22])."',
                                 emailContactoContabilidad='".trim($row[23])."',
                                 telefonoContactoContabilidad='".trim($row[24])."',
                                 nameContactoDirectivo='".trim($row[25])."',
                                 emailContactoDirectivo='".trim($row[26])."',
                                 telefonoContactoDirectivo='".trim($row[27])."',
                                 telefonoCelularDirectivo='".trim($row[28])."',
                                 claveCiec='".trim($row[29])."',
                                 claveFiel='".trim($row[30])."',
                                 claveIdse='".trim($row[31])."',
                                 claveIsn='".trim($row[32])."',
                                 facturador='".trim($row[33])."',
                                 metodoDePago='".trim($row[34])."',
                                 noCuenta='".trim($row[35])."'
                                 WHERE contractId='".$row[1]." '
                                 ";
                        $db->setQuery($sqlr);
                        $upr = $db->UpdateData();
                        if($upr>0) {
                            $contContract++;
                            $logFileGlobal .= "La razon social " . $row[10] . " con ID=" . $row[1] . " ha sido actualizado. ----<br>";
                        }
                        else
                            $logFileGlobal .="Ningun cambio realizado en la razon social ".$row[10]." con ID=".$row[1].". ----<br>";
                    }
                    echo "ok[#]";
                    echo $logFileGlobal;
                    echo "Total clientes actualizados = ".$contCustomer."<br>";
                    echo "Total contratos actualizados =".$contContract."<br>";
                break;
                case 'imp-new-razon':
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
                           break;
                        }

                        $logCancel .=" FOLIO FISCAL".$row[0].". cancelado correctamente.";
                        $logCancel .='response : '.print_r($response["cancelaCFDiReturn"]);
                        //cancelar uno ala vez
                        break;
                    }
                    echo "ok[#]";
                    echo $logCancel;
                break;
            }
            fclose($fp);
        }
    break;
}