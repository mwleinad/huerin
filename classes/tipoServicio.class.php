<?php 

class TipoServicio extends Main
{
	private $tipoServicioId;
	private $nombreServicio;
	private $costo;
	private $periodicidad;
	private $departamentoId;
	private $costoVisual;
	private $mostrarCostoVisual;

	public function setperiodicidad($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, 'Periodicidad');
		$this->periodicidad = $value;
	}

	public function setTipoServicioId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->tipoServicioId = $value;
	}

	public function getTipoServicioId()
	{
		return $this->tipoServicioId;
	}
	
	public function setDepartamentoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->departamentoId = $value;
	}

	public function setNombreServicio($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, '<br> Nombre de servicio');
		$this->nombreServicio = $value;
	}

	public function getNombreServicio()
	{
		return $this->nombreServicio;
	}

	public function setCosto($value)
	{
		$this->Util()->ValidateFloat($value, 6);
		$this->costo = $value;
	}

	public function getCosto()
	{
		return $this->costo;
	}

	private $costoUnico;
	public function setCostoUnico($value)
	{
		$this->Util()->ValidateFloat($value, 6);
		$this->costoUnico = $value;
	}
	
	public function setCostoVisual($value){
		$this->Util()->ValidateFloat($value, 6);
		$this->costoVisual = $value;
	}
	
	public function setMostrarCostoVisual($value){
		$this->mostrarCostoVisual = $value;
	}

	public function ListDepartamentos()
	{
						
		$sql = "SELECT 
					* 
				FROM 
					departamentos
				ORDER BY 
					departamento ASC";
		
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
		
		foreach($result as $key => $res)
		{
			$result[$key]["departamento"] = $result[$key]["departamento"];
		}
				
		return $result;
	}
	
	public function Enumerate()
	{
		global $User;
		
		//filtro departamento
		if($User['departamentoId']!="1" && $User["roleId"]!=1)
		$filtroDep="WHERE departamentoId=".$User['departamentoId'];
		
		$this->Util()->DB()->setQuery('SELECT COUNT(*) FROM tipoServicio');
		$total = $this->Util()->DB()->GetSingle();

		$pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/tipoServicio");

		$sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
		$this->Util()->DB()->setQuery('SELECT * FROM tipoServicio '.$filtroDep.' ORDER BY nombreServicio ASC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $row)
		{
			$this->Util()->DB()->setQuery("SELECT COUNT(*) FROM step WHERE servicioId = '".$row["tipoServicioId"]."'");
			$result[$key]["totalPasos"] = $this->Util()->DB()->GetSingle();
		}

		$data["items"] = $result;
		$data["pages"] = $pages;
		return $data;
	}
    public function EnumerateOnePage(){
        global $User;

        //filtro departamento
        if($User['departamentoId']!="1" && $User["roleId"]!=1)
            $filtroDep="WHERE departamentoId=".$User['departamentoId'];

        $this->Util()->DB()->setQuery('SELECT * FROM tipoServicio '.$filtroDep.' ORDER BY nombreServicio ASC ');
        $result = $this->Util()->DB()->GetResult();

        foreach($result as $key => $row)
        {
            $this->Util()->DB()->setQuery("SELECT COUNT(*) FROM step WHERE servicioId = '".$row["tipoServicioId"]."'");
            $result[$key]["totalPasos"] = $this->Util()->DB()->GetSingle();
        }

        $data["items"] = $result;

        return $data;
    }
	public function EnumerateAll()
	{
		$this->Util()->DB()->setQuery('SELECT * FROM tipoServicio ORDER BY tipoServicioId ASC');
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM tipoServicio WHERE tipoServicioId = '".$this->tipoServicioId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}
	
	function Suggest($value)
	{      
		$this->Util()->DB()->setQuery("SELECT * 
		FROM tipoServicio
		WHERE tipoServicioId LIKE '%".$value."%'  ORDER BY tipoServicioId");
		$result = $this->Util()->DB()->GetResult();
		return $result;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				tipoServicio
			SET
				`tipoServicioId` = '".$this->tipoServicioId."',
				`nombreServicio` = '".$this->nombreServicio."',
				`periodicidad` = '".$this->periodicidad."',
				`departamentoId` = '".$this->departamentoId."',
				`costoUnico` = '".$this->costoUnico."',
				`costo` = '".$this->costo."',
				`costoVisual` = '".$this->costoVisual."',
				`mostrarCostoVisual` = '".$this->mostrarCostoVisual."'
			WHERE tipoServicioId = '".$this->tipoServicioId."'");
		$this->Util()->DB()->UpdateData();
		
		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		
		return true;
	}

	public function Save()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			INSERT INTO
				tipoServicio
			(
				`tipoServicioId`,
				`nombreServicio`,
				`periodicidad`,
				`departamentoId`,
				`costoUnico`,
				`costo`,
				costoVisual,
				mostrarCostoVisual
		)
		VALUES
		(
				'".$this->tipoServicioId."',
				'".$this->nombreServicio."',
				'".$this->periodicidad."',
				'".$this->departamentoId."',
				'".$this->costoUnico."',
				'".$this->costo."',
				'".$this->costoVisual."',
				'".$this->mostrarCostoVisual."'
		);");
		
		$this->Util()->DB()->InsertData();
		$this->Util()->setError(2, "complete");
		$this->Util()->PrintErrors();
		
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				tipoServicio
			WHERE
				tipoServicioId = '".$this->tipoServicioId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}
	
	public function GetField($field)
	{
		$sql = 'SELECT '.$field.' FROM tipoServicio 
				WHERE tipoServicioId = "'.$this->tipoServicioId.'"';
		$this->Util()->DB()->setQuery($sql);
		$value = $this->Util()->DB()->GetSingle();
		
		return $value;
	}

}

?>