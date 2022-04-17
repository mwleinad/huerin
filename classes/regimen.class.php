<?php 

class Regimen extends Main
{
	private $regimenId;
	private $regimenName;
	private $tipoDePersona;

	public function setTipoDePersona($value)
	{
		$this->tipoDePersona = $value;
	}

	public function setRegimenId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->regimenId = $value;
	}

	public function getRegimenId()
	{
		return $this->regimenId;
	}

	public function setRegimenName($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, 'Nombre del regimen');
		$this->regimenName = $value;
	}

	public function getRegimenName()
	{
		return $this->regimenName;
	}

	public function EnumerateAll($tipo = 0)
	{
		$add =  $tipo > 0
            ? " WHERE tax_purpose IN(3, $tipo)"
            : "";

        $sql = "SELECT *,
                CASE tax_purpose
                 WHEN 1 THEN 'Persona Moral'   
                 WHEN 2 THEN 'Persona Fisica'
                 ELSE 'Ambos'   
                END as tipoDePersona
                FROM regimen ".$add." ORDER BY tax_purpose ASC, nombreRegimen ASC";
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery('SELECT COUNT(*) FROM regimen');
		$total = $this->Util()->DB()->GetSingle();

		$pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/regimen");

		$sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
        $sql = "SELECT *,
                CASE tax_purpose
                 WHEN 1 THEN 'Persona Moral'   
                 WHEN 2 THEN 'Persona Fisica'
                 ELSE 'Ambos'   
                END as tipoDePersona
                FROM regimen ORDER BY regimenId ASC, nombreRegimen ASC ".$sql_add;
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		$data["items"] = $result;
		$data["pages"] = $pages;
		return $data;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM regimen WHERE regimenId = '".$this->regimenId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				regimen
			SET
				`regimenId` = '".$this->regimenId."',
				`nombreRegimen` = '".$this->regimenName."',
				`tax_purpose` = '".$this->tipoDePersona."'
			WHERE regimenId = '".$this->regimenId."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(1, "complete", "Has editado este Regimen");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			INSERT INTO
				regimen
			(
				`regimenId`,
				`nombreRegimen`,
				`tax_purpose`
		)
		VALUES
		(
				'".$this->regimenId."',
				'".$this->regimenName."',
				'".$this->tipoDePersona."'
		);");
		$this->Util()->DB()->InsertData();
		$this->Util()->setError(1, "complete", "Has agregado un Regimen");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				regimen
			WHERE
				regimenId = '".$this->regimenId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(1, "complete", "Has borrado un Regimen");
		$this->Util()->PrintErrors();
		return true;
	}
	
	public function GetNameById(){
		
		$sql = "SELECT nombreRegimen FROM regimen
				WHERE regimenId = '".$this->regimenId."'";
		$this->Util()->DB()->setQuery($sql);
		$nombre = $this->Util()->DB()->GetSingle();
		
		return $nombre; 
	}
    function ListTiposRegimen()
    {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM tipoRegimen ORDER BY nombreRegimen ASC");

        $result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();

        return $result;
    }


}

?>