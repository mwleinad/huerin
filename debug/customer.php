<?php
	
	include_once('../init.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');
		
	if(!$_GET["tipo"])
	{
		$_GET["tipo"] = "Activos";
	}
	
	$result = SuggestCustomerCatalog("", $type = "subordinado", $customerId = 0, $_GET["tipo"]);
	//$result = $customer->SuggestCustomerContract('MARIANA');
	echo '<pre>';
	print_r($result);
  
  function SuggestCustomerCatalog($like = "", $type = "subordinado", $customerId = 0, $tipo = "",$limite=false){
  
  	global $User, $page, $util;
      
	if ($customerId) {
		$add = " AND customerId = '".$customerId."' ";
		if ($page == "report-servicio") {
			$User["roleId"] = 1;
		}
	}
    
    if ($tipo == "Activos") {
      	$addActivo = " AND active = '1' ";
    } elseif ($tipo == "Inactivos") {
      	$addActivo = " AND active = '0' ";
    } else {
      	$addActivo = " AND active = '1' ";
    }

    if (strlen($like) > 1) {
      $addWhere = " AND (contract.name LIKE '%".$like."%' 
                OR contract.rfc LIKE '%".$like."%' 
                OR customer.nameContact LIKE '%".$like."%')  ";
    }
	
    if($limite)
	   $addLimite =" LIMIT 15";
	 else 
	 	$addLimite ="";
   
   	$sql = "SELECT 
            customer.customerId,customer.fechaAlta, customer.nameContact, contract.contractId, contract.name,
			customer.phone, customer.email, customer.password,customer.active 
          	FROM customer
			LEFT JOIN contract ON contract.customerId = customer.customerId	
          	WHERE 
            1 ".$sqlActive." ".$add." ".$addActivo." ".$addWhere."
			GROUP BY customerId 	
          	ORDER BY 
            nameContact ASC ".$addLimite."";
    $util->DB()->setQuery($sql);   
    $result = $util->DB()->GetResult();
		
    $count = 0;
    $personal = new Personal;
    $personal->setPersonalId($User["userId"]);
    $subordinados = $personal->Subordinados();
  	
    foreach ($result as $key => $val) {
		
    	$sql = "SELECT
                contract.*
              FROM 
                contract
              WHERE 
                customerId = '".$val["customerId"]."' AND activo = 'Si'
              ORDER BY 
                name ASC";      
      	$util->DB()->setQuery($sql);
      	$result[$key]["contracts"] = $util->DB()->GetResult();
		$result[$key]["servicios"] = count($result[$key]["contracts"]);
					
		$countContracts = count($result[$key]["contracts"]);
		
		//de todas las cuentas, revisar si al menos una esta asignada a nosotros
      	$showCliente = true;
       	$result[$key]["servicios"] = 0;
      	
		if($countContracts > 0){
		
        	$showCliente = false;
      		foreach ($result[$key]["contracts"] as $keyContract => $value) {
				
          		$contract = new Contract;
          		$conPermiso = $contract->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);

				//checar servicios del contrato para saber si lo debemos mostrar o no
				$util->DB->setQuery(
				  "SELECT 
					servicioId, nombreServicio, departamentoId 
				  FROM 
					servicio 
				  LEFT JOIN 
					tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
				  WHERE 
					contractId = '".$value["contractId"]."' AND servicio.status = 'activo'          
				  ORDER BY 
					nombreServicio ASC"
				);
				$serviciosContrato = $util->DB()->GetResult();
          
          		$cUser = new User;
				if(count($serviciosContrato) == 0 && ($User["roleId"] == 1 || $User["roleId"] == 4) ){
					$showCliente = true;				
				}else{				
          			//agregar o no agregar servicio a arreglo de contratos?
					foreach ($serviciosContrato as $servicio) {
	
						$responsableId = $result[$key]["contracts"][$keyContract]['permisos'][$servicio['departamentoId']];
						$cUser->setUserId($value["responsableCuenta"]);
				
						$userInfo = $cUser->Info();
						
						$result[$key]["contracts"][$keyContract]["responsable"] = $userInfo;
	
						if($type == "propio") {
							$subordinadosPermiso = array($User["userId"]);
						}else {
			
						  $subordinadosPermiso = array();
						  foreach ($subordinados as $sub) {
							array_push($subordinadosPermiso, $sub["personalId"]);
						  }
						  array_push($subordinadosPermiso, $User["userId"]);
						}
						
						//si es usuario de contabilidad
						if ($User["roleId"] == 1 || $User["roleId"] == 4) {
							$result[$key]["contracts"][$keyContract]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
						} else {
		
							foreach ($subordinadosPermiso as $usuarioPermiso) {
								if (in_array($usuarioPermiso, $conPermiso)) {
								 $result[$key]["contracts"][$keyContract]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
								 break;
								}	
							}
							
						}//else
				
          			}//foreach
					
					if (count($result[$key]["contracts"][$keyContract]['instanciasServicio']) > 0) {
						$showCliente = true;
						$result[$key]["servicios"]++;						
					} else {
						unset($result[$key]["contracts"][$keyContract]);						
					}	
		  
				}//else 

        	}//foreach
			
		}else{				
			$result[$key]["contracts"][0]["customerId"] = $val["customerId"];
			$result[$key]["contracts"][0]["nameContact"] = $val["nameContact"];
			$result[$key]["contracts"][0]["fake"] = 1;
		}
			
		if($showCliente)
			echo 'si';
		else
			echo 'No';
        
		if (($showCliente === false && in_array($User['roleId'],explode(',',ROLES_LIMITADOS))) || ($showCliente === false && $type == "propio")) {
          unset($result[$key]);
        }        
    }
    return $result;
  }
  
  //*******************
  
  function SuggestCustomerContract($like = "", $type = "subordinado", $customerId = 0, $tipo = "")
  {
    global $User, $page;
    //print_r($_POST);
    if ($this->active) {
      $sqlActive = " AND active = '1' ";
    }
    
    if ($customerId) {
      $add = " AND customerId = '".$customerId."' ";
      if ($page == "report-servicio") {
        $User["roleId"] = 1;
      }
    }
    
    if ($tipo == "Activos") {
      $addActivo = " AND active = '1' ";
    } elseif ($tipo == "Inactivos") {
      $addActivo = " AND active = '0' ";
    } else {
      $addActivo = " AND active = '1' ";
    }

    if (strlen($like) > 1) {
      $addWhere = " AND (contract.name LIKE '%".$like."%' 
                OR contract.rfc LIKE '%".$like."%' 
                OR customer.nameContact LIKE '%".$like."%')  ";
    }
    
    $sql = "SELECT 
            customer.customerId, customer.nameContact, contract.contractId, contract.name 
          FROM 
            customer
					LEFT JOIN contract ON contract.customerId = customer.customerId	
          WHERE 
            1 ".$sqlActive." ".$add." ".$addActivo." ".$addWhere."
					GROUP BY customerId 	
          ORDER BY 
            nameContact ASC LIMIT 15";
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();
    $count = 0;
    $personal = new Personal;
    $personal->setPersonalId($User["userId"]);
    $subordinados = $personal->Subordinados();

    foreach ($result as $key => $val) {
      $sql = "SELECT
                contract.*
              FROM 
                contract
              WHERE 
                customerId = '".$val["customerId"]."' AND activo = 'Si'
              ORDER BY 
                name ASC";
      
      $this->Util()->DB()->setQuery($sql);
      $result[$key]["contracts"] = $this->Util()->DB()->GetResult();
      $result[$key]["servicios"] = count($result[$key]["contracts"]);

      //de todas las cuentas, revisar si al menos una esta asignada a nosotros
        $showCliente = false;
        $result[$key]["servicios"] = 0;
      //        echo "<pre>";
      foreach ($result[$key]["contracts"] as $keyContract => $value) {
          $contract = new Contract;
          $conPermiso = $contract->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);

          //checar servicios del contrato para saber si lo debemos mostrar o no
          $this->Util()->DB->setQuery(
              "SELECT 
                servicioId, nombreServicio, departamentoId 
              FROM 
                servicio 
              LEFT JOIN 
                tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
              WHERE 
                contractId = '".$value["contractId"]."' AND servicio.status = 'activo'          
              ORDER BY 
                nombreServicio ASC"
          );
          $serviciosContrato = $this->Util()->DB()->GetResult();
          
          $cUser = new User;
          //agregar o no agregar servicio a arreglo de contratos?
          foreach ($serviciosContrato as $servicio) {
            //$conPermiso = $result[$key]["contracts"][$keyContract]['permisos'];
            $responsableId = $result[$key]["contracts"][$keyContract]['permisos'][$servicio['departamentoId']];

            //            $cUser->setUserId($responsableId);
            $cUser->setUserId($value["responsableCuenta"]);
            $userInfo = $cUser->Info();
            $result[$key]["contracts"][$keyContract]["responsable"] = $userInfo;
            
            if ($type == "propio") {
              $subordinadosPermiso = array( 
                $User["userId"]);
            } else {
              //echo $User["userId"];
              //print_r($subordinados);
              $subordinadosPermiso = array();
              foreach ($subordinados as $sub) {
                array_push($subordinadosPermiso, $sub["personalId"]);
              }
              array_push($subordinadosPermiso, $User["userId"]);
            }
            //si es usuario de contabilidad
            if ($User["roleId"] == 1 || $User["roleId"] == 4) {
              $result[$key]["contracts"][$keyContract]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
            } else {
              //print_r($value);
              foreach ($subordinadosPermiso as $usuarioPermiso) {
                if (in_array($usuarioPermiso, $conPermiso)) {
                  $result[$key]["contracts"][$keyContract]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
                  break;
                }
              }
            } 
          }
          
          if (count($result[$key]["contracts"][$keyContract]['instanciasServicio']) > 0) {
            $showCliente = true;
            $result[$key]["servicios"]++;
          } else {
            unset($result[$key]["contracts"][$keyContract]);
          }

        }

        if (($showCliente === false && ($User["roleId"] > 2 && $User["roleId"] < 4)) || ($showCliente === false && $type == "propio")) {
          unset($result[$key]);
        }

        
    }
    return $result;
  }	
	
	exit;

?>	