<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 30/04/2018
 * Time: 07:01 PM
 */

class Invoice extends Comprobante
{
    /*
     * function CreateInvoicesAutomatic
     * Se toma en cuenta las intancias en status baja, se aplica mas para rif que se brinca los meses impares
     * y solo aplica solo para meses anteriores a mayo 2018, ya que en esos meses aun se creaban los workflos de los meses impares y ya tienen factura creada hay que dejarlos asi.
     * se toman en cuenta los servicios en status bajaParcial, si se crean o no su factura en el mes corriente dependera de si tiene workflow creado
     * - dejar pendiente como trabajar con rif cuando esta en bajaParcial, ya que en los meses impares crea factura sin workflow.
     */
   function CreateInvoicesAutomatic(){
       global $months;
       $cadLog="";
       $month = date("m");
       $year = date("Y");
       $init = microtime();
       $cadLog .=" INICIO EJECUCION :".$init.chr(13).chr(10);

       $this->Util()->DB()->setQuery("SELECT * FROM rfc
			WHERE empresaId = '21' ORDER BY rfcId ASC LIMIT 1");
       $emisorHuerin = $this->Util()->DB()->GetRow();

       $this->Util()->DB()->setQuery("SELECT * FROM rfc
			WHERE empresaId = '20' ORDER BY rfcId ASC LIMIT 1");
       $emisorBraun = $this->Util()->DB()->GetRow();

       // solo se sacaran los clientes con estatus active=1 evitar un foreach
       $this->Util()->DB()->setQuery("SELECT * FROM customer WHERE active='1' AND customerId!=280 ");
       $clientes = $this->Util()->DB()->GetResult();
       $customerNoCon=0;
       $allCont=0;
       $allCustomer =  count($clientes);

       $contractsBraun= array();
       $contractsHuerin= array();
       $contractsEfectivo= array();
       foreach($clientes as $key => $cliente)
       {
           //obtener solos los contratos que se encuentren activos
           //AND contractId NOT IN(18, 24, 677, 651, 622, 581, 872, 875, 881, 936, 1236, 1315, 1407, 1440, 1441, 1562, 1702, 1731, 1858, 1941, 2058)(se quito);
           $this->Util()->DB()->setQuery("SELECT * FROM contract WHERE customerId = '".$cliente["customerId"]."' AND activo='Si' ");

           $contratos = $this->Util()->DB()->GetResult();
           if(count($contratos) == 0)
           {
               $customerNoCon++;
               unset($clientes[$key]);
               continue;
           }
           $allCont += count($contratos);
           foreach($contratos as $keyContrato => $contrato)
           {
               switch($contrato["facturador"]){
                   case 'Braun':
                       $contractsBraun[] = $contrato;
                   break;
                   case 'BHSC':
                       $contractsHuerin[]=$contrato;
                   break;
                   default:
                       $contractsEfectivo[] = $contrato;
                   break;
               }
           }
       }
       $cadLog .="TOTAL CLIENTES CON CONTRATOS = ".count($clientes).chr(13).chr(10);
       $cadLog .="TOTAL CLIENTES SIN CONTRATOS = ".$customerNoCon.chr(13).chr(10);
       $cadLog .="TOTAL CLIENTES  = ".$allCustomer.chr(13).chr(10);
       $cadLog .="TOTAL CONTRATOS = ".$allCont.chr(13).chr(10);
       $cadLog .="CONTRATOS BRAUN = ".count($contractsBraun)." CONTRATOS BHSC = ".count($contractsHuerin)." CONTRATOS EFEC = ".count($contractsEfectivo).chr(13).chr(10);
       //ya no necesitamos efectivo
       unset($contractsEfectivo);
       //obtenemos los servicios por contrato solo los activos
       $allService=0;
       $allServices=array();
       //obtener solos los servicios activos y con precio mayor que 0 de huerin
       $contractIds = $this->Util()->ConvertToLineal($contractsHuerin,'contractId');
       if(!empty($contractIds))
        $idContracts = implode(',',$contractIds);
        else
         $idContracts =0;
       $this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId IN(".$idContracts.") AND status IN('activo','bajaParcial') AND costo>0  AND inicioFactura!='0000-00-00' ");
       //
       $this->Util()->DB()->getQuery();
       $servicesHuerin= $this->Util()->DB()->GetResult();
       $allServiceHuerin= count($servicesHuerin);

       $allService = $allService + count($servicesHuerin);
       $allServices=array_merge($allServices,$servicesHuerin);

       //obtener solos los servicios activos y con precio mayor que 0 de braun
       $contractIdsBraun = $this->Util()->ConvertToLineal($contractsBraun,'contractId');
       if(!empty($contractIdsBraun))
        $idContractsBraun = implode(',',$contractIdsBraun);
       else
        $idContractsBraun =0;
       $this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId IN(".$idContractsBraun.") AND status IN('activo','bajaParcial') AND costo>0  AND inicioFactura!='0000-00-00' ");
        $this->Util()->DB()->getQuery();
       $servicesBraun= $this->Util()->DB()->GetResult();
       $allServiceBraun=count($servicesBraun);

       $allService =$allService + count($servicesBraun);
       $allServices=array_merge($allServices,$servicesBraun);

       $cadLog .="TOTAL SERVICIOS HUERIN = ".count($servicesHuerin) .chr(13).chr(10);
       $cadLog .="TOTAL SERVICIOS BRAUN = ".count($servicesBraun) .chr(13).chr(10);
       $cadLog .="TOTAL SERVICIOS = ".count($allServices)."  =  ".$allService.chr(13).chr(10);
       //hasta aca los servicios braun y huerin estan bien.
       $cadLog .="TOTAL SERVICIOS BEFORE FILTRO HUERIN = ".count($servicesHuerin)."   BRAUN = ".count($servicesBraun).chr(13).chr(10);
       ///filtrar los servicios donde inicioFactura sea igual a "0000-00-00"
       $allAfterDate = 0;
       $afterDateHuerin = 0;
       $count = 1;
       foreach($servicesHuerin as $key => $servicio)
       {
           if($servicio["inicioFactura"] == "0000-00-00")//no debe suceder si se trato desde la parte donde se obtiene los servicios
           {
               unset($servicesHuerin[$key]);
               continue;
           }
           /// filtrar los servicios que tengan inicioFactura posteriores al año en curso
           $fecha = explode("-", $servicio["inicioFactura"]);
           if($fecha[0] > $year)
           {
               $afterDateHuerin++;
               $allAfterDate++;
               unset($servicesHuerin[$key]);
               continue;
           }
           /// filtrar los servicios que tengan inicioFactura posteriores al mes actual y ademas sea del año en curso
           if($fecha[1] > $month && $fecha[0] == $year)
           {
               $afterDateHuerin++;
               $allAfterDate++;
               unset($servicesHuerin[$key]);
               continue;
           }
       }
       $afterDateBraun=0;
       foreach($servicesBraun as $key => $servicio)
       {
           if($servicio["inicioFactura"] == "0000-00-00")//no debe suceder si se trato desde la parte donde se obtiene los servicios
           {
               unset($servicesBraun[$key]);
               continue;
           }
           /// filtrar los servicios que tengan inicioFactura posteriores al año en curso
           $fecha = explode("-", $servicio["inicioFactura"]);
           if($fecha[0] > $year)
           {
               $afterDateBraun++;
               $allAfterDate++;
               unset($servicesBraun[$key]);
               continue;
           }
           /// filtrar los servicios que tengan inicioFactura posteriores al mes actual y ademas sea del año en curso
           if($fecha[1] > $month && $fecha[0] == $year)
           {
               $afterDateBraun++;
               $allAfterDate++;
               unset($servicesBraun[$key]);
               continue;
           }
       }
       $cadLog .="TOTAL FECHAS POSTERIORES = ".$allAfterDate.chr(13).chr(10);
       $cadLog .="TOTAL FECHAS POSTERIORES HUERIN = ".$afterDateHuerin.chr(13).chr(10);
       $cadLog .="TOTAL FECHAS POSTERIORES BRAUN = ".$afterDateBraun.chr(13).chr(10);
       $cadLog .="TOTAL SERVICIOS AFTER FILTRO HUERIN = ".count($servicesHuerin)."   BRAUN = ".count($servicesBraun).chr(13).chr(10);
       //quitar los servicios que no tienen instancia creada en el mes en curso excepto el RIF ese debe crear factura aun que no este dentro del mes
       //hasta este punto se da por hecho  que el array de servicios esta listo para facturar
       //ha pasado los filtros de:
       //1. Servicio activos
       //2. Servicios que tienen costo
       //3. Servicios que su fecha de inicio de factura no son 0000-00-00 o Posteriores al año o mes actual.
       $noInstanciasHuerin=0;
       $noInstancias=0;
       $whitInvoice=0;
       $whitInvoiceHuerin=0;
       //obtener las instancias de los servicios y exluir los que ya tienen factura
       foreach($servicesHuerin as $key => $servicio)
       {
            $this->Util()->DB()->setQuery("SELECT instanciaServicio.instanciaServicioId,instanciaServicio.date,instanciaServicio.comprobanteId,
                                           servicio.servicioId,servicio.costo AS costoServicio,contract.contractId,contract.rfc,contract.address,contract.noExtAddress,
                                           contract.noIntAddress, contract.coloniaAddress,contract.municipioAddress, contract.cpAddress, contract.estadoAddress,
                                           contract.paisAddress, contract.emailContactoAdministrativo, contract.telefonoContactoAdministrativo, contract.noCuenta,
                                           contract.name AS name,tipoServicio.tipoServicioId,tipoServicio.nombreServicio,tipoServicio.claveSat  
                                           FROM instanciaServicio
                                           INNER JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
                                           INNER JOIN contract ON contract.contractId = servicio.contractId
                                           INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId AND  tipoServicio.status='1'
                                           INNER JOIN customer ON customer.customerId = contract.customerId
                                           WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'");
           //$this->Util()->DB()->getQuery();
            $row = $this->Util()->DB()->GetRow();

           //si no tiene instancia y tampoco es RIF se ignora
           if(empty($row)&&($servicio['tipoServicioId']!=RIF&&$servicio['tipoServicioId']!=RIFAUDITADO))
           {
               $noInstanciasHuerin++;
               $noInstancias++;
               unset($servicesHuerin[$key]);
               continue;
           }elseif(empty($row)&&($servicio['tipoServicioId']==RIF||$servicio['tipoServicioId']==RIFAUDITADO)){
               //Comprobar si el mes actual es par si es par  debe tener instancia creada en ese mes el RIF por lo tanto debe ignorarse y quitar
               //ese servicio por no tener instancia creada.
               if(date('m')%2==0)
               {
                   $noInstanciasHuerin++;
                   $noInstancias++;
                   unset($servicesHuerin[$key]);
                   continue;
               }
               //de lo contrario seguira su curso.
               if(empty($row)){
                $this->Util()->DB()->setQuery("SELECT servicio.servicioId,servicio.costo AS costoServicio,contract.contractId,contract.rfc,contract.address,contract.noExtAddress,
                                               contract.noIntAddress, contract.coloniaAddress,contract.municipioAddress, contract.cpAddress, contract.estadoAddress,
                                               contract.paisAddress, contract.emailContactoAdministrativo, contract.telefonoContactoAdministrativo, contract.noCuenta,
                                               contract.name AS name,tipoServicio.tipoServicioId,tipoServicio.nombreServicio,tipoServicio.claveSat
                                               FROM servicio
                                               INNER JOIN contract ON contract.contractId = servicio.contractId
                                               INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId AND  tipoServicio.status='1'
                                               INNER JOIN customer ON customer.customerId = contract.customerId
                                               WHERE servicio.servicioId = '".$servicio["servicioId"]."' ");
                $row = $this->Util()->DB()->GetRow();

               $this->Util()->DB()->setQuery('SELECT comprobanteId FROM  comprobante WHERE procedencia="fromRifNoInstance" AND servicioId="'.$servicio['servicioId'].'"
                                              AND YEAR(fecha)="'.$year.'" AND MONTH(fecha)="'.$month.'" ');
               $comp = $this->Util()->DB()->GetSingle();
               if($comp>0)
                   $row['comprobanteId'] =$comp;
               else
                   $row['comprobanteId'] =0;

                $row['date'] =date('Y-m-d');
                $row['isRifNoInstance'] =true;
               }
           }
           //comprobar si la instancia del mes en cuestion no tenga comprobante emitido , de existir se excluye.
           if($row['comprobanteId']!=0){//RIF que no este en el mes siempre salteara esta condicion.
               $whitInvoiceHuerin++;
               $whitInvoice++;
               unset($servicesHuerin[$key]);
               continue;
           }
           $servicesHuerin[$key] = $row;
       }
       $noInstanciasBraun=0;
       $whitInvoiceBraun=0;
       foreach($servicesBraun as $key => $servicio)
       {
           $this->Util()->DB()->setQuery("SELECT instanciaServicio.instanciaServicioId,instanciaServicio.date,instanciaServicio.comprobanteId,
                                          servicio.servicioId,servicio.costo AS costoServicio,contract.contractId,contract.rfc,contract.address,contract.noExtAddress,
                                          contract.noIntAddress, contract.coloniaAddress,contract.municipioAddress, contract.cpAddress, contract.estadoAddress,
                                          contract.paisAddress, contract.emailContactoAdministrativo, contract.telefonoContactoAdministrativo, contract.noCuenta,
                                          contract.name AS name,tipoServicio.tipoServicioId,tipoServicio.nombreServicio,tipoServicio.claveSat
                                          FROM instanciaServicio
                                          INNER JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
                                          INNER JOIN contract ON contract.contractId = servicio.contractId
                                          INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId AND  tipoServicio.status='1'
                                          INNER JOIN customer ON customer.customerId = contract.customerId
                                          WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'");
           $row = $this->Util()->DB()->GetRow();
           //si no tiene instancia y tampoco es RIF se ignora
           if(empty($row)&&($servicio['tipoServicioId']!=RIF&&$servicio['tipoServicioId']!=RIFAUDITADO))
           {
               $noInstanciasBraun++;
               $noInstancias++;
               unset($servicesBraun[$key]);
               continue;
           }elseif(empty($row)&&($servicio['tipoServicioId']==RIF||$servicio['tipoServicioId']==RIFAUDITADO)){
               //Comprobar si el mes actual es par si es par  debe tener instancia creada en ese mes el RIF por lo tanto debe ignorarse y quitar
               //ese servicio si esta vacio
               if(date('m')%2==0)
               {
                   $noInstanciasBraun++;
                   $noInstancias++;
                   unset($servicesBraun[$key]);
                   continue;
               }
               if(empty($row)){
                $this->Util()->DB()->setQuery("SELECT servicio.servicioId,servicio.costo AS costoServicio,contract.contractId,contract.rfc,contract.address,contract.noExtAddress,
                                               contract.noIntAddress, contract.coloniaAddress,contract.municipioAddress, contract.cpAddress, contract.estadoAddress,
                                               contract.paisAddress, contract.emailContactoAdministrativo, contract.telefonoContactoAdministrativo, contract.noCuenta,
                                               contract.name AS name,tipoServicio.tipoServicioId,tipoServicio.nombreServicio,tipoServicio.claveSat
                                               FROM servicio
                                               INNER JOIN contract ON contract.contractId = servicio.contractId
                                               INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId AND  tipoServicio.status='1'
                                               INNER JOIN customer ON customer.customerId = contract.customerId
                                               WHERE servicio.servicioId = '".$servicio["servicioId"]."' ");
                 $row = $this->Util()->DB()->GetRow();
                 //comprobar si no existe un comprobante emitido del servicio correspondiente al mes y año actual
                 //Si el cliente se le facturo multiples servicios  y dentro esta el RIF, comprobar campo procedencia,fecha y servicioId de la tabla comprobante7
                 $this->Util()->DB()->setQuery('SELECT comprobanteId FROM  comprobante WHERE procedencia="fromRifNoInstance" AND servicioId="'.$servicio['servicioId'].'"
                                                AND YEAR(fecha)="'.$year.'" AND MONTH(fecha)="'.$month.'" ');
                 $comp = $this->Util()->DB()->GetSingle();
                 if($comp>0)
                     $row['comprobanteId'] =$comp;
                 else
                    $row['comprobanteId'] =0;

                 $row['date'] =date('Y-m-d');
                 $row['isRifNoInstance'] =true;

               }
           }
           //comprobar si la instancia del mes en cuestion no tenga comprobante emitido , de existir se excluye.
           if($row['comprobanteId']!=0){//RIF que no este en el mes siempre salteara esta condicion.
               $whitInvoiceBraun++;
               $whitInvoice++;
               unset($servicesBraun[$key]);//quitar si esta facturada.
               continue;
           }
           $servicesBraun[$key] = $row;
       }
       $porFacturar = count($servicesHuerin) + count($servicesBraun);
       $cadLog .="SIN INSTANCIAS .".$noInstancias." SIN INSTANCIAS HUERIN=".$noInstanciasHuerin." SIN INSTANCIAS BRAUN=".$noInstanciasBraun.chr(13).chr(10);
       $cadLog .="TOTAL INSTANCIAS CON FACTURA EMITIDA =".$whitInvoice."  HUERIN=".$whitInvoiceHuerin." BRAUN=".$whitInvoiceBraun.chr(13).chr(10);
       $cadLog .="POR FACTURAR .".$porFacturar.chr(13).chr(10);
       $cadLog .="POR FACTURAR HUERIN = ".count($servicesHuerin)." POR FACTURAR BRAUN =".count($servicesBraun).chr(13).chr(10).chr(13).chr(10);
       $cadLog .="BEFORE HUERIN = ".count($servicesHuerin)." BEFORE BRAUN =".count($servicesBraun).chr(13).chr(10);

       if(!is_array($servicesHuerin))
       {
           $servicesHuerin = array();
       }

       if(!is_array($servicesBraun))
       {
           $servicesBraun = array();
       }
       $servicios = array_merge($servicesHuerin, $servicesBraun);
       $cadLog .="TOTAL SERVICIOS = ".count($servicios).chr(13).chr(10);
       $contratos = array();
       foreach($servicios as $res){

           $contractId = $res['contractId'];
           $contratos[$contractId][] = $res;

       }//foreach
       $cadLog .="TOTAL DE CONTRATOS = ".count($contratos).chr(13).chr(10);
       unset($_SESSION["conceptos"]);
       $countInvoice=0;
       foreach($contratos as $contractId => $servicios){
           $cadLog .="facturar a $contractId ".chr(13).chr(10);
           $this->Util()->DB()->setQuery("SELECT facturador FROM contract WHERE contractId = '".$contractId."'");
           $value['facturador'] = $this->Util()->DB()->GetSingle();
           if($value["facturador"] == "BHSC")
           {
               $empresaIdFacturador = 21;
               $_SESSION['empresaId'] = 21;
               $emisor = $emisorHuerin;
               $nombreFactura = "Factura";
           }
           if($value["facturador"] == "Huerin")
           {
               $empresaIdFacturador = 15;
               $_SESSION['empresaId'] = 15;
               $emisor = $emisorHuerin;
               $nombreFactura = "Factura";
           }
           elseif($value["facturador"] == "Braun")
           {
               $empresaIdFacturador = 20;
               $_SESSION['empresaId'] = 20;
               $emisor = $emisorBraun;
               $nombreFactura = "Recibo Honorarios";
           }
           $subtotal = 0;
           $idInstServ = array();
           $_SESSION["conceptos"] = array();
           $tasaIva = $emisor["iva"];
           $isRifNoInstance = false;
           $servicioId=0;
           $fechaRif ="";
           foreach($servicios as $res){
               if($res['isRifNoInstance']){
                   $isRifNoInstance=true;
                   $servicioId=$res['servicioId'];
                   $fechaRif=$res['date'];
               }
               $iva = $res["costoServicio"] * ($emisor["iva"] / 100);
               $subtotal += $res["costoServicio"];
               $total = $subtotal + $iva;
               $fecha = explode("-", $res["date"]);
               $fechaText = $months[$fecha[1]]." del ".$fecha["0"];
               $concepto = $res["nombreServicio"]." CORRESPONDIENTE AL MES DE ".$fechaText;
               if($this->Util()->ValidateOnlyNumeric($res["claveSat"]))
                   $claveProdServ = $res["claveSat"];
               else
                   $claveProdServ = '84111500';
               $_SESSION["conceptos"][] = array(
                   "noIdentificacion" => "",
                   "cantidad" => 1,
                   "unidad" => "No Aplica",
                   "valorUnitario" => $res["costoServicio"],
                   "importe" => $res["costoServicio"],
                   "excentoIva" => "no",
                   "descripcion" => $concepto,
                   "tasaIva" => $tasaIva,
                   "claveProdServ" => $claveProdServ,
                   "claveUnidad" => 'E48',
                   "importeTotal" => $res["costoServicio"],
                   "totalIva" => $iva,
               );
               if($res['instanciaServicioId']>0)
                $idInstServ[] = $res['instanciaServicioId'];

           }//foreach
           if($isRifNoInstance)
           {
              $data['servicioId']=$servicioId;
              $data['fechaRif'] = $fechaRif;
              $data['isRifNoInstance'] = $isRifNoInstance;
              $data['procedencia'] = 'rifWhithoutInstance';
           }else{
               $data['procedencia'] = 'whithInstance';
           }

           $data["idFactura"] = $res["instanciaServicioId"];
           $data["condicionesDePago"] = "";
           $data["tasaIva"] = $tasaIva;
           $data["tiposDeMoneda"] = "MXN";
           $data["porcentajeRetIva"] = 0;
           $data["porcentajeDescuento"] = 0;
           $data["tipoDeCambio"] = 1.00;
           $data["porcentajeRetIsr"] = 0;
           $data["porcentajeIEPS"] = 0 ;

           $this->Util()->DB()->setQuery("SELECT * FROM serie WHERE empresaId = '".$empresaIdFacturador."'
				ORDER BY serieId ASC LIMIT 1");
           $serie = $this->Util()->DB()->GetRow();
           //agregar seri
           $data["serie"] =array
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
           $data["comprobante"] = array
           (
               "tiposComprobanteId" => 1,
               "nombre" => $nombreFactura,
               "tipoDeComprobante" => "ingreso"
           );

           //nodo emisor
           $emisor["rfc"] = trim(str_replace("-", "", $emisor["rfc"]));
           $emisor["rfc"] = str_replace(" ", "", $emisor["rfc"]);

           $data["nodoEmisor"]["rfc"] = array
           (

               "rfcId" => $emisor["rfcId"],
               "empresaId" => $empresaIdFacturador,
               "regimenFiscal" => $emisor["regimenFiscal"],
               "rfc" => $emisor["rfc"],
               "razonSocial" => $emisor["razonSocial"],
               "pais" => $emisor["pais"],
               "calle" => $emisor["calle"],
               "noExt" => $emisor["noExt"],
               "noInt" => $emisor["noInt"],
               "colonia" => $emisor["colonia"],
               "localidad" => $emisor["localidad"],
               "municipio" => $emisor["municipio"],
               "ciudad" => $emisor["ciudad"],
               "referencia" => $emisor["referencia"],
               "estado" => $emisor["estado"],
               "cp" => $emisor["cp"],
               "activo" => $emisor["activo"],
               "main" => $emisor["main"]
           );

           if($value["facturador"] == "BHSC")
           {
               $data["nodoEmisor"]["sucursal"] = array
               (
                   "identificador" => "Matriz",
                   "rfcId" => $emisor["rfcId"],
                   "empresaId" => $empresaIdFacturador,
                   "regimenFiscal" => $emisor["regimenFiscal"],
                   "rfc" => $emisor["rfc"],
                   "razonSocial" => $emisor["razonSocial"],
                   "pais" => $emisor["pais"],
                   "calle" => "NAVARRA",
                   "noExt" => "210",
                   "noInt" => "PB",
                   "colonia" => "Alamos",
                   "localidad" => "BENITO JUAREZ",
                   "municipio" => "BENITO JUAREZ",
                   "ciudad" => "BENITO JUAREZ",
                   "referencia" => "",
                   "estado" => "DF",
                   "cp" => "03400",
                   "activo" => $emisor["activo"],
                   "main" => $emisor["main"]
               );
           }
           else
           {
               $data["nodoEmisor"]["sucursal"] = array(
                   "identificador" => "Matriz",
                   "rfcId" => $emisor["rfcId"],
                   "empresaId" => $empresaIdFacturador,
                   "regimenFiscal" => $emisor["regimenFiscal"],
                   "rfc" => $emisor["rfc"],
                   "razonSocial" => $emisor["razonSocial"],
                   "pais" => $emisor["pais"],
                   "calle" => $emisor["calle"],
                   "noExt" => $emisor["noExt"],
                   "noInt" => $emisor["noInt"],
                   "colonia" => $emisor["colonia"],
                   "localidad" => $emisor["localidad"],
                   "municipio" => $emisor["municipio"],
                   "ciudad" => $emisor["ciudad"],
                   "referencia" => $emisor["referencia"],
                   "estado" => $emisor["estado"],
                   "cp" => $emisor["cp"],
                   "activo" => $emisor["activo"],
                   "main" => $emisor["main"]
               );
           }
           //$data["nodoEmisor"]["sucursal"]["identificador"] = "Matriz";
           $res["rfc"] = trim(str_replace("-", "", $res["rfc"]));
           $res["rfc"] = str_replace(" ", "", $res["rfc"]);

           if($res["rfc"] == "123123123123")
           {
               continue;
           }
           if(!$res["rfc"])
           {
               continue;
               $res["rfc"] = "XAXX010101000";
           }

           if(strlen($res["rfc"]) < 12)
           {
               continue;
               $res["rfc"] = "XAXX010101000";
           }

           $data["nodoReceptor"] = array
           (
               "userId" => $res["contractId"],
               "empresaId" => $empresaIdFacturador,
               "rfcId" => $emisor["rfcId"],
               "rfc" => $res["rfc"],
               "nombre" => $res["name"],
               "calle" => $res["address"],
               "noExt" => $res["noExtAddress"],
               "noInt" => $res["noIntAddress"],
               "colonia" => $res["coloniaAddress"],
               "municipio" => $res["municipioAddress"],
               "cp" => $res["cpAddress"],
               "estado" => $res["estadoAddress"],
               "localidad" => $res["municipioAddress"],
               "referencia" => "",
               "pais" => $res["paisAddress"],
               "email" => $res["emailContactoAdministrativo"],
               "telefono" => $res["telefonoContactoAdministrativo"],
               "password" => ""
           );
            /*$formaDePago = $res["metodoDePago"];
              if($formaDePago == 'NA'){
                 $formaDePago = 99;
            }*/
           $formaDePago = 99;
           $data["formaDePago"] = $formaDePago;
           $data["NumCtaPago"] = $res["noCuenta"];

           if(strlen($data["NumCtaPago"]) != 4){
               $data["NumCtaPago"] = '';
           }
           $data['userId'] = $res["contractId"];
           $data['format'] = 'generar';
           $data['metodoDePago'] = 'PPD';
           $data['cfdiRelacionadoSerie'] = null;
           $data['cfdiRelacionadoFolio'] = null;
           $data['tipoRelacion'] = '04';
           $data['usoCfdi'] = 'G03';
           $data["tiposComprobanteId"] = $serie["tiposComprobanteId"]."-".$serie['serieId'];

           $cfdi = new Cfdi();
           $result = $cfdi->Generar($data);
           if(!$result){
               $cadLog .=chr(13).chr(10)." Error al generar factura para ".$res['name']."con rfc = ".$res['rfc'].chr(13).chr(10);
               $cadLog .="ERROR PAC =".$_SESSION['errorPac'].chr(13).chr(10);
           } else {
               $last = $this->GetLastComprobante();
               $cadLog .="Se creo la factura ".$last['serie'].$last['folio']." para ".$res['name']."con rfc = ".$res['rfc'].chr(13).chr(10);
               if(!empty($idInstServ)){
                   $sql = "UPDATE instanciaServicio SET comprobanteId = '".$last["comprobanteId"]."'
						WHERE instanciaServicioId IN (0, ".implode(',',$idInstServ).")";
                   $this->Util()->DB()->setQuery($sql);
                   $this->Util()->DB()->UpdateData();
                   $cadLog .="Actualizada las intancias siguientes : ".chr(13).chr(10);
                   $cadLog .=trim($sql).chr(13).chr(10);

               }
           }
           //facturar uno por uno, se intento facturar por lotes de 10 pero afecto , el servidor tarda en responder y  causa inconsistencias
           //romper en el primer ciclo.
           break;
       }/// END LOOP CREATE INVOICE

       $cad['log']=$cadLog;
       $cad['totalContract'] =count($contratos);
      return $cad;
   }
}