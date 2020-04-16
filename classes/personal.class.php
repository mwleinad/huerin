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
    private $levelRol;
    public function setLevelRol($value)
    {
            $this->levelRol = $value;
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
	    if(strlen($value)>0)
	        $this->Util()->ValidateMail($value,"Correo Electrónico");
		$this->email = $value;
	}

	public function setUsername($value)
	{
		$this->username = $value;
	}
	public function setPasswd($value)
	{
        //$this->Util()->ValidateRequireField($value, "Contraseña");
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
		global $User;
		
        if ($this->active)
            $sqlActive = " AND a.active = '1'";
        if ($this->levelRol && $this->showAll)
            $sqlFilter = " and d.nivel='" . $this->levelRol . "' ";
        
        if ((int)$User['level'] == 1 || stripos($User['tipoPersonal'], 'DH') !== false || $this->showAll || stripos($User['tipoPersonal'], 'Sistema') !== false) {
           $sql = "SELECT a.*,b.name as nombreJefe,c.departamento
					FROM personal a 
					LEFT JOIN personal b ON a.jefeInmediato=b.personalId 
					LEFT JOIN roles d on a.roleId=d.rolId
					LEFT JOIN departamentos c ON a.departamentoId=c.departamentoId WHERE 1
					$sqlFilter $sqlActive 
					ORDER BY a.name ASC";
            $this->Util()->DB()->setQuery($sql);
            $result = $this->Util()->DB()->GetResult();
            return $result;

		}
	
        $this->setPersonalId($User['userId']);
		$result = $this->SubordinadosDetails();
		foreach($result as $key => $var){
			$this->Util()->DB()->setQuery("select departamento from departamentos where departamentoId = '".$var["departamentoId"]."' ");
			$result[$key]["departamento"] = $this->Util()->DB()->GetSingle()?$this->Util()->DB()->GetSingle():"";
			$result[$key]["nombreJefe"] = $var["jefeName"];
		}
		$result = $this->Util()->orderMultiDimensionalArray($result,'name');
        return $result;
    }
    public function suggestPersonal($like){
	    $ftr = "";
        if (strlen($like) > 1) {
            $ftr .= " AND name LIKE '%$like%' ";
        }
        else return false;
        $sql = "SELECT * 
				FROM personal
			    WHERE 1
			    $ftr
				ORDER BY name ASC LIMIT 15";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        return $result;
    }
    public function EnumerateGerenteDepartamento($dep)
    {
        $this->Util()->DB()->setQuery("select departamentoId from departamentos where lower(departamento)='".strtolower($dep)."' ");
        $depId = $this->Util()->DB()->GetSingle();
        if(!$depId)
            return [];

        $this->Util()->DB()->setQuery("select personalId from personal inner join roles on personal.roleId=roles.rolId where personal.departamentoId='".$depId."' and nivel=2");
        $perId = $this->Util()->DB()->GetSingle();
        if(!$perId)
            return [];

        $this->setPersonalId($perId);
        $result = $this->SubordinadosDetailsAddPass();
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
		$filtro = "";
		$filtro .= (int)$_SESSION["User"]["level"] != 1 ? 
                    " where departamentoId = '".$_SESSION["User"]["departamentoId"]."' " 
                    : "";
		$sql = "SELECT
					*
				FROM
					departamentos
					$filtro
				ORDER BY
					departamento
				ASC ";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

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
				ORDER BY name ASC";

		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

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
	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM personal WHERE personalId = '".$this->personalId."'");
		$row = $this->Util()->DB()->GetRow();
		$row['fechaIngresoMysql'] = $this->Util()->FormatDateMySql($row['fechaIngreso']);
		return $row;
	}
    public function InfoWhitRol()
    {
        $this->Util()->DB()->setQuery("SELECT a.personalId,a.name,a.roleId,b.name as nameRol,b.nivel,a.sueldo,a.jefeInmediato, 
                                             CASE b.nivel
                                             WHEN 1 THEN 'Socio'
                                             WHEN 2 THEN 'Gerente'
											 WHEN 3 THEN 'Subgerente'
                                             WHEN 4 THEN 'Supervisor'
                                             WHEN 5 THEN 'Contador'
                                             WHEN 6 THEN 'Auxiliar'
                                             WHEN 100 THEN 'Auxiliar' END AS nameLevel
                                             FROM personal a INNER JOIN roles b ON a.roleId=b.rolId WHERE a.personalId = '".$this->personalId."'");
        $row = $this->Util()->DB()->GetRow();
        $this->Util()->DB()->setQuery("select name from personal where personalId='".$row['jefeInmediato']."' ");
        $row["nameJefeInmediato"] = $this->Util()->DB()->GetSingle();
        $this->Util()->DB()->setQuery("select porcentaje from porcentajesBonos where categoria='".$row['nivel']."' ");
        $row["porcentajeBono"] = $this->Util()->DB()->GetSingle();
        return $row;
    }
	public function jefeInmediato(){
        $this->Util()->DB()->setQuery("SELECT j.* FROM personal a INNER JOIN personal j ON a.jefeInmediato=j.personalId WHERE a.personalId = '".$this->personalId."'");
        $row = $this->Util()->DB()->GetRow();
        $row['fechaIngresoMysql'] = $this->Util()->FormatDateMySql($row['fechaIngreso']);
        $this->Util()->DB()->setQuery("select name from personal where personalId='".$row['jefeInmediato']."' ");
        $row["nameJefeInmediato"] = $this->Util()->DB()->GetSingle();
        return $row;
    }

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }
		$strUpdate ="";
		if(strlen($this->sueldo)>0)
        {
            if(!is_numeric($this->sueldo))
                $this->sueldo=0;
            $strUpdate .= " sueldo = '".$this->sueldo."', ";
        }
        if(strlen($this->phone)>0)
            $strUpdate .=" phone='".$this->phone."', ";
        if(strlen($this->email)>0)
            $strUpdate .=" email='".$this->email."', ";
        if(strlen($this->username)>0)
            $strUpdate .=" username='".$this->username."', ";
        if(strlen($this->passwd)>0)
            $strUpdate .=" passwd='".$this->passwd."', ";
        if(strlen($this->ext)>0)
            $strUpdate .="ext='".$this->ext."', ";
        if(strlen($this->celphone)>0)
            $strUpdate .=" celphone='".$this->celphone."', ";
        if(strlen($this->aspel)>0)
            $strUpdate .=" aspel='".$this->aspel."', ";
        if(strlen($this->skype)>0)
            $strUpdate .=" skype='".$this->skype."', ";
        if(strlen($this->horario)>0)
            $strUpdate .=" horario='".$this->horario."', ";
        if(strlen($this->fechaIngreso)>0)
            $strUpdate .=" fechaIngreso='".$this->fechaIngreso."', ";
        if(strlen($this->computadora)>0)
            $strUpdate .=" computadora='".$this->computadora."', ";
        if(strlen($this->grupo)>0)
            $strUpdate .=" grupo='".$this->grupo."', ";
        if(strlen($this->tipoPersonal)>0)
            $strUpdate .=" tipoPersonal='".$this->tipoPersonal."', ";
        if(strlen($this->roleId)>0)
            $strUpdate .=" roleId='".$this->roleId."', ";
        if(strlen($this->departamentoId)>0)
            $strUpdate .=" departamentoId='".$this->departamentoId."', ";
        if(strlen($this->jefeInmediato)>0)
            $strUpdate .=" jefeInmediato='".$this->jefeInmediato."', ";

		$this->Util()->DB()->setQuery("
			UPDATE
				personal
			SET
				`name` = '".$this->name."',
				$strUpdate
				active = '".$this->active."'
			WHERE personalId = '".$this->personalId."'");
		$this->Util()->DB()->UpdateData();
        //actualizar los expedientes.
        if(isset($_POST["expe"])){
            if(!empty($_POST['expe'])){
                $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("SELECT expedienteId from personalExpedientes WHERE personalId='".$this->personalId."' ");
                $arrayExp = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
                $expActual = $this->Util()->ConvertToLineal($arrayExp,'expedienteId');

                foreach($_POST['expe'] as $exp){
                    //si ya tiene archivo adjunto no debe reemplazar
                    $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("select expedienteId from personalExpedientes where expedienteId='$exp' and personalId='".$this->personalId."' ");
                    $expExist = $this->Util()->DBSelect($_SESSION['empresaId'])->GetSingle();
                    $key = array_search($exp,$expActual);
                    unset($expActual[$key]);
                    if($expExist)
                        continue;
                    $sql =  "INSERT INTO personalExpedientes(personalId,expedienteId)VALUES(".$this->personalId.",$exp)";
                    $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
                    $this->Util()->DBSelect($_SESSION['empresaId'])->InsertData();
                }

                if(!empty($expActual)){
                    //eliminar los expedientes que se deseleccionaron incluyendo su archivo si tiene
                    foreach ($expActual as $expA){
                        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("select path from personalExpedientes where expedienteId='$expA' and personalId='".$this->personalId."' ");
                        $nameFile= $this->Util()->DBSelect($_SESSION['empresaId'])->GetSingle();
                        $file = DOC_ROOT."/expedientes/".$this->personalId."/".$nameFile;
                        $sqlu = "DELETE FROM personalExpedientes WHERE expedienteId='$expA'AND personalId='".$this->personalId."'";
                        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sqlu);
                        $this->Util()->DBSelect($_SESSION['empresaId'])->DeleteData();
                        if(file_exists($file)&&is_file($file)){
                            unlink($file);
                        }
                    }

                }
            }
        }
		$this->Util()->setError(10049, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		if($this->Util()->PrintErrors()){ return false; }

        if(!is_numeric($this->sueldo))
            $this->sueldo=0;

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
		if(isset($_POST["expe"])){
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
function AddPassToArray()
{
    $sql ="SELECT personal.*, jefes.name AS jefeName FROM personal
    LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato WHERE personal.personalId = '".$this->personalId."'ORDER BY name ASC";
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
function SubordinadosDetailsAddPass()
{
    $sql ="SELECT personal.*, jefes.name AS jefeName FROM personal
       LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult($sql);

    $jerarquia = $this->Jerarquia($result, $this->personalId);

    $_SESSION["lineal"] = array();
    $this->JerarquiaLineal($jerarquia);
    $this->AddPassToArray();

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
		    $_SESSION["lineal"][] = $value;
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
    function deepJefesArray(&$jefes = [], $me = false)
    {
        global $rol;
        $employe = $this->InfoWhitRol();
        if ($me)
            $jefes['me'] = $employe['name'];

        if ($employe["jefeInmediato"]) {
            $this->setPersonalId($employe['jefeInmediato']);
            $inmediato = $this->InfoWhitRol();
            $jefes[$inmediato["nameLevel"]] = $inmediato["name"];
            $this->setPersonalId($inmediato["personalId"]);
            $this->deepJefesArray($jefes);
        }
	}
	function deepJefesAssoc(&$jefes = [], $me = false)
    {
        global $rol;
        $employe = $this->InfoWhitRol();
        if ($me)
            $jefes['me'] = $employe;

        if ($employe["jefeInmediato"]) {
            $this->setPersonalId($employe['jefeInmediato']);
            $inmediato = $this->InfoWhitRol();
            $jefes[$inmediato["nameLevel"]] = $inmediato;
            $this->setPersonalId($inmediato["personalId"]);
            $this->deepJefesAssoc($jefes);
        }
    }
    public function changePassword()
    {
        $sendmail = new SendMail;
        $sql =  "SELECT * FROM personal WHERE active='1' ORDER BY personalId ASC ";
        $this->Util()->DB()->setQuery($sql);
        $results =  $this->Util()->DB()->GetResult();

        foreach ($results as $key => $item) {
            //if($item['personalId']!='259')
            //  continue;
            $cadena = "";
            $cadena = $this->Util()->generateRandomString(6, true);
            $this->Util()->DB()->setQuery("UPDATE personal SET passwd='" . $cadena . "' WHERE personalId='" . $item['personalId'] . "' ");
            $this->Util()->DB()->UpdateData();
            //if($this->Util()->DB()->UpdateData()){
            $this->Util()->DB()->setQuery("UPDATE personal SET lastChangePassword='" . date('Y-m-d') . "' WHERE personalId='" . $item['personalId'] . "' ");
            $this->Util()->DB()->UpdateData();
            $body = "ESTIMADO USUARIO CON EL FIN DE MANTENER LA SEGURIDAD DE SUS DATOS Y DE LOS CLIENTES QUE SE ENCUENTRAN EN LA PLATAFORMA BAJO SU RESPONSABILIDAD 
                SE HA REALIZADO EL CAMBIO DE CONTRASE&Ntilde;A DE SU CUENTA, CIERRE SU SESSION SI SE ENCUENTRA ACTUALMENTE EN LA PLATAFORMA E INGRESE NUEVAMENTE CON LOS SIGUIENTES DATOS:  <br>
                USUARIO:" . $item['username'] . " <br>
                PASSWD:" . $cadena . " 
                <br><br>
                Este correo se creo automaticamente, favor de no responder.
                ";
            $subject = "CAMBIO DE CONTRASEÑA " . $item['name'];
            $to = $item['email'];
            $toName = $item['name'];
            $sendmail->Prepare($subject, $body, $to, $toName, '', "", "", "", 'noreply@braunhuerin.com.mx', "ADMINISTRADOR DE PLATAFORMA");
            //}
        }
        $this->Util()->setError(0, "complete", "Se han cambiado las contraseñas correctamente");
        $this->Util()->PrintErrors();
        return  true;
    }
    public function unlinkExpendiente($expedienteId, $perId)
    {
        $base_path = DOC_ROOT . '/expedientes';
        //almacenar el archivo en el servidor
        $dir_employe = $base_path . '/' . $perId;
        //comprobar si tiene un archivo actualmente y eliminarlo
        $sqlc = "SELECT path FROM personalExpedientes  WHERE personalId='" . $perId . "' AND expedienteId='" . $expedienteId . "'";
        $this->Util()->DB()->setQuery($sqlc);
        $nameFile = $this->Util()->DB()->GetSingle();
        $file = $dir_employe . '/' . $nameFile;
        if (file_exists($file) && is_file($file)) {
            if (unlink($file)) {
                $sql = "UPDATE personalExpedientes SET path=null,fecha=null WHERE personalId='" . $perId . "' AND expedienteId='" . $expedienteId . "'";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
                $this->Util()->setError(0, 'complete', 'Archivo eliminado correctamente');
                $this->Util()->PrintErrors();
                return true;
            } else {
                $this->Util()->setError(0, 'error', 'Error al eliminar archivo');
                $this->Util()->PrintErrors();
                return false;
            }
        } else {
            $this->Util()->setError(0, 'error', 'Error al encontrar archivo');
            $this->Util()->PrintErrors();
            return false;
        }
    }
    public function findSupervisor($id)
    {
        global $rol;
        $this->setPersonalId($id);
        $infP = $this->InfoWhitRol();
        $jefes = [];
        $this->deepJefesArray($jefes, true);
        $supervisor = "";
        switch ($infP["nameLevel"]) {
            case 'Contador':
            case 'Auxiliar':
                $supervisor = $jefes['Supervisor'];
			break;
			default:
				$supervisor = $jefes['me'];
			break;
        }
        return $supervisor;
    }
    public function getOrdenJefes()
    {
        $ordenJefes = [];
        $this->setPersonalId($this->personalId);
        $infP = $this->InfoWhitRol();
        $needle = strtolower($infP["nameLevel"]);
        if (!empty($infP)) {
            $jefes = array();
            $this->deepJefesArray($jefes, true);
            $ordenJefes['contador'] = $jefes['Contador'];
            $ordenJefes['supervisor'] = $jefes['Supervisor'];
            $ordenJefes['subgerente'] = $jefes['Subgerente'];
            $ordenJefes['gerente'] = $jefes['Gerente'];
            $ordenJefes['jefeMax'] = $jefes['Socio'];
            $ordenJefes[$needle] = $jefes['me'];
        } else {
            $ordenJefes['auxiliar'] = 'N/E';
            $ordenJefes['contador'] = 'N/E';
            $ordenJefes['supervisor'] = 'N/E';
            $ordenJefes['subgerente'] = 'N/E';
            $ordenJefes['gerente'] = 'N/E';
        }
        return $ordenJefes;
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
    public function GetIdResponsablesSubordinados($filtro = [])
    {
        global $User;
        $idPersons = [];
        if ( (int)$User["level"] == 1|| $this->showAll) {
            if ($filtro['responsableCuenta'] == 0) {
                $this->setActive(1);
                $socios = $this->ListSocios();
                foreach ($socios as $res) {
                    array_push($idPersons, $res['personalId']);
                    $this->setPersonalId($res['personalId']);
                    $subordinados = $this->Subordinados();
                    if (empty($subordinados))
                        continue;

                    $subsLine = $this->Util()->ConvertToLineal($subordinados, 'personalId');
                    $idPersons = array_merge($idPersons, $subsLine);
                    unset($subsLine);
                    unset($subordinados);
                } //foreac
                $idPersons = array_unique($idPersons);
            } else {
                $idPersons = array();
                $respCuenta = $filtro['responsableCuenta'];
                array_push($idPersons, $respCuenta);
                if ($filtro['deep']) {
                    $this->setPersonalId($respCuenta);
                    $subordinados = $this->Subordinados();
                    if (!empty($subordinados)) {
                        $subsLine = $this->Util()->ConvertToLineal($subordinados, 'personalId');
                        $idPersons = array_merge($idPersons, $subsLine);
                        unset($subsLine);
                        unset($subordinados);
                    }
                }
            }
        } else {
            $idPersons = array();
            if ($filtro['responsableCuenta'] == 0)
                $respCuenta = $User['userId'];
            else
                $respCuenta = $filtro['responsableCuenta'];
            array_push($idPersons, $respCuenta);
            if ($filtro['deep']) {
                $this->setPersonalId($respCuenta);
                $subordinados = $this->Subordinados();
                if (!empty($subordinados)) {
                    $subsLine = $this->Util()->ConvertToLineal($subordinados, 'personalId');
                    $idPersons = array_merge($idPersons, $subsLine);
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
            $row['personalId'] = 999990000;
        }
        return $row;
    }
    /*
     * funcion getListPersonalByDepartamento retorna informacion de personas dado un departamento que se pasa mediante el metodo setDepartamentoId() del objeto persona.
     * @field parametro que definira que se retornara como clave valor
     * - email: retorta como clave el correo y valor el nombre de la persona, solo si el correo es valido
     * -default: retorna un array associativo de todos los campos de una persona
     */
    public function getListPersonalByDepartamento($field="")
    {
        $return = [];
        $this->Util()->DB()->setQuery("SELECT * FROM personal WHERE departamentoId='".$this->departamentoId."' ORDER BY name asc ");
        $empleados = $this->Util()->DB()->GetResult();
        switch($field){
            case 'email':
                foreach($empleados as $key=>$value){
                    if($this->Util()->ValidateEmail($value["email"]))
                        $return[$value["email"]] = $value["name"];
                }
            break;
            default:
                $return = $empleados;
            break;
        }
        return $return;
    }
    function getTotalSalarioByMultipleId($id=[]){
        if(empty($id))
            return 0;

        $this->Util()->DB()->setQuery("select sum(sueldo) from personal where personalId IN (".implode(',',$id).") ");
        return $this->Util()->DB()->GetSingle();
    }
    function GenerateReportExpediente()
    {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("select expedienteId,name from expedientes where status='activo' order by name ASC ");
        $expedientes = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
        $baseExp = [];
        foreach ($expedientes as $exp) {
            $cad["background"] = "#EFEFEF"; //iniciar class para todos expedientes como no aplica
            $cad["name"] = $exp["name"];
            $cad["fecha"] = "";
            $baseExp[$exp["expedienteId"]] = $cad;
        }
        $filter = "";
        if ($this->personalId)
            $filter .= " and personalId = '" . $this->personalId . "' ";

        $sql = "select personalId,name from personal where 1 $filter order by name asc";
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql);
        $employes = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
        foreach ($employes as $key => $value) {
            $complete = 0;
            $baseExpOwn = $baseExp;
            $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("select expedienteId,path,fecha from personalExpedientes where personalId = '" . $value["personalId"] . "' ");
            $ownFiles = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
            $countOwnFiles =  count($ownFiles);

            foreach ($ownFiles as $ownFile) {
                $file = DOC_ROOT . "/expedientes/" . $value["personalId"] . "/" . $ownFile["path"];
                if (file_exists($file) && is_file($file)) {
                    $baseExpOwn[$ownFile["expedienteId"]]["background"] = "#009900";
                    $baseExpOwn[$ownFile["expedienteId"]]["fecha"] = $ownFile["fecha"];
                    $complete++;
                } else {
                    if (array_key_exists($ownFile["expedienteId"], $baseExpOwn))
                        $baseExpOwn[$ownFile["expedienteId"]]["background"] = "#F00";
                }
            }
            switch ($_POST["status"]) {
                case 'incomplete':
                    if ($complete < $countOwnFiles)
                        $employes[$key]["ownExpedientes"] = $baseExpOwn;
                    else
                        unset($employes[$key]);
                    break;
                case 'complete':
                    if ($complete == $countOwnFiles && $countOwnFiles > 0)
                        $employes[$key]["ownExpedientes"] = $baseExpOwn;
                    else
                        unset($employes[$key]);
                    break;
                default:
                    if ($countOwnFiles > 0)
                        $employes[$key]["ownExpedientes"] = $baseExpOwn;
                    else
                        $employes[$key]["ownExpedientes"] = [];

                    break;
            }
        }
        if (!is_array($employes))
            $employes = [];

        $data["employes"] = $employes;
        $data["expedientes"] = $expedientes;
        return $data;
    }
}