<?php 

class Workflow extends Servicio
{
	private $instanciaServicioId;
	

	public function setInstanciaServicioId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->instanciaServicioId = $value;
	}

  public function setTipoOperacion($value) {
    $this->tipoOperacion = $value;
  }
	public function EnumerateWorkflows($clientes, $month, $year, $tipo = "subordinado", $reorder = true)
	{
		global $User;
		$_SESSION["PorIniciar"] = 0;
		$_SESSION["Iniciado"] = 0;
		$_SESSION["PorCompletar"] = 0;
		$_SESSION["CompletoTardio"] = 0;
		$_SESSION["Completo"] = 0;

		$_SESSION["PorIniciarContract"] = 0;
		$_SESSION["IniciadoContract"] = 0;
		$_SESSION["PorCompletarContract"] = 0;
		$_SESSION["CompletoTardioContract"] = 0;
		$_SESSION["CompletoContract"] = 0;

		$_SESSION["PorIniciarServicio"] = 0;
		$_SESSION["IniciadoServicio"] = 0;
		$_SESSION["PorCompletarServicio"] = 0;
		$_SESSION["CompletoTardioServicio"] = 0;
		$_SESSION["CompletoServicio"] = 0;

		$_SESSION["PorIniciarSteps"] = 0;
		$_SESSION["IniciadoSteps"] = 0;
		$_SESSION["PorCompletarSteps"] = 0;
		$_SESSION["CompletoTardioSteps"] = 0;
		$_SESSION["CompletoSteps"] = 0;
		//print_r($_POST);
		foreach($clientes as $keyCliente => $cliente)
		{
			$clientes[$keyCliente]["totalContracts"] = 0;
			$clientes[$keyCliente]["totalServicios"] = 0;
			$clientes[$keyCliente]["totalServiciosCompletados"] = 0;
			$clientes[$keyCliente]["maxDay"] = 15;
			$clientes[$keyCliente]["maxCompleted"] = 0;

			$totalPorcentajeContracts = 0;
			$totalPorcentajeContractsCompleted = 0;
			
			if($_POST["responsableCuenta"])
			{
				$responsable = false;
			}
			else
			{
				$responsable = true;
			}
			
			foreach($cliente["contracts"] as $keyContract => $contract)
			{
				//ver si tengo permiso de verlo
				if($User["roleId"] == 4)
				{
					$showContract = true;
				}
				else
				{
					$showContract = false;
				}

				$user = new User;
				$user->setUserId($contract["responsableCuenta"]);
				$userInfo = $user->Info();
				
				if($contract["responsableCuenta"] == $_POST["responsableCuenta"])
				{
					$responsable = true;
				}
				
				if($tipo == "propio")
				{
					if(
						$User["userId"] == $contract["responsableCuenta"] 
					)
					{
						$showContract = true;
					}
				}
				else
				{
					if(
						$User["userId"] == $contract["responsableCuenta"] || 
						$userInfo["jefeContador"] == $User["userId"] || 
						$userInfo["jefeSupervisor"] == $User["userId"] || 
						$userInfo["jefeGerente"] == $User["userId"] || 
						$userInfo["jefeSocio"] == $User["userId"] ||
						$User["roleId"] == 1
				
					)
					{
						$showContract = true;
					}
				}

				if($contract["responsableCuenta"] != $_POST["responsableCuenta"] && $_POST["responsableCuenta"])
				{
					$showContract = false;
				}
				
				if($showContract === false)
				{
					unset($clientes[$keyCliente]["contracts"][$keyContract]);
					continue;
				}
				$clientes[$keyCliente]["totalContracts"]++;
				
				$user = new User;
				$clientes[$keyCliente]["contracts"][$keyContract]["responsable"] = $userInfo;
				$clientes[$keyCliente]["contracts"][$keyContract]["responsable"]["name"] = utf8_encode($clientes[$keyCliente]["contracts"][$keyContract]["responsable"]["name"]);
				
				if($User["subRoleId"] == "Nomina")
				{
					$addNomina = " AND servicio.tipoServicioId IN (".SERVICIOS_NOMINA.")";
				}
				
				$this->Util()->DB()->setQuery("SELECT instanciaServicioId  FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				LEFT JOIN contract ON contract.contractid = servicio.contractId
				LEFT JOIN customer ON customer.customerId = contract.customerId
				WHERE 
					contract.contractId = '".$contract["contractId"]."' 
					AND MONTH(instanciaServicio.date) = '".$month."' 
					AND YEAR(instanciaServicio.date) = '".$year."'
					AND servicio.status != 'baja'
					".$addNomina." ORDER BY tipoServicio.nombreServicio");
				$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"] = $this->Util()->DB()->GetResult();

/*<!--				if($User["subRoleId"] == "Nomina" && count($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"]) == 0)
				{
					unset($clientes[$keyCliente]["contracts"][$keyContract]);
					unset($clientes[$keyCliente]);
					continue;
				}
-->*/				
				$clientes[$keyCliente]["contracts"][$keyContract]["totalInstancias"] = count($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"]);
				$clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasCompletadas"] = 0;
				
				$totalPorcentajeSteps = 0;
				$totalPorcentajeStepsCompleted = 0;
				if(count($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"]) == 0)
				{
					$totalPorcentajeSteps = 100;
					$totalPorcentajeStepsCompleted = 100;
//					continue;
				}
				foreach($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"] as $keyInstancia => $row)
				{
					$clientes[$keyCliente]["totalServicios"]++;
					$this->setInstanciaServicioId($row["instanciaServicioId"]);
					$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia] = $this->Info();				
					$fechaCompletoServicio = strtotime($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["fechaCompleta"]);
					if($fechaCompletoServicio  > $clientes[$keyCliente]["maxCompleted"])
					{
						$clientes[$keyCliente]["maxCompleted"] = $fechaCompletoServicio;
					}
					//aun no toma en cuenta la prorroga
					if($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["maxDay"] > $clientes[$keyCliente]["maxDay"])
					{
						$clientes[$keyCliente]["maxDay"] = $clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["maxDay"];
					}
					
					$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["status"];
					if($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["status"] == "completa")
					{
						$clientes[$keyCliente]["totalServiciosCompletados"]++;
						$clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasCompletadas"]++;
					}
					
					$totalPorcentajeSteps += 100;
					if($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["totalSteps"] > 0)
					{
						$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"] = floor($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["completedSteps"] * 100 / $clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["totalSteps"]);
					}
					else
					{
						$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"] = 100;
					}
					$totalPorcentajeStepsCompleted += $clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"];
					
					if($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"] == 0)
						$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["class"] = "PorIniciar";
					elseif($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"] > 0 && $clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"] < 70)
						$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["class"] = "Iniciado";
					elseif($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"] >= 70 && $clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"] < 100)
						$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["class"] = "PorCompletar";
          elseif($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["porcentajeSteps"] >= 100 && $clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["maxDay"] < date("j"))
            $clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["class"] = "CompletoTardio";
					else
						$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"][$keyInstancia]["class"] = "Completo";

				}
				//echo $totalPorcentajeStepsCompleted;

				$clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasPendientes"] = $clientes[$keyCliente]["contracts"][$keyContract]["totalInstancias"] - $clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasCompletadas"];
				
				if($totalPorcentajeSteps <= 0)
				{
					$totalPorcentajeSteps = 1;
				}
				$clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasPorcentaje"] = $totalPorcentajeStepsCompleted / $totalPorcentajeSteps * 100;

				$totalPorcentajeContracts += 100;
				$totalPorcentajeContractsCompleted += $clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasPorcentaje"];

				if($clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasPorcentaje"] == 0)
					$clientes[$keyCliente]["contracts"][$keyContract]["class"] = "PorIniciar";
				elseif($clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasPorcentaje"] > 0 && $clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasPorcentaje"] < 70)
					$clientes[$keyCliente]["contracts"][$keyContract]["class"] = "Iniciado";
				elseif($clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasPorcentaje"] >= 70 && $clientes[$keyCliente]["contracts"][$keyContract]["totalInstanciasPorcentaje"] < 100)
					$clientes[$keyCliente]["contracts"][$keyContract]["class"] = "PorCompletar";
				else
					$clientes[$keyCliente]["contracts"][$keyContract]["class"] = "Completo";
				


				$clientes[$keyCliente]["totalServiciosPendientes"] = $clientes[$keyCliente]["totalServicios"] - $clientes[$keyCliente]["totalServiciosCompletados"];
				
				$clientes[$keyCliente]["totalServiciosPorcentaje"] = $totalPorcentajeContractsCompleted / $totalPorcentajeContracts * 100;
				
				if($clientes[$keyCliente]["totalServiciosPorcentaje"] == 0)
				{
					$clientes[$keyCliente]["class"] = "PorIniciar";
				}
				elseif($clientes[$keyCliente]["totalServiciosPorcentaje"] > 0 && $clientes[$keyCliente]["totalServiciosPorcentaje"] < 70)
				{
					$clientes[$keyCliente]["class"] = "Iniciado";
				}
				elseif($clientes[$keyCliente]["totalServiciosPorcentaje"] >= 70 && $clientes[$keyCliente]["totalServiciosPorcentaje"] < 100)
				{
					$clientes[$keyCliente]["class"] = "PorCompletar";
				}
				else
				{
					$maxDay = mktime(0, 0, 0, date("m"), $clientes[$keyCliente]["maxDay"], date("Y"));
					$clientes[$keyCliente]["maxCompleted"];
					if($clientes[$keyCliente]["maxCompleted"] > $maxDay)
					{
						$clientes[$keyCliente]["class"] = "CompletoTardio";
					}
					else
					{
						$clientes[$keyCliente]["class"] = "Completo";
					}
				}
				
				if($_POST["status"])
				{
					if($clientes[$keyCliente]["class"] != $_POST["status"])
					{
						unset($clientes[$keyCliente]);
						continue;
					}
				}
				
				if($responsable === false)
				{
					unset($clientes[$keyCliente]);
					continue;
				}
				
			}
			if($clientes[$keyCliente]["totalContracts"] == 0 || !$clientes[$keyCliente]["totalContracts"])
			{
				unset($clientes[$keyCliente]);
				continue;
			}
			
		}
		//print_r($clientes);
		//$clientes = $this->Util()->orderMultiDimensionalArray($clientes,'totalServiciosPorcentaje', false);

		//$clientes = $this->Util()->orderMultiDimensionalArray($clientes,'nameContact', false);		
		
		//print_r($_POST);
		foreach($clientes as $keyCliente => $cliente)
		{
			switch($cliente["class"])
			{
				case "PorIniciar": $_SESSION["PorIniciar"]++; break;
				case "PorCompletar": $_SESSION["PorCompletar"]++; break;
				case "Iniciado": $_SESSION["Iniciado"]++; break;
				case "Completo": $_SESSION["Completo"]++; break;
				case "CompletoTardio": $_SESSION["CompletoTardio"]++; break;
			}
			
			if($reorder === true)
			{
				$clientes[$keyCliente]["contracts"] = $this->Util()->orderMultiDimensionalArray($clientes[$keyCliente]["contracts"],'totalInstanciasPorcentaje', false);
			}
			
			foreach($clientes[$keyCliente]["contracts"] as $keyContract => $contract)
			{
				if($reorder === true)
				{
					$clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"] = $this->Util()->orderMultiDimensionalArray($clientes[$keyCliente]["contracts"][$keyContract]["instanciaServicio"],'porcentajeSteps', false);
				}
				//print_r($contract);
				//aqui voy
					switch($contract["class"])
					{
						case "PorIniciar": $_SESSION["PorIniciarContract"]++; break;
						case "PorCompletar": $_SESSION["PorCompletarContract"]++; break;
						case "Iniciado": $_SESSION["IniciadoContract"]++; break;
						case "Completo": $_SESSION["CompletoContract"]++; break;
						case "CompletoTardio": $_SESSION["CompletoTardioContract"]++; break;
					}

				foreach($contract["instanciaServicio"] as $instancia)
				{
					switch($instancia["class"])
					{
						case "PorIniciar": $_SESSION["PorIniciarServicio"]++; break;
						case "PorCompletar": $_SESSION["PorCompletarServicio"]++; break;
						case "Iniciado": $_SESSION["IniciadoServicio"]++; break;
						case "Completo": $_SESSION["CompletoServicio"]++; break;
						case "CompletoTardio": $_SESSION["CompletoTardioServicio"]++; break;
					}
					
					foreach($instancia["steps"] as $step)
					{
						switch($step["class"])
						{
							case "PorIniciar": $_SESSION["PorIniciarSteps"]++; break;
							case "PorCompletar": $_SESSION["PorCompletarSteps"]++; break;
							case "Iniciado": $_SESSION["IniciadoSteps"]++; break;
							case "Completo": $_SESSION["CompletoSteps"]++; break;
							case "CompletoTardio": $_SESSION["CompletoTardioSteps"]++; break;
						}
					}
				}
				
			}
			
		}
		
		return $clientes;
	}
	public function getTasks(){

        $data =  array();
        $this->Util()->DB()->setQuery("SELECT *, customer.nameContact AS customerName, contract.name AS contractName,
		instanciaServicio.status AS status  FROM instanciaServicio 
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
		LEFT JOIN contract ON contract.contractid = servicio.contractId
		LEFT JOIN customer ON customer.customerId = contract.customerId
		WHERE instanciaServicioId = '".$this->instanciaServicioId."' ");
        $row = $this->Util()->DB()->GetRow();

        $data['info'] =  $row;
        $this->Util()->DB()->setQuery("SELECT * FROM step WHERE stepId ='".$_POST['stepId']."' AND servicioId = '".$row["tipoServicioId"]."' ");
        $step = $this->Util()->DB()->GetRow();
        $step["step"] =  $_POST['numStep'];

        $data["step"] =  $step;

        $this->Util()->DB()->setQuery("SELECT * FROM task WHERE stepId = '".$_POST['stepId']."' ");
        $tasks = $this->Util()->DB()->GetResult();
        foreach($tasks as $keyTask => $valueTask){
            $tasks[$keyTask]["controlFile"] = 0;
            if($valueTask["control"]) {
                $this->Util()->DB()->setQuery("SELECT COUNT(*) FROM taskFile 
					WHERE servicioId = '".$this->instanciaServicioId."' AND stepId = '".$_POST["stepId"]."' AND taskId = '".$valueTask["taskId"]."' AND control = 1");
                $find = $this->Util()->DB()->GetSingle();

                if($find > 0){
                    $tasks[$keyTask]["controlFile"] = 1;
                }
                $this->Util()->DB()->setQuery("SELECT * FROM taskFile 
					WHERE servicioId = '".$this->instanciaServicioId."' AND stepId = '".$_POST["stepId"]."' AND taskId = '".$valueTask["taskId"]."' AND control = 1 ORDER BY version DESC");
                $tasks[$keyTask]["controlFileInfo"] = $this->Util()->DB()->GetResult();
            }else{
                $tasks[$keyTask]["controlFile"] = 1;
            }

        }
        $data["tasks"] =  $tasks;

        /*echo "<pre>";
        print_r($data);
        exit;*/
      return $data;
    }
	public function Info()
	{
    	if($this->tipoOperacion == "reporteMensual")
      		$add = " AND instanciaServicio.status != 'baja'";

		$this->Util()->DB()->setQuery("SELECT *, customer.name AS customerName, contract.name AS contractName,
		instanciaServicio.status AS status  FROM instanciaServicio 
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
		LEFT JOIN contract ON contract.contractid = servicio.contractId
		LEFT JOIN customer ON customer.customerId = contract.customerId
		WHERE instanciaServicioId = '".$this->instanciaServicioId."'".$add."");
		$row = $this->Util()->DB()->GetRow();

		$date = explode("-", $row["date"]);
		$contabilidad2015 = false;
		if($row["tipoServicioId"] == SERVICIO_CONTABILIDAD && $date["0"] < 2016) 
		{
					$this->Util()->DB()->setQuery("SELECT * FROM step 
		WHERE stepId != 103 AND servicioId = '".$row["tipoServicioId"]."'");
		}
		elseif($row["tipoServicioId"] == 3 && $date["0"] < 2016) 
		{
					$this->Util()->DB()->setQuery("SELECT * FROM step 
		WHERE stepId != 103 AND servicioId = '".$row["tipoServicioId"]."'");
		}
		else
		{
					$this->Util()->DB()->setQuery("SELECT * FROM step 
		WHERE servicioId = '".$row["tipoServicioId"]."'");

		}
		//Get Steps
		$row["steps"] = $this->Util()->DB()->GetResult();


		//Get Tasks
		
		$ii = 1;
		$row["completedSteps"] = 0;
		foreach($row["steps"] as $key => $value){
			
			$row["steps"][$key]["step"] = $ii;
			$this->Util()->DB()->setQuery("SELECT * FROM task 
			WHERE stepId = '".$value["stepId"]."'");
			$row["steps"][$key]["tasks"] = $this->Util()->DB()->GetResult();
			$row["steps"][$key]["totalTasks"] = count($row["steps"][$key]["tasks"]);
			
			$row["steps"][$key]["completedTasks"] = 0;			
			if(count($row["steps"][$key]["tasks"]) == 0){
				unset($row["steps"][$key]);
				continue;
			}
			
			$porcentajeTotal = 0;
			$porcentajeDone = 0;
			foreach($row["steps"][$key]["tasks"] as $keyTask => $valueTask){
			
				$porcentajeTotal += 100;
				$row["steps"][$key]["tasks"][$keyTask]["controlFile"] = 0;
				
				if($valueTask["control"]){
				
					//Checar si ya se subio ese archivo
					$this->Util()->DB()->setQuery("SELECT COUNT(*) FROM taskFile 
					WHERE servicioId = '".$this->instanciaServicioId."' AND stepId = '".$valueTask["stepId"]."' AND taskId = '".$valueTask["taskId"]."' AND control = 1");
					$done = $this->Util()->DB()->GetSingle();
					
					if($done > 0){
						$row["steps"][$key]["tasks"][$keyTask]["controlFile"] = 1;
					}

					$this->Util()->DB()->setQuery("SELECT * FROM taskFile 
					WHERE servicioId = '".$this->instanciaServicioId."' AND stepId = '".$valueTask["stepId"]."' AND taskId = '".$valueTask["taskId"]."' AND control = 1 ORDER BY version DESC");
					$row["steps"][$key]["tasks"][$keyTask]["controlFileInfo"] = $this->Util()->DB()->GetResult();
					
				}else{
					$row["steps"][$key]["tasks"][$keyTask]["controlFile"] = 1;
				}//else

				$row["steps"][$key]["tasks"][$keyTask]["controlFile2"] = 1;
				$row["steps"][$key]["tasks"][$keyTask]["controlFile3"] = 1;

				$row["steps"][$key]["tasks"][$keyTask]["taskCompleted"] = 0; 
				if($row["steps"][$key]["tasks"][$keyTask]["controlFile"] + $row["steps"][$key]["tasks"][$keyTask]["controlFile2"] + $row["steps"][$key]["tasks"][$keyTask]["controlFile3"] == 3){
				
					$porcentajeDone += 100; 
					$row["steps"][$key]["tasks"][$keyTask]["taskCompleted"] = 1; 
					$row["steps"][$key]["completedTasks"]++;			
					
				}//if
				
			}//foreach
			
			if($porcentajeTotal == 0)
				$porcentajeTotal = 1;
						
			$realPercent = $porcentajeDone / $porcentajeTotal * 100;
			
			if($realPercent == 0)
				$row["steps"][$key]["class"] = "PorIniciar";
			elseif($realPercent > 0 && $realPercent < 70)
				$row["steps"][$key]["class"] = "Iniciado";
			elseif($realPercent >= 70 && $realPercent < 100)
				$row["steps"][$key]["class"] = "PorCompletar";
			else
				$row["steps"][$key]["class"] = "Completo";
			
			$row["steps"][$key]["stepCompleted"] = 0;
			if($row["steps"][$key]["completedTasks"] == $row["steps"][$key]["totalTasks"]){
				$row["steps"][$key]["stepCompleted"] = 1;
				$row["completedSteps"]++;
			}//if

			//Get prev step
			$this->Util()->DB()->setQuery("SELECT * FROM step 
			WHERE servicioId = '".$row["tipoServicioId"]."' AND stepId < '".$value["stepId"]."' LIMIT 1");
			$row["steps"][$key]["prevStep"] = $this->Util()->DB()->GetRow();
			
			$row["steps"][$key]["prevStep"]["completed"] = $row["steps"][$key - 1]["stepCompleted"];
			$ii++;
			
		}//foreach
		
		$row["totalSteps"] = count($row["steps"]);

		$completo = explode("-", $row["fechaCompleta"]);
		$fechaInstancia = explode("-", $row["date"]);
		
		//Si se completo en un anio mayor es completo tardio
				
		$row["completoTardio"] = 'No';
		if($completo[0] < 2014 || ($completo[0] == 2014 && $completo[1] < 2))
			$row["completoTardio"] = "No";
		elseif($completo[0] > $fechaInstancia[0])
			$row["completoTardio"] = "Si";
		//Si el mes es mayor
		elseif($completo[1] > $fechaInstancia[1]) //Aqui
			$row["completoTardio"] = "Si";
		//Si el dia es mayor al dia de entrega
		elseif($completo[2] > $row["maxDay"])
			$row["completoTardio"] = "Si";
		
		if($row["completedSteps"] == $row["totalSteps"]){
      		if($row['status'] != "baja") {
				$this->Util()->DB()->setQuery("
				  UPDATE
					instanciaServicio
				  SET status = 'completa'
				  WHERE
					instanciaServicioId = '".$row["instanciaServicioId"]."'");
				$this->Util()->DB()->UpdateData();
        
        		$this->Util()->DB()->setQuery("
				  UPDATE
					instanciaServicio
				  SET fechaCompleta = '".date("Y-m-d")."'
				  WHERE
					instanciaServicioId = '".$row["instanciaServicioId"]."' AND fechaCompleta = '0000-00-00'");
				$this->Util()->DB()->UpdateData();
      		}//if
			
		}else{
		
      		if($row['status'] != "baja") {
        		$this->Util()->DB()->setQuery("
          		UPDATE instanciaServicio
          		SET status = 'activa'
          		WHERE instanciaServicioId = '".$row["instanciaServicioId"]."'");
        		$this->Util()->DB()->UpdateData();
      		}//if
			
		}//else

		return $row;
		
	}//Info
	
	function UploadControl()
	{
		global $User;
		$folder = DOC_ROOT."/tasks";
		$ext = strtolower(end(explode('.', $_FILES["file"]['name'])));

			$this->Util()->DB()->setQuery("
				SELECT MAX(version) FROM taskFile WHERE 
					servicioId = ".$_POST["servicioId"]." AND
					stepId = '".$_POST["stepId"]."' AND
					taskId = '".$_POST["taskId"]."' AND
					control = '".$_POST["control"]."'");
			$version = $this->Util()->DB()->GetSingle()+ 1;
			

		$target_path = $folder ."/".$_POST["servicioId"]."_".$_POST["stepId"]."_".$_POST["taskId"]."_".$_POST["control"]."_".$version.".".$ext;
		$target_path_path = basename( $_FILES["file"]['name']); 
		if(move_uploaded_file($_FILES["file"]['tmp_name'], $target_path)) {
				$this->Util()->DB()->setQuery("
					INSERT INTO `taskFile` 
					(
					`servicioId`, 
					`stepId`, 
					`taskId`, 
					`control`, 
					`version`, 
					`ext`, 
					`date`,
					`mime`
					) 
					VALUES 
					(
					'".$_POST["servicioId"]."', 
					'".$_POST["stepId"]."', 
					'".$_POST["taskId"]."', 
					'".$_POST["control"]."', 
					'".$version."', 
					'".$ext."', 
					'".date("Y-m-d")."', 
					'".$_FILES["file"]["type"]."'
				);");
				$this->Util()->DB()->InsertData();
        
				$result = $this->StatusById($this->instanciaServicioId);
                $this->Util()->DB()->setQuery("UPDATE instanciaServicio SET class = '".$result["class"]."' 
                WHERE instanciaServicioId = '".$this->instanciaServicioId."'");
                $this->Util()->DB()->UpdateData();
				//enviar al jefe inmediato
				if($version > 1)
				{
					$user = new User;
					$user->setUserId($User["userId"]);
					$userInfoPrev = $user->Info();
					//a quien
					if($userInfo['jefeContador'] != 0)
					{
						$enviarA = $userInfo['jefeContador'];
					}
					elseif($userInfo['jefeSupervisor'] != 0)
					{
						$enviarA = $userInfo['jefeSupervisor'];
					}
					elseif($userInfo['jefeGerente'] != 0)
					{
						$enviarA = $userInfo['jefeGerente'];
					}
					elseif($userInfo['jefeSocio'] != 0)
					{
						$enviarA = $userInfo['jefeSocio'];
					}
					
					$user->setUserId($enviarA);
					$userInfo = $user->Info();
					$subject = "El contador ".$userInfoPrev["name"]." ha actualizado un archivo";
					$body = "El archivo anterior y nuevo va adjunto en este correo.";
					$sendmail = new SendMail;
	
					$to = $userInfo["email"];
					$toName = $userInfo["name"];
					
					$versionAnt = $version - 1;
					
					$attachment = DOC_ROOT."/tasks/".$_POST["servicioId"]."_".$_POST["stepId"]."_".$_POST["taskId"]."_".$_POST["control"]."_".$version.".".$ext;
					$fileName = "ArchivoActualizado.".$ext;

					$attachment2 = DOC_ROOT."/tasks/".$_POST["servicioId"]."_".$_POST["stepId"]."_".$_POST["taskId"]."_".$_POST["control"]."_".$versionAnt.".".$ext;
					$fileName2 = "ArchivoAnterior.".$ext;
					
					$sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileName, $attachment2, $fileName2, "admin@avantikdads.com", "Administrador del Sistema") ;
				}
				$this->Util()->setError(0,'complete','Archivo guardado correctamente');
                $this->Util()->PrintErrors();
                return true;
			}
		else
		{
            $this->Util()->setError(0,'error','Error al guardar archivo');
            $this->Util()->PrintErrors();
			return false;
		}
		
	}
	function DeleteControl($id)
	{	
		$this->Util()->DB()->setQuery("SELECT * FROM `taskFile` WHERE taskFileId = '".$id."'");
		$file = $this->Util()->DB()->GetRow();
		
		$this->Util()->DB()->setQuery("DELETE FROM `taskFile` WHERE taskFileId = '".$id."'");
		$this->Util()->DB()->DeleteData();
		$result = $this->StatusById($this->instanciaServicioId);
				
        $this->Util()->DB()->setQuery("UPDATE instanciaServicio SET class = '".$result["class"]."' 
        WHERE instanciaServicioId = '".$this->instanciaServicioId."'");
        $this->Util()->DB()->UpdateData();
				
		$nomFile = $file['servicioId'].'_'.$file['stepId'].'_'.$file['taskId'].'_'.$file['control'].'_'.$file['version'].'.'.$file['ext'];
		
		@unlink(DOC_ROOT.'/tasks/'.$nomFile);
        $this->Util()->setError(0,'complete','Archivo eliminado correctamente');
        $this->Util()->PrintErrors();
        return true;
		return true;
	}
	/*
	 * funcion getStatusByComprobante | la sumatoria de monto total de cobranza debe ser por mes y tomar en cuenta manuales  y automaticas.
	 * recibe id del contrato, el año, y tipo
	 * tipo pueden ser
	 * A = todos los documentos
	 * I = todos los ingresos
	 * E = todos los egresos
	 * P = pagos
	 */
    function GetStatusByComprobante($contratoId,$year,$tipo='A'){
	    $ftrTipo = "";
	    if($tipo=='I')
	        $ftrTipo = " and a.tiposComprobanteId IN(1,3,4)";

	    $monthBase = array(1=>array('class'=>'#000000'),2=>array('class'=>'#000000'),3=>array('class'=>'#000000'),4=>array('class'=>'#000000'),
            5=>array('class'=>'#000000'),6=>array('class'=>'#000000'),7=>array('class'=>'#000000'),8=>array('class'=>'#000000'),
            9=>array('class'=>'#000000'),10=>array('class'=>'#000000'),11=>array('class'=>'#000000'),12=>array('class'=>'#000000'));
	    $months = array();
	    $new =array();
        $sql = "SELECT MONTH(a.fecha) as mes,year(fecha) as anio,a.comprobanteId, a.userId, sum(a.total) as total, a.fecha, `status`,sum(b.payments) as payment,
                a.version,a.xml FROM comprobante a 
                LEFT JOIN (select comprobanteId , sum(amount) as payments from payment where paymentStatus='activo' group by comprobanteId)  b ON a.comprobanteId=b.comprobanteId
                WHERE
				YEAR(a.fecha) = ".$year." AND MONTH(a.fecha) IN(1,2,3,4,5,6,7,8,9,10,11,12) AND a.userId = '".$contratoId."' AND a.status = '1' $ftrTipo
				GROUP BY MONTH(a.fecha) ORDER BY a.fecha ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        $noComplete=0;
        foreach($result as $key=>$value){
            //rellenar $new con meses que si tienen facturas
            if(!in_array($value['mes'],$months))
            {
                array_push($months,$value['mes']);
                $value['saldo'] =  $value['total']-$value['payment'];
                if($value["saldo"] > 1)
                {
                    $value["class"] = $value['payment']>0 ? "#FC0":"#ff0000";
                    $noComplete++;
                }
                else{
                    $value["class"] = "#00ff00";
                }
                $new[$value['mes']] =  $value;
            }
        }
        //recorrer los meses base y para los que no tiene facturas comprobar que no existen canceladas
        foreach($monthBase as $km=>$mes){
            if(key_exists($km,$new)){
               $monthBase[$km] = $new[$km];
            }else{
                $sql = "SELECT MONTH(a.fecha) as mes,year(fecha) as anio,a.comprobanteId, a.userId, sum(a.total) as total, a.fecha, `status` FROM comprobante a 
                WHERE
				YEAR(a.fecha) = ".$year." AND MONTH(a.fecha)='".$km."' AND a.userId = '".$contratoId."' AND a.status = '0' $ftrTipo
				GROUP BY MONTH(a.fecha) ORDER BY a.fecha ASC";
                $this->Util()->DB()->setQuery($sql);
                $row = $this->Util()->DB()->GetRow();
                if(!empty($row)){
                    $row['class'] ="#EFEFEF";
                    $row['payment'] =0;
                    $row['saldo'] =0;
                    $monthBase[$km]=$row;
                }

            }
        }
        //$new = array_replace_recursive($monthBase,$new);
        $data['serv'] = $monthBase;
        $data['noComplete']=$noComplete;
        return $data;
    }
	function StatusByComprobante($contratoId, $month , $year)
	{
		$sql = "SELECT comprobanteId, userId, total, fecha, `status` FROM comprobante WHERE
				YEAR(fecha) = ".$year." AND MONTH(fecha) = ".$month." AND userId = '".$contratoId."' AND status = '1'";

		$this->Util()->DB()->setQuery($sql);
		$data = $this->Util()->DB()->GetRow();
		if(!$data)
		{
			$data["class"] = "#000000";
			return $data;
		}
		$sqlQuery = "SELECT SUM(amount) FROM payment
						WHERE comprobanteId = '".$data["comprobanteId"]."'";
		$this->Util()->DB()->setQuery($sqlQuery);
		$data["payment"] = $this->Util()->DB()->GetSingle();
		$data['saldo'] = $data["total"] - $data["payment"];

		if($data["saldo"] > 1)
		{
			$data["class"] = "#ff0000";
		}
		else{
			$data["class"] = "#00ff00";
		}
		return $data;
	}
	function StatusByMonth($servicioId, $month , $year)
	{
		$sql = "SELECT class, instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE MONTH(instanciaServicio.date) = '".$month."' 
				AND YEAR(instanciaServicio.date) = '".$year."'
				AND (servicio.status != 'baja'
      			OR servicio.status != 'inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."' ORDER BY instanciaServicio.instanciaServicioId ASC";
		$this->Util()->DB()->setQuery($sql);
		$data = $this->Util()->DB()->GetRow();

		return $data;
	}
	function StatusById($id)
	{
		$this->Util()->DB()->setQuery("SELECT instanciaServicioId  FROM instanciaServicio 
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		WHERE 
			instanciaServicioId = '".$id."' 
			AND (servicio.status != 'baja' AND instanciaServicio.status != 'inactiva')");
			//echo $this->Util()->DB()->query;
		$instancia = $this->Util()->DB()->GetRow();

		$totalPorcentajeSteps = 0;
		$totalPorcentajeStepsCompleted = 0;

		if(count($instancia) == 0)
		{
			$totalPorcentajeSteps = 100;
			$totalPorcentajeStepsCompleted = 100;
//					continue;
		}
		$this->setInstanciaServicioId($instancia["instanciaServicioId"]);
		$instancia = $this->Info();				
		
		$fechaCompletoServicio = strtotime($instancia["fechaCompleta"]);

		if($instancia["totalSteps"] > 0)
		{
			$instancia["porcentajeSteps"] = floor($instancia["completedSteps"] * 100 / $instancia["totalSteps"]);
		}
		else
		{
			$instancia["porcentajeSteps"] = 0;
		}

		if($instancia["porcentajeSteps"] == 0)
			$class = "PorIniciar";
		elseif($instancia["porcentajeSteps"] > 0 && $instancia["porcentajeSteps"] < 70)
			$class = "Iniciado";
		elseif($instancia["porcentajeSteps"] >= 70 && $instancia["porcentajeSteps"] < 100)
			$class = "PorCompletar";
		elseif($instancia["porcentajeSteps"] >= 100)
		{
      $class = "Completo";
			if($instancia["completoTardio"] == "Si")
			{
				$class = "CompletoTardio";
			}
		}
		else
			$class = "PorIniciar";
			
		
		$data["instanciaServicioId"] = $instancia["instanciaServicioId"];
		$data["class"] = $class;
		
		return $data;
	}
	public function infoWorkflow(){

        $this->Util()->DB()->setQuery("SELECT c.periodicidad,a.status,a.instanciaServicioId,c.departamentoId,a.date,d.name as razon,e.nameContact as cliente  FROM instanciaServicio a 
		LEFT JOIN servicio b ON a.servicioId = b.servicioId
		LEFT JOIN tipoServicio c ON b.tipoServicioId = c.tipoServicioId
		LEFT JOIN contract d ON b.contractid = d.contractId
		LEFT JOIN customer e ON d.customerId = e.customerId
		WHERE a.instanciaServicioId = '".$this->instanciaServicioId."' ");
        $row = $this->Util()->DB()->GetRow();
        return  $row;
    }
    public function listStepByWorkflow($filter){
         global $task;
        $date = explode("-", $filter["finstancia"]);
        if($filter["tipoServicioId"] == SERVICIO_CONTABILIDAD && $date["0"] < 2016)
        {
            $this->Util()->DB()->setQuery("SELECT * FROM step 
		WHERE stepId != 103 AND servicioId = '".$filter["tipoServicioId"]."'");
        }
        elseif($filter["tipoServicioId"] == 3 && $date["0"] < 2016)
        {
            $this->Util()->DB()->setQuery("SELECT * FROM step 
		WHERE stepId != 103 AND servicioId = '".$filter["tipoServicioId"]."'");
        }
        else
        {
            $this->Util()->DB()->setQuery("SELECT * FROM step 
		WHERE servicioId = '".$filter["tipoServicioId"]."'");

        }
        //Get Steps
        $steps = $this->Util()->DB()->GetResult();
        $data["completedSteps"] = 0;
        $ii=1;
        foreach($steps as $key => $value){
            $task->setStepId($value["stepId"]);
            $task->setWorkflowId($_POST['instanciaId']);
            $tasks =  $task->checkTasksByStep();
            $steps[$key]['isComplete'] = $tasks["stepCompleted"];
            $steps[$key]['instanciaId'] = $_POST["instanciaId"];
            $steps[$key]['class'] = $tasks["class"];
            $steps[$key]['step'] = $ii;
            $ii++;

        }//foreach
        return $steps;
    }
    /*
     * funcion getDetailCobranzaByContract obtiene las facturas del contrato dado
     * del mes y año proporcionado.
     */
    public function getDetailCobranzaByContract($contractId,$year,$month,$cancelados=true){
        global $monthsComplete;
        if(!$cancelados)
            $filtro =  " and a.status='1' ";

       $sql =  "select case
                   when a.total<=sum(b.amount)
                   then 'Pagado'
                   else
                   'Pendiente por liquidar' 
                 end as pagado,a.folio,a.serie,a.total,a.status,a.comprobanteId,sum(b.amount) as totalPagos,a.procedencia,a.fecha,a.version 
                from comprobante a
                left join payment b on a.comprobanteId=b.comprobanteId  where a.userId='$contractId' and month(a.fecha)=$month and year(a.fecha)=$year $filtro and a.tiposComprobanteId IN(1,3,4) 
                group by a.comprobanteId order by a.fecha desc";
        $this->Util()->DB()->setQuery($sql);
        $result['items'] = $this->Util()->DB()->GetResult();

        $sql1= "select name from contract where contractId = '".$contractId."' ";
        $this->Util()->DB()->setQuery($sql1);
        $result['razon'] = $this->Util()->DB()->GetSingle();
        $result['mes'] = $monthsComplete["0".$month];
        return $result;
    }
    public function getRowCobranzaBono($contratoId,$year,$tipo='A',$meses=[],$whitIva=true){
        $ftrTipo = "";
        if($tipo=='I')
            $ftrTipo = " and a.tiposComprobanteId IN(1,3,4)";

        if($whitIva)
            $strIva = " sum(a.total) as total";
        else
            $strIva ="  sum(a.subTotal) as total";

         //create monthBase
        foreach($meses as $mes)
            $monthBase[$mes]['class'] = '#000000';

        $months = array();
        $new =array();
        $sql = "SELECT MONTH(a.fecha) as mes,year(fecha) as anio,a.comprobanteId, a.userId, $strIva, a.fecha, `status`,sum(b.payments) as payment,
                a.version,a.xml,a.tasaIva FROM comprobante a 
                LEFT JOIN (select comprobanteId , sum(amount) as payments from payment where paymentStatus='activo' group by comprobanteId)  b ON a.comprobanteId=b.comprobanteId
                WHERE
				YEAR(a.fecha) = ".$year." AND MONTH(a.fecha) IN(".implode(',',$meses).") AND a.userId = '".$contratoId."' AND a.status = '1' $ftrTipo
				GROUP BY MONTH(a.fecha) ORDER BY a.fecha ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        $noComplete=0;
        $totalCobrado = 0;
        foreach($result as $key=>$value){
            //rellenar $new con meses que si tienen facturas
            $pago = $value['payment']/(1+($value['tasaIva']/100));
            if(!in_array($value['mes'],$months))
            {
                array_push($months,$value['mes']);
                $value['saldo'] =  $value['total']-$value['payment'];
                $totalCobrado +=$pago;
                if($value["saldo"] >0.1)//margen de .1 de rror en saldo
                {
                    $value["class"] = $value['payment']>0 ? "#FC0":"#ff0000";
                    $noComplete++;
                }
                else{
                    $value["class"] = "#00ff00";
                }
                $new[$value['mes']] =  $value;
            }
        }
        //recorrer los meses base y para los que no tiene facturas comprobar que no existen canceladas
        foreach($monthBase as $km=>$mes){
            if(key_exists($km,$new)){
                $monthBase[$km] = $new[$km];
            }else{
                $sql = "SELECT MONTH(a.fecha) as mes,year(fecha) as anio,a.comprobanteId, a.userId, sum(a.total) as total, a.fecha, `status` FROM comprobante a 
                WHERE
				YEAR(a.fecha) = ".$year." AND MONTH(a.fecha)='".$km."' AND a.userId = '".$contratoId."' AND a.status = '0' $ftrTipo
				GROUP BY MONTH(a.fecha) ORDER BY a.fecha ASC";
                $this->Util()->DB()->setQuery($sql);
                $row = $this->Util()->DB()->GetRow();
                if(!empty($row)){
                    $row['class'] ="#EFEFEF";
                    $row['payment'] =0;
                    $row['saldo'] =0;
                    $monthBase[$km]=$row;
                }
            }
        }
        //$new = array_replace_recursive($monthBase,$new);
        $data['serv'] = $monthBase;
        $data['noComplete']=$noComplete;
        $data['totalCobrado']=$totalCobrado;
        return $data;
    }

}




?>