<?php 

class Sociedad extends Main
{
	private $sociedadId;
	private $nombreSociedad;

	public function setSociedadId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->sociedadId = $value;
	}

	public function getSociedadId()
	{
		return $this->sociedadId;
	}

	public function setNombreSociedad($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, '<br /> Nombre de la sociedad');
		$this->nombreSociedad = $value;
	}

	public function getNombreSociedad()
	{
		return $this->nombreSociedad;
	}

	public function EnumerateAll()
	{
		$this->Util()->DB()->setQuery('SELECT * FROM sociedad ORDER BY nombreSociedad ASC');
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery('SELECT COUNT(*) FROM sociedad');
		$total = $this->Util()->DB()->GetSingle();

		$pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/sociedad");

		$sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
		$this->Util()->DB()->setQuery('SELECT * FROM sociedad ORDER BY sociedadId ASC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $row)
		{
		}
		$data["items"] = $result;
		$data["pages"] = $pages;
		return $data;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM sociedad WHERE sociedadId = '".$this->sociedadId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				sociedad
			SET
				`sociedadId` = '".$this->sociedadId."',
				`nombreSociedad` = '".$this->nombreSociedad."'
			WHERE sociedadId = '".$this->sociedadId."'");
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
				sociedad
			(
				`sociedadId`,
				`nombreSociedad`
		)
		VALUES
		(
				'".$this->sociedadId."',
				'".$this->nombreSociedad."'
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
				sociedad
			WHERE
				sociedadId = '".$this->sociedadId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

}

?>