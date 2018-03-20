<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 16/03/2018
 * Time: 04:42 PM
 */

class Rol extends main
{
    private $rolId;
    function setRolId($value){
        $this->rolId=$value;
    }
    public function Info(){
        $sql = "SELECT * FROM roles WHERE status='activo' AND rolId='".$this->rolId."' ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $info = $this->Util()->DBSelect($_SESSION['empresaId'])->GetRow();
        return $info;
    }
    public function Enumerate(){
       $sql ="SELECT * FROM roles WHERE status='activo' ORDER BY name ASC";
       $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
       $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
       return $result;
    }
    function FindDeep(array $elements,$parentId=0){
       $branch=array();
       foreach($elements as $element){
           if ($element['parentId'] == $parentId) {
                $children = $this->FindDeep($elements, $element['permisoId']);
               if ($children) {
                   $element['children'] = $children;
               }
               $branch[] = $element;
           }
       }
       return $branch;
    }
   function CountChild(array $temps,&$count,$owns){
       $tree =array();
       $cad=array();
       foreach($temps as $kt=>$temp){
           $deep = array();
           $cad = $temp;
           if(in_array($temp['permisoId'],$owns))
               $cad['letMe']=true;
           else
               $cad['letMe']=false;

           if(!empty($temp['children']))
           {
               $count++;
              $deep =  $this->CountChild($temp['children'],$count,$owns);
           }
           $cad['children'] =  $deep;
           $tree[]=$cad;
       }
       return $tree;
   }
   function GetConfigRol(){
       //find permisos by rol
       $sql =  "SELECT permisoId from rolesPermisos where rolId=".$this->rolId;
       $this->Util()->DB()->setQuery($sql);
       $array_perm = $this->Util()->DB()->GetResult();
       $owns_lineal =$this->Util()->ConvertToLineal($array_perm,'permisoId');

       $sql =  "SELECT * from permisos";
       $this->Util()->DB()->setQuery($sql);
       $lst2 = $this->Util()->DB()->GetResult();

       $res = $this->FindDeep($lst2);
       $new = array();
       $card=array();
       foreach($res as $ky=>$val){
           $deep = array();
           $card = $val;
           $countLevel = 0;
           if(in_array($val['permisoId'],$owns_lineal))
               $card['letMe']=true;
           else
               $card['letMe']=false;

           if(!empty($val['children']))
           {
              $deep = $this->CountChild($val['children'],$countLevel,$owns_lineal);
           }
           $card['children']=$deep;
           $card['levels'] = $countLevel;
           $new[]=$card;
       }
      return $new;
   }
   function SaveConfigRol(){
       $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery('SELECT permisoId from rolesPermisos WHERE rolId="'.$this->rolId.'" ');
       $arrayPerm = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
       $permActual = $this->Util()->ConvertToLineal($arrayPerm,'permisoId');
       $sql2 = 'REPLACE INTO rolesPermisos(rolId,permisoId,date) VALUES';
       if(!empty($_POST['permisos']))
       {
           foreach($_POST['permisos'] as $perm)
           {
               if($perm===end($_POST['permisos']))
                   $sql2 .="(".$this->rolId.",".$perm.",'".date('Y-m-d')."');";
               else
                   $sql2 .="(".$this->rolId.",".$perm.",'".date('Y-m-d')."'),";
               //encontrar la posicion de $exp en expActual
               $key = array_search($perm,$permActual);
               unset($permActual[$key]);
           }
           $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql2);
           $this->Util()->DBSelect($_SESSION['empresaId'])->UpdateData();
       }
       if(!empty($permActual)){
           $sqlu = "DELETE FROM rolesPermisos WHERE permisoId IN(".implode(",",$permActual).") AND rolId='".$this->rolId."' ";
           $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sqlu);
           $this->Util()->DBSelect($_SESSION['empresaId'])->DeleteData();
       }

       $this->Util()->setError(10049, "complete",'Se han guardado los cambios correctamente');
       $this->Util()->PrintErrors();
       return true;
   }
   function GetPermisosByRol(){
       $sql =  "SELECT permisoId from rolesPermisos where rolId='".$this->rolId."' ";
       $this->Util()->DB()->setQuery($sql);
       $array_perm = $this->Util()->DB()->GetResult();
       $owns_lineal =$this->Util()->ConvertToLineal($array_perm,'permisoId');
       return $owns_lineal;
   }
}