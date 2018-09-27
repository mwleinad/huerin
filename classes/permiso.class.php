<?php
/**
 * Descomponer en tabla los permisos de cada contracto activo
 */


class Permiso extends Main
{
    private $contractId;
    public function setContractId($value){

        $this->contractId=$value;
    }
    public function doPermiso(){
        $sql =  "select contractId,permisos from contract where activo='Si'  and contractId='".$this->contractId."' ";
        $this->Util()->DB()->setQuery($sql);
        $contrato = $this->Util()->DB()->getRow();

        $permisos = explode('-',$contrato['permisos']);
        if(!is_array($permisos) || empty($permisos))
           return false;

        $this->Util()->DB()->setQuery("delete  from contractPermiso where contractId='".$this->contractId."' ");
        $this->Util()->DB()->DeleteData();

        $sqlPer = "replace into contractPermiso (contractId,departamentoId,personalId) VALUES ";
        $sqlComp = "";
        foreach($permisos as $perm)
        {
            list($dep,$id) =  explode(',',$perm);
            if($dep>0&&$id>0){
                if($dep==26)
                    $dep=33;

                $sqlComp .= "($this->contractId,$dep,$id),";
            }

        }

        if($sqlComp!=""){
            $sqlComp = substr($sqlComp,0,strlen($sqlComp)-1);
            $sql = $sqlPer.$sqlComp;
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->InsertData();
        }
        return true;
    }
    public function doPermisos($inactive=false){
        $where ="";

        if($inactive)
            $where .= "where activo='No' ";

        $sql =  "select contractId,permisos from contract $where ";
        $this->Util()->DB()->setQuery($sql);
        $contratos = $this->Util()->DB()->GetResult();

        foreach($contratos as $key=>$value){
            $idContrato = $value['contractId'];
            //limpiar permisos actuales
            $this->Util()->DB()->setQuery("delete  from contractPermiso where contractId='".$idContrato."' ");
            $this->Util()->DB()->DeleteData();

            $sqlPer = "replace into contractPermiso (contractId,departamentoId,personalId) VALUES ";
            $permisos = explode('-',$value['permisos']);
            if(!is_array($permisos) || empty($permisos))
                continue;

            $sqlComp = "";
            foreach($permisos as $perm)
            {
                list($dep,$id) =  explode(',',$perm);
                if($dep>0&&$id>0){
                    if($dep==26)
                        $dep=33;

                    $sqlComp .= "($idContrato,$dep,$id),";
                }

            }

            if($sqlComp!=""){
               $sqlComp = substr($sqlComp,0,strlen($sqlComp)-1);
               $sql = $sqlPer.$sqlComp;
               $this->Util()->DB()->setQuery($sql);
               $this->Util()->DB()->InsertData();
            }
        }
    }
}