<?php 

class TipoDocumento extends Main
{
	private $tipoDocumentoId;
	private $nombre;

	public function setTipoDocumentoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->tipoDocumentoId = $value;
	}

	public function getTipoDocumentoId()
	{
		return $this->tipoDocumentoId;
	}

	public function setNombre($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, '<br> Nombre del documento');
		$this->nombre = $value;
	}

	public function getNombre()
	{
		return $this->nombre;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery('SELECT * FROM tipoDocumento WHERE status="1" ORDER BY tipoDocumentoId ASC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();
		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM tipoDocumento WHERE tipoDocumentoId = '".$this->tipoDocumentoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				tipoDocumento
			SET
				`tipoDocumentoId` = '".$this->tipoDocumentoId."',
				`nombre` = '".$this->nombre."'
			WHERE tipoDocumentoId = '".$this->tipoDocumentoId."'");
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
				tipoDocumento
			(
				`tipoDocumentoId`,
				`nombre`
		)
		VALUES
		(
				'".$this->tipoDocumentoId."',
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
			UPDATE 	tipoDocumento
			  SET status='0'
			WHERE
				tipoDocumentoId = '".$this->tipoDocumentoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

}

?>