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
        $sql = "select a.*,b.nameContact as cliente,b.noFactura13 from contract a
                inner join customer b on a.customerId = b.customerId
                where b.active ='1' and a.activo='Si' 
                and facturador != 'Efectivo' $filtro and a.lastProcessInvoice<'$firstDayCurrentMonth' order by a.customerId asc limit 15";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    function ProcessIfIsRif($serv,$date){
        $mes =  (int)date('m',strtotime($date));
        $year =  (int)date('Y',strtotime($date));
        if($serv["tipoServicioId"] == RIF||$serv["tipoServicioId"] == RIFAUDITADO){
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
        $sql = "select a.*,b.claveSat,b.nombreServicio, b.uniqueInvoice, b.periodicidad
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

           $fecha_tope = $firstDayCurrentDate;
           if($validateInstance) {
               if (strtolower($item['periodicidad']) == 'eventual' || (int)$item['uniqueInvoice'] === 1) {
                   $realDate =  $item['inicioFactura'];
                   $fecha_tope =  $item['inicioFactura'];
               } else {
                    $currentPeriodicidad = $item['periodicidad'];
                    // rifs es bimestral pero factura se genera mensual.
                   if($item["tipoServicioId"] == RIF||$item["tipoServicioId"] == RIFAUDITADO)
                       $currentPeriodicidad = 'mensual';

                   $fechas = $this->getRangoFechaByPeriodicidad($currentPeriodicidad, $firstDayInicioFactura);
                   $fecha_tope = end($fechas);
                   $realDate = date('Y-m-d', strtotime($fecha_tope . " -1 month"));
               }
               $fecha_tope_first_day = $this->Util()->getFirstDate($fecha_tope);
               // asegurarse que eventuales y facturas de unica ocasion posteriores no se refacturen con la nueva modalidad.
               if ((strtolower($item['periodicidad']) == 'eventual' || (int)$item['uniqueInvoice'] === 1) && $fecha_tope_first_day < '2022-02-01') {
                   continue;
               }
               $existFactura =  $this->GetExistsFacturaActual($item['servicioId'], $this->currentContract["contractId"], $realDate);

               //$instancia   = !$instancia ? $this->ProcessIfIsRif($item, date('Y-m-d')) : $instancia;
               //if (!$instancia)
                 //   continue;

              // if((int)$instancia["comprobanteId"]>0)
                //   continue;
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
           if($this->validateIfGenerateUniqueInvoice($item))
              continue;

           //$item["workflowId"] = $instancia["instanciaServicioId"];
           $item["date"] = $fecha_tope;
           //$item["isRifNoInstance"] = $instancia["isRifNoInstance"];
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
        $this->data["procedencia"] = $this->month13 ? "manual" :$this->data["procedencia"];
        $this->data['isRifNoInstance'] = $this->rifNoInstance;
    }

    function GenerateConceptos($fromManual = false){
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

            if ($fromManual) {
                $fecha_real_correspondiente = date('Y-m-d');
            } else {
                $fecha_real_correspondiente = (strtolower($item['periodicidad']) == 'eventual' || (int)$item['uniqueInvoice'] === 1)
                ? $item['date']
                : date("Y-m-d",strtotime($item['date']." - 1 month"));
            }

            $fecha = explode("-", $fecha_real_correspondiente);
            $fechaText = $this->month13?" 13 del ".$fecha["0"]:" DE ".$months[$fecha[1]]." del ".$fecha["0"];
            $descripcion = $item["nombreServicio"]." CORRESPONDIENTE AL MES ".$fechaText;
            if($this->Util()->ValidateOnlyNumeric($item["claveSat"],""))
                $claveProdServ =  trim($item['claveSat']);
                else
                $claveProdServ =  84111500;

            $cad = [];
            $cad["noIdentificacion"] = $item["tipoServicioId"];
            $cad["servicioId"] = $item["servicioId"];
            $cad["fechaCorrespondiente"] = $fecha_real_correspondiente;
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
            $sendmail->Prepare('LOG INVOICES','Logs invoices',EMAIL_DEV,'HBKRUZPE',$file,'logInvoices.txt','','',FROM_MAIL);
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
            $msj = 'Ya existe un comprobante generado anteriormente con fecha : <strong> '.$facturaHijo['fecha'].'</strong>, folio: <strong>'. $folio.'</strong>';
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
            $sqlp = "SELECT count(*) FROM pending_cfdi_cancel WHERE cfdi_id= '".$row['comprobanteId']."' ";
            $this->Util()->DB()->setQuery($sqlp);
            $existProceso = $this->Util()->DB()->GetSingle();
            if($existProceso > 0)
                $row['status'] = '0';
        }


        return $row;
    }

    function getFullDataInvoiceByFolio($serie, $folio) {
        global $months;
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

        $tipoInner = $row['fecha'] >= '2022-02-01' ? " INNER JOIN " : " LEFT JOIN ";
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
                        sb.tipoServicioId
				FROM concepto sa
                ".$tipoInner." (select a.servicioId, b.nombreServicio, b.claveSat,b.tipoServicioId FROM servicio a
                            INNER JOIN tipoServicio b ON a.tipoServicioId = b.tipoServicioId) sb
                ON sa.servicioId = sb.servicioId
                WHERE sa.comprobanteId = '".$row['comprobanteId']."'
				";

        $this->Util()->DB()->setQuery($sql);
        $dataConceptos = $this->Util()->DB()->GetResult();
        $dataConceptos = !is_array($dataConceptos) ? [] : $dataConceptos;
        $conceptos = [];
        foreach($dataConceptos as $item){
            $iva = $item["valorUnitario"] * ($row["tasaIva"] / 100);
            $fecha = explode("-", $item['fechaCorrespondiente']);
            $fechaText = " DE ".$months[$fecha[1]]." del ".$fecha["0"];
            $descripcion = $item["nombreServicio"]." CORRESPONDIENTE AL MES ".$fechaText;
            if($this->Util()->ValidateOnlyNumeric($item["claveSat"],""))
                $claveProdServ =  trim($item['claveSat']);
            else
                $claveProdServ =  84111500;
            // si es factura anterior a mes feb 2022 intentar encontrar los servicios por nombre
            if($row['fecha '] < '2022-02-01' && (int)$item['servicioId'] <= 0) {
                $descripcion_explode = explode('correspondiente', strtolower($item['descripcion']));
                $nombre_serv_extract = trim($descripcion_explode[0]);

                //revisar empresa si presta datos para facturacion
                $sqlRev = "select contractId from contract where alternativeRzId = '".$row['userId']."' 
                   and createSeparateInvoice = 0 and useAlternativeRzForInvoice = 1 ";
                $this->Util()->DB()->setQuery($sqlRev);
                $res = $this->Util()->DB()->GetResult();

                $childs = $res ? $this->Util()->ConvertToLineal($res, 'contractId') : [];
                $strId  =  implode(',', $childs);
                $sql    = "SELECT a.servicioId, a.nombreServicio FROM (
                                SELECT sa.servicioId,sa.contractId, sb.nombreServicio FROM servicio sa
                                INNER JOIN tipoServicio sb ON sa.tipoServicioId = sb.tipoServicioId) a
                            WHERE a.contractId ='".$item['userId']."' AND a.nombreServicio LIKE '%".$nombre_serv_extract."%'  
                            ORDER BY a.servicioId DESC LIMIT 1 ";
                $this->Util()->DB()->setQuery($sql);
                $rowFind = $this->Util()->DB()->GetRow();
                if(!$rowFind && count($childs) > 0) {
                    $sql    = "SELECT a.servicioId, a.nombreServicio FROM (
                                SELECT sa.servicioId,sa.contractId, sb.nombreServicio FROM servicio sa
                                INNER JOIN tipoServicio sb ON sa.tipoServicioId = sb.tipoServicioId) a
                            WHERE a.contractId IN(".$strId.") AND a.nombreServicio LIKE '%".$nombre_serv_extract."%'  
                            ORDER BY a.servicioId DESC LIMIT 1 ";
                    $this->Util()->DB()->setQuery($sql);
                    $rowFind = $this->Util()->DB()->GetRow();
                }
                if($rowFind) {
                    $item['servicioId'] = $rowFind['servicioId'];
                    $item['nombreServicio'] = $rowFind['nombreServicio'];
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
            $cad["descripcion"] = $row['fecha'] >= '2022-02-01' ? $descripcion : $item['descripcion'];
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
}
