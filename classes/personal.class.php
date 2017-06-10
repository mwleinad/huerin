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

	public function setExt($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Extension");
		$this->ext = $value;
	}

	private $celphone;
	public function setCelphone($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Celular");
		$this->celphone = $value;
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
			$sqlActive = " AND active = '1'";

		if ($infoUser['tipoPersonal'] == "Socio" || $infoUser['tipoPersonal'] == "Asistente") {
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
				WHERE tipoPersonal = 'Socio'
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

/*				jefeContador = '".$this->jefeContador."',
				jefeSupervisor = '".$this->jefeSupervisor."',
				jefeGerente = '".$this->jefeGerente."',
				jefeSocio = '".$this->jefeSocio."',
*/
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
				tipoPersonal = '".$this->tipoPersonal."',
				departamentoId = '".$this->departamentoId."',
				fechaIngreso = '".$this->fechaIngreso."',
				active = '".$this->active."'
			WHERE personalId = '".$this->personalId."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(10049, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		if($this->Util()->PrintErrors()){ return false; }

/*				jefeContador,
				jefeSupervisor,
				jefeGerente,
				jefeSocio,
								'".$this->jefeContador."',
				'".$this->jefeSupervisor."',
				'".$this->jefeGerente."',
				'".$this->jefeSocio."',

*/
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
				departamentoId,
				fechaIngreso,
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
				'".$this->tipoPersonal."',
				'".$this->departamentoId."',
				'".$this->fechaIngreso."',
				'".$this->active."'
		);");
		$this->Util()->DB()->InsertData();
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
	
function Subordinados()
{   
	$sql ="SELECT personal.*, jefes.name AS jefeName FROM personal
		LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
	$this->Util()->DB()->setQuery($sql);
	$result = $this->Util()->DB()->GetResult($sql);
	
	$jerarquia = $this->Jerarquia($result, $this->personalId);
	
	$_SESSION["lineal"] = array();
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

}

?>