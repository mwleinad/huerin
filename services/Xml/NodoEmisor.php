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

    public function build(DomDocument $xml) {
        $this->emisor = $this->xml->createElement("cfdi:Emisor");
        $this->emisor = $this->root->appendChild($this->emisor);

        $emisorData = array(
            "Rfc"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoEmisor"]["rfc"]["rfc"],false,false),
            "Nombre"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoEmisor"]["rfc"]["razonSocial"],false,false),
            "RegimenFiscal"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoEmisor"]["rfc"]["regimenFiscal"],false,false)
        );

        $this->CargaAtt($this->emisor, $emisorData);

        return $xml;
    }

}
?>
