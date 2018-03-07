<?php

class Log extends Util
{
	private $personalId;
	private $fecha;
	private $tabla;
	private $tablaId;
	private $action;
	private $oldValue;
	private $newValue;
	
	public function setPersonalId($value){
		$this->Util()->ValidateInteger($value);
		$this->personalId = $value;
	}
	
	public function setFecha($value){
		$this->fecha = $value;		
	}
	
	public function setTabla($value){
		$this->tabla = $value;		
	}
	
	public function setTablaId($value){
		$this->tablaId = $value;		
	}
	
	public function setAction($value){
		$this->action = $value;		
	}
	
	public function setOldValue($value){
		$this->oldValue = $value;		
	}
	
	public function setNewValue($value){
		$this->newValue = $value;		
	}
	
	public function Save(){
				
		$sql = "INSERT INTO log (personalId, fecha, tabla, tablaId, action, oldValue, newValue)
				 VALUES ('".$this->personalId."', '".$this->fecha."', '".$this->tabla."', '".$this->tablaId."',
				 '".$this->action."', '".$this->oldValue."', '".$this->newValue."')";								
		$this->Util()->DB()->setQuery($sql);
		$this->Util()->DB()->InsertData();
	
		return true;				
	}
	
  	function GetLog(){
	
    	$this->Util()->DB()->setQuery(
        "SELECT
          comprobante.comprobanteId,comprobante.userId,comprobante.fecha, personal.name
        FROM
          comprobante
        LEFT JOIN 
          instanciaServicio ON instanciaServicio.comprobanteId = comprobante.comprobanteId
        LEFT JOIN
          personal ON personal.personalId=comprobante.userId
        ORDER BY
          comprobante.fecha DESC
        LIMIT 
          0 , 1000"
    	);
    	$idsUsers = $this->Util()->DB()->GetResult();
		
    	return $idsUsers;
  	}
  	function SearchLog($values){
  	    $filter ="";
  	    if($values['modulo']!="")
  	        $filter .=" AND log.tabla='".$values['modulo']."' ";
        if($values['finicial']!="")
            $filter .=" AND log.fecha>='".$values['finicial']." 00:00:00' ";
        if($values['ffinal']!="")
            $filter .=" AND log.fecha<='".$values['ffinal']." 00:00:00' ";

         $sql = "SELECT date(log.fecha) as fecham,log.*,personal.name as usuario FROM log LEFT JOIN personal ON log.personalId=personal.personalId where 1 ".$filter."   ORDER BY log.fecha ASC";
         $this->Util()->DB()->setQuery($sql);
         $result = $this->Util()->DB()->GetResult();

         return $result;
    }

  		
}//Log
