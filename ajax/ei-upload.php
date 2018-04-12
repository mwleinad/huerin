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
            $html ="";
            $fila=1;
            switch($_POST['tipo-ei']){
                case 'imp-rsocial':
                    $fila=1;
                    $upDo=0;
                    $logFile="";
                    while(($row=fgetcsv($fp,4096,","))==true){
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
                        if(empty($contrato_actual)||$contrato_actual['permisos']==""||((trim($row[40])==""||trim($row[40])=="--")&&(trim($row[41])==""||trim($row[41])=="--")))
                        {
                            $logFil .="este no pasa ".$row[1]."<br>";
                            $fila++;
                            continue;
                        }
                        foreach($permisos_actuales as $val) {
                            $dep = explode(',', $val);
                            $dptos[$dep[0]] = $dep[1];
                        }
                        //encontrar id de responsables.
                        if(array_key_exists(1,$dptos)&&$dptos[1]>0){
                            $db->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[38])."' ");
                            $respConId =  $db->GetSingle();
                            $logFil .="Contabiliad:".$row[38]."(".$respConId.") <-> ";
                            if($dptos[1]!=$respConId&&$respConId>0)
                                $deptosNew[1] = $respConId;
                            else
                                $deptosNew[1] =$dptos[1];

                        }

                        if(array_key_exists(8,$dptos)&&$dptos[8]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[39]) . "' ");
                            $respNomId = $db->GetSingle();
                            $logFil .="Nomina:".$row[39]."(".$respNomId.") <-> ";
                            if ($dptos[8] != $respNomId&&$respNomId>0)
                                $deptosNew[8] = $respNomId;
                            else
                                $deptosNew[8] =$dptos[8];
                        }
                        if(array_key_exists(21,$dptos)&&$dptos[21]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[40]) . "' ");
                            $respAdmId = $db->GetSingle();
                            $logFil .="Admin:".$row[40]."(".$respAdmId.") <-> ";
                            if ($dptos[21] != $respAdmId&&$respAdmId>0)
                                $deptosNew[21] = $respAdmId;
                            else
                                $deptosNew[21] =$dptos[21];
                        }
                        if(array_key_exists(22,$dptos)&&$dptos[22]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[41]) . "' ");
                            $respJurId = $db->GetSingle();
                            $logFil .="Jurid:".$row[41]."(".$respJurId.") <-> ";
                            if ($dptos[22] != $respJurId&&$respJurId>0)
                                $deptosNew[22] = $respJurId;
                            else
                                $deptosNew[22] =$dptos[22];
                        }
                        if(array_key_exists(24,$dptos)&&$dptos[24]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[42]) . "' ");
                            $respImmId = $db->GetSingle();
                            $logFil .="Imms:".$row[42]."(".$respImmId.") <-> ";
                            if ($dptos[24] != $respImmId&&$respImmId>0)
                                $deptosNew[24] = $respImmId;
                            else
                                $deptosNew[24] =$dptos[24];
                        }
                        if(array_key_exists(26,$dptos)&&$dptos[26]>0) {
                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[43]) . "' ");
                            $respMsjId = $db->GetSingle();
                            $logFil .="Mensaje :".$row[43]."(".$respMsjId.") <-> ";
                            if ($dptos[26] != $respMsjId&&$respMsjId>0)
                                $deptosNew[26] = $respMsjId;
                            else
                                $deptosNew[26] =$dptos[26];
                        }
                        if(array_key_exists(31,$dptos)&&$dptos[31]>0) {

                            $db->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[44]) . "' ");
                            $respAudId = $db->GetSingle();
                            $logFil .="Audit :".$row[44]."(".$respAudId.") <-> ";
                            if ($dptos[31] != $respAudId&&$respAudId>0)
                                $deptosNew[31] = $respAudId;
                            else
                                $deptosNew[31] =$dptos[31];
                        }
                        //concatenar nuevos permisos
                        $per = array();
                        foreach($deptosNew as $kp=>$valp){
                            $cad= $kp.",".$valp;
                            array_push($per,$cad);
                        }
                      $logFil .= ' =|'.$row[1].'<br>';
                      $html.= $contrato_actual['permisos']."<=>".implode("-",$per);
                      $html.= "<br>";
                        $db->setQuery('UPDATE contract SET permisos="'.implode('-',$per).'" WHERE contractId="'.$row[1].'" ');
                        $up = $db->UpdateData();
                        if($up>0){
                            $upDo++;
                            $html .=$db->getQuery();
                            $html .="<br><br>";
                        }

                        unset($per);
                        unset($dptos);
                        unset($deptosNew);
                        $fila++;
                    }
                    break;
            }
            $html .=" total actualizados : ".$upDo."<br>";

            fclose($fp);
            echo "ok[#]";
            echo $html;
            echo  $logFil;
        }
    break;
}