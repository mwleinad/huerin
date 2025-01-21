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
