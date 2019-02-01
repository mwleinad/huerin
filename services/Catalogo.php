<?php

class Catalogo extends Main {

    function formasDePago() {
        $this->Util()->DB()->setQuery("
			SELECT * FROM c_FormaPago
			ORDER BY descripcion");
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    function metodosDePago() {
        $this->Util()->DB()->setQuery("
			SELECT * FROM c_MetodoPago
			ORDER BY descripcion");
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    function usoCfdi() {
        $this->Util()->DB()->setQuery("
			SELECT * FROM c_UsoCfdi
			ORDER BY descripcion");
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }

    function tipoRelacion() {
        $this->Util()->DB()->setQuery("
			SELECT * FROM c_TipoRelacion
			ORDER BY descripcion");
        $result = $this->Util()->DB()->GetResult();

        return $result;
    }
    function getFormaPagoByClave($clave){
        $this->Util()->DB()->setQuery("
			SELECT * FROM c_FormaPago WHERE c_FormaPago = '".$clave."' ");
        $row = $this->Util()->DB()->GetRow();

        return $row;
    }
    function getMetodoPagoByClave($clave){
        $this->Util()->DB()->setQuery("
			SELECT * FROM c_MetodoPago WHERE c_MetodoPago = '".$clave."' ");
        $row = $this->Util()->DB()->GetRow();

        return $row;
    }

}


?>