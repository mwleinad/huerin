<?php


class Articulo extends Main
{
    private $id;
    public function setId($value){
        $this->Util()->ValidateInteger($value);
        $this->id = $value;
    }
    public function getId(){
        return $this->id;
    }
    private $nombre;
    public function setNombre($value)
    {
        $this->Util()->ValidateRequireField($value,"Nombre");
        $this->nombre = htmlspecialchars($value,ENT_QUOTES);
    }
    public function getNombre(){
        return $this->nombre;
    }

    private $descripcion;
    public function setDescripcion($value){
        $this->Util()->ValidateRequireField($value,"Descripcion");
        $this->descripcion = htmlspecialchars($value,ENT_QUOTES);
    }
    public function getDescripcion(){
        return $this->descripcion;
    }

    private $noBreak = false;
    public function withNobreak($value){
        $this->noBreak =  $value;
    }
    public function isNobreak(){
        return $this->noBreak;
    }

    private $fechaBaja;
    public function setFechaBaja($value){
        if($value!="")
            $this->Util()->validateDateFormat($value,"Fecha baja","d-m-Y");
        $this->fechaBaja = $value;
    }
    public function getFechaBaja(){
        return $this->fechaBaja;
    }

    private $tipoRecurso;
    public function setTipoRecurso($value){
        $this->Util()->ValidateRequireField($value,"Tipo recurso");
        $this->tipoRecurso = $value;
    }
    public function getTipoRecurso(){
        return $this->tipoRecurso;
    }

    private $noSerie;
    public function setNoSerie($value){
        $this->noSerie = htmlspecialchars($value,ENT_QUOTES);
    }
    public function getNoSerie(){
        return $this->noSerie;
    }

    private $noLicencia;
    public function setNoLicencia($value){
       $this->noLicencia= htmlspecialchars($value,ENT_QUOTES);
    }
    public function getNoLicencia(){
        return $this->noLicencia;
    }


    private $codigoActivacion;
    public function setCodigoActivacion($value){
        $this->codigoActivacion= htmlspecialchars($value,ENT_QUOTES);
    }
    public function getCodigoActivacion(){
        return $this->codigoActivacion;
    }

    private $fechaCompra;
    public function setFechaCompra($value){
        if($this->Util()->ValidateRequireField($value,"Fecha de compra"))
            $this->Util()->validateDateFormat($value,"Fecha de compra","d-m-Y");
        $this->fechaCompra= $this->Util()->FormatDateMySql($value);
    }
    public function  getFechaCompra(){
        return $this->fechaCompra;
    }

    private $tipoEquipo;
    public function setTipoEquipo($value){
        $this->Util()->ValidateRequireField($value,"Tipo de equipo");
        $this->tipoEquipo  = $value;
    }
    public function  getTipoEquipo(){
        return $this->tipoEquipo;
    }

    private $hubUsb = false;
    public function withHubUsb($value){
        $this->hubUsb = $value;
    }
    public function isHubUsb(){
        return  $this->hubUsb;
    }

    private $motivoBaja;
    public function setMotivoBaja($value){
        $this->Util()->ValidateRequireField($value," Motivo por la que se dara de baja");
        $this->motivoBaja = htmlspecialchars($value,ENT_QUOTES);
    }
    public function getMotivoBaja(){
        return $this->motivoBaja;
    }

    /*
     * set & get responsables de recurso de oficina
     */
    private $nombreResponsable;
    public function setNombreResponsable($value)
    {
        $this->Util()->ValidateRequireField($value,"Nombre responsable");
        $this->nombreResponsable = htmlspecialchars($value,ENT_QUOTES);
    }
    public function getNombreResponsable(){
        return $this->nombreResponsable;
    }

    private $fechaEntregaResponsable;
    public function setFechaEntregaResponsable($value)
    {
        if($this->Util()->ValidateRequireField($value,"Fecha de entrega a res."))
            $this->Util()->validateDateFormat($value,"Fecha de entrega a res.","d-m-Y");
        $this->fechaEntregaResponsable = $this->Util()->FormatDateMySql($value);
    }
    public function getFechaEntregaResponsable(){
        return $this->fechaEntregaResponsable;
    }

    private $tipoResponsable;
    public function setTipoResponsable($value)
    {
        $this->Util()->ValidateRequireField($value,"Tipo responsable");
        $this->tipoResponsable = $value;
    }
    public function getTipoResponsable(){
        return $this->tipoResponsable;
    }

}