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
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, "Nombre del directivo");
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
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Nombre del directivo");
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

    private $is_referred;
    public function setIsReferred($value)
    {
        $this->is_referred = $value;
    }

    public function getIsReferred()
    {
        return $this->is_referred;
    }

    private $type_referred;

    public function setTypeReferred($value)
    {
        $this->Util()->ValidateRequireField($value, "Referido por");
        $this->type_referred = $value;
    }

    public function getTypeReferred()
    {
        return $this->type_referred;
    }

    private $partner_id;

    public function setPartner($value)
    {
        $this->Util()->ValidateRequireField($value, "Asociados comerciales");
        $this->partner_id = $value;
    }

    public function getPartner()
    {
        return $this->partner_id;
    }

    private $name_referrer;

    public function setNameReferrer($value)
    {
        $this->Util()->ValidateRequireField($value, "Referente");
        $this->name_referrer = $value;
    }

    public function getNameReferrer()
    {
        return $this->name_referrer;
    }

    private $tipoClasificacionClienteId;
    public function setTipoClasificacionCliente($value)
    {
        $this->tipoClasificacionClienteId = $value;
    }

    public function getTipoClasificacionCliente()
    {
        return $this->tipoClasificacionClienteId;
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
                    $unlimited = $this->accessAnyContract();
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
            $unlimited = $this->accessAnyContract();
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
                    $unlimited = $this->accessAnyContract();
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
            $unlimited = $this->accessAnyContract();
            if (($showCliente === false && !$unlimited) || ($showCliente === false && $type == "propio")) {
                unset($result[$key]);
            }
        } //foreach
        return $result;
    } //Enumerate

    /*
    * EnumerateOptimizado
    * Quitar ordenamiento ASC (quita mucho tiempo)
    * Quitar el foreach de encontrar los subordinados por cada servicio eso se hace en el foreach de contratos.
    */
    public function EnumerateOptimizado($type = "subordinado", $customerId = 0, $tipo = "")
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
        $sql = "SELECT 	*  FROM customer  WHERE 1 " . $sqlActive . " " . $add . " " . $addActivo . "";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        $count = 0;
        $personal = new Personal;
        $personal->setPersonalId($User["userId"]);
        $subordinados = $personal->Subordinados();
        foreach ($result as $key => $val) {
            $allEmailsCliente = array();
            $sql = "SELECT contract.* FROM contract WHERE customerId = '" . $val["customerId"] . "' AND activo = 'Si' ORDER BY contractId ASC";
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
					contractId = '" . $value["contractId"] . "' AND servicio.status = 'activo' AND tipoServicio.status='1' "
                );
                $serviciosContrato = $this->Util()->DB()->GetResult();
                $cUser = new User;
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
                    } else {
                        $subordinadosPermiso = array(
                            $_SESSION['User']["userId"]);
                    }
                } else {
                    $subordinadosPermiso = array();
                    foreach ($subordinados as $sub) {
                        array_push($subordinadosPermiso, $sub["personalId"]);
                    }
                    //si no es admin se agrega al array (admin no tiene userId valido)
                    //se usa la $_SESSION por que $User se cambio al mandar a llamar esta funcion
                    if ($User['tipoPers'] != 'Admin')
                        array_push($subordinadosPermiso, $_SESSION['User']['userId']);
                }
                //comprobar privilegios del rol o permisos del usuario acti
                $unlimitedRol = $this->accessAnyContract();
                $unlimited = false;
                if ($unlimitedRol) {
                    $unlimited = true;
                } else {
                    foreach ($subordinadosPermiso as $usuarioPermiso) {
                        if (in_array($usuarioPermiso, $conPermiso)) {
                            $unlimited = true;
                            break;
                        }
                    }
                }
                foreach ($serviciosContrato as $servicio) {
                    if ($unlimited)
                        $result[$key]["contracts"][$keyContract]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
                }
                if (count($result[$key]["contracts"][$keyContract]['instanciasServicio']) > 0) {
                    $showCliente = true;
                    $result[$key]["servicios"]++;
                    //si el contrato se va mostrar obtener todos sus emails.
                    $razon = new Razon;
                    $razon->setContractId($value["contractId"]);
                    $emailsContract = $razon->getEmailContractByArea('all');
                    if (!is_array($emailsContract['allEmails']))
                        $emailsContract['allEmails'] = [];
                    $emailTemp = array();
                    foreach ($emailsContract['allEmails'] as $vemail) {
                        if ($this->Util()->ValidateEmail(trim($vemail)))
                            $emailTemp[trim($vemail)] = trim($value['name']);
                    }
                    $allEmailsCliente = array_merge($allEmailsCliente, $emailTemp);
                } else {
                    unset($result[$key]["contracts"][$keyContract]);
                }
            }
            $result[$key]['allEmails'] = $allEmailsCliente;
            $rol->setRolId($User['roleId']);
            $unlimited = $this->accessAnyContract();
            if (($showCliente === false && !$unlimited) || ($showCliente === false && $type == "propio")) {
                unset($result[$key]);
            }
        }//foreach cliente
        return $result;
    }//EnumerateOptimizado

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
                    $unlimited = $this->accessAnyContract();
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
            $unlimited = $this->accessAnyContract();
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

        $partner = $this->partner_id > 0 ? $this->partner_id : 'NULL';
        $clasificacion = $this->tipoClasificacionClienteId > 0 ? $this->tipoClasificacionClienteId : 'NULL';
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
                        is_referred = '" . $this->is_referred . "', 
                        type_referred = '" . $this->type_referred . "', 
                        partner_id = $partner, 
                        name_referrer = '" . $this->name_referrer . "',   
                        tipo_clasificacion_cliente_id = $clasificacion   
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

    public function Save($saveAndSendLog = true)
    {
        global $User, $log;
        if($saveAndSendLog) {
            if ($this->Util()->PrintErrors()){
                return false;
            }
        } else {
            if ($this->Util()->getErrors()['total'])
            return false;
        }

        $partner = $this->partner_id > 0 ? $this->partner_id : 'NULL';
        $clasificacion = $this->tipoClasificacionClienteId > 0 ? $this->tipoClasificacionClienteId : 'NULL';
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
            is_referred,
            type_referred,
            partner_id,
            name_referrer,
            noFactura13,
            tipo_clasificacion_cliente_id
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
            '" . $this->is_referred . "', 
            '" . $this->type_referred . "', 
            $partner, 
            '" . $this->name_referrer . "',
            '" . $this->noFactura13 . "',
            $clasificacion
                   
        );");
        $customerId = $this->Util()->DB()->InsertData();

        if($customerId > 0) {
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
            $saveAndSendLog ? $log->Save() : $log->SaveOnly();

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
        } else {
            $this->Util()->setError(10045, "error", "Error al guardar intente nuevamente.");
            $this->Util()->PrintErrors();
        }
        return $customerId > 0 ;
    }

    public function Delete()
    {
        global $User, $log, $contract;
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
        // dar de baja a los contratos.


        $sql = "SELECT * FROM customer WHERE customerId = '" . $this->customerId . "'";
        $this->Util()->DB()->setQuery($sql);
        $newData = $this->Util()->DB()->GetRow();

        $contractsAfectados = $active
            ? $contract->reactiveContractByCustomer($this->customerId)
            : $contract->downContractByCustomer($this->customerId);
        $log->setContractsAfectados($contractsAfectados);
        //Guardamos  y enviamos el Log
        $log->setFecha(date('Y-m-d H:i:s'));
        $log->setTabla('customer');
        $log->setTablaId($this->customerId);
        $log->setAction($action);
        $log->setOldValue(serialize($info));
        $log->setNewValue(serialize($newData));
        $log->Save();
        //actualizar historial
        $log->saveHistoryCustomer($this->customerId, $info, $newData);

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
        $unlimited = $this->accessAnyContract();
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
            $unlimited = $this->accessAnyContract();
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
                    $unlimited = $this->accessAnyContract();
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
                }
            }

            if (($showCliente === false && !$unlimited) || ($showCliente === false && $type == "propio")) {
                unset($result[$key]);
            }
        }
        return $result;
    }

    public function SuggestCustomerRazon($filter = [], $group = 'contractId')
    {
        global $personal, $departamentos, $contractRep;
        $catalogo = new Catalogo;
        $ftrCustomer = "";
        $ftrContract = "";
        $ftrSubquery = "";
        $findEncargados = false;

        $str_group = " group by a." . $group;

        switch ($filter['tipos']) {
            case 'temporal':
            case 'activos':
                $ftrCustomer .= " and b.active = '1' ";
                $ftrContract .= " and a.activo = 'Si' ";
                break;
            case 'inactivos':
                $ftrCustomer = $group === 'contractId' ? " and a.activo = 'No' " : " and b.active = '0' ";
                break;
        }
        $ftrCustomer .= $filter['cliente'] ? " and b.customerId = '" . $filter['cliente'] . "' " : "";
        $ftrCustomer .= $filter['factura13'] === 'si'
            ? " and b.noFactura13 = 'No' "
            : ($filter['noFactura13'] === 'no' ? " and b.noFactura13 = 'Si' " : "");

        $allowAccessAnyContract = $this->accessAnyContract();
        if ($allowAccessAnyContract === false)
            return [];

        $idImplode = implode(',', $filter['encargados']);
        if ($allowAccessAnyContract === '0' || $filter['selectedResp']) {
            $ftrSubquery .= " and contractPermiso.personalId IN ($idImplode) ";
            $findEncargados = true;
        }

        $sql = "SELECT  b.customerId, b.nameContact,
             b.tipo_clasificacion_cliente_id,   
             (SELECT nombre from tipo_clasificacion_cliente WHERE tipo_clasificacion_cliente.id = b.tipo_clasificacion_cliente_id limit 1) clasificacionCliente,   
             b.phone, b.email, b.password,b.noFactura13,
             b.fechaAlta as fechaAltaCustomer,b.observacion,b.active, a.*
             FROM (SELECT contract.contractId, contract.name, contract.customerId, contract.type, contract.rfc,
                  contract.regimenId, contract.activo, contract.nombreComercial, contract.direccionComercial,
                  contract.address, contract.noExtAddress, contract.noIntAddress, contract.coloniaAddress, contract.municipioAddress, 
                  contract.estadoAddress, contract.paisAddress, contract.cpAddress, contract.nameContactoAdministrativo,
                  contract.emailContactoAdministrativo, contract.telefonoContactoAdministrativo, contract.nameContactoContabilidad,
                  contract.emailContactoContabilidad, contract.telefonoContactoContabilidad, contract.nameContactoDirectivo, 
                  contract.emailContactoDirectivo, contract.telefonoContactoDirectivo, contract.telefonoCelularDirectivo,
                  contract.nameRepresentanteLegal, contract.claveCiec, contract.claveFiel, contract.claveIdse, contract.claveIsn,
                  contract.facturador, contract.metodoDePago, contract.noCuenta, regimen.nombreRegimen, sociedad.nombreSociedad,
                  contract.idTipoClasificacion, tipo_clasificacion.nombre as tipoClasificacion,
                  case
                      when contract.activo = 'No' then contract.fechaBaja
                      when contract.activo = 'Si' then null 
                  end
                  as fechaBaja,
                  ac.*,
                  CONCAT(
                       '[',
                        GROUP_CONCAT(
                            CONCAT(
                                '{\"departamentoId',
                                '\":\"',
                                contractPermiso.departamentoId,
                                '\",\"',
                                'personalId',
                                '\":\"',
                                contractPermiso.personalId,
                                '\",\"',
                                'departamento',
                                '\":\"',
                                departamentos.departamento,
                                '\",\"',
                                'name',
                                '\":\"',
                                personal.name,
                                '\"}'
                            )
                        ),
                      ']'      
                   )  as encargados
                   FROM contract 
                   LEFT JOIN contractPermiso ON contract.contractId = contractPermiso.contractId
                   LEFT JOIN personal ON contractPermiso.personalId = personal.personalId
                   LEFT JOIN departamentos ON contractPermiso.departamentoId = departamentos.departamentoId
                   INNER JOIN regimen ON contract.regimenId = regimen.regimenId
                   LEFT JOIN sociedad ON contract.sociedadId = sociedad.sociedadId
                   LEFT JOIN tipo_clasificacion ON contract.idTipoClasificacion = tipo_clasificacion.id    
                   LEFT JOIN(select actividad_comercial.id as ac_id, actividad_comercial.name as ac_name,
                            sector.id as sector_id, subsector.id as subsector_id, sector.name as sec_name, subsector.name as subsec_name from actividad_comercial
                            inner join subsector on actividad_comercial.subsector_id = subsector.id
                            inner join sector on subsector.sector_id = sector.id 
                            ) as ac  on contract.actividadComercialId = ac.ac_id 
                   WHERE 1 $ftrSubquery  GROUP BY contract.contractId 
             ) a 
             INNER JOIN  customer b ON a.customerId = b.customerId
             WHERE 1 $ftrCustomer $ftrContract $str_group order by b.nameContact ASC, a.name ASC";

        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        $listDepartamentos = $departamentos->GetListDepartamentos();

        foreach ($result as $key => $val) {
            $idResponsable = 0;
            $val['metodoDePago'] = strlen($val['metodoDePago']) > 1 ? $val['metodoDePago'] : "0" . $val['metodoDePago'];
            $metodoDePago = $catalogo->getFormaPagoByClave($val['metodoDePago']);
            $result[$key]['metodoDePago'] = $metodoDePago['descripcion'];
            $result[$key]["numActiveContracts"] = $this->HowManyRazonesSociales($val["customerId"]);
            $encargadosComplete = $findEncargados
                ? $contractRep->encargadosArea($val['contractId'])
                : json_decode($val['encargados'], true);
            $encargados = $encargadosComplete !== null && !empty($encargadosComplete)
                ? $encargadosComplete
                : [];
            foreach ($listDepartamentos as $dep) {
                $key_exist = array_search($dep['departamentoId'], array_column($encargados, 'departamentoId'));

                $currentEncargado = $key_exist !== false ? $encargados[$key_exist] : [];
                $result[$key]['resp' . ucfirst(mb_strtolower(str_replace(" ", "", $dep['departamento'])))] = $currentEncargado['personalId'];
                $result[$key]['name' . ucfirst(mb_strtolower(str_replace(" ", "", $dep['departamento'])))] = $currentEncargado['name'];
                if ($dep['departamentoId'] == 1) {
                    $idResponsable = $currentEncargado['personalId'];
                    $result[$key]["responsable"] = $currentEncargado['name'];
                }
            }
            $contract = new Contract;
            $contract->setContractId($val['contractId']);
            $result[$key]["totalMensual"] = number_format($contract->getTotalIguala(), 2, '.', ',');
            $result[$key]["generaFactura13"] = $val['noFactura13'] == 'Si' ? 'No' : 'Si';


            $jefes = [];
            $personal->setPersonalId($idResponsable);
            $infP = $personal->InfoWhitRol();
            $personal->deepJefesArray($jefes, true);
            $result[$key]["supervisadoBy"] = $jefes['Supervisor'] ?? $jefes['me'];
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
            $result[$key]["contracts"] = $allContracts;
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
                    $idResponsable = !$idResponsable ? 0 : $idResponsable;

                    $result[$key]["contracts"][$keyContract]["responsable"] = $result[$key]["contracts"][$keyContract]['nameContabilidad'];
                    $contract = new Contract;
                    $contract->setContractId($value['contractId']);
                    $result[$key]["contracts"][$keyContract]["totalMensual"] = number_format($contract->getTotalIguala(), 2, '.', ',');

                    $jefes = [];
                    $personal->setPersonalId($idResponsable);
                    $infP = $personal->InfoWhitRol();
                    $personal->deepJefesArray($jefes, true);
                    $needle = strtolower($infP["nameLevel"]);
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
                        $result[$key]["showCliente"] = $filtro->ShowByDefault($serviciosContrato, $User["roleId"]);
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

    public function SuggestCustomerFilter($filter = [], $limit = false)
    {
        $sfSubquery = $sfQueryPermiso = $sfQuery = $sfLimit = "";
        $tipo = strtolower($filter['tipos']);
        switch ($tipo) {
            case 'inactivos':
                $sfQuery .= " and (a.active = '0') ";
                break;
            default:
                $sfQuery .= " and (a.active = '1') ";
                break;
        }
        $allowAccessAnyContract = $this->accessAnyContract();
        if ($allowAccessAnyContract === false)
            return [];

        $sfQuery .= $filter['clienteId'] ? " and a.customerId = '" . $filter['clienteId'] . "' " : "";

        $idImplode = implode(',', $filter['encargados']);
        $sfQueryPermiso .= $allowAccessAnyContract === '0' || $filter['responsableCuenta'] > 0
            ? " and contractPermiso.personalId IN ($idImplode) "
            : "";

        $sfSubquery .= $filter['like'] !== '' ? " or contract.name like '%" . $filter['like'] . "%' " : "";
        $sfQuery .= $filter['like'] !== '' ? " and (a.nameContact like '%" . $filter['like'] . "%' or b.name like '%" . $filter['like'] . "%' or b.rfc like '%" . $filter['like'] . "%') " : "";
        $sfLimit .= $limit ? " LIMIT 15 " : "";

        $sQuery = "SELECT  a.customerId as clienteId, a.nameContact, a.phone, a.email, a.password,a.noFactura13,
                 a.fechaAlta,a.observacion,a.active, b.*
                 FROM customer a 
                 LEFT JOIN (SELECT contract.contractId, contract.name, contract.customerId, contract.type, contract.rfc,
                 contract.regimenId, contract.activo, contract.nombreComercial, contract.direccionComercial,
                 contract.address, contract.noExtAddress, contract.noIntAddress, contract.coloniaAddress, contract.municipioAddress, 
                 contract.estadoAddress, contract.paisAddress, contract.cpAddress, contract.nameContactoAdministrativo,
                 contract.emailContactoAdministrativo, contract.telefonoContactoAdministrativo, contract.nameContactoContabilidad,
                 contract.emailContactoContabilidad, contract.telefonoContactoContabilidad, contract.nameContactoDirectivo, 
                 contract.emailContactoDirectivo, contract.telefonoContactoDirectivo, contract.telefonoCelularDirectivo,
                 contract.nameRepresentanteLegal, contract.claveCiec, contract.claveFiel, contract.claveIdse, contract.claveIsn,
                 contract.facturador, contract.metodoDePago, contract.noCuenta, regimen.nombreRegimen, sociedad.nombreSociedad
                 FROM contract
                 INNER JOIN regimen ON contract.regimenId = regimen.regimenId
                 LEFT JOIN sociedad ON contract.sociedadId = sociedad.sociedadId
                 WHERE 1  $sfSubquery
             ) b
             ON a.customerId = b.customerId   
             WHERE 1 $sfQuery  order by a.nameContact ASC, b.name ASC $sfLimit";

        $this->Util()->DB()->setQuery($sQuery);
        $result = $this->Util()->DB()->GetResult();
        $contract = new Contract;
        $newArray = [];
        foreach ($result as $key => $value) {
            $allow = $allowAccessAnyContract === '1' && $filter['responsableCuenta'] <= 0 ? true : false;
            if (!$allow && $value['contractId'] !== null) {
                $contract->setContractId($value['contractId']);
                $allow = count($contract->getPermisosByContract($sfQueryPermiso));
            }
            if (!$allow)
                continue;

            $cad = $value;
            $cad["doBajaTemporal"] = 0;
            $cad["haveTemporal"] = 0;
            $cad["contractsActivos"] = $this->HowManyRazonesSociales($value["clienteId"], 'Si');
            $cad["contractsInactivos"] = $this->HowManyRazonesSociales($value["clienteId"], 'No');
            $cad["contracts"] = $cad['contractsActivos'] + $cad['contractsInactivos'];

            $parciales = $value['contractId'] !== null ? $this->GetServicesByContract($value["contractId"], 'bajaParcial') : [];

            if ($tipo === 'temporal') {
                if (count($parciales) <= 0)
                    continue;
            }
            array_push($newArray, $cad);
        }
        return $newArray;
    }

    public function EnumerateAllCustomer($filter, $limit = 0)
    {
        $result = $this->SuggestCustomerFilter($filter);
        $clientes = [];
        $items = 0;
        foreach ($result as $key => $value) {
            $parciales = $value['contractId'] !== null ? $this->GetServicesByContract($value["contractId"], 'bajaParcial') : [];
            $serviciosActivos = $value['contractId'] !== null ? $this->GetServicesByContract($value["contractId"], 'activo') : [];
            $allowBajaTemp = $value['activo'] === 'Si' && count($serviciosActivos) > 0 ? count($serviciosActivos) : 0;
            $countTemporal = $value['activo'] === 'Si' && count($parciales) > 0 ? count($parciales) : 0;
            if (!key_exists($value['clienteId'], $clientes)) {
                $cad = $value;
                $cad['doBajaTemporal'] = $allowBajaTemp;
                $cad['haveTemporal'] = $countTemporal;
                $clientes[$value['clienteId']] = $cad;
                $items++;
            } else {
                $clientes[$value['clienteId']]['doBajaTemporal'] += $allowBajaTemp;
                $clientes[$value['clienteId']]['haveTemporal'] += $countTemporal;
            }
            if ($limit) {
                if ($items >= $limit)
                    break;
            }
        }
        return $clientes;
    }

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

    function GetServicesByContract($id, $tipo = "activos")
    {
        //por default se debe tomarse en cuenta los de baja temporal
        if ($tipo == 'activos')
            $ftrStatus = " AND servicio.status IN ('activo','bajaParcial','readonly') ";
        else
            $ftrStatus = " AND servicio.status = '" . $tipo . "' ";

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

     function GetServiciosActivosById($id)
    {
        $ftrStatus = " AND servicio.status IN ('activo') ";

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
					AND activo = '" . $activo . "' ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetSingle();
    }

    function GetRazonesSociales($customerId, $like = "", $limit = 0, $activos = '')
    {
        $strLike = "";
        $strLimit = "";
        if ($like != "") {
            $strLike = " and name like '%" . $like . "%' ";
        }
        if ($limit) {
            $strLimit = " LIMIT 1";
        }
        if ($activos !== '')
            $strLike .= " and activo = '$activos' ";

        $sql = "SELECT contract.*
              		FROM contract
              		WHERE customerId = '" . $customerId . "' $strLike
              		ORDER BY name ASC $strLimit";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        return $result;
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
    public function getListContratos($tipo)
    {
        $sql = "select a.contractId,a.name,a.activo from contract a 
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
    public function getListCustomer($tipo = 'all')
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
        $sql = "select customerId,nameContact from customer where 1 $filtro order by nameContact asc ";
        $this->Util()->DB()->setQuery($sql);
        $results = $this->Util()->DB()->GetResult();
        return $results;
    }

    public function getTotalContratosInPlatform($tipo = 'Activos')
    {
        $ftr = "";
        switch ($tipo) {
            case 'Activos':
                $ftr .= " and a.activo='Si'  and b.active='1'";
                break;
        }
        $sql = "select count(*) from contract a 
                 inner join customer b on a.customerId=b.customerId
                 where 1  $ftr ";
        $this->Util()->DB()->setQuery($sql);
        $total = $this->Util()->DB()->GetSingle();
        return $total;
    }

    public function SuggestAutoComplete($value)
    {
        $this->Util()->DB()->setQuery("SELECT customerId, nameContact, email, phone, observacion  FROM customer WHERE nameContact LIKE '%" . $value . "%' and active ='1' ORDER BY nameContact ASC LIMIT 15");
        $result = $this->Util()->DB()->GetResult();
        return $result;
    }
}
