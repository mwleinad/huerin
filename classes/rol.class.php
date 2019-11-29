<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 16/03/2018
 * Time: 04:42 PM
 */

class Rol extends main
{
    private $admin;
    function setAdmin($value){
        $this->admin=$value;
    }
    public function isAdmin(){
        return $this->admin;
    }
    private $rolId;
    function setRolId($value){
        $this->rolId=$value;
    }
    private $name;
    public function setName($value){
        $this->Util()->ValidateRequireField($value,'Nombre de rol');
        $this->name=$value;
    }
    private $depId;
    public function setDepartamentoId($value){
        if(!$_SESSION["User"]["isRoot"])
            $this->Util()->ValidateRequireField($value,'Departamento');
        $this->depId=$value;
    }
    private $titulo;
    public function setTitulo($value){
        $this->titulo=$value;
    }
    private $porcentId;
    function setPorcentId($value){
        $this->porcentId=$value;
    }
    private $namePorcent;
    public function setNamePorcent($value){
        $this->Util()->ValidateRequireField($value,'Nombre');
        $this->namePorcent=$value;
    }
    private $categoria;
    public function setCategoria($value){
        $this->Util()->ValidateRequireField($value,'Categoria');
        $this->Util()->ValidateOnlyNumeric($value,"categoria");
        $this->categoria=$value;
    }
    private $porcentaje;
    public function setPorcentaje($value){
        $this->Util()->ValidateRequireField($value,'Porcentaje');
        $this->Util()->ValidateNumericWhitRange($value,0,100,'Porcentaje');
        $this->porcentaje=$value;
    }
    public function Info(){
        $sql = "SELECT * FROM roles WHERE status='activo' AND rolId='".$this->rolId."' ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $info = $this->Util()->DBSelect($_SESSION['empresaId'])->GetRow();
        return $info;
    }
    public function InfoPorcent(){
        $sql = "SELECT * FROM porcentajesBonos WHERE  porcentId='".$this->porcentId."' ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $info = $this->Util()->DBSelect($_SESSION['empresaId'])->GetRow();
        return $info;
    }
    public function getInfoByData($data){
        $this->setTitulo($data['tipoPersonal']);
        $roleId = $this->GetIdByName();
        if($roleId<=0){
            $roleId=$data['roleId'];
        }
        $this->setRolId($roleId);
        $role = $this->Info();
        return $role;
    }
    public function GetIdByName(){
        $sql = "SELECT rolId FROM roles WHERE status='activo' AND name='".$this->titulo."' ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $single = $this->Util()->DBSelect($_SESSION['empresaId'])->GetSingle();
        return $single;
    }
    public function Enumerate(){
        $where ="";
        $where .=!$_SESSION["User"]["isRoot"] ? (int)$_SESSION["User"]["level"] != 1 ? " and nivel >= '".$_SESSION['User']['level']."' ":" and nivel > '".$_SESSION['User']['level']."' ":"";
        $where .= (int)$_SESSION["User"]["level"] != 1? 
                    " and nivel <= 6 and departamentoId = '".$_SESSION["User"]["departamentoId"]."' " 
                    : "";

       $sql ="SELECT a.*,
              CASE 
              WHEN a.departamentoId is null  THEN 'SIN DEPARTAMENTO'
              WHEN a.departamentoId <=0 THEN 'SIN DEPARTAMENTO'
              ELSE b.departamento END AS departamento
              FROM roles a LEFT JOIN departamentos b ON a.departamentoId=b.departamentoId WHERE a.status='activo' ".$where." ORDER BY b.departamento ASC,a.name ASC";
       $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
       $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
       return $result;
    }
    public function EnumeratePorcentajes(){
        $sql ="SELECT * FROM porcentajesBonos order by categoria ASC";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
        return $result;
    }
    public function GetRolesGroupByDep(){
       $res = $this->Enumerate();
       $groups = array();
       $deps = array();
       foreach($res as $key => $item){
           if(!$item['departamentoId'])
               continue;

           $depId = $item['departamentoId'];

           if(in_array($depId,$deps)){
               $groups[$depId]['roles'][] = $item;
           }else{
               array_push($deps,$depId);
               $groups[$depId]['departamento'] =$item['departamento'];
               $groups[$depId]['departamentoId'] =$item['departamentoId'];
               $groups[$depId]['roles'][] =$item;
           }
       }
       return $groups;
    }
    public function Save(){
        $sql = "SELECT * FROM  roles WHERE LOWER(name)='".strtolower($this->name)."' ";
        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();
        if(!empty($res))
            $this->Util()->setError(0,'error',"Ya existe un registro con el nombre proporcionado");


        if($this->Util()->PrintErrors())
            return false;

        $sql = "INSERT INTO roles(name,status,departamentoId,nivel) VALUES('".$this->name."','activo','".$this->depId."','".$this->categoria."') ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->InsertData();

        $this->Util()->setError(0,"complete",'Se ha creado el registro correctamente');
        $this->Util()->PrintErrors();
        return true;
    }
    public function SavePorcent(){
        $sql = "SELECT porcentId FROM  porcentajesBonos WHERE LOWER(name)='".strtolower($this->name)."' OR categoria='".$this->categoria."' ";
        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();
        if(!empty($res))
            $this->Util()->setError(0,'error',"Ya existe un registro con el nombre o categoria proporcionado");


        if($this->Util()->PrintErrors())
            return false;

        $sql = "INSERT INTO porcentajesBonos(name,categoria,porcentaje) VALUES('".$this->namePorcent."',$this->categoria,'".$this->porcentaje."') ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->InsertData();

        $this->Util()->setError(0,"complete",'Se ha creado el registro correctamente');
        $this->Util()->PrintErrors();
        return true;
    }
    function Update(){
        $sql = "SELECT * FROM  roles WHERE LOWER(name)='".strtolower($this->name)."' AND rolId!='".$this->rolId."' ";
        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();
        if(!empty($res))
            $this->Util()->setError(0,'error',"Ya existe un registro con el nombre proporcionado");


        if($this->Util()->PrintErrors())
            return false;

        $sql = "UPDATE roles SET name='".$this->name."',departamentoId='".$this->depId."',nivel='".$this->categoria."' WHERE rolId='".$this->rolId."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();

        $this->Util()->setError(0,"complete",'Se ha actualizado el registro correctamente');
        $this->Util()->PrintErrors();
        return true;
    }
    function UpdatePorcent(){
        $sql = "SELECT porcentId FROM  porcentajesBonos WHERE categoria='".$this->categoria."' and  porcentId!='".$this->porcentId."'";
        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();
        if(!empty($res))
            $this->Util()->setError(0,'error',"Ya existe un registro con la categoria proporcionado");


        if($this->Util()->PrintErrors())
            return false;

        $sql = "UPDATE porcentajesBonos SET categoria='".$this->categoria."',porcentaje='".$this->porcentaje."' WHERE porcentId='".$this->porcentId."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();

        $this->Util()->setError(0,"complete",'Se ha actualizado el registro correctamente');
        $this->Util()->PrintErrors();
        return true;
    }
    function Delete(){

        $sql = "UPDATE roles SET status='baja' WHERE rolId='".$this->rolId."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        //eliminar permisos
        $sqlDel = "DELETE FROM rolesPermisos WHERE rolId='".$this->rolId."' ";
        $this->Util()->DB()->setQuery($sqlDel);
        $this->Util()->DB()->DeleteData();

        $this->Util()->setError(0,"complete",'Se ha dado de baja el registro correctamente');
        $this->Util()->PrintErrors();
        return true;
    }
    function DeletePorcent(){

        $sql = "delete from porcentajesBonos  WHERE porcentId='".$this->porcentId."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();

        $this->Util()->setError(0,"complete",'Se ha dado de baja el registro correctamente');
        $this->Util()->PrintErrors();
        return true;
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
   function CountChild(array $temps,$count,$owns){
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
       $sql =  "SELECT permisoId from rolesPermisos where rolId='".$this->rolId."'";
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
   function AssignPermisoToRol($permiso){
        $sql = "REPLACE INTO rolesPermisos
                (
                    rolId,
                    permisoId,
                    date
                )
                VALUES(
                    '".$this->rolId."',
                    '".$permiso."',
                    '".date('Y-m-d')."'
                    
                )";
       $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
       $this->Util()->DBSelect($_SESSION['empresaId'])->UpdateData();
    }
    function RemovePermisoToRol($permiso){
        $ftr = "";
        if($this->rolId>0)
            $ftr =" and rolId ='".$this->rolId."' ";
        $sql = "DELETE FROM rolesPermisos WHERE permisoId='".$permiso."' $ftr ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $this->Util()->DBSelect($_SESSION['empresaId'])->DeleteData();
    }
   function GetPermisosByRol(){
       if($this->isAdmin())
           $sql =  "SELECT permisoId from rolesPermisos where 1";
       else
           $sql =  "SELECT permisoId from rolesPermisos where rolId='".$this->rolId."' ";
       $this->Util()->DB()->setQuery($sql);
       $array_perm = $this->Util()->DB()->GetResult();
       $owns_lineal =$this->Util()->ConvertToLineal($array_perm,'permisoId');
       return $owns_lineal;
   }
   function GetPermisoByTitulo($titulo){
       $sql =  "SELECT permisoId from permisos where titulo='".$titulo."' ";
       $this->Util()->DB()->setQuery($sql);
       $single = $this->Util()->DB()->GetSingle();
       return $single;
   }
   function FindFirstPage(){
       global $User;
       if(!$this->isAdmin())
           $filtro = " AND a.rolId='".$this->rolId."' ";

       $roleId =$User['roleId'];
       $sql =  "SELECT a.permisoId from rolesPermisos a INNER JOIN permisos b ON a.permisoId=b.permisoId  where b.parentId is null ".$filtro." ";
       $this->Util()->DB()->setQuery($sql);
       $result = $this->Util()->DB()->GetResult();
       $firstPages = array();
       foreach($result  as $key=>$value){
           $sql =  "SELECT namePage from rolesPermisos a INNER JOIN permisos b ON a.permisoId=b.permisoId  where b.parentId='".$value['permisoId']."' ".$filtro." ORDER BY b.permisoId ASC limit 1";
           $this->Util()->DB()->setQuery($sql);
           $single = $this->Util()->DB()->GetSingle();
           $firstPages[$value['permisoId']]=$single;
       }
       return $firstPages;
   }
    public function GetListRoles(){

        $filtro ="";
        // $filtro .= !$_SESSION['User']['isRoot'] ? " and nivel >= '".$_SESSION['User']['level']."' " : " and nivel > '".$_SESSION['User']['level']."' ";
        $filtro .=!$_SESSION["User"]["isRoot"] ? (int)$_SESSION["User"]["level"] != 1 ? " and nivel >= '".$_SESSION['User']['level']."' ":" and nivel > '".$_SESSION['User']['level']."' ":"";
        $filtro .= (int)$_SESSION["User"]["level"] != 1? 
                    " and nivel <= 6 and departamentoId = '".$_SESSION["User"]["departamentoId"]."' " 
                    : "";

        $sql ="SELECT * FROM roles WHERE status='activo' ".$filtro." ORDER BY name ASC";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $result = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();

        return $result;
    }
    public function ValidatePrivilegiosRol($incluye=array(),$except=array()){
        $sql = "SELECT * FROM roles WHERE rolId='".$this->rolId."' AND status='activo' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        //si no se encuentra rol se convierte en limitado retorna false
        if(empty($row))
            return false;

        //comprobar nombre de rol por trozos de nombre
        $lim=0;
        foreach($incluye as $inc){
            if(stripos(strtolower($row['name']),$inc)!==false)
              $lim++;
        }
        foreach($except as $excep){
            if(stripos(strtolower($row['name']),$excep)!==false)
                $lim--;
        }
        if($lim>0)
            return false;
        else
            return true;
    }
    public function DrawChildrenExcel(&$row,$sheet,$children,$styles){
        foreach($children as $var){
            $col=$var['levelDeep'];
            $cell = PHPExcel_Cell::stringFromColumnIndex($col);
            $cell2 = PHPExcel_Cell::stringFromColumnIndex($col+1);
            if($var['letMe']){
                $sheet->getStyle($cell.$row)->applyFromArray($styles['styleLetMe']);
                $sheet->getStyle($cell2.$row)->applyFromArray($styles['styleLetMe']);
            }
            if(!empty($var['children'])){
                $sheet->getStyle($cell.$row)->applyFromArray($styles['styleBold']);
                $sheet->setCellValueByColumnAndRow($col,$row,$var['titulo']);
                $row++;
                $this->DrawChildrenExcel($row,$sheet,$var['children'],$styles);
            }
            else{
                 $sheet->setCellValueByColumnAndRow($col,$row,$var['titulo']);

                 $row++;
            }

        }
    }

}