<?php


class Inventory extends Articulo
{
    public function validateFileResponsiva(){
        if(!isset($_FILES['responsiva']))
            return false;

        if($_FILES['responsiva']['error'] == 0){
            if($_FILES['responsiva']['size']>2*MB){
                $this->Util()->setError(0,"error","Archivo de responsiva excede el limite maximo permitido: 2MB");
                return false;
            }

            if(strtoupper(end(explode('.',$_FILES["responsiva"]['name']))) !='PDF'){
                $this->Util()->setError(0,"error","Archivo de responsiva formato invalido");
                return false;
            }

        }

    }
    public function saveResource(){
        if($this->Util()->PrintErrors())
            return false;
        $sql = "INSERT INTO office_resource(
                            nombre,
                            descripcion,
                            con_nobreak,
                            tipo_recurso,
                            no_serie,
                            no_licencia,
                            codigo_activacion,
                            fecha_compra,
                            tipo_equipo,
                            con_hubusb,
                            status,
                            usuario_alta,
                            fecha_alta) VALUES(
                              '".$this->getNombre()."',
                              '".$this->getDescripcion()."',
                              '".$this->isNobreak()."',
                              '".$this->getTipoRecurso()."',
                              '".$this->getNoSerie()."',
                              '".$this->getNoLicencia()."',
                              '".$this->getCodigoActivacion()."',
                              '".$this->getFechaCompra()."',
                              '".$this->getTipoEquipo()."',
                              '".$this->isHubUsb()."',
                              'Activo',
                              '".$_SESSION['User']['username']."',
                              '".date("Y-m-d H:i:s")."'             
                            )";
        $this->Util()->DB()->setQuery($sql);
        $id = $this->Util()->DB()->InsertData();
        $this->saveResponsablesResource($id);
        $this->Util()->setError(0,"complete","El registro se ha guardado correctamente.");
        $this->Util()->PrintErrors();
        return true;
    }
    public function updateResource(){
        if($this->Util()->PrintErrors())
            return false;

       $sql = " UPDATE office_resource SET
                    nombre = '".$this->getNombre()."',
                    descripcion = '".$this->getDescripcion()."',
                    con_nobreak = '".$this->isNobreak()."',
                    usuario_modificacion = '".$_SESSION["User"]["username"]."',
                    tipo_recurso =  '".$this->getTipoRecurso()."',
                    no_serie = '".$this->getNoSerie()."',
                    no_licencia = '".$this->getNoLicencia()."',
                    codigo_activacion = '".$this->getCodigoActivacion()."',
                    fecha_compra = '".$this->getFechaCompra()."',
                    tipo_equipo = '".$this->getTipoEquipo()."',
                    con_hubusb = '".$this->isHubUsb()."',
                    fecha_ultima_modificacion = '".date("Y-m-d H:i:s")."' 
                    WHERE office_resource_id = '".$this->getId()."'
                ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        $this->saveResponsablesResource($this->getId());
        $this->Util()->setError(0,"complete","El registro se ha actualizado correctamente.");
        $this->Util()->PrintErrors();
        return true;
    }
    public function saveResponsablesResource($resourceId)
    {
        if(!isset($_SESSION["responsables_resource"]) || !$resourceId)
            return false;

        $responsables = $_SESSION["responsables_resource"];
        if(count($responsables)<=0)
            return false;

        foreach($responsables as $res){
            if($res["insertUpdate"]){
                if($res["responsable_resource_id"]){
                    if($res["status"]=="Baja")
                        $addUp = " fecha_liberacion_responsable = '".date("Y-m-d")."',  ";

                    $sql = "UPDATE responsables_resource_office
                            SET 
                            nombre = '".$res['nombre']."',
                            fecha_entrega_responsable = '".$res['fecha_entrega_responsable']."',
                            tipo_responsable = '".$res['tipo_responsable']."',
                            status = '".$res['status']."',
                            $addUp
                            usuario_modificador = '".$_SESSION['User']['username']."',
                            fecha_ultima_modificacion = '".date("Y-m-d H:i:s")."'
                            WHERE responsable_resource_id = '".$res['responsable_resource_id']."'
                                 
                        ";
                    $this->Util()->DB()->setQuery($sql);
                    $this->Util()->DB()->UpdateData();

                }else{
                    $sql = "INSERT INTO responsables_resource_office(
                             office_resource_id,
                             nombre,
                             fecha_entrega_responsable,
                             tipo_responsable,
                             usuario_creador,
                             status,
                             fecha_creacion
                             )values(
                              '$resourceId',
                              '".$res['nombre']."',
                              '".$res['fecha_entrega_responsable']."',
                              '".$res['tipo_responsable']."',
                              '".$_SESSION['User']['username']."',
                              'Activo',
                              '".date("Y-m-d H:i:s")."'       
                             )
                            ";
                    $this->Util()->DB()->setQuery($sql);
                    $last_res_id = $this->Util()->DB()->InsertData();
                    $this->moveToRealFolderResponsiva($res["responsiva_root"],$resourceId,$last_res_id);

                }
            }
        }
    }

    public function addResponsablesToArray(){

        if($this->Util()->PrintErrors())
            return false;

        if(!isset($_SESSION['responsables_resource']))
            $_SESSION['responsables_resource'] = [];


        @end($_SESSION['responsables_resource']);
        $key = @key($_SESSION['responsables_resource'])+1;

        $tmp['nombre'] = $this->getNombreResponsable();
        $tmp['fecha_entrega_responsable'] = $this->getFechaEntregaResponsable();
        $tmp['tipo_responsable'] = $this->getTipoResponsable();
        $tmp['status'] = "Activo";
        $tmp['insertUpdate'] = "true";
        $tmp['responsiva_root'] = $this->saveResponsiva($_FILES,$key);

        //guardar responsiva

        $_SESSION['responsables_resource'][$key] = $tmp;

        $this->Util()->setError(0,"complete","Has agregado correctamente un registro");
        $this->Util()->PrintErrors();

        return  true;
    }
    function saveResponsiva($FILES,$id){
        $base_ruta = "/swap/tmp_responsiva";
        $folder = DOC_ROOT.$base_ruta;
        if($FILES['responsiva']['error']==0) {
            $ext =  end(explode('.',$FILES['responsiva']['name']));
            $name = session_id()."_$id.$ext";
            if(!is_dir($folder))
                mkdir($folder);
            if(move_uploaded_file($FILES['responsiva']['tmp_name'],$folder."/$name"))
                return $base_ruta."/$name";
            else return false;
        }else
            return false;
    }
    function moveToRealFolderResponsiva($ruta,$resourceId,$responsableId){
        $old_file = DOC_ROOT.$ruta;
        $base_ruta_real = "/expedientes/responsivas";
        $folder_real = DOC_ROOT.$base_ruta_real;
        if(!$ruta)
            return false;
        $ext_real = end(explode(".",$ruta));
        $name_real = $resourceId."_file_responsiva_".$responsableId.".".$ext_real;
        if(is_file($old_file)){
            if(!is_dir($folder_real))
                mkdir($folder_real);

            $ruta_real = $base_ruta_real."/".$name_real;
            if(rename($old_file,DOC_ROOT.$ruta_real)){
                $sql  = "update responsables_resource_office set responsiva_root = '$ruta_real' where responsable_resource_id = '$responsableId' ";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
            }

        }

    }
    public function  deleteResponsableFromArray($id){
        $current = $_SESSION['responsables_resource'][$id];
        if($current['responsable_resource_id']){
            $current['status'] = "Baja";
            $current['insertUpdate'] =  true;
            $_SESSION['responsables_resource'][$id] = $current;
        }else{
            $ruta_reponsiva =$_SESSION['responsables_resource'][$id]["responsiva_root"];
            if($ruta_reponsiva!=""){
                if(is_file(DOC_ROOT.$ruta_reponsiva));
                    unlink(DOC_ROOT.$ruta_reponsiva);
            }
            unset($_SESSION['responsables_resource'][$id]);
        }
        $this->Util()->setError(0,"complete","Has eliminado correctamente un registro");
        $this->Util()->PrintErrors();
        return true;
    }

    function getListResponsablesResource($id,$incluirBaja =  false){

        $filtro = "";
        if(!$incluirBaja)
            $filtro .=" and status ='Activo' ";

        $sql = "select * from responsables_resource_office where office_resource_id = '".$id."' $filtro order by status asc, fecha_entrega_responsable desc ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    public function infoResource(){
        $sql = "select * from office_resource where office_resource_id = '".$this->getId()."'  ";
        $this->Util()->DB()->setQuery($sql);
        $info = $this->Util()->DB()->GetRow();
        if($info){
            $responsables = $this->getListResponsablesResource($info["office_resource_id"],true);
            if($responsables){
                $info["responsables"] = $responsables;
            }
        }
        return $info;
    }
    public function enumerateResource(){
        $this->Util()->DB()->setQuery("SELECT COUNT(*) FROM office_resource WHERE status='Activo'");
        $total = $this->Util()->DB()->GetSingle();
        $pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/resource-office");
        $sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];

        $this->Util()->DB()->setQuery('SELECT * FROM office_resource WHERE status="Activo" ORDER BY office_resource_id DESC '.$sql_add);
        $result = $this->Util()->DB()->GetResult();

        foreach($result as $key=>$var)
            $result[$key]["responsables"] = $this->getListResponsablesResource($var["office_resource_id"]);


        $data["items"] = $result;
        $data["pages"] = $pages;

        return $data;

    }
    public function searchResource(){
        $filtro = "";

        $like  = $_POST['name_descripcion'];
        $responsable = $_POST['responsable'];
        $tipoRecurso = $_POST['tipo_recurso'];
        $finit = $_POST['fecha_alta_inicio'];
        $fend = $_POST['fecha_alta_fin'];
        $fcinit = $_POST['fecha_compra_inicio'];
        $fcend = $_POST['fecha_compra_fin'];

        if(strlen($like)>0)
            $filtro .=" and (a.nombre like '%$like%' OR a.descripcion like '%$like%') ";

        if(strlen($responsable)>0)
            $filtro .=" and b.nombre like '%$responsable%' ";
        if($tipoRecurso!="")
            $filtro .=" and a.tipo_recurso = '$tipoRecurso' ";
        if($this->Util()->isValidateDate($finit,"d-m-Y"))
            $filtro .=" and a.fecha_alta >= '".$this->Util()->FormatDateMySql($finit)."' ";
        if($this->Util()->isValidateDate($fend,"d-m-Y"))
            $filtro .=" and a.fecha_alta <= '".$this->Util()->FormatDateMySql($fend)."' ";
        if($this->Util()->isValidateDate($fcinit,"d-m-Y"))
            $filtro .=" and a.fecha_compra >= '".$this->Util()->FormatDateMySql($fcinit)."' ";
        if($this->Util()->isValidateDate($fcend,"d-m-Y"))
            $filtro .=" and a.fecha_compra <= '".$this->Util()->FormatDateMySql($fcend)."' ";

        $sql  = "SELECT count(DISTINCT(a.office_resource_id)) FROM office_resource a 
                 LEFT JOIN responsables_resource_office b ON a.office_resource_id = b.office_resource_id
                 WHERE a.status='Activo' and b.status='Activo' $filtro";

        $this->Util()->DB()->setQuery($sql);
        $total = $this->Util()->DB()->GetSingle();
        $pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/resource-office");
        $sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];

        $sql  = "SELECT a.* FROM office_resource a 
                LEFT JOIN responsables_resource_office b ON a.office_resource_id = b.office_resource_id
                WHERE a.status = 'Activo' and b.status='Activo' $filtro GROUP BY a.office_resource_id  ORDER BY a.office_resource_id DESC ".$sql_add;
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        foreach($result as $key=>$var)
            $result[$key]["responsables"] = $this->getListResponsablesResource($var["office_resource_id"]);

        $data["items"] = $result;
        $data["pages"] = $pages;

        return $data;
    }
    public function makeDownResource(){
        if($this->Util()->PrintErrors())
            return false;

        $sql  =" UPDATE office_resource 
                 SET status ='Baja',
                     motivo_baja='".$this->getMotivoBaja()."',
                     usuario_baja='".$_SESSION['User']['username']."',
                     fecha_ultima_modificacion = '".date("Y-m-d H:i:s")."', 
                     fecha_baja='".date('Y-m-d H:i:s')."' WHERE office_resource_id = '".$this->getId()."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
        $this->deleteAllResponsablesResource();
        $this->Util()->setError(0,"complete","Se ha realizado la baja del registro");
        $this->Util()->PrintErrors();

        return true;    }
    function deleteAllResponsablesResource(){
        if(!$this->getId())
            return false;

       $sql = "UPDATE responsables_resource_office
                SET 
                status = 'Baja',
                fecha_liberacion_responsable = '".date("Y-m-d")."',
                fecha_ultima_modificacion = '".date("Y-m-d H:i:s")."', 
                usuario_modificador = '".$_SESSION['User']['username']."'
                WHERE office_resource_id = '".$this->getId()."' and status = 'Activo'
                                 
              ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
    }
    function CleanResponsables()
    {
        unset($_SESSION["responsables_resource"]);
    }
}