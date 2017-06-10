<?php

class Sucursal extends Rfc
{
	private $sucursalId;
	private $identificador;
	
	public function setSucursalId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->sucursalId = $value;
	}
	
	public function getSucursalId()
	{
		return $this->sucursalId;
	}	
	
	public function setIdentificador($value, $checkIfExists = 0)
	{
		$this->Util()->ValidateString($value, $max_chars=300, $minChars = 1, "Identificador");
		$this->identificador = $value;
	}

	public function getIdentificador()
	{
		return $this->identificador;
	}	

	function AddSucursal()
	{		
		if($this->Util()->PrintErrors()){ return false; }
				
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
			INSERT INTO `sucursal` ( 
				`empresaId`, 
				`rfcId`, 
				`identificador`,
				`pais`, 
				`calle`,
				`noInt`, 
				`noExt`, 
				`referencia`, 
				`colonia`, 
				`localidad`, 
				`municipio`,
				`ciudad`, 
				`estado`,				 
				`cp`
				) 
			VALUES (
				'".$this->getEmpresaId()."',
				'".$this->getRfcId()."',
				'".$this->identificador."',
				'".$this->getPais()."',
				'".$this->getCalle()."',
				'".$this->getNoInt()."',
				'".$this->getNoExt()."',
				'".$this->getReferencia()."',
				'".$this->getColonia()."',
				'".$this->getLocalidad()."',
				'".$this->getMunicipio()."',
				'".$this->getCiudad()."',
				'".$this->getEstado()."',
				'".$this->getCp()."'
				)"
			);
		$sucursalId = $this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();

		$this->Util()->setError(20009, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	function EditSucursal()
	{
		if($this->Util()->PrintErrors()){ return false; }
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
			UPDATE `sucursal` SET 
				`identificador` = '".$this->identificador."',
				`pais` = '".$this->getPais()."', 
				`calle` = '".$this->getCalle()."',
				`noInt` = '".$this->getNoInt()."', 
				`noExt` = '".$this->getNoExt()."', 
				`referencia` = '".$this->getReferencia()."', 
				`colonia` = '".$this->getColonia()."', 
				`localidad` = '".$this->getLocalidad()."', 
				`municipio` = '".$this->getMunicipio()."', 
				`ciudad` = '".$this->getCiudad()."', 
				`estado` = '".$this->getEstado()."', 
				`cp` = '".$this->getCp()."'
			WHERE sucursalId = '".$this->sucursalId."'");
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();

		$this->Util()->setError(20010, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	function SucursalInfo()
	{
		$this->Util()->DBSelect($this->getEmpresaId())->setQuery("
			SELECT * FROM sucursal 
			LEFT JOIN rfc ON rfc.rfcId = sucursal.rfcId
			WHERE sucursal.sucursalId ='".$this->sucursalId."'");
		$sucursal = $this->Util()->DBSelect($this->getEmpresaId())->GetRow();
	
		return $sucursal;
	}
	
		
	function GetSucursalesByRfc()
	{
		$this->Util()->DBSelect($this->getEmpresaId())->setQuery("
			SELECT * FROM sucursal 
			LEFT JOIN rfc ON rfc.rfcId = sucursal.rfcId
			WHERE sucursal.rfcId ='".$this->getRfcId()."'");
		$sucursales = $this->Util()->DBSelect($this->getEmpresaId())->GetResult();
	
		return $sucursales;
	}
	
	function GetSucursalInfoById()
	{
		$this->Util()->DBSelect($this->getEmpresaId())->setQuery("
			SELECT * FROM sucursal 
			WHERE sucursalId ='".$this->sucursalId."'");
		$sucursal = $this->Util()->DBSelect($this->getEmpresaId())->GetRow();
	
		return $sucursal;
	}

	function DeleteSucursal()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("DELETE FROM sucursal WHERE sucursalId = '".$this->sucursalId."'");
		$this->Util()->DBSelect($_SESSION["empresaId"])->DeleteData();
		$this->Util()->setError(20008, "complete");
		$this->Util()->PrintErrors();
		return true;
	}
	
	function ChangeStatus()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("UPDATE sucursal SET sucursalActiva = 'no'");
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();

		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("UPDATE sucursal SET sucursalActiva = 'si' WHERE sucursalId = '".$this->sucursalId."'");
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
		return true;
	}	
}


?>