<?php

class CxC extends Producto
{
	function SearchCuentasPorCobrarNoReporte($contracts, $values){
		//Viene del Buscador o viene del Modulo
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
			{
				$sqlSearch .= ' AND EXTRACT(YEAR FROM c.fecha) = '.intval($values['anio']);
			}
			

			$id_rfc = $this->getRfcActive();
				$sql = "SELECT *, c.status AS status, c.comprobanteId AS comprobanteId,
						customer.nameContact AS nameContact FROM comprobante AS c
						LEFT JOIN contract ON contract.contractId = c.userId
						LEFT JOIN customer ON customer.customerId = contract.customerId
						LEFT JOIN personal ON contract.responsableCuenta = personal.personalId
						WHERE c.status='1' AND c.tiposComprobanteId != 10 AND customer.active = '1'
						".$sqlSearch."
						ORDER BY fecha DESC ".$sqlAdd;
						
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
					$card['nombre']=$val['nombreComercial'];
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

					$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
					$card['nameContact'] = $val["nameContact"];

					$card['instanciaServicioId'] = $val['instanciaServicioId'];

					$timbreFiscal = unserialize($val['timbreFiscal']);
					$card["uuid"] = $timbreFiscal["UUID"];

					//get payments
					$sqlQuery = "SELECT * FROM payment
						WHERE comprobanteId = '".$val["comprobanteId"]."'";
					$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
					$card["payments"] = $this->Util()->DBSelect($id_empresa)->GetResult();

					$sqlQuery = "SELECT SUM(amount) FROM payment
						WHERE comprobanteId = '".$val["comprobanteId"]."'";
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
					$card['nombre']=$val['nombreComercial'];
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
						WHERE instanciaServicioId = '".$val["instanciaServicioId"]."'";
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
    function searchCxC($values){
      $id_empresa = $_SESSION['empresaId'];
      $anio =  $values['anio'];
	  $ffact ="";
	  $innerPer = "";
	  $mainFilter ="";
      if($values['facturador'])
          $ffact .="  AND co.facturador IN ('".$values['facturador']."')";
      else
          $ffact .= " AND co.facturador IN(".implode(',',unserialize(FACTURADOR)).")";

      if($values['serie'])
          $mainFilter .= "  AND cm.serie='".$values['serie']."' ";
      if($values['folio'])
          $mainFilter .= "  AND cm.folio>='".$values['folio']."' ";
      if($values['folioA'])
            $mainFilter .= " AND cm.folio<='".$values['folioA']."' ";
      if($values['nombre'])
         $mainFilter .= ' AND (cu.nameContact LIKE "%'.$values['nombre'].'%" OR co.name LIKE "%'.$values['nombre'].'%")';

      $innerPer .=" inner join contractPermiso p ON co.contractId=p.contractId AND p.personalId IN (".implode(',',$values['respCuenta']).") ";

      $sql =  " select cm.comprobanteId,cm.serie,cm.folio,cm.fecha,cm.total,cu.nameContact,co.name,co.nombreComercial,co.facturador,co.contractId
                 from comprobante cm
                 inner join contract co ON cm.userId=co.contractId $ffact
                 $innerPer
                 inner join customer cu ON cu.customerId=co.customerId AND cu.active='1'
                 where cm.status='1' and year(cm.fecha)= $anio AND cm.tiposComprobanteId not in(10)
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
            $card['nombre']=$val['nombreComercial'];
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
                          inner join comprobante ON payment.comprobantePagoId=comprobante.comprobanteId
						WHERE payment.comprobanteId = '".$val["comprobanteId"]."'";

            $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
            $card["payments"] = $this->Util()->DBSelect($id_empresa)->GetResult();

            $sqlQuery = "SELECT SUM(amount) FROM payment
						WHERE comprobanteId = '".$val["comprobanteId"]."'";
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
    function getSaldo($anio,$contractId){
        $id_empresa = $_SESSION['empresaId'];
        $sql ="select sum(a.total) as total,sum(b.pagos) as pagos from comprobante a 
                left join (select comprobanteId,sum(amount) as pagos from payment group by comprobanteId) b on a.comprobanteId=b.comprobanteId
                where year(a.fecha)<='".$anio."' and a.userId='".$contractId."' and a.status='1' and a.tiposComprobanteId not in(10)
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
					$card['nombre']=$val['nombreComercial'];
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
						WHERE comprobanteId = '".$val["comprobanteId"]."'";
					$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
					$card["payments"] = $this->Util()->DBSelect($id_empresa)->GetResult();

					$sqlQuery = "SELECT SUM(amount) FROM payment
						WHERE comprobanteId = '".$val["comprobanteId"]."'";
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
					$card['nombre']=$val['nombreComercial'];
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
						WHERE instanciaServicioId = '".$val["instanciaServicioId"]."'";
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

	public function AddPayment($id, $metodoDePago,$amount,$deposito=0,$fecha,$efectivo=false, $comprobantePago)
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

		if($metodoDePago!="Saldo a Favor")
            if($deposito<$amount)
            {
                $this->Util()->setError(10046, "error", "El monto de pago no debe ser mayor al deposito");
                $this->Util()->PrintErrors();
                return false;
            }
		$comprobante = new Comprobante;

		if($efectivo)
			$compInfo = $comprobante->GetInfoComprobante($id,true);
		else
			$compInfo = $comprobante->GetInfoComprobante($id);

		$user = new User;

		if($efectivo)
		$user->setUserId($compInfo['contractId'],1);
		else
		$user->setUserId($compInfo['userId'],1);
		$usr = $user->GetUserInfo();

        if($metodoDePago == "Saldo a Favor")
        {
            if($usr["cxcSaldoFavor"] < $amount)
            {
                $this->Util()->setError(10046, "error", "El Saldo a Favor del cliente no es suficiente para cubrir el importe del pago.");
                $this->Util()->PrintErrors();
                return false;
            }
            else
            {
                $this->Util()->DB()->setQuery("
					UPDATE
						customer
					SET
						`cxcSaldoFavor` = cxcSaldoFavor - '".$amount."'
					WHERE customerId = '".$usr["customerId"]."'");
                $this->Util()->DB()->UpdateData();

            }
        }

        $comprobanteId = null;
		if($comprobantePago){
			$comprobantePago = new ComprobantePago();


			$infoPago = new stdClass();

			$infoPago->fecha = $fecha;
			$infoPago->amount = $amount;
			$infoPago->metodoPago = $metodoDePago;
			$infoPago->operacion = uniqid();
			$comprobanteId = $comprobantePago->generar($compInfo, $infoPago);

			if($_SESSION['errorPac']) {
				$this->Util()->setError(10046, "error", $_SESSION['errorPac']);
				$this->Util()->PrintErrors();
				return false;
			}
		}


		if($metodoDePago != "Saldo a Favor")
		{
			if($amount > $compInfo["saldo"])
			{
				$rest = $amount - $compInfo["saldo"];
				$amount = $compInfo["saldo"];

				$this->Util()->DB()->setQuery("
					UPDATE
						customer
					SET
						`cxcSaldoFavor` = cxcSaldoFavor + '".$rest."'
					WHERE customerId = '".$usr["customerId"]."'");
				$this->Util()->DB()->UpdateData();
			}
		}

		$ext = strtolower(end(explode('.', $_FILES["comprobante"]['name'])));

		$campo=($efectivo)?"instanciaServicioId":"comprobanteId";

		$this->Util()->DB()->setQuery("
			INSERT INTO  `payment` (
				`".$campo."` ,
				`metodoDePago` ,
				`amount` ,
				`deposito` ,
				`ext` ,
				`comprobantePagoId`,
				`paymentDate`
				)
				VALUES (
				'".$id."',
				'".$metodoDePago."',
				'".$amount."',
				'".$deposito."',
				'".$ext."',
				'".$comprobanteId."',
				'".$fecha."'
			)");
		$paymentId = $this->Util()->DB()->InsertData();

		$folder = DOC_ROOT."/payments";
		$target_path = $folder ."/".$paymentId.".".$ext;

		@move_uploaded_file($_FILES["comprobante"]['tmp_name'], $target_path);

		$this->Util()->setError(10046, "complete", "Has Agregado un Pago correctamente");
        $this->Util()->PrintErrors();
		return true;
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
				'".$compInfo['folio']."',
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
		$payment = $this->PaymentInfo($id);

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
			DELETE FROM payment WHERE paymentId = '".$id."'");
		$this->Util()->DB()->DeleteData();

		$this->Util()->setError(10046, "complete", "El pago fue borrado correctamente");
		$this->Util()->PrintErrors();
		return true;
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