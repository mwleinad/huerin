<?php 

class TipoRequerimiento extends Main
{
	private $tipoRequerimientoId;
	private $nombre;

	public function setTipoRequerimientoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->tipoRequerimientoId = $value;
	}

	public function getTipoRequerimientoId()
	{
		return $this->tipoRequerimientoId;
	}

	public function setNombre($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, '<br> Nombre del requerimiento');
		$this->nombre = $value;
	}

	public function getNombre()
	{
		return $this->nombre;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery('SELECT * FROM tipoRequerimiento ORDER BY tipoRequerimientoId ASC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM tipoRequerimiento WHERE tipoRequerimientoId = '".$this->tipoRequerimientoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				tipoRequerimiento
			SET
				`tipoRequerimientoId` = '".$this->tipoRequerimientoId."',
				`nombre` = '".$this->nombre."'
			WHERE tipoRequerimientoId = '".$this->tipoRequerimientoId."'");
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
				tipoRequerimiento
			(
				`tipoRequerimientoId`,
				`nombre`
		)
		VALUES
		(
				'".$this->tipoRequerimientoId."',
				'".$this->nombre."'
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
				tipoRequerimiento
			WHERE
				tipoRequerimientoId = '".$this->tipoRequerimientoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

}

?>