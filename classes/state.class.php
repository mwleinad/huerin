<?php

class State extends Main
{
	private $stateId;	
	private $name;	
	private $active;
	
	public function setStateId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->stateId = $value;
	}
			
	public function setName($value)
	{
		if($this->Util()->ValidateRequireField($value, "Nombre"))
			$this->Util()->ValidateString($value, $max_chars=60, $minChars = 1, "Nombre");
		
		$this->name = $value;
	}
			
	public function Enumerate()
	{		
								
		$sql = "SELECT 
					* 
				FROM 
					state						
				ORDER BY 
					stateId ASC";
		
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
				
		return $result;
	}
		
	public function Info()
	{
		
		$sql = "SELECT 
					* 
				FROM 
					state 
				WHERE 
					stateId = '".$this->stateId."'";
	
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
					state 
					(										
						name											
					)
				 VALUES 
					(						
						'".utf8_decode($this->name)."'					
					)";
								
		$this->Util()->DB()->setQuery($sqlQuery);
		$stateId = $this->Util()->DB()->InsertData();
						
		$this->Util()->setError(10032, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
	
	public function Update(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "UPDATE 
					state 
				SET 
					name =  '".utf8_decode($this->name)."'									
				WHERE 
					stateId = ".$this->stateId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
						
		$this->Util()->setError(10033, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
	
	public function Delete(){
		
		if($this->Util()->PrintErrors()){ 
			return false; 
		}
		
		$sql = "DELETE FROM 
					state
				WHERE 
					stateId = ".$this->stateId;
							
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->ExecuteQuery();
										
		$this->Util()->setError(10034, "complete");
		$this->Util()->PrintErrors();
		
		return true;
				
	}
		
	public function GetNameById(){
			
		$sql = 'SELECT 
					name
				FROM 
					state 
				WHERE 
					stateId = '.$this->stateId;
		
		$this->Util()->DB()->setQuery($sql);
		
		return $this->Util()->DB()->GetSingle();
		
	}	
	
}//State

?>