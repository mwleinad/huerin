<?php

class Notice extends Main
{
	private $usuario;
	private $description;	
	private $fecha;	
	private $noticeId;
	private $prioridad;
	private $dir;
	
	public function setUsuario($value)
	{
		$this->usuario = $value;
	}
	public function setNoticeId($value)
	{
		$this->noticeId = $value;
	}
	public function setPath($value)
	{
		$this->dir = $value;
	}			
	public function setDescription($value)
	{
		if($this->Util()->ValidateRequireField($value, "Aviso"))
		$this->description = $value;
	}
	public function setPrioridad($value)
	{
		if($this->Util()->ValidateRequireField($value, "Prioridad"))
		$this->prioridad = $value;
	}
	public function setFecha($value)
	{
		$this->fecha = $value;
	}
			
	public function Enumerate()
	{		
	   $this->Util()->DB()->setQuery('SELECT COUNT(*) FROM notice');
		$total = $this->Util()->DB()->GetSingle();

		$pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/homepage");

		$sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
		$this->Util()->DB()->setQuery('SELECT * FROM notice ORDER BY noticeId DESC '.$sql_add);
		$result = $this->Util()->DB()->GetResult();

		
		$data["items"] = $result;
		$data["pages"] = $pages;
		return $data;	
	}
	public function GetLast()
	{		
								
		$sql = "SELECT 
					MAX(noticeId)
				FROM 
					notice";
		
		$this->Util()->DB()->setQuery($sql);
		$single = $this->Util()->DB()->GetSingle();
				
		return $single;
	}
		
	public function Info()
	{
		
		$sql = "SELECT 
					* 
				FROM 
					notice 
				WHERE 
					noticeId = '".$this->noticeId."'";
	
		$this->Util()->DB()->setQuery($sql);
		$info = $this->Util()->DB()->GetRow();
		
		$row = $this->Util->EncodeRow($info);
				
		return $row;
	}
	
	public function Save(){
            
                foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
                    if (array_key_exists($key, $_SERVER) === true){
                        foreach (explode(',', $_SERVER[$key]) as $ip){
                            $ip = trim($ip); // just to be safe

                            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                                echo $ip;
                            }
                        }
                    }
                }
                
                exit(0); 
                 
		if($this->Util()->PrintErrors()){
			return false; 
		}
		
		$sqlQuery = "INSERT INTO 
					notice 
					(
						usuario,										
						fecha,
						description,
						priority											
					)
				 VALUES 
					(			
						'".$this->usuario."',			
						'".$this->fecha."',
						'".$this->description."',
						'".$this->prioridad."'
					)";
								
		$this->Util()->DB()->setQuery($sqlQuery);
		$noticeId = $this->Util()->DB()->InsertData();
		
	   $ruta = DOC_ROOT.'/archivos';
	   $tamano = $_FILES["path"]['size'];
	   $tipo = $_FILES["path"]['type'];
	   $archivo = $_FILES["path"]['name'];
	   $extension = explode(".",$archivo);
		if($_FILES["path"]['name'])
	  {
	     $prefijo = "notice_".$_POST["usuario"].$noticeId;
			 $fileName = $prefijo.".".end($extension);
			 $destino =  $ruta."/".$fileName;
			 move_uploaded_file($_FILES['path']['tmp_name'],$destino);
			 
			 if(move_uploaded_file($_FILES['path']['tmp_name'],$destino))
			 {
					$_SESSION["avisoadd"] = true;
			 }
	  }
						
		$this->Util()->setError(25000, "complete");
		$this->Util()->PrintErrors();
		
		$sqlQuery = "SELECT name, email FROM personal WHERE personalId != '".IDBRAUN."'";
		$this->Util()->DB()->setQuery($sqlQuery);
		$personal = $this->Util()->DB()->GetResult();
		
		$subject = "Has recibido un nuevo aviso del sistema creado por ".$this->usuario;
		$sendmail = new SendMail();
		
		foreach($personal as $key => $usuario)
		{
			$to = $usuario["email"];
			//$to = "mwleinad@yahoo.com";
			$toName = $usuario["name"];
			$body = nl2br(utf8_decode($this->description));
			$body .= "<br><br>El aviso fue creado por ".$this->usuario;
			if($this->dir)
			{
				$body .= "<br><br>El aviso tiene un archivo que puedes descargar dentro del sistema";
			}
			$sendmail->Prepare($subject, $body, $to, $toName, $destino, $fileName, "", "");
			//break;
		}

		$sendmail->Prepare("envio de aviso", "<pre>".print_r($personal, true), "mwleinad@yahoo.com", "daniel");


		$sqlQuery = "SELECT nameContact, email FROM customer";
		$this->Util()->DB()->setQuery($sqlQuery);
		$customer = $this->Util()->DB()->GetResult();
		
		$subject = "Has recibido un nuevo aviso del sistema creado por ".$this->usuario;
		$sendmail = new SendMail();
		
/*		foreach($customer as $key => $cliente)
		{
			$to = $cliente["email"];
			//$to = "mwleinad@yahoo.com";
			$toName = $cliente["name"];
			$body = nl2br(utf8_decode($this->description));
			$sendmail->Prepare($subject, $body, $to, $toName, $destino, $fileName, "", "");
		//	break;
		}

*/
		echo $noticeId;
		return $noticeId;
				
	}
	
	public function Update(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "UPDATE 
					notice 
				SET 
					url =  '".utf8_decode($this->dir)."'									
				WHERE 
					noticeId = ".$this->noticeId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();			
		return true;
				
	}
	
	public function Delete(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "DELETE FROM 
					notice
				WHERE 
					noticeId = ".$this->noticeId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
										
		$this->Util()->setError(21043, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
		
	public function GetNameById(){
			
		$sql = 'SELECT 
					name
				FROM 
					city 
				WHERE 
					cityId = '.$this->cityId;
		
		$this->Util()->DB()->setQuery($sql);
		
		return $this->Util()->DB()->GetSingle();
		
	}	
	
}//Notice

?>