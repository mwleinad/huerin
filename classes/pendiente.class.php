<?php

class Pendiente extends Main
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
		$this->Util()->DB()->setQuery('SELECT pendiente.*, personal.name AS name FROM pendiente 
		LEFT JOIN personal ON personal.personalId = pendiente.responsable
		ORDER BY name ASC, status ASC, pendienteId DESC ');
		$result = $this->Util()->DB()->GetResult();
		
		$personal = new Personal;
		$personal->setPersonalId($_SESSION["User"]["userId"]);
		$subordinados = $personal->Subordinados();
		
		
		$subordinadosPermiso = array();
		array_push($subordinadosPermiso, $_SESSION["User"]["userId"]);
		foreach ($subordinados as $sub) {
    	 array_push($subordinadosPermiso, $sub["personalId"]);
		}
		
		foreach($result as $key => $res)
		{
				$this->Util()->DB()->setQuery("SELECT personalId FROM personal
				WHERE username = '".$res["usuario"]."'");
				$creador = $this->Util()->DB()->GetSingle();
				
				if($creador == $_SESSION["User"]["userId"])
				{
					continue;
				}
				
				if(!in_array($res["responsable"], $subordinadosPermiso))
				{
					unset($result[$key]);
				}
		}
		
		$data["items"] = $result;
		$data["pages"] = $pages;
		return $data;	
	}
	
	public function GetLast()
	{		
								
		$sql = "SELECT 
					MAX(pendienteId)
				FROM 
					pendiente";
		
		$this->Util()->DB()->setQuery($sql);
		$single = $this->Util()->DB()->GetSingle();
				
		return $single;
	}
		
	public function Info()
	{
		
		$sql = "SELECT 
					pendiente.*, personal.name
				FROM 
					pendiente 
					LEFT JOIN personal ON personal.personalId = pendiente.responsable
				WHERE 
					pendienteId = '".$this->noticeId."'";
	
		$this->Util()->DB()->setQuery($sql);
		$info = $this->Util()->DB()->GetRow();
		
		$row = $this->Util->EncodeRow($info);
				
		return $row;
	}

	public function Comentarios()
	{
	
		$sql = "SELECT 
					pendienteComentario.*, personal.name 
				FROM 
					pendienteComentario
				LEFT JOIN personal ON personal.personalId = pendienteComentario.usuario
				WHERE 
					pendienteId = '".$this->noticeId."'";
	
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
		
		return $result;
	}
	
	public function Save(){
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sqlQuery = "INSERT INTO 
					pendiente 
					(
						usuario,										
						responsable,										
						fecha,
						description,
						priority											
					)
				 VALUES 
					(			
						'".$this->usuario."',			
						'".$_POST["responsable"]."',			
						'".$this->fecha."',
						'".$this->description."',
						'".$this->prioridad."'
					)";
								
		$this->Util()->DB()->setQuery($sqlQuery);
		$noticeId = $this->Util()->DB()->InsertData();
		
		$this->Util()->setError(25000, "complete");
		$this->Util()->PrintErrors();

		$sqlQuery = "SELECT personalId FROM personal WHERE username = '".$this->usuario."'";
		$this->Util()->DB()->setQuery($sqlQuery);
		$id = $this->Util()->DB()->GetSingle();
		
		$personal = new Personal;
		$personal->setPersonalId($_POST["responsable"]);
		$info = $personal->Info();

		$ids = $id.",".$_POST["responsable"].",".$info["jefeContador"].",".$info["jefeGerente"].",".$info["jefeSupervisor"];
		
		$sqlQuery = "SELECT name, email FROM personal WHERE personalId IN (".$ids.")";
		$this->Util()->DB()->setQuery($sqlQuery);
		$personal = $this->Util()->DB()->GetResult();
		
		$subject = "Has recibido un nuevo pendiente del sistema creado por ".$this->usuario;
		$sendmail = new SendMail();
		
		foreach($personal as $key => $personal)
		{
			$to = $personal["email"];
			//$to = "comprobantefiscal@braunhuerin.com.mx";
			$toName = $personal["name"];
			$body = nl2br(utf8_decode($this->description));
			$body .= "<br><br>El pendiente fue creado por ".$this->usuario;
			$body .= "<br><br>El pendiente es para por ".$info["name"];
			
			$sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
			//break;
		} 
		
		return $noticeId;
				
	}
	
	public function SaveComentario(){
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sqlQuery = "INSERT INTO 
					pendienteComentario 
					(
						usuario,										
						comentario,										
						fecha,
						pendienteId
					)
				 VALUES 
					(			
						'".$_SESSION["User"]["userId"]."',			
						'".$_POST["comentario"]."',			
						'".date("Y-m-d")."',
						'".$_POST["noticeId"]."'
		)";
								
		$this->Util()->DB()->setQuery($sqlQuery);
		$noticeId = $this->Util()->DB()->InsertData();
		
		$this->setNoticeId($_POST["noticeId"]);
		$pendiente = $this->Info();			
							
		$this->Util()->setError(21043, "complete", "Has comentado exitosamente en este pendiente.");
		$this->Util()->PrintErrors();
		
		$sqlQuery = "SELECT personalId FROM personal WHERE username = '".$pendiente["usuario"]."'";
		$this->Util()->DB()->setQuery($sqlQuery);
		$id = $this->Util()->DB()->GetSingle();
		
		$personal = new Personal;
		$personal->setPersonalId($pendiente["responsable"]);
		$info = $personal->Info();

		$ids = $id.",".$pendiente["responsable"].",".$info["jefeContador"].",".$info["jefeGerente"].",".$info["jefeSupervisor"];
		
		$sqlQuery = "SELECT name, email FROM personal WHERE personalId IN (".$ids.")";
		$this->Util()->DB()->setQuery($sqlQuery);
		$personal = $this->Util()->DB()->GetResult();
		
		$subject = "Has comentado en un pendiente del sistema creado por ".$this->usuario;
		$sendmail = new SendMail();
		
		foreach($personal as $key => $personal)
		{
			$to = $personal["email"];
			//$to = "comprobantefiscal@braunhuerin.com.mx";
			$toName = $personal["name"];
			$body = "Pendiente original:".$pendiente["description"];
			$body .= "<br>:Responsable".$pendiente["name"];
			
			$this->setNoticeId($_POST["noticeId"]);
			$comentarios = $this->Comentarios();
			
			$body .= "<br><br>Historial de Comentarios<br><br>";
			$body .= "<table border='1'>";
			$body .= "<tr><td>Comentario</td><td>Nombre</td><td>Fecha</td></tr>";
			
			foreach($comentarios as $comentario)
			{
				$body .= "<tr><td>".$comentario["comentario"]."</td><td>".$comentario["name"]."</td><td>".$comentario["fecha"]."</td></tr>";
			}
			

			$sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
			//break;
		} 
		
		return true;
				
	}	
	
	public function Update(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "UPDATE 
					pendiente 
				SET 
					url =  '".utf8_decode($this->dir)."'									
				WHERE 
					pendienteId = ".$this->noticeId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();			
		return true;
				
	}
	
	public function Close(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "DELETE FROM pendiente
				WHERE 
					pendienteId = ".$this->noticeId;
					
		$pendiente = $this->Info();			
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
										
		$this->Util()->setError(21043, "complete", "El pendiente fue cerrado exitosamente.");
		$this->Util()->PrintErrors();
		
		$sqlQuery = "SELECT personalId FROM personal WHERE username = '".$pendiente["usuario"]."'";
		$this->Util()->DB()->setQuery($sqlQuery);
		$id = $this->Util()->DB()->GetSingle();
		
		$personal = new Personal;
		$personal->setPersonalId($pendiente["responsable"]);
		$info = $personal->Info();

		$ids = $id.",".$pendiente["responsable"].",".$info["jefeContador"].",".$info["jefeGerente"].",".$info["jefeSupervisor"];
		
		$sqlQuery = "SELECT name, email FROM personal WHERE personalId IN (".$ids.")";
		$this->Util()->DB()->setQuery($sqlQuery);
		$personal = $this->Util()->DB()->GetResult();
		
		$subject = "Has cerrado un pendiente del sistema creado por ".$this->usuario;
		$sendmail = new SendMail();
		
		foreach($personal as $key => $personal)
		{
			$to = $personal["email"];
			//echo $to = "mwleinad@yahoo.com";
			$toName = $personal["name"];
			$body = nl2br(utf8_decode($this->description));
			$sendmail->Prepare($subject, $body, $to, $toName, $destino, "", "", "");
			//break;
		} 
		
		
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
	
}//pendiente

?>