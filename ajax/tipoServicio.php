<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
include_once(DOC_ROOT . '/libs/excel/PHPExcel.php');

switch($_POST["type"])
{
	case "addTipoServicio":
	        $data['title'] = "Agregar servicio";
	        $data['form'] = "frm-servicio";
			$departamentos = $personal->ListDepartamentos();
            $smarty->assign('data', $data);
			$smarty->assign('servicios', $tipoServicio->EnumerateAll());
			$smarty->assign("departamentos", $departamentos);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
            $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
            $json['secondary_services'] =  $tipoServicio->EnumerateServiceGroupByDepForSelect2(0);
            $json['current_secondary'] = '';
            echo json_encode($json);
	break;
	case "saveAddTipoServicio":
			$mostrarCostoVisual = ($_POST['mostrarCostoVisual']) ? '1' : '0';
			$tipoServicio->setNombreServicio($_POST['nombreServicio']);
			$tipoServicio->setCosto($_POST['costo']);
			$tipoServicio->setCostoUnico($_POST['costoUnico']);
			$tipoServicio->setPeriodicidad($_POST['periodicidad']);
			$tipoServicio->setDepartamentoId($_POST['departamentoId']);
			$tipoServicio->setCostoVisual($_POST['costoVisual']);
			$tipoServicio->setMostrarCostoVisual($mostrarCostoVisual);
            $tipoServicio->setUniqueInvoice(isset($_POST['uniqueInvoice']) ? 1 : 0);
        	$tipoServicio->setClaveSat($_POST['claveSat']);
        	$tipoServicio->setIsPrimary((int) $_POST['isPrimary'] );
            $tipoServicio->setConceptoMesVencido(isset($_POST['concepto_mes_vencido']) ? 1 : 0);
			if(!$tipoServicio->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resTipoServicio = $tipoServicio->EnumerateOnePage();
				$smarty->assign("resTipoServicio", $resTipoServicio);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoServicio.tpl');
			}

		break;
	case "deleteTipoServicio":
			$tipoServicio->setTipoServicioId($_POST['tipoServicioId']);
			if($tipoServicio->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resTipoServicio = $tipoServicio->EnumerateOnePage();
				$smarty->assign("resTipoServicio", $resTipoServicio);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoServicio.tpl');
			}
		break;
	case "editTipoServicio":
            $data['title'] = "Editar servicio";
            $data['form'] = "frm-servicio";
			$departamentos = $personal->ListDepartamentos();
            $smarty->assign("data", $data);
			$smarty->assign("departamentos", $departamentos);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$tipoServicio->setTipoServicioId($_POST['tipoServicioId']);
			$myTipoServicio = $tipoServicio->Info(true);

			$myTipoServicio['template'] = is_file(PUBLIC_STORAGE_PROSPECT. "/service/template_".$myTipoServicio['tipoServicioId'].".docx")
                                          ? HTTP_STORAGE_PROSPECT. "/file_service/template_".$myTipoServicio['tipoServicioId'].".docx"
                                          : "";
            $smarty->assign('servicios', $tipoServicio->EnumerateAll());
			$smarty->assign("post", $myTipoServicio);
            $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
            $secondaryServices =  $tipoServicio->EnumerateServiceGroupByDepForSelect2(0);
            $currentSecondary = is_array($myTipoServicio['current_secondary']) ? array_column($myTipoServicio['current_secondary'], 'secondary_id'): [];
            $json['secondary_services'] = $secondaryServices;
            $json['current_secondary'] = implode(',', $currentSecondary);
            echo json_encode($json);
		break;

	case "saveEditTipoServicio":
			$mostrarCostoVisual = ($_POST['mostrarCostoVisual']) ? '1' : '0';
			$tipoServicio->setTipoServicioId($_POST['tipoServicioId']);
			$tipoServicio->setNombreServicio($_POST['nombreServicio']);
			$tipoServicio->setCosto($_POST['costo']);
			$tipoServicio->setPeriodicidad($_POST['periodicidad']);
			$tipoServicio->setCostoUnico($_POST['costoUnico']);
			$tipoServicio->setDepartamentoId($_POST['departamentoId']);
			$tipoServicio->setCostoVisual($_POST['costoVisual']);
			$tipoServicio->setMostrarCostoVisual($mostrarCostoVisual);
			$tipoServicio->setUniqueInvoice(isset($_POST['uniqueInvoice']) ? 1 : 0);
        	$tipoServicio->setClaveSat($_POST['claveSat']);
            $tipoServicio->setIsPrimary((int) $_POST['isPrimary']);
            $tipoServicio->setConceptoMesVencido(isset($_POST['concepto_mes_vencido']) ? 1 : 0);
			if(!$tipoServicio->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resTipoServicio = $tipoServicio->EnumerateOnePage();
				$smarty->assign("resTipoServicio", $resTipoServicio);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoServicio.tpl');
			}
		break;
	case "openConfigTextToReport":
		$data['title'] = 'Cofiguracion de textos';
		$data['form'] = 'frm-config-text-service';

		$tipoServicio->setTipoServicioId($_POST['id']);
		$post = $tipoServicio->GetTextReportByServicio();
		if(!$post)
			$post['service_id'] = $_POST['id'];
		$smarty->assign('data', $data);
		$smarty->assign('post', $post);
		$smarty->display(DOC_ROOT."/templates/boxes/general-popup.tpl");
	break;
	case 'saveTextReport':
	    if(isset($_POST['id']))
		    $tipoServicio->setActivityServiceId($_POST['id']);

		$tipoServicio->setTipoServicioId($_POST['service_id']);
		$tipoServicio->setLargeDescription($_POST['large_description']);
		$tipoServicio->setShortDescription($_POST['short_description']);
		$tipoServicio->setExpectation($_POST['expectation']);
		$tipoServicio->setRequestInformation($_POST['request_information']);
		$tipoServicio->setWorkSchedule($_POST['work_schedule']);
		$tipoServicio->setReports($_POST['reports']);
		if(!$tipoServicio->SaveTextReport()) {
			echo "fail[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		} else {
			echo "ok[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
		}
	break;
    case 'inheritanceFrom':
        $tipoServicio->setTipoServicioId($_POST['id']);
        $steps = $tipoServicio->getSteps();
        $smarty->assign('steps', $steps);
        $smarty->display(DOC_ROOT."/templates/forms/form-partial-inheritance.tpl");
    break;

    case 'exportarExcel':
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('Catalogo de servicios');

        $row=1;

        $sheet->setCellValueByColumnAndRow(0, $row, 'Area')
              ->getStyleByColumnAndRow()->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(1, $row, 'Nomenclatura')
            ->getStyleByColumnAndRow(1, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(2, $row, 'Nombre')
            ->getStyleByColumnAndRow(2, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(3, $row, 'Clave SAT')
            ->getStyleByColumnAndRow(3, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(4, $row, 'Periodicidad')
            ->getStyleByColumnAndRow(4, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(5, $row, 'Periodicidad facturacion')
            ->getStyleByColumnAndRow(5, $row)->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(6, $row, 'Costo')
            ->getStyleByColumnAndRow(6, $row)->getFont()->setBold(true);

        $row++;

        $sql = "select 
                    (select departamento from departamentos where departamentos.departamentoId = tipoServicio.departamentoId limit 1) as area, 
                    SUBSTRING_INDEX(nombreServicio,' ',1) nomenclatura, 
                    TRIM(SUBSTRING(nombreServicio, LENGTH(SUBSTRING_INDEX(nombreServicio,' ',1))+1, LENGTH(nombreServicio))) as nombre,
                    claveSat clave_sat,
                    periodicidad,
                    periodicidad periodicidad_de_facturacion,
                    costoVisual costo
                    from tipoServicio
                where status = '1' 
                AND nombreServicio 
                NOT LIKE '%Z*%' HAVING area != ''
        ";

        $db->setQuery($sql);
        $results = $db->GetResult();

        foreach($results as $result) {
            $sheet->setCellValueByColumnAndRow(0, $row, $result['area']);
            $sheet->setCellValueByColumnAndRow(1, $row, $result['nomenclatura']);
            $sheet->setCellValueByColumnAndRow(2, $row, $result['nombre']);
            $sheet->setCellValueByColumnAndRow(3, $row, $result['clave_sat']);
            $sheet->setCellValueByColumnAndRow(4, $row, $result['periodicidad']);
            $sheet->setCellValueByColumnAndRow(5, $row, $result['periodicidad']);
            $sheet->setCellValueByColumnAndRow(6, $row, $result['costo']);

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
        $nameFile = "catalogo_servicios_activo.xlsx";
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
        echo WEB_ROOT . "/download.php?file=" . WEB_ROOT . "/sendFiles/" . $nameFile;

    break;
}
