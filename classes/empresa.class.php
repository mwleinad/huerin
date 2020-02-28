<?php

class Empresa extends Main
{
	protected $username;
	private $nombre;
	private $emailPersonal;
	private $celular;
	private $telPersonal;
	private $socioComercial;
	
	private $rfc;
	private $razonSocial;
	private $pais;
	private $calle;
	private $noInt;
	private $noExt;
	private $referencia;
	private $colonia;
	private $localidad;
	private $municipio;
	private $ciudad;
	private $estado;
	private $cp;
	private $regimenFiscal;
	private $email;
	private $password;
	private $productId;
	private $empresaId;
	private $sucursalId;
	private $proveedorId;
	private $socioId;
	private $comprobanteId;
	private $motivoCancelacion;
	private $folios;
	private $telefono;
	private $nameFileCertificado;
	private $nameFileKeyPrivate;
	private $claveFacturador;
	
	public function setNombre($value)
	{
		$this->Util()->ValidateRequireField($value, 'Nombre Completo');
		$this->Util()->ValidateString($value, $max_chars=300, $minChars = 0, 'Nombre Completo');
		$this->nombre = $value;
	}
	
	public function setEmailPersonal($value)
	{
		if($this->Util()->ValidateRequireField($value, 'Email')){
			if($this->Util()->ValidateEmail($value)){
				/*
				$this->Util()->DB()->setQuery("SELECT COUNT(*) FROM usuario WHERE email ='".$value."'");
				if($this->Util()->DB()->GetSingle() > 0)
				{
					$this->Util()->setError(30002, "error", "");
				}
				*/				
				$this->emailPersonal = $value;
			}
		}
	}
	
	public function setSocioComercial($value)
	{
		$this->Util()->ValidateRequireField($value, 'N&uacute;mero de Socio');
		$this->socioComercial = $value;
	}
	
	public function setTelefono($value)
	{
		$this->Util()->ValidateRequireField($value, 'Tel&eacute;fono de la Empresa');
		$this->Util()->ValidateString($value, $max_chars=300, $minChars = 10, "El telefono de la empresa debe de tener Al menos 10 digitos");
		$this->telefono = $value;
	}
	
	public function setTelPersonal($value)
	{
		$this->Util()->ValidateRequireField($value, 'Tel&eacute;fono');
		$this->Util()->ValidateString($value, $max_chars=300, $minChars = 0, "Tel&eacute;fono");
		$this->telPersonal = $value;
	}
	
	public function setCelular($value)
	{
		$this->Util()->ValidateString($value, $max_chars=300, $minChars = 0, "Celular");
		$this->celular = $value;
	}
	
	public function setCondicionPersonal($value)
	{
		if($value == 0)
		{
			$this->Util()->setError(30001, "error", "");			
		}
	}
	
	public function getTelefono()
	{
		return $this->telefono;
	}

	public function setFolios($value)
	{
		$this->Util()->ValidateRequireField($value, 'Folios');
		$this->Util()->ValidateString($value, $max_chars=300, $minChars = 0, "Folios");
		$this->folios = $value;
	}
	
	public function getFolios()
	{
		return $this->folios;
	}

	public function setComprobanteId($value)
	{
		$this->Util()->ValidateString($value, $max_chars=100, $minChars = 1, "ID Comprobante");
		$this->Util()->ValidateInteger($value);
		$this->comprobanteId = $value;
	}
	
	public function getComprobanteId()
	{
		return $this->comprobanteId;
	}
    public function setClaveFacturador($value)
    {
        $this->Util()->ValidateRequireField($value,"Clave facturador");
        $this->claveFacturador = $value;
    }
    public function getClaveFacturador(){
	    return $this->claveFacturador;
    }
	
	public function setMotivoCancelacion($value)
	{
		$this->Util()->ValidateString($value, $max_chars=300, $minChars = 1, "Motivo de Cancelacion");
		$this->motivoCancelacion = $value;
	}
	
	public function getMotivoCancelacion()
	{
		return $this->motivoCancelacion;
	}
	
	public function setProveedorId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->proveedorId = $value;
	}
	
	public function getProveedorId()
	{
		return $this->proveedorId;
	}

	public function setSocioId($value)
	{
		//$this->Util()->ValidateRequireField($value, 'N&uacute;mero de Socio');
		$this->Util()->ValidateInteger($value);
		$this->socioId = $value;
	}
	
	public function getSocioId()
	{
		return $this->socioId;
	}

	public function setEmpresaId($value, $checkIfExists = 0)
	{
	    if($value)
		    $this->empresaId = $value;
	    else
	        $this->empresaId = 21;
	}

	public function getEmpresaId()
	{
		return $this->empresaId;
	}

	public function setRazonSocial($value, $checkIfExists = 0)
	{
		$this->Util()->ValidateRequireField($value, 'Raz&oacute;n Social');
		$this->Util()->ValidateString($value, $max_chars=300, $minChars = 0, "Raz&oacute;n Social");
		$this->razonSocial = $value;
	}

	public function getRazonSocial()
	{
		return $this->razonSocial;
	}
	
	public function setSucursalId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->sucursalId = $value;
	}
	
	public function getSucursalId()
	{
		return $this->sucursalId;
	}

	public function setCalle($value)
	{
		$this->Util()->ValidateRequireField($value, 'Calle');
		$this->calle = $value;
	}
	
	public function getCalle()
	{
		return $this->calle;
	}

	public function setColonia($value)
	{
		$this->Util()->ValidateRequireField($value, 'Colonia');
		$this->colonia = $value;
	}
	
	public function getColonia()
	{
		return $this->colonia;
	}

	public function setReferencia($value)
	{
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "Referencia");
		$this->referencia = $value;
	}
	
	public function getReferencia()
	{
		return $this->referencia;
	}

	public function setMunicipio($value)
	{
		$this->Util()->ValidateRequireField($value, 'Municipio');
		$this->municipio = $value;
	}
	
	public function getMunicipio()
	{
		return $this->municipio;
	}
	
	public function setCiudad($value)
	{
		$this->Util()->ValidateRequireField($value, 'Ciudad');
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "Ciudad");
		$this->ciudad = $value;
	}
	
	public function getCiudad()
	{
		return $this->ciudad;
	}	

	public function setEstado($value)
	{
		$this->Util()->ValidateRequireField($value, 'Estado');
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "Estado");
		$this->estado = $value;
	}
	
	public function getEstado()
	{
		return $this->estado;
	}	

	public function setPais($value)
	{
		$this->Util()->ValidateRequireField($value, 'Pais');
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "Pais");
		$this->pais = $value;
	}

	public function getRegimenFiscal()
	{
		return $this->regimenFiscal;
	}	

	public function setRegimenFiscal($value)
	{
		$this->Util()->ValidateRequireField($value, 'R&eacute;gimen Fiscal');
		$this->regimenFiscal = $value;
	}
	
	public function getPais()
	{
		return $this->pais;
	}	

	public function setNoInt($value)
	{
		$this->Util()->ValidateString($value, $max_chars=255, $minChars = 0, "No. Int.");
		$this->noInt = $value;
	}
	
	public function getNoInt()
	{
		return $this->noInt;
	}	

	public function setNoExt($value)
	{
		$this->Util()->ValidateRequireField($value, 'No. Exterior');
		$this->Util()->ValidateString($value, $max_chars=255, $minChars = 0, "No. Ext.");
		$this->noExt = $value;
	}
	
	public function getNoExt()
	{
		return $this->noExt;
	}	

	public function setLocalidad($value)
	{
		$this->Util()->ValidateRequireField($value, 'Localidad');
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "Localidad");
		$this->localidad = $value;
	}
	
	public function getLocalidad()
	{
		return $this->localidad;
	}	
	
	public function setRfc($value)
	{
		$value = strtoupper($value);
		$this->Util()->ValidateRequireField($value, 'RFC');
		$this->Util()->ValidateString($value, $max_chars=13, $minChars = 0, "RFC");
		$this->rfc = $value;
	}
	
	public function getRfc()
	{
		return $this->rfc;
	}
	
	public function setPassword($value)
	{
		$this->Util()->ValidateRequireField($value, "Contrase&ntilde;a");
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "Contrase&ntilde;a");
		$this->password = $value;
	}
	
	public function getPassword()
	{
		return $this->password;
	}	
	
	public function setEmail($value)
	{
		if($this->Util()->ValidateRequireField($value, 'Correo de Acceso')){
			if($this->Util()->ValidateEmail($value)){				
				$this->Util()->DB()->setQuery("SELECT COUNT(*) FROM usuario WHERE email ='".$value."'");
				if($this->Util()->DB()->GetSingle() > 0)
				{
					$this->Util()->setError(30002, "error", "");
				}				
				$this->email = $value;
			}
		}
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setEmailLogin($value)
	{
		$this->Util()->ValidateMail($value);
		$this->email = $value;
	}

	public function getEmailLogin()
	{
		return $this->email;
	}

	public function setCp($value)
	{
		$this->Util()->ValidateRequireField($value, 'C&oacute;digo Postal');
		$this->Util()->ValidateInteger($value);
		$this->cp = $value;
	}
	
	public function getCp()
	{
		return $this->cp;
	}

	public function setProductId($value)
	{
		$this->Util()->ValidateRequireField($value, 'Producto');
		$this->Util()->ValidateInteger($value);
		$this->productId = $value;
	}
	public function getProductId()
	{
		return $this->productId;
	}
	public function setNameFileCertificado($value){
	    $this->nameFileCertificado = $value;
    }
    public function getNameFileCertificado(){
	    return $this->nameFileCertificado;
    }
    public function setNameFileKeyPrivate($value){
        $this->nameFileKeyPrivate = $value;
    }
    public function getNameFileKeyPrivate(){
        return $this->nameFileKeyPrivate;
    }
	function Register()
	{
		if($this->Util()->PrintErrors()){ return false; }
		
		//connect to general database
		$generalDb = new DB;
		$generalDb->setSqlDatabase("facturas_general");
		
		$year = date("Y");
		$month = date("m");
		
		$month = $month + 1;
		if($month == 13)
		{
			$month = 1;
			$year = $year + 1;
		}
		
		$limite = 10;
		switch($_POST["productId"])
		{
			case "auto": $producto = "auto"; $limite = 0;break;
			case "v3": $producto = "v3";break;
			case "construc": $producto = "construc";break;
			case "pro": $producto = "auto"; $this->folios = 50;break;
		}
		
		$vencimiento = $year."-".$month."-".date("d");
		//inserto la empresa, esta es la principal
		$generalDb->setQuery("
			INSERT INTO `empresa` ( 
				`activo`,
				`activadoEl`,
				`vencimiento`,
				`registerDate`,
				`socioId`,
				`proveedorId`,
				`productId`,
				`limite`,
				`telefono`,
				`version`,
				`nombrePer`,
				`emailPer`,
				`telefonoPer`,
				`celularPer`
				) 
			VALUES (
				'1',
				'".date("Y-m-d")."',
				'".$vencimiento."',
				'".date("Y-m-d")."',
				'".$this->socioId."',
				'".$this->proveedorId."',
				'".$this->productId."',
				'".$limite."',
				'".$this->telefono."',
				'".$producto."',
				'".$this->nombre."',
				'".$this->emailPersonal."',
				'".$this->telPersonal."',
				'".$this->celular."'
				)"
			);
		$empresaId = $generalDb->InsertData();

		//inserto la cantidad de folios
		if($this->folios)
		{
			$generalDb->setQuery("
				INSERT INTO `ventas` (
				`cantidad` ,
				`fecha` ,
				`idSocio` ,
				`idEmpresa` ,
				`status`
				) VALUES (
					'".$this->folios."',  
					'".date("Y-m-d")."',  
					'".$this->socioId."',  
					'".$empresaId."',  
					'noPagado')");
			$generalDb->InsertData();
		}
		
			$generalDb->setQuery("
				INSERT INTO `orden` (
				`fecha` ,
				`idSocio` ,
				`idEmpresa` ,
				`status`
				) VALUES (
					'".date("Y-m-d")."',  
					'".$this->socioId."',  
					'".$empresaId."',  
					'noPagado')");
			$generalDb->InsertData();


		//inserto el usuario por default de la empresa, con privilegios de aministrados
		$generalDb->setQuery("
			INSERT INTO `usuario` ( 
				`empresaId`,
				`email`,
				`type`,
				`main`,
				`password`
				) 
			VALUES (
				'".$empresaId."',
				'".$this->email."',
				'admin',
				'si',
				'".$this->password."')"
			);
		$generalDb->InsertData();

		//creamos la base de datos del usuario en base al id de la empresa generada
		$generalDb->setQuery("CREATE DATABASE IF NOT EXISTS facturas_".$empresaId);
		$generalDb->ExecuteQuery();

		$newDb = new DB;
		$newDb->setSqlDatabase("facturas_".$empresaId);

		//creamos las tablas necesarioas en la nueva base de datos
		include_once(DOC_ROOT."/classes/db_script.php");
		
		//insertamos el rfc principal
		$newDb->SetQuery("
			INSERT INTO `rfc` ( 
				`empresaId`, 
				`rfc`, 
				`razonSocial`, 
				`pais`, 
				`calle`,
				`noInt`, 
				`noExt`, 
				`referencia`, 
				`colonia`, 
				`localidad`, 
				`municipio`, 
				`estado`, 
				`regimenFiscal`, 
				`activo`, 
				`main`, 
				`cp`
				) 
			VALUES (
				'".$empresaId."',
				'".$this->rfc."',
				'".$this->razonSocial."',
				'".$this->pais."',
				'".$this->calle."',
				'".$this->noInt."',
				'".$this->noExt."',
				'".$this->referencia."',
				'".$this->colonia."',
				'".$this->localidad."',
				'".$this->municipio."',
				'".$this->estado."',
				'".$this->regimenFiscal."',
				'si',
				'si',
				'".$this->cp."')"
			);
		$rfcId = $newDb->InsertData();

		//insertamos la sucursal matriz principal para ese RFC
		$newDb->SetQuery("
			INSERT INTO `sucursal` ( 
				`empresaId`, 
				`rfcId`, 
				`identificador`,
				`sucursalActiva`
			) 
			VALUES (
				'".$empresaId."',
				'".$rfcId."',
				'matriz',
				'si'
				)"
			);

		$newDb->InsertData();

		//creamos el folder del usuario
		//echo DOC_ROOT."/empresas/".$empresaId;
		//echo $empresaId;
		@mkdir(DOC_ROOT."/empresas/".$empresaId, 0777);
		@mkdir(DOC_ROOT."/empresas/".$empresaId, 0777);
		
		$this->Util()->setError(30003, "complete", "");
		$this->Util()->PrintErrors();
		return true;

	}

	function DoLogin()
	{
		if($this->Util()->PrintErrors())
		{
			return false;
		}

		$generalDb = new DB;
		$generalDb->setQuery("SELECT COUNT(*) FROM usuario WHERE email = '".$this->email."' AND password = '".$this->password."'");
		$rows = $generalDb->GetSingle();
		if($rows == 0)
		{
			unset($_SESSION["loginKey"]);	
			unset($_SESSION["empresaId"]);	
			$this->Util()->setError(10006, "error");
			if($this->Util()->PrintErrors())
			{
				return false;
			}
		}
		$generalDb->setQuery("SELECT usuario.empresaId, empresa.version, empresa.socioId FROM usuario
			LEFT JOIN empresa ON usuario.empresaId = empresa.empresaId WHERE email = '".$this->email."' AND password = '".$this->password."'");
		
		$login = $generalDb->GetRow();
		$empresaId = $login["empresaId"];
		
		$_SESSION["loginKey"] = $this->email;	
		$_SESSION["empresaId"] = $empresaId;	
		$_SESSION["version"] =  $login["version"];	
		$_SESSION["socioId"] =  $login["socioId"];	
		
		return true;
	}
	
	function CancelarComprobante()
	{
		global $comprobante,$personal;
		if($this->Util()->PrintErrors())
		{
			return false;
		}
		$id_comprobante = $this->comprobanteId;
		$motivo_cancelacion = $this->motivoCancelacion;
		$sqlQuery = "SELECT data, conceptos, userId,serie,folio,
                     CASE tiposComprobanteId
                     WHEN 1 THEN 'de la factura'
                     WHEN 10 THEN 'del complemento de pago'
                     ELSE 'del documento' END AS tipoDocumento,rfcId            
                     FROM comprobante WHERE comprobanteId = ".$id_comprobante;

		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);		
		$row = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		$data = unserialize(urldecode($row['data']));
		$conceptos = unserialize(urldecode($row['conceptos']));
		$_SESSION["conceptos"] = array();
		$_SESSION["conceptos"] = $conceptos;
		$comprobante->setRfcId($row['rfcId']);
		if(!$comprobante->CancelarComprobante($data, $id_comprobante, false, $motivo_cancelacion))
			return false;
		else{
            //enviar notificacion a los encargados de area, supervisor y gerentes.
            $this->Util()->DB()->setQuery("select name from contract where contractId ='".$row["userId"]."' ");
            $razon = $this->Util()->DB()->GetSingle();
            $currentUser =  $personal->getCurrentUser();

            $body = "";
            $subject = "CANCELACION DE ".$row['tipoDocumento']." ".$row['serie'].$row['folio'];
            $body .="<div style='width: 600px;text-align: justify'>";
            $body .="<p>El colaborador ".$currentUser['name']." ha realizado la cancelacion ".$row['tipoDocumento']." con folio ".$row['serie'].$row['folio']." de la razon social $razon </p>";
            $body .="<p>Por el siguiente motivo:</p>";
            $body .="<p><b>".$motivo_cancelacion."</b></p>";
            $body .="</div>";

           $contractRep =  new ContractRep();
		   $contractRep->setContractId($row["userId"]);
		   $ftr["maxLevelRol"] = [4,5,6];
           $ftr["departamentoId"] = [1,21];
		   $ftr["incluirJefes"] = true;
		   $ftr["sendBraun"] = false;
		   $ftr["senHuerin"] = false;
		   $correos = $contractRep->getEmailsEncargadosLevel($ftr);
		   $send = new SendMail();
		   if(!SEND_LOG_MOD){
               $correos = [];
           }
		   $send->PrepareMultipleNotice($subject,$body,$correos,"varios","","","","","noreply@braunhuerin.com.mx","DEP. FACTURACION",true);
		   return true;
        }

	}

	function DoLogout()
	{
		unset($_SESSION["loginKey"]);	
		unset($_SESSION["empresaId"]);
	}

	function IsLoggedIn()
	{
		if($_SESSION["loginKey"])
		{
			$GLOBALS["smarty"]->assign('user', $this->Info());
			return true;
		}
		return false;
	}
	
	function Info($userId = 0)
	{
		$user["empresaId"] = $_SESSION['empresaId'];
		$user["version"] = "v3";

		if(!$user)
		{
			return;
		}

		$this->Util()->DBSelect($user["empresaId"])->setQuery("SELECT COUNT(*) FROM comprobante LIMIT 1");			
		$user["expedidos"] = $this->Util()->DBSelect($user["empresaId"])->GetSingle();

		return $user;
	}
	
	function AuthUser()
	{
		if(!$this->IsLoggedIn())
		{
			$this->Util()->LoadPage('login');
			return;
		}
		
		$empresa = $this->GetEmpresaGeneralInfo($_SESSION["empresaId"]);
		if($empresa["activo"] == 0)
		{
			$this->Util()->LoadPage('activar');
		}
		
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT COUNT(*) FROM comprobante WHERE empresaId =".$_SESSION["empresaId"]);
		$facturas = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();
		
		if($facturas > $empresa["limite"] && $empresa["limite"] > 0)
		{
			$this->Util()->LoadPage('activar');
		}
	}

	function AuthAdmin()
	{
		if(!$this->IsLoggedIn())
		{
			$this->Util()->LoadPage('homepage');
		}
		
		$info = $this->Info();
		if($info["type"] != "admin" && $info["type"] != "moderador")
		{
			$this->Util()->LoadPage('sistema');
		}
	}
	
	function ListSucursales()
	{
	
		$this->Util()->DB()->setQuery("SELECT * FROM sucursal WHERE empresaId = ".$this->empresaId." ORDER BY identificador");
		
		$result = $this->Util()->DB()->GetResult();
		
		foreach($result as $key => $periodo)
		{
		}
		return $result;
	}

	function GetSucursalInfo()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM sucursal WHERE empresaId = ".$this->empresaId." AND sucursalId = ".$this->sucursalId);
		$this->Util()->DBSelect($_SESSION["empresaId"])->query;
		
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		
		return $result;
	}

	public function GetEmpresaGeneralInfo($empresaId)
	{
		$generalDb = new DB;
		$generalDb->setQuery("SELECT * FROM empresa WHERE empresaId = '".$empresaId."'");
		$row = $generalDb->GetRow();	

		return $row;
	}	

	function GetPublicEmpresaInfo()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM rfc LIMIT 1");
		
		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		
		return $result;
	}
    function GetListEmpresas()
    {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM rfc WHERE rfcId!=1 AND activo='si' ");

        $result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();

        return $result;
    }
    function validateFileCertificado(){
        $nombre_archivo = $_FILES['file_certificado']['name'];
        $file = pathinfo($nombre_archivo);
        if($file["extension"] != "cer")
            $this->Util()->setError(0,"error","Certificado invalido");

    }
    function validateFileKey(){
        $nombre_archivo = $_FILES['file_llave']['name'];
        $file = pathinfo($nombre_archivo);
        if($file["extension"] != "key")
            $this->Util()->setError(0,"error","LLave privada invalida");
    }


}//empresa

?>