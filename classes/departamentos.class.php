<?php 

class Departamentos extends Main
{
	private $departamentoId;
	private $departamento;

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

    public function GetListDepartamentos()
    {
        $this->Util()->DB()->setQuery('SELECT * FROM departamentos  ORDER BY departamento ASC ');
        $result = $this->Util()->DB()->GetResult();
        return $result;
    }
	public function Enumerate($all=false)
	{
		global $infoUser;
		
		//if($infoUser["tipoPersonal"]!="Socio" && !$all)
		//$filtroDepto=' WHERE departamentoId="'.$infoUser['departamentoId'].'" ';
				
		$this->Util()->DB()->setQuery('SELECT * FROM departamentos '.$filtroDepto.' ORDER BY departamento ASC '.$sql_add);
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
		$this->Util()->DB()->setQuery("SELECT * FROM departamentosArchivos WHERE departamentoId = '".$this->departamentoId."'");
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
		global $User;
		$folder = DOC_ROOT."/archivos";
		$ext = strtolower(end(explode('.', $_FILES["path"]['name'])));

		$target_path = $folder ."/depa_".$_POST["id"]."_".$_FILES["path"]['name']; 

		$short_path = "archivos/depa_".$_POST["id"]."_".$_FILES["path"]['name'];
		
		$target_path_path = basename( $_FILES["path"]['name']); 
			
		if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)) {
			
			$this->Util()->DB()->setQuery("
				SELECT COUNT(*) FROM
					departamentosArchivos
				WHERE
					departamentoId = '".$_POST["id"]."' AND path = '".$short_path."'");
			$count = $this->Util()->DB()->GetSingle();
			
			if($count > 0)
			{
				return;
			}
			
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
					'".$_POST["id"]."', 
					'".$short_path."', 
					'".$_POST["name"]."', 
					'".date("Y-m-d")."', 
					'".$_FILES["path"]["type"]."'
				);");
				$this->Util()->DB()->InsertData();
			}
		else
		{
			echo "No se pudo subir el archivo";
		}
		
	}	
	
	function ActualizarArchivo()
	{
		global $User;
		$folder = DOC_ROOT."/archivos";
		$ext = strtolower(end(explode('.', $_FILES["path"]['name'])));

		$target_path = $folder ."/depa_".$_POST["id"]."_".$_FILES["path"]['name']; 

		$short_path = "archivos/depa_".$_POST["id"]."_".$_FILES["path"]['name'];
		
		$target_path_path = basename( $_FILES["path"]['name']); 
			
		if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)) {
			
				$this->Util()->DB()->setQuery("
					UPDATE `departamentosArchivos`  SET
					`path` = '".$short_path."', 
					`name` = '".$_POST["name"]."', 
					`fecha` = '".date("Y-m-d")."', 
					`mime` = '".$_FILES["path"]["type"]."' WHERE  departamentosArchivosId = '".$_POST["departamentosArchivosId"]."'");
				$this->Util()->DB()->InsertData();
			}
		else
		{
			echo "No se pudo subir el archivo";
		}
		
	}		
	
	public function InfoArchivo($id)
	{
		$this->Util()->DB()->setQuery("SELECT * FROM departamentosArchivos WHERE departamentosArchivosId = '".$id."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function DeleteArchivo($id)
	{
		$this->Util()->DB()->setQuery("DELETE FROM departamentosArchivos WHERE departamentosArchivosId = '".$id."'");
		$row = $this->Util()->DB()->DeleteData();
		return true;
	}

}

?>