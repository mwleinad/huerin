<?php

class Catalogue extends Main
{
    function ListFilesExtension(){
        $this->Util()->DB()->setQuery('select * from mime_types order by name ASC');
        return $this->Util()->DB()->GetResult();
    }
    function EnumerateCatalogue($table = ""){
        if($table === "")
            return [];

        $sql  = "select * from $table where 1 ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    function EnumerateFromArrayLineal($source = []) {
        $new_source = [];
        foreach($source as $src) {
            $tmp['name'] = $src;
            array_push($new_source, $tmp);
        }
        return $new_source;
    }
    function ListSectores() {
        $this->Util()->DB()->setQuery("select * from sector where 1 order by name asc ");
        return $this->Util()->DB()->GetResult();
    }
    function ListSubsectores($sectorId = 0) {
        $where = "";
        $where .= " and sector_id = '$sectorId' ";
        $this->Util()->DB()->setQuery("select * from subsector where 1 $where order by name asc ");
        return $this->Util()->DB()->GetResult();
    }
    function ListActividadesComerciales($subsectorId = 0, $all = false) {
        $where = "";
        if(!$all)
            $where .= " and subsector_id = '$subsectorId' ";
        $this->Util()->DB()->setQuery("select * from actividad_comercial where 1 $where order by name asc ");
        return $this->Util()->DB()->GetResult();
    }

}