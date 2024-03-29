<?php

class Task extends Step
{
	private $taskId;
	public function setTaskId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->taskId = $value;
	}
	public $workId;
    public function setWorkflowId($value)
    {
        //$this->Util()->ValidateInteger($value);
        $this->workId = $value;
    }

	private $diaVencimiento;
	public function setDiaVencimiento($value)
	{
		$this->Util()->ValidateInteger($value, 31, 1);
		$this->diaVencimiento = $value;
	}

	private $prorroga;
	public function setProrroga($value)
	{
		$this->Util()->ValidateInteger($value, 365, 0);
		$this->prorroga = $value;
	}

	private $nombreTask;
	public function setNombreTask($value)
	{
		$this->Util()->ValidateString($value, 255, 1, 'Nombre');
		$this->nombreTask = $value;
	}

	public function getNombreTask()
	{
		return $this->nombreTask;
	}

	private $control;
	public function setControl($value)
	{
		$this->Util()->ValidateString($value, 255, 1, 'Cliente Bueno');
		$this->control = $value;
	}

	private $control2;
	public function setControl2($value)
	{
		$this->Util()->ValidateString($value, 255, 1, 'Cliente Malo');
		$this->control2 = $value;
	}

	private $control3;
	public function setControl3($value)
	{
		$this->Util()->ValidateString($value, 255, 0, 'Control Malo');
		$this->control3 = $value;
	}

    private $taskOrder;
    public function setTaskOrder($value)
    {
        $this->Util()->ValidateString($value, 255, 0, 'Orden');
        $this->Util()->ValidateOnlyNumeric($value, 'Orden');
        $this->taskOrder = $value;
    }

    private $rutaZipCreated;
	public function  setRutaZipCreated($value){
	    $this->rutaZipCreated = $value;
    }
    public function  getRutaZipCreated(){
        return $this->rutaZipCreated;
    }
    private $extensiones=[];
    public function setExtensiones($value)
    {
        if(!is_array($value)||empty($value))
            $this->Util()->setError(0,'error',"Es necesario seleccionar por lo menos un elemento  de la lista",'Extensiones de archivos');
        else
            $this->extensiones = $value;

    }
	public function Enumerate($id = 0, $status = '')
	{
		global $months;

		$this->Util()->DB()->setQuery("SELECT * FROM step 
			LEFT JOIN servicio ON step.servicioId = servicio.servicioId
				WHERE step.servicioId = '".$this->getServicioId()."'					
				ORDER BY step.stepId ASC");
		$result = $this->Util()->DB()->GetResult();
		foreach($result as $key => $value)
		{
			//get tasks
			$this->Util()->DB()->setQuery("SELECT * FROM task
				WHERE stepId = '".$value["stepId"]."'					
				ORDER BY taskId ASC");
			$result[$key]["tasks"] = $this->Util()->DB()->GetResult();
			$result[$key]["countTasks"] = count($result[$key]["tasks"]);

		}

		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM task WHERE taskId = '".$this->taskId."'");
		$row = $this->Util()->DB()->GetRow();

		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

        $dateEffective = strlen($this->getEffectiveDate()) ? "'" . $this->getEffectiveDate() . "'" : 'NULL';
        $dateFinalEffective = strlen($this->getFinalEffectiveDate()) ? "'" . $this->getFinalEffectiveDate() . "'" : 'NULL';

		$this->Util()->DB()->setQuery("
			UPDATE
				task
			SET
				`nombreTask` = '".$this->nombreTask."',
				`diaVencimiento` = '".$this->diaVencimiento."',
				`prorroga` = '".$this->prorroga."',
				`control` = '".$this->control."',
				`control2` = '".$this->control2."',
				`control3` = '".$this->control3."',
				`taskPosition` = '".$this->taskOrder."',
				`effectiveDate` = $dateEffective,
				`finalEffectiveDate` = $dateFinalEffective,
				`extensiones` = '".implode(',',$this->extensiones)."'
			WHERE taskId = '".$this->taskId."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		if($this->Util()->PrintErrors()){ return false; }

        $dateEffective = strlen($this->getEffectiveDate()) ? "'" . $this->getEffectiveDate() . "'" : 'NULL';
        $dateFinalEffective = strlen($this->getFinalEffectiveDate())? "'" . $this->getFinalEffectiveDate() . "'" : 'NULL';

		$this->Util()->DB()->setQuery("
			INSERT INTO
				task
			(
				`stepId`,
				`nombreTask`,
				`diaVencimiento`,
				`prorroga`,
				`control`,
				`control2`,
				`control3`,
			 	`taskPosition`,
			    `effectiveDate`,
				`finalEffectiveDate`,
				`extensiones`
		)
		VALUES
		(
				'".$this->getStepId()."',
				'".$this->nombreTask."',
				'".$this->diaVencimiento."',
				'".$this->prorroga."',
				'".$this->control."',
				'".$this->control2."',
				'".$this->control3."',
				'".$this->taskOrder."',
				 $dateEffective,
                 $dateFinalEffective,
				'".implode(',',$this->extensiones)."'
				
		);");
		$this->Util()->DB()->InsertData();
		$this->Util()->setError(2, "complete");
		$this->Util()->PrintErrors();
		return true;
	}
	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }
		$info = $this->Info();
		$this->Util()->DB()->setQuery("
			DELETE FROM 
				task
			WHERE
				taskId = '".$this->taskId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete", $complete);
		$this->Util()->PrintErrors();
		return true;
	}
	public function checkTasksByStep()
    {
        global $workflow;
        $sql = "SELECT b.nombreServicio,a.nombreStep from step a 
                INNER JOIN tipoServicio b ON a.servicioId=b.tipoServicioId
                WHERE a.stepId='".$this->getStepId()."'";
        $this->Util()->DB()->setQuery($sql);
        $dataService = $this->Util()->DB()->GetRow();

        $workflow->setInstanciaServicioId($this->workId);
        $data['workflow'] = $workflow->infoWorkflow();
        $dateWorkflow =  explode('-',$data["workflow"]["date"]);

        $strFiltroStepTask ="";
        switch(strtoupper(trim($dataService["nombreServicio"]))){
            case 'IMSS E INFONAVIT':
            case 'IMSS E INFONAVIT ESTADO DE MEXICO':
                if(strtoupper(trim($dataService["nombreStep"]))=='MOVIMIENTOS IMSS')
                    if($dateWorkflow[0]<2019)
                            $strFiltroStepTask .=" and UPPER(nombreTask)!='MOVIMIENTOS IMSS' ";
            break;
            case 'ARRENDAMIENTO':
            case 'ASALARIADOS':
            case 'CERO':
            case 'CONTABILIDAD FINANCIERA':
            case 'CONTABILIDAD MENSUAL':
            case 'REVISION MENSUAL':
            case 'RIF':
            case 'SUELDOS Y SALARIOS':
                if(strtoupper(trim($dataService["nombreStep"]))=='DOCUMENTACION' ||strtoupper(trim($dataService["nombreStep"]))=='REQUERIMIENTO DE INFORMACION'||strtoupper(trim($dataService["nombreStep"]))=='RECEPCION DE DOCUMENTACION')
                    if($data['workflow']['date']<'2019-07-01')
                        $strFiltroStepTask .=" and UPPER(nombreTask)!='CUMPLIMIENTO OBLIGACIONES' and  UPPER(nombreTask)!='CUMPLIMIENTO DE OBLIGACIONES' ";

                if(strtoupper(trim($dataService["nombreStep"]))=='DOCUMENTACION')
                    if($data['workflow']['date']<'2020-01-01')
                        $strFiltroStepTask .=" and UPPER(nombreTask)!='RELACION CFDI' and  UPPER(nombreTask)!='RELACION DE CFDI' ";

            break;

        }
        //workflowId  es la instanciaServicioId viene desde url
        $this->Util()->DB()->setQuery("SELECT * FROM task WHERE stepId = '" . $this->getStepId() . "' $strFiltroStepTask order by taskPosition asc");
        $data['tasks'] = $this->Util()->DB()->GetResult();
        $totalTask = 0;
        $data['completedTasks'] = 0;
        $data['stepId']=$this->getStepId();
        $porcentajeTotal = 0;
        $porcentajeDone = 0;
        foreach ($data['tasks'] as $keyTask => $valueTask) {

            if($data['workflow']['date'] < $valueTask['effectiveDate']) {
                unset($data["tasks"][$keyTask]);
                continue;
            }

            if($valueTask['finalEffectiveDate'] !== null && $data['workflow']['date'] > $valueTask['finalEffectiveDate']) {
                unset($data["tasks"][$keyTask]);
                continue;
            }

            $totalTask++;
            $porcentajeTotal += 100;
            $data["tasks"][$keyTask]["controlFile"] = 0;
            if ($valueTask["control"]) {
                //Checar si ya se subio ese archivo
                $this->Util()->DB()->setQuery("SELECT  *  FROM taskFile 
                    WHERE servicioId = '" .$this->workId . "' AND stepId = '" . $valueTask["stepId"] . "' AND taskId = '" . $valueTask["taskId"] . "' AND control = 1 ORDER BY version DESC");
                $filesTask = $this->Util()->DB()->GetResult();

                if (count($filesTask) > 0) {
                    $data["tasks"][$keyTask]["controlFile"] = 1;
                }
                $data["tasks"][$keyTask]["controlFileInfo"] = $filesTask;

            } else {
                $data["tasks"][$keyTask]["controlFile"] = 1;
            }//else

            $data["tasks"][$keyTask]["controlFile2"] = 1;
            $data["tasks"][$keyTask]["controlFile3"] = 1;
            $data["tasks"][$keyTask]["taskCompleted"] = 0;
            if ($data["tasks"][$keyTask]["controlFile"] + $data["tasks"][$keyTask]["controlFile2"] + $data["tasks"][$keyTask]["controlFile3"] == 3) {

                $porcentajeDone += 100;
                $data["tasks"][$keyTask]["taskCompleted"] = 1;
                $data["completedTasks"]++;
            }//if

        }//foreach
        $data["totalTasks"] = $totalTask;
        if ($porcentajeTotal == 0)
            $porcentajeTotal = 1;

        $realPercent = $porcentajeDone / ($porcentajeTotal * 100);
        if ($realPercent == 0)
            $data["class"] = "PorIniciar";
        elseif ($realPercent > 0 && $realPercent < 70)
            $data["class"] = "Iniciado";
        elseif ($realPercent >= 70 && $realPercent < 100)
            $data["class"] = "PorCompletar";
        else
            $data["class"] = "Completo";

        $data["stepCompleted"] = 0;
        if ($data["completedTasks"] == $data["totalTasks"]) {
            $data["stepCompleted"] = 1;
        }//if



        //comprobar que el step anterior este completo de lo contrario no puede avanzar

        /*$this->Util()->DB()->setQuery("SELECT * FROM step
			WHERE servicioId = '".$_POST["tipoServicioId"]."' AND stepId < '".$value["stepId"]."' LIMIT 1");
        $row["steps"][$key]["prevStep"] = $this->Util()->DB()->GetRow();*/

        return $data;
    }
    public function getFilesByWorkflow(){
        if(is_array($this->workId)){
            $in = "0";
            if(count($this->workId)>0)
                $in .= ",".implode(",",$this->workId);

            $where = " taskFile.servicioId IN($in) ";
        }
        else
            $where =" taskFile.servicioId = ".$this->workId;

        $sql = "SELECT taskFile.*, tipoServicio.nombreServicio, task.nombreTask, contract.name, instanciaServicio.date FROM taskFile
                INNER JOIN task ON task.taskId = taskFile.taskId
                INNER JOIN instanciaServicio ON instanciaServicio.instanciaServicioId = taskFile.servicioId
                INNER JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
                INNER JOIN contract ON contract.contractId = servicio.contractId
                INNER JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
                WHERE $where";
        $this->Util()->DB()->setQuery($sql);
        $files = $this->Util()->DB()->GetResult();

        return $files;
    }
    function CreateZipTasks(){
        global $monthsComplete;
        $files = $this->getFilesByWorkflow();
        if(!$files){
            $this->Util()->setError(0,"error","No existen archivos dentro del workflow.");
            $this->Util()->PrintErrors();
            return false;
        }
        $fecha = $files[0]["date"];
        $dateExploded =  explode("-",$fecha);
        $anio = $dateExploded[0];
        $month = strtolower($monthsComplete[$dateExploded[1]]);
        $nombreServicio = $files[0]["nombreServicio"];
        $nombreServicio = str_replace(" ", "_", $nombreServicio);
        $nombreServicio = str_replace(".", "_", $nombreServicio);
        $nombreServicio = str_replace(",", "_", $nombreServicio);
        $nombreServicio = str_replace("/", "", $nombreServicio);
        $nombreCliente = $files[0]["name"];
        $nombreCliente = str_replace(" ", "_", $nombreCliente);
        $nombreCliente = str_replace(".", "_", $nombreCliente);
        $nombreCliente = str_replace(",", "_", $nombreCliente);
        $nombreCliente = str_replace("/", "", $nombreCliente);
        $nombreCliente = str_replace("&amp;", "&", $nombreCliente);
        $name = $nombreCliente."_".$nombreServicio."_".$anio."_".$month;
        $name = strtolower($name);
        $zip = DOC_ROOT."/archivos/".$name.".zip";
        if(!$this->Util()->ZipTasks($zip,$files))
        {
            $this->Util()->setError(0,"error","Ocurrio un error al generar archivo.");
            $this->Util()->PrintErrors();
            return false;
        }
        $this->setRutaZipCreated($zip);
        return true;
    }
    function CreateZipTasksAnual(){
        $sql = "select instanciaServicioId from instanciaServicio where servicioId='".$this->getServicioId()."' and year(date)=".$_POST['year']." ";
        $this->Util()->DB()->setQuery($sql);
        $instancias = $this->Util()->DB()->GetResult();
        $workflows = $this->Util()->ConvertToLineal($instancias,'instanciaServicioId');
        $this->setWorkflowId($workflows);
        $files = $this->getFilesByWorkflow();
        if(!$files){
            $this->Util()->setError(0,"error","No existen archivos para descargar.");
            $this->Util()->PrintErrors();
            return false;
        }
        $anio = $_POST["year"];
        $nombreServicio = $files[0]["nombreServicio"];
        $nombreServicio = str_replace(" ", "_", $nombreServicio);
        $nombreServicio = str_replace(".", "_", $nombreServicio);
        $nombreServicio = str_replace(",", "_", $nombreServicio);
        $nombreServicio = str_replace("/", "", $nombreServicio);
        $nombreCliente = $files[0]["name"];
        $nombreCliente = str_replace(" ", "_", $nombreCliente);
        $nombreCliente = str_replace(".", "_", $nombreCliente);
        $nombreCliente = str_replace(",", "_", $nombreCliente);
        $nombreCliente = str_replace("/", "", $nombreCliente);
        $nombreCliente = str_replace("&amp;", "&", $nombreCliente);
        $name = $nombreCliente."_".$nombreServicio."_".$anio;
        $name = strtolower($name);
        $zip = DOC_ROOT."/archivos/".$name.".zip";
        if(!$this->Util()->ZipTasks($zip,$files))
        {
            $this->Util()->setError(0,"error","Ocurrio un error al generar archivo.");
            $this->Util()->PrintErrors();
            return false;
        }
        $this->setRutaZipCreated($zip);
        return true;
    }

}

?>
