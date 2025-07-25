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
    private $showAll = false;

    public function setExt($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Extension");
        $this->ext = $value;
    }

    public function isShowAll()
    {
        $this->showAll = true;
    }

    private $celphone;

    public function setCelphone($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Celular");
        $this->celphone = $value;
    }

    private $roleId;

    public function setRole($value)
    {
        if ($this->Util()->ValidateRequireField($value, "Tipo de Usuario(rolId)"))
            $this->roleId = $value;
    }

    private $levelRol;

    public function setLevelRol($value)
    {
        $this->levelRol = $value;
    }

    private $skype;

    public function setSkype($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Skype");
        $this->skype = $value;
    }

    private $numeroCelularInstitucional = null;

    public function setNumeroCelularInstitucional($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Número celular institucional");
        $this->numeroCelularInstitucional = $value;
    }

    private $numeroTelefonicoWebex = null;

    public function setNumeroTelefonicoWebex($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 20, $minChars = 0, "Número telefónico de Webex");
        $this->numeroTelefonicoWebex = $value;
    }

    private $extensionWebex =  null;

    public function setExtensionWebex($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 10, $minChars = 0, "Extensión de Webex");
        $this->extensionWebex = $value;
    }

    private $fechaPromocion =  null;

    public function setFechaPromocion($value)
    {
        $this->fechaPromocion = $value;
    }

    private $resourceId;

    public function setResource($value)
    {
        $this->resourceId = $value;
        if ($this->resourceId)
            $this->resourceIsAvailable();
    }

    private $numberAccountsAllowed;

    public function setNumberAccountsAllowed($value)
    {
        $this->Util()->ValidateOnlyNumeric($value, 'Numero de empresas por administrar');
        $this->numberAccountsAllowed = $value;
    }

    private $cuentaInhouse;
    public function setCuentaInhouse($value)
    {
        $this->Util()->ValidateString($value, 255,0, 'Cuenta');
        $this->cuentaInhouse = $value;
    }

    private $puesto;

    public function setPuesto($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Puesto");
        $this->puesto = $value;
    }

    private $systemAspel;

    public function setSystemAspel($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Sistema aspel");
        $this->systemAspel = $value;
    }

    private $passwordAspel;

    public function setPasswordAspel($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Contraseña aspel");
        $this->passwordAspel = $value;
    }

    private $userAspel;

    public function setUserAspel($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Usuario aspel");
        $this->userAspel = $value;
    }

    private $horario;

    public function setHorario($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Horario");
        $this->horario = $value;
    }

    private $sueldo;

    public function setSueldo($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Sueldo");
        $this->sueldo = $value;
    }

    private $grupo;

    public function setGrupo($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Grupo");
        $this->grupo = $value;
    }

    private $mailGrupo;
    public function setMailGrupo($value)
    {
        $this->Util()->ValidateString($value,  60, 0, "Mail grupo");
        $this->mailGrupo = $value;
    }
    private $listaDistribucion;
    public function setListaDistribucion($value)
    {
        $this->Util()->ValidateString($value,60, 0, "Listas distribución");
        $this->listaDistribucion = $value;
    }

    private $userComputadora;

    public function setUserComputadora($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Usuario computadora");
        $this->userComputadora = $value;
    }

    private $passwordComputadora;

    public function setPasswordComputadora($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Contraseña computadora");
        $this->passwordComputadora = $value;
    }


    public function setPersonalId($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->personalId = $value;
    }

    public function setName($value)
    {
        if ($this->Util()->ValidateRequireField($value, "Nombre"))
            $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, "Nombre");
        $this->name = $value;
    }

    public function setPhone($value)
    {
        $this->phone = $value;
    }

    public function setEmail($value)
    {
        if (strlen($value) > 0)
            $this->Util()->ValidateMail($value, "Correo Electrónico");
        $this->email = $value;
    }

    public function setUsername($value)
    {
        $this->username = $value;
    }

    public function setPasswd($value)
    {
        //$this->Util()->ValidateRequireField($value, "Contraseña");
        $this->passwd = $value;
    }

    public function setActive($value)
    {
        $this->active = $value;
    }

    var $tipoPersonal;

    public function setTipoPersonal($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, "Nombre del puesto");
        $this->tipoPersonal = $value;
    }

    var $nivel;
    public function setNivel($value)
    {
        $this->Util()->ValidateRequireField($value, "Nivel del puesto");
        $this->nivel = $value;
    }

    var $departamentoId;

    public function setDepartamentoId($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->departamentoId = $value;
    }

    var $jefeInmediato;

    public function setJefeInmediato($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->jefeInmediato = $value;
    }

    public function setFechaIngreso($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 0, '');
        $this->fechaIngreso = $value;
    }

    public function Enumerate()
    {
        global $User;

        if ($this->active)
            $sqlActive = " AND a.active = '1'";
        if ($this->levelRol && $this->showAll)
            $sqlFilter = " and d.nivel='" . $this->levelRol . "' ";

        if ((int)$User['level'] == 1 || $this->showAll || $this->accessAnyEmployee()) {
            $sql = "SELECT a.*,b.name as nombreJefe,c.departamento
                    FROM personal a 
                    LEFT JOIN personal b ON a.jefeInmediato=b.personalId 
                    LEFT JOIN roles d on a.roleId=d.rolId
                    LEFT JOIN departamentos c ON a.departamentoId=c.departamentoId WHERE 1 
                    $sqlFilter $sqlActive 
                    ORDER BY a.name ASC";
            $this->Util()->DB()->setQuery($sql);
            $result = $this->Util()->DB()->GetResult();
            return $result;

        }

        $this->setPersonalId($User['userId']);
        $result = $this->SubordinadosDetails();
        foreach ($result as $key => $var) {
            $this->Util()->DB()->setQuery("select departamento from departamentos where departamentoId = '" . $var["departamentoId"] . "' ");
            $result[$key]["departamento"] = $this->Util()->DB()->GetSingle() ? $this->Util()->DB()->GetSingle() : "";
            $result[$key]["nombreJefe"] = $var["jefeName"];
        }
        $result = $this->Util()->orderMultiDimensionalArray($result, 'name');
        return $result;
    }

    public function suggestPersonal($like)
    {
        $ftr = "";
        if (strlen($like) > 1) {
            $ftr .= " AND name LIKE '%$like%' ";
        } else return false;
        $sql = "SELECT * 
                FROM personal
                WHERE 1
                $ftr
                ORDER BY name ASC LIMIT 15";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        return $result;
    }

    public function EnumerateGerenteDepartamento($dep)
    {
        $this->Util()->DB()->setQuery("select departamentoId from departamentos where lower(departamento)='" . strtolower($dep) . "' ");
        $depId = $this->Util()->DB()->GetSingle();
        if (!$depId)
            return [];

        $this->Util()->DB()->setQuery("select personalId from personal inner join roles on personal.roleId=roles.rolId where personal.departamentoId='" . $depId . "' and nivel=2");
        $perId = $this->Util()->DB()->GetSingle();
        if (!$perId)
            return [];

        $this->setPersonalId($perId);
        $result = $this->SubordinadosDetailsAddPass();
        return $result;
    }

    public function EnumerateAll()
    {
        $sql = "select personal.*, roles.nivel  from personal
                inner join roles on personal.roleId = roles.rolId
                order by personal.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    public function ListDepartamentos()
    {
        $filtro = "";
        $filtro .= (int)$_SESSION["User"]["level"] != 1 && !$this->accessAnyDepartament() ?
            " AND departamentoId = '" . $_SESSION["User"]["departamentoId"] . "' "
            : "";
        $sql = "SELECT
                    *
                FROM
                    departamentos
                WHERE estatus =  1    
                    $filtro
                ORDER BY
                    departamento
                ASC ";

        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    public function ListAll()
    {
        if ($this->active)
            $sqlActive = " AND active = '1'";

        $sql = "SELECT
                    *
                FROM
                    personal
                WHERE 1 
                " . $sqlActive . "
                ORDER BY name ASC";

        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

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
                name like '%" . $name . "%' AND
                active = '1'
            ORDER BY name ASC
        ";

        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    public function Info()
    {
        $this->Util()->DB()->setQuery("SELECT * FROM personal WHERE personalId = '" . $this->personalId . "'");
        $row = $this->Util()->DB()->GetRow();
        $row['fechaIngresoMysql'] = $this->Util()->FormatDateMySql($row['fechaIngreso']);
        $row['resource'] = $this->getCurrentResponsableResource();
        return $row;
    }

    public function InfoWhitRol()
    {
        $this->Util()->DB()->setQuery("SELECT a.personalId,a.name,a.roleId,b.name as nameRol,b.nivel,a.sueldo,a.jefeInmediato, a.departamentoId,
                                             CASE 
                                             WHEN (b.nivel = 1 AND a.roleId = 5) THEN 'Coordinador'
                                             WHEN b.nivel = 100 THEN (SELECT name from porcentajesBonos order by categoria DESC LIMIT 1)
                                             WHEN b.nivel < 100 THEN (SELECT name from porcentajesBonos WHERE categoria = b.nivel LIMIT 1)  
                                                 END AS nameLevel
                                             FROM personal a INNER JOIN roles b ON a.roleId=b.rolId WHERE a.personalId = '" . $this->personalId . "'");
        $row = $this->Util()->DB()->GetRow();
        $this->Util()->DB()->setQuery("select name from personal where personalId='" . $row['jefeInmediato'] . "' ");
        $row["nameJefeInmediato"] = $this->Util()->DB()->GetSingle();
        $this->Util()->DB()->setQuery("select porcentaje from porcentajesBonos where categoria='" . $row['nivel'] . "' ");
        $row["porcentajeBono"] = $this->Util()->DB()->GetSingle();

        return $row;
    }

    public function jefeInmediato()
    {
        $this->Util()->DB()->setQuery("SELECT j.* FROM personal a INNER JOIN personal j ON a.jefeInmediato=j.personalId WHERE a.personalId = '" . $this->personalId . "'");
        $row = $this->Util()->DB()->GetRow();
        $row['fechaIngresoMysql'] = $this->Util()->FormatDateMySql($row['fechaIngreso']);
        $this->Util()->DB()->setQuery("select name from personal where personalId='" . $row['jefeInmediato'] . "' ");
        $row["nameJefeInmediato"] = $this->Util()->DB()->GetSingle();
        return $row;
    }

    public function Edit()
    {
        if ($this->Util()->PrintErrors()) {
            return false;
        }
        $strUpdate = "";
        if (strlen($this->sueldo) > 0) {
            if (!is_numeric($this->sueldo))
                $this->sueldo = 0;
            $strUpdate .= " sueldo = '" . $this->sueldo . "', ";
        }


        if (strlen($this->phone) > 0)
            $strUpdate .= " phone='" . $this->phone . "', ";
        if (isset($_POST['email']))
            $strUpdate .= " email='" . $this->email . "', ";
        if (strlen($this->username) > 0)
            $strUpdate .= " username='" . $this->username . "', ";
        if (strlen($this->passwd) > 0)
            $strUpdate .= " passwd='" . $this->passwd . "', ";
        if (strlen($this->ext) > 0)
            $strUpdate .= "ext='" . $this->ext . "', ";
        if (strlen($this->celphone) > 0)
            $strUpdate .= " celphone='" . $this->celphone . "', ";
        if (strlen($this->systemAspel) > 0)
            $strUpdate .= " systemAspel='" . $this->systemAspel . "', ";
        if (strlen($this->userAspel) > 0)
            $strUpdate .= " userAspel='" . $this->userAspel . "', ";
        if (strlen($this->passwordAspel) > 0)
            $strUpdate .= " passwordAspel='" . $this->passwordAspel . "', ";
        if (strlen($this->skype) > 0)
            $strUpdate .= " skype='" . $this->skype . "', ";
        if (!is_null($this->numeroCelularInstitucional))
            $strUpdate .= " numeroCelularInstitucional ='" . $this->numeroCelularInstitucional . "', ";
        if (!is_null($this->numeroTelefonicoWebex))
            $strUpdate .= " numeroTelefonicoWebex ='" . $this->numeroTelefonicoWebex . "', ";
        if (!is_null($this->extensionWebex))
            $strUpdate .= " extensionWebex ='" . $this->extensionWebex . "', ";
        if (!is_null($this->fechaPromocion)) {
            $fechaPromocion = $this->fechaPromocion === ''? 'NULL' : "'".$this->fechaPromocion."'";
            $strUpdate .= " fechaPromocion =". $fechaPromocion.",";
        }
        if (strlen($this->horario) > 0)
            $strUpdate .= " horario='" . $this->horario . "', ";
        if (strlen($this->fechaIngreso) > 0)
            $strUpdate .= " fechaIngreso='" . $this->fechaIngreso . "', ";
        if (strlen($this->userComputadora) > 0)
            $strUpdate .= " userComputadora='" . $this->userComputadora . "', ";
        if (strlen($this->passwordComputadora) > 0)
            $strUpdate .= " passwordComputadora='" . $this->passwordComputadora . "', ";
        if (strlen($this->grupo) > 0)
            $strUpdate .= " grupo='" . $this->grupo . "', ";
        if (strlen($this->mailGrupo) > 0)
            $strUpdate .= " mailGrupo='" . $this->mailGrupo . "', ";
        if (strlen($this->listaDistribucion) > 0)
            $strUpdate .= " listaDistribucion='" . $this->listaDistribucion . "', ";
        if (strlen($this->tipoPersonal) > 0)
            $strUpdate .= " tipoPersonal='" . $this->tipoPersonal . "', ";
        if (strlen($this->nivel) > 0)
            $strUpdate .= " nivel='" . $this->nivel . "', ";
        if (strlen($this->roleId) > 0)
            $strUpdate .= " roleId='" . $this->roleId . "', ";
        if (strlen($this->departamentoId) > 0)
            $strUpdate .= " departamentoId='" . $this->departamentoId . "', ";
        if (strlen($this->jefeInmediato) > 0)
            $strUpdate .= " jefeInmediato='" . $this->jefeInmediato . "', ";

        if (isset($_POST['fechaCompra']) && $this->Util()->isValidateDate($_POST['fechaCompra'], 'd-m-Y'))
            $strUpdate .= " fechaCompra='" . $this->Util()->FormatDateMySql($_POST['fechaCompra']) . "', ";

        if (strlen($this->numberAccountsAllowed) > 0)
            $strUpdate .= " numberAccountsAllowed='" . $this->numberAccountsAllowed . "', ";

        if (strlen($this->cuentaInhouse) > 0)
            $strUpdate .= " cuentaInhouse='" . $this->cuentaInhouse . "', ";

        $this->Util()->DB()->setQuery("
            UPDATE
                personal
            SET
                `name` = '" . $this->name . "',
                $strUpdate
                active = '" . $this->active . "'
            WHERE personalId = '" . $this->personalId . "'");
        $this->Util()->DB()->UpdateData();
        //actualizar los expedientes.
        if (isset($_POST["expe"])) {
            if (!empty($_POST['expe'])) {
                $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("SELECT expedienteId from personalExpedientes WHERE personalId='" . $this->personalId . "' ");
                $arrayExp = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
                $expActual = $this->Util()->ConvertToLineal($arrayExp, 'expedienteId');

                foreach ($_POST['expe'] as $exp) {
                    //si ya tiene archivo adjunto no debe reemplazar
                    $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("select expedienteId from personalExpedientes where expedienteId='$exp' and personalId='" . $this->personalId . "' ");
                    $expExist = $this->Util()->DBSelect($_SESSION['empresaId'])->GetSingle();
                    $key = array_search($exp, $expActual);
                    unset($expActual[$key]);
                    if ($expExist)
                        continue;
                    $sql = "INSERT INTO personalExpedientes(personalId,expedienteId)VALUES(" . $this->personalId . ",$exp)";
                    $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
                    $this->Util()->DBSelect($_SESSION['empresaId'])->InsertData();
                }

                if (!empty($expActual)) {
                    //eliminar los expedientes que se deseleccionaron incluyendo su archivo si tiene
                    foreach ($expActual as $expA) {
                        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("select path from personalExpedientes where expedienteId='$expA' and personalId='" . $this->personalId . "' ");
                        $nameFile = $this->Util()->DBSelect($_SESSION['empresaId'])->GetSingle();
                        $file = DOC_ROOT . "/expedientes/" . $this->personalId . "/" . $nameFile;
                        $sqlu = "DELETE FROM personalExpedientes WHERE expedienteId='$expA'AND personalId='" . $this->personalId . "'";
                        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sqlu);
                        $this->Util()->DBSelect($_SESSION['empresaId'])->DeleteData();
                        if (file_exists($file) && is_file($file)) {
                            unlink($file);
                        }
                    }

                }
            }
        }
        // relacionar el recurso
        $this->assignResource();
        $this->Util()->setError(10049, "complete");
        $this->Util()->PrintErrors();
        return true;
    }

    public function Save()
    {
        if ($this->Util()->PrintErrors()) {
            return false;
        }

        if (!is_numeric($this->sueldo))
            $this->sueldo = 0;

        $fechaCompra    = $this->Util()->isValidateDate($_POST['fechaCompra']) ? $this->Util()->FormatDateMySql($_POST['fechaCompra']) : '';
        $fechaPromocion = $this->fechaPromocion === ''? 'NULL' : "'".$this->fechaPromocion."'";

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
                systemAspel,
                userAspel,
                passwordAspel,
                puesto,
                horario,
                sueldo,
                grupo,
                jefeInmediato,
                userComputadora,
                passwordComputadora,
                tipoPersonal,
                nivel,
                roleId,
                departamentoId,
                fechaIngreso,
                lastChangePassword,
                active,
                fechaCompra,
                numberAccountsAllowed,
                mailGrupo,
                listaDistribucion,
                cuentaInhouse,
                numeroCelularInstitucional,
                numeroTelefonicoWebex,
                extensionWebex,
                fechaPromocion
        )
        VALUES
        (
                '" . $this->name . "',
                '" . $this->phone . "',
                '" . $this->email . "',
                '" . $this->username . "',
                '" . $this->passwd . "',
                '" . $this->ext . "',
                '" . $this->celphone . "',
                '" . $this->skype . "',
                '" . $this->systemAspel . "',
                '" . $this->userAspel . "',
                '" . $this->passwordAspel . "',
                '" . $this->puesto . "',
                '" . $this->horario . "',
                '" . $this->sueldo . "',
                '" . $this->grupo . "',
                '" . $this->jefeInmediato . "',
                '" . $this->userComputadora . "',
                '" . $this->passwordComputadora . "',
                '" . trim($this->tipoPersonal) . "',
                '" . trim($this->nivel) . "',
                '" . trim($this->roleId) . "',
                '" . $this->departamentoId . "',
                '" . $this->fechaIngreso . "',
                '" . $this->fechaIngreso . "',
                '" . $this->active . "',
                '" . $fechaCompra . "',
                '" . $this->numberAccountsAllowed . "',
                '" . $this->mailGrupo. "',
                '" . $this->listaDistribucion. "',
                '" . $this->cuentaInhouse. "',
                '" . $this->numeroCelularInstitucional. "',
                '" . $this->numeroTelefonicoWebex. "',
                '" . $this->extensionWebex. "',
                $fechaPromocion
        );");
        $id = $this->Util()->DB()->InsertData();
        if (isset($_POST["expe"])) {
            if (!empty($_POST['expe'])) {
                $sql = 'REPLACE INTO personalExpedientes(personalId,expedienteId) VALUES';
                foreach ($_POST['expe'] as $exp) {
                    if ($exp === end($_POST['expe']))
                        $sql .= "(" . $id . "," . $exp . ");";
                    else
                        $sql .= "(" . $id . "," . $exp . "),";
                }
                $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
                $this->Util()->DBSelect($_SESSION['empresaId'])->UpdateData();
            }
        }
        // relacionar el recurso
        $this->setPersonalId($id);
        $this->assignResource();
        $this->Util()->setError(10048, "complete");
        $this->Util()->PrintErrors();
        return true;
    }

    public function Delete()
    {
        if ($this->Util()->PrintErrors()) {
            return false;
        }

        $this->Util()->DB()->setQuery("
            DELETE FROM
                personal
            WHERE
                personalId = '" . $this->personalId . "'");
        $affect = $this->Util()->DB()->DeleteData();
        if ($affect > 0)
        $current = $this->getCurrentResponsableResource();
        if ($current) {
                $sql = "UPDATE responsables_resource_office 
                       SET status = 'Baja',
                       motivo_baja_responsable = 'Se elimino usuario',
                       fecha_liberacion_responsable='" . date('Y-m-d') . "' 
                       WHERE responsable_resource_id = '" . $current['responsable_resource_id'] . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
        }

        $this->Util()->setError(10050, "complete", "Has eliminado al Contador satisfactoriamente");
        $this->Util()->PrintErrors();
        return true;
    }

    public function GetDataReport()
    {

        $sql = 'SELECT
                    name,
                    tipoPersonal
                FROM
                    personal
                WHERE
                    personalId = ' . $this->personalId;

        $this->Util()->DB()->setQuery($sql);

        return $this->Util()->DB()->GetRow();

    }

    public function GetNameById()
    {

        $sql = 'SELECT
                    name
                FROM
                    personal
                WHERE
                    personalId = ' . $this->personalId;

        $this->Util()->DB()->setQuery($sql);

        return $this->Util()->DB()->GetSingle();
    }

    function Restrict()
    {
        global $infoUser, $page;

        $restricted = array();

        switch ($infoUser["tipoPersonal"]) {
            case "Recepcion":
                $restricted = array(
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
        if (in_array($page, $restricted)) {
            header("Location:" . WEB_ROOT);
        }
    }

    function GetCascadeSubordinates()
    {
        $sql = "SELECT personal.*, jefes.name AS jefeName, roles.name as nameRol, roles.nivel,
                CASE 
                 WHEN (roles.nivel = 1 AND personal.roleId = 5) THEN 'Coordinador'
                 WHEN roles.nivel = 100 THEN (SELECT name from porcentajesBonos order by categoria DESC LIMIT 1)
                 WHEN roles.nivel < 100 THEN (SELECT name from porcentajesBonos WHERE categoria = roles.nivel LIMIT 1)  
                 END AS nameLevel
               FROM personal
               LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato 
               INNER JOIN roles on personal.roleId = roles.rolId ORDER BY personal.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult($sql);
        $jerarquia = $this->Jerarquia($result, $this->personalId);
        $new = [];
        $this->JerarquiaLinealReferencia($new, $jerarquia);
        return $new;
    }

    function Subordinados($whitDpto = false)
    {
        $sql = "SELECT personal.*, jefes.name AS jefeName FROM personal
        LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult($sql);

        $jerarquia = $this->Jerarquia($result, $this->personalId);

        $_SESSION["lineal"] = array();
        if ($whitDpto)
            $this->JerarquiaLinealWhitDpto($jerarquia);
        else
            $this->JerarquiaLinealJustId($jerarquia);

        return $_SESSION["lineal"];
    }

    function AddMeToArray()
    {
        $sql = "SELECT personal.*, jefes.name AS jefeName FROM personal
        LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato WHERE personal.personalId = '" . $_SESSION["User"]["userId"] . "'ORDER BY name ASC";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow($sql);

        array_unshift($_SESSION["lineal"], $row);
    }

    function AddPassToArray()
    {
        $sql = "SELECT personal.*, jefes.name AS jefeName FROM personal
    LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato WHERE personal.personalId = '" . $this->personalId . "'ORDER BY name ASC";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow($sql);

        array_unshift($_SESSION["lineal"], $row);
    }

    function SubordinadosDetails()
    {
        $sql = "SELECT personal.*, jefes.name AS jefeName FROM personal
           LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult($sql);

        $jerarquia = $this->Jerarquia($result, $this->personalId);

        $_SESSION["lineal"] = array();
        $this->JerarquiaLineal($jerarquia);

        $this->AddMeToArray();

        return $_SESSION["lineal"];
    }

    function SubordinadosDirectos() {
        $sql = "SELECT personal.*, jefes.name AS jefeName, roles.name as nameRol, roles.nivel FROM personal
                LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato 
                inner join roles as roles on personal.roleId=roles.rolId 
                where personal.jefeInmediato = '".$this->personalId."' ORDER BY name ASC
        ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }

    function SubordinadosDetailsAddPass()
    {
        $sql = "SELECT personal.*, jefes.name AS jefeName, roles.nivel FROM personal
       LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato 
       inner join roles as roles on personal.roleId=roles.rolId ORDER BY name ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult($sql);

        $jerarquia = $this->Jerarquia($result, $this->personalId);

        $_SESSION["lineal"] = array();
        $this->JerarquiaLineal($jerarquia);
        $this->AddPassToArray();

        return $_SESSION["lineal"];
    }

    function GetRoleId($tipoPersonal)
    {
        $this->Util()->DB()->setQuery("select rolId from roles where name='".$tipoPersonal."'");
        return $this->Util()->DB()->GetSingle();
    }

    function Jerarquia(array $elements, $parentId = 0)
    {
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

    function JerarquiaLinealReferencia(array &$new, $tree)
    {
        foreach ($tree as $key => $value) {
            $subs = [];
            $children= !is_array($value['children']) ? [] : $value['children'];
            $this->JerarquiaJustIdByReference($subs, $children);
            $value['subordinadosId'] = $subs;
            $new[] = $value;
            if (count($children) > 0) {
                $this->JerarquiaLinealReferencia($new, $children);
            }
        }
    }

    function JerarquiaJustIdByReference(array &$lineal, $tree)
    {
        foreach ($tree as $key => $value) {
            array_push($lineal, $value['personalId']);
            if (count($value["children"]) > 0) {
                $this->JerarquiaJustIdByReference($lineal, $value["children"]);
            }
        }
    }

    function JerarquiaLineal($tree)
    {

        foreach ($tree as $key => $value) {
            $_SESSION["lineal"][] = $value;
            if (count($value["children"]) > 0) {
                $this->JerarquiaLineal($value["children"]);
            }
        }
    }

    function JerarquiaLinealJustId($tree)
    {
        foreach ($tree as $key => $value) {
            $card["personalId"] = $value["personalId"];
            $_SESSION["lineal"][] = $card;

            if (@count($value["children"]) > 0) {
                $this->JerarquiaLinealJustId($value["children"]);
            }
        }
    }

    function JerarquiaLinealWhitDpto($tree)
    {
        foreach ($tree as $key => $value) {
            $card["personalId"] = $value["personalId"];
            $card["dptoId"] = $value["departamentoId"];
            $_SESSION["lineal"][] = $card;

            if (count($value["children"]) > 0) {
                $this->JerarquiaLinealWhitDpto($value["children"]);
            }
        }
    }

    function ArrayOrdenadoPersonal()
    {
        $sql = "SELECT personal.personalId, personal.name, personal.tipoPersonal, personal.jefeInmediato, jefes.name AS jefeName FROM personal
            LEFT JOIN personal AS jefes ON jefes.personalId = personal.jefeInmediato ORDER BY name ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult($sql);

        $jerarquia = $this->Jerarquia($result);

        $_SESSION["lineal"] = array();
        $this->JerarquiaLineal($jerarquia);

        $lineal = $_SESSION["lineal"];
        return $lineal;

    }

    function jefes($inputId = 0, $idList = array())
    {
        $db = new DB();
        $sql = "SELECT * FROM personal where personalId='" . $inputId . "'";
        $db->setQuery($sql);
        $result = $db->GetResult();

        if ($result) {
            $currentId = $result[0]["personalId"];
            $parentId = $result[0]["jefeInmediato"];

            $idList[] = $currentId;

            if ($parentId != 0) {
                return $this->jefes($parentId, $idList);
            }
        }
        return $idList;
    }

    public function GetExpedientes()
    {
        $sql = "SELECT a.expedienteId,a.path,a.personalId,b.name,a.fecha,b.extension from personalExpedientes a LEFT JOIN expedientes b ON a.expedienteId=b.expedienteId WHERE a.personalId='" . $this->personalId . "' and b.status='activo' ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
        foreach ($result as $key => $value) {
            $file = DOC_ROOT . "/expedientes/" . $this->personalId . "/" . $value['path'];
            if (file_exists($file) && is_file($file)) {
                $ext = end(explode('.', $value['path']));
                $result[$key]['findFile'] = true;
                $result[$key]['ext'] = $ext;
            } else
                $result[$key]['findFile'] = false;
        }
        return $result;
    }

    function deepJefesArray(&$jefes = [], $me = false)
    {
        $employe = $this->InfoWhitRol();
        if ($me)
            $jefes['me'] = $employe['name'];

        if ($employe["jefeInmediato"]) {
            $this->setPersonalId($employe['jefeInmediato']);
            $inmediato = $this->InfoWhitRol();
            $jefes[$inmediato["nameLevel"]] = $inmediato["name"];
            $this->setPersonalId($inmediato["personalId"]);
            $this->deepJefesArray($jefes);
        }
    }

    function deepJefesByLevel(&$jefes = [], $me = false)
    {
        global $rol;
        $employe = $this->InfoWhitRol();
        if ($me)
            $jefes['me'] = $employe['name'];

        if ($employe["jefeInmediato"]) {
            $this->setPersonalId($employe['jefeInmediato']);
            $inmediato = $this->InfoWhitRol();
            $jefes[$inmediato["nivel"]] = $inmediato["name"];
            $this->setPersonalId($inmediato["personalId"]);
            $this->deepJefesByLevel($jefes);
        }
    }

    function deepJefesAssoc(&$jefes = [], $me = false)
    {
        global $rol;
        $employe = $this->InfoWhitRol();
        if ($me)
            $jefes['me'] = $employe;

        if ($employe["jefeInmediato"]) {
            $this->setPersonalId($employe['jefeInmediato']);
            $inmediato = $this->InfoWhitRol();
            $jefes[$inmediato["nameLevel"]] = $inmediato;
            $this->setPersonalId($inmediato["personalId"]);
            $this->deepJefesAssoc($jefes);
        }
    }

    function getListaPersonalPuestoAsc() {

        $sql = "SELECT
            personal.personalId id,
            personal.name nombre,
            personal.roleId rol_id,
            personal.sueldo sueldo,
            personal.jefeInmediato jefe,
            departamentos.departamento,
            (SELECT name FROM porcentajesBonos WHERE categoria = roles.nivel LIMIT 1) puesto
            FROM personal
            INNER JOIN roles ON personal.roleId = roles.rolId
            LEFT JOIN departamentos ON personal.departamentoId = departamentos.departamentoId               
            ORDER BY roles.nivel ASC
        ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    function superiores($id) {

        $resultados = $this->getListaPersonalPuestoAsc();
        $deep = [];
        $this->superioresRecursivo($resultados, $id, $deep);

        return $deep;
    }

    function findSuperiores($id, $rows) {

        $deep = [];
        $this->superioresRecursivo($rows, $id, $deep);

        return $deep;
    }

    function superioresRecursivo(array $nodos, $id, &$nested)
    {
        $current = current(array_filter($nodos, fn($item) => $item['id'] == $id));
        array_push($nested, $current);
        if($current['jefe']) {
            $this->superioresRecursivo($nodos, $current['jefe'], $nested);
        }
    }

    function inferiores($id) {
        $resultados = $this->getListaPersonalPuestoAsc();
        return $this->inferioresRecursivo($resultados, $id);
    }

    function inferioresRecursivo(array $nodos, $padre)
    {
        $nested = [];
        foreach($nodos as $nodo) {
            if($nodo['jefe'] == $padre)  {
                $nested [] = $nodo;
                $nested = array_merge($nested, $this->inferioresRecursivo($nodos,$nodo['id']));
            }
        }
        return $nested;
    }

    public function changePassword()
    {
        $sendmail = new SendMail;
        $sql = "SELECT * FROM personal WHERE active='1' ORDER BY personalId ASC ";
        $this->Util()->DB()->setQuery($sql);
        $results = $this->Util()->DB()->GetResult();

        foreach ($results as $key => $item) {
            //if($item['personalId']!='259')
            //  continue;
            $cadena = "";
            $cadena = $this->Util()->generateRandomString(6, true);
            $this->Util()->DB()->setQuery("UPDATE personal SET passwd='" . $cadena . "' WHERE personalId='" . $item['personalId'] . "' ");
            $this->Util()->DB()->UpdateData();
            //if($this->Util()->DB()->UpdateData()){
            $this->Util()->DB()->setQuery("UPDATE personal SET lastChangePassword='" . date('Y-m-d') . "' WHERE personalId='" . $item['personalId'] . "' ");
            $this->Util()->DB()->UpdateData();
            $body = "ESTIMADO USUARIO CON EL FIN DE MANTENER LA SEGURIDAD DE SUS DATOS Y DE LOS CLIENTES QUE SE ENCUENTRAN EN LA PLATAFORMA BAJO SU RESPONSABILIDAD 
                SE HA REALIZADO EL CAMBIO DE CONTRASE&Ntilde;A DE SU CUENTA, CIERRE SU SESSION SI SE ENCUENTRA ACTUALMENTE EN LA PLATAFORMA E INGRESE NUEVAMENTE CON LOS SIGUIENTES DATOS:  <br>
                USUARIO:" . $item['username'] . " <br>
                PASSWD:" . $cadena . " 
                <br><br>
                Este correo se creo automaticamente, favor de no responder.
                ";
            $subject = "CAMBIO DE CONTRASEÑA " . $item['name'];
            $to = $item['email'];
            $toName = $item['name'];
            $sendmail->Prepare($subject, $body, $to, $toName, '', "", "", "", 'noreply@braunhuerin.com.mx', "ADMINISTRADOR DE PLATAFORMA");
            //}
        }
        $this->Util()->setError(0, "complete", "Se han cambiado las contraseñas correctamente");
        $this->Util()->PrintErrors();
        return true;
    }

    public function unlinkExpendiente($expedienteId, $perId)
    {
        $base_path = DOC_ROOT . '/expedientes';
        //almacenar el archivo en el servidor
        $dir_employe = $base_path . '/' . $perId;
        //comprobar si tiene un archivo actualmente y eliminarlo
        $sqlc = "SELECT path FROM personalExpedientes  WHERE personalId='" . $perId . "' AND expedienteId='" . $expedienteId . "'";
        $this->Util()->DB()->setQuery($sqlc);
        $nameFile = $this->Util()->DB()->GetSingle();
        $file = $dir_employe . '/' . $nameFile;
        if (file_exists($file) && is_file($file)) {
            if (unlink($file)) {
                $sql = "UPDATE personalExpedientes SET path=null,fecha=null WHERE personalId='" . $perId . "' AND expedienteId='" . $expedienteId . "'";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
                $this->Util()->setError(0, 'complete', 'Archivo eliminado correctamente');
                $this->Util()->PrintErrors();
                return true;
            } else {
                $this->Util()->setError(0, 'error', 'Error al eliminar archivo');
                $this->Util()->PrintErrors();
                return false;
            }
        } else {
            $this->Util()->setError(0, 'error', 'Error al encontrar archivo');
            $this->Util()->PrintErrors();
            return false;
        }
    }

    public function findSupervisor($id, $associative = false)
    {
        $this->setPersonalId($id);
        $jefes = [];
        if ($associative)
            $this->deepJefesAssoc($jefes, true);
        else
            $this->deepJefesArray($jefes, true);

        return $jefes['Supervisor'] ?? $jefes['me'];
    }

    public function getOrdenJefes()
    {
        $puestos =  $this->getPuestos();
        $ordenJefes = [];
        $this->setPersonalId($this->personalId);
        $infP = $this->InfoWhitRol();
        $needle = strtolower($infP["nameLevel"]);

        if (!empty($infP)) {

            $jefes = array();
            $this->deepJefesArray($jefes, true);
            foreach ($puestos as $puesto) {
               $ordenJefes[$puesto['name']] = $jefes[$puesto['name']] ?? 'NE';
            }
            $ordenJefes[$needle] = $jefes['me'];

        } else {

            foreach ($puestos as $puesto) {
                $ordenJefes[$puesto['name']] = 'NE';
            }
        }
        return $ordenJefes;
    }

    public function getPuestos()
    {
        $sql = "SELECT name,categoria FROM porcentajesBonos ORDER BY categoria DESC";
        $this->Util()->DB()->setQuery($sql);
        $results = $this->Util()->DB()->GetResult();

        return array_column($results, null, 'categoria');
    }
    public function ListSocios()
    {
        if ($this->active)
            $sqlActive = " AND active = '1'";

        $sql = "SELECT * FROM personal
                WHERE tipoPersonal = 'Socio' OR roleId in(1,5)
                " . $sqlActive . "
                ORDER BY name ASC";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    public function GetIdResponsablesSubordinados($filtro = [])
    {
        global $User;
        $idPersons = [];
        if ((int)$User["level"] == 1 || $this->showAll || $this->accessAnyContract() === '1') {
            if ($filtro['responsableCuenta'] == 0) {
                $this->setActive(1);
                $socios = $this->ListSocios();
                foreach ($socios as $res) {
                    array_push($idPersons, $res['personalId']);
                    $this->setPersonalId($res['personalId']);
                    $subordinados = $this->Subordinados();
                    if (empty($subordinados))
                        continue;

                    $subsLine = $this->Util()->ConvertToLineal($subordinados, 'personalId');
                    $idPersons = array_merge($idPersons, $subsLine);
                    unset($subsLine);
                    unset($subordinados);
                }
                $idPersons = array_unique($idPersons);
            } else {
                $idPersons = array();
                $respCuenta = $filtro['responsableCuenta'];
                array_push($idPersons, $respCuenta);
                if ($filtro['deep']) {
                    $this->setPersonalId($respCuenta);
                    $subordinados = $this->Subordinados();
                    if (!empty($subordinados)) {
                        $subsLine = $this->Util()->ConvertToLineal($subordinados, 'personalId');
                        $idPersons = array_merge($idPersons, $subsLine);
                        unset($subsLine);
                        unset($subordinados);
                    }
                }
            }
        } else {
            $idPersons = array();
            if ($filtro['responsableCuenta'] == 0)
                $respCuenta = $User['userId'];
            else
                $respCuenta = $filtro['responsableCuenta'];
            array_push($idPersons, $respCuenta);
            if ($filtro['deep']) {
                $this->setPersonalId($respCuenta);
                $subordinados = $this->Subordinados();
                if (!empty($subordinados)) {
                    $subsLine = $this->Util()->ConvertToLineal($subordinados, 'personalId');
                    $idPersons = array_merge($idPersons, $subsLine);
                    unset($subsLine);
                    unset($subordinados);
                }
            }
        }
        return $idPersons;
    }

    public function getCurrentUser()
    {
        if (!$_SESSION['User']['userId'])
            return false;

        $this->Util()->DB()->setQuery('SELECT * FROM personal WHERE personalId="' . $_SESSION['User']['userId'] . '" ');
        $row = $this->Util()->DB()->GetRow();
        if ($_SESSION['User']['tipoPers'] == 'Admin') {
            $row['name'] = "Administrador de sistema";
            $row['personalId'] = 999990000;
        }
        return $row;
    }

    /*
     * funcion getListPersonalByDepartamento retorna informacion de personas dado un departamento que se pasa mediante el metodo setDepartamentoId() del objeto persona.
     * @field parametro que definira que se retornara como clave valor
     * - email: retorta como clave el correo y valor el nombre de la persona, solo si el correo es valido
     * -default: retorna un array associativo de todos los campos de una persona
     */
    public function getListPersonalByDepartamento($field = "")
    {
        $return = [];
        $this->Util()->DB()->setQuery("SELECT * FROM personal WHERE departamentoId='" . $this->departamentoId . "' ORDER BY name asc ");
        $empleados = $this->Util()->DB()->GetResult();
        switch ($field) {
            case 'email':
                foreach ($empleados as $key => $value) {
                    if ($this->Util()->ValidateEmail($value["email"]))
                        $return[$value["email"]] = $value["name"];
                }
                break;
            default:
                $return = $empleados;
                break;
        }
        return $return;
    }

    function getTotalSalarioByMultipleId($id = [])
    {
        if (empty($id))
            return 0;

        $this->Util()->DB()->setQuery("select sum(sueldo) from personal where personalId IN (" . implode(',', $id) . ") ");
        return $this->Util()->DB()->GetSingle();
    }

    function GenerateReportExpediente()
    {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("select expedienteId,name from expedientes where status='activo' order by name ASC ");
        $expedientes = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
        $baseExp = [];
        foreach ($expedientes as $exp) {
            $cad["background"] = "#EFEFEF"; //iniciar class para todos expedientes como no aplica
            $cad["name"] = $exp["name"];
            $cad["fecha"] = "";
            $baseExp[$exp["expedienteId"]] = $cad;
        }
        $filter = "";
        if ($this->personalId)
            $filter .= " and personalId = '" . $this->personalId . "' ";

        $sql = "select personalId,name from personal where 1 $filter order by name asc";
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql);
        $employes = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
        foreach ($employes as $key => $value) {
            $complete = 0;
            $baseExpOwn = $baseExp;
            $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("select expedienteId,path,fecha from personalExpedientes where personalId = '" . $value["personalId"] . "' ");
            $ownFiles = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
            $countOwnFiles = count($ownFiles);

            foreach ($ownFiles as $ownFile) {
                $file = DOC_ROOT . "/expedientes/" . $value["personalId"] . "/" . $ownFile["path"];
                if (file_exists($file) && is_file($file)) {
                    $baseExpOwn[$ownFile["expedienteId"]]["background"] = "#009900";
                    $baseExpOwn[$ownFile["expedienteId"]]["fecha"] = $ownFile["fecha"];
                    $complete++;
                } else {
                    if (array_key_exists($ownFile["expedienteId"], $baseExpOwn))
                        $baseExpOwn[$ownFile["expedienteId"]]["background"] = "#F00";
                }
            }
            switch ($_POST["status"]) {
                case 'incomplete':
                    if ($complete < $countOwnFiles)
                        $employes[$key]["ownExpedientes"] = $baseExpOwn;
                    else
                        unset($employes[$key]);
                    break;
                case 'complete':
                    if ($complete == $countOwnFiles && $countOwnFiles > 0)
                        $employes[$key]["ownExpedientes"] = $baseExpOwn;
                    else
                        unset($employes[$key]);
                    break;
                default:
                    if ($countOwnFiles > 0)
                        $employes[$key]["ownExpedientes"] = $baseExpOwn;
                    else
                        $employes[$key]["ownExpedientes"] = [];

                    break;
            }
        }
        if (!is_array($employes))
            $employes = [];

        $data["employes"] = $employes;
        $data["expedientes"] = $expedientes;
        return $data;
    }

    // funciones para control de equipo relacionado
    public function assignResource()
    {
        $current = $this->getCurrentResponsableResource();
        $insertData = true;
        if ($current) {
            if ($current['office_resource_id'] !== $this->resourceId) {
                $sql = "UPDATE responsables_resource_office 
                       SET status = 'Baja',
                       motivo_baja_responsable = 'Edicion desde personal',
                       fecha_liberacion_responsable='" . date('Y-m-d') . "' 
                       WHERE responsable_resource_id = '" . $current['responsable_resource_id'] . "' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
            } else {
                $insertData = false;
            }

        }
        if (!$this->resourceId)
            $insertData = false;

        if ($insertData) {
            $sql = "INSERT INTO responsables_resource_office(
                             office_resource_id,
                             personalId,
                             fecha_entrega_responsable,
                             tipo_responsable,
                             usuario_creador,
                             status,
                             fecha_creacion
                             )values(
                              '" . $this->resourceId . "',
                              '" . $this->personalId . "',
                              '" . date('Y-m-d') . "',
                              'Principal',
                              '" . $_SESSION['User']['username'] . "',
                              'Activo',
                              '" . date("Y-m-d H:i:s") . "'       
                             )
                            ";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->InsertData();
        }
        return true;
    }

    function resourceIsAvailable()
    {
        $ftr = "";
        if ($this->personalId)
            $ftr .= " and personalId != '". $this->personalId . "'";

        $sql = "select  personalId from responsables_resource_office where office_resource_id = '". $this->resourceId."' and status = 'Activo' $ftr ";
        $this->Util()->DB()->setQuery($sql);
        $find = $this->Util()->DB()->GetSingle();
        if ($find)
            $this->Util()->setError(0, "error", "El equipo de computo seleccionado se encuentra ocupado.");
    }

    function getCurrentResponsableResource()
    {
        $sql = "select  responsable_resource_id, office_resource_id from responsables_resource_office where status = 'Activo' and personalId = '". $this->personalId ."' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        return $row;
    }

    public function GetPersonalGroupByDepartament () {

        $this->Util()->DB()->setQuery("SET SESSION group_concat_max_len = 1000000");
        $this->Util()->DB()->ExecuteQuery();
        $sql = "select
       			departamentos.departamentoId,
				departamentos.departamento,
        		CONCAT(
					'[',
					GROUP_CONCAT(
						CONCAT(
							'{\"id',
							'\":\"',
							personal.personalId,
							'\",\"',
							'name',
							'\":\"',
							 personal.name,
						    '\",\"',
						    'level',
						    '\":\"',
							 personal.nivel,
							'\"}'
						)
					),
					']'
				)  as responsables
				FROM (select a.personalId, a.name, a.departamentoId, b.nivel from personal a 
				INNER JOIN roles b on a.roleId=b.rolId) as personal 	
				INNER JOIN departamentos on departamentos.departamentoId = personal.departamentoId
				where departamentos.estatus='1'   
				group by personal.departamentoId order by personal.name asc
				";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        
        // Cargar responsables por departamento

        $premerge = [];
        foreach ($result as  $var){
            if(in_array(mb_strtolower($var['departamento']), ['asociado', 'asociada', 'asociados', 'asociadas'])) {

               $responsablesPropios = json_decode($var['responsables'], true);

               $operativos = array_filter($result, function($item) {
                   return !in_array($item['departamento'], AREAS_NO_OPERATIVAS)
                          && !in_array(mb_strtolower($item['departamento']), ['asociado', 'asociada', 'asociados', 'asociadas']);
               });

               $directoresOperativos = array_map(function($item) {
                   $responsables = json_decode($item['responsables'], true);
                   return array_filter($responsables, function($resp) {
                       return $resp['level'] == 2; // filtrar por nivel 2
                   });
               }, $operativos);

               $premerge[$var['departamentoId']] = array_merge($responsablesPropios, ...$directoresOperativos);
            } else {
               $premerge[$var['departamentoId']] = json_decode($var['responsables'], true);
            }
        }

        foreach($premerge as $key => $value) {
            $premerge[$key] = $this->Util()->orderMultiDimensionalArray(
                $premerge[$key] ?? [], 'name'
            );
        }

        $gerenciales= array_column(DEPARTAMENTOS_TIPO_GERENCIA, 'principal');

        $gerencialesMap = array_map(function ($item) {
            return "'" . $item . "'";
        }, $gerenciales);

        $implodeDepartamentos = implode(",", $gerencialesMap);
        $sql = "select departamentoId, departamento from departamentos where departamento in (".$implodeDepartamentos.") and estatus = 1";
        $this->Util()->DB()->setQuery($sql);
        $departamentosGerenciales = $this->Util()->DB()->GetResult();

        $responsables = $this->getPersonasParaDepartamentoGerencial();

        foreach($departamentosGerenciales as $depGerencial) {

            $tipoGerencia = current(array_filter(DEPARTAMENTOS_TIPO_GERENCIA,
                function ($item) use ($depGerencial) {
                    return $item['principal'] == $depGerencial['departamento'];
                }
            ));

            $premerge[$depGerencial['departamentoId']] = $responsables[$tipoGerencia['secundario']] ?? [];
        }

        return $premerge;
    }

    public function getPersonasParaDepartamentoGerencial() {

        $this->Util()->DB()->setQuery("SET SESSION group_concat_max_len = 1000000");
        $this->Util()->DB()->ExecuteQuery();

        $sql = "SELECT 
                    tbl_main.departamentoId,
                    (select departamento from departamentos where tbl_main.departamentoId = departamentoId limit 1) as departamento,
                    CONCAT(
                        '[',
                        GROUP_CONCAT(
                            CONCAT('{\"departamento_id',
                                '\":\"',
                                tbl_main.departamentoId,
                                '\",\"',
                                'departamento',
                                '\":\"',
                                (SELECT tbl_dep.departamento FROM departamentos tbl_dep WHERE tbl_main.departamentoId = tbl_dep.departamentoId LIMIT 1),
                                '\",\"',
                                'id',
                                '\":\"',
                                tbl_main.personalId,
                                '\",\"',
                                'name',
                                '\":\"',
                                 tbl_main.name,
                                '\",\"',
                                'level',
                                '\":\"',
                                 tbl_main.nivel,
                                '\"}'
                            )
                        ),
                        ']'
                    )  as personas FROM personal as tbl_main
                    INNER JOIN roles ON tbl_main.roleId = roles.rolId
                    WHERE 
                        (
                            roles.nivel IN (3,5) 
                            AND (SELECT tbl_dep.departamento FROM departamentos tbl_dep WHERE tbl_main.departamentoId = tbl_dep.departamentoId LIMIT 1) != 'Contabilidad e Impuestos'
                        ) 
                         OR 
                        (
                            (roles.nivel >=3 and roles.nivel <=4) 
                            AND (SELECT tbl_dep.departamento FROM departamentos tbl_dep WHERE tbl_main.departamentoId = tbl_dep.departamentoId LIMIT 1) = 'Contabilidad e Impuestos'
                        )
                    AND tbl_main.active = '1'
                    GROUP BY tbl_main.departamentoId ORDER BY tbl_main.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $personas = $this->Util()->DB()->GetResult();

        $personasPorDepartamento = [];
        foreach ($personas as $value) {
            $personasPorDepartamento[$value['departamento']] = json_decode($value['personas'], true);
        }

        return $personasPorDepartamento;
    }

    public function getPersonalGerenciaResponsable($nombreDepartamento, $niveles=[3,4]) {

        $this->Util()->DB()->setQuery("SELECT 
                                                a.personalId as id,
                                                a.name,
                                                b.nivel,
                                                (SELECT departamento FROM departamentos WHERE departamentoId = a.departamentoId LIMIT 1) as departamento
                                             FROM personal a 
                                             INNER JOIN roles b ON a.roleId=b.rolId 
                                             WHERE 
                                                ((UPPER((SELECT departamento FROM departamentos WHERE departamentoId = a.departamentoId LIMIT 1)) = '".$nombreDepartamento."' 
                                                AND b.nivel IN (" . implode(',', $niveles) . ")) 
                                             ORDER BY a.name ASC , b.nivel ASC");
        return  $this->Util()->DB()->GetResult();
    }

    public function getSubordinadosByLevel ($nivel = 0) {
        $subordinados_filtrados = [];
        $subordinados = $this->SubordinadosDirectos();
        if(!$nivel)
            return $subordinados;

        if (is_array($nivel)) {
            foreach ($subordinados as $sub) {
                if (in_array($sub['nivel'], $nivel))
                    array_push($subordinados_filtrados, $sub);
            }

        } else {
            foreach ($subordinados as $sub) {
                if ($sub['nivel'] == $nivel)
                    array_push($subordinados_filtrados, $sub);
            }
        }
        return $subordinados_filtrados;
    }

    public function getSubordinadosNoDirectoByLevel ($nivel = 0) {
        $subordinados_filtrados = [];
        $subordinados = $this->GetCascadeSubordinates();
        if(!$nivel)
            return $subordinados;

        if (is_array($nivel)) {
            foreach ($subordinados as $sub) {
                if (in_array($sub['nivel'], $nivel))
                    array_push($subordinados_filtrados, $sub);
            }

        } else {
            foreach ($subordinados as $sub) {
                if ($sub['nivel'] == $nivel)
                    array_push($subordinados_filtrados, $sub);
            }
        }
        return $subordinados_filtrados;
    }
}
