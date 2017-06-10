<?php

class CxC extends Producto
{
	function SearchCuentasPorCobrar($values){
		if($values['facturador']=="0")
		{
			$values['facturador']="15";
			$arr1=$this->SearchCuentasPorCobrar($values);
			$values['facturador']="20";
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
				$sqlSearch .= ' AND c.empresaId = "'.$values['facturador'].'"';
			}//if

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

			if($values['facturador']){
				$id_rfc =($values['facturador']=="15")? 1 : 29;
			}//if
			
			if($values['facturador']!="Efectivo")
			{
			echo $sqlQuery = "SELECT *, c.status AS status, c.comprobanteId AS comprobanteId, customer.nameContact AS nameContact FROM comprobante AS c 
			LEFT JOIN contract ON contract.contractId = c.userId
			LEFT JOIN customer ON customer.customerId = contract.customerId
			WHERE c.rfcId = ".$id_rfc.$sqlSearch." AND ((MONTH(fecha) >= '".BALANCE_MONTH."' AND YEAR(fecha) >= '".BALANCE_YEAR."') OR YEAR(fecha) > '".BALANCE_YEAR."') ORDER BY fecha DESC ".$sqlAdd;
			}
			else
			{
				$sqlQuery="SELECT * FROM instanciaServicio
				LEFT JOIN servicio ON instanciaServicio.servicioId=servicio.servicioId
				LEFT JOIN contract ON servicio.contractId=contract.contractId
				LEFT JOIN customer ON contract.customerId=customer.customerId
				WHERE contract.facturador='Efectivo'".$sqlSearch;
			}
			$id_empresa = $_SESSION['empresaId'];
			
			$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
			$comprobantes = $this->Util()->DBSelect($id_empresa)->GetResult();

			//echo "<pre>";
			//print_r($comprobantes);
			
			$info = array();
			
			if($values['facturador']!="Efectivo")
			{
				foreach($comprobantes as $key => $val){
					
					$user->setUserId($val['userId'],1);
					$usr = $user->GetUserInfo();
					$card['serie'] = $val['serie'];
					$card['folio'] = $val['folio'];
					$card['rfc'] = $usr['rfc'];
					$card['nombre'] = $usr['nombre'];
					$card['fecha'] = date('d/m/Y',strtotime($val['fecha']));
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
					
					if($values["status_activo"] == "adeuda")
					{
						if($card['saldo'] <= 0)
						{
							unset($info[$key]);
							continue;
						}
					}

					if($values["status_activo"] == "pagada")
					{
						if($card['saldo'] > 0)
						{
							unset($info[$key]);
							continue;
						}
					}
								
					$info[$key] = $card;
					
				}//foreach
			}
			else
			{
				foreach($comprobantes as $key => $val){
					$card['serie']="E";
					$card['folio']=$val['instanciaServicioId'];
					$card['nameContact']=$val['nameContact'];
					$card['nombre']=$val['nombreComercial'];
					$card['fecha']=date('d/m/Y',strtotime($val['date']));
					$card['cxcDiscount']=0;
					$card['status']=1;
					$card['total_formato']=$val['costo'];
					$card['instanciaServicioId'] = $val['instanciaServicioId'];
					$card['comprobanteId'] = $val['instanciaServicioId'];
					$card['efectivo'] = true;

					$sqlQuery = "SELECT SUM(amount) FROM payment 
						WHERE instanciaServicioId = '".$val["instanciaServicioId"]."'";
					$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
					$card["payment"] = $this->Util()->DBSelect($id_empresa)->GetSingle();
					
					$card['saldo'] = $card["total_formato"] - $card["payment"];
					
					if($val['costo']>0)
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

	public function AddPayment($id, $metodoDePago, $amount, $fecha,$efectivo=false)
	{
		$fecha = $this->Util()->FormatDateMySql($fecha);
		if($this->Util()->PrintErrors()){ return false; }
		
		$this->Util()->ValidateFloat($amount);
		
		if($amount < 0.01)
		{
			$this->Util()->setError(10046, "error", "La cantidad a pagar debe de ser mayor a 0");
			$this->Util()->PrintErrors();
			return false;
		}
		
		
		if(!$_FILES["comprobante"]["name"])
		{
			$this->Util()->setError(10046, "error", "Debes de subir un comprobante de pago");
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
				`ext` ,
				`paymentDate`
				)
				VALUES (
				'".$id."',  
				'".$metodoDePago."',  
				'".$amount."',  
				'".$ext."',  
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

	public function DeletePayment($id)
	{
		$this->Util()->DB()->setQuery("
			DELETE FROM payment WHERE paymentId = '".$id."'");
		$this->Util()->DB()->DeleteData();

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
	
} 


?>