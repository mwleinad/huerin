<?php 

class Obligacion extends Main
{
	private $contractObligacionId;
	private $contractId;
	private $obligacionId;
	private $obligacionNombre;

	public function setContractObligacionId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->contractObligacionId = $value;
	}

	public function getContractObligacionId()
	{
		return $this->contractObligacionId;
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

	public function setObligacionId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->obligacionId = $value;
	}

	public function getObligacionId()
	{
		return $this->obligacionId;
	}

	public function setObligacionNombre($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, '<br> Nombre de la Obligacion');
		$this->obligacionNombre = $value;
	}

	public function getObligacionNombre()
	{
		return $this->obligacionNombre;
	}

	public function DeleteContract()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				contractObligacion
			WHERE
				contractObligacionId = '".$this->contractObligacionId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(1, "complete", "Has borrado esta obligacion");
		$this->Util()->PrintErrors();
		return true;
	}


	public function InfoContract()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM contractObligacion 
		LEFT JOIN obligacion ON obligacion.obligacionId = contractObligacion.obligacionId WHERE contractObligacionId = '".$this->contractObligacionId."'");
		$result = $this->Util()->DB()->GetRow();

		return $result;
	}

	public function EnumerateContract()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM contractObligacion
		LEFT JOIN obligacion ON obligacion.obligacionId = contractObligacion.obligacionId WHERE contractId = '".$this->contractId."' ORDER BY obligacionNombre ASC ".$sql_add);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $row)
		{
		}
		return $result;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery('SELECT * FROM obligacion ORDER BY obligacionId ASC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $row)
		{
		}
		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM obligacion WHERE obligacionId = '".$this->obligacionId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				obligacion
			SET
				`obligacionId` = '".$this->obligacionId."',
				`obligacionNombre` = '".$this->obligacionNombre."'
			WHERE obligacionId = '".$this->obligacionId."'");
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
				obligacion
			(
				`obligacionId`,
				`obligacionNombre`
		)
		VALUES
		(
				'".$this->obligacionId."',
				'".$this->obligacionNombre."'
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
				obligacion
			WHERE
				obligacionId = '".$this->obligacionId."'");
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
				contractObligacion
			(
				`obligacionId`,
				`contractId`
		)
		VALUES
		(
				'".$this->obligacionId."',
				'".$this->contractId."'
		);");
		$this->Util()->DB()->InsertData();
		$this->Util()->setError(1, "complete", "Has insertado una obligacion a este contrato");
		$this->Util()->PrintErrors();
		return true;
	}

}

?>