<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 30/04/2018
 * Time: 07:01 PM
 */

class Invoice extends Comprobante
{
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
       $this->Util()->DB()->setQuery("SELECT * FROM customer WHERE active='1' AND customerId != 280");
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
           $this->Util()->DB()->setQuery("SELECT * FROM contract WHERE
			customerId = '".$cliente["customerId"]."' 
			AND contractId NOT IN(18, 24, 677, 651, 622, 581, 872, 875, 881, 936, 1236, 1315, 1407, 1440, 1441, 1562, 1702, 1731, 1858, 1941, 2058)
			AND activo='Si' ");
           //$this->Util()->DB()->getQuery();
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
       $idContracts = implode(',',$contractIds);
       $this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId IN(".$idContracts.") AND status='activo' AND costo>0 ORDER BY servicioId DESC");
       $servicesHuerin= $this->Util()->DB()->GetResult();
       $allServiceHuerin= count($servicesHuerin);

       $allService = $allService + count($servicesHuerin);
       $allServices=array_merge($allServices,$servicesHuerin);


       //obtener solos los servicios activos y con precio mayor que 0 de braun
       $contractIdsBraun = $this->Util()->ConvertToLineal($contractsBraun,'contractId');
       $idContractsBraun = implode(',',$contractIdsBraun);
       $this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId IN(".$idContractsBraun.") AND status='activo' AND costo>0 ORDER BY servicioId DESC");
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
           if($servicio["inicioFactura"] == "0000-00-00")
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
           if($servicio["inicioFactura"] == "0000-00-00")
           {
               unset($servicesBraun[$key]);
               continue;
           }
           $fecha = explode("-", $servicio["inicioFactura"]);
           if($fecha[0] > $year)
           {
               $afterDateBraun++;
               $allAfterDate++;
               unset($servicesBraun[$key]);
               continue;
           }
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
           $this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM instanciaServicio
                LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
                LEFT JOIN contract ON contract.contractId = servicio.contractId
                LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
                LEFT JOIN customer ON customer.customerId = contract.customerId
                WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'");
           $row = $this->Util()->DB()->GetRow();

           //si no tiene instancia y tampoco es RIF se ignora
           if(empty($row)&&$servicio['tipoServicioId']!=RIF)
           {
               $noInstanciasHuerin++;
               $noInstancias++;
               unset($servicesHuerin[$key]);
               continue;
           }elseif($servicio['tipoServicioId']==RIF){
               //si el mes del RIF es donde no se creo instancia entonces llenar los datos manualmente.
               //de lo contrario seguira su curso.
               if(empty($row)){
                   $this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM servicio
                LEFT JOIN contract ON contract.contractId = servicio.contractId
                LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
                LEFT JOIN customer ON customer.customerId = contract.customerId
                WHERE servicio.servicioId = '".$servicio["servicioId"]."' ");
                $row = $this->Util()->DB()->GetRow();
                $row['comprobanteId'] =0;
               }
           }
           //comprobar si la instancia del mes en cuestion no tenga comprobante emitido , de existir se excluye.
           /*if($row['comprobanteId']!=0){//RIF que no este en el mes siempre salteara esta condicion.
               $whitInvoiceHuerin++;
               $whitInvoice++;
               continue;
           }*/
           $servicesHuerin[$key] = $row;
       }
       $noInstanciasBraun=0;
       $whitInvoiceBraun=0;
       foreach($servicesBraun as $key => $servicio)
       {
           $this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM instanciaServicio
                LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
                LEFT JOIN contract ON contract.contractId = servicio.contractId
                LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
                LEFT JOIN customer ON customer.customerId = contract.customerId
                WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'");
           $row = $this->Util()->DB()->GetRow();

           //si no tiene instancia y tampoco es RIF se ignora
           if(empty($row)&&$servicio['tipoServicioId']!=RIF)
           {
               $noInstanciasBraun++;
               $noInstancias++;
               unset($servicesBraun[$key]);
               continue;
           }elseif($servicio['tipoServicioId']==RIF){
               //si el mes del RIF es donde no se creo instancia entonces llenar los datos manualmente.
               //de lo contrario seguira su curso.
               if(empty($row)){
                   $this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM servicio
                LEFT JOIN contract ON contract.contractId = servicio.contractId
                LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
                LEFT JOIN customer ON customer.customerId = contract.customerId
                WHERE servicio.servicioId = '".$servicio["servicioId"]."' ");
                   $row = $this->Util()->DB()->GetRow();
                  $row['comprobanteId'] =0;
               }
           }
           //comprobar si la instancia del mes en cuestion no tenga comprobante emitido , de existir se excluye.
           /*if($row['comprobanteId']!=0){//RIF que no este en el mes siempre salteara esta condicion.
               $whitInvoiceBraun++;
               $whitInvoice++;
               continue;
           }*/
           $servicesBraun[$key] = $row;
       }
       $porFacturar = count($servicesHuerin) + count($servicesBraun);
       $cadLog .="SIN INSTANCIAS .".$noInstancias." SIN INSTANCIAS HUERIN=".$noInstanciasHuerin." SIN INSTANCIAS BRAUN=".$noInstanciasBraun.chr(13).chr(10);
       $cadLog .="POR FACTURAR .".$porFacturar.chr(13).chr(10);
       $cadLog .="POR FACTURAR HUERIN = ".count($servicesHuerin)." POR FACTURAR BRAUN =".count($servicesBraun).chr(13).chr(10).chr(13).chr(10);

       $cadLog .="BEFORE HUERIN = ".count($servicesHuerin)." BEFORE BRAUN =".count($servicesBraun).chr(13).chr(10);
       //facturadas
      /* foreach($servicesHuerin as $key => $servicio)
       {
           if($servicio["comprobanteId"] != 0)
           {
               $data["facturadaBraun"]++;
               $data["facturada"]++;
               unset($servicesHuerin[$key]);
               continue;
           }

           if(!$data["facturadaBraun"])
           {
               $data["facturadaBraun"] = 0;
           }
       }

       foreach($servicesBraun as $key => $servicio)
       {
           if($servicio["comprobanteId"] != 0)
           {
               $data["facturadaHuerin"]++;
               $data["facturada"]++;
               unset($data["serviciosHuerin"][$key]);
               continue;
           }

           if(!$data["facturadaHuerin"])
           {
               $data["facturadaHuerin"] = 0;
           }

       }
       if(!$data["facturada"])
       {
           $data["facturada"] = 0;
       }

       $data["totalFacturadas"] = count($servicesHuerin) + count($servicesBraun);
       $cadLog .="TOTAL FACTURADAS = ".$data['facturada']." FACTURADAS HUERIN  =".$data['facturadaHuerin']." FACTURADAS BRAUN =".$data['facturadaBraun'].chr(13).chr(10);
       $cadLog .="POR FACTURAR .".$data['totalFacturadas'].chr(13).chr(10);
       $cadLog .="POR FACTURAR HUERIN = ".count($servicesHuerin)." POR FACTURAR BRAUN =".count($servicesBraun).chr(13).chr(10);
*/
       return $cadLog;
       exit;
       ?>
       <tr>
           <td>Facturadas</td>
           <td><?php echo $data["facturadaHuerin"]?></td>
           <td><?php echo $data["facturadaBraun"]?></td>
           <td></td>
           <td><?php echo $data["facturada"]; ?></td>
       </tr>
       <tr>
           <td>Por Facturar</td>
           <td><b><?php echo count($data["serviciosHuerin"])?></b></td>
           <td><b><?php echo count($data["serviciosBraun"])?></b></td>
           <td></td>
           <td><b><?php echo $data["totalFacturadas"]; ?></b></td>
       </tr>
       </table>
       <?php

       if(!is_array($data["serviciosHuerin"]))
       {
           $data["serviciosHuerin"] = array();
       }

       if(!is_array($data["serviciosBraun"]))
       {
           $data["serviciosBraun"] = array();
       }

       $servicio = array_merge($data["serviciosHuerin"], $data["serviciosBraun"]);
       //$servicio = $data["serviciosBraun"];
       //Agrupamos por Contratos (Razones Sociales)
       //print_r($data["serviciosBraun"]);

       $idContracts = array();
       $contratos = array();
       foreach($servicio as $res){

           $contractId = $res['contractId'];
           $contratos[$contractId][] = $res;

       }//foreach
       //exit;
       //Contratos
       unset($data);
       unset($_SESSION["conceptos"]);
       foreach($contratos as $contractId => $servicios){
           //	echo "jere";
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

           echo '*****************';
           echo '<br>';
           echo $contractId.' :: '.$value["facturador"];
           echo "<br>";

           $subtotal = 0;
           $idInstServ = array();
           $_SESSION["conceptos"] = array();
           $tasaIva = $emisor["iva"];
           foreach($servicios as $res){

               $iva = $res["costoServicio"] * ($emisor["iva"] / 100);
               $subtotal += $res["costoServicio"];
               $total = $subtotal + $iva;

               $fecha = explode("-", $res["date"]);
               $fechaText = $months[$fecha[1]]." del ".$fecha["0"];
               $concepto = $res["nombreServicio"]." CORRESPONDIENTE AL MES DE ".$fechaText;

               $_SESSION["conceptos"][] = array(
                   "noIdentificacion" => "",
                   "cantidad" => 1,
                   "unidad" => "No Aplica",
                   "valorUnitario" => $res["costoServicio"],
                   "importe" => $res["costoServicio"],
                   "excentoIva" => "no",
                   "descripcion" => $concepto,
                   "tasaIva" => $tasaIva,
                   "claveProdServ" => '84111500',
                   "claveUnidad" => 'E48',
                   'importeTotal' => $res["costoServicio"],
                   'totalIva' => $iva,
               );

               echo  $res["nombreServicio"]." ".$res["instanciaServicioId"]." ".$res["name"]." ".$res["rfc"]." ".$res["costoServicio"];
               echo "<br>";

               $idInstServ[] = $res['instanciaServicioId'];

           }//foreach

           $data["idFactura"] = $res["instanciaServicioId"]; //Duda


           $data["condicionesDePago"] = "";
           $data["tasaIva"] = $tasaIva;
           $data["tiposDeMoneda"] = "MXN";
           $data["porcentajeRetIva"] = 0;
           $data["porcentajeDescuento"] = 0;
           $data["tipoDeCambio"] = 1.00;
           $data["porcentajeRetIsr"] = 0;
           $data["porcentajeIEPS"] = 0 ;

           //get serie
           $this->Util()->DB()->setQuery("SELECT * FROM serie WHERE empresaId = '".$empresaIdFacturador."'
				ORDER BY serieId ASC LIMIT 1");
           $serie = $this->Util()->DB()->GetRow();
           //agregar serie
           $data["serie"] = array
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

           /*            $formaDePago = $res["metodoDePago"];

                       if($formaDePago == 'NA'){
                           $formaDePago = 99;
                       }*/
           $formaDePago = 99;

           $data["formaDePago"] = $formaDePago;
           $data["NumCtaPago"] = $res["noCuenta"];

           if(strlen($data["NumCtaPago"]) != 4){
               $data["NumCtaPago"] = '';
           }

           //print_r($_SESSION["conceptos"]);

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
               echo "\nError al generar la factura para ".$res["rfc"]."\n\n";
               print_r($_SESSION['errorPac']);
           } else {
               $last = $this->GetLastComprobante();

               $sql = "UPDATE instanciaServicio SET comprobanteId = '".$last["comprobanteId"]."'
						WHERE instanciaServicioId IN (0, ".implode(',',$idInstServ).")";
               $this->Util()->DB()->setQuery($sql);
               $this->Util()->DB()->UpdateData();
               echo "\n\nFactura para ".$res["rfc"]." Lista\n";

           }
           break;

       }//foreach

       //FIN AGRUPADO POR CONTRATOS

       $end = microtime();
       $tiempo = $end-$init;
       echo "<br>Script ejecutado en ".$tiempo." Milisegundos";


   }
}