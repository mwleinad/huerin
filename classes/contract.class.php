<?php
class Contract extends Main
{
    private $facturador;
    public function setFacturador($value)
    {
        $this->Util()->ValidateRequireField($value, 'Facturador');
        $this->facturador = $value;
    }

    private $auxiliarCuenta;
    public function setAuxiliarCuenta($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->auxiliarCuenta = $value;
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

    private $cobrador;
    public function setCobrador($value)
    {
        $this->Util()->ValidateInteger($value);
        $this->cobrador = $value;
    }

    private $permisos;
    public function setPermisos($value)
    {
        $this->permisos = implode("-", array_filter($value));
    }

    private $noExtComercial;
    public function setNoExtComercial($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 0, 'No Ext Comercial');
        $this->noExtComercial = $value;
    }

    private $noIntComercial;
    public function setNoIntComercial($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 0, 'No Int Comercial');
        $this->noIntComercial = $value;
    }

    private $coloniaComercial;
    public function setColoniaComercial($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Colonia Comercial');
        $this->coloniaComercial = $value;
    }

    private $municipioComercial;
    public function setMunicipioComercial($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Municipio Comercial');
        $this->municipioComercial = $value;
    }

    private $estadoComercial;
    public function setEstadoComercial($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Estado Comercial');
        $this->estadoComercial = $value;
    }

    private $noExtAddress;
    public function setNoExtAddress($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 0, 'No Ext');
        $this->noExtAddress = $value;
    }

    private $noIntAddress;
    public function setNoIntAddress($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 0, 'No Int');
        $this->noIntAddress = $value;
    }

    private $coloniaAddress;
    public function setColoniaAddress($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Colonia');
        $this->coloniaAddress = $value;
    }

    private $municipioAddress;
    public function setMunicipioAddress($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Municipio');
        $this->municipioAddress = $value;
    }

    private $estadoAddress;
    public function setEstadoAddress($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Estado');
        $this->estadoAddress = $value;
    }

    private $paisAddress;
    public function setPaisAddress($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Pais');
        $this->paisAddress = $value;
    }

    private $type;
    public function setType($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Tipo')) {
            $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, 'Tipo');
        }
        $this->type = $value;
    }

    private $sociedadId;
    public function setSociedadId($value)
    {
        if ($this->type == "Persona Moral") {
            if ($this->Util()->ValidateRequireField($value, 'Sociedad')) {
                $this->Util()->ValidateInteger($value);
            }
        }
        $this->sociedadId = $value;
    }

    private $regimenId;
    public function setRegimenId($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Regimen')) {
            $this->Util()->ValidateInteger($value);
        }
        $this->regimenId = $value;
    }

    private $telefono;
    public function setTelefono($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Telefono');
        $this->telefono = $value;
    }
    private $actividadComercialId;
    public function setActividadComercialId($value)
    {
        $this->Util()->ValidateRequireField($value,  'Actividad comercial');
        $this->actividadComercialId = $value;
    }
    private $nombreComercial;
    public function setNombreComercial($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Nombre Comercial');
        $this->nombreComercial = $value;
    }

    private $direccionComercial;
    public function setDireccionComercial($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Direccion Comercial');
        $this->direccionComercial = $value;
    }

    private $nameContactoAdministrativo;
    public function setNameContactoAdministrativo($value)
    {
        $this->Util()->ValidateString(
            $value,
            $max_chars = 255,
            $minChars = 1,
            'Nombre Contacto Administrativo'
        );
        $this->nameContactoAdministrativo = $value;
    }

    private $emailContactoAdministrativo;
    public function setEmailContactoAdministrativo($value)
    {
        $this->Util()->ValidateString(
            $value,
            $max_chars = 255,
            $minChars = 1,
            'Email Contacto Administrativo'
        );
        $this->emailContactoAdministrativo = $value;
    }

    private $telefonoContactoAdministrativo;
    public function setTelefonoContactoAdministrativo($value)
    {
        $this->Util()->ValidateString(
            $value,
            $max_chars = 255,
            $minChars = 1,
            'Telefono Contacto Administrativo'
        );
        $this->telefonoContactoAdministrativo = $value;
    }

    private $nameRepresentanteLegal;
    public function setNameRepresentanteLegal($value)
    {
        $this->nameRepresentanteLegal = $value;
    }

    private $emailRepresentanteLegal;
    public function setEmailRepresentanteLegal($value)
    {
        $this->emailRepresentanteLegal = $value;
    }

    private $telefonoRepresentanteLegal;
    public function setTelefonoRepresentanteLegal($value)
    {
        $this->telefonoRepresentanteLegal = $value;
    }

    private $nameContactoContabilidad;
    public function setNameContactoContabilidad($value)
    {
        $this->Util()->ValidateString(
            $value,
            $max_chars = 255,
            $minChars = 1,
            'Nombre Contacto Contabilidad'
        );
        $this->nameContactoContabilidad = $value;
    }

    private $emailContactoContabilidad;
    public function setEmailContactoContabilidad($value)
    {
        $this->Util()->ValidateString(
            $value,
            $max_chars = 255,
            $minChars = 1,
            'Email Contacto Contabilidad'
        );
        $this->emailContactoContabilidad = $value;
    }

    private $telefonoContactoContabilidad;
    public function setTelefonoContactoContabilidad($value)
    {
        $this->Util()->ValidateString(
            $value,
            $max_chars = 255,
            $minChars = 1,
            'Telefono Contacto Contabilidad'
        );
        $this->telefonoContactoContabilidad = $value;
    }

    private $nameContactoDirectivo;
    public function setNameContactoDirectivo($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Nombre Contacto Directivo');
        $this->nameContactoDirectivo = $value;
    }

    private $emailContactoDirectivo;
    public function setEmailContactoDirectivo($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Email Contacto Directivo');
        $this->emailContactoDirectivo = $value;
    }

    private $telefonoContactoDirectivo;
    public function setTelefonoContactoDirectivo($value)
    {
        $this->Util()->ValidateString(
            $value,
            $max_chars = 255,
            $minChars = 1,
            'Telefono Contacto Directivo'
        );
        $this->telefonoContactoDirectivo = $value;
    }

    private $telefonoCelularDirectivo;
    public function setTelefonoCelularDirectivo($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Telefono Celular Directivo');
        $this->telefonoCelularDirectivo = $value;
    }

    private $claveCiec;
    public function setClaveCiec($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Clave Ciec');
        $this->claveCiec = $value;
    }

    private $claveFiel;
    public function setClaveFiel($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Clave Fiel');
        $this->claveFiel = $value;
    }

    private $claveIdse;
    public function setClaveIdse($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Clave Idse');
        $this->claveIdse = $value;
    }

    private $claveIsn;
    public function setClaveIsn($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Clave Isn');
        $this->claveIsn = $value;
    }

    private $claveSip;
    public function setClaveSip($value)
    {
        $this->claveSip = $value;
    }

    private $useAlternativeRzForInvoice;
    public function setUseAlternativeRzForInvoice($value)
    {
        $this->useAlternativeRzForInvoice = $value;
    }

    private $alternativeRzId;
    public function setAlterntiveRzId($value)
    {
        $this->Util()->ValidateRequireField($value, 'Razon social alternativa para facturacion');
        $this->alternativeRzId = $value;
    }
    private $alternativeRz;
    public function setAlternativeRz($value) {
        $this->Util()->ValidateRequireField($value, 'Nombre o razon social alternativa');
        $this->alternativeRz =  $value;
    }
    private $alternativeRfc;
    public function setAlternativeRfc($value) {
        $value = str_replace(" ", "", $value);
        $value = str_replace("-", "", $value);
        $value = strtoupper($value);
        if ($this->Util()->ValidateRequireField($value, 'RFC alternativo')) {
            $this->Util()->ValidateString($value, $max_chars = 13, $minChars = 11, 'RFC alternativo');
        }
        $value = str_replace("&amp;", "&", $value);
        $this->alternativeRfc = $value;
    }
    private $alternativeCp;
    public function setAlternativeCp($value) {
        if($this->Util()->ValidateRequireField($value, 'Codigo postal alternativa'))
            if($this->Util()->ValidateOnlyNumeric($value, 'Codigo postal alternativa'))
                $this->Util()->ValidateString($value, $max_chars = 5, $minChars = 5, 'Codigo postal alternativa');
        $this->alternativeCp =  $value;
    }

    private $alternativeRegimen;
    public function setAlternativeRegimen($value) {
        if($this->Util()->ValidateRequireField($value, 'Régimen fiscal alternativo'))
            if($this->Util()->ValidateOnlyNumeric($value, 'Régimen fiscal'))
                $this->Util()->ValidateString($value, 10, 3, 'Régimen fiscal');
        $this->alternativeRegimen =  $value;
    }
    private $alternativeType;
    public function setAlternativeType($value) {
        if($this->Util()->ValidateRequireField($value, 'Tipo persona alternativa'))
                $this->Util()->ValidateString($value, 50, 1, 'Tipo persona alternativa');
        $this->alternativeType =  $value;
    }

    private $alternativeUsoCfdi;
    public function setAlternativeUsoCfdi($value) {
        if($this->Util()->ValidateRequireField($value, 'Uso CFDI alternativo'))
                $this->Util()->ValidateString($value, 10, 1, 'Uso CFDI Alternativo');
        $this->alternativeUsoCfdi =  $value;
    }
    private $qualification;
    public function setQualification($value)
    {
        $this->qualification = $value;
    }

    private $separateInvoice;
    public function setSeparateInvoice($value)
    {
        $this->separateInvoice = $value;
    }

    private $claveUsoCfdi;
    public function setClaveUsoCfdi($value)
    {
        if($this->Util()->ValidateRequireField($value, 'Uso de CFDI'))
            $this->Util()->ValidateString($value, 10, 1, 'Uso de CFDI');
        $this->claveUsoCfdi = $value;
    }

    private $contractId;
    private $customerId;
    private $personalId;
    private $name;
    private $folio;
    private $address;
    private $contCatId;
    private $contSubcatId;
    private $status;
    private $docsBasic;
    private $docsSellado;
    private $docsGral;
    private $partes;
    private $year;
    private $fechaProrroga;
    private $docGralId;
    private $docBasicId;
    private $fechaDoc;
    private $desc;
    private $rfc;
    private $cpComercial;
    public function setCpComercial($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'CP Recoleccion')) {
            $this->Util()->ValidateString($value, $max_chars = 5, $minChars = 5, 'CP Recoleccion');
        }
        $this->cpComercial = $value;
    }

    private $cpAddress;
    public function setCpAddress($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Codigo postal')) {
            $this->Util()->ValidateString($value, $max_chars = 5, $minChars = 5, 'Codigo postal');
        }
        $this->cpAddress = $value;
    }

    private $metodoDePago;
    public function setMetodoDePago($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Metodo de Pago')) {
            $this->Util()->ValidateString($value, $max_chars = 2, $minChars = 2, 'Metodo de Pago');
        }
        $this->metodoDePago = $value;
    }

    private $noCuenta;
    public function setNoCuenta($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 4, $minChars = 0, '# Cuenta');
        $this->noCuenta = $value;
    }

    public function setContractId($value, $required = false)
    {
        if ($required)
            $this->Util()->ValidateRequireField($value, "Razones sociales");
        $this->Util()->ValidateInteger($value);
        $this->contractId = $value;
    }

    private $idContracts = [];
    public function setIdContracts($value)
    {
        if (empty($value) || !is_array($value))
            $this->Util()->setError(0, 'error', 'Es necesario seleccionar una razon social de la lista.');
        $this->idContracts = $value;
    }

    public function getIdContracts()
    {
        return $this->idContracts;
    }

    public function getContractId()
    {
        return $this->contractId;
    }

    public function setCustomerId($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Cliente')) {
            $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, 'Cliente');
        }
        $this->customerId = $value;
    }

    public function setPersonalId($value)
    {
        if ($this->Util()->ValidateRequireField(
            $value,
            'Responsable del proyecto por parte de Roqueñi Straffon S.C.'
        )) {
            $this->Util()->ValidateString(
                $value,
                $max_chars = 60,
                $minChars = 1,
                'Responsable del proyecto por parte de Roqueñi Straffon S.C.'
            );
        }
        $this->personalId = $value;
    }

    public function setName($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Razon Social')) {
            $this->Util()->ValidateString($value, 1000, 1, 'Razon Social');
        }
        $this->name = $value;
    }

    public function setFolio($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Folio')) {
            $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, 'Folio');
        }
        $this->folio = $value;
    }

    public function setContCatId($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Tipo de Contrato')) {
            $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, 'Tipo de Contrato');
        }
        $this->contCatId = $value;
    }

    public function setContSubcatId($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Tipo de Subcontrato')) {
            $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, 'Tipo de Subcontrato');
        }
        $this->contSubcatId = $value;
    }

    public function setStatus($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Estatus')) {
            $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, 'Estatus');
        }
        $this->status = $value;
    }

    public function setYear($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'A&ntilde;o')) {
            $this->year = $value;
        }
    }

    public function setFechaProrroga($value)
    {
        $this->fechaProrroga = $value;
    }

    public function setFechaDoc($value)
    {
        if ($value > date('Y-m-d')) {
            $this->Util()->setError(10054, "error");
        }
        $this->fechaDoc = $value;
    }

    public function setDocGralId($value)
    {
        $this->docGralId = $value;
    }

    public function setDocBasicId($value)
    {
        $this->docBasicId = $value;
    }

    public function setDesc($value)
    {
        $this->desc = $value;
    }

    public function setAddress($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Calle')) {
            $this->address = $value;
        }
    }

    public function setRfc($value)
    {
        $value = str_replace(" ", "", $value);
        $value = str_replace("-", "", $value);
        $value = strtoupper($value);
        if ($this->Util()->ValidateRequireField($value, 'RFC')) {
            $this->Util()->ValidateString($value, 13,11, 'RFC');
        }

        $value = str_replace("&amp;", "&", $value);
        $this->rfc = $value;
    }
    private $acuerdoComercial;
    public function setAcuerdoComercial($value)
    {
        if ($this->Util()->ValidateRequireField($value, 'Acuerdo comercial')) {
            $this->acuerdoComercial =$this->Util()->normalizeString($value);
        }
    }

    private function contratWithPermission($contrato, $respCuenta, $skip)
    {
        $split = split('-', $contrato['permisos']);

        foreach ($split as $sp) {
            $split2 = split(',', $sp);

            //Se agrego dep 25 que ya no existe
            if ($split2[0] == 25) {
                continue;
            }

            if ($split2[1] == $respCuenta || $skip) {
                return true;
            }
        }
        return false;
    }

    public function BuscarContract($formValues, $activos = false)
    {

        global $personal;
        global $User;

        if ($formValues['cliente'])
            $sqlFilter = " AND customer.nameContact LIKE '%" . $formValues['cliente'] . "%'";

        if ($formValues['razonSocial'])
            $sqlFilter = " AND contract.nombreComercial LIKE '%" . $formValues['razonSocial'] . "%'";

        if ($formValues['departamentoId'])
            $sqlDepto = " AND tipoServicio.departamentoId='" . $formValues['departamentoId'] . "'";

        if ($activos)
            $sqlFilter .= " AND customer.active = '1'";

        if ($formValues['facturador'])
            $sqlFilter .= ' AND contract.facturador = "' . $formValues['facturador'] . '"';

        //Contratos Activos
        $sqlFilter .= ' AND contract.activo = "Si"';

        //Si selecciona TODOS los responsables, debe incluir a los subordinados automaticamente.
        $skip = false;
        if ($formValues['respCuenta'] == 0) {
            $respCuenta = $User['userId'];
            $formValues['subordinados'] = 1;
            //el roleId 4 es de cliente solo deberia poder ver la sus contratos, en otros apartados el roleId 4 es usado como admin(verificarlo)
            if ($_SESSION['User']["roleId"] == 4) {
                $skip = true;
            }
        } else {
            $respCuenta = $formValues['respCuenta'];
        }

        $sql = "SELECT contract.*, contract.name AS name, contract.encargadoCuenta AS encargadoCuenta,
				contract.responsableCuenta AS responsableCuenta, personal.jefeSocio, personal.jefeSupervisor,
				personal.jefeGerente, personal.jefeContador, customer.nameContact
				FROM contract
				LEFT JOIN customer ON customer.customerId = contract.customerId
				LEFT JOIN regimen ON regimen.regimenId = contract.regimenId
				LEFT JOIN sociedad ON sociedad.sociedadId = contract.sociedadId
				LEFT JOIN personal ON contract.responsableCuenta = personal.personalId
				WHERE 1 " . $sqlFilter . "
				ORDER BY customer.nameContact ASC,contract.name ASC";

        $this->Util()->DB()->setQuery($sql);
        $resContratos = $this->Util()->DB()->GetResult();
        $contratos = array();

        foreach ($resContratos as $res) {
            $encontrado = $this->contratWithPermission($res, $respCuenta, $skip);
            if ($encontrado == false) {
                continue;
            }
            //Checamos Servicios
            $sql = "SELECT *,servicio.status as servicioStatus  FROM servicio
					LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
					WHERE contractId = '" . $res["contractId"] . "'
					AND servicio.status IN ('activo','bajaParcial') AND tipoServicio.status='1'
					" . $sqlDepto . "
					ORDER BY tipoServicio.nombreServicio ASC";
            $this->Util()->DB()->setQuery($sql);
            $res["servicios"] = $this->Util()->DB()->GetResult();
            $res["noServicios"] = count($res["servicios"]);
            //Si no tiene departamento asignado lo borro
            if ($res["servicios"][0]['departamentoId'] == "") {
                continue;
            }
            $contratos[] = $res;
        }//foreach
        //INCLUIR SUBORDINADOS
        if (!$formValues['subordinados'])
            return $contratos;

        $personal->setPersonalId($respCuenta);
        $subordinados = $personal->Subordinados();

        $sql = "SELECT contract.*, contract.name AS name, contract.encargadoCuenta AS encargadoCuenta,
					contract.responsableCuenta AS responsableCuenta, personal.jefeSocio, personal.jefeSupervisor,
					personal.jefeGerente, personal.jefeContador, customer.nameContact
					FROM contract
					LEFT JOIN customer ON customer.customerId = contract.customerId
					LEFT JOIN regimen ON regimen.regimenId = contract.regimenId
					LEFT JOIN sociedad ON sociedad.sociedadId = contract.sociedadId
					LEFT JOIN personal ON contract.responsableCuenta = personal.personalId
					WHERE 1 " . $sqlFilter . "
					ORDER BY customer.nameContact ASC, contract.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $resContratos = $this->Util()->DB()->GetResult();

        foreach ($subordinados as $sub) {
            $personalId = $sub['personalId'];

            foreach ($resContratos as $res) {

                $encontrado = $this->contratWithPermission($res, $personalId, $skip);

                if ($encontrado == false) {
                    continue;
                }
                //Checamos Servicios
                $sql = "SELECT *,servicio.status as servicioStatus  FROM servicio
						LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
						WHERE contractId = '" . $res["contractId"] . "'
						AND servicio.status IN ('activo','bajaParcial') AND tipoServicio.status='1'
						" . $sqlDepto . "
						ORDER BY tipoServicio.nombreServicio ASC";
                $this->Util()->DB()->setQuery($sql);
                $res["servicios"] = $this->Util()->DB()->GetResult();
                $res["noServicios"] = count($res["servicios"]);

                //Si no tiene departamento asignado lo borro
                if ($res["servicios"][0]['departamentoId'] == "")
                    continue;

                $contratos[$res["contractId"]] = $res;
            }//foreach

        }//foreach
        return $contratos;

    }//BuscarContract

    /**
     * Enumerate
     *
     * @param int $id id del customer
     *
     * @return muestra los contratos del customer con id especificado
     */
    public function Enumerate($id = 0, $status = '')
    {
        global $User, $rol, $personal, $contract,$customer;
        $creport = new ContractRep();
        if ($id) {
            $add = "WHERE contract.customerId = '" . $id . "'";
        }

        if ($status == 'activos')
            $add .= ' AND contract.activo = "Si"';
        elseif ($status == 'inactivos')
            $add .= ' AND contract.activo = "No"';
        $filter["deep"] = true;
        $filter['responsableCuenta'] = 0;

        //$personal->setPersonalId($User["userId"]);//si se pasa 0 se obtiene todos los subordinados desde socio asta el mas bajo
        //$subordinados = $personal->Subordinados();
        $subordinados = $personal->GetIdResponsablesSubordinados($filter);
        $strSubordinados = implode(",", $subordinados);

        $sql = "SELECT
            *,
            contract.name AS name,
            contract.encargadoCuenta AS encargadoCuenta,
            contract.responsableCuenta AS responsableCuenta
            FROM
              contract
            LEFT JOIN
              customer ON customer.customerId = contract.customerId
            LEFT JOIN
              regimen ON regimen.regimenId = contract.regimenId
            LEFT JOIN
              sociedad ON sociedad.sociedadId = contract.sociedadId
              " . $add . "
            ORDER BY
              contract.name ASC
            ";
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        foreach ($result as $key => $value) {
            $jefes = [];
            $contract->setContractId($value["contractId"]);
            $serviciosContrato = $customer->GetServicesByContract($value["contractId"]);
            $result[$key]["noServicios"] = count($serviciosContrato);

            $sql = "SELECT b.personalId FROM contract a  
                  INNER JOIN contractPermiso b ON a.contractId=b.contractId 
                  WHERE b.personalId IN($strSubordinados) 
                  AND a.contractId = '".$value['contractId']."' 
                  group by a.contractId ";
            $this->Util()->DB()->setQuery($sql);
            $whitPermiso = $this->Util()->DB()->GetSingle();

            if (!$whitPermiso) {
                $unlimited = $this->accessAnyContract();
                if (!$unlimited) {
                    unset($result[$key]);
                    continue;
                }
            }
            $parciales = $customer->GetServicesByContract($value["contractId"],'bajaParcial');
            if(count($parciales)>0&&$value['activo']=='Si')
                $result[$key]["haveTemporal"] = 1;

            $encargados = $creport->encargadosCustomKey("departamento","name",$value["contractId"]);
            $encargadosXdep = $creport->encargadosCustomKey("departamentoId","name",$value["contractId"]);
            $user = new User;
            $result[$key]["responsable"] = $encargados["Contabilidad"];
            $result[$key]["encargadosXdep"] = $encargadosXdep;
        }
        return $result;
    }

    /**
     * Search
     *
     * @param string $sql contiene parte de la consulta a la db
     *
     * @return busca un contrato de acuerdo a  los paramentros de busqueda en la variable sql
     */
    public function Search($sql)
    {
        $sql = "SELECT
              *
            FROM
              contract
            WHERE
              1 " . $sql . "
            ORDER BY
              name ASC";

        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    /**
     * Info
     *
     * @return $row la informacion de un contrato con respecto a su id
     */
    public function Info()
    {
        $this->Util()->DB()->setQuery(
            "SELECT
          *,
          contract.name AS name,
          contract.encargadoCuenta AS encargadoCuenta,
          contract.responsableCuenta AS responsableCuenta,
          contract.auxiliarCuenta AS auxiliarCuenta,
          customer.email as email,
          ac.*  
        FROM
          contract
        LEFT JOIN
          customer ON customer.customerId = contract.customerId
        LEFT JOIN
          regimen ON regimen.regimenId = contract.regimenId
        LEFT JOIN
          sociedad ON sociedad.sociedadId = contract.sociedadId
        LEFT JOIN(
          select actividad_comercial.id as ac_id, actividad_comercial.name as ac_name,
                 sector.id as sector_id, subsector.id as subsector_id from actividad_comercial
          inner join subsector on actividad_comercial.subsector_id = subsector.id
          inner join sector on subsector.sector_id = sector.id 
        ) as ac  on contract.actividadComercialId = ac.ac_id 
        WHERE
          contractId = '" . $this->contractId . "'"
        );
        $row = $this->Util()->DB()->GetRow();
        if($row) {
            $row['rfcFacturacion'] = $row['rfc'];
            $row['nameFacturacion'] = $row['name'];
            $row['cpFacturacion'] = $row['cpAddress'];
            $row['idFacturacion'] = $row['contractId'];
            $row['regimenFacturacion'] = $row['regimenId'];
            $row['usoCfdiFacturacion'] = $row['claveUsoCfdi'];
            $row['typeFacturacion'] = $row['type'];
            if ((int)$row['useAlternativeRzForInvoice'] === 1) {
                if ((int)$row['alternativeRzId'] > 0) {
                    $alternativeRzId = $row['alternativeRzId'];
                    $sqlAlternative = "SELECT contractId,rfc, name, cpAddress,regimenId,claveUsoCfdi,type
                                       FROM contract WHERE contractId = '$alternativeRzId' ";
                    $this->Util()->DB()->setQuery($sqlAlternative);
                    $alternativeData = $this->Util()->DB()->GetRow();
                    $row['rfcFacturacion'] = $alternativeData['rfc'];
                    $row['nameFacturacion'] = $alternativeData['name'];
                    $row['cpFacturacion'] = $alternativeData['cpAddress'];
                    $row['idFacturacion'] = $alternativeData['contractId'];
                    $row['regimenFacturacion'] = $alternativeData['regimenId'];
                    $row['usoCfdiFacturacion'] = $alternativeData['claveUsoCfdi'];
                    $row['typeFacturacion'] = $alternativeData['type'];
                } elseif ((int)$row['alternativeRzId'] === 0) {
                    $row['rfcFacturacion'] = $row['alternativeRfc'];
                    $row['nameFacturacion'] = $row['alternativeRz'];
                    $row['cpFacturacion'] = $row['alternativeCp'];
                    $row['regimenFacturacion'] = $row['alternativeRegimen'];
                    $row['usoCfdiFacturacion'] = $row['alternativeUsoCfdi'];
                    $row['typeFacturacion'] = $row['alternativeType'];
                }
            }
        }

        return $row;
    }

    /**
     * Save
     *
     * @return true | false un nuevo contrato
     */
    public function Save($saveAndSendLog = true)
    {
        global $User, $log;
        $permiso = new Permiso();
        if($this->Util()->getErrors()['total'])
            return false;

        $valueRzid = !strlen($this->alternativeRzId) ? 'NULL' : "'".$this->alternativeRzId."'";
        $usoCfdi = !strlen($this->claveUsoCfdi) ? 'NULL' : "'".$this->claveUsoCfdi."'";

        $alternativeType    = !strlen($this->alternativeType) ? 'NULL' : "'".$this->alternativeType."'";
        $alternativeRegimen = !strlen($this->alternativeRegimen) ? 'NULL' : "'".$this->alternativeRegimen."'";
        $alternativeUso     = !strlen($this->alternativeUsoCfdi) ? 'NULL' : "'".$this->alternativeUsoCfdi."'";

        $this->Util()->DB()->setQuery(
            "INSERT INTO
          contract
        (
          customerId,
          address,
          type,
          sociedadId,
          `name`,
          regimenId,
          telefono,
          nombreComercial,
          actividadComercialId,
          direccionComercial,
          nameContactoAdministrativo,
          emailContactoAdministrativo,
          telefonoContactoAdministrativo,
          nameContactoContabilidad,
          emailContactoContabilidad,
          telefonoContactoContabilidad,
          nameRepresentanteLegal,
          emailRepresentanteLegal,
          telefonoRepresentanteLegal,
          nameContactoDirectivo,
          emailContactoDirectivo,
          telefonoContactoDirectivo,
          telefonoCelularDirectivo,
          claveCiec,
          claveFiel,
          claveIdse,
          claveSip,
          rfc,
          noExtComercial,
          noIntComercial,
          coloniaComercial,
          municipioComercial,
          estadoComercial,
          noExtAddress,
          noIntAddress,
          coloniaAddress,
          municipioAddress,
          estadoAddress,
          paisAddress,
          cpAddress,
          metodoDePago,
          noCuenta,
          cpComercial,
          encargadoCuenta,
          responsableCuenta,
          permisos,
          auxiliarCuenta,
          facturador,
          claveIsn,
          fechaAlta,
          useAlternativeRzForInvoice,
          alternativeRzId,
          claveUsoCfdi,
          alternativeRz,
          alternativeRfc,
          alternativeCp,
          alternativeType,
          alternativeRegimen,
          alternativeUsoCfdi,
          createSeparateInvoice,
          idTipoClasificacion
        )
        VALUES
        (
          '" . $this->customerId . "',
          '" . $this->address . "',
          '" . $this->type . "',
          '" . $this->sociedadId . "',
          '" . $this->name . "',
          '" . $this->regimenId . "',
          '" . $this->telefono . "',
          '" . $this->nombreComercial . "',
          '" . $this->actividadComercialId . "',
          '" . $this->direccionComercial . "',
          '" . $this->nameContactoAdministrativo . "',
          '" . $this->emailContactoAdministrativo . "',
          '" . $this->telefonoContactoAdministrativo . "',
          '" . $this->nameContactoContabilidad . "',
          '" . $this->emailContactoContabilidad . "',
          '" . $this->telefonoContactoContabilidad . "',
          '" . $this->nameRepresentanteLegal . "',
          '" . $this->emailRepresentanteLegal . "',
          '" . $this->telefonoRepresentanteLegal . "',
          '" . $this->nameContactoDirectivo . "',
          '" . $this->emailContactoDirectivo . "',
          '" . $this->telefonoContactoDirectivo . "',
          '" . $this->telefonoCelularDirectivo . "',
          '" . $this->claveCiec . "',
          '" . $this->claveFiel . "',
          '" . $this->claveIdse . "',
          '" . $this->claveSip . "',
          '" . $this->rfc . "',
          '" . $this->noExtComercial . "',
          '" . $this->noIntComercial . "',
          '" . $this->coloniaComercial . "',
          '" . $this->municipioComercial . "',
          '" . $this->estadoComercial . "',
          '" . $this->noExtAddress . "',
          '" . $this->noIntAddress . "',
          '" . $this->coloniaAddress . "',
          '" . $this->municipioAddress . "',
          '" . $this->estadoAddress . "',
          '" . $this->paisAddress . "',
          '" . $this->cpAddress . "',
          '" . $this->metodoDePago . "',
          '" . $this->noCuenta . "',
          '" . $this->cpComercial . "',
          '" . $this->encargadoCuenta . "',
          '" . $this->responsableCuenta . "',
          '" . $this->permisos . "',
          '" . $this->auxiliarCuenta . "',
          '" . $this->facturador . "',
          '" . $this->claveIsn . "',
          '".date('Y-m-d H:i:s')."',
          '" . $this->useAlternativeRzForInvoice . "',
          $valueRzid,
          $usoCfdi,
          '" . $this->alternativeRz . "',
          '" . $this->alternativeRfc . "',
          '" . $this->alternativeCp . "',
           $alternativeType,
           $alternativeRegimen,
           $alternativeUso,
          '" . $this->separateInvoice . "',
          '" . $this->qualification . "'
          
          )"
        );
        $contractId = $this->Util()->DB()->InsertData();
        //insertar nuevos permisos en la tabla contractPermiso
        $permiso->setContractId($contractId);
        $permiso->doPermiso();

        $sql = "SELECT * FROM contract WHERE contractId = '" . $contractId . "'";
        $this->Util()->DB()->setQuery($sql);
        $newData = $this->Util()->DB()->GetRow();
        //Guardamos el Log
        $log->setPersonalId($User['userId']);
        $log->setFecha(date('Y-m-d H:i:s'));
        $log->setTabla('contract');
        $log->setTablaId($contractId);
        $log->setAction('Insert');
        $log->setOldValue('');
        $log->setNewValue(serialize($newData));
        ($saveAndSendLog || isset($_POST['sendNotificacion']))
            ? $log->Save()
            : $log->SaveOnly();
        //actualizar historial
        $this->Util()->DB()->setQuery("
			INSERT INTO
				contractChanges
			(
				`contractId`,
				`status`,
				`oldData`,
				`newData`,
				`personalId`
		)
		VALUES
		(
				'" . $contractId . "',
				'" . $newData["activo"] . "',
				'',
				'" . urlencode(serialize($newData)) . "',
				'" . $User["userId"] . "'
		);");
        $this->Util()->DB()->InsertData();
        foreach ($_FILES as $key => $file) {
            if ($key == "cerFiel" || $key == "keyFiel" || $key == "reqFiel") {
                $folder = DOC_ROOT . "/fieles/";
            }

            if ($key == "cerSellos" || $key == "keySellos" || $key == "reqSellos") {
                $folder = DOC_ROOT . "/sellos/";
            }

            if ($key == "idse1" || $key == "idse2" || $key == "idse3") {
                $folder = DOC_ROOT . "/idse/";
            }

            $target_path = $folder . basename($_FILES[$key]['name']);

            if (move_uploaded_file($_FILES[$key]['tmp_name'], $target_path)) {
                $this->Util()->DB()->setQuery(
                    "UPDATE
              contract
            SET
              " . $key . " = '" . $target_path . "'
            WHERE
              contractId = '" . $contractId . "'"
                );
                $this->Util()->DB()->UpdateData();
            }

        }
        $this->Util()->setError(10029, "complete");
        $this->Util()->PrintErrors();
        return $contractId;
    }

    /**
     * Update
     *
     * @return Actualiza un contrato
     */
    public function UpdateMyContract()
    {
        global $User, $log;
        $permiso = new Permiso();
        //Obtenemos los datos de la BD antes de actualizar para el Log
        $sql = "SELECT * FROM contract WHERE contractId = '" . $this->contractId . "'";
        $this->Util()->DB()->setQuery($sql);
        $oldData = $this->Util()->DB()->GetRow();
        $valueRzid = !strlen($this->alternativeRzId) ? 'NULL' : "'".$this->alternativeRzId."'";
        $usoCfdi = !strlen($this->claveUsoCfdi) ? 'NULL' : "'".$this->claveUsoCfdi."'";

        $alternativeType    = !strlen($this->alternativeType) ? 'NULL' : "'".$this->alternativeType."'";
        $alternativeRegimen = !strlen($this->alternativeRegimen) ? 'NULL' : "'".$this->alternativeRegimen."'";
        $alternativeUso     = !strlen($this->alternativeUsoCfdi) ? 'NULL' : "'".$this->alternativeUsoCfdi."'";

        //Cuando se edita solo se actualiza los contactos modificados.
        $contactos = "";
        if (strlen($this->nombreComercial) > 0)
            $contactos .= "$this->nombreComercial = '" . $this->nombreComercial . "',";
        if (strlen($this->actividadComercialId) > 0)
            $contactos .= "actividadComercialId = '" . $this->actividadComercialId . "',";
        if (strlen($this->nameContactoAdministrativo) > 0)
            $contactos .= "nameContactoAdministrativo = '" . $this->nameContactoAdministrativo . "',";
        if (strlen($this->emailContactoAdministrativo) > 0)
            $contactos .= "emailContactoAdministrativo = '" . $this->emailContactoAdministrativo . "',";
        if (strlen($this->telefonoContactoAdministrativo) > 0)
            $contactos .= "telefonoContactoAdministrativo = '" . $this->telefonoContactoAdministrativo . "',";
        if (strlen($this->nameContactoContabilidad) > 0)
            $contactos .= "nameContactoContabilidad = '" . $this->nameContactoContabilidad . "',";
        if (strlen($this->emailContactoContabilidad) > 0)
            $contactos .= "emailContactoContabilidad = '" . $this->emailContactoContabilidad . "',";
        if (strlen($this->telefonoContactoContabilidad) > 0)
            $contactos .= "telefonoContactoContabilidad = '" . $this->telefonoContactoContabilidad . "',";
        if (strlen($this->nameContactoDirectivo) > 0)
            $contactos .= "nameContactoDirectivo = '" . $this->nameContactoDirectivo . "',";
        if (strlen($this->emailContactoDirectivo) > 0)
            $contactos .= "emailContactoDirectivo = '" . $this->emailContactoDirectivo . "',";
        if (strlen($this->telefonoContactoDirectivo) > 0)
            $contactos .= "telefonoContactoDirectivo = '" . $this->telefonoContactoDirectivo . "',";
        if (strlen($this->telefonoCelularDirectivo) > 0)
            $contactos .= "telefonoCelularDirectivo = '" . $this->telefonoCelularDirectivo . "',";

        if (isset($_POST['nameRepresentanteLegal']))
            $contactos .= "nameRepresentanteLegal = '" . $this->nameRepresentanteLegal . "',";
        if (isset($_POST['emailRepresentanteLegal']))
            $contactos .= "emailRepresentanteLegal = '" . $this->emailRepresentanteLegal . "',";
        if (isset($_POST['telefonoRepresentanteLegal']))
            $contactos .= "telefonoRepresentanteLegal = '" . $this->telefonoRepresentanteLegal . "',";

        if (isset($_POST['claveCiec']))
            $contactos .= "claveCiec = '" . $this->claveCiec. "',";
        if (isset($_POST['claveFiel']))
            $contactos .= "claveFiel = '" . $this->claveFiel. "',";
        if (isset($_POST['claveIdse']))
            $contactos .= "claveIdse = '" . $this->claveIdse. "',";
        if (isset($_POST['claveSip']))
            $contactos .= "claveSip = '" . $this->claveSip. "',";
        if (isset($_POST['claveIsn']))
            $contactos .= "claveIsn = '" . $this->claveIsn. "',";

        //Actualizamos
        $sql = "UPDATE
			  contract
			SET
			  sociedadId = '" . $this->sociedadId . "',
			  rfc = '" . $this->rfc . "',
			  type = '" . $this->type . "',
			  regimenId = '" . $this->regimenId . "',
			  telefono = '" . $this->telefono . "',
			  address = '" . $this->address . "',
			  `name` = '" . $this->name . "',
			  direccionComercial = '" . $this->direccionComercial . "',
              $contactos
              createSeparateInvoice = '".$this->separateInvoice."',
			  noExtComercial = '" . $this->noExtComercial . "',
			  noIntComercial = '" . $this->noIntComercial . "',
			  coloniaComercial = '" . $this->coloniaComercial . "',
			  municipioComercial = '" . $this->municipioComercial . "',
			  estadoComercial = '" . $this->estadoComercial . "',
			  noExtAddress = '" . $this->noExtAddress . "',
			  noIntAddress = '" . $this->noIntAddress . "',
			  coloniaAddress = '" . $this->coloniaAddress . "',
			  municipioAddress = '" . $this->municipioAddress . "',
			  estadoAddress = '" . $this->estadoAddress . "',
			  paisAddress = '" . $this->paisAddress . "',
			  cpAddress = '" . $this->cpAddress . "',
			  metodoDePago = '" . $this->metodoDePago . "',
			  noCuenta = '" . $this->noCuenta . "',
			  cpComercial = '" . $this->cpComercial . "',
			  encargadoCuenta = '" . $this->encargadoCuenta . "',
              cobrador = '" . $this->cobrador . "',
			  responsableCuenta = '" . $this->responsableCuenta . "',
			  permisos = '" . $this->permisos . "',
			  auxiliarCuenta = '" . $this->auxiliarCuenta . "',
			  facturador = '" . $this->facturador . "',
			  lastModified = '" . date("Y-m-d H:i:s") . "',
			  modifiedBy = '" . $_SESSION["User"]["username"] . "',
			  useAlternativeRzForInvoice = '" .$this->useAlternativeRzForInvoice. "',
			  alternativeRzId = $valueRzid,
			  claveUsoCfdi = $usoCfdi,
			  alternativeRz = '".$this->alternativeRz."',
			  alternativeRfc = '".$this->alternativeRfc."',
			  alternativeCp = '".$this->alternativeCp."',
			  alternativeType = $alternativeType,
			  alternativeRegimen = $alternativeRegimen,
			  alternativeUsoCfdi = $alternativeUso,
			  idTipoClasificacion = '".$this->qualification."'
			  WHERE
			  contractId = '" . $this->contractId . "'";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();

        //insertar nuevos permisos en la tabla contractPermiso
        $permiso->setContractId($this->contractId);
        $permiso->doPermiso();

        $this->Util()->setError(10030, "complete");
        $this->Util()->PrintErrors();


        //reseteamos intentos del dia si se edita la empresa
        $sqlDel = "DELETE FROM attempt_create_invoice 
                WHERE contract_id = '" . $this->contractId . "'
                AND date_format(fecha, '%Y-%m-%d')='".date('Y-m-d')."'";
        $this->Util()->DB()->setQuery($sqlDel);
        $this->Util()->DB()->DeleteData();

        //Obtenemos los nuevos datos ya actualizados para el Log
        $sql = "SELECT * FROM contract WHERE contractId = '" . $this->contractId . "'";
        $this->Util()->DB()->setQuery($sql);
        $newData = $this->Util()->DB()->GetRow();
        //Guardamos y enviamos log
        $log->setPersonalId($User['userId']);
        $log->setFecha(date('Y-m-d H:i:s'));
        $log->setTabla('contract');
        $log->setTablaId($this->contractId);
        $log->setAction('Update');
        $log->setOldValue(serialize($oldData));
        $log->setNewValue(serialize($newData));
        $log->Save();

        //guardar historial
        $log->saveHistoryContract($this->contractId, $newData["activo"], $oldData, $newData);
        return true;
    }

    /**
     * SaveProrrogaTemp
     *
     * @return guarda una nueva prorroga temporalmente
     */
    public function SaveProrrogaTemp()
    {
        if ($this->Util()->PrintErrors()) {
            return 0;
        }

        $_SESSION['prorroga'][$this->docGralId][] = $this->fechaProrroga;;

        $this->Util()->setError(10038, "complete");
        $this->Util()->PrintErrors();

        return true;
    }
    /**
     * Delete
     *
     * @return Borra contratos
     */
    public function Delete()
    {
        global $User, $log, $servicio;
        $add = "";
        $permiso = new Permiso();
        if ($this->Util()->PrintErrors()) {
            return false;
        }
        $info = $this->Info();
        if ($info["activo"] == 'Si') {
            $active = 'No';
            $complete = "La razon social fue dada de baja correctamente";
            $add .= ", fechaBaja = '".date('Y-m-d H:i:s')."' ";
        } else {
            $active = 'Si';
            $complete = "La razon social fue dada de alta correctamente";
        }
        $this->Util()->DB()->setQuery(
            "UPDATE contract
               SET activo = '" . $active . "'
               $add
               WHERE contractId = '" . $this->contractId . "'");
        $this->Util()->DB()->UpdateData();
        //insertar nuevos permisos en la tabla contractPermiso
        $permiso->setContractId($this->contractId);
        $permiso->doPermiso();

        $sql = "SELECT * FROM contract WHERE contractId = '" . $this->contractId . "'";
        $this->Util()->DB()->setQuery($sql);
        $newData = $this->Util()->DB()->GetRow();

        //guardar historial de baja de razon social y dar de baja los servicios con status = activo,readonly o bajaParcial
        $log->saveHistoryContract($this->contractId, $newData["activo"], $info, $newData);
        $serviciosAfectados = $active == 'No'
            ? $servicio->downServicesByContract($this->contractId)
            : $servicio->reactiveServicesByContract($this->contractId);
        $log->setServiciosAfectados($serviciosAfectados);
        //Guardar log y enviar por correo
        $log->setFecha(date('Y-m-d H:i:s'));
        $log->setTabla('contract');
        $log->setTablaId($this->contractId);
        if ($active == "Si")
            $log->setAction('Reactivacion');
        elseif ($active == 'No')
            $log->setAction('Baja');

        $log->setOldValue(serialize($info));
        $log->setNewValue(serialize($newData));
        $log->Save();

        $this->Util()->setError(10031, "complete");
        $this->Util()->PrintErrors();
        return true;
    }

    function c($contract)
    {
        $resPermisos = explode('-', $contract['permisos']);
        $personal = new Personal();
        foreach ($resPermisos as $res) {
            $value = explode(',', $res);

            $idPersonal = $value[1];
            $idDepto = $value[0];

            $personal->setPersonalId($idPersonal);
            $nomPers = $personal->GetNameById();

            $permisos[$idDepto] = $nomPers;
            $permisos2[$idDepto] = $idPersonal;
        }

        $cleanedUp = array();
        foreach ($permisos2 as $id) {
            $personal->setPersonalId($id);
            $responsables = $personal->jefes($id, $idList = array());

            foreach ($responsables as $responsable) {
                $cleanedUp[] = $responsable;
            }
        }
        $cleanedUp = array_unique($cleanedUp);
        return $cleanedUp;

    }

    /**
     * GetNameById
     *
     * @return obtiene el campo name en un contrato
     */
    public function GetNameById()
    {

        $sql = 'SELECT
              name
            FROM
              contract
            WHERE
              contractId = ' . $this->contractId;

        $this->Util()->DB()->setQuery($sql);

        return $this->Util()->DB()->GetSingle();
    }

    function getLastProrroga($contractId, $docGralId)
    {

        $sql = 'SELECT
              fecha
            FROM
              docgral_prorroga
            WHERE
              contractId = ' . $contractId . ' AND docGralId = ' . $docGralId . '
            ORDER BY
              dgProId DESC
            LIMIT 1';
        $this->Util()->DB()->setQuery($sql);
        $fecha = $this->Util()->DB()->GetSingle();

        return $fecha;
    }

    /**
     * GetStatusOblig
     *
     * @return obtiene el estatus en una prorroga
     */
    function GetStatusOblig()
    {

        global $docGral;
        global $util;

        $docsEnt = array();

        $contractId = $this->contractId;

        /* Obtenemos los Documentos Generales - Obligaciones */
        $resDGral = $docGral->Enumerate();

        $statusOb = 3;

        foreach ($resDGral as $val) {

            $card = $val;

            $sql = 'SELECT
                fecha, fechaRec, cartaCump
              FROM
                contract_docgral
              WHERE
                aplica = "1"
              AND
                contractId = "' . $contractId . '"
              AND
                docGralId = "' . $val['docGralId'] . '"';
            $util->DB()->setQuery($sql);
            $row = $util->DB()->GetRow();

            if ($row) {
                /* Checamos si existe Prorroga */
                $fechaProrroga = $this->getLastProrroga($contractId, $val['docGralId']);
                if ($fechaProrroga) {
                    $row['fecha'] = $fechaProrroga;
                }
            }

            if ($row['fecha']) {

                $mesEnt = $util->GetMonthByKey(date('n', strtotime($row['fecha'])));
                $mesEnt = substr($mesEnt, 0, 3);

                $card['fechaEnt'] = date('d', strtotime($row['fecha'])) .
                    ' ' . strtoupper($mesEnt) . ' ' .
                    date('Y', strtotime($row['fecha']));
                $fecha = $row['fecha'];

            }

            if ($row['fechaRec']) {

                $mesRec = $util->GetMonthByKey(date('n', strtotime($row['fechaRec'])));
                $mesRec = substr($mesRec, 0, 3);

                $card['fechaRec'] = date('d', strtotime($row['fechaRec'])) .
                    ' ' . strtoupper($mesRec) . ' ' .
                    date('Y', strtotime($row['fechaRec']));
                $fecha = $row['fechaRec'];
            }

            if ($fecha) {
                $mes = $util->GetMonthByKey(date('n', strtotime($fecha)));
                $mes = substr($mes, 0, 3);
                $card['mes'] = $mes . ' ' . date('y', strtotime($fecha));
            }

            if ($row['fechaRec']) {
                $status = 'Entregado';
            } else {
                $status = $util->GetStatusByDate($fecha);
            }


            if ($row) {

                if ($status == 'Entregado') {
                    $statusOb = 1;
                } elseif ($status == 'Futuro' || $status == 'Proximo' || $status == 'Retrasado') {
                    $statusOb = 2;
                    break;
                }
            }

        } /* foreach */

        return $statusOb;

    }

    /**
     * Validate
     *
     * @return Valida si hay errores
     */
    public function Validate()
    {
        if ($this->Util()->PrintErrors()) {
            return 0;
        }

        return 1;
    }

    /**
     * Suggest
     *
     * @param string $value contiene el valor de la busqueda
     *
     * @return array una lista de resultados de busqueda
     */
    public function Suggest($filter = [], $limit = false, $whitService = false)
    {
        $sfSubquery = $sfQueryPermiso = $sfQuery =  $sfLimit = $sfQueryService = "";
        $tipo =  strtolower($filter['tipos']);
        switch ($tipo) {
            case 'temporal':
            case 'activos':
              $sfQuery .= " and (b.active = '1') ";
              $sfSubquery .= " and contract.activo = 'Si' ";
            break;
            case 'inactivos':
              $sfQuery .= " and (b.active = '0') ";
            break;
        }
        $allowAccessAnyContract = $this->accessAnyContract();
        if($allowAccessAnyContract === false)
            return [];

        $sfQuery .= $filter['clienteId'] ? " and b.customerId = '".$filter['clienteId']."' ": "";

        $idImplode =  implode(',',  $filter['subordinados']);
        $sfQueryPermiso .= $allowAccessAnyContract === '0' || $filter['responsableCuenta'] > 0
            ? " and contractPermiso.personalId IN ($idImplode) "
            : "";
        $sfSubquery .= isset($filter['like']) && $filter['like'] !== '' ? " or contract.name like '%".$filter['like']."%' " : "";
        $sfQuery .= isset($filter['like']) && $filter['like'] !== '' ? " and (b.nameContact like '%".$filter['like']."%' or a.name like '%".$filter['like']."%' or a.rfc like '%".$filter['like']."%') " : "";
        $sfLimit .= $limit ? " LIMIT 15 " : "";

        $sfSubquery .= isset($filter['like_contract_name'])  && $filter['like_contract_name'] !== '' ? " or  contract.name like '%".$filter['like_contract_name']."%' " : "";
        $strCustomerName =  isset($filter['like_customer_name']) && $filter['like_customer_name'] !== '' ? " and (b.nameContact like '%".$filter['like_customer_name']."%'  " : "";

        $strContractName =  isset($filter['like_contract_name']) && $filter['like_contract_name'] !=='' && $strCustomerName !== '' ?" or a.name like '%".$filter['like_contract_name']."%' or a.rfc like '%".$filter['like_contract_name']."%')" :
            (isset($filter['like_contract_name']) && $filter['like_contract_name'] !== '' ? " and (a.name like '%".$filter['like_contract_name']."%' or a.rfc like '%".$filter['like_contract_name']."%') " :
            (isset($filter['like_customer_name']) && $strCustomerName !== '' ? " ) ": ""));
        $sfQuery .= $strCustomerName.$strContractName;

        // service filter
        if($_SESSION['User']['level'] > 50) {
            $sfQueryService .= " AND a.status IN('activo', 'bajaParcial')";
            $skip =  true;
        } else {
            if(isset($filter["statusSearch"]))
            {
                switch($filter["statusSearch"]){
                    case 'activo':
                        $sfQueryService = " AND a.status IN('activo','readonly') ";
                        break;
                    case 'baja':
                        $sfQueryService = " AND a.status IN('baja','bajaParcial') ";
                        break;
                    default:
                        $sfQueryService = " AND a.status IN('activo','bajaParcial','readonly','baja') ";
                        break;
                }
            }else
                $sfQueryService .= " AND a.status IN('activo','bajaParcial','readonly') ";
        }
        if($filter['departamentoId'])
            $sfQueryService .= " AND b.departamentoId='".$filter['departamentoId']."'";

        $sQuery = "SELECT   a.*, b.customerId as clienteId, b.nameContact, b.phone, b.email, b.password,b.noFactura13,
                 b.fechaAlta,b.observacion,b.active
                 FROM (SELECT contract.contractId, contract.name, contract.customerId, contract.type, contract.rfc,
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
             ) as a 
             INNER JOIN customer b
             ON a.customerId = b.customerId   
             WHERE 1 $sfQuery  order by  b.nameContact asc, a.name ASC $sfLimit";
        $this->Util()->DB()->setQuery($sQuery);
        $result = $this->Util()->DB()->GetResult();
        $contracts = [];
        $contractDim = [2507,2508];
        foreach($result as $key => $value) {
            $allow = $allowAccessAnyContract === '1' &&  $filter['responsableCuenta'] <=0 ? true : false;
            $this->setContractId($value['contractId']);
            $responsables = $this->getPermisosByContract($sfQueryPermiso);
            if (!$allow) {
                $allow =  count($responsables);
            }
            if(!$allow)
                continue;

            $value['responsables'] = count($responsables) > 0 ? $responsables : [];
            $noInclude = (isset($ilter['year']) && $filter['year'] >= 2018) && !in_array($value['contractId'], $contractDim) ?
                "AND lower(b.nombreServicio) NOT LIKE '%dim%'" : "";
            if($whitService) {
                $sql = "SELECT a.status as servicioStatus, a.servicioId, a.contractId, a.tipoServicioId, a.costo, a.status,
                        a.inicioOperaciones, a.inicioFactura, a.fechaBaja, a.lastDateWorkflow, b.nombreServicio, b.periodicidad, 
                        b.costoVisual, b.departamentoId,b.nombreServicio
                        FROM servicio a
                        LEFT JOIN tipoServicio b ON a.tipoServicioId = b.tipoServicioId
                        WHERE a.contractId = '".$value["contractId"]."' $sfQueryService
                        AND b.status='1' $noInclude
                        ORDER BY b.nombreServicio ASC,a.servicioid ASC";
                $this->Util()->DB()->setQuery($sql);
                $value["servicios"] = $this->Util()->DB()->GetResult();
              if(count($value['servicios']) <=0 )
                  continue;
            }
            array_push($contracts, $value);
        }
        return $contracts;
    }

    /**
     * UsuariosConPermiso
     *
     * @param string $permisos contiene la cadena de permisos de la DB
     * @param int $extraId contiene el id del responsable de la cuenta
     *
     * @return devuelve un arreglo con los persmisos departamento => usuarioAsignado
     */
    public function UsuariosConPermiso($permisos, $extraId)
    {
        $permisos = explode("-", $permisos);

        foreach ($permisos as $permiso) {
            list($depa, $resp) = explode(",", $permiso);

            if ($depa == 25) {
                continue;
            }

            if ($resp) {
                $misPermisos[$depa] = $resp;
            }
            if ($resp) {
                $misPermisos[$depa] = $resp;
            }
        }

        if (count($misPermisos) == 0) {
            $misPermisos[1] = $extraId;
        }
        return $misPermisos;

    }
    public function getPermisosByContract($sfQueryPermiso =  "") {
       $sQuery =  "SELECT contractPermiso.contractId,contractPermiso.departamentoId, contractPermiso.personalId, 
                    departamentos.departamento,personal.name 
                    FROM contractPermiso 
                    LEFT JOIN departamentos ON contractPermiso.departamentoId =  departamentos.departamentoId
                    INNER JOIN personal ON contractPermiso.personalId =  personal.personalId 
                    WHERE contractPermiso.contractId = '".$this->contractId."' $sfQueryPermiso ";
        $this->Util()->DB()->setQuery($sQuery);
        return $this->Util()->DB()->GetResult();
    }
    public function UpdateComentario($comentario)
    {
        global $User;

        if ($this->Util()->PrintErrors()) {
            return false;
        }

        $this->Util()->DB()->setQuery("
			UPDATE
				contract
			SET
				`comentario` = '" . $comentario . "'
			WHERE contractId = '" . $this->contractId . "'");
        $this->Util()->DB()->UpdateData();


        $this->Util()->setError(1, "complete");
        $this->Util()->PrintErrors();
        return true;
    }

    public function HistorialAll()
    {
        $this->Util()->DB()->setQuery("
			SELECT contractChanges.*, personal.name AS personalName, contract.customerId, contract.name AS contractName, customer.customerId, customer.nameContact FROM contractChanges
			JOIN personal ON personal.personalId = contractChanges.personalId
			JOIN contract ON contract.contractId = contractChanges.contractId
			JOIN customer ON customer.customerId = contract.customerId
			ORDER BY contractChanges.contractChangesId DESC");
        $data = $this->Util()->DB()->GetResult();

        return $data;
    }

    public function getTotalIguala()
    {
        $sql = "SELECT SUM(a.costo) FROM servicio a 
              INNER JOIN tipoServicio b ON a.tipoServicioId=b.tipoServicioId  AND b.status='1' 
              WHERE a.contractId='" . $this->contractId . "' 
              AND a.status='activo' AND  TO_DAYS(STR_TO_DATE(a.inicioFactura,'%Y-%m-%d')) IS NOT NULL
              AND b.departamentoId IN(1,24)";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $single = $this->Util()->DBSelect($_SESSION['empresaId'])->GetSingle();
        return $single;
    }

    public function getInfoLastContract()
    {
        $this->Util()->DB()->setQuery('SELECT * FROM contract WHERE customerId="' . $this->customerId . '"  ORDER BY contractId DESC');
        $row = $this->Util()->DB()->GetRow();
        return $row;
    }

    /*
     * funcion ValidateEncargados
     * @parametros
     * $row informacion de contrato(contractId, permisos, etc).
     * @devuelve
     * FALSE = en caso de que el contrato no exista.
     * permisos=un string con los permisos nuevos en caso de que hubo cambio.
     */
    public function ValidateEncargados($row = array())
    {
        $permisos = "";
        $this->Util()->DB()->setQuery('SELECT permisos,contractId from contract WHERE contractId="' . $row[0] . '" ');
        $contrato_actual = $this->Util()->DB()->GetRow();
        $dptos = array();
        if (empty($contrato_actual))//||((trim($row[40])==""||trim($row[40])=="--")&&(trim($row[41])==""||trim($row[41])=="--"))
        {
            return false;
        }
        $permisos_actuales = explode("-", $contrato_actual['permisos']);
        foreach ($permisos_actuales as $val) {
            $dep = explode(',', $val);
            $dptos[$dep[0]] = $dep[1];
        }
        $deptosNew = array();
        //encontrar id de responsables.
        /*--------------------------------------------------------------------------------------*/
        if (array_key_exists(1, $dptos) && $dptos[1] > 0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[1]) . "' ");
            $respConId = $this->Util()->DB()->GetSingle();
            if ($dptos[1] != $respConId && $respConId > 0)
                $deptosNew[1] = $respConId;
            else {
                if (trim($row[1]) != "")
                    $deptosNew[1] = $dptos[1];
            }

        } else {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[1]) . "' ");
            $respConId = $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if ($respConId) {
                $deptosNew[1] = $respConId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if (array_key_exists(8, $dptos) && $dptos[8] > 0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[2]) . "' ");
            $respNomId = $this->Util()->DB()->GetSingle();
            if ($dptos[8] != $respNomId && $respNomId > 0)
                $deptosNew[8] = $respNomId;
            else {
                if (trim($row[2]) != "")
                    $deptosNew[8] = $dptos[8];
            }

        } else {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[2]) . "' ");
            $respNomId = $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if ($respNomId) {
                $deptosNew[8] = $respNomId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if (array_key_exists(21, $dptos) && $dptos[21] > 0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[3]) . "' ");
            $respAdmId = $this->Util()->DB()->GetSingle();
            if ($dptos[21] != $respAdmId && $respAdmId > 0)
                $deptosNew[21] = $respAdmId;
            else {
                if (trim($row[3]) != "")
                    $deptosNew[21] = $dptos[21];
            }

        } else {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[3]) . "' ");
            $respAdmId = $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if ($respAdmId) {
                $deptosNew[21] = $respAdmId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if (array_key_exists(22, $dptos) && $dptos[22] > 0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[4]) . "' ");
            $respJurId = $this->Util()->DB()->GetSingle();
            if ($dptos[22] != $respJurId && $respJurId > 0)
                $deptosNew[22] = $respJurId;
            else {
                if (trim($row[4]) != "")
                    $deptosNew[22] = $dptos[22];
            }

        } else {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[4]) . "' ");
            $respJurId = $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if ($respJurId) {
                $deptosNew[22] = $respJurId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if (array_key_exists(24, $dptos) && $dptos[24] > 0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[5]) . "' ");
            $respImmId = $this->Util()->DB()->GetSingle();
            if ($dptos[24] != $respImmId && $respImmId > 0)
                $deptosNew[24] = $respImmId;
            else {
                if (trim($row[5]) != "")
                    $deptosNew[24] = $dptos[24];
            }

        } else {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[5]) . "' ");
            $respImmId = $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if ($respImmId) {
                $deptosNew[24] = $respImmId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if (array_key_exists(31, $dptos) && $dptos[31] > 0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[6]) . "' ");
            $respAudId = $this->Util()->DB()->GetSingle();
            if ($dptos[31] != $respAudId && $respAudId > 0)
                $deptosNew[31] = $respAudId;
            else {
                if (trim($row[6]) != "")
                    $deptosNew[31] = $dptos[31];
            }

        } else {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[6]) . "' ");
            $respAudId = $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if ($respAudId) {
                $deptosNew[31] = $respAudId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        $this->Util()->DB()->setQuery("SELECT departamentoId FROM departamentos WHERE upper(departamento)='DESARROLLO HUMANO' ");
        $keyDH = $this->Util()->DB()->GetSingle();
        if ($keyDH > 0) {
            if (array_key_exists($keyDH, $dptos) && $dptos[$keyDH] > 0) {
                $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[7]) . "' ");
                $respDh = $this->Util()->DB()->GetSingle();
                if ($dptos[$keyDH] != $respDh && $respDh > 0)
                    $deptosNew[$keyDH] = $respDh;
                else {
                    if (trim($row[7]) != "")
                        $deptosNew[$keyDH] = $dptos[$keyDH];
                }

            } else {
                $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[7]) . "' ");
                $respDh = $this->Util()->DB()->GetSingle();
                //si el responsable existe se agrega
                if ($respDh) {
                    $deptosNew[$keyDH] = $respDh;
                }
            }
        }
        /*--------------------------------------------------------------------------------------*/
        $per = array();
        foreach ($deptosNew as $kp => $valp) {
            $cad = $kp . "," . $valp;
            array_push($per, $cad);
        }

        $permisos = implode('-', $per);
        unset($per);
        unset($dptos);
        unset($deptosNew);
        return $permisos;

    }

    function ConcatenarEncargadosRebuild($row = array())
    {
        $permisos = "";
        $deptos = array();
        //comprobar que los encargados esten dados de alta siempre y cuando no este vacio
        if ($row[0] != "" and $row[0] != "--") {
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='" . strtolower(trim($row[0])) . "'");
            $idCont = $this->Util()->DB()->GetSingle();
            if ($idCont)
                $deptos[1] = $idCont;
        }
        if ($row[1] != "" and $row[1] != "--") {
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='" . strtolower(trim($row[1])) . "'");
            $idNom = $this->Util()->DB()->GetSingle();
            if ($idNom)
                $deptos[8] = $idNom;
        }
        if ($row[2] != "" and $row[2] != "--") {
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='" . strtolower(trim($row[2])) . "'");
            $idAdmin = $this->Util()->DB()->GetSingle();
            if ($idAdmin)
                $deptos[21] = $idAdmin;
        }
        if ($row[3] != "" and $row[3] != "--") {
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='" . strtolower(trim($row[3])) . "'");
            $idJur = $this->Util()->DB()->GetSingle();
            if ($idJur)
                $deptos[22] = $idJur;

        }
        if ($row[4] != "" and $row[4] != "--") {
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='" . strtolower(trim($row[4])) . "'");
            $idImss = $this->Util()->DB()->GetSingle();
            if ($idImss)
                $deptos[24] = $idImss;
        }
        if ($row[5] != "" and $row[5] != "--") {
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='" . strtolower(trim($row[5])) . "'");
            $idAud = $this->Util()->DB()->GetSingle();
            if ($idAud)
                $deptos[31] = $idAud;
        }
        $permisosArray = array();
        foreach ($deptos as $dep => $per) {
            $cad = "";
            $cad = $dep . "," . $per;
            $permisosArray[] = $cad;
        }
        $permisos = implode('-', $permisosArray);
        return $permisos;
    }

    public function findEmailEncargadosJefesByContractId($filtros = [])
    {
        global $personal;
        $jefes = [];
        $defaultId = [0];
        $encargados = [];
        $data = [];
        $departamentos = $filtros['departamentos'] ?? [];
        array_push($defaultId, IDHUERIN);

        $filtroDep = "";
        $filtroDepGen = "";
        if(count($departamentos) > 0) {
            $filtroDep = " AND a.departamentoId IN(" . implode(",", $departamentos) . ")";
            $filtroDepGen = " AND departamentoId IN(" . implode(",", $departamentos) . ")";
        }

        $sql = "SELECT a.name as razon,b.nameContact as cliente FROM contract a INNER JOIN customer b ON a.customerId=b.customerId WHERE a.contractId='" . $this->contractId . "' ";
        $this->Util()->DB()->setQuery($sql);
        $razonSocial = $this->Util()->DB()->GetRow();
        $sqlp = "select a.personalId,b.email,b.name from contractPermiso a  left join personal b ON  a.personalId=b.personalId where a.contractId='" . $this->contractId . "' ".$filtroDep;
        $this->Util()->DB()->setQuery($sqlp);
        $permisos = $this->Util()->DB()->GetResult();
        foreach ($permisos as $permiso) {
            if ($this->Util()->ValidateEmail(trim($permiso['email']))) {
                $encargados[trim($permiso['email'])] = $permiso['name'];
                //encontramos los jefes de forma ascendente de los encargados de cuenta
                $personal = new Personal();
                if ($filtros['incluirJefes']) {
                    $yourJefes = $personal->Jefes($permiso['personalId']);
                    $jefes = array_merge($jefes, $yourJefes);
                }
            }
        }
        if (!empty($jefes) && $filtros['sendBraun']) {
            array_push($jefes, IDBRAUN);
        }
        //Si no tiene ningun encargado se usan los gerentes, coordinador y socio.
        //Se excluyen los del departamento de mensajeria y RRHH)
        if (empty($encargados)) {
            if ($filtros['sendBraun'])
                array_push($defaultId, IDBRAUN);
            if (!$filtros['sendHuerin']) {
                $indexFind = array_search(IDHUERIN, $defaultId);
                if ($indexFind)
                    unset($defaultId[$indexFind]);
            }

            $sqlo = "SELECT email,name FROM personal  WHERE (LOWER(puesto) LIKE'%gerente%') OR personalId IN (" . implode(',', $defaultId) . ") ".$filtroDepGen;
            $this->Util()->DB()->setQuery($sqlo);
            $persons = $this->Util()->DB()->GetResult();
            foreach ($persons as $pers) {
                if ($this->Util()->ValidateEmail(trim($pers['email'])))
                    $encargados[trim($pers['email'])] = $pers['name'];
            }
        }
        //encontrar correos de los jefes de cada encargado, esto siempre se debe cumplor debido  a que todos tiene jefe inmediato hasta llegar a jacobo
        $correosJefes = array();
        if (!empty($jefes)) {
            //si jefes no esta vacio hay que agregar a ROGELIO y el nuevo socio Ricardo
            // array_push($jefes, 32);
            //array_push($jefes, 319);
            $jefes = array_unique($jefes);
            //comprobar si se excluye a huerin
            if (!$filtros['sendHuerin']) {
                $index = array_search(IDHUERIN, $jefes);
                if ($index)
                    unset($jefes[$index]);
            }
            $ids = implode(',', $jefes);
            $this->Util()->DB()->setQuery('SELECT email,name FROM personal WHERE personalId IN(' . $ids . ') AND active="1" ');
            $resultJefes = $this->Util()->DB()->GetResult();
            foreach ($resultJefes as $var) {
                if ($this->Util()->ValidateEmail(trim($var['email']))) {
                    $correosJefes[trim($var['email'])] = $var['name'];
                }
            }
        }
        $encargados = array_merge($encargados, $correosJefes);
        $data['encargados'] = $encargados;
        $data['razon'] = $razonSocial;
        return $data;
    }

    public function TrasnferContract()
    {
        $sql = "select contractId from contract where customerId='" . $this->customerId . "' and name=(select name from contract where contractId='" . $this->contractId . "') ";
        $this->Util()->DB()->setQuery($sql);
        $find = $this->Util()->DB()->GetSingle();
        if ($find > 0) {
            $this->Util()->setError(0, "error", "El cliente seleccionado, cuenta con una razon social con el mismo nombre que desea transferir, verificar informacion o elegir un cliente diferente.");
        }
        if ($this->Util()->PrintErrors())
            return false;
        $up = "update contract set customerId='" . $this->customerId . "' where contractId='" . $this->contractId . "' ";
        $this->Util()->DB()->setQuery($up);
        $this->Util()->DB()->UpdateData();
        $this->Util()->setError(0, "complete", "Contrato transferido correctamente.");
        return true;
    }
    /*
     * @
     */
    public function downContractByCustomer($id){
        global $log, $servicio;
        $contractsAfectados = [];
        $sql ="select *  from contract where customerId='$id' and activo = 'Si' ";
        $this->Util()->DB()->setQuery($sql);
        $contracts = $this->Util()->DB()->GetResult();
        foreach($contracts as $ky => $con){
            $currentContract = [];
            $fechaBaja = date('Y-m-d H:i:s');
            $newData = $con;
            $newData['fechaBaja'] = $fechaBaja;
            $newData['activo'] = 'No';

            $this->Util()->DB()->setQuery("
                UPDATE contract
                SET activo = 'No',
                fechaBaja = '$fechaBaja'
                WHERE contractId = '" . $con['contractId']. "'");
            $del = $this->Util()->DB()->UpdateData();
            if($del > 0) {
                $log->saveHistoryContract($con['contractId'], 'No', $con, $newData);
                //Guardar log y enviar por correo
                $log->setFecha($fechaBaja);
                $log->setTabla('contract');
                $log->setTablaId($con['contractId']);
                $log->setAction('Baja');
                $log->setOldValue(serialize($con));
                $log->setNewValue(serialize($newData));
                $log->SaveOnly();

                $serviciosAfectados = $servicio->downServicesByContract($con['contractId']);
                $currentContract = $newData;
                $currentContract['serviciosAfectados'] = $serviciosAfectados;
                array_push($contractsAfectados,$currentContract);
            }
        }
        return $contractsAfectados;
    }
    /*
    * @
    */
    public function reactiveContractByCustomer($id){
        global $log, $servicio;
        $contractsAfectados = [];
        $sql ="select *  from contract where customerId='$id' and activo = 'No' ";
        $this->Util()->DB()->setQuery($sql);
        $contracts = $this->Util()->DB()->GetResult();
        foreach($contracts as $ky => $con){
            $currentContract = [];
            $con['fechaBaja'] = null;
            $fechaAlta = date('Y-m-d H:i:s');
            $newData = $con;
            $newData['fechaAlta'] = $fechaAlta;
            $newData['activo'] = 'Si';

            $this->Util()->DB()->setQuery("
                UPDATE contract
                SET activo = 'Si',
                fechaBaja = null,
                fechaAlta ='$fechaAlta'   
                WHERE contractId = '" . $con['contractId']. "'");
            $del = $this->Util()->DB()->UpdateData();
            if($del > 0) {
                $log->saveHistoryContract($con['contractId'], 'Si', $con, $newData);
                //Guardar log y enviar por correo
                $log->setFecha($fechaAlta);
                $log->setTabla('contract');
                $log->setTablaId($con['contractId']);
                $log->setAction('Reactivacion');
                $log->setOldValue(serialize($con));
                $log->setNewValue(serialize($newData));
                $log->SaveOnly();
                $serviciosAfectados = $servicio->reactiveServicesByContract($con['contractId']);
                $currentContract = $newData;
                $currentContract['serviciosAfectados'] = $serviciosAfectados;
                array_push($contractsAfectados,$currentContract);
            }
        }
        return $contractsAfectados;
    }
    public function SuggestContractPureAutocomplete ($value) {
        $this->Util()->DB()->setQuery("SELECT * FROM contract WHERE name LIKE '%".$value."%' and activo ='Si'
                                              and customerId = '".$this->customerId."' ORDER BY name ASC LIMIT 15");
        $result =  $this->Util()->DB()->GetResult();
        return $result;
    }

    public function guardarAcuerdoComercial () {
        if($this->Util()->PrintErrors())
            return false;

        $this->Util()->DB()->setQuery("UPDATE contract SET acuerdo_comercial='".$this->acuerdoComercial."' WHERE contractId='".$this->contractId."'");
        $this->Util()->DB()->UpdateData();
        $this->Util()->setError(0, "complete", "Registro actualizado correctamente.");
        $this->Util()->PrintErrors();
        return true;
    }
}
?>
