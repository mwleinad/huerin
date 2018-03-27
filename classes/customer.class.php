<?php 
/** 
* customer.class.php
*
* PHP version 5
*
* @category Desarrollo
* @package  Customer.class.php
* @author   Daniel Lopez <desarrollos@avantika.com.mx>
* @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link     http://avantika.com.mx
**/

/**
* Customer
*
* @category Desarrollo
* @package  Customer.class.php
* @author   Daniel Lopez <desarrollos@avantika.com.mx>
* @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link     http://avantika.com.mx
*/

class Customer extends Main
{
  private $customerId;
  private $name;
  private $phone;
  private $email;  
  private $active;
  private $password;

  public function setPassword($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Password");
    $this->password = $value;
  }

  public function getPassword($value)
  {
    return $this->password;
  }

  private $fechaAlta;
  public function setFechaAlta($value)
  {
    if ($this->Util()->ValidateRequireField($value, "Fecha Alta")) {
      $value = $this->Util()->FormatDateMySql($value);
    }
    $this->fechaAlta = $value;
  }

  private $encargadoCuenta;
  public function setEncargadoCuenta($value)
  {
    $this->Util()->ValidateInteger($value);
    $this->encargadoCuenta = $value;
  }

  private $responsableCuenta;
  public function setResponsableCuenta($value)
  {
    $this->Util()->ValidateInteger($value);
    $this->responsableCuenta = $value;
  }

  private $nameContact;
  public function setNameContact($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, "Nombre Contacto");
    $this->nameContact = $value;
  }

  public function getNameContact($value)
  {
    return $this->nameContact;
  }

  private $street;
  public function setStreet($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Calle");
    $this->street = $value;
  }

  public function getStreet($value)
  {
    return $this->street;
  }
  
  
  private $numExt;
  public function setNumExt($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Numero Exterior");
    $this->numExt = $value;
  }

  public function getNumExt($value)
  {
    return $this->numExt;
  }
  
  private $numInt;
  public function setNumInt($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Numero Interior");
    $this->numInt = $value;
  }

  public function getNumInt($value)
  {
    return $this->numInt;
  }
  
  private $colony;
  public function setColony($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Colonia");
    $this->colony = $value;
  }

  public function getColony($value)
  {
    return $this->colony;
  }
  
  private $city;
  public function setCity($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Ciudad");
    $this->city = $value;
  }

  public function getCity($value)
  {
    return $this->city;
  }
  
  private $state;
  public function setState($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Estado");
    $this->state = $value;
  }

  public function getState($value)
  {
    return $this->state;
  }

  public function setCustomerId($value)
  {
    $this->Util()->ValidateInteger($value);
    $this->customerId = $value;
  }

  public function setName($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Nombre");
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
    
  public function setActive($value)
  {
    $this->active = $value;    
  }

	private $noFactura13;
  public function setNoFactura13($value)
  {
    $this->noFactura13 = $value;    
  }

  public function Search($tipo = "subordinado", $type = "")
  {
    global $User;
    
    if ($User['departamentoId'] != SERVICIO_CONTABILIDAD) {
      $depto = $User['departamentoId'];
    }
    
    if ($this->active) {
      $sqlActive = " AND active = '1'";
    }
    if ($type) {
      if ($type == "Inactivo" || $type == "Inactivos") {
        $add = " AND customer.active = '0'";
      } else {
        $add = " AND customer.active = '1'";
      }
    }
    //    $_POST["valur"] = $_POST["valur"];
    $sql = "SELECT 
              customer.* 
            FROM 
              customer
            LEFT JOIN
              contract ON contract.customerId = customer.customerId
            LEFT JOIN 
              servicio ON servicio.contractId=contract.contractId
            LEFT JOIN 
              tipoServicio 
              ON tipoServicio.tipoServicioId=servicio.tipoServicioId 
              AND tipoServicio.departamentoId='".$depto."'
            WHERE 
              1 ".$add." AND (
                         nameContact LIKE '%".$_POST["valur"]."%' || 
                         ((contract.name LIKE '%".$_POST["valur"]."%' ||
                          contract.rfc LIKE '%".$_POST["valur"]."%') && contract.activo = 'Si')
                        )        
              ".$sqlActive."
            GROUP BY 
              customerId  
            ORDER BY 
              nameContact ASC
            ";
    //        LIMIT 20";
    
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();
    foreach ($result as $key => $val) {
      //$result[$key]["nameContact"] = utf8_encode($result[$key]["nameContact"]);      
      $sql = "SELECT 
              * 
             FROM 
              contract 
             WHERE 
              customerId = '".$val["customerId"]."'
             ORDER BY 
              name ASC";
      
      $this->Util()->DB()->setQuery($sql);
      $result[$key]["contracts"] = $this->Util()->DB()->GetResult();
      $result[$key]["servicios"] = count($result[$key]["contracts"]);
      
      //de todas las cuentas, revisar si al menos una esta asignada a nosotros
        $showCliente = false;
        $result[$key]["servicios"] = 0;
      foreach ($result[$key]["contracts"] as $keyContract => $value) {
          //checar subordinados o encargados
          $cUser = new User;
          $cUser->setUserId($value["responsableCuenta"]);
          $userInfo = $cUser->Info();
        if ($tipo == "propio") {
          if ($User["userId"] == $value["responsableCuenta"]) {
              $showCliente = true;
              $result[$key]["servicios"]++;
          }
        } else {
          if (($User["userId"] == $value["responsableCuenta"] 
              || $userInfo["jefeContador"] == $User["userId"] 
              || $userInfo["jefeSupervisor"] == $User["userId"] 
              || $userInfo["jefeGerente"] == $User["userId"] 
              || $userInfo["jefeSocio"] == $User["userId"]) 
              || $User["roleId"] < 2
          ) {
              $showCliente = true;
              $result[$key]["servicios"]++;
          }
        }
      }
        
      if ($showCliente === false && $User["roleId"] > 2) {
        unset($result[$key]);
      }
    }
    return $result;
  }

	public function Enumerate($type = "subordinado", $customerId = 0, $tipo = ""){
	
    	global $User, $page;
		   
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
    
    	$sql = "SELECT 
				* 
			  FROM 
				customer
			  WHERE 
				1 ".$sqlActive." ".$add." ".$addActivo."
			  ORDER BY 
				nameContact ASC";    
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
	
					//$cUser->setUserId($responsableId);
					$cUser->setUserId($value["responsableCuenta"]);
					$userInfo = $cUser->Info();
					$result[$key]["contracts"][$keyContract]["responsable"] = $userInfo;
            
					if ($type == "propio") {
						$subordinadosPermiso = array( 
						$User["userId"]);
					} else {
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
            		} 
          		}
          
          		if (count($result[$key]["contracts"][$keyContract]['instanciasServicio']) > 0) {
            		$showCliente = true;
            		$result[$key]["servicios"]++;
          		} else {
            		unset($result[$key]["contracts"][$keyContract]);
          		}

        	}

        	if (($showCliente === false && in_array($User['roleId'],explode(',',ROLES_LIMITADOS))) || ($showCliente === false && $type == "propio")) {
          unset($result[$key]);
        	}
			
    	}//foreach

		return $result;
		
	}//Enumerate
	
	public function EnumerateBest($type = 'subordinado', $customerId = 0, $tipo = ""){
	
    	global $page;
		
		$User = $_SESSION['User'];
		       
    	if($tipo == 'Activos')
      		$addActivo = " AND active = '1' ";
    	elseif($tipo == 'Inactivos') 
      		$addActivo = " AND active = '0' ";
    	else
      		$addActivo = " AND active = '1' ";
    	    
    	$sql = "SELECT * FROM customer
				WHERE 1 ".$sqlActive." ".$add." ".$addActivo."
			  	ORDER BY nameContact ASC";    
    	$this->Util()->DB()->setQuery($sql);
    	$result = $this->Util()->DB()->GetResult();
    			
		$count = 0;
		
    	$personal = new Personal;		
    	$personal->setPersonalId($User["userId"]);
    	$subordinados = $personal->Subordinados();
    	
		$clientes = array();
    	foreach ($result as $key => $val) {
			
			$card = $val;
			
			$sql = "SELECT contract.*
					FROM contract
					WHERE customerId = '".$val["customerId"]."' 
					AND activo = 'Si'
					ORDER BY name ASC";      
			$this->Util()->DB()->setQuery($sql);
			$resContracts = $this->Util()->DB()->GetResult();
			$card["servicios"] = count($card["contracts"]);
			
			//De todas las cuentas, revisar si al menos una esta asignada a nosotros
			$showCliente = false;
			$card["servicios"] = 0;
			
			$contract = new Contract;
			
			$contratos = array();
      		foreach ($resContracts as $keyContract => $value) {
				
				$card2 = $value;
				
				$conPermiso = $contract->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);
				
          		//Checar servicios del contrato para saber si lo debemos mostrar o no
				$sql = "SELECT servicioId, nombreServicio, departamentoId 
						FROM servicio 
						LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
						WHERE contractId = '".$value["contractId"]."' 
						AND servicio.status = 'activo' 
						ORDER BY nombreServicio ASC";
          		$this->Util()->DB->setQuery($sql);
          		$serviciosContrato = $this->Util()->DB()->GetResult();
          
          		$cUser = new User;
          	
				//Agregar o no agregar servicio al arreglo de contratos
				
				$servicios = array();
          		foreach ($serviciosContrato as $servicio) {
					
					$card3 = $servicio;
					
					$cUser->setUserId($value["responsableCuenta"]);
					$userInfo = $cUser->Info();
					$card2["responsable"] = $userInfo;
					
					$subordinadosPermiso = array($User["userId"]);
								
					if($type == "subordinado"){					  	
					  	foreach ($subordinados as $sub) {
							array_push($subordinadosPermiso, $sub["personalId"]);
						}
            		}
					
            	/*
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
            		} 
					�*/
					
					$servicios[] = $card3;
					
          		}//foreach
				
				$card2['instanciasServicio'] = $servicios;
				
          		/*
          		if (count($result[$key]["contracts"][$keyContract]['instanciasServicio']) > 0) {
            		$showCliente = true;
            		$result[$key]["servicios"]++;
          		} else {
            		unset($result[$key]["contracts"][$keyContract]);
          		}
			*/
				
				$contratos[] = $card2;
			
        	}//foreach
			
			$card['contracts'] = $contratos;
			
			/*
        	if (($showCliente === false && ($User["roleId"] > 2 && $User["roleId"] < 4)) || ($showCliente === false && $type == "propio")) {
          unset($result[$key]);
        	}
			*/
			
			$clientes[] = $card;
			
    	}//foreach
		
		return $clientes;
		
	}//EnumerateBest

  public function EnumerateNameOnly($type = "subordinado", $customerId = 0, $tipo = "")
  {
    global $User;
    if ($this->active) {
      $sqlActive = " AND active = '1' ";
    }
    
    if ($customerId) {
      $add = " AND customerId = '".$customerId."' ";
    }
    
    if ($tipo == "Activos") {
      $addActivo = " AND active = '1' ";
    } elseif ($tipo == "Inactivos") {
      $addActivo = " AND active = '0' ";
    } else {
      $addActivo = " AND active = '1' ";
    }
    
    $sql = "SELECT 
              customerId, nameContact 
            FROM 
              customer
            WHERE 
              1 ".$sqlActive." ".$add." ".$addActivo."
            ORDER BY 
              nameContact ASC";
    
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();
    
    $count = 0;
    foreach ($result as $key => $val) {
      $sql = "SELECT 
                contractId,  responsableCuenta, name
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
        foreach ($result[$key]["contracts"] as $keyContract => $value) {
          //checar subordinados o encargados
          $cUser = new User;
          $cUser->setUserId($value["responsableCuenta"]);
          $userInfo = $cUser->Info();
          if ($type == "propio") {
            if ($User["userId"] == $value["responsableCuenta"]) {
              $showCliente = true;
              $result[$key]["servicios"]++;
            } elseif ($User["roleId"] == 4) {
              $showCliente = true;
              $result[$key]["servicios"]++;
            }
          } else {
            if (
              ($User["userId"] == $value["responsableCuenta"] 
              || $userInfo["jefeContador"] == $User["userId"] 
              || $userInfo["jefeSupervisor"] == $User["userId"] 
              || $userInfo["jefeGerente"] == $User["userId"] 
              || $userInfo["jefeSocio"] == $User["userId"]) 
              || in_array($User['roleId'],explode(',',ROLES_LIMITADOS))
            ) {
              $showCliente = true;
              $result[$key]["servicios"]++;
            }
          }
        }

      if (($showCliente === false && in_array($User['roleId'],explode(',',ROLES_LIMITADOS))) || ($showCliente === false && $type == "propio")) {
          unset($result[$key]);
      }
        
        //$result[$key]["phone"] = str_replace("/", " ", $result[$key]["phone"]);
        //$result[$key]["email"] = str_replace("/", " ", $result[$key]["email"]);
    }
    return $result;
  }
  
  public function Info()
  {
    $this->Util()->DB()->setQuery(
        "SELECT 
          * 
        FROM 
          customer 
        WHERE 
          customerId = '".$this->customerId."'"
    );
    $this->Util()->DB()->query;
    $row = $this->Util()->DB()->GetRow();
    
      $sql = "SELECT 
                * 
              FROM 
                personal 
              WHERE 
                personalId = '".$row["encargadoCuenta"]."'";
      
      $this->Util()->DB()->setQuery($sql);
      $row["encargadoCuentaData"] = $this->Util()->DB()->GetRow();

      $sql = "SELECT * FROM 
            personal WHERE personalId = '".$row["responsableCuenta"]."'";
      $row["responsableCuentaData"] = $this->Util()->DB()->GetRow();

      $row["fechaMysql"] = $this->Util()->FormatDateMysql($row["fechaAlta"]);
    
    return $row;
  }

  public function InfobyName($name)
  {
    $this->Util()->DB()->setQuery("
        SELECT 
          * 
        FROM 
          customer 
        WHERE 
          nameContact LIKE '%".$name."%'"
    );
    $row = $this->Util()->DB()->GetRow();
    
      $sql = "SELECT 
                * 
              FROM 
                personal 
              WHERE 
                personalId = '".$row["encargadoCuenta"]."'";
      
      $this->Util()->DB()->setQuery($sql);
      $row["encargadoCuentaData"] = $this->Util()->DB()->GetRow();

      $sql = "SELECT 
              * 
            FROM 
              personal 
            WHERE 
              personalId = '".$row["responsableCuenta"]."'";
      $row["responsableCuentaData"] = $this->Util()->DB()->GetRow();

      $row["fechaMysql"] = $this->Util()->FormatDateMysql($row["fechaAlta"]);
    
    return $row;
  }


  public function Edit()
  {
    global $User,$log;
    if($this->Util()->PrintErrors()){ return false; }

    $oldData = $this->Info();

    $this->Util()->DB()->setQuery("
      UPDATE
        customer
      SET        
        `name` = '".$this->name."',
        phone = '".$this->phone."',
        email = '".$this->email."',        
        nameContact = '".$this->nameContact."',        
        password = '".$this->password."',        
        noFactura13 = '".$this->noFactura13."',        
        encargadoCuenta = '".$this->encargadoCuenta."',        
        responsableCuenta = '".$this->responsableCuenta."',        
        fechaAlta = '".$this->fechaAlta."',        
        active = '".$this->active."'
      WHERE customerId = '".$this->customerId."'"
    );
    $this->Util()->DB()->UpdateData();

    $sql = "SELECT * FROM customer WHERE customerId = '".$this->customerId."'";
    $this->Util()->DB()->setQuery($sql);
    $newData = $this->Util()->DB()->GetRow();

    //Guardamos el Log
    $log->setPersonalId($User['userId']);
    $log->setFecha(date('Y-m-d H:i:s'));
    $log->setTabla('customer');
    $log->setTablaId($this->customerId);
    $log->setAction('Update');
    $log->setOldValue(serialize($oldData));
    $log->setNewValue(serialize($newData));
    $log->Save();

    //actualizar historial de customer de forma independiente(analizar si es conveniente dejarlo)
    $this->Util()->DB()->setQuery("
			INSERT INTO
				customerChanges
			(
				`customerId`,
				`status`,
				`oldData`,
				`newData`,
				`personalId`
		)
		VALUES
		(
				'".$this->customerId."',
				'".$newData["active"]."',
				'".urlencode(serialize($oldData))."',
				'".urlencode(serialize($newData))."',
				'".$User["userId"]."'
		);");
    $this->Util()->DB()->InsertData();

    $this->Util()->setError(10046, "complete", "El cliente fue Actualizado correctamente");
    $this->Util()->PrintErrors();
    return true;
  }

  public function Save()
  {
    global $User;
    if($this->Util()->PrintErrors()){ return false; }

    $this->Util()->DB()->setQuery("
      INSERT INTO
        customer
      (
        `name`,        
        phone,
        email,        
        nameContact,        
        password,        
        responsableCuenta,        
        encargadoCuenta,        
        fechaAlta,        
        active
    )
    VALUES
    (
        '".$this->name."',        
        '".$this->phone."',
        '".$this->email."',        
        '".$this->nameContact."',        
        '".$this->password."',        
        '".$this->responsableCuenta."',        
        '".$this->encargadoCuenta."',        
        '".$this->fechaAlta."',        
        '".$this->active."'
    );");
    $customerId = $this->Util()->DB()->InsertData();

    $sql = "SELECT * FROM customer WHERE customerId = '".$customerId."'";
    $this->Util()->DB()->setQuery($sql);
    $newData = $this->Util()->DB()->GetRow();

    //actualizar historial
    $this->Util()->DB()->setQuery("
			INSERT INTO
				customerChanges
			(
				`customerId`,
				`status`,
				`oldData`,
				`newData`,
				`personalId`
		)
		VALUES
		(
				'".$customerId."',
				'".$newData["active"]."',
				'',
				'".urlencode(serialize($newData))."',
				'".$User["userId"]."'
		);");
    $this->Util()->DB()->InsertData();
    $this->Util()->setError(10045, "complete", "El cliente fue agregado correctamente.");
    $this->Util()->PrintErrors();
    return true;
  }

  public function Delete()
  {
    global $User;
    if($this->Util()->PrintErrors()){ return false; }

/*    $this->Util()->DB()->setQuery("
      DELETE FROM
        customer
      WHERE
        customerId = '".$this->customerId."'");
*/
    
    $info = $this->Info();
    
    if($info["active"] == '1')
    {
      $active = 0;
      $complete = "El cliente fue dado de baja correctamente";
    }
    else
    {
      $active = 1;
      $complete = "El cliente fue dado de alta correctamente";
    }
    
    $this->Util()->DB()->setQuery("
      UPDATE 
        customer
      SET 
        active = '".$active."'  
      WHERE
        customerId = '".$this->customerId."'");
    $this->Util()->DB()->UpdateData();

    $sql = "SELECT * FROM customer WHERE customerId = '".$this->customerId."'";
    $this->Util()->DB()->setQuery($sql);
    $newData = $this->Util()->DB()->GetRow();

    //actualizar historial
    $this->Util()->DB()->setQuery("
			INSERT INTO
				customerChanges
			(
				`customerId`,
				`status`,
				`oldData`,
				`newData`,
				`personalId`
		)
		VALUES
		(
				'".$this->customerId."',
				'".$newData["active"]."',
				'".urlencode(serialize($info))."',
				'".urlencode(serialize($newData))."',
				'".$User["userId"]."'
		);");
    $this->Util()->DB()->InsertData();
        
    $this->Util()->setError(10047, "complete", $complete);
    $this->Util()->PrintErrors();
    return true;
  }
  
  public function GetNameById(){
      
    $sql = 'SELECT 
          name
        FROM 
          customer 
        WHERE 
          customerId = '.$this->customerId;
    
    $this->Util()->DB()->setQuery($sql);
    
    return $this->Util()->DB()->GetSingle();
    
  }
  
  public function Suggest($value,$tipo="")
  {
    global $User;
    
    if($tipo == "Activos")
    {
      $activevalue = "1";
    }
    elseif($tipo == "Inactivos")
    {
      $activevalue = "0";
    }
    else
    {
    $activevalue = "1";
    }
    
    
    if(strlen($value) < 3)
    {
      return;
    }
    $this->Util()->DB()->setQuery("SELECT contract.*, customer.nameContact, customer.customerId AS customerId FROM customer
    LEFT JOIN contract ON customer.customerId = contract.customerId 
    WHERE customer.active = '".$activevalue."'
    AND (
      ((contract.name LIKE '%".$value."%' OR 
      contract.rfc LIKE '%".$value."%') AND contract.activo = 'Si') 
      OR customer.nameContact LIKE '%".$value."%'
    ) ORDER BY customer.nameContact ASC, contract.name ASC LIMIT 10");
/*    $this->Util()->DB()->setQuery("SELECT contract.*, customer.nameContact FROM contract
    LEFT JOIN customer ON customer.customerId = contract.customerId 
    WHERE customer.active = '1' AND (contract.name LIKE '%".$value."%' OR contract.rfc LIKE '%".$value."%' OR customer.nameContact LIKE '%".$value."%') ORDER BY customer.nameContact ASC, contract.name ASC LIMIT 10");
*/    
    $result = $this->Util()->DB()->GetResult();
    foreach($result as $key => $value)
    {
      if ($User['departamentoId'] != "1" && $User["roleId"] != 1) {
        $this->Util()->DB()->setQuery(
            "SELECT 
              departamentoId 
            FROM 
              tipoServicio,servicio 
            WHERE 
              tipoServicio.tipoServicioId=servicio.tipoServicioId 
              AND servicio.contractId='".$value['contractId']."'"
        );
        $deps=$this->Util()->DB()->GetSingle();
        
        $show = false;
        $show = ($deps == $User['departamentoId'])? true : false;
        
        if ($show === false) {
          unset($result[$key]);
          continue;
        }
      }
      $cUser = new User;
      $cUser->setUserId($value["responsableCuenta"]);
      $userInfo = $cUser->Info();
      if ($User["roleId"] > 2 
          && ($User["userId"] != $value["responsableCuenta"] 
          && $userInfo["jefeContador"] != $User["userId"] 
          && $userInfo["jefeSupervisor"] != $User["userId"] 
          && $userInfo["jefeGerente"] != $User["userId"] 
          && $userInfo["jefeSocio"] != $User["userId"])
      ) {
        unset($result[$key]);
        continue;
      }     
    }
    return $result;
  }  

/*  public function SuggestCustomer($value)
  {
    global $User;
    
    $this->Util()->DB()->setQuery(
        "SELECT 
          customer.nameContact, customer.customerId, contract.responsableCuenta, contract.permisos
        FROM 
          customer
        LEFT JOIN 
          contract ON contract.customerId = customer.customerId 
        WHERE 
          customer.active = '1' 
          AND (contract.name LIKE '%".$value."%' 
                OR contract.rfc LIKE '%".$value."%' 
                OR customer.nameContact LIKE '%".$value."%')  
        GROUP BY 
          customer.customerId 
        ORDER BY 
          customer.nameContact ASC 
        LIMIT 
          10"
    );

    $result = $this->Util()->DB()->GetResult();
    foreach ($result as $key => $value) {
      $personal = new Personal;
      $personal->setPersonalId($User["userId"]);
      $subordinados = $personal->Subordinados();
      
      $subordinadosPermiso = array();
      foreach ($subordinados as $sub) {
        array_push($subordinadosPermiso, $sub["personalId"]);
      }
      array_push($subordinadosPermiso, $User["userId"]);
      
      //Obtenemos la lista de personas que tienen acceso al contrato
      $contract = new Contract;
      $conPermiso = $contract->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);
      
      $show = false;
      foreach ($subordinadosPermiso as $usuarioPermiso) {
        if (in_array($usuarioPermiso, $conPermiso)) {
          $show = true;
          continue;
        }
      }
      
      if (!$show && $User["roleId"] != 1 && $User["roleId"] != 4) {
        unset($result[$key]);
      }
    }
    return $result;
  }  */
	
  public function SuggestCustomer($like = "", $type = "subordinado", $customerId = 0, $tipo = "", $limit = 25)
  {
    global $User, $page;
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
            customer.*
          FROM 
            customer
					LEFT JOIN contract ON contract.customerId = customer.customerId	
          WHERE 
            1 ".$sqlActive." ".$add." ".$addActivo." ".$addWhere."
          ORDER BY 
            nameContact ASC LIMIT ".$limit;
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
            } 
          }
          
          if (count($result[$key]["contracts"][$keyContract]['instanciasServicio']) > 0) {
            $showCliente = true;
            $result[$key]["servicios"]++;
          } else {
            unset($result[$key]["contracts"][$keyContract]);
          }

        }

        if (($showCliente === false && in_array($User['roleId'],explode(',',ROLES_LIMITADOS))) || ($showCliente === false && $type == "propio")) {
          unset($result[$key]);
        }

        
    }
		$noDuplicados = array();
		foreach($result as $key => $value)
		{
			$noDuplicados[$value["customerId"]] = $value;
		}
		$result = $noDuplicados;

    return $result;
  }	
	
  public function SuggestCustomerContract($like = "", $type = "subordinado", $customerId = 0, $tipo = "")
  {
    global $User, $page;
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
            } 
          }
          
          if (count($result[$key]["contracts"][$keyContract]['instanciasServicio']) > 0) {
            $showCliente = true;
            $result[$key]["servicios"]++;
          } else {
            unset($result[$key]["contracts"][$keyContract]);
          }

        }

        if (($showCliente === false &&in_array($User['roleId'],explode(',',ROLES_LIMITADOS))) || ($showCliente === false && $type == "propio")) {
          unset($result[$key]);
        }

        
    }
    return $result;
  }
	
	public function SuggestCustomerCatalog($like = "", $type = "subordinado", $customerId = 0, $tipo = "",$limite=false)
	{
		global $User, $page;
		if ($this->active) {
			$sqlActive = " AND active = '1' ";
		}
	
		if ($customerId) {
				$add = " AND customer.customerId = '".$customerId."' ";
				if ($page == "report-servicio") {
					$User["roleId"] = 1;
				}
		}
	
		if ($tipo == "Activos") {
				$addActivo = " AND active = '1' ";
		} elseif ($tipo == "Inactivos") {
				$addActivo = " AND (active = '0' OR (active = '1' AND contract.activo = 'No' ))";
		} else {
				$addActivo = " AND active = '1' ";
		}

		if (strlen($like) > 1) {
				$addWhere = " AND (contract.name LIKE '%".$like."%' 
					OR contract.rfc LIKE '%".$like."%' 
					OR customer.nameContact LIKE '%".$like."%')  ";
		}
		
		if($limite)
			$addLimite = " LIMIT 15";
		else 
			$addLimite = "";
   
		$sql = "SELECT 
						customer.customerId,customer.fechaAlta, customer.nameContact, contract.contractId, contract.name,
			customer.phone, customer.email, customer.password,customer.active 
						FROM customer
			LEFT JOIN contract ON contract.customerId = customer.customerId	
						WHERE 1 ".$sqlActive." ".$add." ".$addActivo." ".$addWhere."
			GROUP BY customerId 	
						ORDER BY nameContact ASC 
			".$addLimite."";
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
		
		$count = 0;
		$filtro = new Filtro;
		$data["subordinados"] = $filtro->Subordinados($User["userId"]);

    foreach ($result as $key => $val) 
		{
			$result[$key]["showCliente"] = 1;
			$result[$key]["contractsActivos"] = $this->HowManyRazonesSociales($val["customerId"], $activo = 'Si');
			
			$result[$key]["contractsInactivos"] = $this->HowManyRazonesSociales($val["customerId"], $activo = 'No');
			
			$result[$key]["contracts"] = $this->GetRazonesSociales($val["customerId"]);

            $result[$key]["servicios"] = count($result[$key]["contracts"]);
						
			$countContracts = count($result[$key]["contracts"]);
			
			$result[$key]["servicios"] = 0;

			if($countContracts > 0){
				$result[$key]["showCliente"] = 0;
				
				foreach ($result[$key]["contracts"] as $keyContract => $value) {
                  //echo "<pre style='text-align: left'>";
                    $oPer =  new Personal;
                    $permisosArray = explode('-',$value['permisos']);
                    foreach($permisosArray as $pk=>$vp){
                        $dp = explode(',',$vp);
                        switch($dp[0]){
                            case 1:
                                $oPer->setPersonalId($dp[1]);
                                $result[$key]["contracts"][$keyContract]["respContabilidad"] = strlen($oPer->GetNameById())>2?$oPer->GetNameById():'--';
                            break;
                            case 8:
                                $oPer->setPersonalId($dp[1]);
                                $result[$key]["contracts"][$keyContract]["respNominas"] = strlen($oPer->GetNameById())>2?$oPer->GetNameById():'--';
                            break;
                            case 31:
                                $oPer->setPersonalId($dp[1]);
                                $result[$key]["contracts"][$keyContract]["respAuditoria"] = strlen($oPer->GetNameById())>2?$oPer->GetNameById():'--';
                            break;
                            case 24:
                                $oPer->setPersonalId($dp[1]);
                                $result[$key]["contracts"][$keyContract]["respImss"] =strlen($oPer->GetNameById())>2?$oPer->GetNameById():'--';
                            break;
                            case 22:
                                $oPer->setPersonalId($dp[1]);
                                $result[$key]["contracts"][$keyContract]["respJuridico"] = strlen($oPer->GetNameById())>2?$oPer->GetNameById():'--';
                            break;
                            case 21:
                                $oPer->setPersonalId($dp[1]);
                                $result[$key]["contracts"][$keyContract]["respAdministracion"] = strlen($oPer->GetNameById())>2?$oPer->GetNameById():'--';
                            break;
                            case 26:
                                $oPer->setPersonalId($dp[1]);
                                $result[$key]["contracts"][$keyContract]["respMensajeria"] = strlen($oPer->GetNameById())>2?$oPer->GetNameById():'--';
                            break;
                        }
                    }
                    $contract = new Contract;
                    $cUser = new User;
                    $cUser->setUserId($value["responsableCuenta"]);
                    $userInfo = $cUser->Info();
                    $contract->setContractId($value['contractId']);
                    $result[$key]["contracts"][$keyContract]["totalMensual"] =  number_format($contract->getTotalIguala(),2,'.',',');
                    $result[$key]["contracts"][$keyContract]["responsable"] = $userInfo;

                    $treeSub = $oPer->findTreeSubordinate($value['responsableCuenta']);
                    switch($treeSub['tipoPersonal']){
                        case 'Supervisor':
                        case 'Asistente':
                        case 'Gerente':
                        case 'socio':
                            $result[$key]["contracts"][$keyContract]["supervisadoBy"] = $treeSub['name'];
                            break;
                        case 'Contador':
                        case 'Auxiliar':
                            $result[$key]["contracts"][$keyContract]["supervisadoBy"] = $treeSub['supervisor'];
                            break;
                    }


					$data["conPermiso"] = $filtro->UsuariosConPermiso($value['permisos'], $value["responsableCuenta"]);
	
					$serviciosContrato = $this->GetServicesByContract($value["contractId"]);

					//si no tiene servicios y es un admin o un asistente hay que mostrar el cliente por default
					if($result[$key]["showCliente"] == 0)
					{
						$result[$key]["showCliente"] = $showCliente = $filtro->ShowByDefault($serviciosContrato, $User["roleId"]);
						if($result[$key]["showCliente"] > 0)
						{
							continue;
						}
					}
					//Agregar o no agregar servicio a arreglo de contratos?
					foreach ($serviciosContrato as $servicio) {
						//$responsableId = $result[$key]["contracts"][$keyContract]['permisos'][$servicio['departamentoId']];
						$data["subordinadosPermiso"] = $filtro->SubordinadosPermiso($type, $data["subordinados"], $User["userId"]);
						//Si es usuario de contabilidad
						$data["withPermission"] = $filtro->WithPermission($User["roleId"], $data["conPermiso"], $data["subordinadosPermiso"], $result, $servicio, $key, $keyContract);

					}//foreach
					//si hay instancias de servcicio se muestra el cliente si no no
					$result[$key]["showCliente"] += $filtro->ShowByInstances($result[$key]["contracts"][$keyContract]['instanciasServicio'], $result, $key, $keyContract);
			
				}//foreach
				
			}else{				
				$result[$key]["contracts"][0]["customerId"] = $val["customerId"];
				$result[$key]["contracts"][0]["nameContact"] = $val["nameContact"];
				$result[$key]["contracts"][0]["fake"] = 1;
			}
			$filtro->RemoveClientFromView($result[$key]["showCliente"], $User["roleId"], $type, $result, $key);
   	}//foreach
        return $result;
		
	}//SuggestCustomerCatalog
	
	function GetServicesByContract($id){
							//Checar servicios del contrato para saber si lo debemos mostrar o no
					$this->Util()->DB->setQuery(
					  "SELECT servicioId, nombreServicio, departamentoId 
					  FROM servicio 
					  LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
					  WHERE contractId = '".$id."' AND servicio.status = 'activo'          
					  ORDER BY nombreServicio ASC"
					);
					$serviciosContrato = $this->Util()->DB()->GetResult();
					return $serviciosContrato;
	}
	
	function HowManyRazonesSociales($customerId, $activo = 'Si')
	{
					$sql = "SELECT COUNT(*)
              		FROM contract
              		WHERE customerId = '".$customerId."'
					AND activo = '".$activo."'
              		ORDER BY name ASC";      
			$this->Util()->DB()->setQuery($sql);
			return $this->Util()->DB()->GetSingle();
	}
	
	function GetRazonesSociales($customerId)
	{
		      		$sql = "SELECT contract.*
              		FROM contract
              		WHERE customerId = '".$customerId."'
              		ORDER BY name ASC";      
			$this->Util()->DB()->setQuery($sql);
            $result = $this->Util()->DB()->GetResult();

			return $result;
	}

  public function DeleteInactivos()
  {
      
    $sql = "DELETE 
        FROM 
          customer 
        WHERE 
          active = '0'";
    $this->Util()->DB()->setQuery($sql);

    $result = $this->Util()->DB()->DeleteData();

    $sql = "DELETE 
        FROM 
          contract 
        WHERE 
          activo = 'No'";
    $this->Util()->DB()->setQuery($sql);

    $result = $this->Util()->DB()->DeleteData();
    
  }

  public function HistorialAll()
  {
    $this->Util()->DB()->setQuery("
			SELECT customerChanges.*, personal.name AS personalName, customer.customerId, customer.nameContact FROM customerChanges
			JOIN personal ON personal.personalId = customerChanges.personalId
			JOIN customer ON customer.customerId = customerChanges.customerId
			ORDER BY customerChanges.customerChangesId DESC");
    $data =$this->Util()->DB()->GetResult();

    return $data;
  }
  
}