<?php

class ContractSubcategory extends Main
{
	private $contCatId;
	private $contSubcatId;
	private $name;
	private $active;
	private $docGralId;
	
	public function setContCatId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->contCatId = $value;
	}
	
	public function setContSubcatId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->contSubcatId = $value;
	}
			
	public function setName($value)
	{
		if($this->Util()->ValidateRequireField($value, "Nombre"))
			$this->Util()->ValidateString($value, $max_chars=60, $minChars = 1, "Nombre");
		
		$this->name = $value;
	}
		
	public function setDocGralId($value)
	{
		$this->docGralId = $value;		
	}
	
	public function setActive($value)
	{
		$this->active = $value;		
	}
		
	public function Enumerate()
	{
		if($this->active)
			$sqlActive = " AND active = '1'";
						
		$sql = "SELECT 
					* 
				FROM 
					contract_subcategory
				WHERE
					contCatId = ".$this->contCatId."
				".$sqlActive."			
				ORDER BY 
					name ASC";
		
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
				
		return $result;
	}
	
	public function GetTotal()
	{
		if($this->active)
			$sqlActive = " AND active = '1'";
						
		$sql = "SELECT 
					COUNT(*) 
				FROM 
					contract_subcategory
				WHERE
					contCatId = ".$this->contCatId."
				".$sqlActive;
		
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetSingle();
				
		return $result;
	}
		
	public function Info()
	{
		
		$sql = "SELECT 
					* 
				FROM 
					contract_subcategory 
				WHERE 
					contSubcatId = '".$this->contSubcatId."'";
	
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
					contract_subcategory 
					(
						contCatId,										
						name,
						active						
					)
				 VALUES 
					(				
						".$this->contCatId.",		
						'".utf8_decode($this->name)."',					
						'".$this->active."'
					)";
								
		$this->Util()->DB()->setQuery($sqlQuery);
		$contSubcatId = $this->Util()->DB()->InsertData();
						
		$this->Util()->setError(10023, "complete");
		$this->Util()->PrintErrors();
		
		return $contSubcatId;
				
	}
	
	public function SaveDocument(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sqlQuery = "INSERT INTO 
					contract_document 
					(
						contCatId,
						contSubcatId,										
						docGralId					
					)
				 VALUES 
					(				
						".$this->contCatId.",
						".$this->contSubcatId.",
						".$this->docGralId."
					)";
								
		$this->Util()->DB()->setQuery($sqlQuery);
		$this->Util()->DB()->InsertData();
								
		return true;
				
	}
	
	public function Update(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "UPDATE 
					contract_subcategory 
				SET 
					name =  '".utf8_decode($this->name)."',
					active = '".$this->active."'					
				WHERE 
					contSubcatId = ".$this->contSubcatId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
						
		$this->Util()->setError(10024, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
	
	public function Delete(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "DELETE FROM 
					contract_subcategory
				WHERE 
					contSubcatId = ".$this->contSubcatId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
										
		$this->Util()->setError(10025, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
			
	public function GetNameById(){
			
		$sql = 'SELECT 
					name
				FROM 
					contract_subcategory 
				WHERE 
					contSubcatId = '.$this->contSubcatId;
		
		$this->Util()->DB()->setQuery($sql);
		
		return $this->Util()->DB()->GetSingle();
		
	}
	
	public function GetContCatId(){
			
		$sql = 'SELECT 
					contCatId
				FROM 
					contract_subcategory 
				WHERE 
					contSubcatId = '.$this->contSubcatId;
		
		$this->Util()->DB()->setQuery($sql);
		
		return $this->Util()->DB()->GetSingle();
		
	}
		
}//ContractSubcategory


?>