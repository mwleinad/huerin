<?php 

class Departamentos extends Main
{
	private $departamentoId;
	private $departamento;
	private $depArchivoId;
	private $nameArchivo;

	public function setDepartamentoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->departamentoId = $value;
	}

	public function getDepartamentoId()
	{
		return $this->departamentoId;
	}

	public function setDepartamento($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, '<br> Nombre del departamento');
		$this->departamento = $value;
	}

	public function getDepartamento()
	{
		return $this->departamento;
	}
	public function setDepArchivoId($value){
	    $this->Util()->ValidateInteger($value);
	    $this->depArchivoId = $value;
    }
    public function setNameArchivo($value){
        $this->Util()->ValidateRequireField($value,"Nombre");
        $this->nameArchivo = $value;
    }
    public function isUploadFile($FILES=[]){
	    if(empty($FILES))
	        $this->Util()->setError(0,'error','Es necesario adjuntar un archivo');
	    else{
            $nameFile = "archivos/depa_".$this->departamentoId."_".$FILES['name'];
            $this->Util()->DB()->setQuery("SELECT COUNT(*) FROM departamentosArchivos WHERE departamentoId = '".$this->departamentoId."' AND path = '".$nameFile."'");
            $count = $this->Util()->DB()->GetSingle();
            if($count>0)
                $this->Util()->setError(0,'error','Ya existe un archivo con el mismo nombre en este departamento, favor de verificar');
        }
    }
    public function GetListDepartamentos()
    {
        $this->Util()->DB()->setQuery('SELECT * FROM departamentos  ORDER BY departamento ASC ');
        $result = $this->Util()->DB()->GetResult();
        return $result;
    }
	public function Enumerate($filtro=[])
	{
	    $strFiltro = "";
		global $infoUser;
		if(!empty($filtro)){
		    if($filtro['depExcluidos']!=""){
		        $depExcluidos = explode(',',$filtro['depExcluidos']);
		        foreach($depExcluidos as $dep){
		            $strFiltro .= " and lower(departamento) not like '%".strtolower($dep)."%' ";
                }
            }
        }
		$this->Util()->DB()->setQuery("SELECT * FROM departamentos where 1 $strFiltro  ORDER BY departamento ASC ");
		$result = $this->Util()->DB()->GetResult();
		//encontrar el permisoId de cada departamento
        foreach($result as $key=> $dep){
            $this->Util()->DB()->setQuery('SELECT permisoId FROM permisos  WHERE titulo="'.$dep['departamento'].'" ');
            $perId = $this->Util()->DB()->GetSingle();
            $result[$key]['permId']=$perId;
        }
		return $result;
	}
    public function GetFirstDep()
    {
        global $User;
        $this->Util()->DB()->setQuery('SELECT permisoId FROM permisos  WHERE parentId=6 and permisoId!=149 AND permisoId!=148 ORDER BY titulo ASC '.$sql_add);
        $perms = $this->Util()->DB()->GetResult();
        $perm = $this->Util()->ConvertToLineal($perms,'permisoId');

        $filtro=' WHERE  a.permisoId IN ('.implode(',',$perm).') ';

        if($User['tipoPers']!="Admin")
           $filtro .=' AND a.rolId="'.$User['roleId'].'" ';

        $this->Util()->DB()->setQuery('SELECT b.titulo FROM rolesPermisos a INNER JOIN permisos b ON 
                                       a.permisoId=b.permisoId '.$filtro.' ORDER BY b.titulo ASC '.$sql_add);
        //$this->Util()->DB()->GetQuery();
        $single = $this->Util()->DB()->GetSingle();

        $this->Util()->DB()->setQuery('SELECT departamentoId FROM departamentos  WHERE departamento="'.$single.'"');
        $depId = $this->Util()->DB()->GetSingle();
        return $depId;
    }

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM departamentos WHERE departamentoId = '".$this->departamentoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}
	public function GetNameById(){
        $this->Util()->DB()->setQuery("SELECT departamento FROM departamentos WHERE departamentoId = '".$this->departamentoId."'");
        $single= $this->Util()->DB()->GetSingle();
        return $single;
    }

	public function Archivos()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM departamentosArchivos WHERE departamentoId = '".$this->departamentoId."' ORDER BY name asc");
		$row = $this->Util()->DB()->GetResult();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				departamentos
			SET
				`departamentoId` = '".$this->departamentoId."',
				`departamento` = '".$this->departamento."'
			WHERE departamentoId = '".$this->departamentoId."'");
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
				departamentos
			(
				`departamentoId`,
				`departamento`
		)
		VALUES
		(
				'".$this->departamentoId."',
				'".$this->departamento."'
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
					departamentos
				WHERE
					departamentoId = '".$this->departamentoId."'");
			$this->Util()->DB()->DeleteData();
			$this->Util()->setError(3, "complete");
			$this->Util()->PrintErrors();
		return true;
	}
	
	function SubirArchivo()
	{
        if($this->Util()->PrintErrors())
            return false;

		$folder = DOC_ROOT."/archivos";

		$target_path = $folder ."/depa_".$this->departamentoId."_".$_FILES["path"]['name'];
		$short_path = "archivos/depa_".$this->departamentoId."_".$_FILES["path"]['name'];
		if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)) {
            $this->Util()->DB()->setQuery("
					REPLACE INTO `departamentosArchivos` 
					(
					`departamentoId`, 
					`path`, 
					`name`, 
					`fecha`,
					`mime`
					) 
					VALUES 
					(
					'" . $this->departamentoId . "', 
					'" . $short_path . "', 
					'" . $this->nameArchivo . "', 
					'" . date("Y-m-d") . "', 
					'" . $_FILES["path"]["type"] . "'
				);");
            $this->Util()->DB()->InsertData();
            $this->Util()->setError(0,'complete',"Se ha agregado un nuevo archivo");
            $this->Util()->PrintErrors();
            return true;
        }else{
			$this->Util()->setError(0,'error','Error al mover archivo al servidor');
			$this->Util()->PrintErrors();
			return false;
		}
	}	
	
	function ActualizarArchivo()
	{
		if($this->Util()->PrintErrors())
		    return false;
        $strUpdateArchivo = "";
        $msj = "";
		$folder = DOC_ROOT."/archivos";
		if(!empty($_FILES["path"])){
            $ext = strtolower(end(explode('.', $_FILES["path"]['name'])));
            $type = strtolower(end(explode('.', $_FILES["path"]['type'])));
            $target_path = $folder ."/depa_".$this->departamentoId."_".$_FILES["path"]['name'];
            $short_path = "archivos/depa_".$this->departamentoId."_".$_FILES["path"]['name'];
            if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)){
                $strUpdateArchivo .=", mime = '$type', path='$short_path' ";
                $msj= " y el archivo ";
            }else {
              $this->Util()->setError(0,"error","Error al mover archivo al servidor");
              $this->Util()->PrintErrors();
              return false;
            }
        }
		$this->Util()->DB()->setQuery("
					UPDATE `departamentosArchivos`  SET
					name = '".$this->nameArchivo."', 
					fecha = '".date("Y-m-d")."'
					$strUpdateArchivo 
					WHERE  departamentosArchivosId = '".$this->depArchivoId."' ");
				$this->Util()->DB()->UpdateData();

        $this->Util()->setError(0,"complete","Se ha actualizado el nombre $msj correctamente");
        $this->Util()->PrintErrors();
		return true;
	}
	public function InfoArchivo($id)
	{
		$this->Util()->DB()->setQuery("SELECT * FROM departamentosArchivos WHERE departamentosArchivosId = '".$id."'");
		$row = $this->Util()->DB()->GetRow();

        $file = "";
		if(strlen($row['path'])>0)
		    $file = DOC_ROOT."/".$row['path'];

		if(file_exists($file))
		    $row["fileExist"] =  true;
		return $row;
	}

	public function DeleteArchivo($id)
	{
	    $this->Util()->DB()->setQuery("select path from departamentosArchivos where departamentosArchivosId = '$id' ");
	    $path = $this->Util()->DB()->GetSingle();
		$this->Util()->DB()->setQuery("DELETE FROM departamentosArchivos WHERE departamentosArchivosId = '".$id."'");
		$this->Util()->DB()->DeleteData();

        if(strlen($path)>1){
            if(file_exists(DOC_ROOT."/".$path))
                unlink(DOC_ROOT."/".$path);
        }
        $this->Util()->setError(0,"complete","Arachivo eliminado correctamente");
        $this->Util()->PrintErrors();
		return true;
	}
}
?>