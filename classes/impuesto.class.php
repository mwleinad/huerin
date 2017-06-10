<?php 

class Impuesto extends Main
{
	private $contractImpuestoId;
	private $contractId;
	private $impuestoId;
	private $impuestoNombre;

	public function setContractImpuestoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->contractImpuestoId = $value;
	}

	public function getContractImpuestoId()
	{
		return $this->contractImpuestoId;
	}

	public function setContractId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->contractId = $value;
	}

	public function getContractId()
	{
		return $this->contractId;
	}

	public function setImpuestoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->impuestoId = $value;
	}

	public function getImpuestoId()
	{
		return $this->impuestoId;
	}

	public function setImpuestoNombre($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, '<br> Nombre del impuesto');
		$this->impuestoNombre = $value;
	}

	public function getImpuestoNombre()
	{
		return $this->impuestoNombre;
	}

	public function DeleteContract()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				contractImpuesto
			WHERE
				contractImpuestoId = '".$this->contractImpuestoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(1, "complete", "Has borrado este impuesto");
		$this->Util()->PrintErrors();
		return true;
	}


	public function InfoContract()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM contractImpuesto 
		LEFT JOIN impuesto ON impuesto.impuestoId = contractImpuesto.impuestoId WHERE contractImpuestoId = '".$this->contractImpuestoId."'");
		$result = $this->Util()->DB()->GetRow();

		return $result;
	}

	public function EnumerateContract()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM contractImpuesto 
		LEFT JOIN impuesto ON impuesto.impuestoId = contractImpuesto.impuestoId WHERE contractId = '".$this->contractId."' ORDER BY impuestoNombre ASC ".$sql_add);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $row)
		{
		}
		return $result;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery('SELECT * FROM impuesto ORDER BY impuestoId ASC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $row)
		{
		}
		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM impuesto WHERE impuestoId = '".$this->impuestoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				impuesto
			SET
				`impuestoId` = '".$this->impuestoId."',
				`impuestoNombre` = '".$this->impuestoNombre."'
			WHERE impuestoId = '".$this->impuestoId."'");
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
				impuesto
			(
				`impuestoId`,
				`impuestoNombre`
		)
		VALUES
		(
				'".$this->impuestoId."',
				'".$this->impuestoNombre."'
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
				impuesto
			WHERE
				impuestoId = '".$this->impuestoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}
	
	public function SaveToContract()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			INSERT INTO
				contractImpuesto
			(
				`impuestoId`,
				`contractId`
		)
		VALUES
		(
				'".$this->impuestoId."',
				'".$this->contractId."'
		);");
		$this->Util()->DB()->InsertData();
		$this->Util()->setError(1, "complete", "Has insertado un impuesto a este contrato");
		$this->Util()->PrintErrors();
		return true;
	}
	

}

?>