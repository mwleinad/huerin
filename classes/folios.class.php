<?php

class Folios extends Empresa{

	private $id_serie;
	private $id_empresa;
	private $id_rfc;
	private $serie;
	private $folio_inicial;
	private $folio_final;
	private $no_aprobacion;
	private $anio_aprobacion;
	private $comprobante;
	private $lugar_expedicion;
	private $no_certificado;
	private $email;
	private $processLogo;
	
	public function setIdSerie($value){
        $this->Util()->ValidateInteger($value);
		$this->id_serie = $value;
	}
	
	public function setIdEmpresa($value){
        $this->Util()->ValidateInteger($value);
		$this->id_empresa = $value;
	}
	public function setProcessLogo($value){
	    $this->processLogo =  $value;
    }
    public function getProcessLogo(){
        return $this->processLogo;
    }
	
	public function setIdRfc($value){
        $this->Util()->ValidateInteger($value);
		$this->id_rfc = $value;
	}
	
	public function setSerie($value){
		$this->Util()->ValidateString($value, $max_chars=255, $minChars = 0, 'Serie');
		$this->serie = $value;
	}
	
	public function setFolioInicial($value){
		$this->Util()->ValidateOnlyNumeric($value,"Folio inicial");
		$this->Util()->ValidateNumericWhitRange($value,1,0,"Folio inicial");
		$this->folio_inicial = $value;
	}
	
	public function setFolioFinal($value){
        $this->Util()->ValidateOnlyNumeric($value,"Folio inicial");
        $this->Util()->ValidateNumericWhitRange($value,$this->folio_inicial,0,"Folio inicial");
		$this->folio_final = $value;
	}
	
	public function setNoAprobacion($value){
		$this->Util()->ValidateString($value, $max_chars=255, $minChars = 0, 'No de Aprobacion');
		$this->Util()->ValidateInteger($value);
		$this->no_aprobacion = $value;
	}
	
	public function setAnioAprobacion($value){
		$this->Util()->ValidateString($value, $max_chars=255, $minChars = 0, 'Ano de Aprobacion');
		$this->Util()->ValidateInteger($value);
		$this->anio_aprobacion = $value;
	}
	
	public function setComprobante($value){
        $this->Util()->ValidateRequireField($value,"Tip de comprobante");
		$this->Util()->ValidateInteger($value);
		$this->comprobante = $value;
	}
	
	public function setLugarExpedicion($value){
		$this->Util()->ValidateInteger($value);
		$this->lugar_expedicion = $value;
	}
	
	public function setNoCertificado($value){
		$this->Util()->ValidateRequireField($value,"Numero de certificado");
		$this->no_certificado = $value;
	}
	
	public function setEmail($value){
		$value = $this->Util()->DecodeVal($value);
		$this->Util()->ValidateMail($value);
		$this->email = $value;
	}
	
	/* Return values Var */
	
	public function getIdSerie(){
		return $this->id_serie;
	}
	
	public function getIdEmpresa(){
		return $this->id_empresa;
	}
	
	public function getIdRfc(){
		return $this->id_rfc;
	}
	
	public function getSerie(){
		return $this->serie;
	}
	
	public function getFolioInicial(){
		return $this->folio_inicial;
	}
	
	public function getFolioFinal(){
		return $this->folio_final;
	}
	
	public function getNoAprobacion(){
		return $this->no_aprobacion;
	}
	
	public function getAnioAprobacion(){		
		return $this->anio_aprobacion;
	}
	
	public function getComprobante(){		
		return $this->comprobante;
	}
	
	public function getLugarExpedicion(){		
		return $this->lugar_expedicion;
	}
	
	public function getNoCertificado(){
		return $this->no_certificado;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	function AddFolios(){
	
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		if($this->VerifyFolios()){
			return false;
		}
		
		$this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("
			INSERT INTO `serie` (
				`empresaId`,
				`rfcId`,
				`sucursalId`, 
				`serie`, 
				`folioInicial`, 
				`folioFinal`, 
				`noAprobacion`, 
				`anoAprobacion`,
				`tiposComprobanteId`, 
				`lugarDeExpedicion`, 
				`noCertificado`, 
				`email`,
				`consecutivo`			
				) 
			VALUES (
				'".$this->getIdEmpresa()."',
				'".$this->getIdRfc()."',
				'".$this->getLugarExpedicion()."',
				'".$this->getSerie()."',
				'".$this->getFolioInicial()."',
				'".$this->getFolioFinal()."',
				'".$this->getNoAprobacion()."',
				'".$this->getAnioAprobacion()."',
				'".$this->getComprobante()."',
				'".$this->getLugarExpedicion()."',
				'".$this->getNoCertificado()."',
				'".$this->getEmail()."',
				'".$this->getFolioInicial()."')"
			);
		
		$id_serie = $this->Util()->DBSelect($_SESSION['empresaId'])->InsertData();
		$this->setIdSerie($id_serie);
		$this->saveLogo();
		$this->Util()->setError(20011, 'complete');
		$this->Util()->PrintErrors();

		return true;
	}//AddFolios
	
	function VerifyFolios(){
		
		$serieId = $this->getIdSerie();
		if(!empty($serieId))
			$sqlSerie = ' AND serieId <> '.$serieId;
		
		$sqlQuery = "SELECT 
						*
					FROM
						serie
					WHERE						
						`rfcId` = '".$this->getIdRfc()."'
					AND	
						`sucursalId` = '".$this->getLugarExpedicion()."' 
					AND	
						`serie` =  '".$this->getSerie()."'					
					AND	
						`noAprobacion` =  '".$this->getNoAprobacion()."'
					AND	
						`anoAprobacion` = '".$this->getAnioAprobacion()."'
					AND	
						`tiposComprobanteId` =  '".$this->getComprobante()."'
					AND	
						`lugarDeExpedicion` =  '".$this->getLugarExpedicion()."'
					AND	
						`noCertificado` =  '".$this->getNoCertificado()."'							
						".$sqlSerie;
		
		$this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sqlQuery);	
		$result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();		
		
		foreach($result as $row){
			
			$folioInicial = $row['folioInicial'];
			$folioFinal = $row['folioFinal'];
			
			$fi = $this->folio_inicial;
			$ff = $this->folio_final;
			//Checamos si esta dentro del rango de folios ya agregados
			if($this->inRange($fi, $folioInicial, $folioFinal) || $this->inRange($ff, $folioInicial, $folioFinal)){
				$this->Util()->setError(20026, 'error');	
				$this->Util()->PrintErrors();
				return true;			
			}//if
			
		}
							
		return false;
		
	}
	
	function GetFoliosByEmpresa(){
				
		$sqlQuery = 'SELECT * FROM serie';
		
		$id_empresa = $_SESSION['empresaId'];
		
		$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
		$folios = $this->Util()->DBSelect($id_empresa)->GetResult();
		
		foreach($folios as $key => $folios)
		{
			$qr = "";
			$ruta_dir = DOC_ROOT.'/empresas/'.$id_empresa.'/qrs';
			$ruta_web_dir = WEB_ROOT.'/empresas/'.$id_empresa.'/qrs';
			if(is_dir($ruta_dir)){
				if($gd = opendir($ruta_dir)){
					while($archivo = readdir($gd)){
						$serie = explode("_", $archivo);
						print_r($serie);
						if($serie[0] == $_POST['id_serie'])
						{
							$qr = $ruta_web_dir.'/'.$archivo;
							break;
						}
						else
						{
							$serie = explode(".", $serie[0]);
							if($serie[0] == $folio['serieId'])
							{
								$qr = $ruta_web_dir.'/'.$archivo;
								break;
							}
						}
					}//while
					closedir($gd);
				}//if
			}//if
			$folios[$key]["qr"] = $qr;

			$folios[$key]["logo"] = DOC_ROOT."/empresas/15/qrs/".$folio["serieId"].".jpg";
			
			if(file_exists($folios[$key]["logo"]))
			{
				$folios[$key]["logo"] = WEB_ROOT."/empresas/15/qrs/".$folio["serieId"].".jpg";
			}
			else
			{
				$folios[$key]["logo"] = "";
			}
			
		}
		print_r($folios);

		return $folios;
		
	}//GetFoliosByEmpresa
	
	function GetFoliosByRfc(){
				
		$sqlQuery = "SELECT a.*,b.razonSocial FROM serie a
                        INNER JOIN rfc b ON a.rfcId=b.rfcId WHERE  a.rfcId = '".$this->getIdRfc()."'  ";
		$id_empresa = $_SESSION['empresaId'];
		$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
		$folios = $this->Util()->DBSelect($id_empresa)->GetResult();
		foreach($folios as $key => $folio)
		{
			$qr = "";
			$id_empresa = $folio['empresaId'];
			$ruta_dir = DOC_ROOT.'/empresas/'.$id_empresa.'/qrs';
			$ruta_web_dir = WEB_ROOT.'/empresas/'.$id_empresa.'/qrs';
			if(is_dir($ruta_dir)){
				if($gd = opendir($ruta_dir)){
					while($archivo = readdir($gd)){
						$serie = explode("_", $archivo);
						if($serie[0] == $folio['serieId'])
						{
							$qr = $ruta_web_dir.'/'.$archivo;
							break;
						}
						else
						{
							$serie = explode(".", $serie[0]);
							if($serie[0] == $folio['serieId'])
							{
								$qr = $ruta_web_dir.'/'.$archivo;
								break;
							}
						}
					}//while
					closedir($gd);
				}//if
			}//if
			$folios[$key]["qr"] = $qr;

			$folios[$key]["logo"] = DOC_ROOT."/empresas/".$id_empresa."/qrs/".$folio["serieId"].".jpg";
			
			if(file_exists($folios[$key]["logo"]))
			{
				$folios[$key]["logo"] = WEB_ROOT."/empresas/".$id_empresa."/qrs/".$folio["serieId"].".jpg";
			}
			else
			{
				$folios[$key]["logo"] = "";
			}
																	 

		}
		return $folios;
	}//GetFoliosByRfc

	function EditFolios(){
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		if($this->VerifyFolios()){
			return false;
		}
		
		$this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("
			UPDATE `serie` SET 
				`empresaId` = '".$this->getIdEmpresa()."',
				`rfcId` = '".$this->getIdRfc()."', 
				`serie` = '".$this->getSerie()."', 
				`folioInicial` = '".$this->getFolioInicial()."', 
				`folioFinal` = '".$this->getFolioFinal()."', 
				`noAprobacion` = '".$this->getNoAprobacion()."',
				`anoAprobacion` = '".$this->getAnioAprobacion()."', 
				`tiposComprobanteId` = '".$this->getComprobante()."', 
				`lugarDeExpedicion` = '".$this->getLugarExpedicion()."', 
				`noCertificado` = '".$this->getNoCertificado()."', 
				`email` = '".$this->getEmail()."'				
			WHERE serieId = '".$this->getIdSerie()."'"
			);
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
		$this->saveLogo();
		$this->Util()->setError(20013, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	function DeleteFolios(){
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("DELETE FROM serie WHERE serieId = '".$this->id_serie."'");
		$this->Util()->DBSelect($_SESSION["empresaId"])->DeleteData();
		$this->Util()->setError(20012, "complete");
		$this->Util()->PrintErrors();
		return true;
	}
	
	public function setFoliosDelete($value){
		$this->Util()->ValidateString($value, $max_chars=13, $minChars = 1, "Folios");
		$this->id_serie = $value;
	}
	
	function getInfoFolios(){
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM serie WHERE serieId ='".$this->getIdSerie()."'");
		$info = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
	
		return $info;
	}
	
	function inRange($number, $folioInicial, $folioFinal){
		
		if($number >= $folioInicial && $number <= $folioFinal)
			return true;
		else
			return false;
		
	}
    function validateFileLogo(){
        if(!isset($_FILES["file_logo"]))
            return false;

        if($_FILES["file_logo"]["error"]!==0)
            return false;

        $nombre_archivo = $_FILES['file_logo']['name'];
        $file = pathinfo($nombre_archivo);
        if($file["extension"] != "jpg"){
            $this->Util()->setError(0,"error","El archivo adjunto de logo es invalido.(solo se acepta jpg)");
        }
        $this->setProcessLogo(true);

    }
    function saveLogo(){
	    if($this->getProcessLogo()){
            $ruta_dir = DOC_ROOT.'/empresas/'.$this->getIdEmpresa().'/qrs';
            $file = basename( $_FILES['file_logo']['name']);
            if(!is_dir($ruta_dir))
                mkdir($ruta_dir, 0755, true);
            $ext = strtolower(substr($file, strrpos($file, '.') + 1));
            $file = '/'.$this->getIdSerie().".".$ext;
            if(is_file($ruta_dir.$file))
                unlink($ruta_dir.$file);
            move_uploaded_file($_FILES['file_logo']['tmp_name'], $ruta_dir.$file);

        }

    }
	
}//Folios


?>