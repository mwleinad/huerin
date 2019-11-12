<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case 'search':
			echo 'ok[#]';
			$formValues['subordinados'] = $_POST['subordinados'];			
			$formValues['respCuenta'] = $_POST['responsableCuenta'];
			$formValues['departamentoId'] = $_POST["departamentoId"];
			$formValues['cliente'] = $_POST["rfc"];
			$idContrato = $_POST['contractId'];
			$contracts = array();
			//este archivo contiene los filtros segun sea el rol, se usa en varios apartado cambiarlos por este archivo
            include_once(DOC_ROOT.'/ajax/filter.php');
			$idClientes = array();
			$idContracts = array();
			$contratosClte = array();
			foreach($contracts as $res){
				$contractId = $res['contractId'];
				$customerId = $res['customerId'];
				if($idContrato > 0 && $contractId != $idContrato)
					continue;
				
				if(!in_array($customerId,$idClientes))
					$idClientes[] = $customerId;
				
				if(!in_array($contractId,$idContracts)){
					$idContracts[] = $contractId;				
					$contratosClte[$customerId][] = $res;
				}
			}//foreach
			$clientes = array();
			foreach($idClientes as $customerId){
				$customer->setCustomerId($customerId);
				$infC = $customer->Info();
				$infC['contracts'] = $contratosClte[$customerId];
				$clientes[] = $infC;
			}//foreach
			$totalPeriodo = 0;
			$totalCosto = 0;
			$resClientes = array();
			foreach($clientes as $clte){
				$contratos = array();
				foreach($clte['contracts'] as $con){
					//Checamos Permisos
					$resPermisos = explode('-',$con['permisos']);
					foreach($resPermisos as $res){
						$value = explode(',',$res);
						
						$idPersonal = $value[1];
						$idDepto = $value[0];
						
						$personal->setPersonalId($idPersonal);
						$nomPers = $personal->GetNameById();
						
						$permisos[$idDepto] = $nomPers;
						$permisos2[$idDepto] = $idPersonal;
					}	
										
					$servicios = array();
					foreach($con['servicios'] as $serv) {
                        $sql = 'SELECT tipoServ.departamentoId, tipoServ.costoVisual FROM servicio serv
								LEFT JOIN tipoServicio tipoServ ON tipoServ.tipoServicioId = serv.tipoServicioId
								WHERE serv.servicioId = "' . $serv['servicioId'] . '"';
                        $util->DB()->setQuery($sql);
                        $rowTipoServ = $util->DB()->GetRow();
                        $departamentoId = $rowTipoServ["departamentoId"];
                        $costoVisual = $rowTipoServ["costoVisual"];
                        $resPermisos = explode('-', $con['permisos']);
                        $personalId = 0;
                        foreach ($resPermisos as $res2) {
                            $value = explode(',', $res2);
                            $idPersonal = $value[1];
                            $idDepto = $value[0];
                            if ($idDepto == $departamentoId)
                                $personalId = $idPersonal;
                        }
                        switch($serv["servicioStatus"]){
                            case 'activo': $serv["nameStatusComplete"] = 'Activo'; break;
                            case 'readonly': $serv["nameStatusComplete"] = 'Activo/Solo Lectura'; break;
                            case 'bajaParcial': $serv["nameStatusComplete"] = 'Baja Temporal'; break;
                            case 'baja': $serv["nameStatusComplete"] = 'Baja'; break;
                        }
                        $personal->setPersonalId($personalId);
                        $infP = $personal->InfoWhitRol();
                        if(!empty($infP)){
                            $jefes = array();
                            $personal->setPersonalId($personalId);
                            $personal->deepJefesArray($jefes,true);
                            $serv["contador"] = $jefes['Contador'];
                            $serv['supervisor'] = $jefes['Supervisor'];
                            $serv['gerente'] = $jefes['Gerente'];
                            $serv['jefeMax'] = $jefes['Socio'];
                            $serv[strtolower($infP["nameLevel"])] = $jefes['me'];
                        }else {
                            $serv['auxiliar'] = 'N/E';
                            $serv['contador'] = 'N/E';
                            $serv['supervisor'] = 'N/E';
                            $serv['gerente'] = 'N/E';
                        }
						$servicio->setServicioId($serv['servicioId']);
						$infServ = $servicio->Info();
																		
						$tipoServicio->setTipoServicioId($infServ['tipoServicioId']);
						$deptoId = $tipoServicio->GetField('departamentoId');
						
						$serv['responsable'] = $permisos[$deptoId];
						$serv['costo'] = number_format($infServ['costo'],2,'.','');
						
						if($serv['periodicidad'] == 'Mensual')
							$costoMens = $infServ['costo'];
						elseif($serv['periodicidad'] == 'Anual')
							$costoMens = $infServ['costo'] / 12;
						elseif($serv['periodicidad'] == 'Bimestral')
							$costoMens = $infServ['costo'] / 2;
							
						$costoMens = number_format($costoMens,2,'.','');
						$serv['costoMens'] = number_format($costoMens,2);
						$serv['costoVisual'] = number_format($costoVisual,2);
						
						$totalPeriodo += $serv['costo'];
						$totalMens += $costoMens;
						
						$servicios[] = $serv;
						
					}//foreach
					$con['instanciasServicio'] = $servicios;
																				
					$contratos[] = $con;
					
				}//foreach
				$clte['contracts'] = $contratos;
				
				$resClientes[] = $clte;
				
			}//foreach
			$smarty->assign('totalMens', $totalMens);
			$smarty->assign('totalPeriodo', $totalPeriodo);
			$smarty->assign("clientes", $resClientes);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-ingresos.tpl');
			
		break;
    case 'searchAltasBajas':
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
                        $sql ="SELECT a.fecha,a.personalId,a.tablaId,a.fecha,b.nombreServicio,b.servicioId,b.tipoServicioId,b.inicioOperaciones,b.inicioFactura,b.periodicidad,b.departamentoId,b.contractId,b.status,c.name,c.nameContact,'$movimiento' as movimiento FROM log a 
                               INNER JOIN (select contractId,servicioId,servicio.tipoServicioId,servicio.status,nombreServicio,inicioOperaciones,inicioFactura,periodicidad,departamentoId from servicio inner join tipoServicio on servicio.tipoServicioId=tipoServicio.tipoServicioId) b ON a.tablaId=b.servicioId
                               INNER JOIN (select contractId,contract.name,nameContact from contract inner join customer on contract.customerId=customer.customerId) c ON b.contractId=c.contractId
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
                        $sql ="SELECT c.name,c.nameContact,c.contractId,b.nombreServicio,b.servicioId,b.tipoServicioId,b.inicioOperaciones,b.inicioFactura,b.periodicidad,b.departamentoId,b.status,b.lastDateWorkflow, 'Baja' as movimiento,a.status as action,a.fecha,a.personalId,a.namePerson FROM historyChanges a 
                               INNER JOIN (select contractId,servicioId,servicio.tipoServicioId,nombreServicio,inicioOperaciones,inicioFactura,periodicidad,departamentoId,servicio.status,servicio.lastDateWorkflow from servicio inner join tipoServicio on servicio.tipoServicioId=tipoServicio.tipoServicioId where servicio.status in('baja','bajaParcial')) b ON a.servicioId=b.servicioId
                               INNER JOIN (select contractId,contract.name,nameContact from contract inner join customer on contract.customerId=customer.customerId) c ON b.contractId=c.contractId
                               WHERE a.status IN('baja','bajaParcial') $ftr order by a.fecha desc";
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
}