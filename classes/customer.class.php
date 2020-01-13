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
  private $observacion;
  public function setObservacion($value)
  {
    $this->observacion = $value;
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
    global $User, $rol;

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
              AND tipoServicio.departamentoId='" . $depto . "'
            WHERE 
              1 " . $add . " AND (
                         nameContact LIKE '%" . $_POST["valur"] . "%' || 
                         ((contract.name LIKE '%" . $_POST["valur"] . "%' ||
                          contract.rfc LIKE '%" . $_POST["valur"] . "%') && contract.activo = 'Si')
                        )        
              " . $sqlActive . "
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
              customerId = '" . $val["customerId"] . "'
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
          $rol->setRolId($User['roleId']);
          $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar'));
          if (($User["userId"] == $value["responsableCuenta"]
              || $userInfo["jefeContador"] == $User["userId"]
              || $userInfo["jefeSupervisor"] == $User["userId"]
              || $userInfo["jefeGerente"] == $User["userId"]
              || $userInfo["jefeSocio"] == $User["userId"])
            || $unlimited
          ) {
            $showCliente = true;
            $result[$key]["servicios"]++;
          }
        }
      }
      $rol->setRolId($User['roleId']);
      $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar'));
      if ($showCliente === false && !$unlimited) {
        unset($result[$key]);
      }
    }
    return $result;
  }
  public function Enumerate($type = "subordinado", $customerId = 0, $tipo = "")
  {

    global $User, $page, $rol;

    if ($customerId) {
      $add = " AND customerId = '" . $customerId . "' ";
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
				1 " . $sqlActive . " " . $add . " " . $addActivo . "
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
					customerId = '" . $val["customerId"] . "' AND activo = 'Si'
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
					contractId = '" . $value["contractId"] . "' AND servicio.status = 'activo' AND tipoServicio.status='1'         
				  ORDER BY 
					nombreServicio ASC"
        );
        $serviciosContrato = $this->Util()->DB()->GetResult();

        $cUser = new User;
        //agregar o no agregar servicio a arreglo de contratos?
        foreach ($serviciosContrato as $servicio) {
          $responsableId = $result[$key]["contracts"][$keyContract]['permisos'][$servicio['departamentoId']];
          $cUser->setUserId($value["responsableCuenta"]);
          $userInfo = $cUser->Info();
          $result[$key]["contracts"][$keyContract]["responsable"] = $userInfo;
          if ($type == "propio") {
            //si es propio pero es administrador debe ver el de todos
            if ($User['tipoPers'] == 'Admin') {
              $subordinadosPermiso = array();
              foreach ($subordinados as $sub) {
                array_push($subordinadosPermiso, $sub["personalId"]);
              }
              array_push($subordinadosPermiso, $User["userId"]);
            } else {
              $subordinadosPermiso = array(
                $User["userId"]
              );
            }
          } else {
            $subordinadosPermiso = array();
            foreach ($subordinados as $sub) {
              array_push($subordinadosPermiso, $sub["personalId"]);
            }
            array_push($subordinadosPermiso, $User["userId"]);
          }
          //si es usuario con privilegio de ver todos los contratos, de lo contrario que verifique permisos
          $rol->setRolId($User['roleId']);
          $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar'));
          if ($unlimited) {
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
      $rol->setRolId($User['roleId']);
      $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar'));
      if (($showCliente === false && !$unlimited) || ($showCliente === false && $type == "propio")) {
        unset($result[$key]);
      }
    } //foreach
    return $result;
  } //Enumerate

  public function EnumerateNameOnly($type = "subordinado", $customerId = 0, $tipo = "")
  {
    global $User, $rol;
    if ($this->active) {
      $sqlActive = " AND active = '1' ";
    }

    if ($customerId) {
      $add = " AND customerId = '" . $customerId . "' ";
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
              1 " . $sqlActive . " " . $add . " " . $addActivo . "
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
                customerId = '" . $val["customerId"] . "' AND activo = 'Si'
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
          $rol->setRolId($User['roleId']);
          $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar'));
          if (
            ($User["userId"] == $value["responsableCuenta"]
              || $userInfo["jefeContador"] == $User["userId"]
              || $userInfo["jefeSupervisor"] == $User["userId"]
              || $userInfo["jefeGerente"] == $User["userId"]
              || $userInfo["jefeSocio"] == $User["userId"])
            || !$unlimited
          ) {
            $showCliente = true;
            $result[$key]["servicios"]++;
          }
        }
      }
      $rol->setRolId($User['roleId']);
      $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar'));
      if (($showCliente === false && !$unlimited) || ($showCliente === false && $type == "propio")) {
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
          customerId = '" . $this->customerId . "'"
    );
    $this->Util()->DB()->query;
    $row = $this->Util()->DB()->GetRow();

    $sql = "SELECT 
                * 
              FROM 
                personal 
              WHERE 
                personalId = '" . $row["encargadoCuenta"] . "'";

    $this->Util()->DB()->setQuery($sql);
    $row["encargadoCuentaData"] = $this->Util()->DB()->GetRow();

    $sql = "SELECT * FROM 
            personal WHERE personalId = '" . $row["responsableCuenta"] . "'";
    $row["responsableCuentaData"] = $this->Util()->DB()->GetRow();

    $row["fechaMysql"] = $this->Util()->FormatDateMysql($row["fechaAlta"]);

    return $row;
  }

  public function InfobyName($name)
  {
    $this->Util()->DB()->setQuery(
      "
        SELECT 
          * 
        FROM 
          customer 
        WHERE 
          nameContact LIKE '%" . $name . "%'"
    );
    $row = $this->Util()->DB()->GetRow();
    $sql = "SELECT 
                * 
              FROM 
                personal 
              WHERE 
                personalId = '" . $row["encargadoCuenta"] . "'";

    $this->Util()->DB()->setQuery($sql);
    $row["encargadoCuentaData"] = $this->Util()->DB()->GetRow();
    $sql = "SELECT 
              * 
            FROM 
              personal 
            WHERE 
              personalId = '" . $row["responsableCuenta"] . "'";
    $row["responsableCuentaData"] = $this->Util()->DB()->GetRow();

    $row["fechaMysql"] = $this->Util()->FormatDateMysql($row["fechaAlta"]);
    return $row;
  }
  public function Edit()
  {
    global $User, $log;
    if ($this->Util()->PrintErrors()) {
      return false;
    }

    $oldData = $this->Info();

    $this->Util()->DB()->setQuery(
      "
      UPDATE
        customer
      SET        
        `name` = '" . $this->name . "',
        phone = '" . $this->phone . "',
        email = '" . $this->email . "',        
        nameContact = '" . $this->nameContact . "',        
        password = '" . $this->password . "',        
        noFactura13 = '" . $this->noFactura13 . "',        
        encargadoCuenta = '" . $this->encargadoCuenta . "',        
        responsableCuenta = '" . $this->responsableCuenta . "', 
        observacion = '" . $this->observacion . "',        
        fechaAlta = '" . $this->fechaAlta . "',        
        active = '" . $this->active . "'
      WHERE customerId = '" . $this->customerId . "'"
    );
    $this->Util()->DB()->UpdateData();

    $sql = "SELECT * FROM customer WHERE customerId = '" . $this->customerId . "'";
    $this->Util()->DB()->setQuery($sql);
    $newData = $this->Util()->DB()->GetRow();

    //Guardamos  y enviamos el Log
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
				'" . $this->customerId . "',
				'" . $newData["active"] . "',
				'" . urlencode(serialize($oldData)) . "',
				'" . urlencode(serialize($newData)) . "',
				'" . $User["userId"] . "'
		);");
    $this->Util()->DB()->InsertData();

    $this->Util()->setError(10046, "complete", "El cliente fue Actualizado correctamente");
    $this->Util()->PrintErrors();
    return true;
  }
  public function Save()
  {
    global $User, $log;
    if ($this->Util()->PrintErrors()) {
      return false;
    }

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
        observacion,        
        fechaAlta,        
        active
    )
    VALUES
    (
        '" . $this->name . "',        
        '" . $this->phone . "',
        '" . $this->email . "',        
        '" . $this->nameContact . "',        
        '" . $this->password . "',        
        '" . $this->responsableCuenta . "',        
        '" . $this->encargadoCuenta . "', 
        '" . $this->observacion . "',        
        '" . $this->fechaAlta . "',        
        '" . $this->active . "'
    );");
    $customerId = $this->Util()->DB()->InsertData();

    $sql = "SELECT * FROM customer WHERE customerId = '" . $customerId . "'";
    $this->Util()->DB()->setQuery($sql);
    $newData = $this->Util()->DB()->GetRow();
    //Guardamos el Log
    $log->setPersonalId($User['userId']);
    $log->setFecha(date('Y-m-d H:i:s'));
    $log->setTabla('customer');
    $log->setTablaId($customerId);
    $log->setAction('Insert');
    $log->setOldValue('');
    $log->setNewValue(serialize($newData));
    $log->Save();

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
				'" . $customerId . "',
				'" . $newData["active"] . "',
				'',
				'" . urlencode(serialize($newData)) . "',
				'" . $User["userId"] . "'
		);");
    $this->Util()->DB()->InsertData();
    $this->Util()->setError(10045, "complete", "El cliente fue agregado correctamente.");
    $this->Util()->PrintErrors();
    return true;
  }
  public function Delete()
  {
    global $User, $log;
    if ($this->Util()->PrintErrors()) {
      return false;
    }
    $info = $this->Info();

    if ($info["active"] == '1') {
      $active = 0;
      $complete = "El cliente fue dado de baja correctamente";
      $action = 'Baja';
    } else {
      $active = 1;
      $complete = "El cliente fue dado de alta correctamente";
      $action = "Reactivacion";
    }

    $this->Util()->DB()->setQuery("
      UPDATE 
        customer
      SET 
        active = '" . $active . "'  
      WHERE
        customerId = '" . $this->customerId . "'");
    $this->Util()->DB()->UpdateData();

    $sql = "SELECT * FROM customer WHERE customerId = '" . $this->customerId . "'";
    $this->Util()->DB()->setQuery($sql);
    $newData = $this->Util()->DB()->GetRow();

    //Guardamos  y enviamos el Log
    $log->setPersonalId($User['userId']);
    $log->setFecha(date('Y-m-d H:i:s'));
    $log->setTabla('customer');
    $log->setTablaId($this->customerId);
    $log->setAction($action);
    $log->setOldValue('');
    $log->setNewValue(serialize($newData));
    $log->Save();
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
				'" . $this->customerId . "',
				'" . $newData["active"] . "',
				'" . urlencode(serialize($info)) . "',
				'" . urlencode(serialize($newData)) . "',
				'" . $User["userId"] . "'
		);");
    $this->Util()->DB()->InsertData();

    $this->Util()->setError(10047, "complete", $complete);
    $this->Util()->PrintErrors();
    return true;
  }
  public function GetNameById()
  {

    $sql = 'SELECT 
          name
        FROM 
          customer 
        WHERE 
          customerId = ' . $this->customerId;

    $this->Util()->DB()->setQuery($sql);

    return $this->Util()->DB()->GetSingle();
  }
  public function Suggest($value, $tipo = "")
  {
    global $User, $rol;

    if ($tipo == "Activos") {
      $activevalue = "1";
    } elseif ($tipo == "Inactivos") {
      $activevalue = "0";
    } else {
      $activevalue = "1";
    }

    if (strlen($value) < 3) {
      return;
    }
    $this->Util()->DB()->setQuery("SELECT contract.*, customer.nameContact, customer.customerId AS customerId FROM customer
    LEFT JOIN contract ON customer.customerId = contract.customerId 
    WHERE customer.active = '" . $activevalue . "'
    AND (
      ((contract.name LIKE '%" . $value . "%' OR 
      contract.rfc LIKE '%" . $value . "%') AND contract.activo = 'Si') 
      OR customer.nameContact LIKE '%" . $value . "%'
    ) ORDER BY customer.nameContact ASC, contract.name ASC LIMIT 10");

    $result = $this->Util()->DB()->GetResult();
    $rol->setRolId($User['roleId']);
    $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar'));
    foreach ($result as $key => $value) {
      if ($User['departamentoId'] != "1" && !$unlimited) {
        $this->Util()->DB()->setQuery(
          "SELECT 
              departamentoId 
            FROM 
              tipoServicio,servicio 
            WHERE 
              tipoServicio.tipoServicioId=servicio.tipoServicioId 
              AND servicio.contractId='" . $value['contractId'] . "'"
        );
        $deps = $this->Util()->DB()->GetSingle();

        $show = false;
        $show = ($deps == $User['departamentoId']) ? true : false;

        if ($show === false) {
          unset($result[$key]);
          continue;
        }
      }
      $cUser = new User;
      $cUser->setUserId($value["responsableCuenta"]);
      $userInfo = $cUser->Info();
      $rol->setRolId($User['roleId']);
      $unlimited = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar', 'cliente'));
      if (
        !$unlimited
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
  public function SuggestCustomer($like = "", $type = "subordinado", $customerId = 0, $tipo = "", $limit = 20)
  {
    global $User, $page, $rol, $personal;
    $ftrCustomer = "";
    $ftrContract = "";

    if ($this->active) {
      $ftrCustomer .= " AND a.active = '1' ";
    }

    if ($customerId) {
      $ftrCustomer .= " AND a.customerId = '" . $customerId . "' ";
      if ($page == "report-servicio") {
        $User["roleId"] = 1;
      }
    }
    switch ($tipo) {
      case 'Inactivos':
        $ftrCustomer .= " AND (active = '0' OR (active = '1' AND contract.activo = 'No' ))";
        break;
      default:
        $ftrCustomer .= " AND a.active = '1' ";
        $ftrContract .= " AND b.activo = 'Si' ";
        break;
    }
    if (strlen($like) > 1) {
      $ftrCustomer .= " AND (a.nameContact LIKE '%$like%' OR (b.name LIKE '%$like%' OR b.rfc LIKE '%$like%'))";
    }
    $fil['deep'] = 'On';
    $subordinados = $personal->GetIdResponsablesSubordinados($fil);
    $subs =  implode(",", $subordinados);
    $rol->setRolId($User['roleId']);
    $unlimited = $rol->ValidatePrivilegiosRol(array('gerente','subgerente','supervisor', 'contador', 'auxiliar', 'cliente'), array('Juridico RRHH'));
    if ($unlimited)
      $join =  " LEFT JOIN ";
    else
      $join =  "  INNER JOIN ";

    $sql = "SELECT a.customerId,a.nameContact,b.contractId,b.name FROM customer a 
              $join contract b ON  a.customerId= b.customerId  
              $join contractPermiso c ON b.contractId=c.contractId AND c.personalId IN($subs)
              WHERE 1 $ftrCustomer GROUP BY b.contractId LIMIT $limit
               ";
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();
    $noDuplicados = array();
    foreach ($result as $key => $value) {
      $noDuplicados[$value["customerId"]] = $value;
    }
    $result = $noDuplicados;
    return $result;
  }
  public function SuggestCustomerContract($like = "", $type = "subordinado", $customerId = 0, $tipo = "")
  {
    global $User, $page, $rol;
    if ($this->active) {
      $sqlActive = " AND active = '1' ";
    }

    if ($customerId) {
      $add = " AND customerId = '" . $customerId . "' ";
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
      $addWhere = " AND (contract.name LIKE '%" . $like . "%' 
                OR contract.rfc LIKE '%" . $like . "%' 
                OR customer.nameContact LIKE '%" . $like . "%')  ";
    }

    $sql = "SELECT 
            customer.customerId, customer.nameContact, contract.contractId, contract.name 
          FROM 
            customer
					LEFT JOIN contract ON contract.customerId = customer.customerId	
          WHERE 
            1 " . $sqlActive . " " . $add . " " . $addActivo . " " . $addWhere . "
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
                customerId = '" . $val["customerId"] . "' AND activo = 'Si'
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
                contractId = '" . $value["contractId"] . "' AND servicio.status IN('activo','bajaParcial','readonly')          
              ORDER BY 
                nombreServicio ASC"
        );
        $serviciosContrato = $this->Util()->DB()->GetResult();

        $cUser = new User;
        //agregar o no agregar servicio a arreglo de contratos?
        foreach ($serviciosContrato as $servicio) {
          $cUser->setUserId($value["responsableCuenta"]);
          $userInfo = $cUser->Info();
          $result[$key]["contracts"][$keyContract]["responsable"] = $userInfo;

          if ($type == "propio") {
            $subordinadosPermiso = array(
              $User["userId"]
            );
          } else {
            $subordinadosPermiso = array();
            foreach ($subordinados as $sub) {
              array_push($subordinadosPermiso, $sub["personalId"]);
            }
            array_push($subordinadosPermiso, $User["userId"]);
          }
          //comprobar el rol si es de tipo limitado pasando nombre de roles que queremos limitar
          $rol->setRolId($User['roleId']);
          $unlimited  = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar', 'cliente'));
          $unlimited2 = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar', 'cliente'));
          if ($unlimited) {
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
          //unset($result[$key]["contracts"][$keyContract]);
        }
      }

      if (($showCliente === false && !$unlimited2) || ($showCliente === false && $type == "propio")) {
        unset($result[$key]);
      }
    }
    return $result;
  }
  public function SuggestCustomerRazon($filter = [])
  {
    global $User, $rol, $personal, $filtro;
    $fltSearch = "";
    $fltContract = "";
    switch ($filter['tipos']) {
      case 'temporal':
      case 'activos':
        $fltSearch .= " and a.active='1'  and b.activo='Si' ";
        $fltContract .= " and ax.activo='Si' ";
        break;
      case 'inactivos':
        $fltSearch .= " and a.active='0' and b.activo='No' ";
        $fltContract .= " and ax.activo='No' ";
        break;
      default:
        $fltSearch .= "";
        $fltContract .= "";
        break;
    }
    if ($filter['cliente'] > 0)
      $fltSearch .= " and a.customerId='" . $filter['cliente'] . "' ";
    switch ($filter['factura13']) {
      case 'si':
        $fltSearch .= " and a.noFactura13='No' ";
        break;
      case 'no':
        $fltSearch .= " and a.noFactura13='Si' ";
        break;
      default:
        break;
    }

    //comprobar rol si es limitado se usa query diferente
    $rol->setRolId($User['roleId']);
    $ilimitado = $rol->ValidatePrivilegiosRol(array('supervisor', 'contador', 'auxiliar'), array('Juridico RRHH','Subgerente'));

    if (!$ilimitado) {
      $str = "select a.customerId as clienteId,a.nameContact,a.active,a.observacion,a.fechaAlta,a.email,a.phone,a.password,a.noFactura13,b.* from customer a 
               inner join (select ax.* from contract ax inner join contractPermiso bx on ax.contractId=bx.contractId  $fltContract where bx.personalId in(" . implode(',', $filter['encargados']) . ") group by ax.contractId) as b
               on a.customerId=b.customerId where 1 $fltSearch   order by a.nameContact asc, b.name asc 
              ";
    } else {
      //si el rol es ilimitado  comprobar que en el array de encargados no este su id, si esta se usa el query que obtiene todos los contratos, si no esta entonces esta buscando de otra persona
      if ($filter["selectedResp"]) {
        $str = "select a.customerId as clienteId,a.nameContact,a.active,a.observacion,a.fechaAlta,a.email,a.phone,a.password,a.noFactura13,b.* from customer a 
               inner join (select ax.* from contract ax inner join contractPermiso bx on ax.contractId=bx.contractId  $fltContract where bx.personalId in(" . implode(',', $filter['encargados']) . ") group by ax.contractId) as b
               on a.customerId=b.customerId where 1 $fltSearch   order by a.nameContact asc, b.name asc 
              ";
      } else {
        $str = "select a.customerId as clienteId,a.nameContact,a.active,a.observacion,a.fechaAlta,a.email,a.phone,a.password,a.noFactura13,b.* from customer a 
               left join (select ax.* from contract ax left join contractPermiso bx on ax.contractId=bx.contractId and bx.personalId in(" . implode(',', $filter['encargados']) . ")  where 1 $fltContract group by ax.contractId) as b
               on a.customerId=b.customerId where 1 $fltSearch  order by a.nameContact asc, b.name asc  
              ";
      }
    }
    $this->Util()->DB()->setQuery($str);
    $result = $this->Util()->DB()->GetResult();
    //$result ya viene filtrado por encargados y por los roles que son ilimitados
    $creport = new ContractRep();
    foreach ($result as $key => $val) {
      $allContracts = [];
      $allContracts = $this->GetRazonesSociales($val["customerId"]);
      $result[$key]["contracts"] =  $allContracts;
      $nameEncargados = $creport->encargadosArea($val['contractId']);
      foreach ($nameEncargados as $var) {
        $result[$key]['resp' . ucfirst(strtolower(str_replace(" ", "", $var['departamento'])))] = $var['personalId'];
        $result[$key]['name' . ucfirst(strtolower(str_replace(" ", "", $var['departamento'])))] = $var['name'];
      }
      //el responsable de contabilidad siempre sera el responsable de cuenta.(viene desde dar de alta el contrato)
      $idResponsable = $result[$key]['respContabilidad'];
      if (!$idResponsable)
        $idResponsable = 0;

      $result[$key]["responsable"] =  $result[$key]['nameContabilidad'];
      $contract = new Contract;
      $contract->setContractId($val['contractId']);
      $result[$key]["totalMensual"] =  number_format($contract->getTotalIguala(), 2, '.', ',');
      $result[$key]["generaFactura13"] =  $val['noFactura13'] == 'Si' ? 'No' : 'Si';
     
      $jefes = [];
      $personal->setPersonalId($idResponsable);
      $infP = $personal->InfoWhitRol();
      $personal->deepJefesArray($jefes,true);
      $needle =strtolower($infP["nameLevel"]);
      /*
          socio,coordinador,gestoria,sistemas,supervisor,gerente,socio = usarse a si mismo como supervisor
          recepcion,cuentas,contador,asistente,contador,auxiliar = usar supevisor encontrado en $jefes
      */
      switch ($needle) {
        case 'contador':
        case 'auxiliar':
          $result[$key]["supervisadoBy"] = $jefes['Supervisor'];
        break;
        default:
        $result[$key]["supervisadoBy"] = $jefes['me'];
        break;
      }
      $serviciosBajaTemporal = $this->GetServicesByContract($val["contractId"], 'bajaParcial');
      if (count($serviciosBajaTemporal) > 0) {
        $result[$key]["activo"] = "Activo/con baja temporal";
      }
      if ($filter["tipos"] == 'temporal') {
        //si el filtro tiene activo busqueda de bajas temporales, evaluar si la empresa tiene servicios con baja temporal de no tenerlo no deberia mostrarse
        if (count($serviciosBajaTemporal) <= 0) {
          unset($result[$key]);
          continue;
        }
      }
      //obtener los servicios del contrato
      $serviciosContrato = $this->GetServicesByContract($val["contractId"]);
      $result[$key]["showContract"] = $showCliente = $filtro->ShowByDefault($serviciosContrato, $User["roleId"]);
      if ($result[$key]["showContract"] < 0) //si un contrato no tiene servicio se comprueba si se visualiza o no segun el tipo de rol.
        unset($result[$key]);
    }
    return $result;
  }
  public function SuggestCustomerCatalog($like = "", $type = "subordinado", $customerId = 0, $tipo = "", $limite = false)
  {
    global $User, $page, $rol, $personal;
    $creport = new ContractRep();
    if ($this->active) {
      $sqlActive = " AND active = '1' ";
    }
    if ($customerId) {
      $add = " AND customer.customerId = '" . $customerId . "' ";
      if ($page == "report-servicio") {
        $User["roleId"] = 1;
      }
    }

    if ($tipo == "Activos") {
      $addActivo = " AND active = '1' ";
    } elseif ($tipo == "Inactivos") {
      $addActivo = " AND (active = '0' OR (active = '1' AND contract.activo = 'No' )) ";
    } else {
      $addActivo = " AND active = '1' ";
    }
    if (strlen($like) > 1) {
      $addWhere = " AND (customer.nameContact LIKE '%" . $like . "%'
				    OR contract.name LIKE '%" . $like . "%' 
					OR contract.rfc LIKE '%" . $like . "%') ";
    }

    if ($limite)
      $addLimite = " LIMIT 15";
    else
      $addLimite = "";

    $sql = "SELECT customer.customerId,customer.fechaAlta, customer.nameContact, contract.contractId, contract.name,
			customer.phone, customer.email, customer.password,customer.active 
			FROM customer
			LEFT JOIN contract ON contract.customerId = customer.customerId	
			WHERE 1  $sqlActive $add $addActivo  $addWhere 
			GROUP BY customerId 	
			ORDER BY nameContact ASC 
			 $addLimite  ";
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();
    $count = 0;
    $filtro = new Filtro;
    $data["subordinados"] = $filtro->Subordinados($User["userId"]);

    foreach ($result as $key => $val) {
      $result[$key]["showCliente"] = 1;
      $result[$key]["doBajaTemporal"] = 0;
      $result[$key]["haveTemporal"] = 0;
      $result[$key]["contractsActivos"] = $this->HowManyRazonesSociales($val["customerId"], $activo = 'Si');
      $result[$key]["contractsInactivos"] = $this->HowManyRazonesSociales($val["customerId"], $activo = 'No');
      $allContracts = [];
      $allContracts = $this->GetRazonesSociales($val["customerId"]);
      $result[$key]["contracts"] =  $allContracts;
      $result[$key]["servicios"] = count($result[$key]["contracts"]);

      $countContracts = count($result[$key]["contracts"]);
      $result[$key]["totalContracts"] = $result[$key]["contractsActivos"] + $result[$key]["contractsInactivos"];
      $result[$key]["servicios"] = 0;
      if ($countContracts > 0) {
        $result[$key]["showCliente"] = 0;
        foreach ($result[$key]["contracts"] as $keyContract => $value) {
          $nameEncargados = $creport->encargadosArea($value['contractId']);

          foreach ($nameEncargados as $var) {
            $result[$key]["contracts"][$keyContract]['resp' . ucfirst(strtolower($var['departamento']))] = $var['personalId'];
            $result[$key]["contracts"][$keyContract]['name' . ucfirst(strtolower($var['departamento']))] = $var['name'];
          }

          $idResponsable = $result[$key]["contracts"][$keyContract]['respContabilidad'];
          $idResponsable = !$idResponsable?0:$idResponsable;

          $result[$key]["contracts"][$keyContract]["responsable"] =  $result[$key]["contracts"][$keyContract]['nameContabilidad'];
          $contract = new Contract;
          $contract->setContractId($value['contractId']);
          $result[$key]["contracts"][$keyContract]["totalMensual"] =  number_format($contract->getTotalIguala(), 2, '.', ',');

          $jefes = [];
          $personal->setPersonalId($idResponsable);
          $infP = $personal->InfoWhitRol();
          $personal->deepJefesArray($jefes,true);
          $needle =strtolower($infP["nameLevel"]);
          /*
              socio,coordinador,gestoria,sistemas,supervisor,gerente,socio = usarse a si mismo como supervisor
              recepcion,cuentas,contador,asistente,auxiliar = usar supevisor encontrado en $jefes
          */
          switch ($needle) {
            case 'contador':
            case 'auxiliar':
              $result[$key]["contracts"][$keyContract]["supervisadoBy"] = $jefes['Supervisor'];
            break;
            default:
              $result[$key]["contracts"][$keyContract]["supervisadoBy"] = $jefes['me'];
            break;
          }
          $data["conPermiso"] = $filtro->UsuariosConPermiso($value['permisos'], $idResponsable);
          //obtiene servicios activos
          $serviciosContrato = $this->GetServicesByContract($value["contractId"]);
          //si no tiene servicios activos un contrato comprobar si tiene baja temporal en sus servicios

          //evaluamos quienes tienen servicios con status baja temporal, con un contrato que exista se muestra el boton  verde
          $parciales = $this->GetServicesByContract($value["contractId"], 'bajaParcial');
          if (!empty($parciales) && $value['activo'] == 'Si')
            $result[$key]["haveTemporal"] = 1;
          //si por lo menos uno de sus contratos no tiene servicios comprobar el rol si tiene privilegios de visualizarlo o no.
          if ($result[$key]["showCliente"] == 0) {
            $result[$key]["showCliente"] = $showCliente = $filtro->ShowByDefault($serviciosContrato, $User["roleId"]);
            if ($result[$key]["showCliente"] > 0) {
              continue;
            }
          }
          //Agregar o no agregar servicio a arreglo de contratos?
          foreach ($serviciosContrato as $servicio) {
            //$responsableId = $result[$key]["contracts"][$keyContract]['permisos'][$servicio['departamentoId']];
            $data["subordinadosPermiso"] = $filtro->SubordinadosPermiso($type, $data["subordinados"], $User["userId"]);
            //Si es usuario de contabilidad
            $data["withPermission"] = $filtro->WithPermission($User["roleId"], $data["conPermiso"], $data["subordinadosPermiso"], $result, $servicio, $key, $keyContract);
          } //foreach
          //contratos sin servicio se eliminan no deberia eliminarlos solo poner en falso para que salf en la busqueda
          $result[$key]["showCliente"] += $filtro->ShowByInstances($result[$key]["contracts"][$keyContract]['instanciasServicio'], $result, $key, $keyContract);
        } //foreach
      } else {
        $result[$key]["contracts"][0]["customerId"] = $val["customerId"];
        $result[$key]["contracts"][0]["nameContact"] = $val["nameContact"];
        $result[$key]["contracts"][0]["fake"] = 1;
      }
      $filtro->RemoveClientFromView($result[$key]["showCliente"], $User["roleId"], $type, $result, $key);
    } //foreach
    return $result;
  } //SuggestCustomerCatalog
  public function SuggestCustomerCatalogFiltrado($like = "", $type = "subordinado", $customerId = 0, $tipo = "", $limite = false)
  {
    global $User, $page, $rol, $personal,$contract;
    $creport = new ContractRep();
    $ftrCustomer = "";
    $ftrContract = "";
    if ($this->active) {
      $ftrCustomer .= " AND active = '1' ";
    }
    if ($customerId) {
      $ftrCustomer .= " AND customer.customerId = '" . $customerId . "' ";
      if ($page == "report-servicio") {
        $User["roleId"] = 1;
      }
    }
    switch ($tipo) {
      case 'Inactivos':
        $ftrCustomer .= " AND (active = '0' OR (active = '1' AND contract.activo = 'No' ))";
        break;
      default:
        $ftrCustomer .= " AND active = '1' ";
        $ftrContract .= " AND contract.activo='Si' ";
        break;
    }
    if (strlen($like) > 1) {
      $ftrCustomer .= " AND (customer.nameContact LIKE '%$like%' OR (contract.name LIKE '%$like%' OR contract.rfc LIKE '%$like%'))";
    }

    if ($limite)
      $addLimite = " LIMIT 15";
    else
      $addLimite = "";

    $sql = "SELECT customer.customerId,customer.fechaAlta, customer.nameContact, contract.contractId, contract.name, customer.phone, customer.email, customer.password,customer.active 
            FROM customer
            LEFT JOIN contract ON contract.customerId = customer.customerId	 $ftrContract
            WHERE 1 $ftrCustomer
            GROUP BY customerId 	
            ORDER BY nameContact ASC 
            $addLimite ";
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();
    $filtro = new Filtro;
    $subordinados = $personal->GetIdResponsablesSubordinados($_POST);

    $strEncargados =  implode(",",$subordinados);
    foreach ($result as $key => $val) {
      $result[$key]["showCliente"] = 0;
      $result[$key]["doBajaTemporal"] = 0;
      $result[$key]["haveTemporal"] = 0;
      $result[$key]["contractsActivos"] = $this->HowManyRazonesSociales($val["customerId"], $activo = 'Si');
      $result[$key]["contractsInactivos"] = $this->HowManyRazonesSociales($val["customerId"], $activo = 'No');
      $result[$key]["contracts"] =  $this->GetRazonesSociales($val["customerId"]);
      $countContracts = count($result[$key]["contracts"]);
      $result[$key]["totalContracts"] = $result[$key]["contractsActivos"] + $result[$key]["contractsInactivos"];
      $contractWhitPermiso = 0;

      if ($countContracts > 0) {
          foreach($result[$key]["contracts"] as $keyContract =>$value){
              $jefes = [];
              $contract->setContractId($value["contractId"]);
              $sql = "SELECT b.personalId FROM contract a  
                  INNER JOIN contractPermiso b ON a.contractId=b.contractId 
                  WHERE b.personalId IN($strEncargados) 
                  AND a.contractId = '".$value['contractId']."' 
                  group by a.contractId ";
              $this->Util()->DB()->setQuery($sql);
              $whitPermiso = $this->Util()->DB()->GetSingle();

              if(!$whitPermiso){
                  unset($result[$key]["contracts"][$keyContract]);
                  continue;
              }


              $contractWhitPermiso++;
              $encargados = $creport->encargadosCustomKey("departamento","name",$value["contractId"]);
              $encargadosId = $creport->encargadosCustomKey("departamento","personalId",$value["contractId"]);
              $result[$key]["contracts"][$keyContract]["responsable"] = $encargados["Contabilidad"];
              $result[$key]["contracts"][$keyContract]["totalMensual"] = number_format($contract->getTotalIguala(),2,'.',',');
              $personal->setPersonalId($encargadosId["Contabilidad"]);
              $infoWhitRol = $personal->InfoWhitRol();
              $personal->deepJefesArray($jefes,true);
              $needle =strtolower($infoWhitRol["nameLevel"]);
              switch ($needle) {
                  case 'contador':
                  case 'auxiliar':
                      $result[$key]["contracts"][$keyContract]["supervisadoBy"] = $jefes['Supervisor'];
                      break;
                  default:
                      $result[$key]["contracts"][$keyContract]["supervisadoBy"] = $jefes['me'];
                      break;
              }
              $serviciosContrato = $this->GetServicesByContract($value["contractId"]);
              $parciales = $this->GetServicesByContract($value["contractId"], 'bajaParcial');
              if (count($parciales)>0 && $value['activo'] == 'Si')
                  $result[$key]["haveTemporal"] = 1;
          }
          $result[$key]["showCliente"] = $contractWhitPermiso;
      } else {
        $result[$key]["contracts"][0]["customerId"] = $val["customerId"];
        $result[$key]["contracts"][0]["nameContact"] = $val["nameContact"];
        $result[$key]["contracts"][0]["fake"] = 1;

        $rol->setRolId($User["roleId"]);
        $result[$key]["showCliente"] = $rol->ValidatePrivilegiosRol(array('supervisor','contador','auxiliar','asistente','sistema'),['socio']);
      }
      $filtro->RemoveClientFromView($result[$key]["showCliente"], $User["roleId"], $type, $result, $key);
    } //foreach
    return $result;
  } //SuggestCustomerCatalog
  public function GetListRazones($like = "", $type = "subordinado", $customerId = 0, $tipo = "", $limite = false)
  {
    $creport = new ContractRep();
    if ($customerId)
      $add = " AND a.customerId = '" . $customerId . "' ";

    if ($tipo == "Activos")
      $addActivo = " AND a.active = '1' and b.activo='Si' ";
    elseif ($tipo == "Inactivos")
      $addActivo = " AND (a.active = '0' OR (a.active = '1' AND b.activo = 'No' ))";
    else
      $addActivo = " AND a.active = '1' ";

    if ($limite)
      $addLimite = " LIMIT 15";
    else
      $addLimite = "";

    $sql = "SELECT a.customerId,a.fechaAlta, a.nameContact, b.contractId, b.name,a.active,b.permisos
				FROM customer a
			    INNER JOIN contract b ON b.customerId = a.customerId	
				WHERE 1 " . $sqlActive . " " . $add . " " . $addActivo . " " . $addWhere . "	
				ORDER BY a.nameContact,b.name ASC 
			" . $addLimite . "";
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();
    foreach ($result as $key => $val) {
      $nameEncargados = $creport->encargadosArea($val['contractId']);
      foreach ($nameEncargados as $var) {
        $result[$key]['resp' . ucfirst(strtolower(str_replace(" ", "", $var['departamento'])))] = $var['personalId'];
        $result[$key]['name' . ucfirst(strtolower(str_replace(" ", "", $var['departamento'])))] = $var['name'];
      }
    } //foreach
    return $result;
  } //GetListRazones()

  function GetServicesByContract($id, $tipo = "activo")
  {
    //por default se debe tomarse en cuenta los de baja temporal
    if ($tipo == 'activo')
      $ftrStatus =  " AND servicio.status IN ('activo','bajaParcial','readonly') ";
    else
      $ftrStatus =  " AND servicio.status = '" . $tipo . "' ";

    $this->Util()->DB()->setQuery(
      "SELECT servicioId, nombreServicio, departamentoId,servicio.status,servicio.costo,servicio.inicioFactura,servicio.inicioOperaciones,servicio.lastDateWorkflow
          FROM servicio 
          LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
          WHERE contractId = '" . $id . "' $ftrStatus and tipoServicio.status='1'        
          ORDER BY nombreServicio ASC"
    );
    $serviciosContrato = $this->Util()->DB()->GetResult();
    return $serviciosContrato;
  }

  function HowManyRazonesSociales($customerId, $activo = 'Si')
  {
    $sql = "SELECT COUNT(*)
              		FROM contract
              		WHERE customerId = '" . $customerId . "'
					AND activo = '" . $activo . "'
              		ORDER BY name ASC";
    $this->Util()->DB()->setQuery($sql);
    return $this->Util()->DB()->GetSingle();
  }

  function GetRazonesSociales($customerId, $like = "", $limit = 0)
  {
    $strLike = "";
    $strLimit = "";
    if ($like != "") {
      $strLike = " and name like '%" . $like . "%' ";
    }
    if ($limit) {
      $strLimit = " LIMIT 1";
    }
    $sql = "SELECT contract.*
              		FROM contract
              		WHERE customerId = '" . $customerId . "' $strLike
              		ORDER BY name ASC $strLimit";
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
    $data = $this->Util()->DB()->GetResult();

    return $data;
  }
  /*
       * funcion getListContratos
       * encuentra la lista de razones sociales del cliente dado
       * la razon social debe tener servicios activos para que pueda ser listado
       * solo se obtienen razones sociales activos.
       */
  public function  getListContratos($tipo)
  {
    $sql  = "select a.contractId,a.name,a.activo from contract a 
                   inner join  servicio b on a.contractId=b.contractId and b.status='" . $tipo . "'
                   inner join  tipoServicio c on b.tipoServicioId=c.tipoServicioId and c.status='1'
                   where a.customerId='" . $this->customerId . "' and a.activo='Si' 
                   group by a.contractId
                   order by a.name asc 
                  ";
    $this->Util()->DB()->setQuery($sql);
    $results = $this->Util()->DB()->GetResult();

    return $results;
  }
  /*
    * funcion getListCustomer
    * encuentra el catalogo de clientes de la base de datos
    * solo se listaran los clieentes segun el valor del parametro siguiente:
     * $tipo puede ser 1(activo), 0(inactivo) o all, por default es todos, es decir se lista el catalogo completo incluyendo las inactivas
    */
  public function  getListCustomer($tipo = 'all')
  {
    $filtro = "";
    switch ($tipo) {
      case 1:
        $filtro .= " and active='1' ";
        break;
      case 0:
        $filtro .= " and active='0' ";
        break;
    }
    $sql  = "select customerId,nameContact from customer where 1 $filtro order by nameContact asc ";
    $this->Util()->DB()->setQuery($sql);
    $results = $this->Util()->DB()->GetResult();
    return $results;
  }
  public function  getTotalContratosInPlatform($tipo = 'Activos')
  {
    $ftr = "";
    switch ($tipo) {
      case 'Activos':
        $ftr .= " and a.activo='Si'  and b.active='1'";
        break;
    }
    $sql  = "select count(*) from contract a 
                 inner join customer b on a.customerId=b.customerId
                 where 1  $ftr ";
    $this->Util()->DB()->setQuery($sql);
    $total = $this->Util()->DB()->GetSingle();
    return $total;
  }
}