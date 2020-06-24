<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "addTipoServicio": 
			$departamentos = $personal->ListDepartamentos();			
			$smarty->assign("departamentos", $departamentos);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-tipoServicio-popup.tpl');
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
        	$tipoServicio->setClaveSat($_POST['claveSat']);
						
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
			$departamentos = $personal->ListDepartamentos();			
			$smarty->assign("departamentos", $departamentos);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$tipoServicio->setTipoServicioId($_POST['tipoServicioId']);
			$myTipoServicio = $tipoServicio->Info();
			$smarty->assign("post", $myTipoServicio);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-tipoServicio-popup.tpl');
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
        	$tipoServicio->setClaveSat($_POST['claveSat']);
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
}