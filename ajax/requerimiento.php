<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addRequerimiento": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-requerimiento-popup.tpl');
		break;	
	case "saveAddRequerimiento":
			$requerimiento->setContractId($_POST['contractId']);
			$requerimiento->setTipoRequerimientoId($_POST['tipoRequerimientoId']);
			$requerimiento->setPath($_POST['path']);
			if(!$requerimiento->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resRequerimiento = $requerimiento->Enumerate();
				$smarty->assign("resRequerimiento", $resRequerimiento);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/requerimiento.tpl');
			}
		break;
	case "deleteRequerimiento":
			$requerimiento->setRequerimientoId($_POST['requerimientoId']);
			$info = $requerimiento->Info();
			if($requerimiento->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$requerimiento->setContractId($info["contractId"]);
				$requerimientos = $requerimiento->Enumerate();
				$smarty->assign("requerimientos", $requerimientos);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/requerimiento.tpl');
			}
		break;
	case "editRequerimiento": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$requerimiento->setRequerimientoId($_POST['requerimientoId']);
			$myRequerimiento = $requerimiento->Info();
			$smarty->assign("post", $myRequerimiento);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-requerimiento-popup.tpl');
		break;
	case "saveEditRequerimiento":
			$requerimiento->setRequerimientoId($_POST['requerimientoId']);
			$requerimiento->setContractId($_POST['contractId']);
			$requerimiento->setTipoRequerimientoId($_POST['tipoRequerimientoId']);
			$requerimiento->setPath($_POST['path']);
			if(!$requerimiento->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resRequerimiento = $requerimiento->Enumerate();
				$smarty->assign("resRequerimiento", $resRequerimiento);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/requerimiento.tpl');
			}
		break;
}
?>
