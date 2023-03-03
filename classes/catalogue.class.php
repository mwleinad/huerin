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

    function ListClasificacion() {
        $this->Util()->DB()->setQuery("select * from tipo_clasificacion where fecha_eliminado is null order by nombre asc ");
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
    function ListAssociated() {
        $this->Util()->DBProspect()->PrepareStmtQuery("select * from associated where 1 order by name asc");
        return $this->Util()->DBProspect()->GetStmtResult();
    }
    function ListRegimen($tax_purpose = false) {
        $where = "";
        $params = [];
        if($tax_purpose) {
            $where .= " and tax_purpose in(?, '')";
            array_push($params, ['type' =>'s', 'value' => $tax_purpose]);
        }
        $this->Util()->DBProspect()->PrepareStmtQuery("select * from regimen where 1 $where order by name asc ", $params);
        return $this->Util()->DBProspect()->GetStmtResult();
    }
    function DefaultSelectedRegimen($id, $tax_purpose) {
        $params = [];
        array_push($params, ['type' =>'i', 'value' => (int)$id]);
        array_push($params, ['type' =>'s', 'value' => $tax_purpose]);
        $this->Util()->DBProspect()->PrepareStmtQuery("select * from regimen where id = ? and tax_purpose in (?, '')", $params);
        return $this->Util()->DBProspect()->GetStmtRow();
    }

    function ListUsoCFDI() {
        $this->Util()->DB()->setQuery("select * from c_UsoCfdi where 1 order by c_UsoCfdi asc ");
        return $this->Util()->DB()->GetResult();
    }
}
