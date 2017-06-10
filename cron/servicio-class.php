<?php 

class Servicio extends Contract
{
	private $servicioId;
	
	private $tipoServicioId;
	public function setTipoServicioId($value)
	{
		$this->Util()->ValidateInteger($value);
		if($value == 0)
		{
			$this->Util()->setError(10055, 'error', '', 'Por favor, escoge un servicio');
		}
		$this->tipoServicioId = $value;
	}

	public function getTipoServicioId()
	{
		return $this->tipoServicioId;
	}

	private $costo;
	public function setCosto($value)
	{
		$this->Util()->ValidateFloat($value, 2);
		$this->costo = $value;
	}

	private $inicioFactura;
	public function setInicioFactura($value)
	{
		$value = $this->Util()->FormatDateMySql($value);
		$this->inicioFactura = $value;
	}

	private $inicioOperaciones;
	public function setInicioOperaciones($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 1, "Inicio Operaciones");
		$value = $this->Util()->FormatDateMySql($value);
		$this->inicioOperaciones = $value;
	}


	public function setServicioId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->servicioId = $value;
	}

	public function getServicioId()
	{
		return $this->servicioId;
	}
	
	public function CreateServiceInstances()
	{	$cont=0;
		$init = microtime();
		$result = $this->EnumerateActive();
		foreach($result as $key => $value)
		{
			$dateExploded = explode("-", $value["inicioOperaciones"]);
			//check if first instance
			if(!count($value["instancias"]))
			{
				$this->Util()->DB()->setQuery("
					INSERT INTO  `instanciaServicio` (
						`servicioId` ,
						`date` ,
						`status`
						)
						VALUES (
							'".$value["servicioId"]."',  
							'".$dateExploded[0]."-".$dateExploded[1]."-1',  
							'activa');"
						);
						$this->Util()->DB()->InsertData();
			}
			//checamos si ya es tiempo de crear otra instancia
			else
			{
				//char ultima fecha de instancia
				$this->Util()->DB()->setQuery("
					SELECT date FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' ORDER BY date DESC LIMIT 1"); 
				$ultimoServicio = $this->Util()->DB()->GetSingle();

				switch($value["periodicidad"])
				{
					case "Mensual": $substract = "-1 month"; break;
					case "Bimestral": $substract = "-2 month"; break;
					case "Trimestral": $substract = "-3 month"; break;
					case "Semestral": $substract = "-6 month"; break;
					case "Anual": $substract = "-12 month"; break;
					case "Eventual": continue 2; break;
				}
				$currentDate = date("Y-m-d");
				$newdate = strtotime ( $substract , strtotime ( $currentDate ) ) ;
				$newdate = date ( 'Y-m-d' , $newdate );
				
				$text.='newdate => '.$newdate.' - ultimoServicio =>'.$ultimoServicio."<br>";
				if($newdate >= $ultimoServicio)
				{
					switch($value["periodicidad"])
					{
						case "Mensual": $add = "+1 month"; break;
						case "Bimestral": $add = "+2 month"; break;
						case "Trimestral": $add = "+3 month"; break;
						case "Semestral": $add = "+6 month"; break;
						case "Anual": $add = "+12 month"; break;
					}
					$addedDate = strtotime ( $add , strtotime ( $ultimoServicio ) ) ;
					$addedDate = date ( 'Y-m-d' , $addedDate );
					$explodedAddedDate = explode("-", $addedDate);
					
					$this->Util()->DB()->setQuery("
						REPLACE INTO  `instanciaServicio` (
							`servicioId` ,
							`date` ,
							`status`
							)
							VALUES (
								'".$value["servicioId"]."',  
								'".$explodedAddedDate[0]."-".$explodedAddedDate[1]."-1',  
								'activa');"
							);
						$this->Util()->DB()->InsertData();
						$cont++;
				}

			}
		}
		$end = microtime();
		$tiempo = $end-$init;
		echo "Script ejecutado en ".$tiempo." Milisegundos ".$cont."<br>";
		echo $text;
	}

	public function Enumerate()
	{
		global $months;
		
		$this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costo FROM servicio 
			LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
			LEFT JOIN contract ON contract.contractId = servicio.contractId
				WHERE servicio.contractId = '".$this->getContractId()."'					
				ORDER BY tipoServicio.nombreServicio ASC");
		$result = $this->Util()->DB()->GetResult();
		foreach($result as $key => $value)
		{
			$fecha = explode("-", $value["inicioOperaciones"]);
			$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

			$fecha = explode("-", $value["inicioFactura"]);
			$result[$key]["formattedInicioFactura"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];
		}
		return $result;
	}

	public function EnumerateActive($customer = 0, $contract = 0, $rfc = "")
	{
		global $months, $User;

		if($customer != 0)
		{
			$sqlCustomer = " AND customer.customerId = '".$customer."'";
		}

		if($contract != 0)
		{
			$sqlContract = " AND contract.contractId = '".$contract."'";
		}
		
		if(strlen($rfc) > 3 && $customer == 0 && $contract == 0)
		{
			$sqlContract = " AND (customer.nameContact LIKE '%".$rfc."%' OR contract.name LIKE '%".$rfc."%')";
		}
		
		if($User["subRoleId"] == "Nomina")
		{
			$addNomina = " AND servicio.tipoServicioId IN (".SERVICIOS_NOMINA.")";
		}

		
		$this->Util()->DB()->setQuery("SELECT servicioId,  customer.nameContact AS clienteName, contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad, servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName, customer.customerId, customer.nameContact FROM servicio 
			LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
			LEFT JOIN contract ON contract.contractId = servicio.contractId
			LEFT JOIN customer ON customer.customerId = contract.customerId
			LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
			WHERE servicio.status = 'activo' AND customer.active = '1'
			".$sqlCustomer.$sqlContract.$addNomina."					
			ORDER BY clienteName, razonSocialName, nombreServicio ASC");
		//$this->Util()->DB()->query;
		$result = $this->Util()->DB()->GetResult();
		foreach($result as $key => $value)
		{
			$user = new User;
			$user->setUserId($value["responsableCuenta"]);
			$userInfo = $user->Info();
			if(
				($User["roleId"] > 2 && $User["roleId"] < 4) && 
				($User["userId"] != $value["responsableCuenta"] && 
				$userInfo["jefeContador"] != $User["userId"] && 
				$userInfo["jefeSupervisor"] != $User["userId"] && 
				$userInfo["jefeGerente"] != $User["userId"] && 
				$userInfo["jefeSocio"] != $User["userId"])
			)
			{
				unset($result[$key]);
				continue;
			}
			
			$result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
//			echo $value["responsableCuenta"];
			$fecha = explode("-", $value["inicioOperaciones"]);
			$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

			$this->Util()->DB()->setQuery("SELECT * FROM instanciaServicio
			WHERE servicioId = '".$value["servicioId"]."'	
			ORDER BY date DESC");
			$result[$key]["instancias"] = $this->Util()->DB()->GetResult();
			
			
			foreach($result[$key]["instancias"] as $keyInstancias => $valueInstancias)
			{
				$result[$key]["instancias"][$keyInstancias]["dateExploded"] = explode("-",$valueInstancias["date"]);
				$result[$key]["instancias"][$keyInstancias]["monthShow"] = $months[$result[$key]["instancias"][$keyInstancias]["dateExploded"][1]]." ".$result[$key]["instancias"][$keyInstancias]["dateExploded"][0];
			}

			//$fecha = explode("-", $value["inicioFactura"]);
			//$result[$key]["formattedInicioFactura"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];
		}
		
		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM servicio WHERE servicioId = '".$this->servicioId."'");
		$row = $this->Util()->DB()->GetRow();
		
		$row["inicioOperacionesMysql"] = $this->Util()->FormatDateMySql($row["inicioOperaciones"]);
		$row["inicioFacturaMysql"] = $this->Util()->FormatDateMySql($row["inicioFactura"]);

		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				servicio
			SET
				`costo` = '".$this->costo."',
				`inicioFactura` = '".$this->inicioFactura."',
				`tipoServicioId` = '".$this->tipoServicioId."',
				`inicioOperaciones` = '".$this->inicioOperaciones."'
			WHERE servicioId = '".$this->servicioId."'");
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
				servicio
			(
				`contractId`,
				`tipoServicioId`,
				`inicioFactura`,
				`inicioOperaciones`,
				`costo`
		)
		VALUES
		(
				'".$this->getContractId()."',
				'".$this->tipoServicioId."',
				'".$this->inicioFactura."',
				'".$this->inicioOperaciones."',
				'".$this->costo."'
		);");
		$this->Util()->DB()->InsertData();
		$this->Util()->setError(2, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }
		
		$info = $this->Info();
		
		if($info["status"] == 'activo')
		{
			$active = 'baja';
			$complete = "El servicio fue dado de baja correctamente";
		}
		else
		{
			$active = 'activo';
			$complete = "El servicio fue dado de alta correctamente";
		}
		
		$this->Util()->DB()->setQuery("
			UPDATE
				servicio
			SET status = '".$active."'
			WHERE
				servicioId = '".$this->servicioId."'");
		$this->Util()->DB()->UpdateData();
		$this->Util()->setError(3, "complete", $complete);
		$this->Util()->PrintErrors();
		return true;
	}

	public function EnumerateActiveOnlyNames($contract = 0)
	{
		global $months, $User;

		if($contract != 0)
		{
			$sqlContract = " AND contract.contractId = '".$contract."'";
		}
		
		$this->Util()->DB()->setQuery("SELECT servicioId,  customer.nameContact AS clienteName, contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad, servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName, customer.customerId, customer.nameContact FROM servicio 
			LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
			LEFT JOIN contract ON contract.contractId = servicio.contractId
			LEFT JOIN customer ON customer.customerId = contract.customerId
			LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
			WHERE servicio.status = 'activo' AND customer.active = '1'
			".$sqlCustomer.$sqlContract.$addNomina."					
			ORDER BY nombreServicio ASC");
		//$this->Util()->DB()->query;
		$result = $this->Util()->DB()->GetResult();
		foreach($result as $key => $value)
		{
			$user = new User;
			$user->setUserId($value["responsableCuenta"]);
			$userInfo = $user->Info();
			if(
				($User["roleId"] > 2 && $User["roleId"] < 4) && 
				($User["userId"] != $value["responsableCuenta"] && 
				$userInfo["jefeContador"] != $User["userId"] && 
				$userInfo["jefeSupervisor"] != $User["userId"] && 
				$userInfo["jefeGerente"] != $User["userId"] && 
				$userInfo["jefeSocio"] != $User["userId"])
			)
			{
				unset($result[$key]);
				continue;
			}
			
			$result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
//			echo $value["responsableCuenta"];
			$fecha = explode("-", $value["inicioOperaciones"]);
			$result[$key]["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

		}
		
		return $result;
	}
}

?>

