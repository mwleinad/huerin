<?php

class ReporteBonos extends Main
{
	public function setAnio($values) {
		$this->anio = $values;
	}

	public function setMesUno($values) {
		$this->mesUno = $values;
	}

	public function setMesDos($values) {
		$this->mesDos = $values;
	}

	public function setMesTres($values) {
		$this->mesTres = $values;
	}

	public function setPersonalId($values) {
		$this->personalId = $values;
	}

	public function setDepartamentoId($values) {
		$this->departamentoId = $values;
	}
	public function DATOS_REPORTE_BONO() {
		$DATOS = $this->EnumerateSupervisor();
		$DATOS['SUPERVISOR'][0]['MES1_NAME']  = $this->Util()->MesCorto($this->mesUno);
		$DATOS['SUPERVISOR'][0]['MES2_NAME']  = $this->Util()->MesCorto($this->mesDos);
		$DATOS['SUPERVISOR'][0]['MES3_NAME']  = $this->Util()->MesCorto($this->mesTres);

		foreach ($DATOS['SUPERVISOR'] as $key1 => $row1) {
			$DATOS['SUPERVISOR'][$key1]['MES1_NAME']  = $this->Util()->MesCorto($this->mesUno);
			$DATOS['SUPERVISOR'][$key1]['MES2_NAME']  = $this->Util()->MesCorto($this->mesDos);
			$DATOS['SUPERVISOR'][$key1]['MES3_NAME']  = $this->Util()->MesCorto($this->mesTres);
			$DATOS['SUPERVISOR'][$key1]['CONTADOR']  = $this->EnumerateContador($row1['personalId']);
			foreach ($DATOS['SUPERVISOR'][$key1]['CONTADOR'] as $key2 => $row2) {
				$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['MES1_NAME']  = $this->Util()->MesCorto($this->mesUno);
				$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['MES2_NAME']  = $this->Util()->MesCorto($this->mesDos);
				$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['MES3_NAME']  = $this->Util()->MesCorto($this->mesTres);
				$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR']  = $this->EnumerateAuxiliar($row2['personalId']);
				foreach ($DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'] as $key3 => $row3) {
					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['MES1_NAME'] = $this->Util()->MesCorto($this->mesUno);
					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['MES2_NAME'] = $this->Util()->MesCorto($this->mesDos);
					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['MES3_NAME'] = $this->Util()->MesCorto($this->mesTres);
				}
			}

		}

		foreach ($DATOS['SUPERVISOR'] as $key1 => $row1) {
			$DATOS['SUPERVISOR'][$key1]['CLIENTE']  = $this->EnumerateClientes($row1['personalId']);
			foreach ($DATOS['SUPERVISOR'][$key1]['CLIENTE'] as $keyCliente => $rowCliente) {
				$DATOS['TOTAL_GENERAL']['MES1'] += $DATOS['SUPERVISOR'][$key1]['TOTAL_CLIENTE']['MES1']  += $rowCliente['MES1'];
				$DATOS['TOTAL_GENERAL']['MES2'] += $DATOS['SUPERVISOR'][$key1]['TOTAL_CLIENTE']['MES2']  += $rowCliente['MES2'];
				$DATOS['TOTAL_GENERAL']['MES3'] += $DATOS['SUPERVISOR'][$key1]['TOTAL_CLIENTE']['MES3']  += $rowCliente['MES3'];
				$DATOS['TOTAL_GENERAL']['TOTAL_MES'] += $DATOS['SUPERVISOR'][$key1]['TOTAL_CLIENTE']['TOTAL_MES']  += $rowCliente['TOTAL_MES'];
			}
			foreach ($DATOS['SUPERVISOR'][$key1]['CONTADOR'] as $key2 => $row2) {
				$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['CLIENTE']  = $this->EnumerateClientes($row2['personalId']);
				foreach ($DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['CLIENTE'] as $keyCliente => $rowCliente) {
					$DATOS['TOTAL_GENERAL']['MES1'] += $DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOTAL_CLIENTE']['MES1']  += $rowCliente['MES1'];
					$DATOS['TOTAL_GENERAL']['MES2'] += $DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOTAL_CLIENTE']['MES2']  += $rowCliente['MES2'];
					$DATOS['TOTAL_GENERAL']['MES3'] += $DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOTAL_CLIENTE']['MES3']  += $rowCliente['MES3'];
					$DATOS['TOTAL_GENERAL']['TOTAL_MES'] += $DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOTAL_CLIENTE']['TOTAL_MES']  += $rowCliente['TOTAL_MES'];

					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['MES1'] += $rowCliente['MES1'];
					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['MES2'] += $rowCliente['MES2'];
					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['MES3'] += $rowCliente['MES3'];
					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['TOTAL_MES'] += $rowCliente['TOTAL_MES'];
					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['COLOR'] = $this->rand_color();
				}
				foreach ($DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'] as $key3 => $row3) {
					$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['CLIENTE']  = $this->EnumerateClientes($row3['personalId']);
					foreach ($DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['CLIENTE'] as $keyCliente => $rowCliente) {
						$DATOS['TOTAL_GENERAL']['MES1'] += $DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['TOTAL_CLIENTE']['MES1']  += $rowCliente['MES1'];
						$DATOS['TOTAL_GENERAL']['MES2'] += $DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['TOTAL_CLIENTE']['MES2']  += $rowCliente['MES2'];
						$DATOS['TOTAL_GENERAL']['MES3'] += $DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['TOTAL_CLIENTE']['MES3']  += $rowCliente['MES3'];
						$DATOS['TOTAL_GENERAL']['TOTAL_MES'] += $DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['AUXILIAR'][$key3]['TOTAL_CLIENTE']['TOTAL_MES']  += $rowCliente['TOTAL_MES'];

						$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['MES1']  += $rowCliente['MES1'];
						$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['MES2']  += $rowCliente['MES2'];
						$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['MES3']  += $rowCliente['MES3'];
						$DATOS['SUPERVISOR'][$key1]['CONTADOR'][$key2]['TOT_CONTADOR_GEN']['TOTAL_MES']  += $rowCliente['TOTAL_MES'];
					}
				}
			}

		}

		// echo "<pre>";
		// print_r($DATOS);
		// exit();
		$INFO['DATOS'] = $DATOS;

		return $INFO;
	}
	public function EnumerateSupervisor() {

		$this->Util()->DB()->setQuery("
			SELECT * FROM
				personal
			WHERE
				tipoPersonal = 'Supervisor' AND
				personalId = '".$this->personalId."'
		");
		$result['SUPERVISOR'] = $this->Util()->DB()->GetResult();
		return $result;
	}
	public function EnumerateContador($personalId) {

		$this->Util()->DB()->setQuery("
			SELECT * FROM
				personal
			WHERE
				tipoPersonal = 'Contador' AND
				jefeSupervisor =  '".$personalId."'
		");
		$result = $this->Util()->DB()->GetResult();
		return $result;
	}
	public function EnumerateAuxiliar($personalId) {

		$this->Util()->DB()->setQuery("
			SELECT * FROM
				personal
			WHERE
				tipoPersonal = 'Auxiliar' AND
				jefeContador =  '".$personalId."'
		");
		$result = $this->Util()->DB()->GetResult();
		return $result;
	}
	public function EnumerateClientes($personalId) {

		$this->Util()->DB()->setQuery("
			SELECT
				CO.contractId,
				CU.nameContact,
				CO.nombreComercial,
				CO.responsableCuenta,
				P.name,
				P.departamentoId

			FROM
				contract CO
			INNER JOIN customer CU ON CU.customerId = CO.customerId
			LEFT JOIN personal P ON P.personalId = CO.responsableCuenta
			WHERE
				CU.active = '1' AND
				CO.activo = 'Si' AND
				CO.customerId <> '' AND
				CO.responsableCuenta = '".$personalId."' AND
				CO.responsableCuenta <> 0
				/*
				AND
				(CO.lastModified <> '0000-00-00' or	CO.lastUpdated <> '0000-00-00')
				*/
		");
		$result = $this->Util()->DB()->GetResult();
		foreach ($result as $key => $row) {
			$this->Util()->DB()->setQuery("
				SELECT
					S.*,
					T.*,

					(SELECT
						CONCAT(ISE.status,'[#]',ISE.class,'[#]',SS.costo)
					FROM
						instanciaServicio ISE
					LEFT JOIN servicio SS ON SS.servicioId = ISE.servicioId
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesUno."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS status1_class1_MES1,



					(SELECT
						CONCAT(ISE.status,'[#]',ISE.class,'[#]',SS.costo)
					FROM
						instanciaServicio ISE
					LEFT JOIN servicio SS ON SS.servicioId = ISE.servicioId
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesDos."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS status2_class2_MES2,



					(SELECT
						CONCAT(ISE.status,'[#]',ISE.class,'[#]',SS.costo)
					FROM
						instanciaServicio ISE
					LEFT JOIN servicio SS ON SS.servicioId = ISE.servicioId
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesTres."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS status3_class3_MES3


				FROM
					servicio S
				LEFT JOIN tipoServicio T ON T.tipoServicioId = S.tipoServicioId
				WHERE
					S.status = 'activo' AND
					S.contractId = '".$row['contractId']."' AND
					/* T.periodicidad = 'Mensual' AND */
					T.departamentoId = '".$row['departamentoId']."' AND
					T.departamentoId = '".$this->departamentoId."'

			");
			$result[$key]['OSWA'] = $this->Util()->DB()->GetResult();
		}
		foreach ($result as $key => $row) {
			foreach ($row['OSWA'] as $key2 => $row2) {
				$status1_class1_MES1 = explode("[#]", $row2['status1_class1_MES1']);
				$status2_class2_MES2 = explode("[#]", $row2['status2_class2_MES2']);
				$status3_class3_MES3 = explode("[#]", $row2['status3_class3_MES3']);

				$result[$key]['OSWA'][$key2]['MES1'] = $status1_class1_MES1[2];
				$result[$key]['OSWA'][$key2]['MES2'] = $status2_class2_MES2[2];
				$result[$key]['OSWA'][$key2]['MES3'] = $status3_class3_MES3[2];

				$result[$key]['OSWA'][$key2]['COLOR_MES1'] = $this->COLOR_CLASS($status1_class1_MES1[0],$status1_class1_MES1[1]);
				$result[$key]['OSWA'][$key2]['COLOR_MES2'] = $this->COLOR_CLASS($status2_class2_MES2[0],$status2_class2_MES2[1]);
				$result[$key]['OSWA'][$key2]['COLOR_MES3'] = $this->COLOR_CLASS($status3_class3_MES3[0],$status3_class3_MES3[1]);

				$result[$key]['nameServicios'] .= '* '.$row2['nombreServicio'].' <br>';

			}
		}
		foreach ($result as $key => $row) {
			foreach ($row['OSWA'] as $key2 => $row2) {

				if ($row2['COLOR_MES1'] == "stCompleto txtStCompleto") {
					$MES1 = $row2['MES1'];
				}else{
					$MES1 = 0;
				}
				if ($row2['COLOR_MES2'] == "stCompleto txtStCompleto") {
					$MES2 = $row2['MES2'];
				}else{
					$MES2 = 0;
				}
				if ($row2['COLOR_MES3'] == "stCompleto txtStCompleto") {
					$MES3 = $row2['MES3'];
				}else{
					$MES3 = 0;
				}
				$result[$key]['MES1'] += $MES1;
				$result[$key]['MES2'] += $MES2;
				$result[$key]['MES3'] += $MES3;
				$result[$key]['TOTAL_MES'] += $MES1 + $MES2 + $MES3;
			}
		}
		return $result;
	}

	function rand_color() {
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}

	function COLOR_CLASS($status,$class) {
		if ($status != 'inactiva') {
			if ($class == 'CompletoTardio') {
				$StyleClassMES = 'stCompleto txtStCompleto';
			}else{
				if ($class == 'Iniciado') {
					$StyleClassMES = 'stPorCompletar txtStPorCompletar';
				}else{
					$StyleClassMES = 'st'.$class.' txtSt'.$class;
				}
			}
		}

		return $StyleClassMES;
	}
	function createMonthBase($flag){
	    $base = [];
	    switch($flag){
            case 'efm':
                for($i=1;$i<=3;$i++)
                    $base[$i] =  [];
                break;
            case 'amj':
                for($i=4;$i<=6;$i++)
                    $base[$i] =  [];
                break;
            case 'jas':
                for($i=7;$i<=9;$i++)
                    $base[$i] =  [];
            break;
            case 'ond':
                for($i=10;$i<=12;$i++)
                    $base[$i] =  [];
            break;
            default:
                for($i=1;$i<=12;$i++)
                    $base[$i] =  [];
            break;
        }
        return $base;
    }
    function createMonthBasesFromArray($meses= []){
	    $mesesBase = [];
	    foreach($meses as $mes)
	        $mesesBase[$mes]=[];
	    return $mesesBase;
    }
    function generateReportBonosWhitLevel($ftr=[]) {
        global $personal,$contractRep,$instanciaServicio;
        $strFilter = "";
        if(strlen($_POST["like_contract_name"])>0||strlen($_POST["like_customer_name"])>0)
        {
            $like = $_POST["like_customer_name"];
            $like2 = $_POST["like_contract_name"];
            $subStr ="";
            if(strlen($like)>0){
                $subStr .= " and (b.nameContact like '%$like%' ";
                if(strlen($like2)>0)
                    $subStr .=" or a.name like '%$like2%' )";
                else
                    $subStr .=" )";
            }else{
                if(strlen($like2)>0){
                    $subStr .= " and (a.name like '%$like2%' ";
                    if(strlen($like)>0)
                        $subStr .=" or a.nameContact like '%$like%' )";
                    else
                        $subStr .=" )";
                }
            }
            $strFilter .=" and a.contractId in(select a.contractId from contract a inner join customer b on a.customerId = b.customerId where b.active='1' and a.activo='Si' $subStr )";
        }
        //filtro departamento
        if($ftr["departamentoId"])
            $strFilter .=" and b.departamentoId='".$ftr["departamentoId"]."' ";

        $mesesBase =  $this->createMonthBase($ftr['period']);

        $fullSubordinados = $personal->GetIdResponsablesSubordinados($ftr);
        $allEncargados = [];

        foreach($fullSubordinados as $subId){
            $personal->setPersonalId($subId);
            $empleado=$personal->InfoWhitRol();
            $sub['name']=$empleado['name'];
            $sub['porcentajeBono']=$empleado['porcentajeBono'];
            $sub['sueldo']=$empleado['sueldo']*3;
            $sub['totalDevengado']=0;
            $sub['totalCompletado']=0;
            $sub['level']=$empleado["nivel"];
            if($empleado['jefeInmediato'])
                $sub['jefeInmediato'] = $empleado["jefeInmediato"];
            $sub['personalId']=$subId;
            $allEncargados[$subId] = $sub;
        }
        $sqlServ = "select a.servicioId,a.contractId,a.status,b.nombreServicio,b.departamentoId ,a.inicioFactura,a.inicioOperaciones,
                    c.name, c.nameContact,a.lastDateWorkflow
                    from servicio a 
                    inner join tipoServicio b on a.tipoServicioId=b.tipoServicioId 
                    inner join (select contract.contractId, contract.name, customer.nameContact from contract
                                inner join  customer on contract.customerId = customer.customerId
                                where contract.activo = 'Si' and customer.active = '1') as c on a.contractId = c.contractId                                
                    where b.status='1' and a.status IN ('activo','bajaParcial')  $strFilter";
        $this->Util()->DB()->setQuery($sqlServ);
        $services = $this->Util()->DB()->GetResult();

        $year = $_POST["year"];
        $serviciosEncontrados = [];
        $totales = [];
        $listsEncargados = [];
        $totalesEncargados=[];
        foreach($services as $key=>$service){
            $cad = [];
            $data = [];
            $temp = [];
            $servId =$service['servicioId'];
            $contrato['contractId'] = $service["contractId"];
            $allow = $this->accessAnyContract() === '1' && $ftr['responsableCuenta'] <= 0 ? true : false;
            $encargados = $contractRep->encargadosCustomKey('departamentoId','personalId',$service['contractId']);
            if(!$allow) {
                if(in_array($encargados[$service['departamentoId']],$fullSubordinados))
                    $allow = true;
            }

            if(!$allow)
                continue;
            //encontrar instancias de servicios
            $isParcial = false;
            if ($service['status']=="bajaParcial")
                $isParcial = true;
            switch ($ftr['period']) {
                case 'efm':
                    $meses = array(1, 2, 3);
                    $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($servId, $year, $meses, $service['inicioOperaciones'], $isParcial,$mesesBase);
                    break;
                case 'amj':
                    $meses = array(4, 5, 6);
                    $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($servId, $year, $meses, $service['inicioOperaciones'], $isParcial,$mesesBase);
                    break;
                case 'jas':
                    $meses = array(7, 8, 9);
                    $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($servId, $year, $meses, $service['inicioOperaciones'], $isParcial,$mesesBase);
                    break;
                case 'ond':
                    $meses = array(10, 11, 12);
                    $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($servId, $year, $meses, $service['inicioOperaciones'], $isParcial,$mesesBase);
                    break;
                default:
                    $meses = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
                    $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($servId, $year, $meses, $service['inicioOperaciones'], $isParcial,$mesesBase);
                    break;
            }
            if(!empty($temp['instancias']) || $isParcial){
                $service['instancias'] = count($temp['instancias'])>0 ? array_replace_recursive($mesesBase, $temp['instancias']) : $mesesBase;
                $yearLastWorkflow = $isParcial ? (int)date('Y',strtotime($service['lastDateWorkflow'])) : null ;
                if($isParcial and ((int)$year >= $yearLastWorkflow)) {
                    $monthLastWorkflow =  (int) date('m', strtotime($service['lastDateWorkflow']));
                    foreach($service['instancias'] as $ki => $inst) {
                        if((int)$year === $yearLastWorkflow) {
                            $service['instancias'][$ki]['class'] = $ki > $monthLastWorkflow ? 'Parcial' : $inst['class'];
                        } else {
                            $service['instancias'][$ki]['class'] = 'Parcial';
                        }
                    }
                }
                $service['totalTrabajado'] = $temp['totalComplete'];
                $service['totalDevengado'] = $temp['totalDevengado'];
                $totales["granTotalHorizontalCompletado"] += $temp['totalComplete'];
                $totales["granTotalHorizontalDevengado"] += $temp['totalDevengado'];
            }
            else
                continue;

            $encargadoDep = $encargados[$service['departamentoId']] ? $encargados[$service['departamentoId']] : 0;
            $personal->setPersonalId($encargadoDep);
            $encargado =$personal->InfoWhitRol();
            $service["encargado"]= $encargado['name'] ? $encargado['name'] : 'Sin encargado' ;
            $service["encargadoId"]=$encargadoDep;

            $service["cliente"]=$service['nameContact'];
            $service["contrato"]=$service['name'];

            $cad['porcentajeBono']=$encargado['porcentajeBono'];
            $cad['sueldoTotal']=$encargado['sueldo']*3;
            //deep jefes
            $jefesAsc = [];
            $personal->setPersonalId($encargadoDep);
            $personal->deepJefesAssoc($jefesAsc,true);
            $service["nivel"]  = strtolower($jefesAsc["me"]["nameLevel"]);
            $socio = isset($jefesAsc["Socio"])?$jefesAsc["Socio"]["personalId"]:0;
            $gerente = isset($jefesAsc["Gerente"])?$jefesAsc["Gerente"]["personalId"]:0;
            $subgerente = isset($jefesAsc["Subgerente"])?$jefesAsc["Subgerente"]["personalId"]:0;
            $supervisor = isset($jefesAsc["Supervisor"])?$jefesAsc["Supervisor"]["personalId"]:0;
            $contador = isset($jefesAsc["Contador"])?$jefesAsc["Contador"]["personalId"]:0;
            $auxiliar = isset($jefesAsc["Auxiliar"])?$jefesAsc["Auxiliar"]["personalId"]:0;
            switch($encargado["nivel"]){
                case 1:
                    $serviciosEncontrados[$encargadoDep]["propios"][]=$service;
                    $serviciosEncontrados[$encargadoDep]["propios"] = $this->Util()->orderMultiDimensionalArray($serviciosEncontrados[$encargadoDep]["propios"],"contrato");

                    if(!is_array($serviciosEncontrados[$encargadoDep]['contratos']))
                        $serviciosEncontrados[$encargadoDep]['contratos'] = [];

                    if(!in_array($contrato['contractId'],$serviciosEncontrados[$encargadoDep]['contratos'])) {
                        $serviciosEncontrados[$encargadoDep]['contratos'][] = $contrato['contractId'];
                        $totales["totalEmpresas"]++;
                    }

                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                        if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['level']=1;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;

                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }
                    }
                break;
                case 2://gerente
                    $serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['propios'][]=$service;
                    $serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['propios'] =
                        $this->Util()->orderMultiDimensionalArray($serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['propios'],"contrato");

                    if(!is_array($serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['contratos']))
                        $serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['contratos'] = [];

                    if(!in_array($contrato['contractId'],$serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['contratos'])){
                        $serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['contratos'][]=$contrato['contractId'];
                        $totales["totalEmpresas"]++;
                    }

                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$socio]["subordinados"][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                        if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['porcentajeBono']=$encargado['porcentajeBono'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$encargado["jefeInmediato"];
                            $cad['level']=2;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;
                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }

                    }
                break;
                case 3: //subgerente
                    $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['propios'][]=$service;
                    $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['propios'] =
                        $this->Util()->orderMultiDimensionalArray($serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['propios'],"contrato");

                    if(!is_array($serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['contratos']))
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['contratos'] = [];

                    if(!in_array($contrato['contractId'],$serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['contratos'])){
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['contratos'][]=$contrato['contractId'];
                        $totales["totalEmpresas"]++;
                    }

                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                        if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['porcentajeBono']=$encargado['porcentajeBono'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$encargado["jefeInmediato"];;
                            $cad['level']=3;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;
                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }
                    }
                break;
                case 4://supervisor
                    $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['propios'][]=$service;
                    $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['propios'] =
                        $this->Util()->orderMultiDimensionalArray($serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['propios'],"contrato");

                    if(!is_array($serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['contratos']))
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['contratos'] = [];

                    if(!in_array($contrato['contractId'],$serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['contratos'])){
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['contratos'][]=$contrato['contractId'];
                        $totales["totalEmpresas"]++;
                    }

                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                       if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['porcentajeBono']=$encargado['porcentajeBono'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$encargado["jefeInmediato"];;
                            $cad['level']=4;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;
                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }
                    }
                break;
                case 5://contador
                    $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]['propios'][]=$service;
                    $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]['propios'] =
                        $this->Util()->orderMultiDimensionalArray($serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]['propios'],"contrato");

                    if(!is_array($serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]["contratos"]))
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]["contratos"]=[];

                    if(!in_array($contrato["contractId"],$serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]["contratos"])){
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]["contratos"][]=$contrato["contractId"];
                        $totales["totalEmpresas"]++;
                    }
                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                        if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$encargado["jefeInmediato"];;
                            $cad['level']=5;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;
                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }
                    }
                break;
                case 6://auxiliar
                    $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]['propios'][]=$service;
                    $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]['propios']=
                        $this->Util()->orderMultiDimensionalArray($serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]['propios'],"contrato");

                    if(!is_array($serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]["contratos"]))
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]["contratos"]=[];

                    if(!in_array($contrato["contractId"],$serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]["contratos"])){
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]["contratos"][]=$contrato["contractId"];
                        $totales["totalEmpresas"]++;
                    }
                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$socio]['subordinados'][$gerente]['subordinados'][$subgerente]["subordinados"][$supervisor]["subordinados"][$contador]["subordinados"][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                        if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$encargado["jefeInmediato"];;
                            $cad['level']=6;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;
                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }
                    }
                break;
            }
        }
        $data["serviciosEncontrados"] = $serviciosEncontrados;
        $data["totales"] = $totales;
        $data["totalesEncargados"] = $totalesEncargados;
        $newArray = [];
        $totalesEncargados = $this->Util()->orderMultiDimensionalArray($totalesEncargados,'level',true,true);
        foreach($totalesEncargados as $ke=>$enc){
            if(!array_key_exists($enc['personalId'],$newArray)){
                $newArray[$enc["personalId"]] = $totalesEncargados[$ke];
                $this->recursiveTotalEncargado($allEncargados,$newArray, $totalesEncargados[$ke],$enc['totalDevengado'],$enc['totalCompletado'],$enc['sueldoTotal']);
            }
            else{
                $newArray[$enc["personalId"]]['totalDevengado']  += $enc['totalDevengado'];
                $newArray[$enc["personalId"]]['totalCompletado'] += $enc['totalCompletado'];
                $newArray[$enc["personalId"]]['sueldoTotal'] += $enc['sueldoTotal'];

                if($newArray[$enc["personalId"]]["jefeInmediato"]){
                    $this->recursiveTotalEncargado($allEncargados,$newArray, $totalesEncargados[$ke],$enc['totalDevengado'],$enc['totalCompletado'],$enc['sueldoTotal']);
                }
            }
        }
        $ordenado = $this->Util()->orderMultiDimensionalArray($newArray,'level',false,true);
        $data["totalesEncargadosAcumulado"] = $ordenado;
        return $data;
    }
    function recursiveTotalEncargado($allEncargados,&$newArray,$value,$acumDevengado,$acumCompletado,$sueldo){
        $this->Util()->DB()->setQuery("select b.nivel from personal a inner join roles b on a.roleId=b.rolId where personalId = '" . $value['jefeInmediato'] . "' ");
        $level = $this->Util()->DB()->GetSingle();

        if (!$value['jefeInmediato'] || $level <= 1 || !array_key_exists($value["jefeInmediato"], $allEncargados))
            return;

        if (array_key_exists($value['jefeInmediato'], $newArray)) {
            $newArray[$value['jefeInmediato']]['totalDevengado']  += $acumDevengado;
            $newArray[$value['jefeInmediato']]['totalCompletado'] += $acumCompletado;
            $newArray[$value['jefeInmediato']]['sueldoTotal'] += $sueldo;
            if ($newArray[$value['jefeInmediato']]['jefeInmediato']) {
                $this->recursiveTotalEncargado($allEncargados, $newArray, $newArray[$value['jefeInmediato']], $acumDevengado, $acumCompletado, $sueldo);
            }
        } else {
            $newArray[$value["jefeInmediato"]] = $allEncargados[$value["jefeInmediato"]];
            $newArray[$value['jefeInmediato']]['totalDevengado']  += $acumDevengado;
            $newArray[$value['jefeInmediato']]['totalCompletado'] += $acumCompletado;
            $newArray[$value['jefeInmediato']]['sueldoTotal'] += $sueldo;
            $sueldo = $newArray[$value['jefeInmediato']]["sueldo"] + $sueldo;
            if ($newArray[$value['jefeInmediato']]['jefeInmediato']) {
                $this->recursiveTotalEncargado($allEncargados, $newArray, $newArray[$value['jefeInmediato']], $acumDevengado, $acumCompletado, $sueldo);
            }
        }
    }
    function generarMesesAconsultar($tipoPeriodo,$periodo){
	    $meses = [];
	    switch($tipoPeriodo){
            case 'mensual':
                if($periodo)
                    $meses = [$periodo];
                else
                    $meses = [1,2,3,4,5,6,7,8,9,10,11,12];
            break;
            case 'trimestral':
                switch($periodo){
                    case 'efm': $meses = [1,2,3];break;
                    case 'amj': $meses = [4,5,6];break;
                    case 'jas': $meses = [7,8,9];break;
                    case 'ond': $meses = [10,11,12];break;
                    default:$meses = $meses = [1,2,3,4,5,6,7,8,9,10,11,12];break;
                }
            break;
        }
        return $meses;
    }
    function generateEstadoResultado($ftr=[]){
	    global $contractRep,$instanciaServicio,$personal;
	    $strFilter ="";
        if($ftr["responsableCuenta"])
            $strFilter .= " and a.personalId = '".$ftr['responsableCuenta']."' ";

        $sql = "select a.* from personal a inner join roles b on a.roleId = b.rolId where b.nivel = 2 $strFilter order by a.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();
        //encontrar los meses
        $meses = $this->generarMesesAconsultar($_POST["tipoPeriodo"],$_POST["period"]);
        $countMonth =  count($meses);
        $mesesBase =  $this->createMonthBasesFromArray($meses);
        $year = $ftr["year"];
        $devengados = [];
        $trabajados = [];
        $control = [];
        foreach($gerentes as $key=>$value){

            $stackSubordinados = [];
            $detalleSubordinados = [];
            $ftr["departamentoId"] = $value["departamentoId"];
            $ftr["responsableCuenta"] = $value["personalId"];
            $ftr['deep'] = 1;

            $subordinados = $personal->GetIdResponsablesSubordinados($ftr);
            $totalSueldoIncluidoSubordinados  = $personal->getTotalSalarioByMultipleId($subordinados);
            $contratos = $contractRep->getContracts($ftr,false);
            if(count($contratos)<=0){
                unset($gerentes[$key]);
                continue;
            }
            $contratos = $this->Util()->ConvertToLineal($contratos,"contractId");
            $strFilter = "";
            if($ftr["departamentoId"])
                $strFilter .=" AND d.departamentoId='".$ftr["departamentoId"]."' ";

            $strFilter .= " AND a.contractId IN(".implode(',',$contratos).") ";

            $sql = "SELECT a.servicioId,a.status,a.inicioFactura,a.inicioOperaciones,a.lastDateWorkflow,b.contractId,b.name,d.nombreServicio,d.departamentoId FROM servicio a 
                    INNER JOIN (SELECT contract.contractId,contract.name,contract.activo,customer.active from  contract INNER JOIN customer ON contract.customerId=customer.customerId WHERE customer.active='1' AND contract.activo='Si') b ON a.contractId=b.contractId
                    INNER JOIN contractPermiso c ON a.contractId=c.contractId
                    INNER JOIN tipoServicio d ON a.tipoServicioId=d.tipoServicioId
                    WHERE a.status IN ('activo','bajaParcial') AND d.status='1' and a.inicioFactura!='0000-00-00' $strFilter group by a.servicioId";
            $this->Util()->DB()->setQuery($sql);
            $servicios = $this->Util()->DB()->GetResult();
            if(count($servicios)<=0){
                unset($gerentes[$key]);
                continue;
            }
            $totalDevengadoGerente = 0;
            $totalTrabajadoGerente = 0;

            foreach($servicios as $ks=>$serv) {
                $temp = [];
                $isParcial = false;

                if ($serv['status'] == "bajaParcial")
                    $isParcial = true;

                $encargados = $contractRep->encargadosCustomKey('departamentoId','personalId',$serv['contractId']);
                if(!in_array($encargados[$serv['departamentoId']],$subordinados))
                    continue;


                $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($serv['servicioId'], $year, $meses, $serv['inicioOperaciones'], $isParcial,$mesesBase);

                if(empty($temp)||empty($temp["instancias"]))
                    continue;

                if(!in_array($encargados[$serv['departamentoId']],$stackSubordinados)){
                    array_push($stackSubordinados,$encargados[$serv["departamentoId"]]);
                    $personal->setPersonalId($encargados[$serv['departamentoId']]);
                    $subordinado = $personal->InfoWhitRol();
                    $subordinado["totalDevengado"] +=$temp["totalDevengado"];
                    $subordinado["totalCompletado"] +=$temp["totalComplete"];
                    $subordinado["sueldoTotal"] = $subordinado["sueldo"]*$countMonth;


                    $detalleSubordinados[$encargados[$serv['departamentoId']]]=$subordinado;
                }else{
                    $detalleSubordinados[$encargados[$serv['departamentoId']]]["totalDevengado"]+=$temp["totalDevengado"];
                    $detalleSubordinados[$encargados[$serv['departamentoId']]]["totalCompletado"]+=$temp["totalComplete"];
                }

                $totalDevengadoGerente += $temp['totalDevengado'];
                $totalTrabajadoGerente += $temp['totalComplete'];
            }

            $allEncargados = [];

            foreach($subordinados as $subId){
                $personal->setPersonalId($subId);
                $empleado=$personal->InfoWhitRol();
                $empleado["sueldoTotal"]=$empleado["sueldo"]*$countMonth;
                $empleado["sueldo"] =$empleado["sueldo"]*$countMonth;
                $empleado['totalDevengado']=0;
                $empleado['totalCompletado']=0;
                $allEncargados[$subId] = $empleado;
            }

            $newArray = [];
            $detalleSubordinados = $this->Util()->orderMultiDimensionalArray($detalleSubordinados,'nivel',true,true);

            foreach($detalleSubordinados as $ke=>$enc){
                if(!array_key_exists($enc['personalId'],$newArray)){
                    $newArray[$enc["personalId"]] = $detalleSubordinados[$ke];
                    if($detalleSubordinados[$ke]["jefeInmediato"])
                        $this->recursiveTotalEncargado($allEncargados,$newArray, $detalleSubordinados[$ke],$enc['totalDevengado'],$enc['totalCompletado'],$enc['sueldoTotal']);
                }
                else{
                    $newArray[$enc["personalId"]]['totalDevengado']  += $enc['totalDevengado'];
                    $newArray[$enc["personalId"]]['totalCompletado'] += $enc['totalCompletado'];

                    if($newArray[$enc["personalId"]]["jefeInmediato"])
                        $this->recursiveTotalEncargado($allEncargados,$newArray, $detalleSubordinados[$ke],$enc['totalDevengado'],$enc['totalCompletado'],$enc['sueldoTotal']);

                }

            }

            $ordenado = $this->Util()->orderMultiDimensionalArray($newArray,'nivel',false,true);
            $personal->setPersonalId($value["personalId"]);
            $currentGerente=$personal->InfoWhitRol();
            $gerentes[$key]["porcentajeBono"] = $currentGerente["porcentajeBono"];
            $gerentes[$key]["totalDevengado"] = $totalDevengadoGerente;
            $gerentes[$key]["totalCompletado"] = $totalTrabajadoGerente;
            $gerentes[$key]["devengados"] = $devengados;
            $gerentes[$key]["trabajados"] = $trabajados;
            $gerentes[$key]["sueldoTotalConSub"] = $totalSueldoIncluidoSubordinados*$countMonth;
            $gerentes[$key]["detalleSubordinados"] = $ordenado;
        }

        return $gerentes;
    }
    function edoResult(array $ftr){
        global $contractRep, $instanciaServicio, $personal, $monthsInt, $contract;
        $strFilter ="";
        if($ftr["responsableCuenta"])
            $strFilter .= " and a.personalId = '".$ftr['responsableCuenta']."' ";

        $sql = "select a.*, b.nivel,c.departamento from personal a 
                inner join roles b on a.roleId = b.rolId 
                inner join departamentos c on a.departamentoId = c.departamentoId where b.nivel = 2 $strFilter order by c.departamento ASC,a.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();
        $meses = $this->generarMesesAconsultar($_POST["tipoPeriodo"],$_POST["period"]);
        $mesesBase =  $this->createMonthBasesFromArray($meses);
        $headerMeses = [];
        foreach($mesesBase as $km=>$month)
            $headerMeses[$km]['name'] = $monthsInt[$km] ;
        //formar los headers.
        $year = $ftr["year"];
        $devengados = [];
        $trabajados = [];
        $cobrados = [];
        $nominas = [];
        $utilidades = [];
        $control = [];
        foreach($gerentes as $key=>$value){
            $ftr["departamentoId"] = $value["departamentoId"];
            $ftr["responsableCuenta"] = $value["personalId"];
            $ftr['deep'] = isset($ftr['deep']);

            $subordinados = $personal->GetIdResponsablesSubordinados($ftr);
            $totalSueldoIncluidoSubordinados  = $personal->getTotalSalarioByMultipleId($subordinados);
            $ftr['subordinados'] = $subordinados;
            $ftr['tipos'] = 'activos';
            $contratos = $contract->Suggest($ftr);
            if(count($contratos)<=0){
                unset($gerentes[$key]);
                continue;
            }
            $contratos = $this->Util()->ConvertToLineal($contratos,"contractId");
            $strFilter = "";
            if($ftr["departamentoId"])
                $strFilter .=" AND d.departamentoId='".$ftr["departamentoId"]."' ";

            $strFilter .= " AND a.contractId IN(".implode(',',$contratos).") ";

            $sql = "SELECT a.servicioId,a.status,a.inicioFactura,a.inicioOperaciones,a.lastDateWorkflow,b.contractId,b.name,d.nombreServicio,d.departamentoId FROM servicio a 
                    INNER JOIN (SELECT contract.contractId,contract.name,contract.activo,customer.active from  contract INNER JOIN customer ON contract.customerId=customer.customerId WHERE customer.active='1' AND contract.activo='Si') b ON a.contractId=b.contractId
                    INNER JOIN tipoServicio d ON a.tipoServicioId=d.tipoServicioId
                    WHERE a.status IN ('activo','bajaParcial') AND d.status='1' and a.inicioFactura!='0000-00-00' $strFilter group by a.servicioId";
            $this->Util()->DB()->setQuery($sql);
            $servicios = $this->Util()->DB()->GetResult();
            if(count($servicios)<=0){
                unset($gerentes[$key]);
                continue;
            }
            foreach($servicios as $ks=>$serv) {
                $temp = [];
                $isParcial = false;
                if ($serv['status'] == "bajaParcial")
                    $isParcial = true;

                $encargados = $contractRep->encargadosCustomKey('departamentoId','personalId',$serv['contractId']);
                if(!in_array($encargados[$serv['departamentoId']],$subordinados))
                    continue;

                $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($serv['servicioId'], $year, $meses, $serv['inicioOperaciones'], $isParcial,$mesesBase);
                if(empty($temp)||empty($temp["instancias"]))
                    continue;

                if(!in_array($value['personalId'], $control)){
                    array_push($control,$value['personalId']);
                    $value['meses'] = [];
                    $devengados[$value['personalId']] =$value;
                    $trabajados[$value['personalId']] =$value;
                    $cobrados[$value['personalId']] =$value;
                    $nominas[$value['personalId']] =$value;
                    $utilidades[$value['personalId']] =$value;
                }
                foreach ($temp['instancias'] as $i => $inst){
                    $devengados[$value['personalId']]['meses'][$i]['total'] +=$inst['costo'];
                    $trabajados[$value['personalId']]['meses'][$i]['total'] +=$inst['completado'];
                    $cobrados[$value['personalId']]['meses'][$i]['total'] +=$inst['cobrado'];
                    $nominas[$value['personalId']]['meses'][$i]['total'] = $totalSueldoIncluidoSubordinados;
                    $utilidades[$value['personalId']]['meses'][$i] = [];
                }
            }
        }
        $data['devengados'] = $devengados;
        $data['trabajados'] = $trabajados;
        $data['cobrados'] = $cobrados;
        $data['nominas'] = $nominas;
        $data['utilidades'] = $utilidades;
        $data['headers'] = $headerMeses;
        return $data;
    }
    function generateReportBonosJuridico($contracts = [],$POST=[]){
        global $personal,$contractRep;
        $data = [];
        $fullSubordinados = $personal->GetIdResponsablesSubordinados($POST);
        $allEncargados = [];
        foreach($fullSubordinados as $subId){
            $personal->setPersonalId($subId);
            $empleado=$personal->InfoWhitRol();
            $allEncargados[$subId] = $empleado;
            $allEncargados[$subId]["totalDevengado"] = 0;
            $allEncargados[$subId]["totalCompletado"] = 0;
        }
        $year = $_POST["year"];
        $period = $_POST['periodo'];
        switch($period){
            case 'efm':
                $monthNames = array("Ene", "Feb", "Mar");
                $inicio = 1;
                $fin = 3;;
                break;
            case 'amj':
                $monthNames = array("Abr", "May", "Jun");
                $inicio = 4;
                $fin = 6;
                break;
            case 'jas':
                $monthNames = array("Jul", "Ago", "Sep");
                $inicio = 7;
                $fin = 9;
                break;
            case 'ond':
                $monthNames = array("Oct", "Nov", "Dic");
                $inicio = 10;
                $fin = 12;
                break;
            default:
                $monthNames = array("Ene", "Feb", "Mar","Abr", "May", "Jun","Jul", "Ago", "Sep","Oct", "Nov", "Dic");
                $inicio = 1;
                $fin = 12;
                break;
        }
        $base = $this->createMonthBase($period);
        $contratos = [];
        $idsEncargados = [];
        $totDevVerXEncargado= [];
        $totCompVerXEncargado = [];
        foreach($contracts as $key => $contrato) {
            $this->Util()->erase_val($base);
            $base2 = $base;
            $encargados = $contractRep->encargadosCustomKey('departamentoId','personalId',$contrato['contractId']);
            $cad = [];
            if(!$encargados[21]){
                $cad["eadministracion"] = 0;

            }
            else
                $cad["eadministracion"] = $encargados[21];

            $cad['contractId'] = $contrato['contractId'];
            $cad['customer'] = $contrato['nameContact'];
            $cad['razon'] = $contrato['name'];
            if(!in_array($cad["eadministracion"],$idsEncargados)){
                array_push($idsEncargados,$cad["eadministracion"]);
                $rowDevTotalXencargados[$cad["eadministracion"]] = $base;
                $rowCobTotalXencargados[$cad["eadministracion"]] = $base;
                $totDevVerXEncargado[$cad["eadministracion"]] = 0;
                $totCompVerXEncargado[$cad["eadministracion"]] = 0;
                if($cad["eadministracion"]){
                    $personal->setPersonalId($cad["eadministracion"]);
                    $totalesAcumuladosEncargados[$cad["eadministracion"]] = $personal->InfoWhitRol();
                    $totalesAcumuladosEncargados[$cad["eadministracion"]]["totalDevengado"] = 0;
                    $totalesAcumuladosEncargados[$cad["eadministracion"]]["totalCompletado"] = 0;
                    $totalesAcumuladosEncargados[$cad["eadministracion"]]["sueldoTotal"] = $totalesAcumuladosEncargados[$cad["eadministracion"]]["sueldo"];

                }else{
                    $totalesAcumuladosEncargados[$cad["eadministracion"]]["name"] ="Sin encargado";
                    $totalesAcumuladosEncargados[$cad["eadministracion"]]["nivel"] =100;
                    $totalesAcumuladosEncargados[$cad["eadministracion"]]["totalDevengado"] = 0;
                    $totalesAcumuladosEncargados[$cad["eadministracion"]]["totalCompletado"] = 0;
                    $totalesAcumuladosEncargados[$cad["eadministracion"]]["sueldoTotal"] = 0;
                }
            }
            $sql ="select sum(a.total) as total,sum(b.amount) as amount,month(a.fecha) as mes
                   from comprobante a 
                   left join (select comprobanteId,sum(amount) as amount from payment where paymentStatus='activo' group by comprobanteId ) b on a.comprobanteId=b.comprobanteId
                   inner join contract c on a.userId=c.contractId and c.activo='Si'
                   where month(a.fecha) >='$inicio' and month(a.fecha)<='$fin' and year(a.fecha)='$year' 
                   and a.userId='".$contrato['contractId']."' and a.tiposComprobanteId in(1)  and a.status ='1' 
                   group by month(a.fecha) order by month(a.fecha) desc";

            $this->Util()->DB()->setQuery($sql);
            $facturas = $this->Util()->DB()->GetResult();

            $totalAcobrarXcontrato = 0;
            $totalCobradoXcontrato = 0;
            if(!empty($facturas)){
                foreach($facturas as $fact){
                    $totalAcobrarXcontrato =$totalAcobrarXcontrato + $fact["total"];
                    $totalCobradoXcontrato =$totalCobradoXcontrato + $fact["amount"];
                    $saldo = $fact["total"]-$fact["amount"];
                    if($saldo>0.1 && $fact["amount"]>0)
                        $class = "pendiente";
                    elseif($saldo>0.1&&$fact["amount"]<=0)
                        $class= "sinabonos";
                    elseif($saldo<=0.1)
                        $class = "pagado";
                    $rowDevTotalXencargados[$cad["eadministracion"]][$fact["mes"]] = $rowDevTotalXencargados[$cad["eadministracion"]][$fact["mes"]]+ $fact["total"];
                    $rowCobTotalXencargados[$cad["eadministracion"]][$fact["mes"]] = $rowCobTotalXencargados[$cad["eadministracion"]][$fact["mes"]]+ $fact["amount"];
                    $totalesAcumuladosEncargados[$encargados[21]]["totalDevengado"] += $fact["total"];
                    $totalesAcumuladosEncargados[$encargados[21]]["totalCompletado"] += $fact["amount"];

                    $totDevVerXEncargado[$cad["eadministracion"]] +=$fact["total"];
                    $totCompVerXEncargado[$cad["eadministracion"]] += $fact["amount"];

                    $fact["class"] = $class;
                    $base2[$fact["mes"]] = $fact;
                }
                $totales = [];
                $totales['isColTotal'] = true;
                $totales['total'] = $totalAcobrarXcontrato;
                $base2[13] = $totales;
                $totales = [];
                $totales['isColTotal'] = true;
                $totales['total'] = $totalCobradoXcontrato;
                $base2[14] = $totales;
                $totales = [];
                $totales['isColTotal'] = true;
                $totales['total'] = $totalAcobrarXcontrato-$totalCobradoXcontrato;
                $base2[15] = $totales;
                $cad['facturas'] = $base2;
                $contratos[]=$cad;
            }


        }
        $stackEncargados = [];
        $groupByEncargados = [];
        foreach($contratos as $kc=>$con){
            $card = [];
            if(!in_array($con["eadministracion"],$stackEncargados)){
                array_push($stackEncargados,$con["eadministracion"]);
                if($con["eadministracion"]){
                    $personal->setPersonalId($con["eadministracion"]);
                    $responsable = $personal->InfoWhitRol();
                    $card = $responsable;
                }else{
                    $card["responsable"] = "Sin encargado";
                }
                $card["contratos"][] = $con;
                $groupByEncargados[$con["eadministracion"]] = $card;
            }else {
                $groupByEncargados[$con["eadministracion"]]["contratos"][] = $con;
            }
        }
        $newArray = [];
        $totalesAcumuladosEncargados = $this->Util()->orderMultiDimensionalArray($totalesAcumuladosEncargados,'nivel',true,true);
        foreach($totalesAcumuladosEncargados as $ke=>$enc){
            if(!array_key_exists($enc['personalId'],$newArray)){
                $newArray[$enc["personalId"]] = $totalesAcumuladosEncargados[$ke];
            }
            else{
                $newArray[$enc["personalId"]]['totalDevengado']  += $enc['totalDevengado'];
                $newArray[$enc["personalId"]]['totalCompletado'] += $enc['totalCompletado'];
                $newArray[$enc["personalId"]]['sueldoTotal'] += $enc['sueldoTotal'];
            }
            //si tiene jefe inmediato se suma el total del sub al jefe
            $this->recursiveTotalEncargado($allEncargados,$newArray, $totalesAcumuladosEncargados[$ke],$enc['totalDevengado'],$enc['totalCompletado'],$enc['sueldoTotal']);
        }
        $ordenado = $this->Util()->orderMultiDimensionalArray($newArray,'nivel',false,true);

        $data["meses"]= $monthNames;
        $data["rowDevTotal"]=$rowDevTotalXencargados;
        $data["rowCobTotal"]=$rowCobTotalXencargados;
        $data["items"] = $groupByEncargados;
        $data["totalesAcumulados"]=$ordenado;
        $data["totDevVerXEncargado"]=$totDevVerXEncargado;
        $data["totCompVerXEncargado"]=$totCompVerXEncargado;
        return $data;
    }
}
?>
