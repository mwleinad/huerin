<?php

class User extends Sucursal
{
	protected $userId = NULL;
	protected $password;

	public function setUserId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->userId = $value;
	}

	public function setUsername($value)
	{
		if($this->Util()->ValidateRequireField($value, 'Usuario'))
			$this->username = $value;
	}

	public function setPassword($value)
	{
		if($this->Util()->ValidateRequireField($value, 'Contrase&ntilde;a'))
			$this->password = $value;
	}

	//private functions
	function Info()
	{
		if(!$this->userId)
			$this->userId = $_SESSION["User"]["userId"];

		if($_SESSION["User"]["roleId"] == 4){
			$sql = "SELECT * FROM customer WHERE customerId = '".$this->userId."'";
			$this->Util()->DB()->setQuery($sql);
			$row = $this->Util()->DB()->GetRow();

			$row["tipoPersonal"] = "Cliente";
			$row["name"] = $row["nameContact"];
		}
		else
		{
			$sql = "SELECT * FROM personal WHERE personalId = '".$this->userId."'";
			$this->Util()->DB()->setQuery($sql);
			$row = $this->Util()->DB()->GetRow();
		}

		$row["version"] = "v3";

		return $row;
	}


	public function allowAccess($page = ''){
		$User = $_SESSION['User'];
		//print_r($_SESSION);
		//$infoUser = $this->Info();
		//print_r($infoUser);
		//exit;
		switch($infoUser["tipoPersonal"])
		{
			case "Socio": $User['roleId'] = 1; break;
			case "Asistente": $User['roleId'] = 1; break;
			case "Gerente": $User['roleId'] = 2; break;
			case "Supervisor": $User['roleId'] = 3; break;
			case "Contador": $User['roleId'] = 3; break;
			case "Auxiliar": $User['roleId'] = 3; break;
		}

		if(!$User['isLogged']){
			header('Location: '.WEB_ROOT.'/login');
			exit;
		}

		if($User['roleId'] != 1 && $page != ''){
			if(!$this->allow_access_module($page)){
				header('Location: '.WEB_ROOT);
				exit;
			}
		}
	}

	public function allow_access_module($page){
		$User = $_SESSION['User'];

		$allowPages = array('');
		if($User['roleId'] == 3)
			$allowPages = array('customer','contract','servicios', 'contract-view');

		if($User['roleId'] == 1)
			$allowPages = array('report-cxc');


		if(in_array($page,$allowPages) || $User['roleId'] == 2)
			return true;
		else
			return false;

	}

	public function doLogin(){

		if($this->Util()->PrintErrors()){
			return false;
		}

		$sqlQuery = "SELECT
				*
		   FROM
				user
			WHERE
				username = '".$this->username."'
			AND
				passwd = '".md5($this->password)."'
			AND
				active = '1'
		";
		$this->Util()->DB()->setQuery($sqlQuery);
		$row = $this->Util()->DB()->GetRow();

		if($row){

			$card['userId'] = 999990000;
			$card['roleId'] = $row['type'];
			$card['username'] = $row['name'];
			$card['isLogged'] = true;
            $card['isRoot'] = true;

			if($row['type'] == 1)
				$card['tipoPers'] = 'Socio';

			$_SESSION['User'] = $card;

			return true;

		}else{

			//Personal de Roqueni

			$sql = "SELECT
					*
			   FROM
					personal
				WHERE
					username = '".$this->username."'
				AND
					passwd = '".$this->password."'
				AND
					active = '1'
			";
			$this->Util()->DB()->setQuery($sql);
			$row = $this->Util()->DB()->GetRow();

			if($row){

				$card['userId'] = $row['personalId'];
				$card['roleId'] = 2;
				$card['username'] = $row['username'];
				$card['departamentoId'] = $row['departamentoId'];
				$card['isLogged'] = true;
				$card['tipoPers'] = $row['tipoPersonal'];
				
				$_SESSION['User'] = $card;
				return true;

			}else{

				//Usuarios Wallmart

				$sql = "SELECT
						*
				   FROM
						customer
					WHERE
						email = '".$this->username."'
					AND
						password = '".$this->password."'
					AND
						active = '1'
				";
				$this->Util()->DB()->setQuery($sql);
				$row = $this->Util()->DB()->GetRow();

				if($row){

					$card['userId'] = $row['customerId'];
					$card['roleId'] = 4;
					$card['username'] = $row['nameContact'];
					$card['isLogged'] = true;

					$_SESSION['User'] = $card;

					return true;

				}else{

					$this->Util()->setError(10006, "error", "");
					$this->Util()->PrintErrors();

				}//else

			}//else

		}//else

		return false;

	}//doLogin

	public function doLogout(){

		$_SESSION['User'] = '';
		unset($_SESSION['User']);
		session_destroy();

	}//doLogout

	function GetUserInfo()
	{
		$myContract = new Contract;
		$myContract->setContractId($this->userId);
		$data = $myContract->Info();

		$result["nombre"] = $data["name"];
		$result["calle"] = $data["address"];;
		$result["noExt"] = $data["noExtAddress"];
		$result["noInt"] = $data["noIntAddress"];
		$result["colonia"] = $data["coloniaAddress"];
		$result["municipio"] = $data["municipioAddress"];
		$result["cp"] = $data["cpAddress"];
		$result["estado"] = $data["estadoAddress"];
		$result["localidad"] = $data["name"];
		$result["referencia"] = $data["name"];
		$result["pais"] = $data["paisAddress"];
		$result["email"] = $data["emailContactoAdministrativo"];
		$result["email2"] = $data["email"];
		$result["rfc"] = $data["rfc"];
		$result["cxcSaldoFavor"] = $data["cxcSaldoFavor"];
		$result["userId"] = $this->userId;
		$result["customerId"] = $data["customerId"];

		return $result;
	}

}

?>
