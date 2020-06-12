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
    function ListSerieTipoComprobantes(){
	    $sql = "SELECT a.serieId,a.serie,a.consecutivo,c.razonSocial,b.tiposComprobanteId from serie a 
                INNER JOIN tiposComprobante b ON a.tiposComprobanteId = b.tiposComprobanteId 
                INNER JOIN rfc c ON a.rfcId = c.rfcId
                WHERE b.tipoDeComprobante = 'ingreso' and c.activo='si' ";
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql);
        return $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult($sql);
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
		return $monedas;
	}

	function ListExcentoIva()
	{
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->EnumSelect("concepto", "excentoIva");
		
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

    public function accessAnyContract() {
        if(!$_SESSION['User']['roleId'])
            return false;

        $sql = "SELECT allow_visualize_any_contract FROM roles WHERE rolId='".$_SESSION['User']['roleId']."' AND status='activo' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        return $row ? $row['allow_visualize_any_contract'] : false;
    }

    public function accessAnyRol() {
        if(!$_SESSION['User']['roleId'])
            return false;

        $sql = "SELECT allow_visualize_any_rol FROM roles WHERE rolId='".$_SESSION['User']['roleId']."' AND status='activo' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        return $row ? $row['allow_visualize_any_rol'] : false;
    }

    public function accessAnyDepartament() {
        if(!$_SESSION['User']['roleId'])
            return false;

        $sql = "SELECT allow_any_departament FROM roles WHERE rolId='".$_SESSION['User']['roleId']."' AND status='activo' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        return $row ? $row['allow_any_departament'] : false;
    }

    public function accessAnyEmployee() {
        if(!$_SESSION['User']['roleId'])
            return false;

        $sql = "SELECT allow_any_employee FROM roles WHERE rolId='".$_SESSION['User']['roleId']."' AND status='activo' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        return $row ? $row['allow_any_employee'] : false;
    }
}


?>