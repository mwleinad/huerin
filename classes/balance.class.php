<?php 
class Balance extends Main
{
	public function GenerarPorRazonSocial($id, $month, $year)
  {
		global $User;
		$sql = "SELECT 
					customer.*, contract.*
				FROM 
					contract
				LEFT JOIN customer ON contract.customerId = customer.customerId 
				WHERE contract.contractId = '".$id."'";
		
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
		
		$data = array();

  		if(empty($month) && !empty($year))
		{
			$add = "AND  (YEAR(fecha) = '".$year."')";
		}

	  	if(!empty($month) && !empty($year))
		{
			$add = "AND  (MONTH(fecha) = '".$month."' AND YEAR(fecha) = '".$year."')";
		}

		foreach($result as $key => $val)
		{
			//sacar primero las facturas
			$sql = "SELECT
						serie, folio, fecha, total, comprobanteId, cxcDiscount
					FROM 
						comprobante
          WHERE status='1' AND userId = '".$val["contractId"]."'
          AND (
          	(MONTH(fecha) >= '".BALANCE_MONTH."'
          	AND YEAR(fecha) >= '".BALANCE_YEAR."')
          	OR YEAR(fecha) > '".BALANCE_YEAR."')
          AND status = '1' ".$add;

			$this->Util()->DB()->setQuery($sql);
			$facturas = $this->Util()->DB()->GetResult();
			
			foreach($facturas as $keyFactura => $factura)
			{
				$card = array();
					$card["comprobanteId"] = $factura["comprobanteId"];
				$card["concepto"] = "Factura #".$factura["serie"].$factura["folio"];
				$card['cxcAmountDiscount'] = $factura['total'] * ($factura['cxcDiscount'] / 100);
				$card["cargo"] = $factura["total"] - $card['cxcAmountDiscount'];
				$card["abono"] = 0;
				$factura["fecha"] = explode(" ", $factura["fecha"]);
				$card["fecha"] = $factura["fecha"][0];
				$data["movimientos"][] = $card;
				
				//get pagos
				$sql = "SELECT 
						paymentId, amount, paymentDate, comprobanteId
					FROM 
						payment
					WHERE comprobanteId = '".$factura["comprobanteId"]."'";
			
				$this->Util()->DB()->setQuery($sql);
				$pagos = $this->Util()->DB()->GetResult();
				
				foreach($pagos as $keyPago => $pago)
				{
					$card = array();
					$card["comprobanteId"] = $pago["comprobanteId"];
					$card["concepto"] = "Abono Folio #".$pago["paymentId"];
					$card["cargo"] = 0;
					$card["abono"] = $pago["amount"];
					$card["fecha"] = $pago["paymentDate"];;
					$data["movimientos"][] = $card;
				}
				
			}
		}
		
		if($data["movimientos"])
		{
			$data["movimientos"] = $this->Util()->orderMultiDimensionalArray ($data["movimientos"], "fecha", true);
			
			foreach($data["movimientos"] as $key => $value)
			{
				$data["cargos"] += $value["cargo"];
				$data["abonos"] += $value["abono"];
				$data["saldo"] += $value["cargo"] - $value["abono"];
				$data["movimientos"][$key]["saldo"] = $data["saldo"];
				$data["movimientos"][$key]["fecha"] = $this->Util()->setFormatDate($value["fecha"]);
			}
		}
		return $data;
  }
	public function Generate($id)
	{
		global $User;
		//echo "<pre>";
		$sql = "SELECT 
					customer.*, contract.*
				FROM 
					customer
				LEFT JOIN contract ON customer.customerId = contract.customerId 
				WHERE customer.customerId = '".$id."'";
		
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
		
		$data = array();

		foreach($result as $key => $val)
		{
			//sacar primero las facturas
			$sql = "SELECT 
						serie, folio, fecha, total, comprobanteId, cxcDiscount
					FROM 
						comprobante
          WHERE status='1' AND userId = '".$val["contractId"]."' AND ((MONTH(fecha) >= '".BALANCE_MONTH."' AND YEAR(fecha) >= '".BALANCE_YEAR."') OR YEAR(fecha) > '".BALANCE_YEAR."')";
			
			$this->Util()->DB()->setQuery($sql);
			$facturas = $this->Util()->DB()->GetResult();
			
			foreach($facturas as $keyFactura => $factura)
			{
				$card = array();
					$card["comprobanteId"] = $factura["comprobanteId"];
				$card["concepto"] = "Factura #".$factura["serie"].$factura["folio"];
				$card['cxcAmountDiscount'] = $factura['total'] * ($factura['cxcDiscount'] / 100);
				$card["cargo"] = $factura["total"] - $card['cxcAmountDiscount'];
				$card["abono"] = 0;
				$factura["fecha"] = explode(" ", $factura["fecha"]);
				$card["fecha"] = $factura["fecha"][0];
				$data["movimientos"][] = $card;
				
				//get pagos
				$sql = "SELECT 
						paymentId, amount, paymentDate, comprobanteId
					FROM 
						payment
					WHERE comprobanteId = '".$factura["comprobanteId"]."'";
			
				$this->Util()->DB()->setQuery($sql);
				$pagos = $this->Util()->DB()->GetResult();
				
				foreach($pagos as $keyPago => $pago)
				{
					$card = array();
					$card["comprobanteId"] = $pago["comprobanteId"];
					$card["concepto"] = "Abono Folio #".$pago["paymentId"];
					$card["cargo"] = 0;
					$card["abono"] = $pago["amount"];
					$card["fecha"] = $pago["paymentDate"];;
					$data["movimientos"][] = $card;
				}
				
			}
		}
		
		if($data["movimientos"])
		{
			$data["movimientos"] = $this->Util()->orderMultiDimensionalArray ($data["movimientos"], "fecha");
			
			foreach($data["movimientos"] as $key => $value)
			{
				$data["cargos"] += $value["cargo"];
				$data["abonos"] += $value["abono"];
				$data["saldo"] += $value["cargo"] - $value["abono"];
				$data["movimientos"][$key]["saldo"] = $data["saldo"];
				$data["movimientos"][$key]["fecha"] = $this->Util()->setFormatDate($value["fecha"]);
			}
		}
//		print_r($result);
		return $data;
	}


}

?>