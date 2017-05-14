<?php
class Servicio extends Contract
{
	private $servicioId;
	private $contratosActivos;
	
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
	
	private $_instanciaServicioId;
	public function setInstanciaServicioId($value) {
		$this->instanciaServicioId = $value; 
	}
	 
	private $_status;
	public function setStatus($value) {
		$this->status = $value; 
	}
	
	public function setContratosActivos($value){
		$this->contratosActivos = $value;
	}
  	
	public function CreateServiceInstances()
	{
		$init = microtime();
		$result = $this->EnumerateActive();
		
		foreach($result as $key => $value)
		{	
			echo 'servicioId = '.$value['servicioId'];
			echo '<br>';
			
			$dateExploded = explode("-", $value["inicioOperaciones"]);
						
			//Check if first instance
			if(!count($value["instancias"]))
			{	
				$sql = "INSERT INTO  `instanciaServicio` (`servicioId`,`date`,`status`)
				VALUES ('".$value["servicioId"]."','".$dateExploded[0]."-".$dateExploded[1]."-1','activa');";
				$this->Util()->DB()->setQuery($sql);
				$this->Util()->DB()->InsertData();
				
				echo $sql.'<br>';
				
			}else{
				
				//Checamos si ya es tiempo de crear otra instancia
			
				//Checar ultima fecha de instancia
				$this->Util()->DB()->setQuery("
					SELECT date FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' ORDER BY date DESC LIMIT 1"); 
				echo $ultimoServicio = $this->Util()->DB()->GetSingle();

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
				//--------------------------------------------------------------------------
				$this->Util()->DB()->setQuery("
				SELECT date FROM instanciaServicio WHERE servicioId = '".$value["servicioId"]."' ORDER BY date ASC LIMIT 1"); 
				$primerServicio = $this->Util()->DB()->GetSingle();
				$startdate=$dateExploded[0]."-".$dateExploded[1]."-01";
				
				echo 'primerServicio = '.$primerServicio;
				echo '<br>ultimoServicio = '.$ultimoServicio;
				echo '<br>currentDate = '.$currentDate;
				echo '<br>newdate = '.$newdate;
				echo '<br>startdate = '.$startdate;
				if($primerServicio > $startdate)
				{	
					switch($value["periodicidad"])
					{
						case "Mensual": $add = "+1 month"; break;
						case "Bimestral": $add = "+2 month"; break;
						case "Trimestral": $add = "+3 month"; break;
						case "Semestral": $add = "+6 month"; break;
						case "Anual": $add = "+12 month"; break;
					}
					while($primerServicio > $startdate)
					{
						$dateExploded = explode("-",$startdate);

						if($value["tipoServicioId"] == RIF )
						{
							if($dateExploded[1] % 2 == 1)
							{
								$dateExploded[1] = $dateExploded[1] + 1;
							}
						}

						$this->Util()->DB()->setQuery("
						REPLACE INTO  `instanciaServicio` (
							`servicioId` ,
							`date` ,
							`status`
						) VALUES (
							'".$value["servicioId"]."',  
							'".$dateExploded[0]."-".$dateExploded[1]."-01',  
							'activa');"
						);
						//echo $this->Util()->DB()->query;
						$this->Util()->DB()->InsertData();
						echo '<br>';
						
						$startdate = strtotime ( $add , strtotime ( $startdate ) ) ;
						$startdate = date ( 'Y-m-d' , $startdate );
					}
				}
				
				//--------------------------------------------------------------------------
	
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
							`servicioId`,
							`date`,
							`status`
							)
							VALUES (
								'".$value["servicioId"]."',  
								'".$explodedAddedDate[0]."-".$explodedAddedDate[1]."-1',  
								'activa');"
							);
					//		echo $this->Util()->DB()->query;
					$this->Util()->DB()->InsertData();
					
					echo $this->Util()->DB()->getQuery();
					echo '<br>';
				}

			}
			
			echo '<br>';
			echo '*****************';
			echo '<br>';
			
		}//foreach
		
		$end = microtime();
		
		echo 'Init = '.$init;
		echo '<br>';
		echo 'End = '.$end;
				
		$tiempo = $end-$init;
		echo "Script ejecutado en ".$tiempo." Milisegundos";
		
	}//CreateServiceInstances

	public function Enumerate()
	{
		global $months;
		
		$sql = "SELECT *, servicio.costo AS costo, tipoServicio.costoVisual, tipoServicio.mostrarCostoVisual 
				FROM servicio 
				LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				LEFT JOIN contract ON contract.contractId = servicio.contractId
				WHERE servicio.contractId = '".$this->getContractId()."'					
				ORDER BY tipoServicio.nombreServicio ASC";
		$this->Util()->DB()->setQuery($sql);
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

	public function EnumerateActive($type ="subordinado",$customer = 0, $contract = 0, $rfc = "", $departamentoId="", $respCta = 0)
	{
		global $months, $User;

		if($customer != 0)
			$sqlCustomer = " AND customer.customerId = '".$customer."'";
		
		if($contract != 0)
			$sqlContract = " AND contract.contractId = '".$contract."'";
		
		if(strlen($rfc) > 3 && $customer == 0 && $contract == 0)
			$sqlContract = " AND (customer.nameContact LIKE '%".$rfc."%' OR contract.name LIKE '%".$rfc."%')";
				
		if($User["subRoleId"] == "Nomina")
			$addNomina = " AND servicio.tipoServicioId IN (".SERVICIOS_NOMINA.")";
				
		if($respCta)
			$sqlRespCta = ' AND contract.responsableCuenta = '.$respCta;
    
		if($departamentoId!="")
		  $depto = " AND tipoServicio.departamentoId='".$departamentoId."'";
		
		//$debug = "servicioId = 3594 AND ";
		$sql = "SELECT servicioId,  customer.nameContact AS clienteName, 
				contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad,
				servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, 
				responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
				customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,
				responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente, servicio.tipoServicioId, contract.activo
				FROM servicio 
				LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				LEFT JOIN contract ON contract.contractId = servicio.contractId
				LEFT JOIN customer ON customer.customerId = contract.customerId
				LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
				WHERE ".$debug." servicio.status = 'activo' AND customer.active = '1'
				".$sqlCustomer.$sqlContract.$addNomina.$depto.$sqlRespCta." 					
				ORDER BY clienteName, razonSocialName, nombreServicio ASC";						
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();

		if(count($User) == 1){
			$User["roleId"] = 1;
		}
		//echo $User["roleId"];
		
		foreach($result as $key => $value){
			//echo $value["customerId"];
			$filtro = new Filtro;
			$contract = new Contract;
			$data["conPermiso"] = $filtro->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);
			$data["subordinados"] = $filtro->Subordinados($User["userId"]);
			
			$data["subordinadosPermiso"] = $filtro->SubordinadosPermiso($type, $data["subordinados"], $User["userId"]);
			
			$data["withPermission"] = $filtro->WithPermission($User["roleId"], $data["subordinadosPermiso"], $data["conPermiso"]);
			
			if($data["withPermission"] === false){
				unset($result[$key]);
				continue;
			}

			$result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
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
			
		}//foreach
				
		return $result;
		
	}//EnumerateActive
	
	public function EnumerateActiveSub($type ="subordinado",$customer = 0, $contract = 0, $rfc = "", $departamentoId="", $respCta = 0)
	{
		global $months, $User;
		
		$sqlContract = '';
		
		if($customer != 0)
			$sqlCustomer = " AND customer.customerId = '".$customer."'";
		
		if($contract != 0)
			$sqlContract .= " AND contract.contractId = '".$contract."'";
		
		if($this->contratosActivos)
			$sqlContract .= " AND contract.activo = '".$this->contratosActivos."'";
		
		if(strlen($rfc) > 3 && $customer == 0 && $contract == 0)
			$sqlContract .= " AND (customer.nameContact LIKE '%".$rfc."%' OR contract.name LIKE '%".$rfc."%')";
				
		if($User["subRoleId"] == "Nomina")
			$addNomina = " AND servicio.tipoServicioId IN (".SERVICIOS_NOMINA.")";
				
		if($respCta)
			$sqlRespCta = ' AND contract.responsableCuenta = '.$respCta;
    
		if($departamentoId!="")
		  $depto = " AND tipoServicio.departamentoId='".$departamentoId."'";
		
		$sql = "SELECT servicioId,  customer.nameContact AS clienteName, 
				contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad,
				servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, 
				responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
				customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,
				responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente
				FROM servicio 
				LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				LEFT JOIN contract ON contract.contractId = servicio.contractId
				LEFT JOIN customer ON customer.customerId = contract.customerId
				LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
				WHERE servicio.status = 'activo' AND customer.active = '1'
				".$sqlCustomer.$sqlContract.$addNomina.$depto.$sqlRespCta." 					
				ORDER BY clienteName, razonSocialName, nombreServicio ASC";						
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
				
		if(!$User)
			$User["roleId"] = 1;
		
		$servicios = array();
		foreach($result as $key => $value){
			
			$card = $value;
			
			$contract = new Contract;
			$conPermiso = $contract->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);
			
			$personal = new Personal;
			$personal->setPersonalId($User["userId"]);
			$subordinados = $personal->Subordinados();

		    if($type == "propio"){			
              	$subordinadosPermiso = array($User["userId"]);
            }else{
			
              $subordinadosPermiso = array();
              foreach ($subordinados as $sub) {
                array_push($subordinadosPermiso, $sub["personalId"]);
              }
			  
              array_push($subordinadosPermiso, $User["userId"]);
            }//else
			
			$withPermission = false;
			
			if($User["roleId"] == 1 || $User["roleId"] == 4){
				$withPermission = true;
			}else{
			
				foreach($subordinadosPermiso as $usuarioPermiso){				
					if(in_array($usuarioPermiso, $conPermiso)){
						$withPermission = true;
						break;
					}
				}
			}//else
			
			if($withPermission === false)
				continue;
			
			$fecha = explode("-", $value["inicioOperaciones"]);
			$card["formattedInicioOperaciones"] = $fecha[2]."/".$months[$fecha[1]]."/".$fecha[0];

			$this->Util()->DB()->setQuery("SELECT * FROM instanciaServicio
			WHERE servicioId = '".$value["servicioId"]."'	
			ORDER BY date DESC");
			$resInstancias = $this->Util()->DB()->GetResult();
			
			$instancias = array();		
			foreach($resInstancias as $val2){
			
				$card2 = $val2;
				
				$dateExploded = explode("-",$val2['date']);
				$card2["monthShow"] = $months[$dateExploded[1]]." ".$dateExploded[0];
				
				$instancias[] = $val2;
			}
			$card['instancias'] = $instancias;
			
			$servicios[] = $card;
			
		}//foreach
				
		if($type != 'subordinado')
			return $servicios;
			
		# ADD SUBORDINADOS #
		
		$personal = new Personal;
		$personal->setPersonalId($respCta);
		$subordinados = $personal->Subordinados();
		
		$result = array();
		foreach($subordinados as $res){
					
			$sqlRespCta = ' AND contract.responsableCuenta = '.$res['personalId'];
			
			$sql = "SELECT servicioId,  customer.nameContact AS clienteName, 
					contract.name AS razonSocialName, nombreServicio, servicio.costo, inicioOperaciones, periodicidad,
					servicio.contractId, contract.encargadoCuenta, contract.responsableCuenta, 
					responsableCuenta.email AS responsableCuentaEmail, responsableCuenta.name AS responsableCuentaName,
					customer.customerId, customer.nameContact, contract.permisos, responsableCuenta.tipoPersonal,
					responsableCuenta.jefeContador, responsableCuenta.jefeSupervisor, responsableCuenta.jefeGerente
					FROM servicio 
					LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
					LEFT JOIN contract ON contract.contractId = servicio.contractId
					LEFT JOIN customer ON customer.customerId = contract.customerId
					LEFT JOIN personal AS responsableCuenta ON responsableCuenta.personalId = contract.responsableCuenta
					WHERE servicio.status = 'activo' AND customer.active = '1'
					".$sqlCustomer.$sqlContract.$addNomina.$depto.$sqlRespCta." 					
					ORDER BY clienteName, razonSocialName, nombreServicio ASC";					
			$this->Util()->DB()->setQuery($sql);
			$result = $this->Util()->DB()->GetResult();
			
			foreach($result as $key => $value){
				
				$result[$key]["responsableCuentaName"] = $result[$key]["responsableCuentaName"];
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
				
				$servicios[] = $result[$key];
				
			}//foreach
		
		}//foreach
		
		return $servicios;
		
	}//EnumerateActiveSub
	
	 
  	public function CancelWorkFlow()
	{
		$this->Util()->DB()->setQuery("
			UPDATE
				instanciaServicio
			SET
				`status` = '".$this->status."'
			WHERE 
      instanciaServicioId = '".$this->instanciaServicioId."'");
		$this->Util()->DB()->UpdateData();
	}
  
	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costo FROM servicio 
		LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
		LEFT JOIN contract ON contract.contractId = servicio.contractId
		WHERE servicioId = '".$this->servicioId."'");
		$row = $this->Util()->DB()->GetRow();
		
		$row["inicioOperacionesMysql"] = $this->Util()->FormatDateMySql($row["inicioOperaciones"]);
		$row["inicioFacturaMysql"] = $this->Util()->FormatDateMySql($row["inicioFactura"]);

		return $row;
	}
	
	public function Historial()
	{
		$this->Util()->DB()->setQuery("SELECT historyChanges.*, personal.name FROM historyChanges 
		LEFT JOIN personal ON personal.personalId = historyChanges.personalId
		WHERE servicioId = '".$this->servicioId."'");
		$result = $this->Util()->DB()->GetResult();
		
		return $result;
	}	
	

  
	public function Edit()
	{
		global $User;
		
		if($this->Util()->PrintErrors()){ return false; }

		$infoServicio = $this->Info();
		
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
	
		//actualizar historial
		$this->Util()->DB()->setQuery("
			INSERT INTO
				historyChanges
			(
				`servicioId`,
				`inicioFactura`,
				`costo`,
				`personalId`,
				`inicioOperaciones`
		)
		VALUES
		(
				'".$this->servicioId."',
				'".$this->inicioFactura."',
				'".$this->costo."',
				'".$User["userId"]."',
				'".$this->inicioOperaciones."'
		);");
		
		$this->Util()->DB()->InsertData();

		
		$subject = "El servicio de ".$infoServicio["nombreServicio"]." del la razon social ".$infoServicio["name"]." ha sido actualizado";

		$this->Util()->DB()->setQuery("SELECT * FROM personal WHERE personalId = 66 OR personalId = '".IDHUERIN."' OR (tipoPersonal = 'Gerente' && departamentoId = '1')");
		$personal = $this->Util()->DB()->GetResult();
		$sendmail = new SendMail();
		
		if($infoServicio["costo"] == $this->costo)
		{
			$this->Util()->setError(1, "complete");
			$this->Util()->PrintErrors();
			return true;
		}
		
		foreach($personal as $key => $personal)
		{
			$to = $personal["email"];
//			$to = "comprobantefiscal@braunhuerin.com.mx";
			$toName = $personal["name"];
			$body = "El servicio de ".$infoServicio["nombreServicio"]." del la razon social ".$infoServicio["name"]." ha sido actualizado<br>La actualization fue hecha por:".$_SESSION["User"]["username"]."<br>El costo era de ".$infoServicio["costo"]." el nuevo costo es de ".$this->costo;
			$sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
		//	break;
		} 

		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		global $User;
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

		$servicioId = $this->Util()->DB()->InsertData();

		//actualizar historial
		$this->Util()->DB()->setQuery("
			INSERT INTO
				historyChanges
			(
				`servicioId`,
				`inicioFactura`,
				`costo`,
				`personalId`,
				`inicioOperaciones`
		)
		VALUES
		(
				'".$servicioId."',
				'".$this->inicioFactura."',
				'".$this->costo."',
				'".$User["userId"]."',
				'".$this->inicioOperaciones."'
		);");

		$this->Util()->DB()->InsertData();
		$this->Util()->setError(2, "complete");
		$this->Util()->PrintErrors();
		
		return $servicioId;
	}

	public function Delete()
	{
		global $User;

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


		$this->Util()->DB()->setQuery("
			SELECT * FROM
				servicio
			WHERE
				servicioId = '".$this->servicioId."'");
		$servicio = $this->Util()->DB()->GetRow();


		//actualizar historial
		$this->Util()->DB()->setQuery("
			INSERT INTO
				historyChanges
			(
				`servicioId`,
				`inicioFactura`,
				`status`,
				`costo`,
				`personalId`,
				`inicioOperaciones`
		)
		VALUES
		(
				'".$servicio["servicioId"]."',
				'".$servicio["inicioFactura"]."',
				'".$servicio["status"]."',
				'".$servicio["costo"]."',
				'".$User["userId"]."',
				'".$servicio["inicioFactura"]."'
		);");
		$this->Util()->DB()->InsertData();
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

	public function UpdateComentario($comentario)
	{
		global $User;

		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				servicio
			SET
				`comentario` = '".$comentario."'
			WHERE servicioId = '".$this->servicioId."'");
		$this->Util()->DB()->UpdateData();


		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function HistorialAll()
	{
		$this->Util()->DB()->setQuery("
			SELECT historyChanges.*, personal.name AS personalName, servicio.tipoServicioId, servicio.contractId, tipoServicio.nombreServicio, contract.customerId, contract.name AS contractName, customer.customerId, customer.nameContact FROM historyChanges
			JOIN personal ON personal.personalId = historyChanges.personalId
			JOIN servicio ON servicio.servicioId = historyChanges.servicioId
			JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
			JOIN contract ON contract.contractId = servicio.contractId
			JOIN customer ON customer.customerId = contract.customerId
			WHERE historyChangesId > 1512 ORDER BY historyChangesId DESC");
		$data =$this->Util()->DB()->GetResult();

		return $data;
	}

}
