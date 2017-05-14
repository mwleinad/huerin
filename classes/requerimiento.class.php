<?php 

class Requerimiento extends Contract
{
	private $requerimientoId;
	private $contractId;
	private $tipoRequerimientoId;
	private $path;

	public function setRequerimientoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->requerimientoId = $value;
	}

	public function getRequerimientoId()
	{
		return $this->requerimientoId;
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

	public function setTipoRequerimientoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->tipoRequerimientoId = $value;
	}

	public function getTipoRequerimientoId()
	{
		return $this->tipoRequerimientoId;
	}

	public function setPath($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, 'path');
		$this->path = $value;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function EnumerateAll()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM requerimiento");
		$result = $this->Util()->DB()->GetResult();
		
		foreach($result as $key => $value)
		{
			$result[$key]["filePath"] = WEB_ROOT."/requerimientos/".$value["contractId"]."_".$value["path"];
		}
		return $result;
	}
	
	public function Enumerate()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM requerimiento 
		LEFT JOIN tipoRequerimiento ON tipoRequerimiento.tipoRequerimientoId = requerimiento.tipoRequerimientoId WHERE contractId = '".$this->getContractId()."' ORDER BY requerimientoId ASC");
		$result = $this->Util()->DB()->GetResult();
		
		foreach($result as $key => $value)
		{
			$result[$key]["filePath"] = WEB_ROOT."/requerimientos/".$value["contractId"]."_".$value["path"];
		}
		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM requerimiento 
		LEFT JOIN tipoRequerimiento ON tipoRequerimiento.tipoRequerimientoId = requerimiento.tipoRequerimientoId WHERE requerimientoId = '".$this->requerimientoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				requerimiento
			SET
				`requerimientoId` = '".$this->requerimientoId."',
				`contractId` = '".$this->contractId."',
				`tipoRequerimientoId` = '".$this->tipoRequerimientoId."',
				`path` = '".$this->path."'
			WHERE requerimientoId = '".$this->requerimientoId."'");
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
				requerimiento
			(
				`contractId`,
				`tipoRequerimientoId`
		)
		VALUES
		(
				'".$this->contractId."',
				'".$this->tipoRequerimientoId."'
		);");
		
		$id =	$this->Util()->DB()->InsertData();
		$folder = DOC_ROOT."/requerimientos/".$this->getContractId();
		
		$nombreArchivo = preg_replace("/&#?[a-z0-9]+;/i","", basename( $_FILES["path"]['name']));
		$nombreArchivo = str_replace(" ","", $nombreArchivo);
		
		$target_path = $folder ."_". $nombreArchivo; 
		$target_path_path = $nombreArchivo; 
			
		if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)) {
			$this->Util()->DB()->setQuery("UPDATE requerimiento SET path = '".$target_path_path."' WHERE requerimientoId = '".$id."'");
			$this->Util()->DB()->UpdateData();
			$this->Util()->setError(0, "complete", 'El archivo fue agregado correctamente');			
		}else{
			$this->Util()->setError(0, 'error', 'Ocurrio un error al subir el archivo');			
		}		
		
		$this->Util()->PrintErrors();
		
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				requerimiento
			WHERE
				requerimientoId = '".$this->requerimientoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

}

?>