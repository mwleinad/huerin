<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 17/01/2018
 * Time: 08:03 PM
 */
/*
     * los servicios domicilio fiscal(16),facturacion(17) y representacion legal fiel(24) saldran en reporte pero sin color
     * estos servicios no tienen seguimiento de sus workflows
     * Comprobar la fecha de inicio de operaciones para llevar control de que se mostrara.
     *  - Si la fecha de inicio de operaciones se modifico y este servicio ya tiene workflows creados en fechas anteriores al nuevo inicio de operaciones
     *        se debe ignorar las anteriores
     */
class InstanciaServicio extends  Servicio
{

    function getInstanciaByServicio($servicioId, $year,$foperaciones="0000-00-00",$isParcial =  false)
    {
        $base = array(1=>array(),2=>array(),3=>array(),4=>array(),5=>array(),6=>array(),7=>array(),8=>array(),9=>array(),
            10=>array(),11=>array(),12=>array());
        $ftrTemporal = "";
        if($isParcial){
            //obtener el año de la lastDateWorkflow , si el año coincide con  $year el filtro aplica. $year es el año que se esta consultando.
            $this->Util()->DB()->setQuery("select YEAR(lastDateWorkflow) from servicio where servicioId='".$servicioId."' ");
            $ylast =  $this->Util()->DB()->GetSingle();
            //si el año requerido es mayor a lastDateWorkflow no debe obtener instancias.
            if($year>$ylast)
                return $base;

            if($year==$ylast)
                $ftrTemporal .=  " AND MONTH(instanciaServicio.date)<=MONTH(servicio.lastDateWorkflow) ";
        }
        $sinceMonth ="";
        if($foperaciones=="0000-00-00")
            return $base;

        $fecha =  explode('-',$foperaciones);
        //si año de IO es superior al año solicitado se ignora aunque tengan workflows creados.
        if(!($year>=$fecha[0]))
            return $base;
        //los meses que debe obtener debe ser apartir del mes de inicio de operaciones. esto es solo para el año de IO lo demas no debe evaluar esto
        if($year==$fecha[0])
            $sinceMonth = " and MONTH(instanciaServicio.date)>=".(int)$fecha[1];

        $sql = "SELECT 
                CASE tipoServicioId 
                WHEN 16 THEN ''
                WHEN 17 THEN ''
                WHEN 24 THEN ''
                ELSE
                class
                END 
                AS class
                ,MONTH(instanciaServicio.date) as mes,instanciaServicio.date as finstancia,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE (MONTH(instanciaServicio.date) IN (1,2,3,4,5,6,7,8,9,10,11,12)  $sinceMonth $ftrTemporal)
				AND YEAR(instanciaServicio.date) = '".$year."'
				AND servicio.status NOT IN('baja','inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."'  ORDER BY instanciaServicio.instanciaServicioId DESC" ;
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        foreach($data as $key => $value)
        {
            $base[$value['mes']] =  $value;
        }
        return $base;
    }
    function getInstanciaByServicio12($servicioId, $year)
    {
        $base = array(1=>array(),2=>array(),3=>array(),4=>array(),5=>array(),6=>array(),7=>array(),8=>array(),9=>array(),
            10=>array(),11=>array(),12=>array());

        $sql = "CALL getInstancias(".$year.", ".$servicioId.",'all'); ";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResultPdo();
        foreach($data as $key => $value)
        {
            $base[$value['mes']] =  $value;
        }
        return $base;
    }
    function getInstanciaAtrasado12($servicioId,$year){

        $sql = "CALL getInstancias(".$year.", ".$servicioId.",'atrasados'); ";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();
        return $data;
    }
    function getInstanciaAtrasado($servicioId,$year,$foperaciones="0000-00-00",$isParcial =  false){
        $data = [];
        $ftrTemporal = "";
        if($isParcial){
            //obtener el año de la lastDateWorkflow , si el año coincide con  $year el filtro aplica. $year es el año que se esta consultando.
            $this->Util()->DB()->setQuery("select YEAR(lastDateWorkflow) from servicio where servicioId='".$servicioId."' ");
            $ylast =  $this->Util()->DB()->GetSingle();
            //si el año requerido es mayor a lastDateWorkflow por default no hay atrasados.
            if($year>$ylast)
                return $data;

            if($year==$ylast)
                $ftrTemporal .=  " AND MONTH(instanciaServicio.date)<=MONTH(servicio.lastDateWorkflow) ";
        }
        $sinceMonth ="";
        if($foperaciones=="0000-00-00")
        {
            $fecha =  explode('-',$foperaciones);
            //si año de IO es superior al año solicitado se retorna atrasados vacio.
            if(!($year>=$fecha[0]))
                return $data;

            if($year==$fecha[0])
                $sinceMonth = " and MONTH(instanciaServicio.date)>=".(int)$fecha[1];
        }

        $sql = "SELECT 
                CASE tipoServicioId 
                    WHEN 16 THEN ''
                    WHEN 17 THEN ''
                    WHEN 24 THEN ''
                    ELSE
                    class
                END 
                AS class,
                MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE MONTH(instanciaServicio.date) < MONTH(NOW()) $ftrTemporal $sinceMonth
				AND YEAR(instanciaServicio.date) = '".$year."'
				AND instanciaServicio.class IN ('PorIniciar','PorCompletar','Iniciado')
				AND servicio.status NOT IN('baja','inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."' GROUP BY MONTH(instanciaServicio.date) ORDER BY instanciaServicio.date";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        return $data;
    }
    function getOnlyAtrasados($servicioId){
       $sql = "SELECT 
                class,
                YEAR(instanciaServicio.date) as anio,
                MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				INNER JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE (instanciaServicio.date >='2017-01-01' AND instanciaServicio.date<DATE(NOW()))
				AND instanciaServicio.class IN ('PorIniciar','PorCompletar','Iniciado')
				AND servicio.status NOT IN('baja','inactiva')
				AND instanciaServicio.status != 'baja'		
				AND servicio.servicioId = '".$servicioId."' group by instanciaServicio.date ORDER BY YEAR(instanciaServicio.date) DESC, MONTH(instanciaServicio.date) ASC";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();
        return $data;
    }
    function getSumaBonoTrimestre($servicioId,$year,$meses=array(),$foperaciones,$isParcial=false){
        $ftrTemporal = "";
        $new = array();
        if($isParcial){
            //obtener el año de la lastDateWorkflow , si el año coincide con  $year el filtro aplica. $year es el año que se esta consultando.
            $this->Util()->DB()->setQuery("select YEAR(lastDateWorkflow) from servicio where servicioId='".$servicioId."' ");
            $ylast =  $this->Util()->DB()->GetSingle();
            //si el año requerido es mayor a lastDateWorkflow no debe obtener instancias.
            if($year>$ylast)
                return $new;

            if($year==$ylast)
                $ftrTemporal .=  " AND MONTH(instanciaServicio.date)<=MONTH(servicio.lastDateWorkflow) ";
        }
        $sinceMonth ="";
        if($foperaciones=="0000-00-00")
            return $new;

        $fecha =  explode('-',$foperaciones);
        //si año de IO es superior al año solicitado se ignora aunque tengan workflows creados.
        if(!($year>=$fecha[0]))
            return $new;
        //los meses que debe obtener debe ser apartir del mes de inicio de operaciones. esto es solo para el año de IO lo demas no debe evaluar esto
        if($year==$fecha[0])
            $sinceMonth = " and MONTH(instanciaServicio.date)>=".(int)$fecha[1];

        $sql = "SELECT 
                sum(servicio.costo) as costoTotal
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE (MONTH(instanciaServicio.date) IN (".implode(',',$meses).") $sinceMonth) AND YEAR(instanciaServicio.date)='".$year."' $ftrTemporal
				AND instanciaServicio.class IN ('CompletoTardio','Completo')
				AND servicio.status NOT IN('baja','inactiva')
				AND instanciaServicio.status != 'baja'	AND servicio.servicioId = '".$servicioId."' GROUP BY servicio.servicioId";
        $this->Util()->DB()->setQuery($sql);
        $total =  $this->Util()->DB()->GetSingle();
        return $total;
    }
    function getBonoTrimestre($servicioId,$year,$meses=array(),$foperaciones,$isParcial=false){
        $ftrTemporal = "";
        $new = array();
        if($isParcial){
            //obtener el año de la lastDateWorkflow , si el año coincide con  $year el filtro aplica. $year es el año que se esta consultando.
            $this->Util()->DB()->setQuery("select YEAR(lastDateWorkflow) from servicio where servicioId='".$servicioId."' ");
            $ylast =  $this->Util()->DB()->GetSingle();
            //si el año requerido es mayor a lastDateWorkflow no debe obtener instancias.
            if($year>$ylast)
                return $new;

            if($year==$ylast)
                $ftrTemporal .=  " AND MONTH(instanciaServicio.date)<=MONTH(servicio.lastDateWorkflow) ";
        }
        $sinceMonth ="";
        if($foperaciones=="0000-00-00")
            return $new;

        $fecha =  explode('-',$foperaciones);
        //si año de IO es superior al año solicitado se ignora aunque tengan workflows creados.
        if(!($year>=$fecha[0]))
            return $new;
        //los meses que debe obtener debe ser apartir del mes de inicio de operaciones. esto es solo para el año de IO lo demas no debe evaluar esto
        if($year==$fecha[0])
            $sinceMonth = " and MONTH(instanciaServicio.date)>=".(int)$fecha[1];

        $sql = "SELECT 
                class,
                servicio.costo,
                YEAR(instanciaServicio.date) as anio,
                MONTH(instanciaServicio.date) as mes,instanciaServicioId, instanciaServicio.status, servicio.tipoServicioId
				FROM instanciaServicio 
				LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE (MONTH(instanciaServicio.date) IN (".implode(',',$meses).") $sinceMonth) AND YEAR(instanciaServicio.date)='".$year."' $ftrTemporal 
				AND servicio.status NOT IN('baja','inactiva')
				AND instanciaServicio.status != 'baja'
				AND servicio.servicioId = '".$servicioId."'  ORDER BY  instanciaServicio.instanciaServicioId DESC";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();

        foreach($data as $key => $value)
        {
            switch($value['mes']){
                case 1:
                case 4:
                case 7:
                case 10:
                    $llave = 0; break;
                case 2:
                case 5:
                case 8:
                case 11:
                    $llave = 1; break;
                case 3:
                case 6:
                case 9:
                case 12:
                $llave = 2; break;
            }
            $new[$llave] =  $value;
        }
        return $new;
    }
    function getBonoInstanciaWhitInvoice($servicioId,$year,$meses=array(),$foperaciones,$isParcial=false,$monthBase=[]){
        global $comprobante;
        $ftrTemporal = "";
        $new = [];
        $newArray = [];
        $this->Util()->DB()->setQuery("select tipoServicioId from servicio where servicioId='".$servicioId."' ");
        $tipoServicio =  $this->Util()->DB()->GetSingle();
        if($isParcial){
            //obtener el año de la lastDateWorkflow , si el año coincide con  $year el filtro aplica. $year es el año que se esta consultando.
            $this->Util()->DB()->setQuery("select YEAR(lastDateWorkflow) from servicio where servicioId='".$servicioId."' ");
            $ylast =  $this->Util()->DB()->GetSingle();
            //si el año requerido es mayor a lastDateWorkflow no debe obtener instancias.
            if($year>$ylast)
                return $new;

            if($year==$ylast)
                $ftrTemporal .=  " AND MONTH(instanciaServicio.date)<=MONTH(servicio.lastDateWorkflow) ";
        }
        $sinceMonth ="";
        if($foperaciones=="0000-00-00")
            return $new;

        $fecha =  explode('-',$foperaciones);
        //si año de IO es superior al año solicitado se ignora aunque tengan workflows creados.
        if(!($year>=$fecha[0]))
            return $new;
        //los meses que debe obtener debe ser apartir del mes de inicio de operaciones. esto es solo para el año de IO lo demas no debe evaluarlo
        if($year==$fecha[0])
            $sinceMonth = " and MONTH(instanciaServicio.date)>=".(int)$fecha[1];

        $sql = "SELECT class,servicio.costo,YEAR(instanciaServicio.date) as anio,MONTH(instanciaServicio.date) as mes,instanciaServicioId, 
                instanciaServicio.status, servicio.tipoServicioId,instanciaServicio.comprobanteId,instanciaServicio.costoWorkflow,servicio.inicioFactura,instanciaServicio.factura
				FROM instanciaServicio 
				INNER JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
				WHERE (MONTH(instanciaServicio.date) IN (".implode(',',$meses).") $sinceMonth) AND YEAR(instanciaServicio.date)='".$year."' $ftrTemporal 
				AND servicio.status NOT IN('baja','inactiva')
				AND instanciaServicio.status != 'baja'
				AND servicio.servicioId = '".$servicioId."'  ORDER BY  instanciaServicio.instanciaServicioId DESC";
        $this->Util()->DB()->setQuery($sql);
        $data = $this->Util()->DB()->GetResult();
        $totalAcompletado = 0;
        $totalDevengado = 0;
        if(empty($data))
          return $newArray;

        foreach($data as $key => $value)
        {
            $costo = 0;
            if($this->Util()->isValidateDate($value['inicioFactura'],'Y-m-d')){
                $costo = $value['costo'];
            }

            $value['cobrado'] = 0;
            if($value['factura']=='Si'){
                $comp =  $comprobante->GetInfoComprobante($value['comprobanteId']);
                if($comp['saldo']<=0.01)
                    $value['cobrado'] = $value['costo'];
            }
            $value['costo'] = $costo;
            //sumar total de lo trabajado
            if($value['class']=='CompletoTardio'|| $value['class']=='Completo'){
                $totalAcompletado += $value['costo'];
                $value['completado'] = $value['costo'];
            }


            $totalDevengado += $value['costo'];
            $monthBase[$value['mes']] =  $value;
        }

        $newArray['instancias']= $monthBase;
        $newArray['totalComplete'] = $totalAcompletado;
        $newArray['totalDevengado'] = $totalDevengado;

        return $newArray;
    }
    function findCostoService($mes,$year,$servicioId,$compId=0)
    {
        global $servicio, $comprobante;
        $servicio->setServicioId($servicioId);
        $infoServicio = $servicio->Info();
        $nombreServicio = $infoServicio['nombreServicio'];
        $contratoId = $infoServicio['contractId'];
        if($compId > 0){
           $sql = "SELECT xml,cadenaOriginal FROM comprobante WHERE comprobanteId='$compId'  and status='1' ";
            $this->Util()->DB()->setQuery($sql);
            $factura = $this->Util()->DB()->GetRow();
        }
        if(!$factura){
            $sql = "SELECT xml,cadenaOriginal FROM comprobante 
                WHERE MONTH(fecha)='$mes' AND YEAR(fecha)='$year' AND userId='$contratoId'  and status='1' AND tipoDeComprobante='ingreso'
               ";
            $this->Util()->DB()->setQuery($sql);
            $factura = $this->Util()->DB()->GetRow();
        }
        $costo =  0;
        if($factura){
            if(file_exists(DIR_FROM_XML."/SIGN_".$factura['xml'].".xml")){
                $data = $comprobante->getDataByXml("SIGN_".$factura['xml']);
                foreach($data['conceptos'] as $con){
                    $description = (string)$con['Descripcion'];
                    if(stripos($description,$nombreServicio)!==false){
                        $costo = (double)$con['ValorUnitario'];
                        break;
                    }
                }
            }
        }
        return $costo;
    }
    function findCostoInstanciaMonth($mes,$year,$servicioId){
        global $servicio,$comprobante;
        $servicio->setServicioId($servicioId);
        $infoServicio = $servicio->Info();
        $nombreServicio = $infoServicio['nombreServicio'];
        $contratoId = $infoServicio['contractId'];

        $sql = "SELECT xml,cadenaOriginal FROM comprobante 
                WHERE MONTH(fecha)='$mes' AND YEAR(fecha)='$year' AND userId='$contratoId'  and status='1' AND tipoDeComprobante='ingreso'
               ";
        $this->Util()->DB()->setQuery($sql);
        $facturas = $this->Util()->DB()->GetResult();
        $costo =  0;
        if(count($facturas)){
            foreach($facturas as $fact=>$factura){
                if(file_exists(DIR_FROM_XML."/SIGN_".$factura['xml'].".xml")){
                    $data = $comprobante->getDataByXml("SIGN_".$factura['xml']);
                    foreach($data['conceptos'] as $con){
                        $description = (string)$con['Descripcion'];
                        if(stripos($description,$nombreServicio)!==FALSE){
                            $costo = (double)$con['ValorUnitario'];
                            break 2;
                        }
                    }
                }
            }
        }
        return $costo;
    }
    function comprobarRif(&$instances = [],$year,$servicioId){

        if(empty($instances))
            return $instances;

        foreach($instances as $ky=>$val){
            switch($ky){
                case 1:
                case 3:
                case 5:
                case 7:
                case 9:
                case 11:
                    $costo = $this->findCostoService($ky,$year,$servicioId);
                    if($costo<=0)
                    {
                        $this->Util()->DB()->setQuery("select costo from servicio where servicioId='".$servicioId."' ");
                        $costo =  $this->Util()->DB()->GetSingle();
                    }
                $instances[$ky]['costo'] = $costo;
                break;
            }
        }
    }
    public function getRowCobranzaByInstancia($contratoId,$year,$meses=[],$whitIva=true,$depId=0){
        global $comprobante;
        $ftrTipo = " and a.tiposComprobanteId IN(1,3,4)";
        if($whitIva)
            $strIva = " a.total as total ";
        else
            $strIva ="  a.subTotal as total ";

        //create monthBase
        foreach($meses as $mes)
            $monthBase[$mes]['class'] = '#000000';

        $totalCobrado = 0;
        $totalXdepartamento = [];
        $totalRealCobranza = 0;
        $totalSinDepartamento = 0;
        foreach($monthBase as $mk=>$month){
            $sql = "SELECT MONTH(a.fecha) as mes,a.xml,year(fecha) as anio,a.comprobanteId, a.userId, $strIva, a.fecha, `status`,b.payments as payment,a.version,a.xml,a.tasaIva 
                    FROM comprobante a 
                    LEFT JOIN (select comprobanteId , sum(amount) as payments from payment where paymentStatus='activo' group by comprobanteId)  b ON a.comprobanteId=b.comprobanteId
                    WHERE YEAR(a.fecha) = ".$year." AND MONTH(a.fecha)='$mk' AND a.userId = '".$contratoId."' AND a.status = '1' $ftrTipo
                    ORDER BY a.fecha ASC";
            $this->Util()->DB()->setQuery($sql);
            $facturas = $this->Util()->DB()->GetResult();

            if(!$facturas)//si no hay facturas normales, encontrar facturas canceladas, solo interesa los totales de los mismos incluyendo iva
            {
                $sql = "SELECT MONTH(a.fecha) as mes,year(fecha) as anio,a.comprobanteId, a.userId, sum(a.total) as total, a.fecha, `status` FROM comprobante a 
                        WHERE YEAR(a.fecha) = ".$year." AND MONTH(a.fecha)='".$mk."' AND a.userId = '".$contratoId."' AND a.status = '0' $ftrTipo
				        GROUP BY MONTH(a.fecha) ORDER BY a.fecha ASC";
                $this->Util()->DB()->setQuery($sql);
                $row = $this->Util()->DB()->GetRow();
                if(!empty($row)){
                    $row['class'] ="#EFEFEF";
                    $row['payment'] =0;
                    $row['saldo'] =0;
                    $monthBase[$mk]=$row;
                }
                continue;
            }
            //encontrar los conceptos mediante los xmls
            $pago = 0;
            $totalPagoXmes = 0;
            $totalSaldoXmes = 0;
            $totalXmes=0;
            $totDepXmes= [];
            //inicio foreach facturas por mes
            foreach($facturas as $factura){
                $pago = $factura['payment']/(1+($factura['tasaIva']/100));
                $saldo= $factura['total']-$pago;
                $totalXmes +=$factura['total'];
                $totalPagoXmes +=$pago;
                $totalSaldoXmes +=$saldo;
                if(file_exists(DIR_FROM_XML."/SIGN_".$factura['xml'].".xml")){
                    $xmlData = $comprobante->getDataByXml("SIGN_".$factura['xml']);
                    foreach($xmlData['conceptos'] as $con){
                        $description = strtoupper((string)$con['Descripcion']);
                        $pu = (double)$con['ValorUnitario'];
                        $cantidad = (double)$con['Cantidad'];
                        $importe = $cantidad*$pu;

                        $index =  strpos($description,' CORRESPONDIENTE');
                        $nameService =  substr($description,0,$index);
                        $nameService =  strtoupper($nameService);
                        $this->Util()->DB()->setQuery("select departamentoId from tipoServicio where UPPER(nombreServicio)='$nameService' ");
                        $departamentoId = $this->Util()->DB()->GetSingle();
                        if($departamentoId)
                        {
                            $totDepXmes[$departamentoId] +=$importe;
                            $totalXdepartamento[$departamentoId] +=$importe;
                        }
                        else{
                            $totalXdepartamento[000000] +=$importe;
                            $totDepXmes[000000] +=$importe;
                        }
                    }
                }
            }/*fin de foreach de facturas por mes*/

            //si hay un deparamento seleccionado
            //encontrar % proporcional mensual
            if($depId>0){
                $totPropMonth= [];
                $totalServiciosMonth = array_sum($totDepXmes);
                foreach($totDepXmes as $xmes=>$cxmes){
                    $porProporcionalMonth =  ($cxmes * 100)/$totalServiciosMonth;
                    $totPropMonth[$xmes] = $totalPagoXmes * ($porProporcionalMonth/100);
                }
                $totalPagoXmes = $totPropMonth[$depId];
            }

            $month['saldo'] =  $totalSaldoXmes;
            $month['mes'] =  $mk;
            $month['anio'] =  $year;
            $month['status'] = 1;
            $month['total'] =  $totalXmes;
            $month['pago'] = $totalPagoXmes;
            if($totalSaldoXmes>0.1)
                $month['class']= $month['pago']>0 ? "#FC0":"#ff0000";
            else
                $month["class"] = "#00ff00";

            $totalCobrado +=$month['pago'];
            $monthBase[$mk] =  $month;
            $totalRealCobranza +=$totalXmes;
        }
        $data['instanciasCobranza'] = $monthBase;
        $data['totalCobrado'] =  $totalCobrado;
        //encontrar el % proporcional que le equivale a cada departamento, tomando como base el total real a cobrar
        $totalServiciosFactura = array_sum($totalXdepartamento);
        $totXdepProporcional = [];
        foreach($totalXdepartamento as  $ck=>$cobranza){
           $porcentajeProporcional =  ($cobranza * 100)/$totalServiciosFactura;
           if($depId){
               $totXdepProporcional[$ck] = $totalCobrado * ($porcentajeProporcional/100);
           }

        }
        $data['totalCobradoXdepProporcional'] =  $totXdepProporcional;
        return $data;
    }
    function getCobranzaByServicio($servicioId,$year,$meses=array(),$monthBase=[]){
        global $servicio,$comprobante;
        $servicio->setServicioId($servicioId);
        $infoServicio = $servicio->Info();
        $nombreServicio = $infoServicio['nombreServicio'];
        $contratoId = $infoServicio['contractId'];
        foreach($meses as $mes){
            $sql = "SELECT xml,cadenaOriginal,month(fecha),subTotal as total,comprobanteId,tasaIva FROM comprobante 
                    WHERE MONTH(fecha)=$mes AND YEAR(fecha)='$year' AND userId='$contratoId'  and status='1' AND tipoDeComprobante='ingreso' ";
            $this->Util()->DB()->setQuery($sql);
            $facturas = $this->Util()->DB()->GetResult();

            if(!$facturas)
            {
                $monthBase[$mes]['total'] = 0;
                $monthBase[$mes]["class"] = "#000000";;
                continue;
            }

            $totalFactura = 0;
            $totalPagos = 0;
            $totalProporcional = 0;
            foreach($facturas as $factura){
                $compId = $factura['comprobanteId'];
                $total= $factura['total'];
                $totalFactura +=$total;
                $porcentaje=0;
                if(file_exists(DIR_FROM_XML."/SIGN_".$factura['xml'].".xml")){
                    $data = $comprobante->getDataByXml("SIGN_".$factura['xml']);
                    foreach($data['conceptos'] as $con){
                        $description = (string)$con['Descripcion'];
                        $index =  strpos($description,' CORRESPONDIENTE');
                        $nameService =  substr($description,0,$index);
                        $nameService =  strtoupper($nameService);
                        $this->Util()->DB()->setQuery("select tipoServicioId,departamentoId from tipoServicio where UPPER(nombreServicio)='$nameService' ");
                        $serv= $this->Util()->DB()->GetRow();
                        if($serv["tipoServicioId"]==$infoServicio["tipoServicioId"]){
                            $importe = (double)$con['ValorUnitario'] * (double)$con['Cantidad'];
                            //calcular el % para obtener el proporcional en el pago
                            $porcentaje = ($importe*100)/$total;
                            break;
                        }
                    }
                }
                //comprobar los pagos de la factura y obtener el proporcional
                $sql = "select sum(amount) from payment where comprobanteId=$compId";
                $this->Util()->DB()->setQuery($sql);
                $pagos = $this->Util()->DB()->GetSingle();
                $pagos = $pagos/(1+($factura['tasaIva']/100));
                $totalPagos +=$pagos;
                $proporcional = $pagos*($porcentaje/100);
                $totalProporcional +=$proporcional;
            }


            $monthBase[$mes]['total'] = $totalProporcional;
            $saldo = $totalFactura-$totalPagos;
            if($saldo>0.1)
                $monthBase[$mes]["class"]= $totalPagos>0 ? "#FC0":"#ff0000";
            else
                $monthBase[$mes]["class"] = "#00ff00";

        }
        return $monthBase;
    }
}