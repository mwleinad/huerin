<?php
namespace trazzos\cfdi\Xml;

use DomDocument;


class NodoEmisor {

    public $data;
    private $totales;
    private $tipoDeCambio;

    private $horasExtraImporte;
    private $totalPercepciones;
    private $totalDeducciones;
    private $totalOtrosPagos;

    private $miEmpresa;
    private $nodosConceptos;
    //TODO move the xml to an object to make it global
    private $xml;
    private $root;
    private $cfdisRelacionados;
    private $emisor;
    private $receptor;
    private $totalImpuestosTrasladados = 0;
    private $totalImpuestosRetenidos = 0;
    private $totalImpuestosLocales = 0;

    private $trasladosGlobales;
    private $retencionesGlobales;

    private $tipoComprobante;

    private $complementos;
    private $impuestosLocales;
    private $impuestoLocal;

    private $pagos;

    private $cfdiUtil;
    private $comprobantePago;
    private $uuidRelacionado;

    //TODO might be able to separate each node into its own class to make it easier to organize
    public function __construct()
    {
        $this->cfdiUtil = new CfdiUtil();
        $this->comprobantePago = new ComprobantePago();
    }

    private function buildNodoRoot(DomDocument $xml, $totales, $miEmpresa) {

        $root = $xml->createElement("cfdi:Comprobante");
        $root = $xml->appendChild($this->root);

        $root->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd");
        $root->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd/3");
        $root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");

        if($totales['porcentajeISH'] > 0){
            $this->root->setAttribute("xmlns:implocal", "http://www.sat.gob.mx/implocal");
        }

        if($miEmpresa['donatarias'] == "Si"){
            $this->root->setAttribute("xmlns:implocal", "http://www.sat.gob.mx/donat");
        }

        if($this->isPago()) {
            $root->setAttribute("xmlns:pago10", "http://www.sat.gob.mx/Pagos");
        }

        $this->tipoDeCambio = $this->Util()->CadenaOriginalVariableFormat($this->data["tipoDeCambio"], true,false, true);
        if($this->data["fromNomina"])
        {
            $this->fromNominaChanges();
        }


    }

}
?>
