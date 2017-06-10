<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "deleteObligacionContract":
			$obligacion->setContractObligacionId($_POST['obligacionId']);
			$info = $obligacion->InfoContract();
			if($obligacion->DeleteContract())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$obligacion->setContractId($info["contractId"]);
				$obligaciones = $obligacion->EnumerateContract();
				$smarty->assign("obligaciones", $obligaciones);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/obligacionContract.tpl');
			}
		break;	
	case "addObligacion": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-obligacion-popup.tpl');
		break;	
	case "saveAddObligacion":
			$obligacion->setObligacionNombre($_POST['obligacionNombre']);
			if(!$obligacion->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resObligacion = $obligacion->Enumerate();
				$smarty->assign("resObligacion", $resObligacion);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/obligacion.tpl');
			}
		break;
	case "deleteObligacion":
			$obligacion->setObligacionId($_POST['obligacionId']);
			if($obligacion->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resObligacion = $obligacion->Enumerate();
				$smarty->assign("resObligacion", $resObligacion);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/obligacion.tpl');
			}
		break;
	case "editObligacion": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$obligacion->setObligacionId($_POST['obligacionId']);
			$myObligacion = $obligacion->Info();
			$smarty->assign("post", $myObligacion);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-obligacion-popup.tpl');
		break;
	case "saveEditObligacion":
			$obligacion->setObligacionId($_POST['obligacionId']);
			$obligacion->setObligacionNombre($_POST['obligacionNombre']);
			if(!$obligacion->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resObligacion = $obligacion->Enumerate();
				$smarty->assign("resObligacion", $resObligacion);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/obligacion.tpl');
			}
		break;
}
?>
