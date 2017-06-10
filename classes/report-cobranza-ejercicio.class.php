<?php

class ReporteCobranzaEjercicio extends Main
{
	public function setAnio($values) {
		$this->anio = $values;
	}

	public function setMesUno($values) {
		$this->mesUno = $values;
	}

	public function setCliente($values) {
		$this->cliente = $values;
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

		$INFO['DATOS'] = $DATOS;

		return $INFO;
	}
	public function EnumerateSupervisor() {

		$this->Util()->DB()->setQuery("
			SELECT * FROM
				personal
			WHERE
				tipoPersonal = 'Supervisor' AND personalId = '".$this->personalId."'
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

	public function EnumerateClientesCobranza() {
		if ($this->cliente) {
			$sql = " AND CO.customerId = '".$this->cliente."' ";
		}
		if ($this->anio) {
			$sql .= " AND (
					SELECT
						SUM(
							(
							SELECT
								COUNT(*)
							FROM
								instanciaServicio ISE
							WHERE
								EXTRACT(YEAR FROM ISE.date) = '".$this->anio."' AND
								ISE.servicioId = S.servicioId
							)
						)
					FROM
						servicio S
					WHERE
						S.status = 'activo' AND
						S.contractId = CO.contractId
				) > 0";
		}
		$this->Util()->DB()->setQuery("
			SELECT
				CO.contractId,
				CU.customerId,
				CU.nameContact as nameCustomer,

				P.personalId,
				P.name as namePersonal,

				(SELECT sum(SE.costo) from  servicio SE where SE.status = 'activo' and SE.contractId = CO.contractId  ) AS COSTO_T,

				CO.name as nameContract,
				CO.nombreComercial

				/*
				, (
					SELECT
						SUM(
							(
							SELECT
								COUNT(*)
							FROM
								instanciaServicio ISE
							WHERE
								EXTRACT(YEAR FROM ISE.date) = '".$this->anio."' AND
								ISE.servicioId = S.servicioId
							)
						)
					FROM
						servicio S
					WHERE
						S.status = 'activo' AND
						S.contractId = CO.contractId
				) AS CARIDAD_FECHA_SERVICIOS
				*/

			FROM
				contract CO
			INNER JOIN customer CU ON CU.customerId = CO.customerId
			LEFT JOIN personal P ON P.personalId = CO.cobrador
			where
				CU.active = '1' AND
				CO.activo = 'Si' AND
				CO.customerId <> '' AND
				CO.responsableCuenta <> 0
				".$sql."

		");
		$result['items'] = $this->Util()->DB()->GetResult();

		foreach ($result['items'] as $key => $row) {
			$this->Util()->DB()->setQuery("
				SELECT
					S.*
				FROM
					servicio S
				LEFT JOIN tipoServicio T ON T.tipoServicioId = S.tipoServicioId
				WHERE
					S.status = 'activo' AND
					S.contractId = '".$row['contractId']."'

			");

			$result['items'][$key]['MESES'] = $this->Util()->DB()->GetResult();
			foreach ($result['items'][$key]['MESES'] as $key2 => $row2) {
				$result['items'][$key]['MESES'][$key2]['ENERO'] = $this->OBTENER_MESES($this->anio,'01',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['FEBRERO'] = $this->OBTENER_MESES($this->anio,'02',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['MARZO'] = $this->OBTENER_MESES($this->anio,'03',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['ABRIL'] = $this->OBTENER_MESES($this->anio,'04',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['MAYO'] = $this->OBTENER_MESES($this->anio,'05',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['JUNIO'] = $this->OBTENER_MESES($this->anio,'06',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['JULIO'] = $this->OBTENER_MESES($this->anio,'07',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['AGOSTO'] = $this->OBTENER_MESES($this->anio,'08',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['SEPTIEMBRE'] = $this->OBTENER_MESES($this->anio,'09',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['OCTUBRE'] = $this->OBTENER_MESES($this->anio,'10',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['NOVIEMBRE'] = $this->OBTENER_MESES($this->anio,'11',$row2['servicioId']);
				$result['items'][$key]['MESES'][$key2]['DICIEMBRE'] = $this->OBTENER_MESES($this->anio,'12',$row2['servicioId']);
			}

		}
		foreach ($result['items'] as $key => $row) {
			$result['items'][$key]['CANTIDAD_CONTRACT'] = count($row['MESES']);
			foreach ($result['items'][$key]['MESES'] as $key2 => $row2) {
				$result['items'][$key]['CANTIDAD_SERVICIOS'] += count($row2['ENERO'])+count($row2['FEBRERO'])+count($row2['MARZO'])+count($row2['ABRIL'])+count($row2['MAYO'])+count($row2['JUNIO'])+count($row2['JULIO'])+count($row2['AGOSTO'])+count($row2['SEPTIEMBRE'])+count($row2['OCTUBRE'])+count($row2['NOVIEMBRE'])+count($row2['DICIEMBRE']);


				if(count($result['items'][$key]['MESES'][$key2]['ENERO']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['ENERO'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['ENERO'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['ENERO'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['ENERO'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['ENERO'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['FEBRERO']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['FEBRERO'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['FEBRERO'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['FEBRERO'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['FEBRERO'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['FEBRERO'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['MARZO']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['MARZO'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['MARZO'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['MARZO'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['MARZO'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['MARZO'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['ABRIL']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['ABRIL'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['ABRIL'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['ABRIL'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['ABRIL'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['ABRIL'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['MAYO']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['MAYO'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['MAYO'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['MAYO'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['MAYO'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['MAYO'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['JUNIO']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['JUNIO'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['JUNIO'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['JUNIO'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['JUNIO'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['JUNIO'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['JULIO']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['JULIO'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['JULIO'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['JULIO'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['JULIO'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['JULIO'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['AGOSTO']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['AGOSTO'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['AGOSTO'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['AGOSTO'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['AGOSTO'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['AGOSTO'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['SEPTIEMBRE']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['SEPTIEMBRE'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['SEPTIEMBRE'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['SEPTIEMBRE'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['SEPTIEMBRE'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['SEPTIEMBRE'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['OCTUBRE']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['OCTUBRE'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['OCTUBRE'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['OCTUBRE'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['OCTUBRE'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['OCTUBRE'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['NOVIEMBRE']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['NOVIEMBRE'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['NOVIEMBRE'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['NOVIEMBRE'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['NOVIEMBRE'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['NOVIEMBRE'] = array('0' => array('COLOR' => 'NEGRO') );
				}




				if(count($result['items'][$key]['MESES'][$key2]['DICIEMBRE']) > 0){
					foreach ($result['items'][$key]['MESES'][$key2]['DICIEMBRE'] as $key3 => $row4) {
						if ($row4['comprobanteId'] != "") {
							if ($row4['PAYMENT'] >= $row4['total']) {
								$result['items'][$key]['MESES'][$key2]['DICIEMBRE'][$key3]['COLOR'] = 'ASTERISCO';
							}else{
								$result['items'][$key]['MESES'][$key2]['DICIEMBRE'][$key3]['COLOR'] = 'BLANCO';
							}
						}else{
							$result['items'][$key]['MESES'][$key2]['DICIEMBRE'][$key3]['COLOR'] = 'SIN-COMPROBANTE';
						}
					}
				}else{
					$result['items'][$key]['MESES'][$key2]['DICIEMBRE'] = array('0' => array('COLOR' => 'NEGRO') );
				}
			}
		}
		foreach ($result['items'] as $key => $row) {
			foreach ($result['items'][$key]['MESES'] as $key2 => $row2) {


					foreach ($result['items'][$key]['MESES'][$key2]['ENERO'] as $key3 => $row4) {
						$result['items'][$key]['ENERO'] .= $result['items'][$key]['MESES'][$key2]['ENERO'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['FEBRERO'] as $key3 => $row4) {
						$result['items'][$key]['FEBRERO'] .= $result['items'][$key]['MESES'][$key2]['FEBRERO'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['MARZO'] as $key3 => $row4) {
						$result['items'][$key]['MARZO'] .= $result['items'][$key]['MESES'][$key2]['MARZO'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['ABRIL'] as $key3 => $row4) {
						$result['items'][$key]['ABRIL'] .= $result['items'][$key]['MESES'][$key2]['ABRIL'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['MAYO'] as $key3 => $row4) {
						$result['items'][$key]['MAYO'] .= $result['items'][$key]['MESES'][$key2]['MAYO'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['JUNIO'] as $key3 => $row4) {
						$result['items'][$key]['JUNIO'] .= $result['items'][$key]['MESES'][$key2]['JUNIO'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['JULIO'] as $key3 => $row4) {
						$result['items'][$key]['JULIO'] .= $result['items'][$key]['MESES'][$key2]['JULIO'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['AGOSTO'] as $key3 => $row4) {
						$result['items'][$key]['AGOSTO'] .= $result['items'][$key]['MESES'][$key2]['AGOSTO'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['SEPTIEMBRE'] as $key3 => $row4) {
						$result['items'][$key]['SEPTIEMBRE'] .= $result['items'][$key]['MESES'][$key2]['SEPTIEMBRE'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['OCTUBRE'] as $key3 => $row4) {
						$result['items'][$key]['OCTUBRE'] .= $result['items'][$key]['MESES'][$key2]['OCTUBRE'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['NOVIEMBRE'] as $key3 => $row4) {
						$result['items'][$key]['NOVIEMBRE'] .= $result['items'][$key]['MESES'][$key2]['NOVIEMBRE'][$key3]['COLOR']."#";
					}




					foreach ($result['items'][$key]['MESES'][$key2]['DICIEMBRE'] as $key3 => $row4) {
						$result['items'][$key]['DICIEMBRE'] .= $result['items'][$key]['MESES'][$key2]['DICIEMBRE'][$key3]['COLOR']."#";
					}
			}
		}
		foreach ($result['items'] as $key => $row) {
			$result['items'][$key]['ENERO'] = implode("#",array_unique(explode("#", $row['ENERO'])));
			$result['items'][$key]['FEBRERO'] = implode("#",array_unique(explode("#", $row['FEBRERO'])));
			$result['items'][$key]['MARZO'] = implode("#",array_unique(explode("#", $row['MARZO'])));
			$result['items'][$key]['ABRIL'] = implode("#",array_unique(explode("#", $row['ABRIL'])));
			$result['items'][$key]['MAYO'] = implode("#",array_unique(explode("#", $row['MAYO'])));
			$result['items'][$key]['JUNIO'] = implode("#",array_unique(explode("#", $row['JUNIO'])));
			$result['items'][$key]['JULIO'] = implode("#",array_unique(explode("#", $row['JULIO'])));
			$result['items'][$key]['AGOSTO'] = implode("#",array_unique(explode("#", $row['AGOSTO'])));
			$result['items'][$key]['SEPTIEMBRE'] = implode("#",array_unique(explode("#", $row['SEPTIEMBRE'])));
			$result['items'][$key]['OCTUBRE'] = implode("#",array_unique(explode("#", $row['OCTUBRE'])));
			$result['items'][$key]['NOVIEMBRE'] = implode("#",array_unique(explode("#", $row['NOVIEMBRE'])));
			$result['items'][$key]['DICIEMBRE'] = implode("#",array_unique(explode("#", $row['DICIEMBRE'])));
		}
		foreach ($result['items'] as $key => $row) {
			$result['items'][$key]['ENERO_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['ENERO']);
			$result['items'][$key]['FEBRERO_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['FEBRERO']);
			$result['items'][$key]['MARZO_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['MARZO']);
			$result['items'][$key]['ABRIL_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['ABRIL']);
			$result['items'][$key]['MAYO_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['MAYO']);
			$result['items'][$key]['JUNIO_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['JUNIO']);
			$result['items'][$key]['JULIO_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['JULIO']);
			$result['items'][$key]['AGOSTO_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['AGOSTO']);
			$result['items'][$key]['SEPTIEMBRE_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['SEPTIEMBRE']);
			$result['items'][$key]['OCTUBRE_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['OCTUBRE']);
			$result['items'][$key]['NOVIEMBRE_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['NOVIEMBRE']);
			$result['items'][$key]['DICIEMBRE_N'] = $this->COLOR_STATUS("VALIDAR STATUS: ".$row['DICIEMBRE']);
		}
		// echo "<pre><br><br>";
		// print_r($result);
		// echo "</pre>";

		return $result;
	}
	function COLOR_STATUS($STATUS) {
		if (strpos($STATUS,"BLANCO")) {
			return "BLANCO";
		}elseif (strpos($STATUS,"ASTERISCO")) {
			return "ASTERISCO";
		}elseif (strpos($STATUS,"SIN-COMPROBANTE")) {
			return "SIN-COMPROBANTE";
		}elseif (strpos($STATUS,"NEGRO")) {
			return "NEGRO";
		}else{
			return "NO SE ENCONTRO: ".$STATUS;
		}
	}
	function OBTENER_MESES($anio,$mes,$servicioId) {
		$this->Util()->DB()->setQuery("
			SELECT
				ISE.instanciaServicioId as instanciaServicioIdISE,
				ISE.servicioId as servicioIdISE,
				ISE.date as dateISE,
				ISE.status as statusISE,
				ISE.fechaCompleta as fechaCompletaISE,
				ISE.comprobanteId as comprobanteIdISE,
				ISE.class as classISE,
				ISE.updated as updatedISE,

				C.*
			FROM
				instanciaServicio ISE
			LEFT JOIN servicio SS ON SS.servicioId = ISE.servicioId
			left join comprobante C on C.comprobanteId = ISE.comprobanteId
			WHERE
				ISE.servicioId = '".$servicioId."' AND
				EXTRACT(MONTH FROM ISE.date) = '".$mes."' AND
				EXTRACT(YEAR FROM ISE.date) = '".$anio."'

		");
		$result = $this->Util()->DB()->GetResult();
		foreach ($result as $key => $row) {
			$this->Util()->DB()->setQuery("
				SELECT sum(amount) FROM
					payment
				WHERE
					comprobanteId = '".$row['comprobanteId']."'
			");
			$result[$key]['PAYMENT'] = $this->Util()->DB()->GetSingle();

			$this->Util()->DB()->setQuery("
				SELECT * FROM
					payment
				WHERE
					comprobanteId = '".$row['comprobanteId']."'
			");
			$result[$key]['PAYMENT_ARRAY'] = $this->Util()->DB()->GetResult();
		}

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
			LEFT JOIN customer CU ON CU.customerId = CO.customerId
			LEFT JOIN personal P ON P.personalId = CO.responsableCuenta
			WHERE
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
						ISE.class
					FROM
						instanciaServicio ISE
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesUno."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS STATUS_class1,
					(SELECT
						ISE.status
					FROM
						instanciaServicio ISE
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesUno."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS STATUS_status1,
					(SELECT
						SS.costo
					FROM
						instanciaServicio ISE
					LEFT JOIN servicio SS ON SS.servicioId = ISE.servicioId
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesUno."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS MES1,



					(SELECT
						ISE.class
					FROM
						instanciaServicio ISE
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesDos."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS STATUS_class2,
					(SELECT
						ISE.status
					FROM
						instanciaServicio ISE
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesDos."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS STATUS_status2,
					(SELECT
						SS.costo
					FROM
						instanciaServicio ISE
					LEFT JOIN servicio SS ON SS.servicioId = ISE.servicioId
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesDos."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS MES2,



					(SELECT
						ISE.class
					FROM
						instanciaServicio ISE
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesTres."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS STATUS_class3,
					(SELECT
						ISE.status
					FROM
						instanciaServicio ISE
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesTres."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS STATUS_status3,
					(SELECT
						SS.costo
					FROM
						instanciaServicio ISE
					LEFT JOIN servicio SS ON SS.servicioId = ISE.servicioId
					WHERE
						ISE.servicioId = S.servicioId AND
						EXTRACT(MONTH FROM ISE.date) = '".$this->mesTres."' AND
						EXTRACT(YEAR FROM ISE.date) = '".$this->anio."'
					) AS MES3





				FROM
					servicio S
				LEFT JOIN tipoServicio T ON T.tipoServicioId = S.tipoServicioId
				WHERE
					contractId = '".$row['contractId']."' AND
					periodicidad = 'Mensual' AND
					departamentoId = '".$row['departamentoId']."' AND
					departamentoId = '".$this->departamentoId."'

			");
			$result[$key]['OSWA'] = $this->Util()->DB()->GetResult();
		}
		foreach ($result as $key => $row) {
			foreach ($row['OSWA'] as $key2 => $row2) {
				$result[$key]['nameServicios'] .= '* '.$row2['nombreServicio'].' <br>';

				$result[$key]['MES1'] += $row2['MES1'];
				$result[$key]['MES2'] += $row2['MES2'];
				$result[$key]['MES3'] += $row2['MES3'];
				$result[$key]['TOTAL_MES'] = $row2['MES1']+$row2['MES2']+$row2['MES3'];
			}
		}
		return $result;
	}

	function rand_color() {
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}

}

?>