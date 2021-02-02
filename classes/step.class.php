<?php

class Step extends Servicio
{
    private $stepId;

    public function setStepId($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->stepId = $value;
    }

    public function getStepId()
    {
        return $this->stepId;
    }

    private $nombreStep;

    public function setNombreStep($value)
    {
        $this->Util()->ValidateString($value, 255, 1, 'Nombre');
        $this->nombreStep = $value;
    }

    public function getNombreStep()
    {
        return $this->nombreStep;
    }

    private $descripcion;

    public function setDescripcion($value)
    {
        $this->Util()->ValidateString($value, 255, 1, 'Descripcion');
        $this->descripcion = $value;
    }

    private $effectiveDate;

    public function setEffectiveDate($value)
    {
        $this->Util()->ValidateRequireField($value, 'Inicio de vigencia');
        $this->Util()->validateDateFormat($value, "Fecha inicio de vigencia", "d-m-Y");
        $this->effectiveDate = (strlen($value)) ? $this->effectiveDate = $this->Util()->FormatDateMySql($value) : "";
    }

    private $finalEffectiveDate;

    public function setFinalEffectiveDate($value)
    {
        if(strlen($value)) {
            $this->finalEffectiveDate = $this->Util()->validateDateFormat($value, 'Fecha fin de vigencia', "d-m-Y")
                ? $this->Util()->FormatDateMySql($value)
                : "";
        }
    }

    public function Enumerate($id = 0, $status = '')
    {
        $this->Util()->DB()->setQuery("SELECT * FROM step 
			LEFT JOIN servicio ON step.servicioId = servicio.servicioId
				WHERE step.servicioId = '" . $this->getServicioId() . "'					
				ORDER BY step.stepId ASC");
        $result = $this->Util()->DB()->GetResult();
        foreach ($result as $key => $value) {
            //get tasks
            $this->Util()->DB()->setQuery("SELECT * FROM task
				WHERE stepId = '" . $value["stepId"] . "'					
				ORDER BY taskId ASC");
            $result[$key]["tasks"] = $this->Util()->DB()->GetResult();
            $result[$key]["countTasks"] = count($result[$key]["tasks"]);

        }

        return $result;
    }

    public function Info()
    {
        $this->Util()->DB()->setQuery("SELECT * FROM step WHERE stepId = '" . $this->stepId . "'");
        $row = $this->Util()->DB()->GetRow();

        return $row;
    }

    public function Edit()
    {
        if ($this->Util()->PrintErrors()) {
            return false;
        }
        $dateEffective = strlen($this->effectiveDate) ? "'" . $this->effectiveDate . "'" : 'NULL';
        $dateFinalEffective = strlen($this->finalEffectiveDate) ? "'" . $this->finalEffectiveDate . "'" : 'NULL';

        $this->Util()->DB()->setQuery("
			UPDATE
				step
			SET
				`nombreStep` = '" . $this->nombreStep . "',
				`descripcion` = '" . $this->descripcion . "',
				`effectiveDate` = $dateEffective,
				`finalEffectiveDate` = $dateFinalEffective
			WHERE stepId = '" . $this->stepId . "'");
        $this->Util()->DB()->UpdateData();

        $this->Util()->setError(1, "complete");
        $this->Util()->PrintErrors();
        return true;
    }

    public function Save()
    {
        if ($this->Util()->PrintErrors()) {
            return false;
        }

        $dateEffective = strlen($this->effectiveDate) ? "'" . $this->effectiveDate . "'" : 'NULL';
        $dateFinalEffective = strlen($this->finalEffectiveDate) ? "'" . $this->finalEffectiveDate . "'" : 'NULL';
        $this->Util()->DB()->setQuery("
			INSERT INTO
				step (
				`servicioId`,
				`nombreStep`,
				`descripcion`,
				`effectiveDate`,
				`finalEffectiveDate`
		      )
                VALUES
                (
                        '" . $this->getServicioId() . "',
                        '" . $this->nombreStep . "',
                        '" . $this->descripcion . "',
                        $dateEffective,
                        $dateFinalEffective
                        
                );");
        $this->Util()->DB()->InsertData();
        $this->Util()->setError(2, "complete");
        $this->Util()->PrintErrors();
        return true;
    }

    public function Delete()
    {
        if ($this->Util()->PrintErrors()) {
            return false;
        }

        $info = $this->Info();

        $this->Util()->DB()->setQuery("
			DELETE FROM 
				step
			WHERE
				stepId = '" . $this->stepId . "'");
        $this->Util()->DB()->DeleteData();
        $this->Util()->setError(3, "complete", $complete);
        $this->Util()->PrintErrors();
        return true;
    }

}

?>
