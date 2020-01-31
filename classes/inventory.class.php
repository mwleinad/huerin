<?php


class Inventory extends Articulo
{
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
                    $this->Util()->DB()->InsertData();
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
        $_SESSION['responsables_resource'][$key] = $tmp;

        $this->Util()->setError(0,"complete","Has agregado correctamente un registro");
        $this->Util()->PrintErrors();

        return  true;
    }
    public function  deleteResponsableFromArray($id){
        $current = $_SESSION['responsables_resource'][$id];
        if($current['responsable_resource_id']){
            $current['status'] = "Baja";
            $current['insertUpdate'] =  true;
            $_SESSION['responsables_resource'][$id] = $current;
        }else{
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