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
	function Info($isRep=false)
	{
		if(!$this->userId)
			$this->userId = $_SESSION["User"]["userId"];

		if($_SESSION["User"]["roleId"] == '4'){
			$sql = "SELECT * FROM customer WHERE customerId = '".$this->userId."'";
			$this->Util()->DB()->setQuery($sql);
			$row = $this->Util()->DB()->GetRow();

			$row["tipoPersonal"] = "Cliente";
            $row["roleId"] =4;
			$row["name"] = $row["nameContact"];
		}
		elseif($_SESSION["User"]["isRoot"]&&!$isRep)
		{
		    $sql = "SELECT * FROM user WHERE userId = '1'";
			$this->Util()->DB()->setQuery($sql);
			$row = $this->Util()->DB()->GetRow();
            $row["tipoPersonal"] = "Admin";

		}else{
            $sql = "SELECT * FROM personal WHERE personalId = '".$this->userId."'";
            $this->Util()->DB()->setQuery($sql);
            $row = $this->Util()->DB()->GetRow();
		}
		$row["version"] = "v3";
		return $row;
	}


	public function allowAccess($page = ''){
		$User = $_SESSION['User'];
		if(!$User['isLogged']){
			header('Location: '.WEB_ROOT.'/login');
			exit();
		}

		if($page != ''&&!$User['isRoot']){
			if(!$this->allow_access_module($page)){
				header('Location: '.WEB_ROOT);
				exit();
			}
		}
	}

	public function allow_access_module($page){
		global $rol;
		$User = $_SESSION['User'];

        $allowPages = $rol->GetPermisosByRol();
		if(in_array($page,$allowPages))
			return true;
		else
			return false;

	}

	public function doLogin(){

		if($this->Util()->PrintErrors()){
			return false;
		}
		if (isset($_SESSION['User'])) {
			echo "existe session";
			$this->doLogout();
		}

		$sqlQuery = "SELECT * 
		   			 FROM user
					 WHERE username = '".$this->username."'
					 AND passwd = '".md5($this->password)."'
					 AND active = '1' ";
		$this->Util()->DB()->setQuery($sqlQuery);
		$row = $this->Util()->DB()->GetRow();
		if($row){
			$card['userId'] = 999990000;
			$card['roleId'] = 1;
            $card['level'] = 1;
			$card['username'] = $row['name'];
			$card['isLogged'] = true;
            $card['isRoot'] = true;
			$card['allow_visualize_any_contract'] = true;
			$card['allow_any_employee'] = true;
			$card['allow_any_departament'] = true;
            $card['allow_visualize_any_rol'] = true;

			if($row['type'] == 1)
				$card['tipoPers'] = 'Admin';
			$_SESSION['User'] = $card;
			$_SESSION["empresaId"] = IDEMPRESA;
			return true;

		}else{
            $sql = "SELECT a.*,b.nivel, b.allow_visualize_any_contract, b.allow_any_employee, b.allow_any_departament
		   			 FROM personal a
		   			 LEFT JOIN roles b ON a.roleId=b.rolId
					 WHERE a.username = '".$this->username."'
					 AND a.passwd = '".$this->password."'
					 AND a.active = '1' ";
			$this->Util()->DB()->setQuery($sql);
			$row = $this->Util()->DB()->GetRow();
			if($row){
				$card['userId'] = $row['personalId'];
				$card['allow_visualize_any_contract'] = $row['allow_visualize_any_contract'] === '1' ?  true : false;
				$card['allow_any_employee'] = $row['allow_any_employee'] === '1' ?  true : false;
				$card['allow_any_departament'] = $row['allow_any_departament'] === '1' ?  true : false;
                $card['allow_visualize_any_rol'] = $row['allow_visualize_any_rol'] === '1' ?  true : false;
				$card['roleId'] = $row["roleId"];
                $card['level'] = $row["nivel"];
				$card['username'] = $row['username'];
				$card['departamentoId'] = $row['departamentoId'];

				$moreDep = [(int)$row['departamentoId']];
				if ($row['departamentoId'] == 8 )
					array_push($moreDep,24 );
				if ($row['departamentoId'] == 24 )
					array_push($moreDep,8 );

				$card['moreDepartament'] =$moreDep;
				$card['isLogged'] = true;
				$card['tipoPers'] = $row['tipoPersonal'];
				$_SESSION['User'] = $card;
                $_SESSION["empresaId"] = IDEMPRESA;
				return true;
			}else{
				$sql = "SELECT  *
				   FROM customer
				   WHERE email = '".$this->username."'
				   AND 	password = '".$this->password."'
				   AND  active = '1' ";
				$this->Util()->DB()->setQuery($sql);
				$row = $this->Util()->DB()->GetRow();
				if($row){
					$card['userId'] = $row['customerId'];
					$card['roleId'] = 4;
					$card['level'] = 100;
					$card['username'] = $row['nameContact'];
					$card['isLogged'] = true;
					$_SESSION['User'] = $card;
                    $_SESSION["empresaId"] = IDEMPRESA;
					return true;
				}else{
					unset($_SESSION["User"]);
                    unset($_SESSION["empresaId"]);
					$this->Util()->setError(10006, "error", "");
					$this->Util()->PrintErrors();
				}//else
			}//else
		}//else
        unset($_SESSION["User"]);
        unset($_SESSION["empresaId"]);
		return false;
	}//doLogin
	public function doLogout(){
		$_SESSION['User'] = '';
		unset($_SESSION['User']);
        unset($_SESSION['empresaId']);
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
		$result["cp"] = $data["cp"]; // cp dinamico
		$result["estado"] = $data["estadoAddress"];
		$result["localidad"] = $data["nameFacturacion"];
		$result["referencia"] = $data["nameFacturacion"];
		$result["pais"] = $data["paisAddress"];
		$result["email"] = $data["emailContactoAdministrativo"];
		$result["email2"] = $data["email"];
		$result["rfc"] = $data["rfcFacturacion"];
		$result["cxcSaldoFavor"] = $data["cxcSaldoFavor"];
		$result["userId"] = $data['contractId'];
		$result["customerId"] = $data["customerId"];

		return $result;
	}
	function GetUserForInvoice()
	{
		$myContract = new Contract;
		$myContract->setContractId($this->userId);
		$data = $myContract->Info();

		$result["nombre"] = $data["nameFacturacion"];// nombre dinamico
		$result["calle"] = $data["address"];;
		$result["noExt"] = $data["noExtAddress"];
		$result["noInt"] = $data["noIntAddress"];
		$result["colonia"] = $data["coloniaAddress"];
		$result["municipio"] = $data["municipioAddress"];
		$result["cp"] = $data["cpFacturacion"]; // cp dinamico
		$result["estado"] = $data["estadoAddress"];
		$result["localidad"] = $data["nameFacturacion"];
		$result["referencia"] = $data["nameFacturacion"];
		$result["pais"] = $data["paisAddress"];
		$result["email"] = $data["emailContactoAdministrativo"];
		$result["email2"] = $data["email"];
		$result["rfc"] = $data["rfcFacturacion"]; // rfcDinamico;
		$result["cxcSaldoFavor"] = $data["cxcSaldoFavor"];
		$result["userId"] = $data['idFacturacion']; // id dinamico
		$result["customerId"] = $data["customerId"];

		return $result;
	}

}

?>
