<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

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
}
