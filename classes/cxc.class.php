<?php

class CxC extends Producto
{
	function SearchCuentasPorCobrarNoReporte($contracts, $values){
	{
	    global $user;
		$sqlSearch = '';
		$ids = array();
		$inIds = "0";
		foreach($contracts as $key => $contract)
		{
			foreach($contract as $id)
			{
				$ids[] = $id["contractId"];
				$inIds .= ",".$id["contractId"];
			}
		}
		$sqlSearch .= ' AND contract.contractId IN ('.$inIds.')';
        if($values['anio'])
				$sqlSearch .= ' AND EXTRACT(YEAR FROM c.fecha) = '.intval($values['anio']);
        if($values['month'])
                $sqlSearch .= ' AND EXTRACT(MONTH FROM c.fecha) = '.intval($values['month']);

		$sql = "SELECT *, c.status AS status, c.comprobanteId AS comprobanteId,customer.nameContact AS nameContact,contract.rfc,contract.name as razon
                FROM comprobante AS c
                LEFT JOIN contract ON contract.contractId = c.userId
                LEFT JOIN customer ON customer.customerId = contract.customerId
                LEFT JOIN personal ON contract.responsableCuenta = personal.personalId
                WHERE c.status='1' AND c.tiposComprobanteId != 10 $sqlSearch
                ORDER BY fecha DESC ";
        $id_empresa = $_SESSION['empresaId'];
        $this->Util()->DBSelect($id_empresa)->setQuery($sql);
        $comprobantes = $this->Util()->DBSelect($id_empresa)->GetResult();
        $info = array();
		if($values['facturador'] != 'Efectivo'){
            foreach($comprobantes as $key => $val){
                $card['serie'] = $val['serie'];
                $card['folio'] = $val['folio'];
                $card['rfc'] = $val['rfc'];
                //$card['nombre'] = $usr['nombre'];
                $card['nombre']=$val['razon'];
                $card['fecha'] = date('Y/m/d',strtotime($val['fecha']));
                $card['fecha'] = $this->Util()->GetMesDiagonal($card['fecha']);

                $card['subTotal'] = $val['subTotal'];
                $card['porcentajeDescuento'] = $val['porcentajeDescuento'];
                $card['descuento'] = $val['descuento'];
                $card['ivaTotal'] = $val['ivaTotal'];

                //aplicar descuento
                $card['cxcAmountDiscount'] = $val['total'] * ($val['cxcDiscount'] / 100);
                $val['total'] = $val['total'] - $card['cxcAmountDiscount'];
                $card['total'] = $val['total'];

                $card['total_formato'] = number_format($val['total'],2,'.',',');
                $card['subtotal_formato'] = number_format($val['subTotal'],2,'.',',');
                $card['iva_formato'] = number_format($val['ivaTotal'],2,'.',',');
                $card['tipoDeMoneda'] = $val['tipoDeMoneda'];
                $card['tipoDeCambio'] = $val['tipoDeCambio'];
                $card['facturador'] = $val['facturador'];
                $card['porcentajeRetIva'] = $val['porcentajeRetIva'];
                $card['porcentajeRetIsr'] = $val['porcentajeRetIsr'];
                $card['porcentajeIEPS'] = $val['porcentajeIEPS'];
                $card['comprobanteId'] = $val['comprobanteId'];
                $card['status'] = $val['status'];
                $card['tipoDeComprobante'] = $val['tipoDeComprobante'];
                $card['cxcDiscount'] = $val['cxcDiscount'];
                $card['version'] = $val['version'];
                $card['xml'] = $val['xml'];
                $card['nameContact'] = $val["nameContact"];

                $card['instanciaServicioId'] = $val['instanciaServicioId'];
                $timbreFiscal = unserialize($val['timbreFiscal']);
                $card["uuid"] = $timbreFiscal["UUID"];
				$monedaComprobante = "";
				switch($val['tipoDeMoneda']){ 
					case "peso": $monedaComprobante = "MXN"; break;
					case "dolar": $monedaComprobante = "USD"; break;
					case "euro": $monedaComprobante = "EUR"; break;
				}
             	$card["moneda"] = $monedaComprobante;

                //si tiene solicitud de cancelacion se debe omitir.
                $sqlQuery = "SELECT solicitud_cancelacion_id FROM pending_cfdi_cancel  WHERE cfdi_id = '".$val["comprobanteId"]."' AND deleted_at IS NULL AND status = '".CFDI_CANCEL_STATUS_PENDING."'";
                $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
                $requestCancel = $this->Util()->DBSelect($id_empresa)->GetSingle();
                if($requestCancel)
                    continue;
                //get payments
                $sqlQuery = "SELECT * FROM payment
                             WHERE comprobanteId = '".$val["comprobanteId"]."' and paymentStatus='activo' ";
                $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
                $card["payments"] = $this->Util()->DBSelect($id_empresa)->GetResult();

                $sqlQuery = "SELECT SUM(amount) FROM payment
                             WHERE comprobanteId = '".$val["comprobanteId"]."' and paymentStatus='activo' ";
                $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
                $card["payment"] = $this->Util()->DBSelect($id_empresa)->GetSingle();

                //saldo
                $card['saldo'] = $card["total"] - $card["payment"];
                $info[$key] = $card;
                if($values["status_activo"] == "adeuda")
                {
                    if($card['saldo'] <= 0.10)
                    {
                        unset($info[$key]);
                        continue;
                    }
                }

                if($values["status_activo"] == "pagada")
                {
                    if($card['saldo'] > 0.10)
                    {
                        unset($info[$key]);
                        continue;
                    }
                }
			}//foreach
        }else{
            foreach($comprobantes as $key => $val){
                $card['serie']="E";
                $card['folio']=$val['instanciaServicioId'];
                $card['nameContact']=$val['nameContact'];
                $card['nombre']=$val['name'];
                $card['fecha'] = date('Y/m/d',strtotime($val['date']));
                $card['fecha'] = $this->Util()->GetMesDiagonal($card['fecha']);
                $card['cxcDiscount']=0;
                $card['status']=1;
                $card['total_formato'] = $val['costo'];
                $card['total'] = $val['costo'];
                $card['instanciaServicioId'] = $val['instanciaServicioId'];
                $card['comprobanteId'] = $val['instanciaServicioId'];
                $card['efectivo'] = true;
                $card['facturador'] = 'Efectivo';
                //si tiene solicitud de cancelacion se debe omitir.
                $sqlQuery = "SELECT solicitud_cancelacion_id FROM pending_cfdi_cancel  WHERE cfdi_id = '".$val["comprobanteId"]."' AND deleted_at IS NULL AND status = '".CFDI_CANCEL_STATUS_PENDING."'";
                $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
                $requestCancel = $this->Util()->DBSelect($id_empresa)->GetSingle();
                if($requestCancel)
                    continue;

                $sqlQuery = "SELECT SUM(amount) FROM payment
                             WHERE instanciaServicioId = '".$val["instanciaServicioId"]."' and paymentStatus='activo' ";
                $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
                $card["payment"] = $this->Util()->DBSelect($id_empresa)->GetSingle();

                $card['saldo'] = $card["total_formato"] - $card["payment"];
				$monedaComprobante = "";
				switch($val['tipoDeMoneda']){ 
					case "peso": $monedaComprobante = "MXN"; break;
					case "dolar": $monedaComprobante = "USD"; break;
					case "euro": $monedaComprobante = "EUR"; break;
				}
             	$card["moneda"] = $monedaComprobante;

                if($val['costo'] > 0)
                    $info[$key] = $card;
            }
		}//if
			$data["items"] = $info;
			$data["total"] = count($comprobantes);
			return $data;
		}
	}//SearchComprobantesByRfc
    function searchCxC($values){
      $id_empresa = $_SESSION['empresaId'];
      $year =  $values['year'];
	  $ffact ="";
	  $innerPer = "";
	  $mainFilter ="";
      if($values['facturador'])
          $mainFilter .=" AND cm.rfcId = '".$values['facturador']."' ";

      if($values['serie'])
          $mainFilter .= "  AND cm.serie='".$values['serie']."' ";
      if($values['folio'])
          $mainFilter .= "  AND cm.folio>='".$values['folio']."' ";
      if($values['folioA'])
            $mainFilter .= " AND cm.folio<='".$values['folioA']."' ";
      if($values['nombre'])
         $mainFilter .= ' AND (cu.nameContact LIKE "%'.$values['nombre'].'%" OR co.name LIKE "%'.$values['nombre'].'%")';
      if($values["year"])
          $mainFilter .=" and year(cm.fecha)= $year ";

      $innerPer .=" inner join contractPermiso p ON co.contractId=p.contractId AND p.personalId IN (".implode(',',$values['respCuenta']).") ";

      $sql =  "select cm.comprobanteId,cm.serie,cm.folio,cm.fecha,cm.total,cu.nameContact,co.name,co.nombreComercial,
                       cm.facturador,co.contractId, co.rfc 
                from (SELECT suba.comprobanteId, suba.userId, suba.serie, suba.folio, suba.fecha, suba.total, suba.rfcId, 
                      suba.status, suba.tiposComprobanteId,subb.claveFacturador facturador,
                      subb.razonSocial, subb.rfc rfcFacturador
                      FROM comprobante suba
                      INNER JOIN rfc subb ON suba.rfcId = subb.rfcId) cm
                inner join contract co ON cm.userId=co.contractId
                $innerPer
                inner join customer cu ON cu.customerId=co.customerId AND cu.active='1'
                where cm.status='1' AND cm.tiposComprobanteId not in(10)
                $mainFilter
                group by cm.comprobanteId order by trim(char(09) from trim(cu.nameContact)) ASC,trim(char(09) from trim(co.name)) ASC,cm.fecha DESC
                ";
        $this->Util()->DBSelect($id_empresa)->setQuery($sql);
        $comprobantes = $this->Util()->DBSelect($id_empresa)->GetResult();
        $items =[];
        foreach($comprobantes as $key => $val){
            $card['serie']=$val['serie'];
            $card['folio']=$val['folio'];
            $card['nameContact']=$val['nameContact'];
            $card['rfc']=$val['rfc'];
            $card['nombre']=$val['name'];
            $card['contractId']=$val['contractId'];
            $card['fecha'] = date('Y/m/d',strtotime($val['fecha']));
            $card['fecha'] = $this->Util()->GetMesDiagonal($card['fecha']);
            $card['cxcDiscount']=0;
            $card['status']=$val['status'];
            $card['total'] = $val['total'];
            $card['total_formato'] = $val['total'];
            $card['comprobanteId'] = $val['comprobanteId'];
            $card['facturador'] = $val['facturador'];

            //get payments for comprobanteId
            $sqlQuery = "SELECT amount,paymentDate,deposito,payment.metodoDePago as mpago,concat(comprobante.serie,comprobante.folio) as folioPago FROM payment
                          left join comprobante ON payment.comprobantePagoId=comprobante.comprobanteId
						WHERE payment.comprobanteId = '".$val["comprobanteId"]."' and payment.paymentStatus='activo' ";

            $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
            $card["payments"] = $this->Util()->DBSelect($id_empresa)->GetResult();

            $sqlQuery = "SELECT SUM(amount) FROM payment
						WHERE comprobanteId = '".$val["comprobanteId"]."' and paymentStatus='activo'";
            $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
            $card["payment"] = $this->Util()->DBSelect($id_empresa)->GetSingle();

            //saldo
            $card['saldo'] = $card["total"] - $card["payment"];
            $items[$key] = $card;
        }
        $data["items"] = $items;
        $data["pages"] = $pages;
        $data["total"] = count($items);
        return $data;
    }
    /*
     * funcion getSaldo
     * $anio es el año que se desea saber cuando tiene de saldo
     * $contractId identificador del contrato que se desea saber su saldo
     * retorna $saldo , es la suma total de todas las facturas pendientes por liquidar
     * emitidas de $anio hacia atras.
     */
    function getSaldo($anio,$contractId, $filtro = []){
        $strFilter = "";
        if($filtro['facturador'])
            $strFilter .=" AND a.rfcId = '".$filtro['facturador']."' ";

        $id_empresa = $_SESSION['empresaId'];
        $sql ="select sum(a.total) as total,sum(b.pagos) as pagos from comprobante a 
                left join (select comprobanteId,sum(amount) as pagos from payment where paymentStatus='activo' group by comprobanteId) b on a.comprobanteId=b.comprobanteId
                where year(a.fecha)<='".$anio."' and a.userId='".$contractId."' and a.status='1' and a.tiposComprobanteId not in(10)
                ".$strFilter."
                group by a.userId";
        $this->Util()->DBSelect($id_empresa)->setQuery($sql);
        $row = $this->Util()->DBSelect($id_empresa)->GetRow();

        return $row['total']-$row['pagos'];
    }
	function SearchCuentasPorCobrar($values){
		//print_r($values);
		//Viene del Buscador o viene del Modulo
		if($values['facturador'] == '0' || $values['facturador'] == '')
		{
			$values['facturador'] = "Huerin";
			$arr1=$this->SearchCuentasPorCobrar($values);
			$values['facturador']="Braun";
			$arr2=$this->SearchCuentasPorCobrar($values);
			$values['facturador']="Efectivo";
			$arr3=$this->SearchCuentasPorCobrar($values);
			$data=array_merge_recursive($arr1,$arr2,$arr3);

			return $data;
		}
		else
		{
			global $user;
			$sqlSearch = '';
			if($values['facturador']){
				if($values['facturador']!="Efectivo")
				{
					//$sqlSearch .= ' AND c.empresaId = "'.$values['facturador'].'"';
				}

					switch($values['facturador'])
					{
						case "15": $facturador = "Huerin"; break;
						case "20": $facturador = "Braun"; break;
						default: $facturador = "Efectivo";
					}
					if($values['facturador'] != "Huerin")
					{
						$sqlSearch .= ' AND contract.facturador = "'.$facturador.'"';
					}
			}//if

			if($values['subordinados']){

				$sqlSearch .= ' AND (personal.jefeSocio = "'.$values['respCuenta'].'" OR
								personal.jefeSupervisor = "'.$values['respCuenta'].'" OR
								personal.jefeGerente = "'.$values['respCuenta'].'" OR
								personal.jefeContador = "'.$values['respCuenta'].'"';

				if($values['respCuenta'])
					$sqlSearch .= ' OR contract.responsableCuenta = "'.$values['respCuenta'].'"';

				$sqlSearch .= ')';

			}elseif($values['respCuenta']){
				$sqlSearch .= ' AND contract.responsableCuenta = "'.$values['respCuenta'].'"';
			}

			if($values['folio'] && !$values["folioA"]){
				if($values['facturador']=="Efectivo")
				$sqlSearch .= ' AND instanciaServicio.instanciaServicioId = "'.$values['folio'].'"';
				else
				$sqlSearch .= ' AND c.folio = "'.$values['folio'].'"';
			}//if

			if($values['folio'] && $values["folioA"]){
				if($values['facturador']=="Efectivo")
				$sqlSearch .= ' AND instanciaServicio.instanciaServicioId >= "'.$values['folio'].'" AND instanciaServicio.instanciaServicioId <="'.$values["folioA"].'"';
				else
				$sqlSearch .= ' AND c.folio >= "'.$values['folio'].'" AND c.folio <="'.$values["folioA"].'"';
			}//if

			if($values['nombre']){

				$sqlSearch .= ' AND (customer.nameContact LIKE "%'.$values['nombre'].'%" OR contract.name LIKE "%'.$values['nombre'].'%")';
				//cambiar para encontrar solo al especifico
				//$sqlSearch .= ' AND (customer.customerId = "'.$values['cliente'].'")';

			}//if

			if($values['mes']){
				if($values['facturador']=="Efectivo")
				$sqlSearch .= ' AND EXTRACT(MONTH FROM instanciaServicio.date) = '.$values['mes'];
				else
				$sqlSearch .= ' AND EXTRACT(MONTH FROM c.fecha) = '.$values['mes'];
			}//if

			if($values['anio'])
			{
				if($values['facturador']=="Efectivo")
				$sqlSearch .= ' AND EXTRACT(YEAR FROM instanciaServicio.date) = '.intval($values['anio']);
				else
				$sqlSearch .= ' AND EXTRACT(YEAR FROM c.fecha) = '.intval($values['anio']);
			}

			$id_rfc = $this->getRfcActive();
			if($values['facturador'] != "Efectivo")
			{
				$sql = "SELECT *, c.status AS status, c.comprobanteId AS comprobanteId,
						customer.nameContact AS nameContact FROM comprobante AS c
						LEFT JOIN contract ON contract.contractId = c.userId
						LEFT JOIN customer ON customer.customerId = contract.customerId
						LEFT JOIN personal ON contract.responsableCuenta = personal.personalId
						WHERE c.status='1' AND customer.active = '1'
						".$sqlSearch."
						AND ((MONTH(fecha) >= '".BALANCE_MONTH."'
						AND YEAR(fecha) >= '".BALANCE_YEAR."')
						OR YEAR(fecha) > '".BALANCE_YEAR."')
						ORDER BY fecha DESC ".$sqlAdd;
			}
			else
			{
				$sql = "SELECT * FROM instanciaServicio
						LEFT JOIN servicio ON instanciaServicio.servicioId=servicio.servicioId
						LEFT JOIN contract ON servicio.contractId=contract.contractId
						LEFT JOIN customer ON contract.customerId=customer.customerId
						LEFT JOIN personal ON contract.responsableCuenta = personal.personalId
						WHERE contract.facturador='Efectivo' AND date > '2015-12-31'
						AND customer.active = '1'".$sqlSearch;
			}
			$id_empresa = $_SESSION['empresaId'];
			//echo $sql;
			//echo $sql;
			$this->Util()->DBSelect($id_empresa)->setQuery($sql);
			$comprobantes = $this->Util()->DBSelect($id_empresa)->GetResult();

			$info = array();

			if($values['facturador'] != 'Efectivo')
			{
				foreach($comprobantes as $key => $val){

					$user->setUserId($val['userId'],1);
					$usr = $user->GetUserInfo();
					$card['serie'] = $val['serie'];
					$card['folio'] = $val['folio'];
					$card['rfc'] = $usr['rfc'];
					//$card['nombre'] = $usr['nombre'];
					$card['nombre']=$val['name'];
					$card['fecha'] = date('Y/m/d',strtotime($val['fecha']));
          			$card['fecha'] = $this->Util()->GetMesDiagonal($card['fecha']);

					$card['subTotal'] = $val['subTotal'];
					$card['porcentajeDescuento'] = $val['porcentajeDescuento'];
					$card['descuento'] = $val['descuento'];
					$card['ivaTotal'] = $val['ivaTotal'];

					//aplicar descuento
					$card['cxcAmountDiscount'] = $val['total'] * ($val['cxcDiscount'] / 100);
					$val['total'] = $val['total'] - $card['cxcAmountDiscount'];
					$card['total'] = $val['total'];


					$card['total_formato'] = number_format($val['total'],2,'.',',');
					$card['subtotal_formato'] = number_format($val['subTotal'],2,'.',',');
					$card['iva_formato'] = number_format($val['ivaTotal'],2,'.',',');
					$card['tipoDeMoneda'] = $val['tipoDeMoneda'];
					$card['tipoDeCambio'] = $val['tipoDeCambio'];
					$card['facturador'] = $val['facturador'];
					$card['porcentajeRetIva'] = $val['porcentajeRetIva'];
					$card['porcentajeRetIsr'] = $val['porcentajeRetIsr'];
					$card['porcentajeIEPS'] = $val['porcentajeIEPS'];
					$card['comprobanteId'] = $val['comprobanteId'];
					$card['status'] = $val['status'];
					$card['tipoDeComprobante'] = $val['tipoDeComprobante'];
					$card['cxcDiscount'] = $val['cxcDiscount'];

					$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
					$card['nameContact'] = $val["nameContact"];

					$card['instanciaServicioId'] = $val['instanciaServicioId'];

					$timbreFiscal = unserialize($val['timbreFiscal']);
					$card["uuid"] = $timbreFiscal["UUID"];

					//get payments
					$sqlQuery = "SELECT * FROM payment
						WHERE comprobanteId = '".$val["comprobanteId"]."' and paymentStatus='activo'";
					$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
					$card["payments"] = $this->Util()->DBSelect($id_empresa)->GetResult();

					$sqlQuery = "SELECT SUM(amount) FROM payment
						WHERE comprobanteId = '".$val["comprobanteId"]."' and paymentStatus='activo'";
					$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
					$card["payment"] = $this->Util()->DBSelect($id_empresa)->GetSingle();

					//saldo
					$card['saldo'] = $card["total"] - $card["payment"];

					$info[$key] = $card;

					if($values["status_activo"] == "adeuda")
					{
						if($card['saldo'] <= 0.10)
						{
							unset($info[$key]);
							continue;
						}
					}

					if($values["status_activo"] == "pagada")
					{
						if($card['saldo'] > 0.10)
						{
							unset($info[$key]);
							continue;
						}
					}

				}//foreach
			}
			else
			{

				foreach($comprobantes as $key => $val){

					$card['serie']="E";
					$card['folio']=$val['instanciaServicioId'];
					$card['nameContact']=$val['nameContact'];
					$card['nombre']=$val['name'];
					$card['fecha'] = date('Y/m/d',strtotime($val['date']));
          			$card['fecha'] = $this->Util()->GetMesDiagonal($card['fecha']);
					$card['cxcDiscount']=0;
					$card['status']=1;
					$card['total_formato'] = $val['costo'];
					$card['total'] = $val['costo'];
					$card['instanciaServicioId'] = $val['instanciaServicioId'];
					$card['comprobanteId'] = $val['instanciaServicioId'];
					$card['efectivo'] = true;
					$card['facturador'] = 'Efectivo';

					$sqlQuery = "SELECT SUM(amount) FROM payment
						WHERE instanciaServicioId = '".$val["instanciaServicioId"]."' and paymentStatus='activo'";
					$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
					$card["payment"] = $this->Util()->DBSelect($id_empresa)->GetSingle();

					$card['saldo'] = $card["total_formato"] - $card["payment"];

					if($val['costo'] > 0)
						$info[$key] = $card;

				}

			}//if

			$data["items"] = $info;
			$data["pages"] = $pages;
			$data["total"] = count($comprobantes);

			return $data;
		}
	}//SearchComprobantesByRfc

	public function Edit($id, $discount)
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->ValidateInteger($discount);
		$this->Util()->DB()->setQuery("
			UPDATE
				comprobante
			SET
				`cxcDiscount` = '".$discount."'
			WHERE comprobanteId = '".$id."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(10046, "complete", "La CxC fue Actualizada correctamente");
		$this->Util()->PrintErrors();
		return true;
	}

	public function AddPayment($id, $formaDePago,$amount,$deposito=0,$fecha,$efectivo=false, $comprobantePago, $tipoDeMoneda='MXN', $tipoCambio=1, $confirmAmount = null)
	{
	    $amount = $this->Util()->limpiaNumero($amount);
        $deposito = $this->Util()->limpiaNumero($deposito);
	    if(!$this->Util()->validateDateFormat($fecha,'Fecha','d-m-Y'))
	        $fecha = date('d-m-Y');
        else
            $fecha = $this->Util()->FormatDateMySql($fecha);

        if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->ValidateFloat($amount);

		if($amount<0.01)
		{
			$this->Util()->setError(10046, "error", "La cantidad a pagar debe de ser mayor a 0");
			$this->Util()->PrintErrors();
			return false;
		}

		// Obtener confirmAmount del parámetro
		$confirmAmount = $confirmAmount !== null ? $this->Util()->limpiaNumero($confirmAmount) : null;

		$comprobante = new Comprobante;

		if($efectivo)
			$compInfo = $comprobante->GetInfoComprobante($id,true);
		else
			$compInfo = $comprobante->GetInfoComprobante($id);

		// Guardar el monto original capturado
		$originalAmount = $amount;
		
		// Validar que el monto no exceda el saldo (con conversión de moneda si aplica)
		$monedaComprobante = "";
		switch($compInfo['tipoDeMoneda']){ 
            case "peso": $monedaComprobante = "MXN"; break;
            case "dolar": $monedaComprobante = "USD"; break;
            case "euro": $monedaComprobante = "EUR"; break;
        }
		if(empty($monedaComprobante)) {
			$this->Util()->setError(10046, "error", "Moneda del comprobante no reconocida");
			$this->Util()->PrintErrors();
			return false;
		}
		
		// Validar que no se soporten pagos entre EUR y USD
		if (($tipoDeMoneda == 'EUR' && $monedaComprobante == 'USD') || ($tipoDeMoneda == 'USD' && $monedaComprobante == 'EUR')) {
			$this->Util()->setError(10046, "error", "Pagos entre EUR y USD no están soportados");
			$this->Util()->PrintErrors();
			return false;
		}
		
		// Validar confirmAmount si monedas diferentes
		if ($tipoDeMoneda != $monedaComprobante) {
			if ($confirmAmount === null || $confirmAmount <= 0) {
				$this->Util()->setError(10046, "error", "Importe de confirmación en {$monedaComprobante} es requerido");
				$this->Util()->PrintErrors();
				return false;
			}
		}
		

		$currencyConverter = new CurrencyConverter();
		// Calcular equivalenciaDR directamente usando confirmAmount si monedas diferentes
		if ($tipoDeMoneda != $monedaComprobante) {
			$equivalenciaDR = $currencyConverter->calculateEquivalenciaDRFromAmount($confirmAmount, $amount);
			$impPagado = $confirmAmount;
		} else {
			$equivalenciaDR = 1; // Misma moneda, no hay conversión
			$impPagado = $amount;
		}
		
		// Validación para asegurar que la conversión es consistente
		$impInverso = $currencyConverter->reverseConvertAmount($impPagado, $equivalenciaDR);
		if (abs($impInverso - $amount) > 0.1) {
			$this->Util()->setError(10046, "error", "Error en la conversión de moneda. Importe original: ".number_format($amount,2)." ".$tipoDeMoneda.". Importe invertido: ".number_format($impInverso, 2)." ".$tipoDeMoneda.".");
			$this->Util()->PrintErrors();
			return false;
		}
		// Validar que el monto convertido no exceda el saldo
		if($impPagado > $compInfo["saldo"])
		{
			if($tipoDeMoneda != $monedaComprobante) {
				$this->Util()->setError(10046, "error", "El importe convertido (".number_format($impPagado, 4)." ".$monedaComprobante.") excede el saldo disponible (".number_format($compInfo["saldo"], 2)." ".$monedaComprobante.")");
			} else {
				$this->Util()->setError(10046, "error", "El importe no puede ser mayor al saldo disponible (".number_format($compInfo["saldo"], 2).")");
			}
			$this->Util()->PrintErrors();
			return false;
		}
		
		// Usar el monto convertido para reducir saldos

        $xmlReader = new XmlReaderService;
        $empresaId = $compInfo['empresaId'];
        $rfcActivo = $compInfo['rfcId'];
        $fileName  = "SIGN_".$compInfo['xml'];
        $xmlPath = DOC_ROOT.'/empresas/'.$empresaId.'/certificados/'.$rfcActivo.'/facturas/xml/'.$fileName.".xml";

        if(!file_exists($xmlPath)) {
            $this->Util()->setError(10046, "error", "Documento relacionado no encontrado");
            $this->Util()->PrintErrors();
            return false;
        }

        // Revisión B de Complemento de pago
        // leer los tipos de impuestos de tipo traslados que tiene la factura y calcularlo sobre el monto pagado
        $xmlData = $xmlReader->execute($xmlPath, $empresaId,$compInfo['comprobanteId']);
        $totalDR = $xmlData['cfdi']['Total'];

        // Calcular impuestos usando el servicio TaxCalculator
        $taxCalculator = new TaxCalculator();

		$traslados = $xmlData['impuestos']['traslados'];
        $result = $taxCalculator->calculateTaxes($traslados, $totalDR, (float)$impPagado, $equivalenciaDR,(float)$amount);

        $impuestosDR = [
            'retenciones' => [], // por si se requiere en el futuro
            'traslados'   => $result['trasladosDR']
        ];

        $impuestosP = [
            'retenciones' => [], // por si se requiere en el futuro
            'traslados'   => $result['trasladosP']
        ];

		$user = new User;

		if($efectivo)
		$user->setUserId($compInfo['contractId'],1);
		else
		$user->setUserId($compInfo['userId'],1);

        $comprobanteId = null;
		if($comprobantePago){
			$comprobantePago = new ComprobantePago();

			$infoPago = new stdClass();
            $infoPago->impuestosDR = $impuestosDR;
            $infoPago->impuestosP = $impuestosP;
			$infoPago->fecha = $fecha;
			$infoPago->amount = $originalAmount; // El monto original en la moneda de pago
			$infoPago->impPagado = $impPagado; // Monto confirmado en moneda del comprobante
			$infoPago->equivalenciaDR = $equivalenciaDR;
			$infoPago->metodoDePago = 'NO DEBE EXISTIR';
			$infoPago->formaDePago = $formaDePago;
			$infoPago->tipoDeCambio = $tipoCambio;
			$infoPago->tipoDeMoneda = $tipoDeMoneda;
			$infoPago->operacion = uniqid();
			$comprobanteId = $comprobantePago->generar($compInfo, $infoPago);

			if($_SESSION['errorPac']) {
				$this->Util()->setError(10046, "error", $_SESSION['errorPac']);
				$this->Util()->PrintErrors();
				return false;
			}
		}

		$ext = strtolower(end(explode('.', $_FILES["comprobante"]['name'])));

		$campo=($efectivo)?"instanciaServicioId":"comprobanteId";

		try {
			$this->Util()->DB()->setQuery("
				INSERT INTO  `payment` (
					`".$campo."` ,
					`metodoDePago` ,
					`amount` ,
					`deposito` ,
					`ext` ,
					`comprobantePagoId`,
					`paymentDate`,
					`tipoDeMoneda`,
					`tipoCambio`,
					`originalAmount`
					)
					VALUES (
					'".$id."',
					'".$formaDePago."',
					'".$impPagado."',
					'".$deposito."',
					'".$ext."',
					'".$comprobanteId."',
					'".$fecha."',
					'".$tipoDeMoneda."',
					'".$tipoCambio."',
					'".$originalAmount."'
				)");
			$paymentId = $this->Util()->DB()->InsertData();

			$folder = DOC_ROOT."/payments";
			$target_path = $folder ."/".$paymentId.".".$ext;

			@move_uploaded_file($_FILES["comprobante"]['tmp_name'], $target_path);

			$this->Util()->setError(10046, "complete", "Has Agregado un Pago correctamente");
			$this->Util()->PrintErrors();
			return true;
		} catch (Exception $e) {
			$this->Util()->setError(10046, "error", "Error al insertar el pago: " . $e->getMessage());
			$this->Util()->PrintErrors();
			return false;
		}
	}
    public function AddPaymentFromXml($file_xml, $metodoDePago,$amount,$deposito=0,$fecha,$efectivo=false, $comprobantePago)
    {
        $amount = $this->Util()->limpiaNumero($amount);
        $deposito = $this->Util()->limpiaNumero($deposito);
        if(!$this->Util()->validateDateFormat($fecha,'Fecha','d-m-Y'))
            $fecha = date('d-m-Y');
        else
            $fecha = $this->Util()->FormatDateMySql($fecha);

        $this->Util()->ValidateFloat($amount);
        if($amount<0.01)
        {
            $this->Util()->setError(10046, "error", "La cantidad a pagar debe de ser mayor a 0");
        }
        if($metodoDePago!="Saldo a Favor")
            if($deposito<$amount)
            {
                $this->Util()->setError(10046, "error", "El monto de pago no debe ser mayor al deposito");
            }
        $comprobante = new Comprobante;
        $compInfo = $dataXml =  $comprobante->getDataByXml($file_xml);
        $saldo = $this->Util()->limpiaNumero($compInfo['saldo']);
        if($amount>$saldo)
            $this->Util()->setError(10046, "error", "El monto de pago no debe ser mayor al saldo del documento");

        //termina validaciones
        if($this->Util()->PrintErrors())
            return false;

        $comprobanteId = null;
        if($comprobantePago){
            $comprobantePago = new ComprobantePago();
            $infoPago = new stdClass();
            $infoPago->fecha = $fecha;
            $infoPago->amount = $amount;
            $infoPago->metodoPago = $metodoDePago;
            $infoPago->operacion = uniqid();
            $comprobanteId = $comprobantePago->generarFromXml($compInfo, $infoPago);
            if($_SESSION['errorPac']) {
                $this->Util()->setError(10046, "error", $_SESSION['errorPac']);
                $this->Util()->PrintErrors();
                return false;
            }
        }

        $ext = strtolower(end(explode('.', $_FILES["comprobante"]['name'])));
        //guardamos por aparte los pagos realizados para tener control al buscar la facturas.
        $this->Util()->DB()->setQuery("
			INSERT INTO  payment_from_xml (
				`metodoDePago` ,
				`amount` ,
				`deposito` ,
				`ext` ,
				`folio`,
				`name_xml`,
				`uuid`,
				`comprobantePagoId`,
				`paymentDate`
				)
				VALUES (
				'".$metodoDePago."',
				'".$amount."',
				'".$deposito."',
				'".$ext."',
				'".$compInfo['folioComplete']."',
				'".$compInfo['nameXml']."',
				'".$compInfo['uuid']."',
				'".$comprobanteId."',
				'".$fecha."'
			)");
        //echo $this->Util()->DB()->getQuery();
        $paymentId = $this->Util()->DB()->InsertData();

        $folder = DOC_ROOT."/payments";
        $target_path = $folder ."/from_xml_".$paymentId.".".$ext;
        @move_uploaded_file($_FILES["comprobante"]['tmp_name'], $target_path);
        $this->Util()->setError(10046, "complete", "Has Agregado un Pago correctamente");
        $this->Util()->PrintErrors();
        return true;
    }

	public function DeletePayment($id)
	{
		try {

			$payment = $this->PaymentInfo($id);

			$eliminarPago = true;
			$comprobantePagoId = $payment["comprobantePagoId"];
			if($comprobantePagoId) {

				$cancelation = new Cancelation();
				$intentos_cancelacion = $cancelation->getCancelationAttempts($comprobantePagoId);
				if($intentos_cancelacion >= MAXIMO_INTENTOS_CANCELACION) {
					$this->Util()->setError(10046, "error", "Se ha alcanzado el número máximo de intentos de cancelación para este comprobante de pago. No se puede cancelar el pago, contacte al administrador.");
					$this->Util()->PrintErrors();
					return false;
				}

				$empresa = new Empresa();
				$empresa->setComprobanteId($comprobantePagoId);
				$empresa->setMotivoCancelacion("Pago eliminado");
				$empresa->setMotivoCancelacionSat('02');

				if(!$empresa->CancelarComprobante()){
					$eliminarPago = false;
				}
			}

			if(!$eliminarPago) {
				$this->Util()->setError(10046, "error", "Hubo un problema al cancelar el comprobante de pago, el pago no fue cancelado");
				$this->Util()->PrintErrors();
				return false;
			}
			$estatusPayment = 'cancelado';	
			$this->Util()->DB()->setQuery("UPDATE payment set paymentStatus='".$estatusPayment."' WHERE paymentId = '".$id."'");
			$this->Util()->DB()->UpdateData();
		
			$this->Util()->setError(10046, "complete", "El pago fue cancelado correctamente");
			$this->Util()->PrintErrors();
			return true;

		} catch (Exception $e) {

			$this->Util()->setError(10046, "error", "Error al eliminar el pago: " . $e->getMessage());
			$this->Util()->PrintErrors();
			return false;
		}
	}

    public function DeletePaymentFromXml($id)
    {
        $payment = $this->PaymentInfoFromXml($id);
        $eliminarPago = true;
        if($payment["comprobantePagoId"]) {

            $empresa = new Empresa();
            $empresa->setComprobanteId($payment["comprobantePagoId"]);
            $empresa->setMotivoCancelacion("Pago eliminado");

            if(!$empresa->CancelarComprobante()){
                $eliminarPago = false;
            }
        }

        if($eliminarPago === false){
            $this->Util()->setError(10046, "error", "Hubo un problema al cancelar el comprobante de pago, el pago no fue cancelado");
            $this->Util()->PrintErrors();
            return false;
        }

        $this->Util()->DB()->setQuery("
			UPDATE payment_from_xml set payment_status='cancelado' WHERE payment_id = '".$id."'");
        $this->Util()->DB()->UpdateData();

        $this->Util()->setError(10046, "complete", "El pago fue borrado correctamente");
        $this->Util()->PrintErrors();
        return true;
    }

	public function PaymentInfo($id)
	{
		$this->Util()->DB()->setQuery("
			SELECT * FROM payment WHERE paymentId = '".$id."'");
		$row = $this->Util()->DB()->GetRow();

		return $row;
	}
    public function PaymentInfoFromXml($id)
    {
        $this->Util()->DB()->setQuery("
			SELECT * FROM payment_from_xml WHERE payment_id = '".$id."'");
        $row = $this->Util()->DB()->GetRow();
        return $row;
    }

}
?>