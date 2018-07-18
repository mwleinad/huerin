<?php
/**
* contract.class.php
*
* PHP version 5
*
* @category Desarrollo
* @package  Contract.class.php
* @author   Daniel Lopez <desarrollos@avantika.com.mx>
* @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link     http://avantika.com.mx
**/

session_start();

/**
* Contract
*
* @category Desarrollo
* @package  Contract.class.php
* @author   Daniel Lopez <desarrollos@avantika.com.mx>
* @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link     http://avantika.com.mx
*/

class Contract extends Main
{
  private $facturador;
  public function setFacturador($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 0, 'Facturador');
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
  public function setPermisos($value, $firsValue)
  {
    $this->permisos = "1,".$firsValue."-".implode("-", array_filter($value));
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
    $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 0, 'No Ext Address');
    $this->noExtAddress = $value;
  }

  private $noIntAddress;
  public function setNoIntAddress($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 0, 'No Int Address');
    $this->noIntAddress = $value;
  }

  private $coloniaAddress;
  public function setColoniaAddress($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Colonia Address');
    $this->coloniaAddress = $value;
  }

  private $municipioAddress;
  public function setMunicipioAddress($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Municipio Address');
    $this->municipioAddress = $value;
  }

  private $estadoAddress;
  public function setEstadoAddress($value)
  {
    $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Estado Address');
    $this->estadoAddress = $value;
  }

    private $paisAddress;
    public function setPaisAddress($value)
    {
        $this->Util()->ValidateString($value, $max_chars = 255, $minChars = 1, 'Pais Address');
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

  private $contractId;
  private $customerId;
  private $wallmartId;
  private $personalId;
  private $name;
  private $folio;
  private $stateId;
  private $cityId;
  private $address;
  private $superficie;
  private $superficieA;
  private $montoRenta;
  private $contCatId;
  private $contSubcatId;
  private $subtype;
  private $status;
  private $docsBasic;
  private $docsSellado;
  private $docsGral;
  private $fechaFirmado;
  private $cartaCump;
  private $fechaCartaCump;
  private $arrendatario;
  private $arrendador;
  private $partes;
  private $year;
  private $fechaProrroga;
  private $docGralId;
  private $docBasicId;
  private $fechaDoc;
  private $desc;
  private $observaciones;

  /*
   * Arrendamiento
   */

  private $ubicacion;
  private $fechaSolicitud;
  private $fechaEnvio;
  private $respProy;
  private $respProyWal;
  private $propAcreditada;
  private $titPropiedad;
  private $dictamen;
  private $firma;
  private $fechaFirma;
  private $plazoPromesa;
  private $plazoArrendamiento;
  private $cobrado;
  private $fechaCobrado;
  private $empresaFirmo;
  private $actaEntrega;
  private $inmuebleEntregado;
  private $fechaInmEnt;

   /*
    * Subcontrol para Certificados de Libertad de Gravamen
    */
  private $certLibGrav;
  private $noCertificados;
  private $ubicacionRpp;
  private $nomTramitaCert;
  private $fechaSolCert;
  private $derechosCert;
  private $honorariosCert;
  private $fechaEntCert;
  private $resCert;
  private $comentarios;
  private $comentario;
  private $certEnviado;
  private $fechaCertEnv;

  /*
   * CompraVenta
   */
  private $persona;
  private $edoCivil;
  private $actaMat;
  private $conDiv;
  private $constitutiva;
  private $modEstatutos;
  private $poder;
  private $rfc;
  private $predial;
  private $fechaPredial;
  private $agua;
  private $fechaAgua;
  private $titProp;
  private $subdivInm;
  private $cuandoDivInm;
  private $fraccionamiento;
  private $descInm;
  private $autFracc;
  private $autEnajenacion;
  private $transHas;
  private $licConst;
  private $munObras;
  private $usoSuelo;
  private $usoSueloEsp;
  private $agenda;
  private $precio;
  private $comparativa;
  private $construcciones;
  private $certificaciones;
  private $avaluo;
  private $notario;
  private $proyEscritura;
  private $fechaProyEsc;
  private $proyEscrAprobado;
  private $calculoIsr;
  private $fechaCalcIsr;
  private $calculoIva;
  private $fechaCalcIva;
  private $cheques;
  private $fechaCheques;
  private $fechaCompVta;
  private $fechaPagoImp;
  private $calcImpDer;
  private $copiaCertEsc;
  private $avaluoCatastral;
  private $ultPagoPredial;
  private $boletaAgua;
  private $inmProvSubdiv;
  private $factNotaria;
  private $pagoImpWal;
  private $fechaImpWal;
  private $compPagoImpNot;
  private $fechaImpNot;
  private $pagoHonorarios;
  private $fechaHonorarios;
  private $pagoPteWal;
  private $descPagoPteWal;
  private $escRpp;
  private $fechaEscRpp;
  private $firmaContWal;

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
    if ($this->Util()->ValidateRequireField($value, 'CP Fiscal')) {
      $this->Util()->ValidateString($value, $max_chars = 5, $minChars = 5, 'CP Fiscal');
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

  public function setContractId($value)
  {
    $this->Util()->ValidateInteger($value);
    $this->contractId = $value;
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

  public function setWallmartId($value)
  {
    if ($this->Util()->ValidateRequireField($value, 'Responsable del are&aacute; de cierres')) {
      $this->Util()->ValidateString(
          $value,
          $max_chars = 60,
          $minChars = 1,
          'Responsable del are&aacute; de cierres'
      );
    }
    $this->wallmartId = $value;
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
      $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, 'Razon Social');
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

  public function setSubtype($value)
  {
    $this->subtype = $value;
  }

  public function setStatus($value)
  {
    if ($this->Util()->ValidateRequireField($value, 'Estatus')) {
      $this->Util()->ValidateString($value, $max_chars = 60, $minChars = 1, 'Estatus');
    }
    $this->status = $value;
  }

  public function setObservaciones($value)
  {
    $this->observaciones = $value;
  }

  public function setDocsBasic($value)
  {
    $this->docsBasic = $value;
  }

  public function setDocsSellado($value)
  {
    $this->docsSellado = $value;
  }

  public function setDocsGral($value)
  {
    $this->docsGral = $value;
  }

  public function setYear($value)
  {
    if ($this->Util()->ValidateRequireField($value, 'A&ntilde;o')) {
      $this->year = $value;
    }
  }

  public function setFechaProrroga($value)
  {
    /* if ($value < date('Y-m-d'))
     * $this->Util()->setError(10056, "error");
     */
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

  /*
   * Varios
   */

  public function setUbicacion($value)
  {
    $this->ubicacion = $value;
  }

  public function setStateId($value)
  {
    if ($this->Util()->ValidateRequireField($value, 'Estado')) {
      $this->stateId = $value;
    }
  }

  public function setCityId($value)
  {
    if ($this->Util()->ValidateRequireField($value, 'Municipio')) {
      $this->cityId = $value;
    }
  }

  public function setAddress($value)
  {
    if ($this->Util()->ValidateRequireField($value, 'Calle')) {
      $this->address = $value;
    }
  }

  public function setFechaSolicitud($value)
  {
    $this->fechaSolicitud = $value;
  }

  public function setRespProy($value)
  {
    $this->respProy = $value;
  }

  public function setRespProyWal($value)
  {
    $this->respProyWal = $value;
  }

  public function setSuperficie($value)
  {
    $this->superficie = $value;
  }

  public function setSuperficieA($value)
  {
    $this->superficieA = $value;
  }

  public function setMontoRenta($value)
  {
    if (trim($value) != '') {
      if ($this->Util()->ValidateDecimal($value)) {
        $this->montoRenta = $value;
      }
    }
  }

  public function setFechaFirmado($value)
  {
    if ($this->status == 'firmado') {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Firma del Contrato')) {
        $this->fechaFirmado = $value;
      }
    }
  }

  public function setCartaCump($value)
  {
    $this->cartaCump = $value;
  }

  public function setFechaCartaCump($value)
  {
    if ($this->cartaCump) {
      if ($this->Util()->ValidateRequireField($value, 'Fecha de Envio Carta de Cumplimiento')) {
        $this->fechaCartaCump = $value;
      }
    }
  }

  public function setArrendador($value)
  {
    $this->arrendador = $value;
  }

  public function setArrendatario($value)
  {
    $this->arrendatario = $value;
  }

  public function setPartes($value)
  {
    $this->partes = $value;
  }

  /*
   *Arrendamiento
   */

  public function setFechaEnvio($value)
  {
    $this->fechaEnvio = $value;
  }

  public function setPropAcreditada($value)
  {
    $this->propAcreditada = $value;
  }

  public function setTitPropiedad($value)
  {
    $this->titPropiedad = $value;
  }

  public function setDictamen($value)
  {
    $this->dictamen = $value;
  }

  public function setFirma($value)
  {
    $this->firma = $value;
  }

  public function setFechaFirma($value)
  {
    if ($this->firma == 'firmado') {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Firma del Contrato')) {
        $this->fechaFirma = $value;
      }
    }
  }

  public function setPlazoPromesa($value)
  {
    $this->plazoPromesa = $value;
  }

  public function setPlazoArrendamiento($value)
  {
    $this->plazoArrendamiento = $value;
  }

  public function setCobrado($value)
  {
    $this->cobrado = $value;
  }

  public function setFechaCobrado($value)
  {
    if ($this->cobrado) {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Cobrado')) {
        $this->fechaCobrado = $value;
      }
    }
  }

  public function setEmpresaFirmo($value)
  {
    $this->empresaFirmo = $value;
  }

  public function setActaEntrega($value)
  {
    $this->actaEntrega = $value;
  }

  public function setInmuebleEntregado($value)
  {
    $this->inmuebleEntregado = $value;
  }

  public function setFechaInmEnt($value)
  {
    if ($this->inmuebleEntregado) {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Inmueble Entregado')) {
        $this->fechaInmEnt = $value;
      }
    }
  }

  /*
   *Subcontrol para Certificados de Libertad de Gravamen
   */

  public function setCertLibGrav($value)
  {
    $this->certLibGrav = $value;
  }

  public function setNoCertificados($value)
  {
    $this->noCertificados = $value;
  }

  public function setUbicacionRpp($value)
  {
    $this->ubicacionRpp = $value;
  }
  public function setNomTramitaCert($value)
  {
    $this->nomTramitaCert = $value;
  }

  public function setFechaSolCert($value)
  {
    $this->fechaSolCert = $value;
  }

  public function setDerechosCert($value)
  {
    $this->derechosCert = $value;
  }

  public function setHonorariosCert($value)
  {
    $this->honorariosCert = $value;
  }

  public function setFechaEntCert($value)
  {
    $this->fechaEntCert = $value;
  }

  public function setResCert($value)
  {
    $this->resCert = $value;
  }

  public function setComentarios($value)
  {
    $this->comentarios = $value;
  }

  public function setComentario($value)
  {
    if ($this->Util()->ValidateRequireField($value, 'Comentario en Resultado del Certificado')) {
      $this->comentario = $value;
    }
  }

  public function setCertEnviado($value)
  {
    $this->certEnviado = $value;
  }

  public function setFechaCertEnv($value)
  {
    if ($this->certEnviado) {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Envio de Certificado a Walmart')) {
        $this->fechaCertEnv = $value;
      }
    }
  }

  /*
   *CompraVenta
   */

  public function setPersona($value)
  {
    $this->persona = $value;
  }

  public function setEdoCivil($value)
  {
    $this->edoCivil = $value;
  }

  public function setActaMat($value)
  {
    $this->actaMat = $value;
  }

  public function setConDiv($value)
  {
    $this->conDiv = $value;
  }

  public function setConstitutiva($value)
  {
    $this->constitutiva = $value;
  }

  public function setModEstatutos($value)
  {
    $this->modEstatutos = $value;
  }

  public function setPoder($value)
  {
    $this->poder = $value;
  }

  public function setRfc($value)
  {
    $value = str_replace(" ", "", $value);
    $value = str_replace("-", "", $value);
    $value = strtoupper($value);
    if ($this->Util()->ValidateRequireField($value, 'RFC')) {
      $this->Util()->ValidateString($value, $max_chars = 13, $minChars = 11, 'RFC');
    }

    $value = str_replace("&amp;", "&", $value);

    $this->rfc = $value;
  }

  public function setPredial($value)
  {
    $this->predial = $value;
  }

  public function setFechaPredial($value)
  {
    if ($this->predial) {
      if ($this->Util()->ValidateRequireField($value, 'Predial Pagado hasta')) {
        $this->fechaPredial = $value;
      }
    }
  }

  public function setAgua($value)
  {
    $this->agua = $value;
  }

  public function setFechaAgua($value)
  {
    if ($this->agua == 1) {
      if ($this->Util()->ValidateRequireField($value, 'Agua Pagado hasta')) {
        $this->fechaAgua = $value;
      }
    }
  }

  public function setTitProp($value)
  {
    $this->titProp = $value;
  }

  public function setSubdivInm($value)
  {
    $this->subdivInm = $value;
  }

  public function setCuandoDivInm($value)
  {
    $this->cuandoDivInm = $value;
  }

  public function setFraccionamiento($value)
  {
    $this->fraccionamiento = $value;
  }

  public function setDescInm($value)
  {
    $this->descInm = $value;
  }

  public function setAutFracc($value)
  {
    $this->autFracc = $value;
  }

  public function setAutEnajenacion($value)
  {
    $this->autEnajenacion = $value;
  }

  public function setTransHas($value)
  {
    $this->transHas = $value;
  }

  public function setLicConst($value)
  {
    $this->licConst = $value;
  }

  public function setMunObras($value)
  {
    $this->munObras = $value;
  }

  public function setUsoSuelo($value)
  {
    $this->usoSuelo = $value;
  }

  public function setUsoSueloEsp($value)
  {
    $this->usoSueloEsp = $value;
  }

  public function setAgenda($value)
  {
    $this->agenda = $value;
  }

  public function setPrecio($value)
  {
    $this->precio = $value;
  }

  public function setComparativa($value)
  {
    $this->comparativa = $value;
  }

  public function setConstrucciones($value)
  {
    $this->construcciones = $value;
  }

  public function setCertificaciones($value)
  {
    $this->certificaciones = $value;
  }

  public function setAvaluo($value)
  {
    $this->avaluo = $value;
  }

  public function setNotario($value)
  {
    $this->notario = $value;
  }

  public function setProyEscritura($value)
  {
    $this->proyEscritura = $value;
  }

  public function setFechaProyEsc($value)
  {
    if ($this->proyEscritura == 1 || $this->proyEscritura == 2) {
      if ($this->Util()->ValidateRequireField($value, 'Proyecto de Escritura Fecha de envio')) {
        $this->fechaProyEsc = $value;
      }
    }
  }

  public function setProyEscrAprobado($value)
  {
    $this->proyEscrAprobado = $value;
  }

  public function setCalculoIsr($value)
  {
    $this->calculoIsr = $value;
  }

  public function setFechaCalcIsr($value)
  {
    if ($this->calculoIsr == 3) {
      if ($this->Util()->ValidateRequireField($value, 'Calculo ISR Fecha de envio')) {
        $this->fechaCalcIsr = $value;
      }
    }
  }

  public function setCalculoIva($value)
  {
    $this->calculoIva = $value;
  }

  public function setFechaCalcIva($value)
  {
    if ($this->calculoIva == 3) {
      if ($this->Util()->ValidateRequireField($value, 'Calculo IVA Fecha de envio')) {
        $this->fechaCalcIva = $value;
      }
    }
  }

  public function setCheques($value)
  {
    $this->cheques = $value;
  }

  public function setFechaCheques($value)
  {
    if ($this->cheques == 2) {
      if ($this->Util()->ValidateRequireField($value, 'Cheques Fecha de solicitud')) {
        $this->fechaCheques = $value;
      }
    }
  }

  public function setFechaCompVta($value)
  {
    $this->fechaCompVta = $value;
  }

  public function setFechaCVta($value)
  {
    if ($this->fechaCompVta == 'firmado') {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Celebraci&oacute;n CompraVenta')) {
        $this->fechaCVta = $value;
      }
    }
  }

  public function setFechaPagoImp($value)
  {
    $this->fechaPagoImp = $value;
  }

  public function setCalcImpDer($value)
  {
    $this->calcImpDer = $value;
  }

  public function setCopiaCertEsc($value)
  {
    $this->copiaCertEsc = $value;
  }

  public function setAvaluoCatastral($value)
  {
    $this->avaluoCatastral = $value;
  }

  public function setUltPagoPredial($value)
  {
    $this->ultPagoPredial = $value;
  }

  public function setBoletaAgua($value)
  {
    $this->boletaAgua = $value;
  }

  public function setInmProvSubdiv($value)
  {
    $this->inmProvSubdiv = $value;
  }

  public function setFactNotaria($value)
  {
    $this->factNotaria = $value;
  }

  public function setPagoImpWal($value)
  {
    $this->pagoImpWal = $value;
  }

  public function setFechaImpWal($value)
  {
    if ($this->pagoImpWal == 1) {
      if ($this->Util()->ValidateRequireField(
          $value,
          'Fecha Pago de Imp. y Der. por Walmart a la Notaria'
      )) {
        $this->fechaImpWal = $value;
      }
    }
  }

  public function setCompPagoImpNot($value)
  {
    $this->compPagoImpNot = $value;
  }

  public function setFechaImpNot($value)
  {
    if ($this->compPagoImpNot == 1) {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Comprobacion del pago de impuestos')) {
        $this->fechaImpNot = $value;
      }
    }
  }

  public function setPagoHonorarios($value)
  {
    $this->pagoHonorarios = $value;
  }

  public function setFechaHonorarios($value)
  {
    if ($this->pagoHonorarios == 1) {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Pago de Honorarios')) {
        $this->fechaHonorarios = $value;
      }
    }
  }

  public function setPagoPteWal($value)
  {
    $this->pagoPteWal = $value;
  }

  public function setDescPagoPteWal($value)
  {
    $this->descPagoPteWal = $value;
  }

  public function setEscRpp($value)
  {
    $this->escRpp = $value;
  }

  public function setFechaEscRpp($value)
  {
    if ($this->escRpp == 1) {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Escritura entregada a Walmart')) {
        $this->fechaEscRpp = $value;
      }
    }
  }

  public function setFechaCobradoC($value)
  {
    if ($this->cobrado == 1) {
      if ($this->Util()->ValidateRequireField($value, 'Fecha Cobrado')) {
        $this->fechaCobrado = $value;
      }
    }
  }

  public function setFirmaContWal($value)
  {
    $this->firmaContWal = $value;
  }

  /**
  * BuscarContract
  *
  * @param multiple $formValues valores de busqueda del formulario
  *
  * @return Busca los contratos por cliente o razon social
  */

  public function BuscarContractOld($formValues, $activos = false)
  {

    if ($formValues['cliente']) {
      $add="AND customer.nameContact LIKE '%".$formValues['cliente']."%'";
    }

    if ($formValues['razonSocial']) {
      $add="AND contract.nombreComercial LIKE '%".$formValues['razonSocial']."%'";
    }

    if ($formValues['departamentoId']) {
      $depto="AND tipoServicio.departamentoId='".$formValues['departamentoId']."'";
    }

	if($activos)
		$add .= " AND customer.active = '1'";

	if($formValues['facturador'])
		$add .= ' AND contract.facturador = "'.$formValues['facturador'].'"';

	if($formValues['subordinados']){

		if($formValues['respCuenta']){
			$add .= ' AND (personal.jefeSocio = "'.$formValues['respCuenta'].'"
					OR personal.jefeSupervisor = "'.$formValues['respCuenta'].'"
					OR personal.jefeGerente = "'.$formValues['respCuenta'].'"
					OR personal.jefeContador = "'.$formValues['respCuenta'].'"
					OR contract.responsableCuenta = "'.$formValues['respCuenta'].'")';
		}

	}elseif($formValues['respCuenta']){
		$add .= ' AND contract.responsableCuenta = "'.$formValues['respCuenta'].'"';
	}


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
		  LEFT JOIN
		  	personal ON contract.responsableCuenta = personal.personalId
          WHERE
            1 ".$add."
          ORDER BY
            contract.name ASC
          ";
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();

	$contratos = array();
    foreach ($result as $key => $value) {

		//Checamos los permisos de la tabla Contract

		if($formValues['respCuenta']){

			$split = split(',',$value['permisos']);

			$encontrado = false;
			foreach($split as $sp){

				$split2 = split('-',$sp);

				if($split2[0] == $formValues['respCuenta']){
					$encontrado = true;
					break;
				}

			}

			if($encontrado == false)
				continue;

		}//if

     	$sql = "SELECT * FROM servicio
              	LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
              	WHERE contractId = '".$value["contractId"]."'
              	AND servicio.status = 'activo' AND tipoServicio.status='1'
              	".$depto."
              	ORDER BY
                tipoServicio.nombreServicio ASC";
      	$this->Util()->DB()->setQuery($sql);
      	$result[$key]["servicios"] = $this->Util()->DB()->GetResult();
      	$result[$key]["noServicios"] = count($result[$key]["servicios"]);

		//Si no tiene departamento asignado lo borro
      	if ($result[$key]["servicios"][0]['departamentoId'] == "") {
      		unset($result[$key]);
			continue;
      	}

        $showContract = false;

        $user = new User;
        $user->setUserId($value["responsableCuenta"]);
        $userInfo = $user->Info();

		global $User;

      	if ($User["userId"] == $value["responsableCuenta"]
          || $userInfo["jefeContador"] == $User["userId"]
          || $userInfo["jefeSupervisor"] == $User["userId"]
          || $userInfo["jefeGerente"] == $User["userId"]
          || $userInfo["jefeSocio"] == $User["userId"]
      	) {
        	$showContract = true;
      	}

      	if ($showContract === false && $User["roleId"] > 2) {
        	unset($result[$key]);
			continue;
      	}

		$contratos[$key] = $result[$key];

    }//foreach contractOld

    return $contratos;

  }//BuscarContractOld

    private function contratWithPermission($contrato, $respCuenta, $skip){
        $split = split('-',$contrato['permisos']);

        foreach($split as $sp){
            $split2 = split(',',$sp);

            //Se agrego dep 25 que ya no existe
            if($split2[0] == 25) {
                continue;
            }

            if($split2[1] == $respCuenta || $skip){
                return true;
            }
        }
        return false;
    }
    public function BuscarGroupCliente($formValues,$activos=false){
       global $personal;
       global $user;

       if($formValues['cliente'])
		  $sqlFilter = " AND customer.nameContact LIKE '%".$formValues['cliente']."%'";

        if($activos)
            $sqlFilter .= " AND customer.active = '1'";

       $sql = "SELECT contract.*, contract.name AS name,customer.nameContact,customer.customerId
				FROM contract
				LEFT JOIN customer ON customer.customerId = contract.customerId
				WHERE 1 ".$sqlFilter." GROUP BY contract.name ORDER BY customer.nameContact ASC";
        $this->Util()->DB()->setQuery($sql);
        $resContratos = $this->Util()->DB()->GetResult();

        return $resContratos;
    }
	public function BuscarContract($formValues, $activos = false){

		global $personal;
		global $User;

		if($formValues['cliente'])
		  $sqlFilter = " AND customer.nameContact LIKE '%".$formValues['cliente']."%'";

		if($formValues['razonSocial'])
			$sqlFilter = " AND contract.nombreComercial LIKE '%".$formValues['razonSocial']."%'";

		if($formValues['departamentoId'])
		  $sqlDepto = " AND tipoServicio.departamentoId='".$formValues['departamentoId']."'";

		if($activos)
			$sqlFilter .= " AND customer.active = '1'";

		if($formValues['facturador'])
			$sqlFilter .= ' AND contract.facturador = "'.$formValues['facturador'].'"';

		//Contratos Activos
		$sqlFilter .= ' AND contract.activo = "Si"';

		//Si selecciona TODOS los responsables, debe incluir a los subordinados automaticamente.
        $skip = false;
		if($formValues['respCuenta'] == 0){
			$respCuenta = $User['userId'];
			$formValues['subordinados'] = 1;
            //el roleId 4 es de cliente solo deberia poder ver la sus contratos, en otros apartados el roleId 4 es usado como admin(verificarlo)
            if($_SESSION['User']["roleId"] == 4)
            {
                $skip = true;
            }
		}else{
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
				WHERE 1 ".$sqlFilter."
				ORDER BY contract.name ASC";

		$this->Util()->DB()->setQuery($sql);
		$resContratos = $this->Util()->DB()->GetResult();
		$contratos = array();

		foreach($resContratos as $res){
            $encontrado = $this->contratWithPermission($res, $respCuenta, $skip);
			if($encontrado == false) {
                continue;
            }
			//Checamos Servicios
			$sql = "SELECT * FROM servicio
					LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
					WHERE contractId = '".$res["contractId"]."'
					AND servicio.status = 'activo' AND tipoServicio.status='1'
					".$sqlDepto."
					ORDER BY tipoServicio.nombreServicio ASC";
			$this->Util()->DB()->setQuery($sql);
			$res["servicios"] = $this->Util()->DB()->GetResult();
			$res["noServicios"] = count($res["servicios"]);
			//Si no tiene departamento asignado lo borro
			if ($res["servicios"][0]['departamentoId'] == "")
            {
                continue;
            }
			$contratos[] = $res;
		}//foreach
		//INCLUIR SUBORDINADOS
		if(!$formValues['subordinados'])
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
					WHERE 1 ".$sqlFilter."
					ORDER BY contract.name ASC";
			$this->Util()->DB()->setQuery($sql);
			$resContratos = $this->Util()->DB()->GetResult();

		foreach($subordinados as $sub){
			$personalId = $sub['personalId'];

			foreach($resContratos as $res){

                $encontrado = $this->contratWithPermission($res, $personalId, $skip);

                if($encontrado == false) {
                    continue;
                }
				//Checamos Servicios
				$sql = "SELECT * FROM servicio
						LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
						WHERE contractId = '".$res["contractId"]."'
						AND servicio.status = 'activo' AND tipoServicio.status='1'
						".$sqlDepto."
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
		global $User,$rol;
    	if($id){
      		$add = "WHERE contract.customerId = '".$id."'";
    	}

		if($status == 'activos')
			$add .= ' AND contract.activo = "Si"';
		elseif($status == 'inactivos')
			$add .= ' AND contract.activo = "No"';

    	$personal = new Personal;
    	$personal->setPersonalId($User["userId"]);//si se pasa 0 se obtiene todos los subordinados desde socio asta el mas bajo
    	$subordinados = $personal->Subordinados();
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
              ".$add."
            ORDER BY
              contract.name ASC
            ";


    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();
    foreach ($result as $key => $value) {
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
        $result[$key]["noServicios"] = count($serviciosContrato);
        //si no tiene servicios se debe comprobar si se va mostrar o no, de lo contrario tratar sus permisos.
        if ($result[$key]["noServicios"] == 0) {
            $showCliente = false;
            $rol->setRolId($User['roleId']);
            $unlimited = $rol->ValidatePrivilegiosRol(array('gerente', 'supervisor', 'contador', 'auxiliar'), array('Juridico RRHH'));
            if (($showCliente === false && !$unlimited) || ($showCliente === false && $type == "propio")) {
                unset($result[$key]);
            }
        } else {
            $user = new User;
            //sacar el control de permisos del foreach de abajo se puede hacer desde aqui.
            $user->setUserId($value["responsableCuenta"]);
            $userInfo = $user->Info();
            $result[$key]["responsable"] = $userInfo;
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
            //agregar o no agregar servicio a arreglo de contratos?
            foreach ($serviciosContrato as $servicio) {
                $responsableId = $result[$key]['permisos'][$servicio['departamentoId']];
                //comprobar si el rol pertenece al grupo de privilegios avanzados, de lo contrario comprobar si
                // el usuario esta dentro de los permisos del contrato
                $rol->setRolId($User['roleId']);
                $unlimited = $rol->ValidatePrivilegiosRol(array('gerente', 'supervisor', 'contador', 'auxiliar'), array('Juridico RRHH'));
                if ($unlimited) {//para el rol cliente siempre va arrojar que es ilimitado pero solo de sus propios contratos. desde arriba ya viene filtrado por el customerId
                    $result[$key]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
                } else {
                    foreach ($subordinadosPermiso as $usuarioPermiso) {//
                        if (in_array($usuarioPermiso, $conPermiso)) {
                            $result[$key]['instanciasServicio'][$servicio["servicioId"]] = $servicio;
                            break;
                        }//if
                    }//foreach
                }//if
            }//foreach
            if (count($result[$key]['instanciasServicio']) > 0) {
                $showCliente = true;
                $result[$key]["servicios"]++;
            } else {
                unset($result[$key]);
            }

        }
    }
    return $result;
  }

  /**
  * EnumerateByUser
  *
  * @param int $userId id del usuario
  *
  * @return muestra el contrato con respecto al id del usuario
  */
  public function EnumerateByUser($userId)
  {
    $sql = "SELECT
             *
            FROM
              contract
            WHERE
              personalId = ".$userId."
            ORDER BY
              name ASC";

    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();

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
              1 ".$sql."
            ORDER BY
              name ASC";

    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();

    return $result;
  }

  /**
  * Info
  *
  * @return devuelve la informacion de un contrato con respecto a su id
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
          customer.email as email
        FROM
          contract
        LEFT JOIN
          customer ON customer.customerId = contract.customerId
        LEFT JOIN
          regimen ON regimen.regimenId = contract.regimenId
        LEFT JOIN
          sociedad ON sociedad.sociedadId = contract.sociedadId
        WHERE
          contractId = '".$this->contractId."'"
    );
    $row = $this->Util()->DB()->GetRow();
    return $row;
  }

  /**
  * InfoArrendamiento
  *
  * @return Devuelve la informacion de arrendamiento de un contrato
  */
  public function InfoArrendamiento()
  {
    $this->Util()->DB()->setQuery(
        "SELECT
          *
        FROM
          contract_arrendamiento
        WHERE
          contractId = '".$this->contractId."'"
    );
    $row = $this->Util()->DB()->GetRow();
    return $row;
  }

  /**
  * InfoCompraVenta
  *
  * @return devuelve la informacion de compra-venta
  */
  public function InfoCompraVenta()
  {
    $this->Util()->DB()->setQuery(
        "SELECT
          *
        FROM
          contract_compraventa
        WHERE
          contractId = '".$this->contractId."'"
    );
    $row = $this->Util()->DB()->GetRow();
    return $row;
  }

  /**
  * Save
  *
  * @return guarda un nuevo contrato
  */
  public function Save()
  {
      global $User,$log;
    /** if ($this->Util()->PrintErrors()){ return 0; } */

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
          direccionComercial,
          nameContactoAdministrativo,
          emailContactoAdministrativo,
          telefonoContactoAdministrativo,
          nameContactoContabilidad,
          emailContactoContabilidad,
          telefonoContactoContabilidad,
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
          claveIsn
        )
        VALUES
        (
          '".$this->customerId."',
          '".$this->address."',
          '".$this->type."',
          '".$this->sociedadId."',
          '".$this->name."',
          '".$this->regimenId."',
          '".$this->telefono."',
          '".$this->nombreComercial."',
          '".$this->direccionComercial."',
          '".$this->nameContactoAdministrativo."',
          '".$this->emailContactoAdministrativo."',
          '".$this->telefonoContactoAdministrativo."',
          '".$this->nameContactoContabilidad."',
          '".$this->emailContactoContabilidad."',
          '".$this->telefonoContactoContabilidad."',
          '".$this->nameContactoDirectivo."',
          '".$this->emailContactoDirectivo."',
          '".$this->telefonoContactoDirectivo."',
          '".$this->telefonoCelularDirectivo."',
          '".$this->claveCiec."',
          '".$this->claveFiel."',
          '".$this->claveIdse."',
          '".$this->claveSip."',
          '".$this->rfc."',
          '".$this->noExtComercial."',
          '".$this->noIntComercial."',
          '".$this->coloniaComercial."',
          '".$this->municipioComercial."',
          '".$this->estadoComercial."',
          '".$this->noExtAddress."',
          '".$this->noIntAddress."',
          '".$this->coloniaAddress."',
          '".$this->municipioAddress."',
          '".$this->estadoAddress."',
          '".$this->paisAddress."',
          '".$this->cpAddress."',
          '".$this->metodoDePago."',
          '".$this->noCuenta."',
          '".$this->cpComercial."',
          '".$this->encargadoCuenta."',
          '".$this->responsableCuenta."',
          '".$this->permisos."',
          '".$this->auxiliarCuenta."',
          '".$this->facturador."',
          '".$this->claveIsn."')"
    );

    $contractId = $this->Util()->DB()->InsertData();

      $sql = "SELECT * FROM contract WHERE contractId = '".$contractId."'";
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
      $log->Save();
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
				'".$contractId."',
				'".$newData["activo"]."',
				'',
				'".urlencode(serialize($newData))."',
				'".$User["userId"]."'
		);");
      $this->Util()->DB()->InsertData();
    foreach ($_FILES as $key => $file) {
      if ($key == "cerFiel" || $key == "keyFiel" || $key == "reqFiel") {
        $folder = DOC_ROOT."/fieles/";
      }

      if ($key == "cerSellos" || $key == "keySellos" || $key == "reqSellos") {
        $folder = DOC_ROOT."/sellos/";
      }

      if ($key == "idse1" || $key == "idse2" || $key == "idse3") {
        $folder = DOC_ROOT."/idse/";
      }

      $target_path = $folder . basename($_FILES[$key]['name']);

      if (move_uploaded_file($_FILES[$key]['tmp_name'], $target_path)) {
        $this->Util()->DB()->setQuery(
            "UPDATE
              contract
            SET
              ".$key." = '".$target_path."'
            WHERE
              contractId = '".$contractId."'"
        );
        $this->Util()->DB()->UpdateData();
      }

    }
    /** $this->Util()->DB()->InsertData();
      $responsables = $this->getAllResponsables($newData);

      $personal = new Personal;
      $sendmail = new SendMail();
      foreach($responsables as $key => $value)
      {
          $personal->setPersonalId($value);
          $userInfo = $personal->Info();
          $to = $userInfo["email"];
          $to = "comprobantefiscal@braunhuerin.com.mx";
          $toName = $userInfo["name"];
          $body = "Razon social: ".$newData["name"]." fue dada de alta, el alta fue hecha por ".$_SESSION["User"]["username"];
          $subject = $body;
          $sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
          //break;
      }*/
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
      global $User,$log;
    	//if ($this->Util()->PrintErrors()){ return false; }

		//Obtenemos los datos de la BD antes de actualizar para el Log

		$sql = "SELECT * FROM contract WHERE contractId = '".$this->contractId."'";
		$this->Util()->DB()->setQuery($sql);
    	$oldData = $this->Util()->DB()->GetRow();

		//Actualizamos

		$sql = "UPDATE
			  contract
			SET
			  sociedadId = '".$this->sociedadId."',
			  rfc = '".$this->rfc."',
			  type = '".$this->type."',
			  regimenId = '".$this->regimenId."',
			  telefono = '".$this->telefono."',
			  address = '".$this->address."',
			  `name` = '".$this->name."',
			  nombreComercial = '".$this->nombreComercial."',
			  direccionComercial = '".$this->direccionComercial."',
			  nameContactoAdministrativo = '".$this->nameContactoAdministrativo."',
			  emailContactoAdministrativo = '".$this->emailContactoAdministrativo."',
			  telefonoContactoAdministrativo = '".$this->telefonoContactoAdministrativo."',
			  nameContactoContabilidad = '".$this->nameContactoContabilidad."',
			  emailContactoContabilidad = '".$this->emailContactoContabilidad."',
			  telefonoContactoContabilidad = '".$this->telefonoContactoContabilidad."',
			  nameContactoDirectivo = '".$this->nameContactoDirectivo."',
			  emailContactoDirectivo = '".$this->emailContactoDirectivo."',
			  telefonoContactoDirectivo = '".$this->telefonoContactoDirectivo."',
			  telefonoCelularDirectivo = '".$this->telefonoCelularDirectivo."',
			  claveCiec = '".$this->claveCiec."',
			  claveFiel = '".$this->claveFiel."',
			  claveIdse = '".$this->claveIdse."',
			  claveSip = '".$this->claveSip."',
			  noExtComercial = '".$this->noExtComercial."',
			  noIntComercial = '".$this->noIntComercial."',
			  coloniaComercial = '".$this->coloniaComercial."',
			  municipioComercial = '".$this->municipioComercial."',
			  estadoComercial = '".$this->estadoComercial."',
			  noExtAddress = '".$this->noExtAddress."',
			  noIntAddress = '".$this->noIntAddress."',
			  coloniaAddress = '".$this->coloniaAddress."',
			  municipioAddress = '".$this->municipioAddress."',
			  estadoAddress = '".$this->estadoAddress."',
			  paisAddress = '".$this->paisAddress."',
			  cpAddress = '".$this->cpAddress."',
			  metodoDePago = '".$this->metodoDePago."',
			  noCuenta = '".$this->noCuenta."',
			  cpComercial = '".$this->cpComercial."',
			  encargadoCuenta = '".$this->encargadoCuenta."',
                  cobrador = '".$this->cobrador."',
			  responsableCuenta = '".$this->responsableCuenta."',
			  permisos = '".$this->permisos."',
			  auxiliarCuenta = '".$this->auxiliarCuenta."',
			  facturador = '".$this->facturador."',
			  lastModified = '".date("Y-m-d H:i:s")."',
			  modifiedBy = '".$_SESSION["User"]["username"]."',
			  claveIsn = '".$this->claveIsn."'
			WHERE
			  contractId = '".$this->contractId."'";
		$this->Util()->DB()->setQuery($sql);
    	$this->Util()->DB()->UpdateData();

    	$this->Util()->setError(10030, "complete");
    	$this->Util()->PrintErrors();

		//Obtenemos los nuevos datos ya actualizados para el Log

		$sql = "SELECT * FROM contract WHERE contractId = '".$this->contractId."'";
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
				'".$this->contractId."',
				'".$newData["activo"]."',

				'".urlencode(serialize($oldData))."',
				'".urlencode(serialize($newData))."',
				'".$User["userId"]."'
		);");
      $this->Util()->DB()->InsertData();
      /*$subject = "La rason social ".$this->name." fue modificada por ".$_SESSION["User"]["username"];
      $this->Util()->DB()->setQuery("SELECT * FROM personal WHERE personalId = 66 OR personalId = '".IDHUERIN."' OR (tipoPersonal = 'Gerente' && departamentoId = '1')");
      $personal = $this->Util()->DB()->GetResult();
      $sendmail = new SendMail();

      $personal = new Personal();
      $personal->setPersonalId($this->responsableCuenta);
      $responsables = $personal->jefes($this->responsableCuenta, $idList=array());

      foreach($responsables as $key => $value)
      {
          $personal->setPersonalId($this->responsableCuenta);
          $info = $personal->Info();
          //print_r($info);
          $to = $info["email"];
          //$to = "comprobantefiscal@braunhuerin.com.mx";
          $toName = $info["name"];
          $body = "Este correo es para notificarle que hubo una modificacion para la razon social ".$this->name." fue hecha por ".$_SESSION["User"]["username"];
          //$body = "<pre>Datos Nuevos:".print_r($oldData, true)."<br>Datos Anteriores:".print_r($newData, true);
          $sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
          //	break;
      }
      //exit;*/

        $fp = fopen(DOC_ROOT.'/contracts.log','a');
        chmod(DOC_ROOT.'/contracts.log',0756);
		fwrite($fp,"OLD DATA\n");
		fwrite($fp,json_encode($oldData));
		fwrite($fp,"\n\nNEW DATA\n");
		fwrite($fp,json_encode($newData));
		fwrite($fp,"\n\n::::::::::::::::::::::::::::::::\n\n");
		fclose($fp);

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
  * SavePartes
  *
  * @return guarda partes de contratos
  */
  public function SavePartes()
  {

    /** Borramos los documentos anteriores */
    $this->Util()->DB()->setQuery(
        "DELETE FROM
          contract_partes
        WHERE
          contractId = '".$this->contractId."'"
    );
    $this->Util()->DB()->DeleteData();

    /** Insertamos los nuevos documentos */
    foreach ($this->partes as $val) {

      $this->Util()->DB()->setQuery(
          "INSERT INTO
            contract_partes
          (
            contractId,
            name
          )
          VALUES
          (
            '".$this->contractId."',
            '".utf8_decode($val)."'
          )"
      );

      if (trim($val) != '') {
        $this->Util()->DB()->InsertData();
      }

    }/** foreach */

  }

  /**
  * SaveDocsBasic
  *
  * @return Guarda documentos de contratos
  */
  public function SaveDocsBasic()
  {

    /** Borramos los documentos anteriores */
    $this->Util()->DB()->setQuery(
        "DELETE FROM
          contract_docbasic
        WHERE
          contractId = '".$this->contractId."'"
    );
    $this->Util()->DB()->DeleteData();

    /* Insertamos los nuevos documentos */
    foreach ($this->docsBasic as $val) {

      $fecha = '';
      if ($val['fecha']) {
        $fecha = date('Y-m-d', strtotime($val['fecha']));
      }

      $fechaRec = '';
      if ($val['fechaRec']) {
        $fechaRec = date('Y-m-d', strtotime($val['fechaRec']));
      }

      $this->Util()->DB()->setQuery(
          "INSERT INTO
            contract_docbasic
          (
            contractId,
            docBasicId,
            fecha,
            fechaRec,
            aplica,
            descripcion
          )
          VALUES
          (
            '".$this->contractId."',
            '".$val['docBasicId']."',
            '".$fecha."',
            '".$fechaRec."',
            '".$val['aplica']."',
            '".utf8_decode($val['descripcion'])."'
          )"
      );

      $this->Util()->DB()->InsertData();

    }/* foreach */

  }

  /**
  * SaveDocsSellado
  *
  * @return guarda documentos de contratos
  */
  public function SaveDocsSellado()
  {

    /* Borramos los documentos anteriores */
    $this->Util()->DB()->setQuery(
        "DELETE FROM
          contract_docsellado
        WHERE
          contractId = '".$this->contractId."'"
    );
    $this->Util()->DB()->DeleteData();

    /* Insertamos los nuevos documentos */
    foreach ($this->docsSellado as $val) {

      $fecha = '';
      if ($val['fecha']) {
        $fecha = date('Y-m-d', strtotime($val['fecha']));
      }

      $fechaRec = '';
      if ($val['fechaRec']) {
        $fechaRec = date('Y-m-d', strtotime($val['fechaRec']));
      }

      $this->Util()->DB()->setQuery(
          "INSERT INTO
            contract_docsellado
          (
            contractId,
            docSelladoId,
            fecha,
            fechaRec,
            enviado,
            archivo
          )
          VALUES
          (
            '".$this->contractId."',
            '".$val['docSelladoId']."',
            '".$fecha."',
            '".$fechaRec."',
            '".$val['enviado']."',
            '".$val['archivo']."'
          )"
      );

      $this->Util()->DB()->InsertData();

      $origen = DOC_ROOT.'/temp/'.$val['archivo'];
      $destino = DOC_ROOT.'/archivos/'.$val['archivo'];

      if (@copy($origen, $destino)) {
        @unlink($origen);
      }

    }/* foreach */

  }

  /**
  * SaveDocsGral
  *
  * @return guarda documentos en general
  */
  public function SaveDocsGral()
  {

    /* Borramos los documentos anteriores */
    $this->Util()->DB()->setQuery(
        "DELETE FROM
          contract_docgral
        WHERE
          contractId = '".$this->contractId."'"
    );
    $this->Util()->DB()->DeleteData();

    /* Insertamos los nuevos documentos     */
    foreach ($this->docsGral as $key => $val) {

      $fecha = '';
      if ($val['fecha']) {
        $fecha = date('Y-m-d', strtotime($val['fecha']));
      }

      $fechaRec = '';
      if ($val['fechaRec']) {
        $fechaRec = date('Y-m-d', strtotime($val['fechaRec']));
      }

      $this->Util()->DB()->setQuery(
          "INSERT INTO
            contract_docgral
          (
            contractId,
            cartaCump,
            docGralId,
            fecha,
            fechaRec,
            aplica,
            descripcion
          )
          VALUES
          (
            '".$this->contractId."',
            '".$val['cartaCump']."',
            '".$val['docGralId']."',
            '".$fecha."',
            '".$fechaRec."',
            '".$val['aplica']."',
            '".utf8_decode($val['descripcion'])."'
          )"
      );

      $this->Util()->DB()->InsertData();

    }/* foreach */

  }

  /**
  * Delete
  *
  * @return Borra contratos
  */
  public function Delete()
  {
    global $User,$log;
    if ($this->Util()->PrintErrors()) {
      return false;
    }

    $info = $this->Info();
    if ($info["activo"] == 'Si') {
      $active = 'No';
      $complete = "La razon social fue dada de baja correctamente";
    } else {
      $active = 'Si';
      $complete = "La razon social fue dada de alta correctamente";
    }


    $this->Util()->DB()->setQuery(
        "UPDATE
          contract
        SET
          activo = '".$active."'
        WHERE
          contractId = '".$this->contractId."'"
    );
    $this->Util()->DB()->UpdateData();

      $sql = "SELECT * FROM contract WHERE contractId = '".$this->contractId."'";
      $this->Util()->DB()->setQuery($sql);
      $newData = $this->Util()->DB()->GetRow();

      //Guardamos el Log
      $log->setPersonalId($User['userId']);
      $log->setFecha(date('Y-m-d H:i:s'));
      $log->setTabla('contract');
      $log->setTablaId($this->contractId);
      if($active=="Si")
        $log->setAction('Reactivacion');
      elseif($active=='No')
        $log->setAction('Baja');

      $log->setOldValue(serialize($info));
      $log->setNewValue(serialize($newData));
      $log->Save();
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
				'".$this->contractId."',
				'".$newData["activo"]."',

				'".urlencode(serialize($info))."',
				'".urlencode(serialize($newData))."',
				'".$User["userId"]."'
		);");
      $this->Util()->DB()->InsertData();

      $responsables = $this->getAllResponsables($info);

      $personal = new Personal;
      $sendmail = new SendMail();
      foreach($responsables as $key => $value)
      {
          $personal->setPersonalId($value);
          $userInfo = $personal->Info();
          $to = $userInfo["email"];
//          $to = "comprobantefiscal@braunhuerin.com.mx";
          $toName = $userInfo["name"];
          $body = $complete.".Razon social: ".$info["name"]." fue hecha por ".$_SESSION["User"]["username"];
          $subject = $body;
          $sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
//          break;
      }


    $this->Util()->setError(10031, "complete");
    $this->Util()->PrintErrors();
    return true;
  }

    function c($contract)
    {
        $resPermisos = explode('-',$contract['permisos']);
        $personal = new Personal();
        foreach($resPermisos as $res){
            $value = explode(',',$res);

            $idPersonal = $value[1];
            $idDepto = $value[0];

            $personal->setPersonalId($idPersonal);
            $nomPers = $personal->GetNameById();

            $permisos[$idDepto] = $nomPers;
            $permisos2[$idDepto] = $idPersonal;
        }

        $cleanedUp = array();
        foreach($permisos2 as $id)
        {
            $personal->setPersonalId($id);
            $responsables = $personal->jefes($id, $idList=array());

            foreach($responsables as $responsable)
            {
                $cleanedUp[] = $responsable;
            }

            /*$sendmail = new SendMail();
            foreach($responsables as $key => $value)
            {
                $personal->setPersonalId($value);
                $userInfo = $personal->Info();
                $to = $userInfo["email"];
                //$to = "comprobantefiscal@braunhuerin.com.mx";
                $toName = $userInfo["name"];
                $body = $complete.".Razon social: ".$info["name"]." fue hecha por ".$_SESSION["User"]["username"];
                $subject = $body;
                $sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
                //	break;
            }*/
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
              contractId = '.$this->contractId;

    $this->Util()->DB()->setQuery($sql);

    return $this->Util()->DB()->GetSingle();

  }

  /**
  * LoadProrroga
  *
  * @return Obtiene la prorroga de docgral_prorroga
  */
  public function LoadProrroga()
  {

    $_SESSION['prorroga'] = array();

    $sql = 'SELECT
              docGralId
            FROM
              docgral_prorroga
            WHERE
              contractId = '.$this->contractId.'
            GROUP BY
              docGralId';
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();

    foreach ($result as $res) {

      $docGralId = $res['docGralId'];

      $sql = 'SELECT
                fecha
              FROM
                docgral_prorroga
              WHERE
                contractId = '.$this->contractId.'
              AND
                docGralId = '.$docGralId;
      $this->Util()->DB()->setQuery($sql);
      $resFechas = $this->Util()->DB()->GetResult();

      foreach ($resFechas as $val) {
        $_SESSION['prorroga'][$docGralId][] = $val['fecha'];
      }
    }
  }

  /**
  * LoadDocs
  *
  * @return Carga los documentos de un contrato
  */
  public function LoadDocs()
  {

    $_SESSION['docs'] = array();

    $sql = 'SELECT
              docBasicId
            FROM
              docbasic_docs
            WHERE
              contractId = '.$this->contractId.'
            GROUP BY
              docBasicId';
    $this->Util()->DB()->setQuery($sql);
    $result = $this->Util()->DB()->GetResult();

    foreach ($result as $res) {

      $docBasicId = $res['docBasicId'];

      $sql = 'SELECT
                fecha, description, archivo
              FROM
                docbasic_docs
              WHERE
                contractId = '.$this->contractId.' AND docBasicId = '.$docBasicId.'
              ORDER BY
                dbDocId ASC';
      $this->Util()->DB()->setQuery($sql);
      $resDocs = $this->Util()->DB()->GetResult();

      foreach ($resDocs as $val) {

        $card['fecha'] = $val['fecha'];
        $card['desc'] = $val['description'];
        $card['archivo'] = $val['archivo'];
        $card['edit'] = 1;

        $_SESSION['docs'][$docBasicId][] = $card;
      }

    }

  }

  function getLastProrroga($contractId, $docGralId)
  {

    $sql = 'SELECT
              fecha
            FROM
              docgral_prorroga
            WHERE
              contractId = '.$contractId.' AND docGralId = '.$docGralId.'
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
                contractId = "'.$contractId.'"
              AND
                docGralId = "'.$val['docGralId'].'"';
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

        $card['fechaEnt'] = date('d', strtotime($row['fecha'])).
                            ' '.strtoupper($mesEnt).' '.
                            date('Y', strtotime($row['fecha']));
        $fecha = $row['fecha'];

      }

      if ($row['fechaRec'] ) {

        $mesRec = $util->GetMonthByKey(date('n', strtotime($row['fechaRec'])));
        $mesRec = substr($mesRec, 0, 3);

        $card['fechaRec'] = date('d', strtotime($row['fechaRec'])).
                            ' '.strtoupper($mesRec).' '.
                            date('Y', strtotime($row['fechaRec']));
        $fecha = $row['fechaRec'];
      }

      if ($fecha) {
        $mes = $util->GetMonthByKey(date('n', strtotime($fecha)));
        $mes = substr($mes, 0, 3);
        $card['mes'] = $mes.' '.date('y', strtotime($fecha));
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
  * @return devuelve una lista de resultados de busqueda
  */
  public function Suggest($value)
  {

    $this->Util()->DB()->setQuery(
        "SELECT
          contract.*, customer.nameContact
        FROM
          contract
        LEFT JOIN
          customer
        ON
          customer.customerId = contract.customerId
        WHERE
          (contract.name LIKE '%".$value."%' OR
          contract.rfc LIKE '%".$value."%' OR
          customer.nameContact LIKE '%".$value."%')
					AND customer.active = '1'
					AND contract.customerId > 0
        ORDER BY
          customer.nameContact ASC, contract.name ASC
        LIMIT
          10"
    );
    $this->Util()->DB()->query;
    $row = $this->Util()->DB()->GetResult();

    return $row;
  }

  /**
  * UsuariosConPermiso
  *
  * @param string $permisos contiene la cadena de permisos de la DB
  * @param int    $extraId  contiene el id del responsable de la cuenta
  *
  * @return devuelve un arreglo con los persmisos departamento => usuarioAsignado
  */
  public function UsuariosConPermiso($permisos, $extraId)
  {
        $permisos = explode("-", $permisos);

        foreach ($permisos as $permiso) {
            list($depa, $resp) = explode(",", $permiso);

            if($depa == 25) {
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

    public function UpdateComentario($comentario)
    {
        global $User;

        if($this->Util()->PrintErrors()){ return false; }

        $this->Util()->DB()->setQuery("
			UPDATE
				contract
			SET
				`comentario` = '".$comentario."'
			WHERE contractId = '".$this->contractId."'");
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
        $data =$this->Util()->DB()->GetResult();

        return $data;
    }
    public function getTotalIguala(){
      $sql = "SELECT SUM(a.costo) FROM servicio a 
              INNER JOIN tipoServicio b ON a.tipoServicioId=b.tipoServicioId  AND b.status='1' 
              WHERE a.contractId='".$this->contractId."' 
              AND a.status='activo' AND  TO_DAYS(STR_TO_DATE(a.inicioFactura,'%Y-%m-%d')) IS NOT NULL
              AND b.departamentoId IN(1,24)";
      $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
      $single =  $this->Util()->DBSelect($_SESSION['empresaId'])->GetSingle();
      return $single;
    }
    public function getInfoLastContract(){
       $this->Util()->DB()->setQuery('SELECT * FROM contract WHERE customerId="'.$this->customerId.'"  ORDER BY contractId DESC');
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
    public function ValidateEncargados($row=array()){
        $permisos ="";
        $this->Util()->DB()->setQuery('SELECT permisos,contractId from contract WHERE contractId="'.$row[0].'" ');
        $contrato_actual = $this->Util()->DB()->GetRow();
        $dptos=array();
        if(empty($contrato_actual))//||((trim($row[40])==""||trim($row[40])=="--")&&(trim($row[41])==""||trim($row[41])=="--"))
        {
            return false;
        }
        $permisos_actuales = explode("-",$contrato_actual['permisos']);
        foreach($permisos_actuales as $val) {
            $dep = explode(',', $val);
            $dptos[$dep[0]] = $dep[1];
        }
        $deptosNew =array();
        //encontrar id de responsables.
        /*--------------------------------------------------------------------------------------*/
        if(array_key_exists(1,$dptos)&&$dptos[1]>0){
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[1])."' ");
            $respConId =  $this->Util()->DB()->GetSingle();
            if($dptos[1]!=$respConId&&$respConId>0)
                $deptosNew[1] = $respConId;
            else
                $deptosNew[1] =$dptos[1];
        }else{
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[1])."' ");
            $respConId =  $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if($respConId){
                $deptosNew[1] =$respConId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if(array_key_exists(8,$dptos)&&$dptos[8]>0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[2]) . "' ");
            $respNomId = $this->Util()->DB()->GetSingle();
            if ($dptos[8] != $respNomId&&$respNomId>0)
                $deptosNew[8] = $respNomId;
            else
                $deptosNew[8] =$dptos[8];
        }else{
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[2])."' ");
            $respNomId =  $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if($respNomId){
                $deptosNew[8] =$respNomId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if(array_key_exists(21,$dptos)&&$dptos[21]>0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[3]) . "' ");
            $respAdmId = $this->Util()->DB()->GetSingle();
            if ($dptos[21] != $respAdmId&&$respAdmId>0)
                $deptosNew[21] = $respAdmId;
            else
                $deptosNew[21] =$dptos[21];
        }else{
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[3])."' ");
            $respAdmId =  $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if($respAdmId){
                $deptosNew[21]=$respAdmId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if(array_key_exists(22,$dptos)&&$dptos[22]>0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[4]) . "' ");
            $respJurId = $this->Util()->DB()->GetSingle();
            if ($dptos[22] != $respJurId&&$respJurId>0)
                $deptosNew[22] = $respJurId;
            else
                $deptosNew[22] =$dptos[22];
        }else{
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[4])."' ");
            $respJurId =  $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if($respJurId){
                $deptosNew[22]=$respJurId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if(array_key_exists(24,$dptos)&&$dptos[24]>0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[5]) . "' ");
            $respImmId = $this->Util()->DB()->GetSingle();
            if ($dptos[24] != $respImmId&&$respImmId>0)
                $deptosNew[24] = $respImmId;
            else
                $deptosNew[24] =$dptos[24];
        }else{
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[5])."' ");
            $respImmId =  $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if($respImmId){
                $deptosNew[24]=$respImmId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if(array_key_exists(26,$dptos)&&$dptos[26]>0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[6]) . "' ");
            $respMsjId = $this->Util()->DB()->GetSingle();
            if ($dptos[26] != $respMsjId&&$respMsjId>0)
                $deptosNew[26] = $respMsjId;
            else
                $deptosNew[26] =$dptos[26];
        }else{
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[6])."' ");
            $respMsjId =  $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if($respMsjId){
                $deptosNew[26]=$respMsjId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        if(array_key_exists(31,$dptos)&&$dptos[31]>0) {
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='" . trim($row[7]) . "' ");
            $respAudId = $this->Util()->DB()->GetSingle();
            if ($dptos[31] != $respAudId&&$respAudId>0)
                $deptosNew[31] = $respAudId;
            else
                $deptosNew[31] =$dptos[31];
        }else{
            $this->Util()->DB()->setQuery("SELECT personalId FROM personal WHERE name='".trim($row[7])."' ");
            $respAudId =  $this->Util()->DB()->GetSingle();
            //si el responsable existe se agrega
            if($respAudId){
                $deptosNew[31]=$respAudId;
            }
        }
        /*--------------------------------------------------------------------------------------*/
        $per = array();
        foreach($deptosNew as $kp=>$valp){
            $cad= $kp.",".$valp;
            array_push($per,$cad);
        }

        $permisos =implode('-',$per);
        unset($per);
        unset($dptos);
        unset($deptosNew);
        return $permisos;

    }
    function ConcatenarEncargados($row=array()){
        $permisos ="";
        $deptos=array();
        //comprobar que los encargados esten dados de alta siempre y cuando no este vacio
        if($row[32]!="" and $row[32]!="--" ){
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[32]))."'");
            $idCont=$this->Util()->DB()->GetSingle();
            if($idCont)
                $deptos[1]=$idCont;
        }
        if($row[33]!="" and $row[33]!="--" ){
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[33]))."'");
            $idNom=$this->Util()->DB()->GetSingle();
            if($idNom)
                $deptos[8]=$idNom;
        }
        if($row[34]!="" and $row[34]!="--" ){
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[34]))."'");
            $idAdmin=$this->Util()->DB()->GetSingle();
            if($idAdmin)
                $deptos[21]=$idAdmin;
        }
        if($row[35]!="" and $row[35]!="--" ){
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[35]))."'");
            $idJur=$this->Util()->DB()->GetSingle();
            if($idJur)
                $deptos[22]=$idJur;

        }
        if($row[36]!="" and $row[36]!="--" ){
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[36]))."'");
            $idImss=$this->Util()->DB()->GetSingle();
            if($idImss)
                $deptos[24]=$idImss;
        }
        if($row[37]!="" and $row[37]!="--" ){
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[37]))."'");
            $idMen=$this->Util()->DB()->GetSingle();
            if($idMen)
                $deptos[26]=$idMen;
        }
        if($row[38]!="" and $row[38]!="--" ){
            $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[38]))."'");
            $idAud=$this->Util()->DB()->GetSingle();
            if($idAud)
                $deptos[31]=$idAud;
        }

        $permisosArray=array();
        foreach($deptos as $dep=>$per){
            $cad ="";
            $cad =$dep.",".$per;
            $permisosArray[]=$cad;
        }

        $permisos = implode('-',$permisosArray);

        return $permisos;
    }

}

?>