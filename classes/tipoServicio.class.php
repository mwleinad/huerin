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
	private $claveSat;

	// config text to report
    private $activityServiceId;
    private $largeDescription;
    private $shortDescription;
    private $expectation;
    private $requestInformation;
    private $workSchedule;
    private $reports;

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
    public function setClaveSat($value)
    {
        $this->Util()->ValidateRequireField($value, 'Clave SAT');
        $this->Util()->ValidateString($value, 8, 8, 'Clave SAT');
        $this->Util()->ValidateOnlyNumeric($value,"Clave SAT");
        $this->claveSat = $value;
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

    public function setActivityServiceId($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->activityServiceId = $value;
    }
	public function setLargeDescription($value) {
	    $this->Util()->ValidateRequireField($value,'Descripcion detallada de actividades');
	    $this->largeDescription = $value;
    }

    public function setShortDescription($value) {
	    $this->Util()->ValidateRequireField($value, 'Descripcion corta de actividades');
	    $this->shortDescription = $value;
    }

    public function setExpectation($value) {
	    $this->expectation = $value;
    }
    public function setRequestInformation($value) {
        $this->requestInformation = $value;
    }
    public function setWorkSchedule($value) {
        $this->Util()->ValidateRequireField($value, 'Programacion de trabajo');
        $this->workSchedule = $value;
    }
    public function setReports($value) {
	    $this->reports = $value;
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
            $filtroDep=" AND departamentoId=".$User['departamentoId'];

        $this->Util()->DB()->setQuery('SELECT * FROM tipoServicio WHERE status="1" '.$filtroDep.' ORDER BY nombreServicio ASC ');
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
		$this->Util()->DB()->setQuery('SELECT * FROM tipoServicio WHERE status="1" ORDER BY tipoServicioId ASC');
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
				`claveSat` = '".$this->claveSat."',
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
				`claveSat`,
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
				'".$this->claveSat."',
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
			UPDATE tipoServicio SET
				status='0'
			WHERE
				tipoServicioId = '".$this->tipoServicioId."'");
		$this->Util()->DB()->UpdateData();
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

	public function SaveTextReport() {

	    if($this->Util()->PrintErrors())
	        return false;

	    $this->Util()->DB()->DatabaseConnect();
        $activityId =  $this->activityServiceId ? $this->activityServiceId : null;
	    $query = "replace into activity_service 
                  (
                    id,
                    service_id,
                    large_description,
                    short_description,
                    expectation,
                    request_information,
                    work_schedule,
                    reports
              ) values
              (
                '$activityId',
                ".$this->tipoServicioId.",
                '".($this->largeDescription)."',
                '".mysql_real_escape_string($this->shortDescription)."',
                '".mysql_real_escape_string($this->expectation)."',
                '".mysql_real_escape_string($this->requestInformation)."',
                '".mysql_real_escape_string($this->workSchedule)."',
                '".mysql_real_escape_string($this->reports)."'
              )";
	    $this->Util()->DB()->setQuery($query);
	    $last = $this->Util()->DB()->InsertData();
	    if(!$last) {
            $this->Util()->setError(0,'error', 'Error al guardar');
            $this->Util()->PrintErrors();
            return false;
        }

	    $this->Util()->setError(0,'complete', 'Informacion guardada');
	    $this->Util()->PrintErrors();
	    return true;
    }

	public function GetTextReportByServicio() {
	    $query = "select * from activity_service where service_id = '".$this->tipoServicioId."' ";
	    $this->Util()->DB()->setQuery($query);
	    return $this->Util()->DB()->GetRow();
    }

}

?>