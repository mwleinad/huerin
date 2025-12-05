<?php
class InvoiceService extends Cfdi{
    private $serviciosToConceptos = [];
    private $rifNoInstance =  false;
    private $data = [];
    private $workflows= [];
    private $emisor;
    private $receptor;
    private $documentName;
    private $currentContract;
    private $initTimeExecution;
    private $logString;
    private $createdInvoice;
    private $month13=false;
    private $procesoRealizado;
    function getServiciosToConceptos(){
        return $this->serviciosToConceptos;
    }
    function isRifNoInstance(){
        $this->rifNoInstance=true;
    }
    function resetWorkflows(){
        $this->workflows=[];
    }
    function resetData(){
        $this->data=[];
    }
    function resetEmisor(){
        $this->emisor=[];
    }
    function resetReceptor(){
        $this->receptor=[];
    }
    function resetServiciosToConceptos(){
        $this->serviciosToConceptos=[];
    }
    function resetLogString(){
        $this->logString = "";
    }
    function setCurrentContract($contrato){
        $this->currentContract=$contrato;
    }
    function setInitTimeExecution(){
        $this->initTimeExecution = date("d-m-Y").' a las '.date('H:i:s');
    }
    function isCreatedInvoice($val){
        $this->createdInvoice =  $val;
    }
    function setMonth13($val){
        $this->month13 =  $val;
    }
    function setProcesoRealizado($val){
        $this->procesoRealizado =  $val;
    }
    function setEmisor(){
        $this->setClaveFacturador($this->currentContract['facturador']);
        $currentRfc = $this->getInfoRfcByClaveFacturador();
        if(!$currentRfc)
            return false;

        $this->setEmpresaId($currentRfc['empresaId']);
        $this->setRfcId($currentRfc['rfcId']);
        $this->documentName = "Factura";

        $currentRfc["rfc"] = trim(str_replace("-", "", $currentRfc["rfc"]));
        $currentRfc["rfc"] = str_replace(" ", "", $currentRfc["rfc"]);
        $this->emisor =  $currentRfc;
        return  true;
    }
    function setReceptor(){
        $rfcReceptor = str_replace("-", "", trim($this->currentContract["rfc"]));
        $rfcReceptor = str_replace(" ", "",$rfcReceptor);
        $this->receptor = array(
            "userId" => $this->currentContract["contractId"],
            "empresaId" => $this->getEmpresaId(),
            "rfcId" => $this->emisor["rfcId"],
            "rfc" => $rfcReceptor,
            "nombre" => $this->currentContract["name"],
            "calle" => $this->currentContract["address"],
            "noExt" => $this->currentContract["noExtAddress"],
            "noInt" => $this->currentContract["noIntAddress"],
            "colonia" => $this->currentContract["coloniaAddress"],
            "municipio" => $this->currentContract["municipioAddress"],
            "cp" => $this->currentContract["cpAddress"],
            "estado" => $this->currentContract["estadoAddress"],
            "localidad" => $this->currentContract["municipioAddress"],
            "referencia" => "",
            "pais" => $this->currentContract["paisAddress"],
            "email" => $this->currentContract["emailContactoAdministrativo"],
            "telefono" => $this->currentContract["telefonoContactoAdministrativo"],
            "password" => ""
        );
    }
    function GetContracts($id=0){
        $firstDayCurrentMonth = $this->Util()->getFirstDate(date("Y-m-d"));
        $filtro = "";
        if($id){
            $filtro .=" and a.customerId='$id' ";
        }
        $sql = "select a.*,b.nameContact as cliente,b.noFactura13,
                (SELECT count(id) FROM attempt_create_invoice 
                 WHERE contract_id = a.contractId 
                 AND date_format(fecha, '%Y-%m-%d')=date_format(now(), '%Y-%m-%d')) intento_dia
                FROM contract a
                inner join customer b on a.customerId = b.customerId
                where b.active ='1' and a.activo='Si' 
                and facturador != 'Efectivo' $filtro and a.lastProcessInvoice<'$firstDayCurrentMonth'  HAVING intento_dia < 1 order by a.customerId asc limit 15";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    function GetCurrentWorkflow($id,$date){
        $mes =  date('m',strtotime($date));
        $year =  date('Y',strtotime($date));
        $sql = "select instanciaServicioId,date,comprobanteId from instanciaServicio 
                where servicioId='$id' and comprobanteId<=0 and status!='baja' 
                and month(date)='$mes' and year(date)='$year' ";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetRow();
    }
    function GetExistsFacturaActual($servicioId, $contractId, $date) {
       $sql = "select conceptoId from concepto sa
                inner join comprobante sb ON sa.comprobanteId = sb.comprobanteId
                where sa.servicioId='$servicioId' 
                and sa.fechaCorrespondiente = '".$date."'  
                and sb.userId = '".$contractId."'
                and (sb.status = '1' or (sb.status='0' && sb.motivoCancelacionSat='03'))
                order by sa.conceptoId desc limit 1";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetSingle();
    }

    private function validateIfGenerateUniqueInvoice($serv) {
        if((int)$serv['uniqueInvoice']) {
            $query = "select instanciaServicioId, comprobanteId from instanciaServicio
                      where servicioId = '".$serv['servicioId']."' and comprobanteId > 0 LIMIT 1";
            $this->Util()->DB()->setQuery($query);
            $row =  $this->Util()->DB()->GetRow();
            return $row  ? true : false;
        }
       return false;
    }

    function getRangoFechaByPeriodicidad($periodicidad, $fif) {
        $suma_periodicidad = "";
        $fechas = [];
        switch(strtolower($periodicidad)) {
            case 'mensual': $suma_periodicidad = ' +1 month '; break;
            case 'bimestral': $suma_periodicidad = ' +2 month '; break;
            case 'trimestral': $suma_periodicidad = ' +3 month '; break;
            case 'cuatrimestral': $suma_periodicidad = ' +4 month '; break;
            case 'semestral': $suma_periodicidad = ' +6 month '; break;
            case 'anual': $suma_periodicidad = ' +12 month '; break;
        }
        $fif_control = $fif;
        while ($fif_control <= date('Y-m-d')) {
            array_push($fechas, $fif_control);
            $fif_control = date('Y-m-d', strtotime($fif_control.$suma_periodicidad));
        }
        return $fechas;
    }

    function GetFilterServicesByContract($validateInstance =  true) {
        $this->resetServiciosToConceptos();
        $id =  $this->currentContract["contractId"];
        //revisar empresa si presta datos para facturacion
        $sqlRev = "select contractId from contract where alternativeRzId = '$id' 
                   and createSeparateInvoice = 0 and useAlternativeRzForInvoice = 1 ";
        $this->Util()->DB()->setQuery($sqlRev);
        $res = $this->Util()->DB()->GetResult();

        $childs = $res ? $this->Util()->ConvertToLineal($res, 'contractId') : [];

        array_push($childs, $id);
        $strId =  implode(',', $childs);
        $sql = "select a.*,b.claveSat,b.nombreServicio, b.uniqueInvoice, b.periodicidad,b.concepto_mes_vencido
                from servicio a
                inner join tipoServicio b on a.tipoServicioId = b.tipoServicioId 
                where a.contractId in($strId) and b.status='1' 
                and a.status in('activo','bajaParcial') and week(inicioFactura) is not null
                and a.costo>0 ";
       $this->Util()->DB()->setQuery($sql);
       $results =  $this->Util()->DB()->GetResult();
       $servicesIn = [];
       $services = [];
       foreach($results as $item){
           if(!$this->Util()->isValidateDate($item["inicioFactura"],'Y-m-d'))
                continue;
           $firstDayCurrentDate = $this->Util()->getFirstDate(date("Y-m-d"));
           $firstDayInicioFactura = $this->Util()->getFirstDate($item["inicioFactura"]);
           if($firstDayInicioFactura>$firstDayCurrentDate)
                continue;

           $fecha_tope_first_day = $firstDayCurrentDate;
           if($validateInstance) {
               if (strtolower($item['periodicidad']) == 'eventual' || (int)$item['uniqueInvoice'] === 1) {
                   $fecha_tope =  $item['inicioFactura'];
               } else {
                    $currentPeriodicidad = $item['periodicidad'];
                    // rifs es bimestral pero factura se genera mensual.
                   if($item["tipoServicioId"] == RIF||$item["tipoServicioId"] == RIFAUDITADO)
                       $currentPeriodicidad = 'mensual';

                   $fechas = $this->getRangoFechaByPeriodicidad($currentPeriodicidad, $firstDayInicioFactura);
                   $fecha_tope = end($fechas);
               }
               $fecha_tope_first_day = $this->Util()->getFirstDate($fecha_tope);
               // que los conceptos con fechas anteriores a < 2022-03-01 ya no se generen por que se supone ya estan creadas.
               if($fecha_tope_first_day < '2022-03-01')
                   continue;
               // asegurarse que eventuales y facturas de unica ocasion posteriores no se refacturen con la nueva modalidad.
               if ((strtolower($item['periodicidad']) == 'eventual' || (int)$item['uniqueInvoice'] === 1) && $fecha_tope_first_day < '2022-03-01') {
                   continue;
               }
               $existFactura =  $this->GetExistsFacturaActual($item['servicioId'], $this->currentContract["contractId"], $fecha_tope_first_day);

               if($existFactura)
                   continue;
            }

           if($item["status"] == "bajaParcial"){
               if(!$this->Util()->isValidateDate($item["lastDateWorkflow"],'Y-m-d'))
                continue;

               $firstDayLastDateWorkflow = $this->Util()->getFirstDate($item["lastDateWorkflow"]);
               if($firstDayCurrentDate>$firstDayLastDateWorkflow)
                continue;
           }
           // remove:: comprobar factura unica ocasion, quitar si no se seguira validando por instancia creada
           // no deberia llegar aca los de unica ocasion, se deja de momento.
           if($this->validateIfGenerateUniqueInvoice($item))
              continue;

           $item["date"] = $fecha_tope_first_day;
           $this->serviciosToConceptos[] = $item;
       }
    }

    function GenerateArrayData(){
        $this->data["condicionesDePago"] = "";
        $this->data["tasaIva"] = $this->emisor["iva"];
        $this->data["tiposDeMoneda"] = "MXN";
        $this->data["porcentajeRetIva"] = 0;
        $this->data["porcentajeDescuento"] = 0;
        $this->data["tipoDeCambio"] = 1.00;
        $this->data["porcentajeRetIsr"] = 0;
        $this->data["porcentajeIEPS"] = 0 ;
        $this->data["formaDePago"] = '99';
        $this->data["NumCtaPago"] = $this->currentContract["noCuenta"];

        $this->data['userId'] = $this->currentContract["contractId"];
        $this->data['format'] = 'generar';
        $this->data['metodoDePago'] = 'PPD';
        $this->data['cfdiRelacionadoSerie'] = null;
        $this->data['cfdiRelacionadoFolio'] = null;
        $this->data['tipoRelacion'] = '04';

        $vs = new User;
        $vs->setUserId($this->currentContract['contractId']);
        $dataInvoice = $vs->GetUserForInvoice();

        $this->data['usoCfdi'] = strlen($dataInvoice['claveUsoCfdi']) <= 0 ? 'G03' :  $dataInvoice['claveUsoCfdi'];
        if(in_array(intval($dataInvoice['regimenFiscal']), [616,605]) && VERSION_CFDI === 4.0)
            $this->data['usoCfdi'] = 'S01';

        $sql  ="SELECT * FROM serie WHERE rfcId = '".$this->getRfcId()."' and tiposComprobanteId=1
                ORDER BY serieId ASC LIMIT 1";
        $this->Util()->DB()->setQuery($sql);
        $serie = $this->Util()->DB()->GetRow();
        $this->data["serie"] =array
        (
            "serieId" => $serie["serieId"],
            "serie" => $serie["serie"],
            "empresaId" => $serie["empresaId"],
            "tiposComprobanteId" => $serie["tiposComprobanteId"],
            "lugarDeExpedicion" => $serie["lugarDeExpedicion"],
            "noCertificado" => $serie["noCertificado"],
            "email" => $serie["email"],
            "consecutivo" => $serie["consecutivo"],
            "rfcId" => $serie["rfcId"]
        );
        $this->data["tiposComprobanteId"] = $this->data["serie"]["tiposComprobanteId"]."-".$this->data["serie"]['serieId'];

        $this->data["comprobante"] = array
        (
            "tiposComprobanteId" => 1,
            "nombre" => $this->documentName,
            "tipoDeComprobante" => "ingreso"
        );
        $this->data["nodoEmisor"]["rfc"] = array
        (
            "rfcId" => $this->emisor["rfcId"],
            "empresaId" => $this->getEmpresaId(),
            "regimenFiscal" => $this->emisor["regimenFiscal"],
            "rfc" => $this->emisor["rfc"],
            "razonSocial" => $this->emisor["razonSocial"],
            "pais" => $this->emisor["pais"],
            "calle" => $this->emisor["calle"],
            "noExt" => $this->emisor["noExt"],
            "noInt" => $this->emisor["noInt"],
            "colonia" => $this->emisor["colonia"],
            "localidad" => $this->emisor["localidad"],
            "municipio" => $this->emisor["municipio"],
            "ciudad" => $this->emisor["ciudad"],
            "referencia" => $this->emisor["referencia"],
            "estado" => $this->emisor["estado"],
            "cp" => $this->emisor["cp"],
            "activo" => $this->emisor["activo"],
            "main" => $this->emisor["main"]
        );
        $this->data["receptor"] = $this->receptor;
        $this->data["procedencia"] = $this->rifNoInstance?"rifWhithoutInstance":"whithInstance";
        $this->data["procedencia"] = $this->month13 ? "manual" :$this->data["procedencia"];
        $this->data['isRifNoInstance'] = $this->rifNoInstance;
    }

    function GenerateConceptos($fromManual = false){
        global $monthsComplete;
        $conceptos = [];
        foreach($this->getServiciosToConceptos() as $item){
            // si seesta generando mes 13 no incluir los conceptos de unica ocasion o eventuales
            if($this->month13 && ((int)$item["uniqueInvoice"] === 1 || strtolower($item["periodicidad"]) === 'eventual'))
                continue;

            if($item["isRifNoInstance"]&&!$this->month13){
                $this->isRifNoInstance();
                $this->data["servicioId"] = $item["servicioId"];
                $this->data["fechaRif"] = $item["date"];
            }

            if($item["workflowId"])
                $this->workflows[] = $item["workflowId"];

            $iva = $item["costo"] * ($this->emisor["iva"] / 100);
            $subtotal += $item["costo"] + $iva;


            $fecha_real_correspondiente =(int)$item['concepto_mes_vencido'] === 1
            ? date("Y-m-d",strtotime($item['date']." - 1 month"))
            : $item['date'] ;

            $fecha_corriente_explode = explode("-", $item['date']);
            $fecha_explode = explode("-", $fecha_real_correspondiente);
            if((int)$item['concepto_mes_vencido'] === 1) {
                $prefix = "HONORARIOS DE ". strtoupper($monthsComplete[$fecha_corriente_explode[1]]). " " . $fecha_corriente_explode[0];
                $sufix = $this->month13
                    ? "MES 13 DEL " . $fecha_explode[0]
                    : "DE " . strtoupper($monthsComplete[$fecha_explode[1]]) . " " . $fecha_explode[0];
                $descripcion = " CORRESPONDIENTES A ". trim($item["nombreServicio"]) . " " . $sufix;
                $descripcion = $prefix.$descripcion;
            } else {
                $sufix = $this->month13
                    ? "13 DEL " . $fecha_explode[0] :
                    "DE " . strtoupper($monthsComplete[$fecha_explode[1]]) . " " . $fecha_explode[0];
                $descripcion = trim($item["nombreServicio"]) . " CORRESPONDIENTE AL MES " . $sufix;
            }

            if($this->Util()->ValidateOnlyNumeric($item["claveSat"],""))
                $claveProdServ =  trim($item['claveSat']);
                else
                $claveProdServ =  84111500;

            $cad = [];
            $cad["noIdentificacion"] = $item["tipoServicioId"];
            $cad["servicioId"] = $item["servicioId"];
            $cad["fechaCorrespondiente"] = $item['date'];
            $cad["cantidad"] = 1;
            $cad["unidad"] = "No Aplica";
            $cad["valorUnitario"] = $item["costo"];
            $cad["importe"] = $item["costo"];
            $cad["excentoIva"] = "no";
            $cad["descripcion"] = $descripcion;
            $cad["nombreServicioOculto"] = trim($item["nombreServicio"]);
            $cad["tasaIva"] = $this->emisor["iva"];
            $cad["claveProdServ"] = $claveProdServ;
            $cad["claveUnidad"] = "E48";
            $cad["importeTotal"] = $item["costo"];
            $cad["totalIva"] = $iva;
            $conceptos[] =$cad;
        }
        return $conceptos;
    }

    function CreateInvoice(){
        $_SESSION["conceptos"] = [];
        if(!$this->setEmisor()){
            $this->logString .=chr(13).chr(10)." Error al generar factura para ".$this->currentContract['name']." con rfc = ".$this->currentContract['rfc'].chr(13).chr(10);
            $this->logString .=chr(13).chr(10)."Emisor ".$this->currentContract['facturador']." no encontrado, verifique si esta activo".chr(13).chr(10);
            return false;
        }

        $this->setReceptor();
        $this->GetFilterServicesByContract();
        if(!count($this->getServiciosToConceptos()))
            return false;

        $this->isCreatedInvoice(true);
        $this->setMonth13(false);
        $_SESSION["conceptos"] = $this->GenerateConceptos();
        $this->GenerateArrayData();
        $result = $this->Generar($this->data, false, false);
        if(!$result){
            $this->setProcesoRealizado(false);
            $this->logString .=chr(13).chr(10)." Error al generar factura para ".$this->currentContract['name']." con rfc = ".$this->currentContract['rfc'].chr(13).chr(10);
            $this->logString .="ERROR PAC =".$_SESSION['errorPac'].chr(13).chr(10);
        } else {
            $last = $this->GetLastComprobante();
            $this->logString .="Se creo la factura ".$last['serie'].$last['folio']." para ".$this->currentContract['name']."con rfc = ".$this->currentContract['rfc'].chr(13).chr(10);
            if(!empty($this->workflows)){
                $idsImplode =  "0,".implode(",",$this->workflows);
                $sql = "UPDATE instanciaServicio SET comprobanteId = '".$last["comprobanteId"]."'
                        WHERE instanciaServicioId IN ($idsImplode)";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
                $this->logString .="Actualizada las intancias siguientes : ".chr(13).chr(10);
                $this->logString .=implode(",",$this->workflows).chr(13).chr(10);
            }
        }
    }

    function CreateInvoice13(){
        $_SESSION["conceptos"] = [];
        $firstDayCurrentDate = $this->Util()->getFirstDate(date("Y-m-d"));
        if(!$this->procesoRealizado)
            return false;

        if(date("m",strtotime($firstDayCurrentDate))!=12)
            return false;

        // volver a obtenr los servicios para facturar para filtrar eventuales o de unica ocasion para mes 13

        if(!count($this->getServiciosToConceptos()))
            return false;

        $this->setMonth13(true);
        $_SESSION["conceptos"] = $this->GenerateConceptos();
        if(!count($_SESSION["conceptos"]))
            return false;
        
        $this->GenerateArrayData();
        $result = $this->Generar($this->data);
        if(!$result){
            $this->logString .=chr(13).chr(10)." Error al generar factura 13 para ".$this->currentContract['name']." con rfc = ".$this->currentContract['rfc'].chr(13).chr(10);
            $this->logString .="ERROR PAC =".$_SESSION['errorPac'].chr(13).chr(10);
        }else{
            $last = $this->GetLastComprobante();
            $this->logString .="Se creo la factura correspondiente al mes 13 ".$last['serie'].$last['folio']." para ".$this->currentContract['name']."con rfc = ".$this->currentContract['rfc'].chr(13).chr(10);
        }
    }

    function GenerateInvoices($id=0){
        $contratos =  $this->GetContracts($id);
        foreach($contratos as $contrato){
            //si el contrato tiene createSeparateInvoice  == 0 se ignora
            //por que sera procesado por otra empresa
            $this->resetEmisor();
            $this->resetData();
            $this->resetReceptor();
            $this->setMonth13(false);
            $this->setProcesoRealizado(true);
            $this->resetLogString();
            $this->setInitTimeExecution();
            $this->isCreatedInvoice(false);
            $this->resetWorkflows();
            $this->setCurrentContract($contrato);

            if($contrato['useAlternativeRzForInvoice'] == '1' && $contrato['alternativeRzId'] > 0
               && $contrato['createSeparateInvoice'] == '0') {
                $this->ChangeLastProcessInvoice();
                continue;
            }

            if(!$this->Util->ValidateRfc($this->currentContract["rfc"]))
                continue;

            $this->CreateInvoice();
            if($contrato["noFactura13"] == "No"){
                $this->CreateInvoice13();
            }
            $this->ChangeLastProcessInvoice();
            $this->GenerateSendLog();
        }
    }

    function ChangeLastProcessInvoice(){

        global $sendmail;

        if(!$this->procesoRealizado) {

            $contractRep = new ContractRep();
            $encargados = $contractRep->encargadosArea($this->currentContract['contractId']);

            foreach($encargados ?? [] as $encargado) {
                $respon = explode("@",$encargado['email']);
                $dominio = $respon[1] ?? '';
                if($encargado['departamentoId'] == 21 && $this->Util()->ValidateEmail($encargado['email']) && $dominio =='braunhuerin.com.mx')
                    $encargadosFiltrados[] = $encargado;
            }
            $responsableCxc = $encargadosFiltrados[0] ?? [];

            $usaDatosAlternos = $this->currentContract['useAlternativeRzForInvoice'] == 1 ? "Si" : "No";
            $usaDatosAlternosRegistrado = $this->currentContract['alternativeRzId'] > 0;

            if ($usaDatosAlternos === 'Si') {
                if ($usaDatosAlternosRegistrado) {
                    $query = "SELECT 
                                name as nombre, 
                                rfc, 
                                cpAddress direccionFiscal,
                                claveUsoCfdi, 
                                regimenId,
                                type
                                from contract where contractId = '".$this->currentContract['alternativeRzId']."'
                                ";
                    $this->Util()->DB()->setQuery($query);
                    $datosFiscales = $this->Util()->DB()->GetRow();

                } else {
                    $datosFiscales['nombre'] = $this->currentContract['alternativeRz'] ?? '';
                    $datosFiscales['rfc'] = $this->currentContract['alternativeRfc'] ?? '';
                    $datosFiscales['direccionFiscal'] = $this->currentContract['alternativeCp'] ?? '';
                    $datosFiscales['claveUsoCfdi'] = $this->currentContract['alternativeUsoCfdi'] ?? '';
                    $datosFiscales['regimenId'] = $this->currentContract['alternativeRegimen'] ?? '';
                    $datosFiscales['type'] = $this->currentContract['alternativeType'] ?? '';
                }
            } else {
                $datosFiscales['nombre'] = $this->currentContract['name'];
                $datosFiscales['rfc'] = $this->currentContract['rfc'];
                $datosFiscales['direccionFiscal'] = $this->currentContract['cpAddress'] ?? '';
                $datosFiscales['claveUsoCfdi'] = $this->currentContract['claveUsoCfdi'] ?? '';
                $datosFiscales['regimenId'] = $this->currentContract['regimenId'] ?? '';
                $datosFiscales['type'] = $this->currentContract['type'] ?? '';
            }

            $errormsj = "Descripcion del error: ".$_SESSION['errorPac']." | ";
            $errormsj .= "Usa datos alternos: ".$usaDatosAlternos." | ";
            $errormsj .= "Nombre o Razón social: ".($datosFiscales['nombre'] ?? '')." | ";
            $errormsj .= "Tipo: ".($datosFiscales['type'] ?? '')." | ";
            $errormsj .= "RFC: ".($datosFiscales['rfc'] ?? '')." | ";
            $errormsj .= "Régimen fiscal: ".($datosFiscales['regimenId'] ?? '')." | ";
            $errormsj .= "Dirección fiscal: ".($datosFiscales['direccionFiscal'] ?? '')." | ";
            $errormsj .= "Uso de CFDI: ".($datosFiscales['claveUsoCfdi'] ?? '')." | ";

            $sql = 'INSERT INTO attempt_create_invoice(
                    contract_id,
                    nombre,
                    rfc,
                    error) VALUES (
                    "'.$this->currentContract['contractId'].'",
                    "'.$this->currentContract['name'].'",
                    "'.$this->currentContract['rfc'].'",
                    "'.$errormsj.'"
                    )';
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->InsertData();


            if ($responsableCxc) {
                $body  = "<pre>";
                $body .= "Ha ocurrido un error al intentar generar la factura  del mes corriente a la empresa ".$this->currentContract['name'].".<br><br>";
                $body .= "<strong>Descripcion del error </strong>:".utf8_decode($_SESSION['errorPac'])."<br><br>";
                $body .= "<strong>Datos fiscales que se utilizaron: </strong>:<br>";
                $body .= "<strong>Usa datos alternos: </strong>:".$usaDatosAlternos."<br>";
                $body .= "<strong>Nombre o Razon social: </strong> ".$datosFiscales['nombre']."<br>";
                $body .= "<strong>Tipo: </strong> ".$datosFiscales['type']."<br>";
                $body .= "<strong>RFC: </strong> ".$datosFiscales['rfc']."<br>";
                $body .= "<strong>Clave del regimen fiscal: </strong> ".$datosFiscales['regimenId']."<br>";
                $body .= "<strong>Direccion fiscal: </strong> ".$datosFiscales['direccionFiscal']."<br>";
                $body .= "<strong>Clave de Usco CFDI: </strong> ".$datosFiscales['claveUsoCfdi']."<br><br>";
                $body .= "Si la descripcion del error contiene el siguiente mensaje : Error al conectar con el PAC....., asegurese que en realidad no se haya generado la factura desde el apartado de <strong>Facturacion >> Comprobantes</strong>, antes de notificar a la administracion.<br>";
                $body .= "<br><br>Este correo se genero de manera automatica, favor de no responder.";
                $sendmail->Prepare("Error al generar factura automatica", $body,$responsableCxc['email'],$responsableCxc['name']);
                if (SEND_ERROR_FACT_AUTO_TO_DEV)
                    $sendmail->Prepare("Error al generar factura automatica", $body,EMAIL_DEV, "Desarrollador");
            }
            return false;
        }


        $currentDate = date("Y-m-d");
        $sql = "UPDATE contract SET lastProcessInvoice = '$currentDate'
                WHERE contractId='".$this->currentContract["contractId"]."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->UpdateData();
    }

    function GenerateSendLog(){
        if(!$this->createdInvoice)
            return false;

        $endTime = date("d-m-Y").' a las '.date('H:i:s');
        $entry = "Cron ejecutado desde ".$this->initTimeExecution." hasta $endTime Hrs.".chr(13).chr(10);
        $entry .=$this->logString;
        $fechaActual = str_replace("-", "_", date("Y-m-d"));
        $file = DOC_ROOT . "/logs/facturacion/automatico_" . $fechaActual . ".log";
        $dirname = dirname($file);
        if (!is_dir($dirname))
            mkdir($dirname, 0755, true);

        $open = fopen($file, "a+");
        if ($open) {
            fwrite($open, $entry);
            fclose($open);
        }
    }

    function getInfoInvoice ($id) {
        $sql  = "SELECT 
                    a.comprobanteId, 
                    date_format(a.fecha, '%Y-%m-%d') fecha,
                    a.serie,
                    a.folio,
                    b.noCuenta,
                    b.contractId,
                    b.facturador,
                    b.name,
                    b.address,
                    b.noExtAddress,
                    b.noIntAddress,
                    b.coloniaAddress,
                    b.municipioAddress,
                    b.cpAddress,
                    b.estadoAddress,
                    b.paisAddress,
                    b.emailContactoAdministrativo,
                    b.telefonoContactoAdministrativo  
                 FROM comprobante a
                 INNER JOIN contract b ON a.userId = b.contractId
                 WHERE a.comprobanteId = '".$id."'   
                 ";

        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetRow();
    }

    function sustituirFactura($beforeData) {

        $_SESSION["conceptos"] = [];
        $dataReturn = [];
        $currentDate = $this->Util()->getFirstDate(date('Y-m-d'));
        $beforeInvoiceDate  = $this->Util()->getFirstDate($beforeData['fecha']);
        $this->resetServiciosToConceptos();
        $this->resetEmisor();
        $this->resetData();
        $this->resetReceptor();

        if($beforeInvoiceDate !==$currentDate) {
            $this->Util()->setError(0,'error',  'No se puede refacturar meses anteriores, desde este apartado.');
            $dataReturn['res'] = false;
        }
        $sql = "SELECT serie, folio, timbreFiscal, date_format(fecha,'%d/%m/%Y') fecha FROM comprobante 
                WHERE status = '1' 
                AND parentId = '".$beforeData['comprobanteId']."' ";
        $this->Util()->DB()->setQuery($sql);
        $facturaHijo = $this->Util()->DB()->GetRow();

        if($facturaHijo) {
            $folio = $facturaHijo['serie'].$facturaHijo['folio'];
            $unserializeTimbre = unserialize($facturaHijo['timbreFiscal']);
            $msj = 'Ya existe un comprobante generado anteriormente relacionado a esta factura, con fecha : <strong> '.$facturaHijo['fecha'].'</strong>, folio: <strong>'. $folio.'</strong>';
            $msj .= ' y UUID: <strong>'.  $unserializeTimbre['UUID'].'</strong>';
            $this->Util()->setError(0,'error',  $msj);
            $dataReturn['uuid'] = $unserializeTimbre['UUID'];
            $dataReturn['res'] = false;
        }

        $sql = "SELECT COUNT(paymentId) total FROM payment 
                WHERE comprobanteId = '".$beforeData['comprobanteId']."'
                AND paymentStatus = 'activo' ";
        $this->Util()->DB()->setQuery($sql);
        $tienePagos = $this->Util()->DB()->GetSingle();

        if($tienePagos > 0){
            $this->Util()->setError(0, 'error', 'La factura que pretende cancelar tiene pagos aplicados.');
            $dataReturn['res'] = false;
        }

        $this->setCurrentContract($beforeData);
        if(!$this->setEmisor()){
            $this->Util()->setError(0, 'error', 'Error!, emisor no encontrado.');
            $dataReturn['res'] = false;
        }
        $this->setReceptor();
        $this->GetFilterServicesByContract(false);

        if(!count($this->getServiciosToConceptos())) {
            $this->Util()->setError(0, 'error', 'No se puede generar factura, no existen servicios.');
            $dataReturn['res'] = false;
        }
        if($this->Util()->PrintErrors())
            return $dataReturn;

        $this->setMonth13(false);
        $_SESSION["conceptos"] = $this->GenerateConceptos();
        $this->GenerateArrayData();
        $this->data['parent'] =  $beforeData['comprobanteId'];
        // aseguramos que se genere con relacion tipo 04 y pasar el identificador del cfdi relacionado para que
        //en el xml la inserte.
        $this->data['cfdiRelacionadoId'] = $beforeData['comprobanteId'];
        $this->data['tipoRelacion'] = '04';
        $idComprobante = $this->Generar($this->data, false, false);
        if(!$idComprobante){
            $this->Util()->setError(0, 'error', 'Error al generar factura, intente nuevamente.');
            $dataReturn['res'] = false;
            $this->Util()->PrintErrors();
            return $dataReturn;
        } else {
            $this->Util()->DB()->setQuery("SELECT timbreFiscal, serie, folio FROM comprobante 
                                                 WHERE comprobanteId ='".$idComprobante."'");
            $dataResult= $this->Util()->DB()->GetRow();
            $dataReturn['result'] = $dataResult;
            $this->Util()->setError(0,
                'complete',
                'Se ha generado la factura correctamente con folio '. $dataResult['serie'].$dataResult['folio']);
            $this->Util()->PrintErrors();
            $dataReturn['res'] = true;
            return $dataReturn;
        }
    }

    /*
     * Funciones para sustitucion manual
     */
    function getInfoInvoiceByFolio ($serie, $folio) {
      $sql  = "SELECT 
                    a.comprobanteId, 
                    date_format(a.fecha, '%Y-%m-%d') fecha,
                    a.serie,
                    a.folio,
                    a.tasaIva,
                    a.tiposComprobanteId, 
                    a.rfcId,
                    a.formaDePago,
                    a.metodoDePago,
                    a.status,
                    b.noCuenta,
                    b.contractId,
                    b.facturador,
                    b.name,
                    b.address,
                    b.noExtAddress,
                    b.noIntAddress,
                    b.coloniaAddress,
                    b.municipioAddress,
                    b.cpAddress,
                    b.estadoAddress,
                    b.paisAddress,
                    b.emailContactoAdministrativo,
                    b.telefonoContactoAdministrativo,
                    b.name razonSocial,
                    b.rfc,
                    b.contractId userId,
                    b.paisAddress pais,
                    b.address calle
                 FROM comprobante a
                 INNER JOIN contract b ON a.userId = b.contractId
                 WHERE lower(a.serie) = '".strtoupper(trim($serie))."' AND a.folio = '".trim($folio)."' 
                 AND  a.tiposComprobanteId = '1' 
                 ";

        $this->Util()->DB()->setQuery($sql);
        $row =  $this->Util()->DB()->GetRow();
        if($row) {
            $sql = "SELECT serieId FROM  serie WHERE serie = '".$row['serie']."'  AND rfcId = '".$row['rfcId']."'";
            $this->Util()->DB()->setQuery($sql);
            $serieId = $this->Util()->DB()->GetSingle();
            $row['setTipoComprobante'] = $row['tiposComprobanteId']."-".$serieId;

            //pending_cfdi_cancel si tiene un registro esta en proceso de cancelacion.
            $sqlp = "SELECT count(*) FROM pending_cfdi_cancel WHERE cfdi_id= '".$row['comprobanteId']."' AND deleted_at IS NULL AND status = '".CFDI_CANCEL_STATUS_PENDING."'";
            $this->Util()->DB()->setQuery($sqlp);
            $existProceso = $this->Util()->DB()->GetSingle();
            if($existProceso > 0)
                $row['status'] = '0';
        }


        return $row;
    }

    function getFullDataInvoiceByFolio($serie, $folio) {
        global $monthsComplete;
        if(!$serie)
            $this->Util()->setError(0, 'error', 'Ingrese serie');
        if($this->Util()->PrintErrors())
            return false;

        if(!$folio)
            $this->Util()->setError(0, 'error', 'Ingrese folio');
        if($this->Util()->PrintErrors())
            return false;
        $row = $this->getInfoInvoiceByFolio($serie, $folio);
        if(!$row)
            $this->Util()->setError(0, 'error', 'No se encontro información con los datos proporcionados, verifique si la factura esta activa.');
        else {
            if($row['status'] == '0')
                $this->Util()->setError(0, 'error', 'La factura ya se encuentra cancelada o en proceso de cancelación.');

        }
        if($this->Util()->PrintErrors())
          return false;
        $fecha_fact = date('Y-m-d', strtotime($row['fecha']));
        $fecha_fact_firts_day = $this->Util()->getFirstDate($fecha_fact);
        $tipoInner = $fecha_fact_firts_day >= '2022-02-01' ? " INNER JOIN " : " LEFT JOIN ";
        $sql = "SELECT sa.`cantidad`,
						sa.`unidad`,
						sa.`noIdentificacion`,
						sa.`descripcion`,
						sa.`valorUnitario`,
						sa.`excentoIva`,
						sa.`importe`,
						sa.`userId`,
						sa.`empresaId`,
						sa.`servicioId`,
						sa.`fechaCorrespondiente`,
                        sb.nombreServicio,
                        sb.claveSat,
                        sb.tipoServicioId,
                        sb.concepto_mes_vencido,
                        sb.uniqueInvoice
				FROM concepto sa
                ".$tipoInner." (SELECT a.servicioId, b.nombreServicio, b.claveSat,b.tipoServicioId,
                                b.concepto_mes_vencido, b.uniqueInvoice
                                FROM servicio a
                                INNER JOIN tipoServicio b ON a.tipoServicioId = b.tipoServicioId) sb
                ON sa.servicioId = sb.servicioId
                WHERE sa.comprobanteId = '".$row['comprobanteId']."'
				";

        $this->Util()->DB()->setQuery($sql);
        $dataConceptos = $this->Util()->DB()->GetResult();
        $dataConceptos = !is_array($dataConceptos) ? [] : $dataConceptos;
        $conceptos = [];
        foreach($dataConceptos as $item) {
            $iva = $item["valorUnitario"] * ($row["tasaIva"] / 100);
            if($fecha_fact_firts_day == '2022-02-01' && (int)$item['uniqueInvoice'] === 0)
                $item['fechaCorrespondiente'] = date("Y-m-d", strtotime($item['fechaCorrespondiente']." + 1 month"));

            $fecha_real_correspondiente = (int)$item['concepto_mes_vencido'] === 1
                ? date("Y-m-d",strtotime($item['fechaCorrespondiente']." - 1 month"))
                : $item['fechaCorrespondiente'] ;

            $fecha_corriente_explode = explode("-", $item['fechaCorrespondiente']);
            $fecha_explode = explode("-", $fecha_real_correspondiente);
            if((int)$item['concepto_mes_vencido'] === 1) {
                $prefix = "HONORARIOS DE ". strtoupper($monthsComplete[$fecha_corriente_explode[1]]). " " . $fecha_corriente_explode[0];
                $sufix = $this->month13
                    ? "MES 13 DEL " . $fecha_explode[0]
                    : "DE " . strtoupper($monthsComplete[$fecha_explode[1]]) . " " . $fecha_explode[0];
                $descripcion = " CORRESPONDIENTES A ".trim($item["nombreServicio"])." " . $sufix;
                $descripcion = $prefix.$descripcion;
            } else {
                $sufix = $this->month13
                    ? "13 DEL " . $fecha_explode[0] :
                    "DE " . strtoupper($monthsComplete[$fecha_explode[1]]) . " " . $fecha_explode[0];
                $descripcion = trim(item["nombreServicio"]) . " CORRESPONDIENTE AL MES " . $sufix;
            }

            /*$fecha = explode("-", $item['fechaCorrespondiente']);
            $fechaText = " DE ".$monthsComplete[$fecha[1]]." del ".$fecha["0"];
            $descripcion = $item["nombreServicio"]." CORRESPONDIENTE AL MES ".$fechaText;*/

            if($this->Util()->ValidateOnlyNumeric($item["claveSat"],""))
                $claveProdServ =  trim($item['claveSat']);
            else
                $claveProdServ =  84111500;
            // si es factura anterior a mes feb 2022 intentar encontrar los servicios por nombre
            if($fecha_fact_firts_day < '2022-02-01' && (int)$item['servicioId'] <= 0) {
                $descripcion_explode = explode('correspondiente', strtolower($item['descripcion']));
                $nombre_serv_extract = trim($descripcion_explode[0]);

                //revisar empresa si presta datos para facturacion
                $sqlRev = "select contractId from contract where alternativeRzId = '".$row['userId']."' 
                   and createSeparateInvoice = 0 and useAlternativeRzForInvoice = 1 ";
                $this->Util()->DB()->setQuery($sqlRev);
                $res = $this->Util()->DB()->GetResult();

                $childs = $res ? $this->Util()->ConvertToLineal($res, 'contractId') : [];
                $strId  =  implode(',', $childs);
                $sql    = "SELECT a.servicioId, a.nombreServicio,a.tipoServicioId FROM (
                                SELECT sa.servicioId,sa.contractId,sa.tipoServicioId, sb.nombreServicio FROM servicio sa
                                INNER JOIN tipoServicio sb ON sa.tipoServicioId = sb.tipoServicioId) a
                            WHERE a.contractId ='".$item['userId']."' AND a.nombreServicio LIKE '%".$nombre_serv_extract."%'  
                            ORDER BY a.servicioId DESC LIMIT 1 ";
                $this->Util()->DB()->setQuery($sql);
                $rowFind = $this->Util()->DB()->GetRow();
                if(!$rowFind && count($childs) > 0) {
                    $sql    = "SELECT a.servicioId, a.nombreServicio, a.tipoServicioId FROM (
                                SELECT sa.servicioId,sa.contractId,sa.tipoServicioId, sb.nombreServicio FROM servicio sa
                                INNER JOIN tipoServicio sb ON sa.tipoServicioId = sb.tipoServicioId) a
                            WHERE a.contractId IN(".$strId.") AND a.nombreServicio LIKE '%".$nombre_serv_extract."%'  
                            ORDER BY a.servicioId DESC LIMIT 1 ";
                    $this->Util()->DB()->setQuery($sql);
                    $rowFind = $this->Util()->DB()->GetRow();
                }
                if($rowFind) {
                    $item['servicioId'] = $rowFind['servicioId'];
                    $item['nombreServicio'] = $rowFind['nombreServicio'];
                    $item['tipoServicioId'] = $rowFind['tipoServicioId'];
                }
            }

            $cad = [];
            $cad["noIdentificacion"] = $item["tipoServicioId"];
            $cad["servicioId"] = $item['servicioId'];
            $cad["fechaCorrespondiente"] = $item['fechaCorrespondiente'];
            $cad["cantidad"] = 1;
            $cad["unidad"] = "No Aplica";
            $cad["valorUnitario"] = $item["valorUnitario"];
            $cad["importe"] = $item["valorUnitario"];
            $cad["excentoIva"] = "no";
            $cad["nombreServicio"] = $item['nombreServicio'];
            $cad["nombreServicioOculto"] = $item['nombreServicio'];
            $cad["descripcion"] = $fecha_fact >= '2022-02-01' ? $descripcion : $item['descripcion'];
            $cad["tasaIva"] = $row["tasaIva"];
            $cad["claveProdServ"] = $claveProdServ;
            $cad["claveUnidad"] = "E48";
            $cad["importeTotal"] = $item["valorUnitario"];
            $cad["totalIva"] = $iva;
            $conceptos[] =$cad;
        }
        $row['conceptos'] = $conceptos;

        return $row;
    }

    function getListaCoincidenciaServicioContrato($tipoServicio, $contrato) {
        $sql = "select contractId from contract where alternativeRzId = '" . $contrato . "' 
                   and createSeparateInvoice = 0 and useAlternativeRzForInvoice = 1 ";
        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();
        $childs = $res ? $this->Util()->ConvertToLineal($res, 'contractId') : [];
        array_push($childs, $contrato);
        $strId  =  implode(',', $childs);

        $sql  = "SELECT a.servicioId,b.name, c.nombreServicio,IF(WEEK(a.inicioFactura) is not null, date_format(a.inicioFactura, '%d-%m-%Y'), NULL) fif";
        $sql .= ", a.status, CASE a.status WHEN 'baja' THEN 'Baja' WHEN 'readonly' THEN 'Activo solo lectura' ";
        $sql .= "  WHEN 'activo' THEN 'Activo' WHEN 'bajaParcial' THEN 'Baja Temporal' END estatus ";
        $sql .= ", IF(a.status ='bajaParcial', date_format(a.lastDateWorkflow, '%d-%m-%Y'), null) as lastDateWorkflow";
        $sql .= ", IF(WEEK(a.inicioOperaciones) is not null, date_format(a.inicioOperaciones, '%d-%m-%Y'), null) fio FROM servicio a ";
        $sql .= "INNER JOIN contract b ON a.contractId = b.contractId ";
        $sql .= "INNER JOIN tipoServicio c ON a.tipoServicioId = c.tipoServicioId ";
        $sql .= "WHERE a.contractId IN(".$strId.") AND a.tipoServicioId = '".$tipoServicio."' ";

        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
}
