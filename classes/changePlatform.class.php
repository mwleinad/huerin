<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 25/03/2019
 * Time: 06:16 PM
 */

class ChangePlatform extends main
{
    private  $changeId;
    function setId($value){
        $this->Util()->ValidateRequireField($value,"ID");
        $this->changeId = $value;
    }
    private  $descripcion;
    function setDescripcion($value){
        $this->Util()->ValidateRequireField($value,"Descripcion");
        $this->descripcion = $value;
    }
    private  $modulo;
    function setModulo($value){
        $this->Util()->ValidateRequireField($value,"Modulo");
        $this->modulo = $value;
    }
    private  $fsolicitud;
    function setFechaSolicitud($value){
        if($this->Util()->ValidateRequireField($value,"Fecha de solicitud"))
            $this->Util()->validateDateFormat($value,"Fecha de solicitud","m-d-Y");
        $this->fsolicitud = $value;
    }
    private  $fentrega;
    function setFechaEntrega($value){
        if($this->Util()->ValidateRequireField($value,"Fecha de entrega"))
            $this->Util()->validateDateFormat($value,"Fecha de entrega","m-d-Y");
        $this->fentrega = $value;
    }
    private  $frevision;
    function setFechaRevision($value){
        if($this->Util()->ValidateRequireField($value,"Fecha de revision"))
            $this->Util()->validateDateFormat($value,"Fecha de revision","m-d-Y");
        $this->frevision = $value;
    }
    function Enumerate(){
        $sql ="SELECT * FROM changesPlatform  ORDER BY fechaSolicitud DESC";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
        foreach($result as $key=>$value)
        {
            $value["fileExist"] = false;
            if($value["url"]!=""){
                if(file_exists(DOC_ROOT.$value["url"]))
                    $value["fileExist"] = true;
            }
            $result[$key]=$value;
        }
        return $result;
    }
    function Info(){
        $sql ="SELECT * FROM changesPlatform  WHERE changeId='".$this->changeId."' ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $row = $this->Util()->DBSelect($_SESSION['empresaId'])->GetRow();

        $row["fileExist"] = false;
        if($row["url"]!=""){
            if(file_exists(DOC_ROOT.$row["url"]))
                $row["fileExist"] = true;
        }
        return $row;
    }
    function savePending(){
        if($this->Util()->PrintErrors())
            return false;

        $sql ="INSERT INTO changesPlatform(
                descripcion,
                modulo,
                fechaSolicitud,
                fechaEntrega,
                fechaRevision
               )VALUES(
               '".$this->descripcion."',
               '".$this->modulo."',
               '".$this->Util()->FormatDateMySql($this->fsolicitud)."',
               '".$this->Util()->FormatDateMySql($this->fentrega)."',
               '".$this->Util()->FormatDateMySql($this->frevision)."'
               )
              ";
        $this->Util()->DB()->setQuery($sql);
        $lastId = $this->Util()->DB()->InsertData();
        if($_FILES["adjunto"]["error"]==0){
            $ext = end(explode(".",$_FILES["adjunto"]["name"]));
            $base_dir="/filesPendiente/pendiente_$lastId.$ext";
            $dir =  DOC_ROOT.$base_dir;
            if(move_uploaded_file($_FILES["adjunto"]["tmp_name"],$dir)){
                $sql = "UPDATE changesPlatform set url='$base_dir' WHERE changeId='$lastId' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
            }
        }
        $this->Util()->setError(0,"complete","Pendiente agregado correctamente");
        $this->Util()->PrintErrors();
        return true;
    }
    function updatePending(){
        if($this->Util()->PrintErrors())
            return false;


        $sql = "UPDATE changesPlatform 
                SET
                descripcion='".$this->descripcion."',
                modulo='".$this->modulo."',
                fechaSolicitud='".$this->Util()->FormatDateMySql($this->fsolicitud)."',
                fechaEntrega='".$this->Util()->FormatDateMySql($this->fentrega)."',
                fechaRevision='".$this->Util()->FormatDateMySql($this->frevision)."'
                WHERE changeId='".$this->changeId."'
               ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        if($_FILES["adjunto"]["error"]==0){
            $Id = $this->changeId;
            $ext = end(explode(".",$_FILES["adjunto"]["name"]));
            $base_dir="/filesPendiente/pendiente_$Id.$ext";
            $dir =  DOC_ROOT.$base_dir;
            if(move_uploaded_file($_FILES["adjunto"]["tmp_name"],$dir)){
                $sql = "UPDATE changesPlatform set url='$base_dir' WHERE changeId='$Id' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
            }
        }
        $this->Util()->setError(0,"complete","Pendiente actualizado correctamente");
        $this->Util()->PrintErrors();
        return true;
    }
    function deletePending(){
        if($this->Util()->PrintErrors())
            return false;
        $info = $this->Info();

        $sql = "DELETE FROM changesPlatform 
                WHERE changeId='".$this->changeId."'
               ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->DeleteData();

        $file =  DOC_ROOT.$info["url"];
        if(file_exists($file)){
            unlink($file);
        }
        $this->Util()->setError(0,"complete","Pendiente eliminado correctamente");
        $this->Util()->PrintErrors();
        return true;
    }

}