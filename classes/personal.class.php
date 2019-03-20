<?php

class Personal extends Main
{
	private $personalId;
	private $name;
	private $phone;
	private $email;
	private $username;
	private $passwd;
	private $active;
	private $ext;
	private $fechaIngreso;
	private $showAll =  false;

	public function setExt($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Extension");
		$this->ext = $value;
	}
    public function isShowAll()
    {
        $this->showAll =  true;
    }
	private $celphone;
	public function setCelphone($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Celular");
		$this->celphone = $value;
	}
    private $roleId;
    public function setRole($value)
    {
        if($this->Util()->ValidateRequireField($value, "Tipo de Usuario(rolId)"))
        $this->roleId = $value;
    }
	private $skype;
	public function setSkype($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Skype");
		$this->skype = $value;
	}

	private $puesto;
	public function setPuesto($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Puesto");
		$this->puesto = $value;
	}

	private $aspel;
	public function setAspel($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Aspel");
		$this->aspel = $value;
	}

	private $horario;
	public function setHorario($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Horario");
		$this->horario = $value;
	}

	private $sueldo;
	public function setSueldo($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Sueldo");
		$this->sueldo = $value;
	}

	private $grupo;
	public function setGrupo($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Grupo");
		$this->grupo = $value;
	}

	private $computadora;
	public function setComputadora($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Computadora");
		$this->computadora = $value;
	}


	public function setPersonalId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->personalId = $value;
	}

	public function setName($value)
	{
		if($this->Util()->ValidateRequireField($value, "Nombre"))
			$this->Util()->ValidateString($value, $max_chars=60, $minChars = 1, "Nombre");
		$this->name = $value;
	}

	public function setPhone($value)
	{
		$this->phone = $value;
	}

	public function setEmail($value)
	{
		$this->email = $value;
	}

	public function setUsername($value)
	{
		$this->username = $value;
	}

	public function setPasswd($value)
	{
		$this->passwd = $value;
	}

	public function setActive($value)
	{
		$this->active = $value;
	}
	var $tipoPersonal;
	public function setTipoPersonal($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Tipo Personal");
		$this->tipoPersonal = $value;
	}

		var $departamentoId;
	public function setDepartamentoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->departamentoId = $value;
	}

	var $jefeContador;
	public function setJefeContador($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->jefeContador = $value;
	}

	var $jefeInmediato;
	public function setJefeInmediato($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->jefeInmediato = $value;
	}

	var $jefeSupervisor;
	public function setJefeSupervisor($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->jefeSupervisor = $value;
	}

	var $jefeGerente;
	public function setJefeGerente($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->jefeGerente = $value;
	}

	var $jefeSocio;
	public function setJefeSocio($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->jefeSocio = $value;
	}

	public function setFechaIngreso($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, '');
		$this->fechaIngreso = $value;
	}


	public function Enumerate()
	{
		global $infoUser;
		//Socio y Asistente pueden ver todo el personal.
		if($this->active)
			$sqlActive = " AND a.active = '1'";
		if ($infoUser['tipoPersonal'] == "Socio" || $infoUser['tipoPersonal'] == "Admin" || $infoUser['tipoPersonal'] == "Coordinador" || stripos($infoUser['tipoPersonal'],'RRHH')!==false || $this->showAll) {
			$sql = "SELECT
						a.*,
						b.name as nombreJefe,
						c.departamento
					FROM
						personal a 
						LEFT JOIN personal b ON a.jefeInmediato=b.personalId 
						LEFT JOIN departamentos c ON a.departamentoId=c.departamentoId WHERE 1
					".$sqlFilter.$sqlActive." 
					ORDER BY
						a.name ASC";
	
			$this->Util()->DB()->setQuery($sql);
			$result = $this->Util()->DB()->GetResult();
			return $result;
		}
		
		$this->setPersonalId($infoUser['personalId']);
   	    $result = $this->SubordinadosDetails();
   	    return $result;
	}
	
	function EnumerateById($ids)
	{
		$sql = "SELECT
						*
					FROM
						personal WHERE personalId IN (".$ids.")";
		$this->Util()->DB()->setQuery($sql);				
		$result = $this->Util()->DB()->GetResult();
	
		return $result;		
	}

	public function EnumerateAll()
	{
		$sql = "SELECT
					*
				FROM
					personal WHERE 1
				".$sqlFilter.$sqlActive."
				ORDER BY
					name ASC";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function ListDepartamentos()
	{

		$sql = "SELECT
					*
				FROM
					departamentos
				ORDER BY
					departamento ASC";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $res)
		{
			$result[$key]["departamento"] = $result[$key]["departamento"];
		}

		return $result;
	}

	public function ListAll()
	{
		if($this->active)
			$sqlActive = " AND active = '1'";

		$sql = "SELECT
					*
				FROM
					personal
				WHERE 1 
				".$sqlActive."
				ORDER BY
					name ASC";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $res)
		{
			$result[$key]["name"] = $result[$key]["name"];
		}

		return $result;
	}

	public function ListContadores()
	{
		if($this->active)
			$sqlActive = " AND active = '1'";

		$sql = "SELECT
					*
				FROM
					personal
				WHERE tipoPersonal = 'Contador'
				".$sqlActive."
				ORDER BY
					name ASC";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $res)
		{
			$result[$key]["name"] = $result[$key]["name"];
		}

		return $result;
	}

	public function ListSupervisores()
	{
		if($this->active)
			$sqlActive = " AND active = '1'";

		$sql = "SELECT
					*
				FROM
					personal
				WHERE tipoPersonal = 'Supervisor'
				".$sqlActive."
				ORDER BY
					name ASC";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $res)
		{
			$result[$key]["name"] = $result[$key]["name"];
		}

		return $result;
	}
	public function ListSupervisoresAutoComplete($name)
	{
		$sql = "
			SELECT
				*
			FROM
				personal
			WHERE
				tipoPersonal = 'Supervisor' AND
				name like '%".$name."%' AND
				active = '1'
			ORDER BY name ASC
		";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function ListGerentes()
	{
		if($this->active)
			$sqlActive = " AND active = '1'";

		$sql = "SELECT
					*
				FROM
					personal
				WHERE tipoPersonal = 'Gerente'
				".$sqlActive."
				ORDER BY
					name ASC";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		foreach($result as $key => $res)
		{
			$result[$key]["name"] = $result[$key]["name"];
		}

		return $result;
	}

	public function ListSocios()
	{
		if($this->active)
			$sqlActive = " AND active = '1'";

		$sql = "SELECT * FROM personal
				WHERE tipoPersonal = 'Socio' OR roleId in(1,5)
				".$sqlActive."
				ORDER BY name ASC";
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM personal WHERE personalId = '".$this->personalId."'");
		$row = $this->Util()->DB()->GetRow();
		$row['fechaIngresoMysql'] = $this->Util()->FormatDateMySql($row['fechaIngreso']);
		return $row;
	}
	public function jefeInmediato(){
        $this->Util()->DB()->setQuery("SELECT j.* FROM personal a INNER JOIN personal j ON a.jefeInmediato=j.jefeInmediato WHERE a.personalId = '".$this->personalId."'");
        $row = $this->Util()->DB()->GetRow();
        $row['fechaIngresoMysql'] = $this->Util()->FormatDateMySql($row['fechaIngreso']);
        return $row;
    }

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		if($this->tipoPersonal == "Contador")
		{
			$this->jefeContador = 0;
		}

		if($this->tipoPersonal == "Supervisor")
		{
			$this->jefeSupervisor = 0;
			$this->jefeContador = 0;
		}

		if($this->tipoPersonal == "Gerente")
		{
			$this->jefeGerente = 0;
			$this->jefeSupervisor = 0;
			$this->jefeContador = 0;
		}

		if($this->tipoPersonal == "Socio")
		{
			$this->jefeSocio = 0;
			$this->jefeGerente = 0;
			$this->jefeSupervisor = 0;
			$this->jefeContador = 0;
		}
		$this->Util()->DB()->setQuery("
			UPDATE
				personal
			SET
				`name` = '".$this->name."',
				phone = '".$this->phone."',
				email = '".$this->email."',
				username = '".$this->username."',
				passwd = '".$this->passwd."',
				ext = '".$this->ext."',
				celphone = '".$this->celphone."',
				aspel = '".$this->aspel."',
				skype = '".$this->skype."',
				puesto = '".$this->puesto."',
				horario = '".$this->horario."',
				sueldo = '".$this->sueldo."',
				grupo = '".$this->grupo."',
				jefeInmediato = '".$this->jefeInmediato."',
				computadora = '".$this->computadora."',
				tipoPersonal = '".trim($this->tipoPersonal)."',
				roleId = '".trim($this->roleId)."',
				departamentoId = '".$this->departamentoId."',
				fechaIngreso = '".$this->fechaIngreso."',
				active = '".$this->active."'
			WHERE personalId = '".$this->personalId."'");
		$this->Util()->DB()->UpdateData();
        //actualizar los expedientes.
        if(!empty($_POST['expe'])){
            $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery('SELECT expedienteId from personalExpedientes WHERE personalId="'.$this->personalId.'" ');
            $arrayExp = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
            $expActual = $this->Util()->ConvertToLineal($arrayExp,'expedienteId');
            $sql2 = 'REPLACE INTO personalExpedientes(personalId,expedienteId) VALUES';
            foreach($_POST['expe'] as $exp){
                if($exp===end($_POST['expe']))
                    $sql2 .="(".$this->personalId.",".$exp.");";
                else
                    $sql2 .="(".$this->personalId.",".$exp."),";
                //encontrar la posicion de $exp en expActual
                $key = array_search($exp,$expActual);
                unset($expActual[$key]);
            }

            $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql2);
            $this->Util()->DBSelect($_SESSION['empresaId'])->UpdateData();
            if(!empty($expActual)){
                $sqlu = "DELETE FROM personalExpedientes WHERE expedienteId IN(".implode(",",$expActual).") AND personalId='".$this->personalId."'";
                $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sqlu);
                $this->Util()->DBSelect($_SESSION['empresaId'])->DeleteData();
            }
        }
		$this->Util()->setError(10049, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		if($this->Util()->PrintErrors()){ return false; }
		$this->Util()->DB()->setQuery("
			INSERT INTO
				personal
			(
				`name`,
				phone,
				email,
				username,
				passwd,
				ext,
				celphone,
				skype,
				aspel,
				puesto,
				horario,
				sueldo,
				grupo,
				jefeInmediato,
				computadora,
				tipoPersonal,
				roleId,
				departamentoId,
				fechaIngreso,
				lastChangePassword,
				active
		)
		VALUES
		(
				'".$this->name."',
				'".$this->phone."',
				'".$this->email."',
				'".$this->username."',
				'".$this->passwd."',
				'".$this->ext."',
				'".$this->celphone."',
				'".$this->skype."',
				'".$this->aspel."',
				'".$this->puesto."',
				'".$this->horario."',
				'".$this->sueldo."',
				'".$this->grupo."',
				'".$this->jefeInmediato."',
				'".$this->computadora."',
				'".trim($this->tipoPersonal)."',
				'".trim($this->roleId)."',
				'".$this->departamentoId."',
				'".$this->fechaIngreso."',
				'".$this->fechaIngreso."',
				'".$this->active."'
		);");
		$id = $this->Util()->DB()->InsertData();
        if(!empty($_POST['expe'])){
            $sql = 'REPLACE INTO personalExpedientes(personalId,expedienteId) VALUES';
            foreach($_POST['expe'] as $exp){
                if($exp===end($_POST['expe']))
                    $sql .="(".$id.",".$exp.");";
                else
                    $sql .="(".$id.",".$exp."),";
            }
            $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
            $this->Util()->DBSelect($_SESSION['empresaId'])->UpdateData();
        }
		$this->Util()->setError(10048, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				personal
			WHERE
				personalId = '".$this->personalId."'");
		$this->Util()->DB()->DeleteData();

		$this->Util()->setError(10050, "complete", "Has eliminado al Contador satisfactoriamente");
		$this->Util()->PrintErrors();
		return true;
	}

	public function GetDataReport(){

		$sql = 'SELECT
					name,
					tipoPersonal
				FROM
					personal
				WHERE
					personalId = '.$this->personalId;

		$this->Util()->DB()->setQuery($sql);

		return $this->Util()->DB()->GetRow();

	}

	public function GetNameById(){

		$sql = 'SELECT
					name
				FROM
					personal
				WHERE
					personalId = '.$this->personalId;

		$this->Util()->DB()->setQuery($sql);

		return $this->Util()->DB()->GetSingle();
	}

	function Restrict()
	{
		global $infoUser, $page;

		$restricted = array();

		switch($infoUser["tipoPersonal"])
		{
			case "Recepcion":
				$restricted  = array(
					"personal",
					"regimen",
					"sociedad",
					"tipoServicio",
					"tipoDocumento",
					"tipoArchivo",
					"impuesto",
					"obligacion",
				);
				break;
		}

		if(in_array($page, $restricted))
		{
			header("Location:".WEB_ROOT);
		}
	}

	function SubordinadosOld()
	{
		$info = $this->Info();

		$subordinados = array();
		$sql = "SELECT
					personalId
				FROM
					personal
				WHERE
					jefeSocio = '".$this->personalId."' OR
					jefeSupervisor = '".$this->personalId."' OR
					jefeGerente = '".$this->personalId."' OR
					jefeContador = '".$this->personalId."'";
		$this->Util()->DB()->setQuery($sql);
		$subordinados = $this->Util()->DB()->GetResult();

		return $subordinados;
	}
	
function Subordinados($whitDpto=false)
{   
	$sql ="SELECT personal.*, jefes.name AS jefeName FROM personal
		LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
	$this->Util()->DB()->setQuery($sql);
	$result = $this->Util()->DB()->GetResult($sql);
	
	$jerarquia = $this->Jerarquia($result, $this->personalId);
	
	$_SESSION["lineal"] = array();
	if($whitDpto)
        $this->JerarquiaLinealWhitDpto($jerarquia);
	else
	    $this->JerarquiaLinealJustId($jerarquia);
	
	return $_SESSION["lineal"];
}

function AddMeToArray()
{
	$sql ="SELECT personal.*, jefes.name AS jefeName FROM personal
		LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato WHERE personal.personalId = '".$_SESSION["User"]["userId"]."'ORDER BY name ASC";
	$this->Util()->DB()->setQuery($sql);
	$row = $this->Util()->DB()->GetRow($sql);

	array_unshift($_SESSION["lineal"], $row);
}

function SubordinadosDetails()
{   
	$sql ="SELECT personal.*, jefes.name AS jefeName FROM personal
		LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
	$this->Util()->DB()->setQuery($sql);
	$result = $this->Util()->DB()->GetResult($sql);
	
	$jerarquia = $this->Jerarquia($result, $this->personalId);
	
	$_SESSION["lineal"] = array();
	$this->JerarquiaLineal($jerarquia);
	
	$this->AddMeToArray();
	
	return $_SESSION["lineal"];
}	
	
	function GetRoleId($tipoPersonal)
	{
	  switch($tipoPersonal)
	  {
		  case "Socio": $roleId = 1; break;
		  case "Gerente": $roleId = 2; break;
		  case "Supervisor": $roleId = 3; break;
		  case "Contador": $roleId = 3; break;
		  case "Auxiliar": $roleId = 3; break;
		  case "Asistente": $roleId = 1; break;
		  case "Recepcion": $roleId = 1; break;
		  case "Cliente":$roleId = 4; break;
		  case "Nomina":
			  $roleId = 1;
			  //$User['subRoleId'] = "Nomina";
		  break;
	  }

	  return $roleId;
	}
	
	function Jerarquia(array $elements, $parentId = 0) {
			$branch = array();
	
			foreach ($elements as $element) {
					if ($element['jefeInmediato'] == $parentId) {
							$children = $this->Jerarquia($elements, $element['personalId']);
							if ($children) {
									$element['children'] = $children;
							}
							$branch[] = $element;
					}
			}
	
			return $branch;
	}
	
	
	function JerarquiaLineal($tree)
	{
		
		foreach($tree as $key => $value)
		{
/*			$card["tipoPersonal"] = $value["tipoPersonal"];
			$card["name"] = $value["name"];
			$card["jefeName"] = $value["jefeName"];
			$card["personalId"] = $value["personalId"];
*/			$_SESSION["lineal"][] = $value;
		
			if(count($value["children"]) > 0)
			{
				$this->JerarquiaLineal($value["children"]);
			}
		}
	}
	
	function JerarquiaLinealJustId($tree)
	{
		foreach($tree as $key => $value)
		{
			$card["personalId"] = $value["personalId"];
			$_SESSION["lineal"][] = $card;
		
			if(count($value["children"]) > 0)
			{
				$this->JerarquiaLinealJustId($value["children"]);
			}
		}
	}
    function JerarquiaLinealWhitDpto($tree)
    {
        foreach($tree as $key => $value)
        {
            $card["personalId"] = $value["personalId"];
            $card["dptoId"] = $value["departamentoId"];
            $_SESSION["lineal"][] = $card;

            if(count($value["children"]) > 0)
            {
                $this->JerarquiaLinealWhitDpto($value["children"]);
            }
        }
    }
	
	
	function ArrayOrdenadoPersonal()
	{
		$sql ="SELECT personal.personalId, personal.name, personal.tipoPersonal, personal.jefeInmediato, jefes.name AS jefeName FROM personal
			LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult($sql);
		
		$jerarquia = $this->Jerarquia($result);
		
		$_SESSION["lineal"] = array();
		$this->JerarquiaLineal($jerarquia);

		$lineal = $_SESSION["lineal"];
		return $lineal;

	}
	
	function printTree($tree)
	{
		foreach($tree as $key => $value)
		{
			?>
			<tr>
				<td><?php echo $value["tipoPersonal"];?></td>
				<td><?php echo $value["name"];?></td>
				<td><?php echo $value["jefeName"];?></td>
			</tr>
			<?php 
			
			if(count($value["children"]) > 0)
			{
				$this->printTree($value["children"]);
			}
			
		}
		
	}
	function SubordinadosOrder()
	{
		$info = $this->Info();

		$subordinados = array();
		$sql = "SELECT
					personalId, name, tipoPersonal
				FROM
					personal
				WHERE
					jefeSocio = '".$this->personalId."' OR
					jefeSupervisor = '".$this->personalId."' OR
					jefeGerente = '".$this->personalId."' OR
					jefeContador = '".$this->personalId."'
				ORDER BY FIELD(tipoPersonal, 'Socio', 'Asistente', 'Gerente', 'Supervisor', 'Contador', 'Nomina','Auxiliar',  'Recepcion'), name ASC";
		$this->Util()->DB()->setQuery($sql);
		$subordinados = $this->Util()->DB()->GetResult();

		return $subordinados;
	}

	function jefes($inputId = 0, $idList=array())
	{   
			$db = new DB();    
			$sql ="SELECT * FROM personal where personalId='".$inputId."'";
			$db->setQuery($sql);
			$result = $db->GetResult($sql);
	
			if($result){
					$currentId = $result[0]["personalId"];
					$parentId = $result[0]["jefeInmediato"];
	
					$idList[] = $currentId;
	
					if ($parentId !=0){
						 return $this->jefes($parentId, $idList);
					}
			}
			return $idList;
	}
	function findTreeSubordinate($id){
	    global $personal,$rol;
	    $cad = array();
        $this->Util()->DB()->setQuery('SELECT name,personalId,jefeInmediato,puesto,tipoPersonal FROM  personal WHERE personalId='.$id);
        $row = $this->Util()->DB()->GetRow();
        $role = $rol->getInfoByData($row);
        $rolArray = explode(' ',$role['name']);
        $needle = trim($rolArray[0]);
	    switch ($needle){
            case 'Coordinador':
            case 'Socio':
                $cad=$row;
             break;
            case 'Gerente':
                $cad=$row;
                $personal->setPersonalId($row['jefeInmediato']);
                $cad['jefeMax'] = $personal->GetNameById();
                break;
            case 'Sistemas':
            case 'Gestoria':
            case 'Supervisor':
                $cad = $row;
                $gerenteId = $row['jefeInmediato'] == 0 ? $row['personalId'] : $row['jefeInmediato'];
                $personal->setPersonalId($gerenteId);
                $jGer = $personal->Info();
                $role = $rol->getInfoByData($jGer);
                $rolArray = explode(' ',$role['name']);
                $needle = trim($rolArray[0]);
                if($needle=='Gerente'){
                    $personal->setPersonalId($jGer['personalId']);
                    $cad['gerente'] = $personal->GetNameById();
                }else
                    $jGer['jefeInmediato']=$gerenteId;

                $jefeId = $jGer['jefeInmediato'] == 0 ? $jGer['personalId'] : $jGer['jefeInmediato'];
                $personal->setPersonalId($jefeId);
                $cad['jefeMax'] = $personal->GetNameById();
            break;
            case 'Asistente':
            case 'Cuentas':
            case 'Contador':
                $cad = $row;
                $supervisorId = $row['jefeInmediato'] == 0 ? $row['personalId'] : $row['jefeInmediato'];
                $personal->setPersonalId($supervisorId);
                $jSup = $personal->Info();
                $role = $rol->getInfoByData($jSup);
                $rolArray = explode(' ',$role['name']);
                $needle = trim($rolArray[0]);
            //cuentas por cobrar puede tener jefe inmediato del mismom rol
                if($needle=='Supervisor' ||$needle=='Gestoria'||$needle=='Sistemas'||$needle=='Cuentas'){
                    $personal->setPersonalId($jSup['personalId']);
                    $cad['supervisor'] = $personal->GetNameById();
                }else
                    $jSup['jefeInmediato']=$supervisorId;

                $gerenteId = $jSup['jefeInmediato'] == 0 ? $jSup['personalId'] : $jSup['jefeInmediato'];
                $personal->setPersonalId($gerenteId);
                $jGer = $personal->Info();
                $role = $rol->getInfoByData($jGer);
                $rolArray = explode(' ',$role['name']);
                $needle = trim($rolArray[0]);
                if($needle=='Gerente'){//|| $needle=='Asistente'
                    $personal->setPersonalId($jGer['personalId']);
                    $cad['gerente'] = $personal->GetNameById();
                }else
                    $jGer['jefeInmediato']=$gerenteId;

                $jefeId = $jGer['jefeInmediato'] == 0 ? $jGer['personalId'] : $jGer['jefeInmediato'];
                $personal->setPersonalId($jefeId);
                $cad['jefeMax'] = $personal->GetNameById();
                break;
            case 'Recepcion':
            case 'Auxiliar':
                $cad = $row;
                $contadorId =$row['jefeInmediato'] == 0 ? $row['personalId'] : $row['jefeInmediato'];
                $personal->setPersonalId($contadorId);
                $jCont = $personal->Info();
                $role = $rol->getInfoByData($jCont);
                $rolArray = explode(' ',$role['name']);
                $needle = trim($rolArray[0]);
                if($needle=='Contador'||$needle=='Asistente'||$needle=='Cuentas'){// || $needle=='Auxiliar'
                    $personal->setPersonalId($jCont['personalId']);
                    $cad['contador'] = $personal->GetNameById();
                }else
                    $jCont['jefeInmediato']=$contadorId;

                $supervisorId = $jCont['jefeInmediato'] == 0 ? $jCont['personalId'] : $jCont['jefeInmediato'];
                $personal->setPersonalId($supervisorId);
                $jSup = $personal->Info();
                $role = $rol->getInfoByData($jSup);
                $rolArray = explode(' ',$role['name']);
                $needle = trim($rolArray[0]);
                if($needle=='Supervisor'||$needle=='Gestoria'||$needle=='Sistemas'){
                    $personal->setPersonalId($jSup['personalId']);
                    $cad['supervisor'] = $personal->GetNameById();
                }else
                    $jSup['jefeInmediato']=$supervisorId;

                $gerenteId = $jSup['jefeInmediato'] == 0 ? $jSup['personalId'] : $jSup['jefeInmediato'];
                $personal->setPersonalId($gerenteId);
                $jGer = $personal->Info();
                $role = $rol->getInfoByData($jGer);
                $rolArray = explode(' ',$role['name']);
                $needle = trim($rolArray[0]);
                if($needle=='Gerente'){// || $needle=='Asistente'
                    $personal->setPersonalId($jGer['personalId']);
                    $cad['gerente'] = $personal->GetNameById();
                }else
                    $jGer['jefeInmediato']=$gerenteId;

                $jefeId = $jGer['jefeInmediato'] == 0 ? $jGer['personalId'] : $jGer['jefeInmediato'];
                $personal->setPersonalId($jefeId);
                $cad['jefeMax'] = $personal->GetNameById();
                break;
        }
        return $cad;
    }
    public function GetExpedientes(){
	    $sql ="SELECT a.expedienteId,a.path,a.personalId,b.name,a.fecha from personalExpedientes a LEFT JOIN expedientes b ON a.expedienteId=b.expedienteId WHERE a.personalId='".$this->personalId."' and b.status='activo' ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
        foreach($result as $key=>$value){
            $file = DOC_ROOT."/expedientes/".$this->personalId."/".$value['path'];
            if(file_exists($file)&&is_file($file))
            {
                $ext =end(explode('.',$value['path']));
                $result[$key]['findFile'] = true;
                $result[$key]['ext'] = $ext;
            }
            else
                $result[$key]['findFile'] = false;
        }
        return $result;
    }
    public function findDeepJefes($personalId,&$jefes=array(),$me=false){
        global $rol;

        $this->setPersonalId($personalId);
        $info = $this->Info();
        if($me){
                $jefes['me'] = $info['name'];
        }
        if($info['jefeInmediato']>0) {
                $this->setPersonalId($info['jefeInmediato']);
                $info = $this->Info();
                $role = $rol->getInfoByData($info);
                $rolArray = explode(' ',$role['name']);
                $needle =trim($rolArray[0]);
                switch($needle){
                    case 'Sistemas':
                    case 'Gestoria':
                    case 'Supervisor':
                     $needle='Supervisor';
                     break;
                    case 'Asistente':
                    case 'Cuentas':
                    case 'cxc':
                    case 'Contador':
                    $needle='Contador';
                    break;
                    case 'Recepcion':
                    case 'Auxiliar':
                    $needle='Auxiliar';
                    break;
                    case 'Coordinador':
                    case 'Socio':
                        $needle='Socio';
                    break;
                }
                $jefes[$needle] = $this->GetNameById();
                $this->findDeepJefes($info['personalId'],$jefes);
            }else{
                $this->setPersonalId($info['jefeInmediato']);
                $info = $this->Info();
                $role = $rol->getInfoByData($info);
                $rolArray = explode(' ',$role['name']);
                $needle = trim($rolArray[0]);
                switch($needle){
                    case 'Sistemas':
                    case 'Gestoria':
                    case 'Supervisor':
                        $needle='Supervisor';
                        break;
                    case 'Asistente':
                    case 'Cuentas':
                    case 'Contador':
                        $needle='Contador';
                        break;
                    case 'Recepcion':
                    case 'Auxiliar':
                        $needle='Auxiliar';
                        break;
                    case 'Coordinador':
                    case 'Socio':
                        $needle='Socio';
                        break;
                }
                $per =   $this->GetNameById();
                if($per=='')
                   $jefes[$needle] ='Sin jefe';
                else
                    $jefes[$needle] =$per;
            }

    }
    public function changePassword(){
        $sendmail = new SendMail;
        $sql =  "SELECT * FROM personal WHERE active='1' ORDER BY personalId ASC ";
        $this->Util()->DB()->setQuery($sql);
        $results =  $this->Util()->DB()->GetResult();

        foreach($results as $key =>$item){
            //if($item['personalId']!='259')
              //  continue;
            $cadena ="";
            $cadena = $this->Util()->generateRandomString(6,true);
            $this->Util()->DB()->setQuery("UPDATE personal SET passwd='".$cadena."' WHERE personalId='".$item['personalId']."' ");
            $this->Util()->DB()->UpdateData();
            //if($this->Util()->DB()->UpdateData()){
                $this->Util()->DB()->setQuery("UPDATE personal SET lastChangePassword='".date('Y-m-d')."' WHERE personalId='".$item['personalId']."' ");
                $this->Util()->DB()->UpdateData();
                $body="ESTIMADO USUARIO CON EL FIN DE MANTENER LA SEGURIDAD DE SUS DATOS Y DE LOS CLIENTES QUE SE ENCUENTRAN EN LA PLATAFORMA BAJO SU RESPONSABILIDAD 
                SE HA REALIZADO EL CAMBIO DE CONTRASE&Ntilde;A DE SU CUENTA, CIERRE SU SESSION SI SE ENCUENTRA ACTUALMENTE EN LA PLATAFORMA E INGRESE NUEVAMENTE CON LOS SIGUIENTES DATOS:  <br>
                USUARIO:".$item['username']." <br>
                PASSWD:".$cadena." 
                <br><br>
                Este correo se creo automaticamente, favor de no responder.
                ";
                $subject="CAMBIO DE CONTRASEÑA ".$item['name'];
                $to = $item['email'];
                $toName= $item['name'];
                $sendmail->Prepare($subject, $body, $to, $toName, '', "", "", "",'noreply@braunhuerin.com.mx' , "ADMINISTRADOR DE PLATAFORMA") ;
            //}
        }
        $this->Util()->setError(0,"complete","Se han cambiado las contraseñas correctamente");
        $this->Util()->PrintErrors();
	    return  true;
    }
    public function unlinkExpendiente($expedienteId,$perId){
        $base_path = DOC_ROOT.'/expedientes';
        //almacenar el archivo en el servidor
        $dir_employe = $base_path.'/'.$perId;
        //comprobar si tiene un archivo actualmente y eliminarlo
        $sqlc ="SELECT path FROM personalExpedientes  WHERE personalId='".$perId."' AND expedienteId='".$expedienteId."'";
        $this->Util()->DB()->setQuery($sqlc);
        $nameFile = $this->Util()->DB()->GetSingle();
        $file = $dir_employe.'/'.$nameFile;
        if(file_exists($file)&&is_file($file)){
            if(unlink($file))
            {
                $sql ="UPDATE personalExpedientes SET path=null,fecha=null WHERE personalId='".$perId."' AND expedienteId='".$expedienteId."'";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
                $this->Util()->setError(0,'complete','Archivo eliminado correctamente');
                $this->Util()->PrintErrors();
                return true;
            }
            else{
                $this->Util()->setError(0,'error','Error al eliminar archivo');
                $this->Util()->PrintErrors();
                return false;

            }
        }
        else{
            $this->Util()->setError(0,'error','Error al encontrar archivo');
            $this->Util()->PrintErrors();
            return false;
        }
     }
    public function findSupervisor($id){
	    global $rol;
         $this->setPersonalId($id);
         $infP = $this->Info();
         $role = $rol->getInfoByData($infP);
         $rolArray = explode(' ',$role['name']);
         $needle = trim($rolArray[0]);
         $jefes = array();
         $this->findDeepJefes($id, $jefes,true);
         $supervisor="";
         switch($needle){
             case 'Coordinador':
             case 'Gestoria':
             case 'Sistemas':
             case 'Supervisor':
             case 'Gerente':
             case 'socio':
                 $supervisor = $jefes['me'];
                 break;
             case 'Recepcion':
             case 'Cuentas':
             case 'Contador':
             case 'Asistente':
             case 'Auxiliar':
                 $supervisor = $jefes['Supervisor'];
                 break;
         }
         return $supervisor;
     }
    public function GetIdResponsablesSubordinados($filtro=[]){
	    global $User;
        $idPersons= [];

        if($User['tipoPersonal'] == 'Admin' || $User['tipoPersonal'] == 'Socio' || $User['tipoPersonal'] == 'Coordinador'||$this->showAll){
            //Si seleccionaron TODOS
            if($filtro['responsableCuenta'] == 0){
                $this->setActive(1);
                $socios = $this->ListSocios();
                foreach($socios as $res){
                    array_push($idPersons,$res['personalId']);
                    $this->setPersonalId($res['personalId']);
                    $subordinados = $this->Subordinados();
                    if(empty($subordinados))
                        continue;

                    $subsLine = $this->Util()->ConvertToLineal($subordinados,'personalId');
                    $idPersons=array_merge($idPersons,$subsLine);
                    unset($subsLine);
                    unset($subordinados);
                }//foreac
                $idPersons = array_unique($idPersons);
            }else{
                $idPersons = array();
                $respCuenta = $filtro['responsableCuenta'];
                array_push($idPersons,$respCuenta);
                if($filtro['deep']){
                    $this->setPersonalId($respCuenta);
                    $subordinados = $this->Subordinados();
                    if(!empty($subordinados)){
                        $subsLine = $this->Util()->ConvertToLineal($subordinados,'personalId');
                        $idPersons=array_merge($idPersons,$subsLine);
                        unset($subsLine);
                        unset($subordinados);
                    }
                }
            }

        }else{
            $idPersons = array();
            if($filtro['responsableCuenta']==0)
                $respCuenta = $User['userId'];
            else
                $respCuenta = $filtro['responsableCuenta'];
            array_push($idPersons,$respCuenta);
            if($filtro['deep']){
                $this->setPersonalId($respCuenta);
                $subordinados = $this->Subordinados();
                if(!empty($subordinados)){
                    $subsLine = $this->Util()->ConvertToLineal($subordinados,'personalId');
                    $idPersons=array_merge($idPersons,$subsLine);
                    unset($subsLine);
                    unset($subordinados);
                }
            }
        }
        return $idPersons;
    }
    public function getCurrentUser(){
        $this->Util()->DB()->setQuery('SELECT * FROM personal WHERE personalId="'.$_SESSION['User']['userId'].'" ');
        $row= $this->Util()->DB()->GetRow();
        if($_SESSION['User']['tipoPers']=='Admin'){
            $row['name'] = "Administrador de sistema";
        }
        return $row;
    }
    
}

?>