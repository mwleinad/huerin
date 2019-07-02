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
	function generateReportBonos($ftr=[]){
	    global $contractRep,$instanciaServicio,$customer,$personal;
        //encargados del filtro
        $fullSubordinados = $personal->GetIdResponsablesSubordinados($ftr);
	    $year = $_POST['year'];
	    $totalContratos =  $customer->getTotalContratosInPlatform();

        $mesesBase =  $this->createMonthBase($ftr['period']);
	    $contratos = $contractRep->getContracts($ftr,true);

       if(isset($ftr['deep']) || isset($ftr['subordinados']))
            $data['subordinados'] = 1;//sirve para controlar texto en el reporte resultado.
       if($ftr['responsableCuenta'])
       {
           $personal->setPersonalId($ftr['responsableCuenta']);
           $responsable = $personal->Info();
           $data['responsable'] = $responsable['name'];
       }
	    $totalContratosAsignados = count($contratos);
        $granTotalContabilidad = 0;
        $granTotalCobranza = 0;
        $idDepsGeneral = [];
        $totalXdepartamento= [];
        $totalXencargados = [];
        $idEncargados = [];
        $idDepsCobranza = [];
        $totalesCobranzaXdep = [];
	    foreach($contratos as $key => $value){
            if(!isset($value['servicios']))
            {
                unset($contratos[$key]);
                continue;
            }
            if(count($value['servicios'])<=0){
                unset($contratos[$key]);
                continue;
            }
            $serviciosFiltrados = [];
            $encargados = $contractRep->encargadosCustomKey('departamentoId','name',$value['contractId']);
            $encargados2 = $contractRep->encargadosCustomKey('departamentoId','personalId',$value['contractId']);
            $rowCobranza = [];
            $sumTotalCobranza = 0;
            foreach($value['servicios'] as $ks=>$serv) {
               $sumaTotalDevengado=0;
               $sumaTotalTrabajado=0;
               $serv['instancias'] = [];
               $isParcial = false;
               if ($serv['status']=="bajaParcial")
                   $isParcial = true;

               $serv['responsable'] = $encargados[$serv['departamentoId']];
                switch ($ftr['period']) {
                   case 'efm':
                       $meses = array(1, 2, 3);
                       $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($serv['servicioId'], $year, $meses, $serv['inicioOperaciones'], $isParcial,$mesesBase);
                       //obtengamos por cada servicio  su total cobranza por trimestre
                       $cobranza = $instanciaServicio->getCobranzaByServicio($serv['servicioId'], $year, $meses,$mesesBase);
                   break;
                   case 'amj':
                       $meses = array(4, 5, 6);
                       $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($serv['servicioId'], $year, $meses, $serv['inicioOperaciones'], $isParcial,$mesesBase);
                       $cobranza = $instanciaServicio->getCobranzaByServicio($serv['servicioId'], $year, $meses,$mesesBase);
                   break;
                   case 'jas':
                       $meses = array(7, 8, 9);
                       $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($serv['servicioId'], $year, $meses, $serv['inicioOperaciones'], $isParcial,$mesesBase);
                       $cobranza = $instanciaServicio->getCobranzaByServicio($serv['servicioId'], $year, $meses,$mesesBase);
                   break;
                   case 'ond':
                       $meses = array(10, 11, 12);
                       $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($serv['servicioId'], $year, $meses, $serv['inicioOperaciones'], $isParcial,$mesesBase);
                       $cobranza = $instanciaServicio->getCobranzaByServicio($serv['servicioId'], $year, $meses,$mesesBase);
                   break;
                   default:
                       $meses = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
                       $temp = $instanciaServicio->getBonoInstanciaWhitInvoice($serv['servicioId'], $year, $meses, $serv['inicioOperaciones'], $isParcial,$mesesBase);
                       $cobranza = $instanciaServicio->getCobranzaByServicio($serv['servicioId'], $year, $meses,$mesesBase);
                   break;
               }
                if(isset($temp['instancias'])){
                    if(!empty($temp['instancias']))
                        $serv['instancias'] = array_replace_recursive($mesesBase, $temp['instancias']);

                 $sumaTotalDevengado=$temp['totalDevengado'];
                 $sumaTotalTrabajado= $temp['totalComplete'];
                }else{
                    if(!$ftr["whitoutWorkflow"])
                        continue;
                    else
                        $serv['instancias'] = $mesesBase;
                }
                //totalizar por encargados de area
                if(!in_array($encargados2[$serv['departamentoId']],$idEncargados)){
                    if(!$encargados2[$serv['departamentoId']])
                    {
                        $keyEncargado = 000000;
                        array_push($idEncargados,000000);
                        $nameEncargado = 'SIN ENCARGADO ASIGNADO';
                    }
                    else
                    {
                        array_push($idEncargados,$encargados2[$serv['departamentoId']]);
                        $nameEncargado = $encargados[$serv['departamentoId']];
                        $keyEncargado = $encargados2[$serv['departamentoId']];
                    }
                    $tXe['name'] =  $nameEncargado;
                    $tXe['total'] = 0;
                    $tXe['total'] = (double)$sumaTotalTrabajado;
                    $totalXencargados[$keyEncargado] = $tXe;
                 }else{
                    if(!$encargados2[$serv['departamentoId']])
                        $keyEncargado = 000000;
                    else
                        $keyEncargado = $encargados2[$serv['departamentoId']];

                    $totalXencargados[$keyEncargado]['total'] += (double)$sumaTotalTrabajado;
                }
                //sumar el total al jefe del encargado actual, si lo tiene.
                $personal->setPersonalId($keyEncargado);
                $jefe = $personal->jefeInmediato();
                if($jefe['personalId']>0 && in_array($jefe["personalId"],$fullSubordinados)){
                    if(!in_array($jefe['personalId'],$idEncargados)){
                        array_push($idEncargados,$jefe['personalId']);
                        $nameJefe = $jefe["name"];
                        $keyJefe =$jefe["personalId"];
                        $totJefe['name'] =  $nameJefe;
                        $totJefe['total'] = 0;
                        $totJefe['total'] = (double)$sumaTotalTrabajado;
                        $totalXencargados[$keyJefe] = $totJefe;
                    }else{
                        $keyJefe =$jefe["personalId"];
                        $totalXencargados[$keyJefe]['total'] += (double)$sumaTotalTrabajado;
                    }

                }
                Departamentos::setDepartamentoId($serv['departamentoId']);
                $nameDepartamento =Departamentos::Info()['departamento'];

               if(!in_array($serv['departamentoId'],$idDepsGeneral)){
                   array_push($idDepsGeneral,$serv['departamentoId']);
                   $tXd['departamento'] =$nameDepartamento;
                   $tXd['total'] = 0;
                   $tXd['total'] = (double)$sumaTotalTrabajado;
                   $totalXdepartamento[$serv['departamentoId']] = $tXd;
               }else{
                   $totalXdepartamento[$serv['departamentoId']]['total'] += (double)$sumaTotalTrabajado;
               }
               $serv['sumatotal'] = $sumaTotalTrabajado;
               $serviciosFiltrados[]= $serv;
               $granTotalContabilidad +=(double)$sumaTotalDevengado;
               //recorrer los tres meses de cobranza por servicio si existen.
                $totalLocalDep = 0;
                foreach($cobranza as $ck=>$cob){
                    $rowCobranza[$ck]["total"]+=$cob["total"];
                    $rowCobranza[$ck]["class"]=$cob["class"];
                    $rowCobranza[$ck]["status"]=1;
                    $rowCobranza[$ck]["mes"]=$ck;
                    $rowCobranza[$ck]["anio"]=$year;
                    $sumTotalCobranza +=$cob["total"];
                    $totalLocalDep +=$cob["total"];
                }
                //obtener totales cobranza por departamento
                if(!in_array($serv['departamentoId'],$idDepsCobranza)){
                    array_push($idDepsCobranza,$serv['departamentoId']);
                    $tXdc['name'] =$nameDepartamento;
                    $tXdc['total'] =  $totalLocalDep;
                    $totalesCobranzaXdep[$serv['departamentoId']] = $tXdc;
                }else{
                    $totalesCobranzaXdep[$serv['departamentoId']]['total'] += $totalLocalDep;
                }
           }//end foreach servicios.
           if(count($serviciosFiltrados)<=0){
                unset($contratos[$key]);
                continue;
           }
            $card2 = [];
            $card2["instancias"] = $rowCobranza;
            $card2["sumatotal"]=$sumTotalCobranza;
            $card2["isRowCobranza"] =  true;
            $card2["description"] = "Total cobranza proporcional";
            $card2["isDevengado"] = false;
            $granTotalCobranza +=$sumTotalCobranza;
            array_push( $serviciosFiltrados,$card2);

            $contratos[$key]['servicios'] = $serviciosFiltrados;
        }//end foreach contratos

        $data['totalContratos'] = $totalContratos;
	    $data['contratosAsignados'] = $totalContratosAsignados;
	    $data['porcentajeAsignado'] = ($totalContratosAsignados*100)/$totalContratos;
        $data['contratos'] = $contratos;

        $data['granTotalContabilidad'] =$granTotalContabilidad;
        $data['granTotalCobranza'] =$granTotalCobranza;
        $data['totalesXdepartamentos'] = $totalXdepartamento;
        $data['totalesXencargados'] = $totalXencargados;
        $data['totalesCobranzaXdepartamento'] = $totalesCobranzaXdep;
        return $data;
    }
    function generateReportBonosWhitLevel($ftr=[]){
        global $personal,$contractRep,$instanciaServicio;
        $strFilter = "";
        if(strlen($_POST["rfc2"])>0||strlen($_POST["rfc"])>0)
        {
            $like = $_POST["rfc"];
            $like2 = $_POST["rfc2"];
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
            $strFilter .=" and contractId in(select a.contractId from contract a inner join customer b on a.customerId = b.customerId where b.active='1' and a.activo='Si' $subStr )";

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

        $sqlServ = "select a.servicioId,a.contractId,a.status,b.nombreServicio,b.departamentoId 
                    from servicio a 
                    inner join tipoServicio b on a.tipoServicioId=b.tipoServicioId 
                    where b.status='1' and a.status IN ('activo','bajaParcial') and a.inicioFactura!='0000-00-00' $strFilter";
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
            $servId =$service['servicioId'];
            $conId= $service["contractId"];
            $sql ="select a.name,b.nameContact,a.contractId from contract a inner join customer b on a.customerId=b.customerId where contractId='$conId' and a.activo='Si' and b.active='1' ";
            $this->Util()->DB()->setQuery($sql);
            $contrato = $this->Util()->DB()->GetRow();
            if(!$contrato)
                continue;
            $encargados = $contractRep->encargadosCustomKey('departamentoId','personalId',$service['contractId']);
            if(!in_array($encargados[$service['departamentoId']],$fullSubordinados))
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
            if(!empty($temp['instancias'])){
                $service['instancias'] = array_replace_recursive($mesesBase, $temp['instancias']);
                $service['totalTrabajado'] = $temp['totalComplete'];
                $service['totalDevengado'] = $temp['totalDevengado'];
                $totales["granTotalHorizontalCompletado"] += $temp['totalComplete'];
                $totales["granTotalHorizontalDevengado"] += $temp['totalDevengado'];
            }
            else
                continue;

            $encargadoDep = $encargados[$service['departamentoId']];
            $personal->setPersonalId($encargadoDep);
            $encargado =$personal->InfoWhitRol();
            $service["encargado"]=$encargado['name'];
            $service["encargadoId"]=$encargadoDep;

            $service["cliente"]=$contrato['nameContact'];
            $service["contrato"]=$contrato['name'];

            $cad['porcentajeBono']=$encargado['porcentajeBono'];
            $cad['sueldoTotal']=$encargado['sueldo']*3;
            switch($encargado["nivel"]){
                case 1:
                    $service["nivel"] = "socio";
                    $serviciosEncontrados[$encargadoDep]["propios"][]=$service;

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
                    $personal->setPersonalId($encargadoDep);
                    $jefe = $personal->jefeInmediato();//un gerente tiene como jefe a un socio siempre debe cumplirse
                                            //socio             gerente         idGerente
                    $serviciosEncontrados[$jefe['personalId']]["subordinados"][$encargadoDep]['propios'][]=$service;
                    if(!is_array($serviciosEncontrados[$jefe['personalId']]["subordinados"][$encargadoDep]['contratos']))
                        $serviciosEncontrados[$jefe['personalId']]["subordinados"][$encargadoDep]['contratos'] = [];

                    if(!in_array($contrato['contractId'],$serviciosEncontrados[$jefe['personalId']]["subordinados"][$encargadoDep]['contratos'])){
                        $serviciosEncontrados[$jefe['personalId']]["subordinados"][$encargadoDep]['contratos'][]=$contrato['contractId'];
                        $totales["totalEmpresas"]++;
                    }

                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$jefe['personalId']]["subordinados"][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$jefe['personalId']]["subordinados"][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                        if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['porcentajeBono']=$encargado['porcentajeBono'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$jefe['personalId'];
                            $cad['level']=2;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;
                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }

                    }
                break;
                case 3:
                    $service["nivel"]  = "supervisor";
                    $personal->setPersonalId($encargadoDep);
                    $jefeGerente = $personal->jefeInmediato();
                    $personal->setPersonalId($jefeGerente['personalId']);
                    $jefeSocio = $personal->jefeInmediato();
                                                //socio              //gerentes       idGerente                  supervisores   idSupervisor    propiosSup
                    $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$encargadoDep]['propios'][]=$service;
                    if(!is_array($serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$encargadoDep]['contratos']))
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$encargadoDep]['contratos'] = [];

                    if(!in_array($contrato['contractId'],$serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$encargadoDep]['contratos'])){
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$encargadoDep]['contratos'][]=$contrato['contractId'];
                        $totales["totalEmpresas"]++;
                    }

                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                        if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['porcentajeBono']=$encargado['porcentajeBono'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$jefeGerente['personalId'];
                            $cad['level']=3;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;
                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }
                    }
                break;
                case 4:
                    $service["nivel"]  = "contador";
                    $personal->setPersonalId($encargadoDep);
                    $jefeSupervisor = $personal->jefeInmediato();
                    $personal->setPersonalId($jefeSupervisor['personalId']);
                    $jefeGerente = $personal->jefeInmediato();
                    $personal->setPersonalId($jefeGerente['personalId']);
                    $jefeSocio = $personal->jefeInmediato();
                    $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$encargadoDep]['propios'][]=$service;

                    if(!is_array($serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$encargadoDep]['contratos']))
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$encargadoDep]['contratos'] = [];

                    if(!in_array($contrato['contractId'],$serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$encargadoDep]['contratos'])){
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$encargadoDep]['contratos'][]=$contrato['contractId'];
                        $totales["totalEmpresas"]++;
                    }

                    foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                       if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['porcentajeBono']=$encargado['porcentajeBono'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$jefeSupervisor['personalId'];
                            $cad['level']=4;
                            $cad['personalId']=$encargadoDep;
                            $totalesEncargados[$encargadoDep]=$cad;
                        }else{
                            $totalesEncargados[$encargadoDep]['totalDevengado']+=$itemins['costo'];
                            $totalesEncargados[$encargadoDep]['totalCompletado']+=$itemins['completado'];
                        }
                    }
                break;
                case 5:
                    $service["nivel"]  = "auxiliar";
                    $personal->setPersonalId($encargadoDep);
                    $jefeContador = $personal->jefeInmediato();
                    $personal->setPersonalId($jefeContador["personalId"]);
                    $jefeSupervisor = $personal->jefeInmediato();
                    $personal->setPersonalId($jefeSupervisor['personalId']);
                    $jefeGerente = $personal->jefeInmediato();
                    $personal->setPersonalId($jefeGerente['personalId']);
                    $jefeSocio = $personal->jefeInmediato();
                    $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$jefeContador['personalId']]["subordinados"][$encargadoDep]['propios'][]=$service;
                    if(!is_array($serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$jefeContador['personalId']]["subordinados"][$encargadoDep]["contratos"]))
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$jefeContador['personalId']]["subordinados"][$encargadoDep]["contratos"]=[];

                    if(!in_array($contrato["contractId"],$serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$jefeContador['personalId']]["subordinados"][$encargadoDep]["contratos"])){
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$jefeContador['personalId']]["subordinados"][$encargadoDep]["contratos"][]=$contrato["contractId"];
                        $totales["totalEmpresas"]++;
                    }
                        foreach($service["instancias"] as $ikey=>$itemins){
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$jefeContador['personalId']]["subordinados"][$encargadoDep]['totalVerticalDevengado'][$ikey] +=$itemins['costo'];
                        $serviciosEncontrados[$jefeSocio["personalId"]]['subordinados'][$jefeGerente['personalId']]['subordinados'][$jefeSupervisor['personalId']]["subordinados"][$jefeContador['personalId']]["subordinados"][$encargadoDep]['totalVerticalCompletado'][$ikey] +=$itemins['completado'];
                        $totales['granTotalVerticalDevengado'][$ikey] += $itemins['costo'];
                        $totales['granTotalVerticalCompletado'][$ikey] += $itemins['completado'];

                        if(!in_array($encargadoDep,$listsEncargados)){
                            array_push($listsEncargados,$encargadoDep);
                            $cad['name']=$encargado['name'];
                            $cad['porcentajeBono']=$encargado['porcentajeBono'];
                            $cad['totalDevengado']=$itemins['costo'];
                            $cad['totalCompletado']=$itemins['completado'];
                            $cad['jefeInmediato']=$jefeContador['personalId'];
                            $cad['level']=5;
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
            }
            else{
                $newArray[$enc["personalId"]]['totalDevengado']  += $enc['totalDevengado'];
                $newArray[$enc["personalId"]]['totalCompletado'] += $enc['totalCompletado'];
                $newArray[$enc["personalId"]]['sueldoTotal'] += $enc['sueldoTotal'];
            }
            //si tiene jefe inmediato se suma el total del sub al jefe
            $this->recursiveTotalEncargado($allEncargados,$newArray, $totalesEncargados[$ke],$enc['totalDevengado'],$enc['totalCompletado'],$enc['sueldoTotal']);
        }
        $ordenado = $this->Util()->orderMultiDimensionalArray($newArray,'level',false,true);
        $data["totalesEncargadosAcumulado"] = $ordenado;
        return $data;
    }
    function recursiveTotalEncargado($allEncargados,&$newArray,$value,$acumDevengado,$acumCompletado,$sueldo){
            $this->Util()->DB()->setQuery("select b.nivel from personal a inner join roles b on a.roleId=b.rolId where personalId = '".$value['jefeInmediato']."' ");
            $level = $this->Util()->DB()->GetSingle();
	        if(!$value['jefeInmediato']||$level<=1 || !array_key_exists($value["jefeInmediato"],$allEncargados))
	           return;

            if(array_key_exists($value['jefeInmediato'],$newArray)){
                $newArray[$value['jefeInmediato']]['totalDevengado']  += $acumDevengado;
                $newArray[$value['jefeInmediato']]['totalCompletado'] += $acumCompletado;
                $newArray[$value['jefeInmediato']]['sueldoTotal'] += $sueldo;
                if($newArray[$value['jefeInmediato']]){
                    $this->recursiveTotalEncargado($allEncargados,$newArray,$newArray[$value['jefeInmediato']],$acumDevengado,$acumCompletado,$sueldo);

                }
            }else{
                $newArray[$value["jefeInmediato"]] = $allEncargados[$value["jefeInmediato"]];
                $newArray[$value['jefeInmediato']]['totalDevengado']  += $acumDevengado;
                $newArray[$value['jefeInmediato']]['totalCompletado'] += $acumCompletado;
                $newArray[$value['jefeInmediato']]['sueldoTotal'] += $sueldo;
                if($newArray[$value['jefeInmediato']]){
                    $this->recursiveTotalEncargado($allEncargados,$newArray,$newArray[$value['jefeInmediato']],$acumDevengado,$acumCompletado,$sueldo);
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
            $strFilter .= " and personalId = '".$ftr['responsableCuenta']."' ";

        $sql = "select * from personal where roleId=2 $strFilter order by name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();
        //encontrar los meses
        $meses = $this->generarMesesAconsultar($_POST["tipoPeriodo"],$_POST["period"]);
        $mesesBase =  $this->createMonthBasesFromArray($meses);
        $year = $ftr["year"];
        foreach($gerentes as $key=>$value){
            $stackSubordinados = [];
            $detalleSubordinados = [];
            $ftr["responsableCuenta"] = $value["personalId"];
            $ftr["departamentoId"] = $value["departamentoId"];

            $subordinados = $personal->GetIdResponsablesSubordinados(["deep"=>1,"responsableCuenta"=>$value["personalId"]]);
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
                $empleado['totalDevengado']=0;
                $empleado['totalCompletado']=0;
                $allEncargados[$subId] = $empleado;
            }
            $newArray = [];
            $detalleSubordinados = $this->Util()->orderMultiDimensionalArray($detalleSubordinados,'nivel',true,true);
            foreach($detalleSubordinados as $ke=>$enc){
                if(!array_key_exists($enc['personalId'],$newArray)){
                    $newArray[$enc["personalId"]] = $detalleSubordinados[$ke];
                }
                else{
                    $newArray[$enc["personalId"]]['totalDevengado']  += $enc['totalDevengado'];
                    $newArray[$enc["personalId"]]['totalCompletado'] += $enc['totalCompletado'];
                }
                //si tiene jefe inmediato se suma el total del sub al jefe
                $this->recursiveTotalEncargado($allEncargados,$newArray, $detalleSubordinados[$ke],$enc['totalDevengado'],$enc['totalCompletado']);
            }
            $ordenado = $this->Util()->orderMultiDimensionalArray($newArray,'nivel',false,true);

            $gerentes[$key]["totalDevengado"] = $totalDevengadoGerente;
            $gerentes[$key]["totalCompletado"] = $totalTrabajadoGerente;
            $gerentes[$key]["sueldoTotalConSub"] = $totalSueldoIncluidoSubordinados;
            $gerentes[$key]["detalleSubordinados"] = $ordenado;
        }
        return $gerentes;
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