<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 15/03/2018
 * Time: 11:12 AM
 */

class Expediente extends Main
{
    private $name;
    private $expedienteId;

    public function setName($value){
        $this->Util()->ValidateRequireField($value,'Nombre');
        $this->name=$value;
    }

    private $extensiones=[];
    public function setExtensiones($value)
    {
        if(!is_array($value)||empty($value))
            $this->Util()->setError(0,'error',"Es necesario seleccionar por lo menos un elemento  de la lista",'Extensiones de archivos');
        else
            $this->extensiones = $value;

    }
    public function setExpedienteId($value){
        $this->Util()->ValidateRequireField($value,'Id');
        $this->expedienteId=$value;
    }
    public function Info(){
        $info = array();
        $this->Util()->DB()->setQuery("SELECT * FROM expedientes WHERE expedienteId='".$this->expedienteId."'");
        $info = $this->Util()->DB()->GetRow();
        return $info;
    }
    public function Enumerate(){
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery('SELECT * FROM expedientes WHERE status="activo" ORDER BY name ASC');
        $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
        return $result;
    }
    public function Save(){
        //comprobar si el mismo nombre no se encuentra dado de alta
        $sq = "SELECT expedienteId FROM expedientes WHERE name='".$this->name."'  ";
        $this->Util()->DB()->setQuery($sq);
        $find = $this->Util()->DB()->GetSingle();
        if($find)
            $this->Util()->setError(0,'error','Ya existe un registro con el nombre proporcionado');

        if($this->Util()->PrintErrors())
            return false;

        $sql =  "INSERT INTO expedientes(
                            name,
                            status,
                            extension)
                            VALUES(
                            '".$this->name."',
                            'activo',
                            '".implode(',', $this->extensiones)."')";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->InsertData();

        $this->Util()->setError(0,'complete','Expediente guardado correctamente');
        $this->Util()->PrintErrors();
        return true;
    }
    public function Update(){
        //comprobar si el mismo nombre no se encuentra dado de alta
        $sq = "SELECT expedienteId FROM expedientes WHERE name='".$this->name."' AND expedienteId!='".$this->expedienteId."'  ";
        $this->Util()->DB()->setQuery($sq);
        $find = $this->Util()->DB()->GetSingle();
        if($find)
            $this->Util()->setError(0,'error','Ya existe un registro con el nombre proporcionado');


        if($this->Util()->PrintErrors())
            return false;

        $sql =  "UPDATE expedientes SET 
                       name= '".$this->name."', 
                       extension= '".implode(',', $this->extensiones)."' 
                       WHERE expedienteId='".$this->expedienteId."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();

        $this->Util()->setError(0,'complete','Expediente guardado correctamente');
        $this->Util()->PrintErrors();
        return true;
    }
    public function Delete(){
        if($this->Util()->PrintErrors())
            return false;

        $sql =  "UPDATE expedientes SET status= 'baja' WHERE expedienteId='".$this->expedienteId."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();

        $this->Util()->setError(0,'complete','Registro actualizado correctamente');
        $this->Util()->PrintErrors();
        return true;
    }


}
