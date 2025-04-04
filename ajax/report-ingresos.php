<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case 'search':
			echo 'ok[#]';
			$contracts = array();
            $subordinados = $personal->GetIdResponsablesSubordinados($_POST);
            $filter = $_POST;
            $filter['subordinados'] =  $subordinados;
            $filter['tipos'] = 'activos';
            $contracts = $contract->Suggest($filter, false, true);
            $totalPeriodo = 0;
            $puestos =  $personal->getPuestos();

            foreach($contracts as $key => $value) {
                $servicios = array();
                foreach($value['servicios'] as $serv) {
                    $departamentoId = $serv["departamentoId"];
                    $costoVisual = $serv["costoVisual"];
                    $coordinate =  count($value['responsables']) > 0 ? array_search($departamentoId, array_column($value['responsables'], 'departamentoId')) : null;
                    $responsable = count($value['responsables']) > 0 && $coordinate !== false ? $value['responsables'][$coordinate] : null ;

                    switch($serv["servicioStatus"]){
                        case 'activo': $serv["nameStatusComplete"] = 'Activo'; break;
                        case 'readonly': $serv["nameStatusComplete"] = 'Activo/Solo Lectura'; break;
                        case 'bajaParcial': $serv["nameStatusComplete"] = 'Baja Temporal'; break;
                        case 'baja': $serv["nameStatusComplete"] = 'Baja'; break;
                    }
                    if($responsable !== null){
                        $jefes = array();
                        $personal->setPersonalId($responsable['personalId']);
                        $rolRes = $personal->InfoWhitRol();
                        $personal->deepJefesArray($jefes,true);
                        foreach ($puestos as $puesto) {
                            $serv[$puesto['name']] = $jefes[$puesto['name']] ?? 'NE';
                        }

                        $serv[$rolRes["nameLevel"]] = $jefes['me'];
                    }else {
                        foreach ($puestos as $puesto) {
                            $serv[$puesto['name']] = 'NE';
                        }
                    }
                    $serv['responsable'] = $responsable['name'];
                    $serv['costo'] = number_format($serv['costo'],2,'.','');

                    if($serv['periodicidad'] == 'Mensual')
                        $costoMensual = $serv['costo'];
                    elseif($serv['periodicidad'] == 'Anual')
                        $costoMensual = $serv['costo'] / 12;
                    elseif($serv['periodicidad'] == 'Bimestral')
                        $costoMensual = $serv['costo'] / 2;

                    $serv['costoMensual'] = number_format($costoMensual,2,'.','');
                    $serv['costoVisual'] = number_format($costoVisual,2,'.','');

                    if(!$util->isValidateDate($serv['inicioFactura'], 'Y-m-d'))
                        $serv['inicioFactura'] = '0000-00-00';

                    $totalPeriodo += $serv['costo'];
                    $totalMensual += $costoMensual;

                    $servicios[] = $serv;

                }//foreach
                $contracts[$key]['servicios'] = $servicios;
            }

			$smarty->assign('totalMensual', $totalMensual);
			$smarty->assign('totalPeriodo', $totalPeriodo);
			$smarty->assign("contracts", $contracts);
			$smarty->assign("puestos", $puestos);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-ingresos.tpl');
		break;
    case 'searchBitacoraAltasBajas':
        echo 'ok[#]';
        $formValues['subordinados'] = $_POST['subordinados'];
        $formValues['respCuenta'] = $_POST['responsableCuenta'];
        $formValues['departamentoId'] = $_POST["departamentoId"];
        $formValues['statusSearch'] = $_POST["statusSearch"];
        $formValues['tipoSearch'] = $_POST["tipoSearch"];
        $formValues['sinServicios'] =true;
        $formValues['cliente'] = $_POST["rfc"];
        $year = $_POST["year"];
        $mes = (int)$_POST["month"];
        $idContrato = $_POST['contractId'];
        $contracts = [];

        switch($_POST["tipoSearch"]) {
            case 'contract':
                switch($_POST["statusSearch"]){
                    case 'modificacion':
                        $ftr  = "";
                        if($mes>0)
                            $ftr .=" and month(a.fecha)='$mes' ";
                        if($year>0)
                            $ftr .=" and year(a.fecha)='$year' ";
                        if(!$_POST["customerId"]&&$_POST["contractId"]){
                                $ftr .=" and a.tablaId = '".$_POST["contractId"]."' ";
                        }
                        if(strlen($_POST["rfc"])<=0)
                            $_POST["customerId"] = "";

                        if($_POST["customerId"]&&!$_POST["contractId"]){
                            $sql = "select contractId from contract where customerId='".$_POST["customerId"]."' ";
                            $db->setQuery($sql);
                            $contratos = $db->GetResult();
                            if(count($contratos)){
                                $ids = $util->ConvertToLineal($contratos,'contractId');
                                $ftr .=" and a.tablaId IN (".implode(",",$ids).") ";
                            }
                        }
                        $sql ="SELECT *,'Modificacion' as movimiento FROM log a 
                                        INNER JOIN (select contractId,contract.name,nameContact from contract inner join customer on contract.customerId=customer.customerId) b ON a.tablaId=b.contractId
                                        WHERE a.tabla = 'contract' and a.action='update'  $ftr order by fecha desc";
                        $db->setQuery($sql);
                        $modificaciones = $db->GetResult();
                        $contratos = [];
                        foreach($modificaciones as $kmod=>$vmod){
                            $conId = $vmod["tablaId"];
                            $db->setQuery("select DATE(fecha) from contractChanges where contractId='$conId' order by contractChangesId ASC limit 1");
                            $vmod["fechaAlta"] = $db->GetSingle();
                            $contratos[] = $vmod;
                        }
                    break;
                    case 'alta':
                          if ($mes > 0)
                              $ftr .=" and month(fecha)='$mes' ";
                          if($year>0)
                              $ftr .=" and year(fecha)='$year' ";

                          include_once(DOC_ROOT.'/ajax/filterOnlyContract.php');
                          $contratos = [];
                          foreach($contracts as $kco => $vcon){
                              $conId = $vcon["contractId"];
                              $db->setQuery("select DATE(fecha) as fechaAlta,fecha from contractChanges where contractId='$conId' and status='Si' $ftr order by contractChangesId ASC limit 1");
                              $date = $db->GetRow();
                              $vcon["fechaAlta"] = $date["fechaAlta"];
                              $vcon["fecha"] = $date["fecha"];

                              if (!$util->isValidateDate($vcon["fechaAlta"], "Y-m-d"))
                                  continue;

                              $vcon["movimiento"]="Alta";
                              $contratos[] = $vcon;
                          }
                    break;
                    case 'baja':
                        if ($mes > 0)
                            $ftr .=" and month(fecha)='$mes' ";
                        if($year>0)
                            $ftr .=" and year(fecha)='$year' ";
                        $formValues['activos'] =false;
                        include_once(DOC_ROOT.'/ajax/filterOnlyContract.php');
                        $contratos = [];
                        foreach($contracts as $kco => $vcon){
                            $conId = $vcon["contractId"];
                            $db->setQuery("select DATE(fecha) as fechaAlta,fecha from contractChanges where contractId='$conId' and status='Si' $ftr order by contractChangesId ASC limit 1");
                            $date = $db->GetRow();
                            $vcon["fechaAlta"] = $date["fechaAlta"];

                            $db->setQuery("select DATE(fecha) as fechaBaja,fecha from contractChanges where contractId='$conId' and status='No' $ftr order by contractChangesId DESC limit 1");
                            $date2 = $db->GetRow();
                            $vcon["fechaBaja"] = $date2["fechaBaja"];
                            $vcon["fecha"] = $date2["fecha"];
                            if (!$util->isValidateDate($vcon["fechaBaja"], "Y-m-d"))
                                continue;

                            $vcon["movimiento"]="Baja";
                            $contratos[] = $vcon;
                        }
                        break;
                }
                $smarty->assign("contratos", $contratos);
                $smarty->display(DOC_ROOT.'/templates/lists/report-up-down-contract.tpl');
            break;
            case 'service':
                $registros = [];
                switch($_POST["statusSearch"]){
                    case 'modificacion':
                    case 'alta':
                        if($_POST["statusSearch"]=="modificacion"){
                            $action="update";
                            $movimiento = "Modificacion";
                        }elseif($_POST["statusSearch"]=="alta"){
                            $action="insert";
                            $movimiento = "Alta";
                        }
                       $ftr ="";
                        if($mes>0)
                            $ftr .=" and month(a.fecha)='$mes' ";
                        if($year>0)
                            $ftr .=" and year(a.fecha)='$year' ";
                        if(strlen($_POST["rfc"])<=0)
                            $_POST["customerId"] = "";

                        if($_POST["contractId"]){
                            //obtener los servicios de la razon s.
                            $db->setQuery("select servicioId from servicio where contractId='".$_POST["contractId"]."' ");
                            $serviciosEncontrados = $db->GetResult();
                            if(count($serviciosEncontrados)>0){
                                $idServiciosEncontrados = $util->ConvertToLineal($serviciosEncontrados,'servicioId');
                                $ftr .= " and tablaId IN (".implode(",",$idServiciosEncontrados).") ";
                            }else{
                                $ftr .=" and tablaId IN(0) ";
                            }
                        }elseif($_POST["customerId"]){
                            //obtener los servicios de la razon s.
                            $db->setQuery("select contractId from contract where customerId='".$_POST["customerId"]."' ");
                            $contratosEncontrados = $db->GetResult();
                            if(count($contratosEncontrados)>0){
                                $idContratosEncontrados = $util->ConvertToLineal($contratosEncontrados,'contractId');
                                $db->setQuery("select servicioId from servicio where contractId IN(".implode(",",$idContratosEncontrados).") ");
                                $serviciosEncontrados = $db->GetResult();
                                if(count($serviciosEncontrados)>0){
                                    $idServiciosEncontrados = $util->ConvertToLineal($serviciosEncontrados,'servicioId');
                                    $ftr .= " and tablaId IN (".implode(",",$idServiciosEncontrados).") ";
                                }else{
                                    $ftr .=" and tablaId IN(0) ";
                                }
                            }else{
                                $ftr .=" and tablaId IN(0) ";
                            }
                        }
                        $sql ="SELECT a.fecha,a.personalId,a.tablaId,a.fecha,b.nombreServicio,b.servicioId,b.tipoServicioId,
                               b.inicioOperaciones,b.inicioFactura,b.periodicidad,b.departamentoId,b.contractId,b.status,
                               c.name,c.nameContact,'$movimiento' as movimiento, b.uniqueInvoice 
                               FROM log a 
                               INNER JOIN (select contractId,servicioId,servicio.tipoServicioId,servicio.status,
                                           nombreServicio,inicioOperaciones,inicioFactura,periodicidad,departamentoId,
                                           uniqueInvoice      
                                           from servicio 
                                           inner join tipoServicio on servicio.tipoServicioId=tipoServicio.tipoServicioId) b ON a.tablaId=b.servicioId
                               INNER JOIN (select contractId,contract.name,nameContact 
                                           from contract 
                                           inner join customer on contract.customerId=customer.customerId) c ON b.contractId=c.contractId
                               WHERE a.tabla = 'servicio' and a.action='$action'  $ftr order by a.fecha desc";
                        $db->setQuery($sql);
                        $modificaciones = $db->GetResult();
                        $registros = [];
                        foreach($modificaciones as $kmod=>$vmod) {
                            $servId = $vmod["tablaId"];
                            $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' and status='activo' order by historyChangesId ASC limit 1");
                            $vmod["fechaAlta"] = $db->GetSingle();
                            $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' and status='baja' order by historyChangesId DESC limit 1");
                            $vmod["fechaBaja"] = $db->GetSingle();
                            //encontrar encargados
                            $encargados = $contractRep->encargadosCustomKey('departamentoId', 'name', $vmod['contractId']);
                            $encargados2 = $contractRep->encargadosCustomKey('departamentoId', 'personalId', $vmod['contractId']);
                            $vmod["responsable"] = $encargados[$vmod['departamentoId']];
                            $personal->setPersonalId($encargados2[$vmod["departamentoId"]]);
                            $ordeJefes = $personal->getOrdenJefes();
                            $vmod["supervisor"] = $ordeJefes["supervisor"];
                            switch ($vmod["status"]) {
                                case 'activo':
                                    $vmod["currentState"] = "Activo";
                                    break;
                                case 'readonly':
                                    $vmod["currentState"] = "Activo solo lectura";
                                    break;
                                case 'bajaParcial':
                                    $vmod["currentState"] = "Baja temporal";
                                    break;
                                case 'baja':
                                    $vmod["currentState"] = "Baja";
                                    break;
                            }
                            $registros[] = $vmod;
                        }
                    break;
                    case 'baja':
                        $ftr ="";
                        if($mes>0)
                            $ftr .=" and month(a.fecha)='$mes' ";
                        if($year>0)
                            $ftr .=" and year(a.fecha)='$year' ";
                        if(strlen($_POST["rfc"])<=0)
                            $_POST["customerId"] = "";
                        if($_POST["contractId"]){
                            //obtener los servicios de la razon s.
                            $db->setQuery("select servicioId from servicio where contractId='".$_POST["contractId"]."' ");
                            $serviciosEncontrados = $db->GetResult();
                            if(count($serviciosEncontrados)>0){
                                $idServiciosEncontrados = $util->ConvertToLineal($serviciosEncontrados,'servicioId');
                                $ftr .= " and a.servicioId IN (".implode(",",$idServiciosEncontrados).") ";
                            }else{
                                $ftr .=" and a.servicioId IN(0) ";
                            }
                        }elseif($_POST["customerId"]){
                            //obtener los servicios de la razon s.
                            $db->setQuery("select contractId from contract where customerId='".$_POST["customerId"]."' ");
                            $contratosEncontrados = $db->GetResult();
                            if(count($contratosEncontrados)>0){
                                $idContratosEncontrados = $util->ConvertToLineal($contratosEncontrados,'contractId');
                                $db->setQuery("select servicioId from servicio where contractId IN(".implode(",",$idContratosEncontrados).") ");
                                $serviciosEncontrados = $db->GetResult();
                                if(count($serviciosEncontrados)>0){
                                    $idServiciosEncontrados = $util->ConvertToLineal($serviciosEncontrados,'servicioId');
                                    $ftr .= " and a.servicioId IN (".implode(",",$idServiciosEncontrados).") ";
                                }else{
                                    $ftr .=" and a.servicioId IN(0) ";
                                }
                            }else{
                                $ftr .=" and a.servicioId IN(0) ";
                            }
                        }
                        $sql ="SELECT c.name,c.nameContact,c.contractId,b.nombreServicio,b.servicioId,b.tipoServicioId,b.inicioOperaciones,b.inicioFactura,b.periodicidad,
                                      b.departamentoId,b.status,b.lastDateWorkflow, 'Baja' as movimiento,a.status as action,a.fecha,a.personalId,a.namePerson, b.uniqueInvoice
                               FROM historyChanges a 
                               INNER JOIN (SELECT contractId,servicioId,servicio.tipoServicioId,nombreServicio,inicioOperaciones,inicioFactura,periodicidad,departamentoId,servicio.status,
                                           servicio.lastDateWorkflow, tipoServicio.uniqueInvoice
                                           FROM servicio 
                                           INNER JOIN tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId 
                                           WHERE servicio.status IN('baja','bajaParcial')) b ON a.servicioId=b.servicioId
                               INNER JOIN (SELECT contractId,contract.name,nameContact 
                                           FROM contract
                                           INNER JOIN customer ON contract.customerId=customer.customerId) c ON b.contractId=c.contractId
                               WHERE a.status IN('baja','bajaParcial') $ftr 
                               ORDER BY a.fecha DESC";
                        $db->setQuery($sql);
                        $modificaciones = $db->GetResult();
                        $registros = [];
                        foreach($modificaciones as $kmod=>$vmod) {
                            $servId = $vmod["servicioId"];
                            $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' and status='activo' order by historyChangesId ASC limit 1");
                            $vmod["fechaAlta"] = $db->GetSingle();
                            if($vmod['action']=='bajaParcial'){
                                $vmod["fechaBaja"] = $vmod["lastDateWorkflow"];
                            }else{
                                $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' and status='baja' order by historyChangesId DESC limit 1");
                                $vmod["fechaBaja"] = $db->GetSingle();
                            }

                            //encontrar encargados
                            $encargados = $contractRep->encargadosCustomKey('departamentoId', 'name', $vmod['contractId']);
                            $encargados2 = $contractRep->encargadosCustomKey('departamentoId', 'personalId', $vmod['contractId']);
                            $vmod["responsable"] = $encargados[$vmod['departamentoId']];
                            $personal->setPersonalId($encargados2[$vmod["departamentoId"]]);
                            $ordeJefes = $personal->getOrdenJefes();
                            $vmod["supervisor"] = $ordeJefes["Supervisor"];
                            switch ($vmod["status"]) {
                                case 'activo':
                                    $vmod["currentState"] = "Activo";
                                    break;
                                case 'readonly':
                                    $vmod["currentState"] = "Activo solo lectura";
                                    break;
                                case 'bajaParcial':
                                    $vmod["currentState"] = "Baja temporal";
                                    break;
                                case 'baja':
                                    $vmod["currentState"] = "Baja";
                                    break;
                            }
                            $registros[] = $vmod;
                        }
                }
                $smarty->assign("registros", $registros);
                $smarty->display(DOC_ROOT.'/templates/lists/report-up-down.tpl');
            break;
        }
    case 'searchAltasBajas':
        $formValues['subordinados'] = $_POST['subordinados'];
        $formValues['respCuenta'] = $_POST['responsableCuenta'];
        $formValues['departamentoId'] = $_POST["departamentoId"];
        $formValues['statusSearch'] = $_POST["statusSearch"];
        $formValues['tipoSearch'] = $_POST["tipoSearch"];
        $formValues['sinServicios'] =true;
        $formValues['cliente'] = $_POST["rfc"];
        $idContrato = $_POST['contractId'];
        $contracts = [];
        $reportService = new ReportService();
        $results = $reportService->getAbServices();
        echo "ok[#]";
        $smarty->assign("registros", $results);
        $smarty->display(DOC_ROOT.'/templates/lists/reporte-ab-all.tpl');
    break;

    case 'exportarServiciosPorFacturar':
        global $monthsIntComplete;
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle("Servicios a facturar");

        $mesAnioSiguiente = strtotime( date("Y-m")." +1 month");
        $mesSiguiente = (int)date('m', $mesAnioSiguiente);

        $styleTotalTexto = [
            'font' => [
                'bold' => true,
                'color' => array('rgb' => '000000'),
                'size' => 9,
                'name' => 'Aptos',
            ],
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'D57A29')
            ),
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,
            ],
        ];

        $styleHeader = [
            'font' => [
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 9,
                'name' => 'Aptos',
            ],
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000')
            ),
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,
            ],
        ];

        $styleTexto = [
            'font' => [
                'bold' => false,
                'color' => array('rgb' => '000000'),
                'size' => 9,
                'name' => 'Aptos',
            ],
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,
            ],
        ];
        $styleCurrency = [
            'font' => [
                'bold' => false,
                'color' => array('rgb' => '000000'),
                'size' => 9,
                'name' => 'Aptos',
            ],
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ];

        $styleTotalCurrency = [
            'font' => [
                'bold' => true,
                'color' => array('rgb' => '000000'),
                'size' => 9,
                'name' => 'Aptos',
            ],
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'D57A29')
            ),
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ];

        $fields = [
            [
                'name' => 'cliente',
                'title' => 'Cliente',
            ],
            [
                'name' => 'empresa',
                'title' => 'Empresa',
            ],
            [
                'name' => 'facturador',
                'title' => 'Facturador',
            ],
            [
                'name' => 'nomenclatura',
                'title' => 'Nomenclatura',
            ],
            [
                'name' => 'servicio',
                'title' => 'Servicio',
            ],
            [
                'name' => 'tipo',
                'title' => 'Tipo',
            ],
            [
                'name' => 'periodicidad',
                'title' => 'Periodicidad',
            ],
            [
                'name' => 'inicio_operacion',
                'title' => 'Inicio operacion',
            ],
            [
                'name' => 'inicio_facturacion',
                'title' => 'Inicio facturacion',
            ],
            [
                'name' => 'estatus_servicio',
                'title' => 'Estatus',
            ],
            [
                'name' => 'fecha_baja_temporal',
                'title' => 'Fecha baja temporal',
            ],
            [
                'name' => 'costo',
                'title' => 'Costo',
            ],
        ];

        $row=1;

        $col = 0;
        foreach ($fields as $field) {
            $sheet->setCellValueByColumnAndRow($col, $row, '')
                ->getStyleByColumnAndRow($col, $row)
                ->applyFromArray($styleHeader);

            $sheet->setCellValueByColumnAndRow($col, $row + 1, $col === 0 ? "FECHA: " . DATE('Y-m-d H:i:s') : "")
                ->getStyleByColumnAndRow($col, $row + 1)
                ->applyFromArray($styleHeader);

            $sheet->setCellValueByColumnAndRow($col, $row + 2, $col === 0 ? 'REPORTE DE SERVICIOS A FACTURAR CORRESPONDIENTE A ' . mb_strtoupper($monthsIntComplete[$mesSiguiente]) . " " . date('Y') : '')
                ->getStyleByColumnAndRow($col, $row + 2)
                ->applyFromArray($styleHeader);

            $sheet->setCellValueByColumnAndRow($col, $row + 3, '')
                ->getStyleByColumnAndRow($col, $row + 3)
                ->applyFromArray($styleHeader);

            $sheet->setCellValueByColumnAndRow($col, $row + 4, $field['title'])
                ->getStyleByColumnAndRow($col, $row + 4)
                ->applyFromArray($styleHeader);
            $col++;
        }


        $row +=5;

        $sql = "SELECT
            REPLACE
                (
                    REPLACE (
                        REPLACE (
                            REPLACE (
                                REPLACE (
                                    REPLACE ( REPLACE ( TRIM( REGEXP_REPLACE ( empresas.cliente, '\\\s{2,}', ' ' )), 'LE?N', 'LEÓN' ), 'NU?EZ', 'NUÑEZ' ),
                                    'PATI?O',
                                    'PATIÑO' 
                                ),
                                'VILLASE?OR',
                                'VILLASEÑOR' 
                            ),
                            'MAR?A',
                            'MARÍA' 
                        ),
                        'CA?IZO',
                        'CAÑIZO' 
                    ),
                    'MU?OZ',
                    'MUÑOZ' 
                ) cliente,
            REPLACE(TRIM(REGEXP_REPLACE (empresas.razon_social, '\\\s{2,}', '' )), '&amp;', '&' ) empresa,(
            SELECT
                razonSocial 
            FROM
                rfc 
            WHERE
                claveFacturador = empresas.facturador 
            ) facturador,
            TRIM( servicios.nomenclatura ) nomenclatura,
            TRIM(
            REGEXP_REPLACE ( servicios.nombre, '\\\s{2,}', '' )) AS servicio,
        IF
            ( DAYNAME( servicios.inicio_operacion ) IS NOT NULL, servicios.inicio_operacion, '' ) inicio_operacion,
        IF
            ( DAYNAME( servicios.inicio_facturacion ) IS NOT NULL, servicios.inicio_facturacion, '' ) inicio_facturacion,
            servicios.costo,
            servicios.periodicidad,
            servicios.tipo,
            servicios.estatus_servicio,
            servicios.fecha_baja_temporal
        FROM
            (
            SELECT
                servicio.servicioId,
                IF(servicio.`status` = 'bajaParcial', 'Baja temporal','Activo') estatus_servicio,
                servicio.costo,
                servicio.inicioOperaciones inicio_operacion,
                servicio.inicioFactura inicio_facturacion,
                servicio.contractId,
                SUBSTRING_INDEX( tipoServicio.nombreServicio, ' ', 1 ) AS nomenclatura,
                TRIM(
                    SUBSTRING(
                        tipoServicio.nombreServicio,
                        LENGTH(
                        SUBSTRING_INDEX( tipoServicio.nombreServicio, ' ', 1 ))+ 1,
                    LENGTH( tipoServicio.nombreServicio ))) AS nombre,
                tipoServicio.periodicidad,
                IF(tipoServicio.is_primary,'Primario','Secundario') tipo,
                IF(servicio.status='bajaParcial',servicio.lastDateWorkflow, null) fecha_baja_temporal,
                IF(servicio.status='bajaParcial',DATE_FORMAT(servicio.lastDateWorkflow,'%Y-%m') < '2025-01', false) servicio_temporal_antiguo,
                IF(tipoServicio.periodicidad='Eventual',DATE_FORMAT(servicio.inicioFactura,'%Y-%m') < '2025-01', false) servicio_eventual_antiguo
            FROM
                servicio
                INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId 
            WHERE
                tipoServicio.`status` = '1' 
                AND servicio.`status` IN ('activo','bajaParcial') 
                AND tipoServicio.nombreServicio NOT LIKE '%Z*%' 
                AND WEEK(inicioFactura) is not null
                AND exists (select * from task where ISNULL(finalEffectiveDate) and stepId in (select stepId from step where servicioId=tipoServicio.tipoServicioId))
                HAVING (servicio_temporal_antiguo=false and servicio_eventual_antiguo=false)
            ) servicios
            INNER JOIN (
            SELECT
                customer.nameContact AS cliente,
                customer.active AS estatus_cliente,
                contract.contractId,
                contract.activo AS estatus_empresa,
                contract.`name` razon_social,
                contract.facturador 
            FROM
                contract
                INNER JOIN customer ON contract.customerId = customer.customerId 
            ) empresas ON servicios.contractId = empresas.contractId 
            AND empresas.estatus_empresa = 'Si' 
            AND empresas.estatus_cliente = '1' 
            AND empresas.cliente != 'CAPACITACION' 
            AND empresas.razon_social != 'CAPACITACION' 
        ORDER BY
            empresas.facturador ASC,
            empresas.cliente ASC,
            empresas.razon_social ASC";


        $db->setQuery($sql);
        $results = $db->GetResult();

            $periodicidades = [
            [
                'id' => 'Eventual',
                'meses' => 0,
                'texto' => 'Eventual',
            ],
            [
                'id' => 'Mensual',
                'meses' => 1,
                'texto' => 'Mensual',
            ],
            [
                'id' => 'Bimestral',
                'meses' => 2,
                'texto' => 'Bimestral',
            ],
            [
                'id' => 'Trimestral',
                'meses' => 3,
                'texto' => 'Trimestral',
            ],
            [
                'id' => 'Cuatrimestral',
                'meses' => 4,
                'texto' => 'Cuatrimestral',
            ],
            [
                'id' => 'Semestral',
                'meses' => 6,
                'texto' => 'Semestral',
            ],
            [
                'id' => 'Anual',
                'meses' => 12,
                'texto' => 'Anual',
            ],
            [
                'id' => 'Bianual',
                'meses' => 24,
                'texto' => 'Bianual',
            ]

        ];

        $currentEmpresa = $results[0]['empresa'] ?? '';
        $serviciosPorEmpresa = 0;
        $rowInicialSuma = $row;
        $colsSumServiciosPorFacturar = [];

        $colsGranTotalMensual = [];

        $indexColTotal = count($fields)-1;

        foreach($results as $key => $result) {

            $col = 0;
            $mesAnioInicioOperacion   = strtotime(date("Y-m", strtotime($result['inicio_operacion'])));
            $mesAnioInicioFacturacion = strtotime(date("Y-m", strtotime($result['inicio_facturacion'])));

            $periodicidad = current(array_filter($periodicidades, fn($per) => ($per['id'] === $result['periodicidad'])));
            $mesesTranscurridos = floor(($mesAnioSiguiente-$mesAnioInicioFacturacion)/(30 * 24 * 60 * 60));


            if ($periodicidad['id'] === 'Eventual') {

                $realizaFacturaSiguiente = ($mesAnioInicioFacturacion === $mesAnioSiguiente) && $mesAnioInicioFacturacion <= $mesAnioSiguiente;
            } else {

                $realizaFacturaSiguiente = ($mesesTranscurridos%$periodicidad['meses'] ===0) && $mesAnioInicioFacturacion <= $mesAnioSiguiente;
                if ($result['estatus_servicio'] === 'Baja temporal') {

                    $mesAnioFechaBajaTemporal = strtotime(date("Y-m",strtotime($result['fecha_baja_temporal'])));
                    $realizaFacturaSiguiente = ($mesAnioSiguiente > $mesAnioFechaBajaTemporal && $mesAnioInicioFacturacion <= $mesAnioSiguiente);
                }
            }


            $serviciosPorEmpresa++;
            foreach ($fields as $field) {
                $valor = $result[$field['name']] ?? '';

                 $styles = in_array($field['name'],['costo'])
                     ? $styleCurrency
                     : $styleTexto;

                $cordenada = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                $sheet->setCellValueByColumnAndRow($col, $row, $valor)
                    ->getStyle($cordenada)
                    ->applyFromArray($styles);

                $col++;
            }

            $colTotalServicio = PHPExcel_Cell::stringFromColumnIndex($indexColTotal).$row;
            if ($realizaFacturaSiguiente)
                $colsSumServiciosPorFacturar[] = $colTotalServicio;

            $row++;

            if ($currentEmpresa !== $results[$key + 1]['empresa']) {

                $currentEmpresa = $results[$key + 1]['empresa'];

                $serviciosPorEmpresa = 0;

                $formula = count($colsSumServiciosPorFacturar) ? "=SUM(".implode("+", $colsSumServiciosPorFacturar).")" : 0;
                $colTotal = PHPExcel_Cell::stringFromColumnIndex($indexColTotal).$row;
                $sheet->setCellValueByColumnAndRow($indexColTotal, $row, $formula)
                    ->getStyle($colTotal)
                    ->applyFromArray($styleTotalCurrency);

                $colsGranTotalMensual[] = $colTotal;

                $row++;
                $colsSumServiciosPorFacturar = [];
            }
        }

        $indexColTextoGranTotal = count($fields)-2;
        $sheet->setCellValueByColumnAndRow($indexColTextoGranTotal, $row, "GRAN TOTAL")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($indexColTextoGranTotal).$row)
            ->applyFromArray($styleTotalTexto);


        $indexColGranTotal = count($fields)-1;
        $colGranTotal = PHPExcel_Cell::stringFromColumnIndex($indexColGranTotal).$row;
        $formula = "=SUM(".implode('+', $colsGranTotalMensual).")";
        $sheet->setCellValueByColumnAndRow($indexColGranTotal, $row, $formula)
            ->getStyle($colGranTotal)
            ->applyFromArray($styleTotalCurrency);

        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet1->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "consolidado_de_servicios_a_facturar.xlsx";
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
        echo WEB_ROOT . "/download.php?file=" . WEB_ROOT . "/sendFiles/" . $nameFile;
        break;
    case 'generarExcelDeServiciosParaP2':
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle("Formato Plataforma 2");

        $fields = [
            [
                'name' => 'cliente',
                'title' => 'Cliente',
            ],
            [
                'name' => 'empresa',
                'title' => 'Empresa',
            ],
            [
                'name' => 'facturador',
                'title' => 'Facturador',
            ],
            [
                'name' => 'nomenclatura',
                'title' => 'Nomenclatura',
            ],
            [
                'name' => 'servicio',
                'title' => 'Servicio',
            ],
            [
                'name' => 'inicio_operacion',
                'title' => 'Inicio operacion',
            ],
            [
                'name' => 'inicio_facturacion',
                'title' => 'Inicio facturacion',
            ],
            [
                'name' => 'costo',
                'title' => 'Costo',
            ],
        ];

        $row=1;

        $col = 0;
        foreach ($fields as $field) {
            $sheet->setCellValueByColumnAndRow($col, $row, $field['title'])
                ->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $col++;
        }

        $row++;

        $sql = "SELECT
            REPLACE
                (
                    REPLACE (
                        REPLACE (
                            REPLACE (
                                REPLACE (
                                    REPLACE ( REPLACE ( TRIM( REGEXP_REPLACE ( empresas.cliente, '\\\s{2,}', ' ' )), 'LE?N', 'LEÓN' ), 'NU?EZ', 'NUÑEZ' ),
                                    'PATI?O',
                                    'PATIÑO' 
                                ),
                                'VILLASE?OR',
                                'VILLASEÑOR' 
                            ),
                            'MAR?A',
                            'MARÍA' 
                        ),
                        'CA?IZO',
                        'CAÑIZO' 
                    ),
                    'MU?OZ',
                    'MUÑOZ' 
                ) cliente,
            REPLACE(TRIM(REGEXP_REPLACE (empresas.razon_social, '\\\s{2,}', '' )), '&amp;', '&' ) empresa,(
            SELECT
                razonSocial 
            FROM
                rfc 
            WHERE
                claveFacturador = empresas.facturador 
            ) facturador,
            TRIM( servicios.nomenclatura ) nomenclatura,
            TRIM(
            REGEXP_REPLACE ( servicios.nombre, '\\\s{2,}', '' )) AS servicio,
        IF
            ( DAYNAME( servicios.inicio_operacion ) IS NOT NULL, servicios.inicio_operacion, '' ) inicio_operacion,
        IF
            ( DAYNAME( servicios.inicio_facturacion ) IS NOT NULL, servicios.inicio_facturacion, '' ) inicio_facturacion,
            servicios.costo 
        FROM
            (
            SELECT
                servicio.servicioId,
                servicio.`status` estatus_servicio,
                servicio.costo,
                servicio.inicioOperaciones inicio_operacion,
                servicio.inicioFactura inicio_facturacion,
                servicio.contractId,
                SUBSTRING_INDEX( tipoServicio.nombreServicio, ' ', 1 ) AS nomenclatura,
                TRIM(
                    SUBSTRING(
                        tipoServicio.nombreServicio,
                        LENGTH(
                        SUBSTRING_INDEX( tipoServicio.nombreServicio, ' ', 1 ))+ 1,
                    LENGTH( tipoServicio.nombreServicio ))) AS nombre 
            FROM
                servicio
                INNER JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId 
            WHERE
                tipoServicio.`status` = '1' 
                AND servicio.`status` IN ( 'activo' ) 
                AND tipoServicio.nombreServicio NOT LIKE '%Z*%'  
                AND tipoServicio.nombreServicio LIKE '%2025%'
                AND tipoServicio.is_primary = 1
                AND exists (select * from task where ISNULL(finalEffectiveDate) and stepId in (select stepId from step where servicioId=tipoServicio.tipoServicioId))
            ) servicios
            INNER JOIN (
            SELECT
                customer.nameContact AS cliente,
                customer.active AS estatus_cliente,
                contract.contractId,
                contract.activo AS estatus_empresa,
                contract.`name` razon_social,
                contract.facturador 
            FROM
                contract
                INNER JOIN customer ON contract.customerId = customer.customerId 
            ) empresas ON servicios.contractId = empresas.contractId 
            AND empresas.estatus_empresa = 'Si' 
            AND empresas.estatus_cliente = '1' 
            AND empresas.cliente != 'CAPACITACION' 
            AND empresas.razon_social != 'CAPACITACION' 
        ORDER BY
            empresas.cliente ASC,
            empresas.razon_social ASC";


        $db->setQuery($sql);
        $results = $db->GetResult();

        foreach($results as $result) {
            $col = 0;

            foreach ($fields as $field) {
                $valor = $result[$field['name']] ?? '';
                $sheet->setCellValueByColumnAndRow($col, $row, $valor);
                $col++;
            }
            $row++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet1->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "formato_servicios_empresas_plataforma_20.xlsx";
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
        echo WEB_ROOT . "/download.php?file=" . WEB_ROOT . "/sendFiles/" . $nameFile;
        break;
}
