<?php

class Main
{
	protected $page;


	public function setPage($value)
	{
		$this->Util()->ValidateInteger($value, 9999999999, 0);
		$this->page = $value;
	}
	
	public function getPage()
	{
		return $this->page;
	}

	function ListProductos()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM product ORDER BY productId ASC");
		
		$result = $this->Util()->DB()->GetResult();
		
		foreach($result as $key => $periodo)
		{
		}
		return $result;
	}
	
	function ListTiposDeComprobantes()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM tiposComprobante ORDER BY tiposComprobanteId");
		
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
		
		return $result;
	}	

	function ListTiposDeComprobantesValidos()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
			SELECT * FROM tiposComprobante 
			RIGHT JOIN serie ON serie.tiposComprobanteId = tiposComprobante.tiposComprobanteId
			ORDER BY tiposComprobante.tiposComprobanteId");
		
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
		
		return $result;
	}	

	function InfoComprobante($id)
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM tiposComprobante WHERE tiposComprobanteId = '".$id."'");
		
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		
		return $result;
	}
	function ListIvas()
	{
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->EnumSelect("comprobante", "tasaIva");
		
		return $result;
	}

	function ListRetIsr()
	{
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->EnumSelect("comprobante", "porcentajeRetIsr");
		
		return $result;
	}

	function ListRetIva()
	{
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->EnumSelect("comprobante", "porcentajeRetIva");
		
		return $result;
	}

	function ListTipoDeMoneda()
	{
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->EnumSelect("comprobante", "tipoDeMoneda");
		
		return $result;
	}

	function ListTipoDeMoneda33()
	{
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->EnumSelect("comprobante", "tipoDeMoneda");

		$monedas = array();
		foreach($result as $key => $moneda)
		{
			switch($moneda)
			{
				case "peso":
					$monedas[$key]["tipo"] = "MXN";
					$monedas[$key]["moneda"] = "Peso";
					break;
				case "dolar":
					$monedas[$key]["tipo"] = "USD";
					$monedas[$key]["moneda"] = "Dolar";
					break;
				case "euro":
					$monedas[$key]["tipo"] = "EUR";
					$monedas[$key]["moneda"] = "Euro";
					break;
				case "quetzal":
					$monedas[$key]["tipo"] = "GTQ";
					$monedas[$key]["moneda"] = "quetzal";
					break;

			}
		}
//		print_r($monedas);
		return $monedas;
	}

	function ListExcentoIva()
	{
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->EnumSelect("concepto", "excentoIva");
		
		return $result;
	}

	function ListSocios()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM socio ORDER BY socioId");
		$result = $this->Util()->DB()->GetResult();
		return $result;
	}

	function SuggestProveedores($value)
	{
		$this->Util()->DB()->setQuery("SELECT * FROM usuario WHERE email LIKE '%".$value."%'ORDER BY email");
		$result = $this->Util()->DB()->GetResult();
		return $result;
	}

	public function Util() 
	{
		if($this->Util == null ) 
		{
			$this->Util = new Util();
		}
		return $this->Util;
	}
}


?>