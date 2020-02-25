<?php

class Rfc extends Empresa
{
	private $rfcId;
	
	public function setRfcId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->rfcId = $value;
	}
	
	public function getRfcId()
	{
		return $this->rfcId;
	}
	
	function SetAsActive()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("UPDATE rfc SET activo = 'no' WHERE activo = 'si' AND empresaId = '".$_SESSION["empresaId"]."'");
		//echo $this->Util()->DBSelect($_SESSION["empresaId"])->query;
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
		
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("UPDATE rfc SET activo = 'si' WHERE rfcId = '".$this->rfcId."' AND empresaId = '".$_SESSION["empresaId"]."'");
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();

		return true;
	}
	
	function GetRfcsByEmpresa()
	{
		$this->Util()->DBSelect($this->empresaId)->setQuery("SELECT * FROM rfc WHERE empresaId ='".$this->getEmpresaId()."'");
		//echo $this->Util()->DBSelect($this->empresaId)->query;
		$empresaRfcs = $this->Util()->DBSelect($this->getEmpresaId())->GetResult();
	
		return $empresaRfcs;
	}
	
	function getRfcActive(){
		$id_empresa = $_SESSION['empresaId'];
		$sqlQuery = "SELECT rfcId FROM rfc WHERE activo = 'si' AND empresaId = ".$_SESSION["empresaId"];
		
		$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
		$rfc = $this->Util()->DBSelect($id_empresa)->GetSingle();
		return $rfc;
	}//getRfcActive

    function getCurrentRfc(){
        $id_empresa = $_SESSION['empresaId'];
        $sqlQuery = "SELECT rfc FROM rfc WHERE activo = 'si' AND rfcId = '".$this->getRfcId()."' ";
        $this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
        return $this->Util()->DBSelect($id_empresa)->GetSingle();
    }//getRfcActive

    function InfoRfc()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM rfc WHERE rfcId ='".$this->getRfcId()."'");
		$rfc = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		if(!$rfc)
		    return false;

		$this->setEmpresaId($rfc['empresaId']);
		$rfc["dataCertificate"]  = $this->getDataInfoCertificate();
		return $rfc;
	}
	public function listEmisores(){
        $this->Util()->DB()->setQuery("SELECT * FROM rfc  where activo='si' ORDER BY razonSocial ASC ");
        return $this->Util()->DB()->GetResult();
    }
    public function EnumerateRfc()
    {

        $this->Util()->DB()->setQuery("SELECT COUNT(*) FROM rfc where activo ='si' " );
        $total = $this->Util()->DB()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/admin-folios/emisores");

        $sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
        $this->Util()->DB()->setQuery("SELECT * FROM rfc  where activo='si' ORDER BY razonSocial ASC $sql_add ");
        $result = $this->Util()->DB()->GetResult();
        foreach($result as $key => $var)
        {
            $this->setEmpresaId($var['empresaId']);
            $this->setRfcId($var['rfcId']);
            $result[$key]['dataCertificate'] = $this->getDataInfoCertificate();

        }
        $data["items"] = $result;
        $data["pages"] = $pages;
        return $data;
    }

	function InfoRfcByRfc()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM rfc WHERE rfc ='".$this->rfc."'");
		$rfc = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
	
		return $rfc;
	}
    function InfoRfcByRfc2($rfc)
    {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM rfc WHERE rfc ='".$rfc."'");
        $rfc = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
        return $rfc;
    }

	//let's override the function :P
	public function setRfc($value)
	{
        $value = strtoupper($value);
		$this->Util()->ValidateString($value, $max_chars=13, $minChars = 12, "RFC");
		$ftr = $this->getRfcId() ? " and rfcId !='".$this->getRfcId()."' ":"";
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT COUNT(*) FROM rfc WHERE rfc ='".$value."' $ftr ");
		$rfc = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();
		
		if($rfc && ($value != 'BCO160224ECA' && $value != 'BHU120320CQ1' && $value != 'BABJ701019LD7'))
		{
			return $this->Util()->setError(10042, "error", "Error en el RFC, es posible que ya se encuentre registrado");
		}
		$this->rfc = $value;
	}

	public function setRfcDelete($value)
	{
		$this->Util()->ValidateString($value, $max_chars=13, $minChars = 12, "RFC");
		$this->rfc = $value;
	}

	function AddRfc()
	{
		//check limite
		/*$this->Util()->DB()->setQuery("SELECT * FROM empresa WHERE empresaId = ".$_SESSION["empresaId"]);
		$empresa = $this->Util()->DB()->GetRow();

		//check limite
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT COUNT(*) FROM rfc");
		echo $rfcs = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();

		if($rfcs >= $empresa["limiteRfcs"])
		{
			$this->Util()->setError(10044, "error");
		}*/

		if($this->Util()->PrintErrors()){ return false; }
		
		$this->setEmpresaId($_SESSION["empresaId"]);
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
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
				`ciudad`, 
				`estado`, 
				`activo`, 
				`cp`,
			    `claveFacturador`
				) 
			VALUES (
				'".$this->getEmpresaId()."',
				'".$this->rfc."',
				'".$this->getRazonSocial()."',
				'".$this->getPais()."',
				'".$this->getCalle()."',
				'".$this->getNoInt()."',
				'".$this->getNoExt()."',
				'".$this->getReferencia()."',
				'".$this->getColonia()."',
				'".$this->getCiudad()."',
				'".$this->getCiudad()."',
				'".$this->getCiudad()."',
				'".$this->getEstado()."',
				'si',
				'".$this->getCp()."',
				'".$this->getClaveFacturador()."')"

			);
		$rfcId = $this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();

		//sucursal por defecto == matriz
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
			INSERT INTO `sucursal` ( 
				`empresaId`, 
				`rfcId`, 
				`identificador`,
				`sucursalActiva`,
				`pais`, 
				`calle`,
				`noInt`, 
				`noExt`, 
				`referencia`, 
				`colonia`, 
				`localidad`, 
				`municipio`,
				`ciudad`, 
				`estado`,				 
				`cp`
				) 
			VALUES (
				'".$this->getEmpresaId()."',
				'".$rfcId."',
				'Matriz',
				'si',
				'".$this->getPais()."',
				'".$this->getCalle()."',
				'".$this->getNoInt()."',
				'".$this->getNoExt()."',
				'".$this->getReferencia()."',
				'".$this->getColonia()."',
				'".$this->getCiudad()."',
				'".$this->getCiudad()."',
				'".$this->getCiudad()."',
				'".$this->getEstado()."',
				'".$this->getCp()."'
				)"
			);
		$this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();

		$this->Util()->setError(0, "complete","Emisor guardado correctamente");
		$this->Util()->PrintErrors();
		return true;
	}
	function EditRfc()
	{
		if($this->Util()->PrintErrors()){ return false; }
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
			UPDATE `rfc` SET 
				`empresaId` = '".$this->getEmpresaId()."', 
				`rfc` = '".$this->rfc."', 
				`razonSocial` = '".$this->getRazonSocial()."', 
				`pais` = '".$this->getPais()."', 
				`calle` = '".$this->getCalle()."',
				`noInt` = '".$this->getNoInt()."', 
				`noExt` = '".$this->getNoExt()."', 
				`referencia` = '".$this->getReferencia()."', 
				`colonia` = '".$this->getColonia()."', 
				`localidad` = '".$this->getCiudad()."', 
				`municipio` = '".$this->getCiudad()."',
				`regimenFiscal` = '".$this->getRegimenFiscal()."',
				`ciudad` = '".$this->getCiudad()."', 
				`estado` = '".$this->getEstado()."', 
				`cp` = '".$this->getCp()."' 
			WHERE rfcId = '".$this->rfcId."' "
			);
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
		$this->Util()->setError(20007, "complete", "Datos actualizados correctamente");
		$this->Util()->PrintErrors();
		return true;
	}


	function DeleteRfc()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("update rfc set activo = 'no' WHERE rfcId = '".$this->getRfcId()."' AND main='no'");
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
				
		$this->Util()->setError(20006, "complete","Se ha realizado la baja correctamente");
		$this->Util()->PrintErrors();
		return true;
	}
	
	function GetCertificadoByRfc(){
	
		$ruta_dir = DOC_ROOT.'/empresas/'.$this->getEmpresaId().'/certificados/'.$this->getRfcId();
	    $nom_certificado = "";
		if(is_dir($ruta_dir)){
			if($gd = opendir($ruta_dir)){
				while($archivo = readdir($gd)){				
					$info = pathinfo($ruta_dir.'/'.$archivo);
					if($info['extension'] == 'cer'){
						$nom_certificado = $info['filename'];						
						break;
					}//if
				}//while
				closedir($gd);
			}//if
		}//if

		$this->setNameFileCertificado($nom_certificado);
		return $nom_certificado;
		
	}
    function processCertificate(){
	    global  $comprobante;
	    if($this->Util()->PrintErrors())
	        return false;


        $ruta_dir = DOC_ROOT.'/empresas/'.$this->getEmpresaId().'/certificados/'.$this->getRfcId();
        $cadenaOriginal = 'abcdefghijklmnopqrstuvwxyz';
        $md5 = md5($cadenaOriginal);

        if(!is_dir($ruta_dir)){
            mkdir($ruta_dir,0755,true);
        }
        $this->cleanDirEmpresaRfc($ruta_dir);
        $cer_name = $_FILES['file_certificado']['name'];
        move_uploaded_file($_FILES['file_certificado']['tmp_name'], $ruta_dir."/".$cer_name);

        $llave_name = $_FILES['file_llave']['name'];
        move_uploaded_file($_FILES['file_llave']['tmp_name'], $ruta_dir."/".$llave_name);

        $comprobante->GenerarSelloGral($cadenaOriginal, $md5, $llave_name, $cer_name, $this->getPassword(), $this->getRfcId());

        $ruta_verified = $ruta_dir.'/verified.txt';
        if(file_exists($ruta_verified))
            $status = file_get_contents($ruta_verified);

        $res =  false;
        if(trim($status) == 'Verified OK'){
            $myFile = $ruta_dir."/password.txt";
            $fh = fopen($myFile, 'w');
            $stringData = $this->getPassword();
            fwrite($fh, $stringData);
            $certNuevo = $this->Util()->GetNoCertificado($ruta_dir, $cer_name);
            $sqlQuery = "UPDATE serie SET noCertificado = '$certNuevo' WHERE  rfcId = '".$this->getRfcId()."' ";
            $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
            $this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
            $this->Util()->setError(0,"complete","El certificado se actualizo correctamente.");
            $res = true;
        }else{
            $this->cleanDirEmpresaRfc($ruta_dir);
            $this->Util()->setError(0,"error","Hubo un error al actualizar el certificado.");
            $res = false;
        }//else
        $this->Util()->PrintErrors();
        return $res;
    }
    public function getDataInfoCertificate()
    {
        $this->GetCertificadoByRfc();
        $ruta_dir = DOC_ROOT . '/empresas/' . $this->getEmpresaId() . '/certificados/' . $this->getRfcId();
        if(is_file($ruta_dir . '/' . $this->getNameFileCertificado().".cer")){
            $expire = exec('openssl x509 -noout -in ' . $ruta_dir . '/' . $this->getNameFileCertificado() . '.cer.pem -dates');
            $exp = explode('=', $expire);
            $fecha_expiracion = $exp[1];
            $data['expireDate'] = date('d-m-Y g:i:s a', strtotime($fecha_expiracion));
            $data['noCertificado'] = $this->Util()->GetNoCertificado($ruta_dir,$this->getNameFileCertificado());
            return $data;
        }
        return false;
    }
    function cleanDirEmpresaRfc($ruta_dir = ""){
        if(is_dir($ruta_dir)){
            if($gd = opendir($ruta_dir)){
                while($archivo = readdir($gd)){
                    @unlink($ruta_dir.'/'.$archivo);
                }//while
                closedir($gd);
            }//if
        }//if
    }
}
?>