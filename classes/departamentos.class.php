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
        $sql_add="";
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

	public function isSameDepartament() {
	    if($_SESSION['User']['isRoot'] || (int)$_SESSION['User']['level'] == 1){
	        return true;
        }
	    if((int)$_SESSION['User']['departamentoId'] === (int)$this->departamentoId)
	        return true;

	    return false;
    }

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

        $this->Util()->DB()->setQuery("select departamento from departamentos where departamentoId = '".$this->departamentoId."' ");
		$currentDepartamento = $this->Util()->DB()->GetSingle();

		$this->Util()->DB()->setQuery("
			UPDATE
				departamentos
			SET
				`departamentoId` = '".$this->departamentoId."',
				`departamento` = '".$this->departamento."'
			WHERE departamentoId = '".$this->departamentoId."'");
		$affect = $this->Util()->DB()->UpdateData();
        if($affect>0){
            $sql = "UPDATE permisos 
                    SET titulo = '".$this->departamento."' where titulo='$currentDepartamento' ";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->UpdateData();
        }
		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
	    global $rol;
		if($this->Util()->PrintErrors()){ return false; }
		$sql = "INSERT INTO departamentos
                (
                    `departamentoId`,
                    `departamento`
                )
                VALUES
                (
                    '".$this->departamentoId."',
                    '".$this->departamento."'
                );";
		$this->Util()->DB()->setQuery($sql);
		$id = $this->Util()->DB()->InsertData();
		//Al agregar un departamento nuevo debe agregarse como permiso
		if($id){
		    $sql= "INSERT INTO permisos
                  (
                    titulo,
                    parentId,
                    levelDeep
                  )VALUES(
                   '".$this->departamento."',
                   6,
                   1
                  )
                  ";
            $this->Util()->DB()->setQuery($sql);
            $permiso = $this->Util()->DB()->InsertData();
            //por default asignar permiso para socio y coordinador
            if($permiso>0){
                $rol->setRolId(1);
                $rol->AssignPermisoToRol($permiso);
                $rol->setRolId(5);
                $rol->AssignPermisoToRol($permiso);
            }

        }
		$this->Util()->setError(2, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Delete()
	{
		global $rol;
	    if($this->Util()->PrintErrors()){ return false; }

        $this->Util()->DB()->setQuery("select departamento from departamentos where departamentoId = '".$this->departamentoId."' ");
        $currentDepartamento = $this->Util()->DB()->GetSingle();
        $this->Util()->DB()->setQuery("
				DELETE FROM
					departamentos
				WHERE
					departamentoId = '".$this->departamentoId."'");
        $affect = $this->Util()->DB()->DeleteData();
        if($affect>0){
            $sql = "select permisoId from permisos where titulo='".$currentDepartamento."' ";
            $this->Util()->DB()->setQuery($sql);
            $idPermiso = $this->Util()->DB()->GetSingle();

            $sql = "DELETE FROM permisos 
                    WHERE titulo='$currentDepartamento' ";
            $this->Util()->DB()->setQuery($sql);
            $affect = $this->Util()->DB()->DeleteData();
            if($affect>0)
                if($idPermiso>0){
                    $rol->setRolId(0);
                    $rol->RemovePermisoToRol($idPermiso);
                }
        }
        $this->Util()->setError(3, "complete");
        $this->Util()->PrintErrors();
		return true;
	}
	function SubirArchivo()
	{
	    global $personal;
        if($this->Util()->PrintErrors())
            return false;

		$folder = DOC_ROOT."/archivos";
        $currentUser = $personal->getCurrentUser();

		$target_path = $folder ."/depa_".$this->departamentoId."_".$_FILES["path"]['name'];
        $nameNewFile = $_FILES["path"]['name'];
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

            $this->Util()->DB()->setQuery("select departamento from departamentos where departamentoId='".$this->departamentoId."' ");
            $nameDep = $this->Util()->DB()->GetSingle();
            $body ="<div style='width: 500px;text-align: justify'>";
            $body .="<p>El colaborador ".$currentUser["name"]." ha agregado un nuevo archivo al departamento de ".$nameDep." con el siguiente nombre:</p> ";
            $body .="<p>- ".$this->nameArchivo."</p> ";
            $body .="<p>Adjunto a este correo puede encontrar el archivo.</p> ";

            $personal->setDepartamentoId($this->departamentoId);
            $emails = $personal->getListPersonalByDepartamento("email");
            if(!SEND_LOG_MOD)
                $emails = [];

            $send = new SendMail();
            $send->PrepareMultipleNotice("NOTIFICACION DE CAMBIOS EN PLATAFORMA",$body,$emails,"","","",$target_path,$nameNewFile,"noreply@braunhuerin.com.mx","PLATAFORMA OPERATIVA",true);

            return true;
        }else{
			$this->Util()->setError(0,'error','Error al mover archivo al servidor');
			$this->Util()->PrintErrors();
			return false;
		}
	}
	function ActualizarArchivo()
	{
		global $personal;
	    if($this->Util()->PrintErrors())
		    return false;
        $strUpdateArchivo = "";
        $msj = "";
		$folder = DOC_ROOT."/archivos";
		$updated = 0;
		$currentUser = $personal->getCurrentUser();
        $current = $this->InfoArchivo($this->depArchivoId);
        $target_path = "";
        $nameNewFile = "";
        $current_path ="";
        $nameCurrentFile ="";

        $this->Util()->DB()->setQuery("select departamento from departamentos where departamentoId='".$this->departamentoId."' ");
        $nameDep = $this->Util()->DB()->GetSingle();

		$body ="<div style='width: 500px;text-align: justify'>";
		$body .="<p>El colaborador ".$currentUser["name"]." ha realizado cambios en los archivos del departamento de ".$nameDep." y son las siguientes:</p> ";
		if(!empty($_FILES["path"])){
		    $nameNewFile ="depa_".$this->departamentoId."_".$_FILES["path"]['name'];
            $type = strtolower(end(explode('.', $_FILES["path"]['type'])));
            $target_path = $folder ."/".$nameNewFile;
            $short_path = "archivos/".$nameNewFile;
            if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)){
                $strUpdateArchivo .=", mime = '$type', path='$short_path' ";
                $msj= " y el archivo ";
                $updated = 1;
                $nameNewFile = "(nuevo)".$nameNewFile;
                $body .="<p>- Se actualizo el archivo ".$this->nameArchivo.", adjunto al correo puede encontrar la anterior y nueva version del archivo.</p>";
                $current_path = DOC_ROOT."/".$current["path"];
                if(file_exists($current_path)){
                    $nameCurrentFile = substr($current["path"],9);
                    $nameCurrentFile = "(anterior)".$nameCurrentFile;
                }
                else
                    $current_path ="";
            }else {
              $this->Util()->setError(0,"error","Error al mover archivo al servidor");
              $this->Util()->PrintErrors();
              return false;
            }
        }
		if($current["name"]!=$this->nameArchivo){
		    $updated=1;
		    $body .="<p>- Actualizacion de nombre de archivo, de llamarse ".$current["name"]." a ".$this->nameArchivo."</p>";
        }
		if($updated) {
            $this->Util()->DB()->setQuery("
					UPDATE `departamentosArchivos`  SET
					name = '" . $this->nameArchivo . "', 
					fecha = '" . date("Y-m-d") . "'
					$strUpdateArchivo 
					WHERE  departamentosArchivosId = '" . $this->depArchivoId . "' ");
            $this->Util()->DB()->UpdateData();
            $personal->setDepartamentoId($this->departamentoId);
            $emails = $personal->getListPersonalByDepartamento("email");
            if(!SEND_LOG_MOD)
                $emails = [];
            $send = new SendMail();
            $send->PrepareMultipleNotice("NOTIFICACION DE CAMBIOS EN PLATAFORMA",$body,$emails,"",$current_path,$nameCurrentFile,$target_path,$nameNewFile,"noreply@braunhuerin.com.mx","PLATAFORMA OPERATIVA",true);
        }
        $this->Util()->setError(0, "complete", "Se ha actualizado el nombre $msj correctamente");
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
	    global $personal;
	    $this->Util()->DB()->setQuery("select * from departamentosArchivos where departamentosArchivosId = '$id' ");
	    $file= $this->Util()->DB()->GetRow();
		$this->Util()->DB()->setQuery("DELETE FROM departamentosArchivos WHERE departamentosArchivosId = '".$id."'");
		$this->Util()->DB()->DeleteData();

        if(strlen($file["path"])>1) {
            if (file_exists(DOC_ROOT . "/" . $file["path"]))
            {
                $currentFile= DOC_ROOT . "/" . $file["path"];
                $nameFile = substr($file["path"],9);
                $currentUser = $personal->getCurrentUser();
                $this->Util()->DB()->setQuery("select departamento from departamentos where departamentoId='" .$file["departamentoId"]. "' ");
                $nameDep = $this->Util()->DB()->GetSingle();
                $body = "<div style='width: 500px;text-align: justify'>";
                $body .= "<p>El colaborador " . $currentUser["name"] . " ha eliminado un archivo del departamento de " . $nameDep . " con el siguiente nombre:</p> ";
                $body .= "<p>- " .$file["name"]. "</p> ";
                $body .= "<p>Adjunto a este correo puede encontrar el archivo eliminado.</p> ";

                $personal->setDepartamentoId($file["departamentoId"]);
                $emails = $personal->getListPersonalByDepartamento("email");
                if (!SEND_LOG_MOD)
                    $emails = [];

                $send = new SendMail();
                $send->PrepareMultipleNotice("NOTIFICACION DE CAMBIOS EN PLATAFORMA", $body, $emails, "", "", "", $currentFile, $nameFile, "noreply@braunhuerin.com.mx", "PLATAFORMA OPERATIVA", true);
                unlink(DOC_ROOT . "/" . $file["path"]) ;
            }
        }
        $this->Util()->setError(0,"complete","Arachivo eliminado correctamente");
        $this->Util()->PrintErrors();
		return true;
	}
}
?>
