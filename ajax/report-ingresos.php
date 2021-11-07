<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case 'search':
			echo 'ok[#]';
			$contracts = array();
            $subordinados = $personal->GetIdResponsablesSubordinados($_POST);
            $filter = $_POST;
            $filter['subordinados'] =  $subordinados;
            $filter['tipos'] = 'activos';
            $contracts = $contract->Suggest($filter, false, true);
            $totalPeriodo = 0;
            foreach($contracts as $key => $value) {
                $servicios = array();
                foreach($value['servicios'] as $serv) {
                    $departamentoId = $serv["departamentoId"];
                    $costoVisual = $serv["costoVisual"];
                    $coordinate =  count($value['responsables']) > 0 ? array_search($departamentoId, array_column($value['responsables'], 'departamentoId')) : null;
                    $responsable = count($value['responsables']) > 0 ? $value['responsables'][$coordinate] : null ;

                    switch($serv["servicioStatus"]){
                        case 'activo': $serv["nameStatusComplete"] = 'Activo'; break;
                        case 'readonly': $serv["nameStatusComplete"] = 'Activo/Solo Lectura'; break;
                        case 'bajaParcial': $serv["nameStatusComplete"] = 'Baja Temporal'; break;
                        case 'baja': $serv["nameStatusComplete"] = 'Baja'; break;
                    }
                    if($responsable !== null){
                        $jefes = array();
                        $personal->setPersonalId($responsable['personalId']);
                        $rolRes = $personal->InfoWhitRol();
                        $personal->deepJefesArray($jefes,true);
                        $serv["contador"] = $jefes['Contador'];
                        $serv['supervisor'] = $jefes['Supervisor'];
                        $serv['subgerente'] = $jefes['Subgerente'];
                        $serv['gerente'] = $jefes['Gerente'];
                        $serv['jefeMax'] = $jefes['Socio'];
                        $serv[strtolower($rolRes["nameLevel"])] = $jefes['me'];
                    }else {
                        $serv['auxiliar'] = 'N/E';
                        $serv['contador'] = 'N/E';
                        $serv['supervisor'] = 'N/E';
                        $serv['subgerente'] = 'N/E';
                        $serv['gerente'] = 'N/E';
                    }
                    $serv['responsable'] = $responsable['name'];
                    $serv['costo'] = number_format($serv['costo'],2,'.','');

                    if($serv['periodicidad'] == 'Mensual')
                        $costoMensual = $serv['costo'];
                    elseif($serv['periodicidad'] == 'Anual')
                        $costoMensual = $serv['costo'] / 12;
                    elseif($serv['periodicidad'] == 'Bimestral')
                        $costoMensual = $serv['costo'] / 2;

                    $serv['costoMensual'] = number_format($costoMensual,2,'.','');
                    $serv['costoVisual'] = number_format($costoVisual,2,'.','');

                    if(!$util->isValidateDate($serv['inicioFactura'], 'Y-m-d'))
                        $serv['inicioFactura'] = '0000-00-00';

                    $totalPeriodo += $serv['costo'];
                    $totalMensual += $costoMensual;

                    $servicios[] = $serv;

                }//foreach
                $contracts[$key]['servicios'] = $servicios;
            }
			$smarty->assign('totalMensual', $totalMensual);
			$smarty->assign('totalPeriodo', $totalPeriodo);
			$smarty->assign("contracts", $contracts);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-ingresos.tpl');
		break;
    case 'searchBitacoraAltasBajas':
        echo 'ok[#]';
        $formValues['subordinados'] = $_POST['subordinados'];
        $formValues['respCuenta'] = $_POST['responsableCuenta'];
        $formValues['departamentoId'] = $_POST["departamentoId"];
        $formValues['statusSearch'] = $_POST["statusSearch"];
        $formValues['tipoSearch'] = $_POST["tipoSearch"];
        $formValues['sinServicios'] =true;
        $formValues['cliente'] = $_POST["rfc"];
        $year = $_POST["year"];
        $mes = (int)$_POST["month"];
        $idContrato = $_POST['contractId'];
        $contracts = [];

        switch($_POST["tipoSearch"]) {
            case 'contract':
                switch($_POST["statusSearch"]){
                    case 'modificacion':
                        $ftr  = "";
                        if($mes>0)
                            $ftr .=" and month(a.fecha)='$mes' ";
                        if($year>0)
                            $ftr .=" and year(a.fecha)='$year' ";
                        if(!$_POST["customerId"]&&$_POST["contractId"]){
                                $ftr .=" and a.tablaId = '".$_POST["contractId"]."' ";
                        }
                        if(strlen($_POST["rfc"])<=0)
                            $_POST["customerId"] = "";

                        if($_POST["customerId"]&&!$_POST["contractId"]){
                            $sql = "select contractId from contract where customerId='".$_POST["customerId"]."' ";
                            $db->setQuery($sql);
                            $contratos = $db->GetResult();
                            if(count($contratos)){
                                $ids = $util->ConvertToLineal($contratos,'contractId');
                                $ftr .=" and a.tablaId IN (".implode(",",$ids).") ";
                            }
                        }
                        $sql ="SELECT *,'Modificacion' as movimiento FROM log a 
                                        INNER JOIN (select contractId,contract.name,nameContact from contract inner join customer on contract.customerId=customer.customerId) b ON a.tablaId=b.contractId
                                        WHERE a.tabla = 'contract' and a.action='update'  $ftr order by fecha desc";
                        $db->setQuery($sql);
                        $modificaciones = $db->GetResult();
                        $contratos = [];
                        foreach($modificaciones as $kmod=>$vmod){
                            $conId = $vmod["tablaId"];
                            $db->setQuery("select DATE(fecha) from contractChanges where contractId='$conId' order by contractChangesId ASC limit 1");
                            $vmod["fechaAlta"] = $db->GetSingle();
                            $contratos[] = $vmod;
                        }
                    break;
                    case 'alta':
                          if ($mes > 0)
                              $ftr .=" and month(fecha)='$mes' ";
                          if($year>0)
                              $ftr .=" and year(fecha)='$year' ";

                          include_once(DOC_ROOT.'/ajax/filterOnlyContract.php');
                          $contratos = [];
                          foreach($contracts as $kco => $vcon){
                              $conId = $vcon["contractId"];
                              $db->setQuery("select DATE(fecha) as fechaAlta,fecha from contractChanges where contractId='$conId' and status='Si' $ftr order by contractChangesId ASC limit 1");
                              $date = $db->GetRow();
                              $vcon["fechaAlta"] = $date["fechaAlta"];
                              $vcon["fecha"] = $date["fecha"];

                              if (!$util->isValidateDate($vcon["fechaAlta"], "Y-m-d"))
                                  continue;

                              $vcon["movimiento"]="Alta";
                              $contratos[] = $vcon;
                          }
                    break;
                    case 'baja':
                        if ($mes > 0)
                            $ftr .=" and month(fecha)='$mes' ";
                        if($year>0)
                            $ftr .=" and year(fecha)='$year' ";
                        $formValues['activos'] =false;
                        include_once(DOC_ROOT.'/ajax/filterOnlyContract.php');
                        $contratos = [];
                        foreach($contracts as $kco => $vcon){
                            $conId = $vcon["contractId"];
                            $db->setQuery("select DATE(fecha) as fechaAlta,fecha from contractChanges where contractId='$conId' and status='Si' $ftr order by contractChangesId ASC limit 1");
                            $date = $db->GetRow();
                            $vcon["fechaAlta"] = $date["fechaAlta"];

                            $db->setQuery("select DATE(fecha) as fechaBaja,fecha from contractChanges where contractId='$conId' and status='No' $ftr order by contractChangesId DESC limit 1");
                            $date2 = $db->GetRow();
                            $vcon["fechaBaja"] = $date2["fechaBaja"];
                            $vcon["fecha"] = $date2["fecha"];
                            if (!$util->isValidateDate($vcon["fechaBaja"], "Y-m-d"))
                                continue;

                            $vcon["movimiento"]="Baja";
                            $contratos[] = $vcon;
                        }
                        break;
                }
                $smarty->assign("contratos", $contratos);
                $smarty->display(DOC_ROOT.'/templates/lists/report-up-down-contract.tpl');
            break;
            case 'service':
                $registros = [];
                switch($_POST["statusSearch"]){
                    case 'modificacion':
                    case 'alta':
                        if($_POST["statusSearch"]=="modificacion"){
                            $action="update";
                            $movimiento = "Modificacion";
                        }elseif($_POST["statusSearch"]=="alta"){
                            $action="insert";
                            $movimiento = "Alta";
                        }
                       $ftr ="";
                        if($mes>0)
                            $ftr .=" and month(a.fecha)='$mes' ";
                        if($year>0)
                            $ftr .=" and year(a.fecha)='$year' ";
                        if(strlen($_POST["rfc"])<=0)
                            $_POST["customerId"] = "";

                        if($_POST["contractId"]){
                            //obtener los servicios de la razon s.
                            $db->setQuery("select servicioId from servicio where contractId='".$_POST["contractId"]."' ");
                            $serviciosEncontrados = $db->GetResult();
                            if(count($serviciosEncontrados)>0){
                                $idServiciosEncontrados = $util->ConvertToLineal($serviciosEncontrados,'servicioId');
                                $ftr .= " and tablaId IN (".implode(",",$idServiciosEncontrados).") ";
                            }else{
                                $ftr .=" and tablaId IN(0) ";
                            }
                        }elseif($_POST["customerId"]){
                            //obtener los servicios de la razon s.
                            $db->setQuery("select contractId from contract where customerId='".$_POST["customerId"]."' ");
                            $contratosEncontrados = $db->GetResult();
                            if(count($contratosEncontrados)>0){
                                $idContratosEncontrados = $util->ConvertToLineal($contratosEncontrados,'contractId');
                                $db->setQuery("select servicioId from servicio where contractId IN(".implode(",",$idContratosEncontrados).") ");
                                $serviciosEncontrados = $db->GetResult();
                                if(count($serviciosEncontrados)>0){
                                    $idServiciosEncontrados = $util->ConvertToLineal($serviciosEncontrados,'servicioId');
                                    $ftr .= " and tablaId IN (".implode(",",$idServiciosEncontrados).") ";
                                }else{
                                    $ftr .=" and tablaId IN(0) ";
                                }
                            }else{
                                $ftr .=" and tablaId IN(0) ";
                            }
                        }
                        $sql ="SELECT a.fecha,a.personalId,a.tablaId,a.fecha,b.nombreServicio,b.servicioId,b.tipoServicioId,
                               b.inicioOperaciones,b.inicioFactura,b.periodicidad,b.departamentoId,b.contractId,b.status,
                               c.name,c.nameContact,'$movimiento' as movimiento, b.uniqueInvoice 
                               FROM log a 
                               INNER JOIN (select contractId,servicioId,servicio.tipoServicioId,servicio.status,
                                           nombreServicio,inicioOperaciones,inicioFactura,periodicidad,departamentoId,
                                           uniqueInvoice      
                                           from servicio 
                                           inner join tipoServicio on servicio.tipoServicioId=tipoServicio.tipoServicioId) b ON a.tablaId=b.servicioId
                               INNER JOIN (select contractId,contract.name,nameContact 
                                           from contract 
                                           inner join customer on contract.customerId=customer.customerId) c ON b.contractId=c.contractId
                               WHERE a.tabla = 'servicio' and a.action='$action'  $ftr order by a.fecha desc";
                        $db->setQuery($sql);
                        $modificaciones = $db->GetResult();
                        $registros = [];
                        foreach($modificaciones as $kmod=>$vmod) {
                            $servId = $vmod["tablaId"];
                            $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' and status='activo' order by historyChangesId ASC limit 1");
                            $vmod["fechaAlta"] = $db->GetSingle();
                            $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' and status='baja' order by historyChangesId DESC limit 1");
                            $vmod["fechaBaja"] = $db->GetSingle();
                            //encontrar encargados
                            $encargados = $contractRep->encargadosCustomKey('departamentoId', 'name', $vmod['contractId']);
                            $encargados2 = $contractRep->encargadosCustomKey('departamentoId', 'personalId', $vmod['contractId']);
                            $vmod["responsable"] = $encargados[$vmod['departamentoId']];
                            $personal->setPersonalId($encargados2[$vmod["departamentoId"]]);
                            $ordeJefes = $personal->getOrdenJefes();
                            $vmod["supervisor"] = $ordeJefes["supervisor"];
                            switch ($vmod["status"]) {
                                case 'activo':
                                    $vmod["currentState"] = "Activo";
                                    break;
                                case 'readonly':
                                    $vmod["currentState"] = "Activo solo lectura";
                                    break;
                                case 'bajaParcial':
                                    $vmod["currentState"] = "Baja temporal";
                                    break;
                                case 'baja':
                                    $vmod["currentState"] = "Baja";
                                    break;
                            }
                            $registros[] = $vmod;
                        }
                    break;
                    case 'baja':
                        $ftr ="";
                        if($mes>0)
                            $ftr .=" and month(a.fecha)='$mes' ";
                        if($year>0)
                            $ftr .=" and year(a.fecha)='$year' ";
                        if(strlen($_POST["rfc"])<=0)
                            $_POST["customerId"] = "";
                        if($_POST["contractId"]){
                            //obtener los servicios de la razon s.
                            $db->setQuery("select servicioId from servicio where contractId='".$_POST["contractId"]."' ");
                            $serviciosEncontrados = $db->GetResult();
                            if(count($serviciosEncontrados)>0){
                                $idServiciosEncontrados = $util->ConvertToLineal($serviciosEncontrados,'servicioId');
                                $ftr .= " and a.servicioId IN (".implode(",",$idServiciosEncontrados).") ";
                            }else{
                                $ftr .=" and a.servicioId IN(0) ";
                            }
                        }elseif($_POST["customerId"]){
                            //obtener los servicios de la razon s.
                            $db->setQuery("select contractId from contract where customerId='".$_POST["customerId"]."' ");
                            $contratosEncontrados = $db->GetResult();
                            if(count($contratosEncontrados)>0){
                                $idContratosEncontrados = $util->ConvertToLineal($contratosEncontrados,'contractId');
                                $db->setQuery("select servicioId from servicio where contractId IN(".implode(",",$idContratosEncontrados).") ");
                                $serviciosEncontrados = $db->GetResult();
                                if(count($serviciosEncontrados)>0){
                                    $idServiciosEncontrados = $util->ConvertToLineal($serviciosEncontrados,'servicioId');
                                    $ftr .= " and a.servicioId IN (".implode(",",$idServiciosEncontrados).") ";
                                }else{
                                    $ftr .=" and a.servicioId IN(0) ";
                                }
                            }else{
                                $ftr .=" and a.servicioId IN(0) ";
                            }
                        }
                        $sql ="SELECT c.name,c.nameContact,c.contractId,b.nombreServicio,b.servicioId,b.tipoServicioId,b.inicioOperaciones,b.inicioFactura,b.periodicidad,
                                      b.departamentoId,b.status,b.lastDateWorkflow, 'Baja' as movimiento,a.status as action,a.fecha,a.personalId,a.namePerson, b.uniqueInvoice
                               FROM historyChanges a 
                               INNER JOIN (SELECT contractId,servicioId,servicio.tipoServicioId,nombreServicio,inicioOperaciones,inicioFactura,periodicidad,departamentoId,servicio.status,
                                           servicio.lastDateWorkflow, tipoServicio.uniqueInvoice
                                           FROM servicio 
                                           INNER JOIN tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId 
                                           WHERE servicio.status IN('baja','bajaParcial')) b ON a.servicioId=b.servicioId
                               INNER JOIN (SELECT contractId,contract.name,nameContact 
                                           FROM contract
                                           INNER JOIN customer ON contract.customerId=customer.customerId) c ON b.contractId=c.contractId
                               WHERE a.status IN('baja','bajaParcial') $ftr 
                               ORDER BY a.fecha DESC";
                        $db->setQuery($sql);
                        $modificaciones = $db->GetResult();
                        $registros = [];
                        foreach($modificaciones as $kmod=>$vmod) {
                            $servId = $vmod["servicioId"];
                            $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' and status='activo' order by historyChangesId ASC limit 1");
                            $vmod["fechaAlta"] = $db->GetSingle();
                            if($vmod['action']=='bajaParcial'){
                                $vmod["fechaBaja"] = $vmod["lastDateWorkflow"];
                            }else{
                                $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' and status='baja' order by historyChangesId DESC limit 1");
                                $vmod["fechaBaja"] = $db->GetSingle();
                            }

                            //encontrar encargados
                            $encargados = $contractRep->encargadosCustomKey('departamentoId', 'name', $vmod['contractId']);
                            $encargados2 = $contractRep->encargadosCustomKey('departamentoId', 'personalId', $vmod['contractId']);
                            $vmod["responsable"] = $encargados[$vmod['departamentoId']];
                            $personal->setPersonalId($encargados2[$vmod["departamentoId"]]);
                            $ordeJefes = $personal->getOrdenJefes();
                            $vmod["supervisor"] = $ordeJefes["supervisor"];
                            switch ($vmod["status"]) {
                                case 'activo':
                                    $vmod["currentState"] = "Activo";
                                    break;
                                case 'readonly':
                                    $vmod["currentState"] = "Activo solo lectura";
                                    break;
                                case 'bajaParcial':
                                    $vmod["currentState"] = "Baja temporal";
                                    break;
                                case 'baja':
                                    $vmod["currentState"] = "Baja";
                                    break;
                            }
                            $registros[] = $vmod;
                        }
                }
                $smarty->assign("registros", $registros);
                $smarty->display(DOC_ROOT.'/templates/lists/report-up-down.tpl');
            break;
        }
    case 'searchAltasBajas':
        $formValues['subordinados'] = $_POST['subordinados'];
        $formValues['respCuenta'] = $_POST['responsableCuenta'];
        $formValues['departamentoId'] = $_POST["departamentoId"];
        $formValues['statusSearch'] = $_POST["statusSearch"];
        $formValues['tipoSearch'] = $_POST["tipoSearch"];
        $formValues['sinServicios'] =true;
        $formValues['cliente'] = $_POST["rfc"];
        $idContrato = $_POST['contractId'];
        $contracts = [];
        $reportService = new ReportService();
        $results = $reportService->getAbServices();
        echo "ok[#]";
        $smarty->assign("registros", $results);
        $smarty->display(DOC_ROOT.'/templates/lists/reporte-ab-all.tpl');
    break;
}
