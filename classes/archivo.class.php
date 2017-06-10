<?php 
include_once(DOC_ROOT.'/classes/class.phpmailer.php');
class Archivo extends Contract
{
	private $archivoId;
	private $contractId;
	private $tipoArchivoId;
	private $datef;
	private $path;

	public function setArchivoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->archivoId = $value;
	}

	public function getArchivoId()
	{
		return $this->archivoId;
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

	public function setTipoArchivoId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->tipoArchivoId = $value;
	}

	public function getTipoArchivoId()
	{
		return $this->tipoArchivoId;
	}

	public function setDate($value)
	{
		$this->datef = $value;
	}
	
	public function setPath($value)
	{
		$this->Util()->ValidateString($value, 10000, 1, 'path');
		$this->path = $value;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function EnumerateAll()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM archivo");
		$result = $this->Util()->DB()->GetResult();
		setlocale(LC_TIME, 'spanish');

		foreach($result as $key => $value)
		{
			$diferencia = ceil(abs((strtotime($result[$key]["date"]) - strtotime(date("Y-m-d")))/86400));
			if(strtotime($result[$key]["date"]) < strtotime(date("Y-m-d")))
			{
				$result[$key]["dateColor"] = "#FF0000";
			}
			else
			{
				$result[$key]["dateColor"]= ($diferencia>60)?"#00CC00":"#FFFF00";
			}
			$result[$key]["filePath"] = WEB_ROOT."/archivos/".$value["contractId"]."_".$value["path"];
		}
		return $result;
	}
	
	public function Enumerate()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM archivo 
		LEFT JOIN tipoArchivo ON tipoArchivo.tipoArchivoId = archivo.tipoArchivoId WHERE contractId = '".$this->getContractId()."'ORDER BY archivoId ASC ".$sql_add);
		$result = $this->Util()->DB()->GetResult();
		setlocale(LC_TIME, 'spanish');

		foreach($result as $key => $value)
		{
			$result[$key]["date"] =strftime("%d %B %Y",strtotime($result[$key]["date"]));
			$result[$key]["filePath"] = WEB_ROOT."/archivos/".$value["contractId"]."_".$value["path"];
		}
		return $result;
	}

	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM archivo WHERE archivoId = '".$this->archivoId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function EditFecha()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				archivo
			SET
				`date` = '".date("Y-m-d",strtotime($this->datef))."'
			WHERE archivoId = '".$this->archivoId."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}  
  
	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				archivo
			SET
				`archivoId` = '".$this->archivoId."',
				`contractId` = '".$this->contractId."',
				`tipoArchivoId` = '".$this->tipoArchivoId."',
				`date` = '".$this->datef."',
				`path` = '".$this->path."'
			WHERE archivoId = '".$this->archivoId."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(1, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			INSERT INTO
				archivo
			(
				`contractId`,
				`tipoArchivoId`,
				`date`
		)
		VALUES
		(
				'".$this->contractId."',
				'".$this->tipoArchivoId."',
				'".date("Y-m-d",strtotime($this->datef))."'
		);");
	
		$id =	$this->Util()->DB()->InsertData();
		$folder = DOC_ROOT."/archivos/".$this->getContractId();

		$nombreArchivo = preg_replace("/&#?[a-z0-9]+;/i","", basename( $_FILES["path"]['name']));
		$nombreArchivo = str_replace(" ","", $nombreArchivo);
		
		$target_path = $folder ."_". $nombreArchivo; 
		$target_path_path = $nombreArchivo; 
		
			
		if(move_uploaded_file($_FILES["path"]['tmp_name'], $target_path)) {
			$this->Util()->DB()->setQuery("UPDATE archivo SET path = '".$target_path_path."' WHERE archivoId = '".$id."'");
			$this->Util()->DB()->UpdateData();
		}
		
		$this->Util()->setError(1, "complete", "Has agregado un archivo satisfactoriamente");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				archivo
			WHERE
				archivoId = '".$this->archivoId."'");
		$this->Util()->DB()->DeleteData();
		$this->Util()->setError(1, "complete" , "Has borrado un archivo satisfactoriamente");
		$this->Util()->PrintErrors();
		return true;
	}
	public function GetArchivoTwo()
	{
	  $this->Util()->DB()->setQuery("SELECT a.*,p.name as uname,p.email,cu.nameContact,c.nombreComercial
	  								 FROM archivo as a, personal as p, contract as c, customer as cu
									 WHERE a.contractId = c.contractId
									 AND   cu.customerId =  cu.customerId
									 AND   c.responsableCuenta = p.personalId
									 AND   a.tipoArchivoId = 2
									 ORDER BY a.archivoId DESC");
		$result = $this->Util()->DB()->GetResult();
		/*setlocale(LC_TIME, 'spanish');

		foreach($result as $key => $value)
		{
			$result[$key]["date"] =strftime("%d %B %Y",strtotime($result[$key]["date"]));
			$result[$key]["filePath"] = WEB_ROOT."/archivos/".$value["contractId"]."_".$value["path"];
		}*/
		return $result;   
	
	}
	public function SendAlerta($mail, $name,$contrato,$ncomercial,$cliente,$fecha)
	{
	   
	    $mail = new PHPMailer();
		$mail->SMTPAuth   = true;
		$mail->Host       = "mail.avantika.com.mx";
		$mail->Port       = 587;
		$mail->Username   = "smtp@avantika.com.mx";
		$mail->Password   = "smtp1234";
//		$mail->SMTPSecure="ssl";
		//$mail->SMTPDebug=1;
		
		$body = '<br><br>Estimado Usuario: '.$name.' le comunicamos que la FIEL del
		                 <br><br>contrato '.$contrato.' ('.$ncomercial.') pertenciente al
						  <br><br>cliente : '.$cliente.' , expira a la fecha  de '.$fecha.'';
	
		try{
		$mail->Subject = 'Alerta de FIEL';
		$fromName = "Administrador del Sistema";
		
		$mail->SetFrom(FROM_MAILAlERTA, $fromName);
		
		$mail->MsgHTML($body);
		if(PROJECT_STATUS=="producccion")
		 $mail->AddAddress($email, $name);
		 else
		 {            
		   $mail->AddAddress("ninguno@gmail.com", $name);		   
		 }
		$mail->Send();
		return true;
		
      } catch (phpmailerException $e) {
		 	 return false;
		} catch (Exception $e) {
			 return false;
		}
	}

}

?>