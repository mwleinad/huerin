<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addStep":
		    $data['title'] = 'Agregar paso';
			$data['nameForm'] = 'addStepForm';
			$smarty->assign("data",$data);
			$smarty->assign("servicioId", $_POST["id"]);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/step-popup.tpl');
		break;

	case "saveAddStep":
			$step->setNombreStep($_POST['nombreStep']);
			$step->setDescripcion($_POST['descripcion']);
			$step->setServicioId($_POST['servicioId']);
			$step->setEffectiveDate($_POST['effectiveDate']);
			$step->setFinalEffectiveDate($_POST['finalEffectiveDate']);
			$step->setOrder($_POST['order']);
			if(!$step->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$step->setServicioId($_POST["servicioId"]);
				$steps = $step->Enumerate();

				$smarty->assign("steps", $steps);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/steps.tpl');
			}

		break;

	case "deleteStep":

			$step->setStepId($_POST['stepId']);
			if($step->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$step->setServicioId($_POST["servicioId"]);
				$steps = $step->Enumerate();

				$smarty->assign("steps", $steps);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/steps.tpl');
			}

		break;

	case "editStep":
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$step->setStepId($_POST['stepId']);
			$myStep = $step->Info();

			$data['title'] = 'Editar paso';
			$data['nameForm'] = 'editStepForm';
			$smarty->assign("data",$data);

			$smarty->assign("post", $myStep);
			$smarty->assign("servicioId", $_POST["servicioId"]);
			$smarty->display(DOC_ROOT.'/templates/boxes/step-popup.tpl');

		break;

	case "saveEditStep":

			$step->setStepId($_POST['stepId']);
			$step->setNombreStep($_POST['nombreStep']);
			$step->setDescripcion($_POST['descripcion']);
			$step->setEffectiveDate($_POST['effectiveDate']);
			$step->setFinalEffectiveDate($_POST['finalEffectiveDate']);
			$step->setOrder($_POST['order']);
			$myStep = $step->Info();

			if(!$step->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$step->setServicioId($_POST["servicioId"]);
				$steps = $step->Enumerate();

				$smarty->assign("steps", $steps);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/steps.tpl');
			}

		break;

}
?>
