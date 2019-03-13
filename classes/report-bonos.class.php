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
	function generateReportBonos($ftr=[]){
	    global $contractRep,$instanciaServicio,$customer,$personal;

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
            $countServ =  count($value['servicios']);
            $meses=[];
            //encontrar los encargados de area;

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

               if(!in_array($serv['departamentoId'],$idDepsGeneral)){
                   array_push($idDepsGeneral,$serv['departamentoId']);
                   Departamentos::setDepartamentoId($serv['departamentoId']);
                   $tXd['departamento'] =Departamentos::Info()['departamento'];
                   $tXd['total'] = 0;
                   $tXd['total'] = (double)$sumaTotalTrabajado;
                   $totalXdepartamento[$serv['departamentoId']] = $tXd;
               }else{
                   $totalXdepartamento[$serv['departamentoId']]['total'] += (double)$sumaTotalTrabajado;
               }
               $serv['sumatotal'] = $sumaTotalTrabajado;
               $serviciosFiltrados[]= $serv;
               $granTotalContabilidad +=(double)$sumaTotalDevengado;
               //recorrer total de cobranza por servicio
                foreach($cobranza as $ck=>$cob){
                    $rowCobranza[$ck]["total"]+=$cob["total"];
                    $rowCobranza[$ck]["class"]=$cob["class"];
                    $rowCobranza[$ck]["status"]=1;
                    $rowCobranza[$ck]["mes"]=$ck;
                    $rowCobranza[$ck]["anio"]=$year;
                    $sumTotalCobranza +=$cob["total"];
                }

           }//end foreach servicios.
           if(count($serviciosFiltrados)<=0){
                unset($contratos[$key]);
                continue;
           }
            $card2 = [];
            //crear el total de cobranza por mes
            $card2["instancias"] = $rowCobranza;
            $card2["sumatotal"]=$sumTotalCobranza;
            $card2["isRowCobranza"] =  true;
            $granTotalCobranza +=$sumTotalCobranza;
            array_push( $serviciosFiltrados,$card2);

           /*Inicio fila de cobranza*/
           /*$rowCobranza=InstanciaServicio::getRowCobranzaByInstancia($value['contractId'],$year,$meses,false);
           $card2["instancias"]=$rowCobranza['instanciasCobranza'];
           $card2["sumatotal"]=$rowCobranza['totalCobrado'];
           $granTotalCobranza +=$rowCobranza['totalCobrado'];
           //summar total cobranza por departamento
           if(count($rowCobranza['totalCobradoXdepProporcional'])>0){
               foreach ($rowCobranza['totalCobradoXdepProporcional'] as $ck=>$totaldep){
                   if(!in_array($ck,$idDepsCobranza)){
                       array_push($idDepsCobranza,$ck);
                       if($ck!=000000)
                       {
                           Departamentos::setDepartamentoId($ck);
                           $tXdc['name'] =Departamentos::Info()['departamento'];
                       }else{
                           $tXdc['name']="SIN DEPARTAMENTO";
                       }

                       $tXdc['total'] =  $totaldep;
                       $totalesCobranzaXdep[$ck] = $tXdc;
                   }else{
                       $totalesCobranzaXdep[$ck]['total'] += $totaldep;
                   }
               }
           }
           $card2["isRowCobranza"] =  true;
           array_push( $serviciosFiltrados,$card2);
           /*Fin de fila cobranza*/
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
}
?>