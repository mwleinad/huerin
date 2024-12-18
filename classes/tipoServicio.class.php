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
		$this->Util()->ValidateRequireField($value,  'Periodicidad');
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
		$this->Util()->ValidateRequireField($value, 'Departamento');
		$this->departamentoId = $value;
	}

	public function setNombreServicio($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, 'Nombre de servicio');
		$this->nombreServicio = $value;
	}
    public function setClaveSat($value)
    {
        $this->Util()->ValidateRequireField($value, 'Clave SAT');
        $this->Util()->ValidateString($value, 8, 8, 'Clave SAT');
        $this->Util()->ValidateOnlyNumeric($value,"Clave SAT");
        $this->claveSat = $value;
    }

    private function validateFileIfExist() {
		if(isset($_FILES['template']) && $_FILES['template']['error'] === 0) {
			$extension = explode(".", $_FILES['template']['name']);
			$ext = end($extension);
			if($ext != "docx")
				$this->Util()->setError(0, 'error', 'El archivo adjunto no es valido.');
		}
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

	private $uniqueInvoice;
	public function setUniqueInvoice($value){
		$this->uniqueInvoice = $value;
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

    private  $isPrimary;
    public  function setIsPrimary($value) {
		$this->isPrimary =  $value;
	}

	public function getIsPrimary () {
		return $this->isPrimary;
	}

	private  $conceptoMesVencido;
	public  function setConceptoMesVencido($value) {
		$this->conceptoMesVencido =  $value;
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
	private function GetServicesGroupByDepartament ($type = null) {
		$ftr = $type !== null ? " and tipoServicio.is_primary ='".$type."'" : "";
		$sql = "select
       			departamentos.departamentoId,
				departamentos.departamento,
        		CONCAT(
					'[',
					GROUP_CONCAT(
						CONCAT(
							'{\"value',
							'\":\"',
							tipoServicio.tipoServicioId,
							'\",\"',
							'name',
							'\":\"',
							 tipoServicio.nombreServicio,
							'\",\"',
							'checked',
							'\":\"',
							 '',
							'\"}'
						)
					),
					']'
				)  as servicios
				from tipoServicio 	
				inner join departamentos on departamentos.departamentoId = tipoServicio.departamentoId
				where tipoServicio.status = '1' AND departamentos.estatus = 1 ".$ftr."
				group by tipoServicio.departamentoId order by departamentos.departamento asc, tipoServicio.nombreServicio asc
				";
		$this->Util()->DB()->setQuery($sql);
		return $this->Util()->DB()->GetResult();
	}
	public function EnumerateServiceGroupByDepForSelect2($type = null) {
		$result = $this->GetServicesGroupByDepartament($type);
		$newServicesGroup = [];
		foreach ($result as $var) {
			$cad = [];
			$services  = $var['servicios'] ? json_decode($var['servicios'], true) : [];
			$servs = [];
			foreach($services as $service) {
				$serv['id'] =  $service['value'];
				$serv['text'] = $service['name'];
				$servs [] =  $serv;
			}
			$cad['text'] =  $var['departamento'];
			$cad['children'] = $servs;
			array_push($newServicesGroup, $cad);
		}
		return $newServicesGroup;
	}
    public function EnumerateOnePage(){
        global $User;

        //filtro departamento
        if($User['departamentoId']!="1" && $User["roleId"]!=1 && !$this->accessAnyDepartament())
            $filtroDep=" AND a.departamentoId=".$User['departamentoId'];

        $this->Util()->DB()->setQuery("SELECT a.*, b.departamento  FROM tipoServicio a 
											  INNER JOIN departamentos b ON a.departamentoId = b.departamentoId
											  WHERE a.status='1' and b.estatus = 1 ".$filtroDep." ORDER BY a.nombreServicio ASC ");
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
		$this->Util()->DB()->setQuery('SELECT * FROM tipoServicio WHERE status="1" ORDER BY nombreServicio ASC');
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}
	public function getSteps() {
		$this->Util()->DB()->setQuery("SELECT step.* FROM step 
            INNER JOIN tipoServicio ON step.servicioId = tipoServicio.tipoServicioId
            WHERE step.servicioId = '".$this->getTipoServicioId()."'					
            ORDER BY step.position ASC");
		$result = $this->Util()->DB()->GetResult();
		foreach($result as $key => $value)
		{
			//get tasks
			$this->Util()->DB()->setQuery("SELECT * FROM task
            WHERE stepId = '".$value["stepId"]."'					
            ORDER BY taskPosition ASC");
			$result[$key]["tasks"] = $this->Util()->DB()->GetResult();
			$result[$key]["countTasks"] = count($result[$key]["tasks"]);

		}
		return $result;
	}

	public function getSecondaryService ($id) {
		$query = "select * from secondary_service where service_id = ? ";
		$params = [];
		array_push($params, ['type' =>'i', 'value' => $id]);
		$this->Util()->DBProspect()->PrepareStmtQuery($query, $params);
		return  $this->Util()->DBProspect()->GetStmtResult();;
	}
	public function Info($whitTask = false)
	{
		$this->Util()->DB()->setQuery("SELECT * FROM tipoServicio WHERE tipoServicioId = '".$this->tipoServicioId."'");
		$row = $this->Util()->DB()->GetRow();
		if($row && $whitTask) {
			$this->Util()->DB()->setQuery("SELECT * FROM step
            WHERE servicioId = '".$this->tipoServicioId."' ");
			$tasks = $this->Util()->DB()->GetResult();
			$row["tasks"] = is_array($tasks) ? $tasks : [];
		}
		if($row) {
			$row['current_secondary'] =  $this->getSecondaryService($this->tipoServicioId);;
		}
		return $row;
	}

	function Suggest($value)
	{
		$this->Util()->DB()->setQuery("SELECT * 
		FROM tipoServicio
		WHERE (tipoServicioId LIKE '%".$value."%' OR nombreServicio like '%".$value."%')  ORDER BY nombreServicio");
		$result = $this->Util()->DB()->GetResult();
		return $result;
	}

	public function Edit()
	{
		$this->validateFileIfExist();
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
				`uniqueInvoice` = '".$this->uniqueInvoice."',
				`mostrarCostoVisual` = '".$this->mostrarCostoVisual."',
				`is_primary` = '".$this->isPrimary."',
				`concepto_mes_vencido` = '".$this->conceptoMesVencido."'
			WHERE tipoServicioId = '".$this->tipoServicioId."'");
		$this->Util()->DB()->UpdateData();
		if(isset($_POST['steps'])) {
			$this->saveSteps($this->tipoServicioId);
		}
		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();

		//save to prospect db
		$sql = "UPDATE
				service
			SET
				`name` = '".$this->nombreServicio."',
				`departament_id` = '".$this->departamentoId."',
				`is_primary` = '".$this->isPrimary."',
				`updated_at` = now()
			WHERE id = '".$this->tipoServicioId."'";
		$this->Util()->DBProspect()->setQuery($sql);
		$this->Util()->DBProspect()->UpdateData();
		//mover archivo
		$this->moveTemplate($this->tipoServicioId);
		$secondarys = $_POST['secondary_services'] ? explode(',', $_POST['secondary_services']) : [];
		$this->assoccSecondary($this->tipoServicioId, $secondarys);
		return true;
	}

	public function Save()
	{
		$this->validateFileIfExist();
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
			 	uniqueInvoice,
				mostrarCostoVisual,
			 	is_primary,
			 	concepto_mes_vencido
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
				'".$this->uniqueInvoice."',
				'".$this->mostrarCostoVisual."',
				'".$this->isPrimary."',
				'".$this->conceptoMesVencido."'
		);");
		$id = $this->Util()->DB()->InsertData();
		if(isset($_POST['steps'])) {
			$this->saveSteps($id);
		}
		//save to prospect db
		$sql = "insert into service(
                    name,
                    departament_id,
                    is_primary,
                    created_at,
                    updated_at
                    ) values(
                       '".$this->nombreServicio."',
                       '".$this->departamentoId."',
                       '".$this->isPrimary."',
                       now(),
                       now()
                    )";
		$this->Util()->DBProspect()->setQuery($sql);
		$id = $this->Util()->DBProspect()->InsertData();

		//mover archivo
		if($id) {
			$this->moveTemplate($id);
			$secondarys = $_POST['secondary_services'] ? explode(',', $_POST['secondary_services']) : [];
			$this->assoccSecondary($id, $secondarys);
		}

		$this->Util()->setError(2, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	function assoccSecondary ($id, $secondary) {
		$secondary_service = is_array($secondary) ? $secondary: [];
		$this->Util()->DBProspect()->setQuery("delete from secondary_service where service_id = ". $id);
		$this->Util()->DBProspect()->UpdateData();

		$this->Util()->DB()->setQuery("SELECT tipoServicioId FROM tipoServicio WHERE is_primary = 0");
		$secundarios = $this->Util()->DB()->GetResult();
		$secundarios = !is_array($secundarios) ? [] : array_column($secundarios, 'tipoServicioId');

		$sql = "insert into secondary_service(service_id, secondary_id) VALUES";
		$strComp ="";
		foreach ($secondary_service as $service) {
				if(in_array($service, $secundarios))
					$strComp .= "($id, $service),";
		}
		if($strComp!=="") {
			$strComp = substr($strComp,0,strlen($strComp)-1);
			$sql = $sql.$strComp;
			$this->Util()->DBProspect()->setQuery($sql);
			$this->Util()->DBProspect()->InsertData();
		}
	}
	function saveSteps($id) {
		$steps = is_array($_POST['steps']) ? $_POST['steps'] : [];
		$countStep = 1;
		foreach ($steps as $key => $step) {
			if($this->Util()->isJson($step)) {
				$stepObj = json_decode($step);
				$sql = " insert into step(servicioId, nombreStep, descripcion, position) values('".$id."', '".$stepObj->nombreStep."', '".$stepObj->descripcion."', '". ($stepObj->position ?? $countStep)."')";
				$this->Util()->DB()->setQuery($sql);
				$idStep = $this->Util()->DB()->InsertData();
				$countStep++;
				$stpId =  $stepObj->stepId;
				$tasks = is_array($_POST["tasks$stpId"]) ? $_POST["tasks$stpId"] : [];
				$query = "insert into task(stepId, nombreTask, diaVencimiento, prorroga, control, extensiones, taskPosition) VALUES";
				$strComp ="";

				$countTask = 1;
				foreach ($tasks as $keyTask => $task) {
					if($this->Util()->isJson($task) && $idStep) {
						$taskObj = json_decode($task);
						$strComp .= "($idStep, 
											'".$taskObj->nombreTask."', 
											'".$taskObj->diaVencimiento."', 
											'".$taskObj->prorroga."', 
											'".$taskObj->control."', 
											'".$taskObj->extensiones."',
											'".($taskObj->taskPosition ?? $countTask)."'
										),";

						$countTask++;
					}

				}
				if($strComp!=="") {
					$strComp = substr($strComp,0,strlen($strComp)-1);
					$sql = $query.$strComp;
					$this->Util()->DB()->setQuery($sql);
					$this->Util()->DB()->InsertData();
				}

			}
		}
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

		$sql = "update service set deleted_at = now() where id = '".$this->tipoServicioId."'";
		$this->Util()->DBProspect()->setQuery($sql);
		$this->Util()->DBProspect()->UpdateData();

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
    private function moveTemplate($id) {
		if(isset($_FILES['template']) && $_FILES['template']['error'] === 0) {
			$rootStorage = PUBLIC_STORAGE_PROSPECT . "/service";
			$file = "template_".$id.".docx";
			$move = move_uploaded_file($_FILES['template']['tmp_name'], $rootStorage."/".$file);
		}
	}

}

?>
