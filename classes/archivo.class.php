<?php
include_once(DOC_ROOT.'/classes/class.phpmailer.php');
class Archivo extends Contract
{
	private $archivoId;
	private $contractId;
	private $tipoArchivoId;
	private $datef;
	private $path;

	public function setArchivoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->archivoId = $value;
	}

	public function getArchivoId()
	{
		return $this->archivoId;
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

	public function setTipoArchivoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->tipoArchivoId = $value;
	}

	public function getTipoArchivoId()
	{
		return $this->tipoArchivoId;
	}

	public function setDate($value)
	{
	    $this->Util()->ValidateRequireField($value,"Fecha de vencimiento");
		$this->datef = $value;
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
		$this->Util()->DB()->setQuery("SELECT * FROM archivo");
		$result = $this->Util()->DB()->GetResult();
		setlocale(LC_TIME, 'spanish');

		foreach($result as $key => $value)
		{
			$diferencia = ceil(abs((strtotime($result[$key]["date"]) - strtotime(date("Y-m-d")))/86400));
			if(strtotime($result[$key]["date"]) < strtotime(date("Y-m-d")))
			{
				$result[$key]["dateColor"] = "#FF0000";
			}
			else
			{
				$result[$key]["dateColor"]= ($diferencia>60)?"#00CC00":"#FFFF00";
			}
			$result[$key]["filePath"] = WEB_ROOT."/archivos/".$value["contractId"]."_".$value["path"];
		}
		return $result;
	}

	public function Enumerate()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM archivo 
		LEFT JOIN tipoArchivo ON tipoArchivo.tipoArchivoId = archivo.tipoArchivoId WHERE contractId = '".$this->getContractId()."'ORDER BY archivoId ASC ".$sql_add);
		$result = $this->Util()->DB()->GetResult();
		setlocale(LC_TIME, 'spanish');

		foreach($result as $key => $value)
		{
			$result[$key]["date"] =strftime("%d %B %Y",strtotime($result[$key]["date"]));
			$result[$key]["filePath"] = WEB_ROOT."/archivos/".$value["contractId"]."_".$value["path"];
		}
		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM archivo WHERE archivoId = '".$this->archivoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function EditFecha()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				archivo
			SET
				`date` = '".date("Y-m-d",strtotime($this->datef))."'
			WHERE archivoId = '".$this->archivoId."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				archivo
			SET
				`archivoId` = '".$this->archivoId."',
				`contractId` = '".$this->contractId."',
				`tipoArchivoId` = '".$this->tipoArchivoId."',
				`date` = '".$this->datef."',
				`path` = '".$this->path."'
			WHERE archivoId = '".$this->archivoId."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
        if($_FILES['path']['error']==4||$_FILES['path']['name']==''||$_FILES['path']['type']=='')
            $this->Util()->setError(0,'error','Es necesario adjuntar un archivo o compruebe que el archivo sea valido');

		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			INSERT INTO
				archivo
			(
				`contractId`,
				`tipoArchivoId`,
				`date`
		)
		VALUES
		(
				'".$this->contractId."',
				'".$this->tipoArchivoId."',
				'".date("Y-m-d",strtotime($this->datef))."'
		);");

		$id =	$this->Util()->DB()->InsertData();
		$folder = DOC_ROOT."/archivos/".$this->getContractId();

		$nombreArchivo = preg_replace("/&#?[a-z0-9]+;/i","", basename( $_FILES["path"]['name']));
		$nombreArchivo = str_replace(" ","", $nombreArchivo);

		$target_path = $folder ."_". $nombreArchivo;
		$target_path_path = $nombreArchivo;


		if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)) {
			$this->Util()->DB()->setQuery("UPDATE archivo SET path = '".$target_path_path."' WHERE archivoId = '".$id."'");
			$this->Util()->DB()->UpdateData();
		}

		$this->Util()->setError(1, "complete", "Has agregado un archivo satisfactoriamente");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				archivo
			WHERE
				archivoId = '".$this->archivoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(1, "complete" , "Has borrado un archivo satisfactoriamente");
		$this->Util()->PrintErrors();
		return true;
	}

	public function GetArchivoContract($item,$dep){
        $result = array();
        $docs = array();
        //Obtener lista de archivos
        $this->Util()->DB()->setQuery('SELECT * FROM tipoArchivo WHERE status="1" order by nombre ASC');
        $tipos =  $this->Util()->DB()->GetResult();
        $noFile=0;
        $totalTipos = count($tipos);
        foreach($tipos as $key => $value) {
            $dptos =  explode(",",$value['dptosId']);
            if(!in_array($dep,$dptos))
            {
                $cad['nombreDoc']=$value['nombre'];
                $cad['fileExist'] = false;
                $cad['required'] = false;
                $cad['typeRequired'] = 'No Aplica';
                $docs[]=$cad;
                $totalTipos--;
                continue;
            }
            $this->Util()->DB()->setQuery('SELECT required  FROM requerimentsPersons WHERE resource="Archivo" AND (type="Ambos" OR type="' . $item['type'] . '") AND relacionId=' . $value['tipoArchivoId'] . '');
            $isRequired = $this->Util()->DB()->GetSingle();
            if ($isRequired == "")
                $isRequired = 'none';
            $this->Util()->DB()->setQuery('select * from archivo where tipoDocumentoId=' . $value['tipoArchivoId'] . ' AND contractId=' . $item['contractId']. ' order by archivoId  DESC');
            $row = $this->Util()->DB()->GetRow();
            $file = DOC_ROOT."/archivos/".$row["contractId"]."_".$row["path"];
            $cad = $row;
            switch($isRequired){
                case 'Obligatorio':
                    if(file_exists($file))
                        $cad['fileExist'] = true;
                    else{
                        $noFile++;
                        $cad['fileExist'] = false;
                    }
                    $cad['nombreDoc']=$value['nombre'];
                    $cad['required'] = true;
                    $cad['typeRequired'] = $isRequired;
                    break;
                case 'Opcional':
                    if(file_exists($file))
                        $cad['fileExist'] = true;
                    else
                        $cad['fileExist'] = false;

                    $cad['nombreDoc']=$value['nombre'];
                    $cad['required'] = false;
                    $cad['typeRequired'] = $isRequired;
                    break;
                case 'Condicional':
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
