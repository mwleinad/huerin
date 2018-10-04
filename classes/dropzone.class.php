<?php

class Dropzone extends main
{
  private $fileId;
  private $relacionId;
  private $table;
  private $fieldFile;
  private $fieldRelacion;
  private $fieldPath;
  private $ruta;
  private $keyTable;
  private $dateExpiration;
  public function setFileId($value){
     $this->Util()->ValidateRequireField($value,"Tipo");
     $this->fileId = $value;
  }
  public function setTable($value){
      $this->Util()->ValidateRequireField($value,"error interno: name table is empty");
      $this->table = $value;
  }
  public function setFieldFile($value)
  {
      $this->Util()->ValidateRequireField($value,"error interno: name field file is empty ");
      $this->fieldFile = $value;
  }
  public function setFieldRelacion($value)
  {
      $this->Util()->ValidateRequireField($value,"error interno: name relacion is empty ");
      $this->fieldRelacion = $value;
  }
  public function setRelacionId($value)
  {
    $this->Util()->ValidateRequireField($value,"error interno: name relacionId is empty ");
    $this->relacionId = $value;
  }
  public function setFielPath($value)
  {
      $this->Util()->ValidateRequireField($value,"error interno: name path is empty ");
      $this->fieldPath = $value;
  }
  public function setRuta($value)
  {
      $this->Util()->ValidateRequireField($value,"error interno: name ruta is empty ");
      $this->ruta = $value;
  }
  public function setKeyTable($value)
  {
      $this->Util()->ValidateRequireField($value,"error interno: name keyTable is empty ");
      $this->keyTable = $value;
  }
  public function setDateExpiration($value){
      $this->Util()->ValidateRequireField($value,"Fecha de vencimiento");
      $this->dateExpiration = $value;
  }
  public function doProcessFile(){
      $add ="";
      if($this->dateExpiration!=""){
          if(!$this->Util()->isValidateDate($this->dateExpiration)){
              $this->Util()->setError(0, 'error', 'Fecha de vencimiento no valida');
          }else{
              $add .=" ,date='".date("Y-m-d",strtotime($this->dateExpiration))."' ";
          }
      }
     if($this->Util()->PrintErrors())
         return false;

      $sql =  "insert into ".$this->table."(".$this->fieldRelacion.",".$this->fieldFile.")
                values(
                 ".$this->relacionId.",
                 ".$this->fileId."
                )";
      $this->Util()->DB()->setQuery($sql);
      $id = $this->Util()->DB()->InsertData();
      if(!$id){
          $this->Util()->setError(0, 'error', 'Error al guardar archivo, intentelo nuevamente');
          $this->Util()->PrintErrors();
          return false;
      }
      $nombreArchivo = preg_replace("/&#?[a-z0-9]+;/i","", basename( $_FILES["file"]['name']));
      $nombreArchivo = str_replace(" ","", $nombreArchivo);
      $nombreArchivo = strtolower($nombreArchivo);
      $target_path = $this->ruta.$this->relacionId."_". $nombreArchivo;
       //mover archivo a destino, si es satisfactorio se actualiza datos en table de lo contrario hacer rollback
      if(move_uploaded_file($_FILES["file"]['tmp_name'], $target_path)) {
          $this->Util()->DB()->setQuery("update ".$this->table." set ".$this->fieldPath." = '".$nombreArchivo."' $add WHERE ".$this->keyTable." = '".$id."'");
          $this->Util()->DB()->UpdateData();
          $this->Util()->setError(0, "complete", 'El archivo fue agregado correctamente');
          $this->Util()->PrintErrors();
          return true;
      }else{
          $this->Util()->DB()->setQuery("delete from  ".$this->table." WHERE ".$this->keyTable." = '".$id."'");
          $this->Util()->DB()->DeleteData();
          $this->Util()->setError(0, 'error', 'Error al guardar archivo, intentelo nuevamente');
          $this->Util()->PrintErrors();
          return false;
      }
  }


}