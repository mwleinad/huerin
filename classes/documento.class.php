<?php

class Documento extends Contract
{
	private $documentoId;
	private $contractId;
	private $tipoDocumentoId;
	private $path;

	public function setDocumentoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->documentoId = $value;
	}

	public function getDocumentoId()
	{
		return $this->documentoId;
	}

	public function setContractId($value, $required = false)
	{
		$this->Util()->ValidateInteger($value);
		$this->contractId = $value;
	}

	public function getContractId()
	{
		return $this->contractId;
	}

	public function setTipoDocumentoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->tipoDocumentoId = $value;
	}

	public function getTipoDocumentoId()
	{
		return $this->tipoDocumentoId;
	}

	public function setPath($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, 'path');
		$this->path = $value;
	}
	private $dateExpiration;
    public function setDateExpiration($value)
    {
        $this->Util()->ValidateRequireField($value,'Fecha de expiracion');
        $this->dateExpiration = $value;
    }
    public function setFile($FILES)
    {
        if($FILES['error']==4)
            $this->Util()->setError(0,'error',"No se ha adjuntado un archivo");
    }

	public function getPath()
	{
		return $this->path;
	}

	public function EnumerateAll()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM documento");
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $value)
		{
			$result[$key]["filePath"] = WEB_ROOT."/documentos/".$value["contractId"]."_".$value["path"];
		}
		return $result;
	}

	public function Enumerate($id = 0, $status = '')
	{
		$this->Util()->DB()->setQuery("SELECT * FROM documento 
		LEFT JOIN tipoDocumento ON tipoDocumento.tipoDocumentoId = documento.tipoDocumentoId WHERE contractId = '".$this->getContractId()."' ORDER BY documentoId ASC");
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $value)
		{
			$result[$key]["filePath"] = WEB_ROOT."/documentos/".$value["contractId"]."_".$value["path"];
		}
		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM documento 
		LEFT JOIN tipoDocumento ON tipoDocumento.tipoDocumentoId = documento.tipoDocumentoId WHERE documentoId = '".$this->documentoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				documento
			SET
				`documentoId` = '".$this->documentoId."',
				`contractId` = '".$this->contractId."',
				`tipoDocumentoId` = '".$this->tipoDocumentoId."',
				`path` = '".$this->path."'
			WHERE documentoId = '".$this->documentoId."'");
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
				documento
			(
				`contractId`,
				`tipoDocumentoId`,
				`dateExpiration`
		)
		VALUES
		(
				'".$this->contractId."',
				'".$this->tipoDocumentoId."',
				'".$this->dateExpiration."'
		);");

		$id =	$this->Util()->DB()->InsertData();
		$folder = DOC_ROOT."/documentos/".$this->getContractId();

		$nombreArchivo = preg_replace("/&#?[a-z0-9]+;/i","", basename( $_FILES["path"]['name']));
		$nombreArchivo = str_replace(" ","", $nombreArchivo);

		$target_path = $folder ."_". $nombreArchivo;
		$target_path_path = $nombreArchivo;

		if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)) {
			$this->Util()->DB()->setQuery("UPDATE documento SET path = '".$target_path_path."' WHERE documentoId = '".$id."'");
			$this->Util()->DB()->UpdateData();
		}
		$this->Util()->setError(2, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				documento
			WHERE
				documentoId = '".$this->documentoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(3, "complete");
		$this->Util()->PrintErrors();
		return true;
	}
	public function GetDocContract($item,$dep){
        $result = array();
        $docs = array();
        //Obtener los documentos que debe ser obligatorio para cada contrato
        $this->Util()->DB()->setQuery('SELECT * FROM tipoDocumento WHERE status="1" order by nombre ASC');
        $tipos =  $this->Util()->DB()->GetResult();
        $totalTipos = count($tipos);
        $noFile=0;
        foreach($tipos as $key => $value)
        {
            $dptos =  explode(",",$value['dptosId']);
            if($dep && !in_array($dep,$dptos))
            {
                $cad['nombreDoc']=$value['nombre'];
                $cad['fileExist'] = false;
                $cad['required'] = false;
                $cad['typeRequired'] = 'No Aplica';
                $docs[]=$cad;
                $totalTipos--;
                continue;
            }
            $this->Util()->DB()->setQuery('SELECT required  FROM requerimentsPersons WHERE resource="Documento" AND (type="Ambos" OR type="'.$item['type'].'") AND relacionId="'.$value['tipoDocumentoId'].'"');
            $isRequired = $this->Util()->DB()->GetSingle();
            if($isRequired=="")
                $isRequired='none';

            $this->Util()->DB()->setQuery('select * from documento where tipoDocumentoId='.$value['tipoDocumentoId'].' AND contractId='.$this->contractId.' order by documentoId  DESC');
            $row = $this->Util()->DB()->GetRow();
            $cad=$row;
            $file ="";
            switch($isRequired){
                case 'Obligatorio':
                    $file = DOC_ROOT."/documentos/".$row["contractId"]."_".$row["path"];
                    if(file_exists($file)){
                        $cad['fileExist'] = true;
                    }
                    else{
                        $noFile++;
                        $cad['fileExist'] = false;
                    }
                    $cad['nombreDoc']=$value['nombre'];
                    $cad['required'] = true;
                    $cad['typeRequired'] = $isRequired;
                break;
                case 'Opcional':
                    $file = DOC_ROOT."/documentos/".$row["contractId"]."_".$row["path"];
                    if(file_exists($file))
                        $cad['fileExist'] = true;
                    else
                        $cad['fileExist'] = false;

                    $cad['nombreDoc']=$value['nombre'];
                    $cad['required'] = false;
                    $cad['typeRequired'] = $isRequired;
                break;
                case 'Condicional':
                    $file = DOC_ROOT."/documentos/".$row["contractId"]."_".$row["path"];
                    $cad['nombreDoc']=$value['nombre'];
                    if($item['respImss']){
                        if(file_exists($file))
                            $cad['fileExist'] = true;
                        else{
                            $noFile++;
                            $cad['fileExist'] = false;
                        }
                        $cad['required'] = true;
                        $cad['typeRequired'] = 'Obligatorio';

                    }
                    else{
                        $cad['fileExist'] = false;
                        $cad['required'] = false;
                        $cad['typeRequired'] = $isRequired;
                    }
                break;
               default:
                    $cad['nombreDoc']=$value['nombre'];
                    $cad['fileExist'] = false;
                    $cad['required'] = false;
                    $cad['typeRequired'] = 'No Aplica';
                break;
            }
            $docs[]=$cad;
        }
        $result['docs']= $docs;
        $result['noFiles'] = $noFile;
        $result['totalTipos'] = $totalTipos;
        return $result;
    }

}

?>
