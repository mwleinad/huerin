<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addDepartamentos": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-departamentos-popup.tpl');
		break;	
	case "saveAddDepartamentos":
			$departamentos->setDepartamento($_POST['departamento']);
			if(!$departamentos->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resDepartamentos = $departamentos->Enumerate();
				$smarty->assign("resDepartamentos", $resDepartamentos);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/departamentos.tpl');
			}
		break;
	case "deleteDepartamentos":
			$departamentos->setDepartamentoId($_POST['departamentoId']);
			if($departamentos->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resDepartamentos = $departamentos->Enumerate();
				$smarty->assign("resDepartamentos", $resDepartamentos);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/departamentos.tpl');
			}
		break;
	case "editDepartamentos": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$departamentos->setDepartamentoId($_POST['departamentoId']);
			$myDepartamentos = $departamentos->Info();
			$smarty->assign("post", $myDepartamentos);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-departamentos-popup.tpl');
		break;
	case "saveEditDepartamentos":
			$departamentos->setDepartamentoId($_POST['departamentoId']);
			$departamentos->setDepartamento($_POST['departamento']);
			if(!$departamentos->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resDepartamentos = $departamentos->Enumerate();
				$smarty->assign("resDepartamentos", $resDepartamentos);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/departamentos.tpl');
			}
		break;
}
?>
