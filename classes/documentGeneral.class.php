<?php

class DocumentGeneral extends Main
{
	private $docGralId;	
	private $name;
	private $info;	
	private $active;
	private $contCatId;
	private $contSubcatId;
	
	public function setDocGralId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->docGralId = $value;
	}
			
	public function setName($value)
	{
		if($this->Util()->ValidateRequireField($value, "Nombre"))
			$this->Util()->ValidateString($value, $max_chars=60, $minChars = 1, "Nombre");
		
		$this->name = $value;
	}
	
	public function setInfo($value)
	{
		$this->Util()->ValidateString($value, $max_chars=60, $minChars = 0, "Informaci&oacute;n requerida");		
		$this->info = $value;
	}
	
	public function setActive($value)
	{
		$this->active = $value;		
	}
	
	public function setContCatId($value)
	{
		$this->contCatId = $value;		
	}
	
	public function setContSubcatId($value)
	{
		$this->contSubcatId = $value;		
	}
		
	public function Enumerate()
	{
		
		if($this->active)
			$sqlActive = " WHERE active = '1'";
						
		$sql = "SELECT 
					* 
				FROM 
					document_general
				".$sqlActive."				
				ORDER BY 
					docGralId ASC";
		
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
				
		return $result;
	}
		
	public function Info()
	{
		
		$sql = "SELECT 
					* 
				FROM 
					document_general 
				WHERE 
					docGralId = '".$this->docGralId."'";
	
		$this->Util()->DB()->setQuery($sql);
		$info = $this->Util()->DB()->GetRow();
		
		$row = $this->Util->EncodeRow($info);
				
		return $row;
	}
	
	public function Save(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sqlQuery = "INSERT INTO 
					document_general 
					(										
						name,
						info,
						active						
					)
				 VALUES 
					(						
						'".utf8_decode($this->name)."',
						'".utf8_decode($this->info)."',
						'".$this->active."'
					)";
								
		$this->Util()->DB()->setQuery($sqlQuery);
		$docGralId = $this->Util()->DB()->InsertData();
						
		$this->Util()->setError(10026, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
	
	public function Update(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "UPDATE 
					document_general 
				SET 
					name =  '".utf8_decode($this->name)."',
					info = '".utf8_decode($this->info)."',
					active = '".$this->active."'					
				WHERE 
					docGralId = ".$this->docGralId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
						
		$this->Util()->setError(10027, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
	
	public function Delete(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "DELETE FROM 
					document_general
				WHERE 
					docGralId = ".$this->docGralId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
										
		$this->Util()->setError(10028, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
		
	public function GetNameById(){
			
		$sql = 'SELECT 
					name
				FROM 
					document_general 
				WHERE 
					docGralId = '.$this->docGralId;
		
		$this->Util()->DB()->setQuery($sql);
		
		return $this->Util()->DB()->GetSingle();
		
	}	
	
	public function GetDocGralByCatId(){
		
		global $docGral;
		
		$sql = 'SELECT 
					docGralId
				FROM 
					contract_document
				WHERE 
					contCatId = '.$this->contCatId.'
				AND
					contSubcatId = '.$this->contSubcatId;
		
		$this->Util()->DB()->setQuery($sql);		
		$result = $this->Util()->DB()->GetResult();
		
		$docs = array();
		foreach($result as $res){
			
			$card = $res;
			
			$docGral->setDocGralId($res['docGralId']);
			$card['name'] = $docGral->GetNameById();
				
			$docs[] = $card;
		}
		
		return $docs;
	}
	
}//DocumentGeneral

?>