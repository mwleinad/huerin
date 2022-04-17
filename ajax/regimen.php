<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addRegimen": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-regimen-popup.tpl');
		break;	
	case "saveAddRegimen":
			$regimen->setRegimenId($_POST['regimenId']);
			$regimen->setRegimenName($_POST['regimenName']);
			$regimen->setTipoDePersona($_POST['tipoDePersona']);
			if(!$regimen->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resRegimen = $regimen->Enumerate();
				$smarty->assign("resRegimen", $resRegimen);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/regimen.tpl');
			}
		break;
	case "deleteRegimen":
			$regimen->setRegimenId($_POST['regimenId']);
			if($regimen->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resRegimen = $regimen->Enumerate();
				$smarty->assign("resRegimen", $resRegimen);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/regimen.tpl');
			}
		break;
	case "editRegimen": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$regimen->setRegimenId($_POST['regimenId']);
			$regimen->setTipoDePersona($_POST['tipoDePersona']);
			$myRegimen = $regimen->Info();
			$smarty->assign("post", $myRegimen);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-regimen-popup.tpl');
		break;
	case "saveEditRegimen":
			$regimen->setRegimenId($_POST['regimenId']);
			$regimen->setRegimenName($_POST['regimenName']);
			$regimen->setTipoDePersona($_POST['tipoDePersona']);
			
			if(!$regimen->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resRegimen = $regimen->Enumerate();
				$smarty->assign("resRegimen", $resRegimen);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/regimen.tpl');
			}
		break;
}
?>
