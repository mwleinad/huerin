<?php 

class TipoArchivo extends Main
{
	private $tipoArchivoId;
	private $descripcion;

	public function setTipoArchivoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->tipoArchivoId = $value;
	}

	public function getTipoArchivoId()
	{
		return $this->tipoArchivoId;
	}

	public function setDescripcion($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, '<br> Nombre del archivo');
		$this->descripcion = $value;
	}

	public function getDescripcion()
	{
		return $this->descripcion;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery('SELECT * FROM tipoArchivo ORDER BY tipoArchivoId ASC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM tipoArchivo WHERE tipoArchivoId = '".$this->tipoArchivoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				tipoArchivo
			SET
				`tipoArchivoId` = '".$this->tipoArchivoId."',
				`descripcion` = '".$this->descripcion."'
			WHERE tipoArchivoId = '".$this->tipoArchivoId."'");
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
				tipoArchivo
			(
				`tipoArchivoId`,
				`descripcion`
		)
		VALUES
		(
				'".$this->tipoArchivoId."',
				'".$this->descripcion."'
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
				tipoArchivo
			WHERE
				tipoArchivoId = '".$this->tipoArchivoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

}

?>