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
    function resetEmisor(){
        $this->emisor=[];
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
        $this->setClaveFacturador(trim($this->currentContract['facturador']));
        $currentRfc = $this->getInfoRfcByClaveFacturador();
        if(!$currentRfc)
            return false;

        $this->setEmpresaId($currentRfc['empresaId']);
        $this->setRfcId($currentRfc['rfcId']);
        $this->documentName = "Factura";

        $currentRfc["rfc"] = trim(str_replace("-", "", $currentRfc["rfc"]));
        $currentRfc["rfc"] = str_replace(" ", "", $currentRfc["rfc"]);
        $this->emisor =  $currentRfc;
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
        $sql = "select a.*,b.nameContact as cliente,b.noFactura13 from contract a
                inner join customer b on a.customerId = b.customerId
                where b.active ='1' and a.activo='Si' 
                and facturador != 'Efectivo' $filtro and a.lastProcessInvoice<'$firstDayCurrentMonth' order by a.customerId asc limit 50";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    function ProcessIfIsRif($serv,$date){
        $mes =  (int)date('m',strtotime($date));
        $year =  (int)date('Y',strtotime($date));
        if($serv["tipoServicioId"]==RIF||$serv["tipoServicioId"]==RIFAUDITADO){
            if($mes%2==0)
                return false;

            $sql ="SELECT comprobanteId FROM  comprobante WHERE procedencia='fromRifNoInstance' AND servicioId='".$serv['servicioId']."'
                   AND YEAR(fecha)='".$year."' AND MONTH(fecha)='".$mes."' ";
            $this->Util()->DB()->setQuery($sql);
            $exist =  $this->Util()->DB()->GetSingle();
            $ins["instanciaServicioId"] = 0;
            $ins["comprobanteId"] = $exist;
            $ins["date"] = $this->Util->getFirstDate(date("Y-m-d"));
            $ins["isRifNoInstance"] =  true;
            return $ins;
        }
         return false;   
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
    function GetFilterServicesByContract(){
        $this->resetServiciosToConceptos();
        $id =  $this->currentContract["contractId"];
        $sql = "select a.*,b.claveSat,b.nombreServicio from servicio a
                inner join tipoServicio b on a.tipoServicioId = b.tipoServicioId 
                where a.contractId='$id' and b.status='1' 
                and a.status in('activo','bajaParcial')
                and a.costo>0 ";
       $this->Util()->DB()->setQuery($sql);
       $results =  $this->Util()->DB()->GetResult();
       foreach($results as $item){
           if(!$this->Util()->isValidateDate($item["inicioFactura"],'Y-m-d'))
                continue;

           $firstDayCurrentDate = $this->Util()->getFirstDate(date("Y-m-d"));
           $firstDayInicioFactura = $this->Util()->getFirstDate($item["inicioFactura"]);
           if($firstDayInicioFactura>$firstDayCurrentDate)
                continue;

                
           $instancia = $this->GetCurrentWorkflow($item["servicioId"],date('Y-m-d'));
           $instancia =  !$instancia?$this->ProcessIfIsRif($item,date('Y-m-d')):$instancia;
           if(!$instancia)
                continue;
           
           if($instancia["comprobanteId"])
                continue; 

           if($item["status"]=="bajaParcial"){
               if(!$this->Util()->isValidateDate($item["lastDateWorkflow"],'Y-m-d'))
                continue;

               $firstDayLastDateWorkflow = $this->Util()->getFirstDate($item["lastDateWorkflow"]);
               if($firstDayCurrentDate>$firstDayLastDateWorkflow)
                continue;
            } 
           $item["workflowId"] = $instancia["instanciaServicioId"];
           $item["date"] = $instancia["date"];
           $item["isRifNoInstance"] = $instancia["isRifNoInstance"];
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
        $this->data["NumCtaPago"] = $this->currentContrato["noCuenta"];
        
        $this->data['userId'] = $this->currentContract["contractId"];
        $this->data['format'] = 'generar';
        $this->data['metodoDePago'] = 'PPD';
        $this->data['cfdiRelacionadoSerie'] = null;
        $this->data['cfdiRelacionadoFolio'] = null;
        $this->data['tipoRelacion'] = '04';
        $this->data['usoCfdi'] = 'G03';
            
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
        $this->data["procedencia"] = $this->month13?"manual":$this->data["procedencia"];
        $this->data['isRifNoInstance'] = $this->rifNoInstance;
    }
    function GenerateConceptos(){
        global $months;
        $conceptos = [];
        foreach($this->getServiciosToConceptos() as $item){
             if($item["isRifNoInstance"]&&!$this->month13){
                $this->isRifNoInstance();
                $this->data["servicioId"] = $item["servicioId"];
                $this->data["fechaRif"] = $item["date"];
            }
            if($item["workflowId"])
                $this->workflows[] = $item["workflowId"];

            $iva = $item["costo"] * ($this->emisor["iva"] / 100);
            $subtotal += $item["costo"] + $iva;
            $fecha = explode("-", $item["date"]);
            $fechaText = $this->month13?" 13 del ".$fecha["0"]:" DE ".$months[$fecha[1]]." del ".$fecha["0"];
            $descripcion = $item["nombreServicio"]." CORRESPONDIENTE AL MES ".$fechaText;
            if($this->Util()->ValidateOnlyNumeric($item["claveSat"],""))
                $claveProdServ =  trim($item['claveSat']);
                else
                $claveProdServ =  84111500;

            $cad = [];
            $cad["noIdentificacion"] = $item[""];
            $cad["cantidad"] = 1;
            $cad["unidad"] = "No Aplica";
            $cad["valorUnitario"] = $item["costo"];
            $cad["importe"] = $item["costo"];
            $cad["excentoIva"] = "no";
            $cad["descripcion"] = $descripcion;
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
        $this->setEmisor();
        $this->setReceptor();
        $this->GetFilterServicesByContract();
        if(!count($this->getServiciosToConceptos()))
            return false;

        $this->setMonth13(false);
        $_SESSION["conceptos"] = $this->GenerateConceptos();
        $this->GenerateArrayData();
        $result = $this->Generar($this->data);
        $this->isCreatedInvoice(true);
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

        if(!count($this->getServiciosToConceptos()))
            return false;

        $this->setMonth13(true);
        $_SESSION["conceptos"] = $this->GenerateConceptos();
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
            $this->resetEmisor();
            $this->setMonth13(false);
            $this->setProcesoRealizado(true);
            $this->resetLogString();
            $this->setInitTimeExecution();
            $this->isCreatedInvoice(false);
            $this->resetWorkflows();
            $this->setCurrentContract($contrato);

            if(!$this->Util->ValidateRfc($this->currentContract["rfc"]))
                continue;

            $this->CreateInvoice();
            if($contrato["noFactura13"]=="No"){
                $this->CreateInvoice13();
            }

    
            $this->ChangeLastProcessInvoice();
            $this->GenerateSendLog();
        }
    }
    function ChangeLastProcessInvoice(){
        if(!$this->procesoRealizado)
            return false;

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
        $file = DOC_ROOT."/sendFiles/logInvoices.txt";
        $open = fopen($file,"w");
        if ( $open ) {
            fwrite($open,$entry);
            fclose($open);
            //enviar por correo el log solo si se crearon facturas
            $sendmail = new SendMail;
            $sendmail->Prepare('LOG INVOICES','Logs invoices','isc061990@outlook.com','HBKRUZPE',$file,'logInvoices.txt','','',FROM_MAIL);
        }
    }
}