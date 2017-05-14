<?php 

class Wallmart extends Main
{
	private $wallmartId;
	private $name;
	private $phone;
	private $email;	
	private $username;
	private $passwd;
	private $active;

	public function setWallmartId($value)
	{
		$this->Util()->ValidateInteger($value);
		$this->wallmartId = $value;
	}

	public function setName($value)
	{
		if($this->Util()->ValidateRequireField($value, "Nombre"))
			$this->Util()->ValidateString($value, $max_chars=60, $minChars = 1, "Nombre");
		$this->name = $value;
	}
	
	public function setPhone($value)
	{
		$this->phone = $value;
	}
	
	public function setEmail($value)
	{
		$this->email = $value;
	}
	
	public function setUsername($value)
	{
		$this->username = $value;
	}
	
	public function setPasswd($value)
	{
		$this->passwd = $value;
	}
		
	public function setActive($value)
	{
		$this->active = $value;		
	}

	public function Enumerate()
	{
		if($this->active)
			$sqlActive = " WHERE active = '1'";
						
		$sql = "SELECT 
					* 
				FROM 
					wallmart
				".$sqlActive."				
				ORDER BY 
					name ASC";
		
		$this->Util()->DB()->setQuery($sql);
		$result = $this->Util()->DB()->GetResult();
				
		return $result;
	}
	
	public function Info()
	{
		$this->Util()->DB()->setQuery("SELECT * FROM wallmart WHERE wallmartId = '".$this->wallmartId."'");
		$row = $this->Util()->DB()->GetRow();
		return $row;
	}

	public function Edit()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			UPDATE
				wallmart
			SET				
				`name` = '".utf8_decode($this->name)."',
				phone = '".utf8_decode($this->phone)."',
				email = '".utf8_decode($this->email)."',
				username = '".utf8_decode($this->username)."',
				passwd = '".utf8_decode($this->passwd)."',				
				active = '".$this->active."'
			WHERE wallmartId = '".$this->wallmartId."'");
		$this->Util()->DB()->UpdateData();

		$this->Util()->setError(10052, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Save()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			INSERT INTO
				wallmart
			(
				`name`,				
				phone,
				email,
				username,
				passwd,				
				active
		)
		VALUES
		(
				'".utf8_decode($this->name)."',				
				'".utf8_decode($this->phone)."',
				'".utf8_decode($this->email)."',
				'".utf8_decode($this->username)."',
				'".utf8_decode($this->passwd)."',				
				'".$this->active."'
		);");
		$this->Util()->DB()->InsertData();
		$this->Util()->setError(10051, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	public function Delete()
	{
		if($this->Util()->PrintErrors()){ return false; }

		$this->Util()->DB()->setQuery("
			DELETE FROM
				wallmart
			WHERE
				wallmartId = '".$this->wallmartId."'");
		$this->Util()->DB()->DeleteData();
				
		$this->Util()->setError(10053, "complete");
		$this->Util()->PrintErrors();
		return true;
	}
	
	public function GetNameById(){
			
		$sql = 'SELECT 
					name
				FROM 
					wallmart 
				WHERE 
					wallmartId = '.$this->wallmartId;
		
		$this->Util()->DB()->setQuery($sql);
		
		return $this->Util()->DB()->GetSingle();
		
	}
	
}

?>