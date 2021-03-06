<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "deleteImpuestoContract":
			$impuesto->setContractImpuestoId($_POST['impuestoId']);
			$info = $impuesto->InfoContract();
			if($impuesto->DeleteContract())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$impuesto->setContractId($info["contractId"]);
				$impuestos = $impuesto->EnumerateContract();
				$smarty->assign("impuestos", $impuestos);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/impuestoContract.tpl');
			}
		break;
	case "addImpuesto": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-impuesto-popup.tpl');
		break;	
	case "saveAddImpuesto":
			$impuesto->setImpuestoNombre($_POST['impuestoNombre']);
			if(!$impuesto->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resImpuesto = $impuesto->Enumerate();
				$smarty->assign("resImpuesto", $resImpuesto);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/impuesto.tpl');
			}
		break;
	case "deleteImpuesto":
			$impuesto->setImpuestoId($_POST['impuestoId']);
			if($impuesto->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resImpuesto = $impuesto->Enumerate();
				$smarty->assign("resImpuesto", $resImpuesto);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/impuesto.tpl');
			}
		break;
	case "editImpuesto": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$impuesto->setImpuestoId($_POST['impuestoId']);
			$myImpuesto = $impuesto->Info();
			$smarty->assign("post", $myImpuesto);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-impuesto-popup.tpl');
		break;
	case "saveEditImpuesto":
			$impuesto->setImpuestoId($_POST['impuestoId']);
			$impuesto->setImpuestoNombre($_POST['impuestoNombre']);
			if(!$impuesto->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resImpuesto = $impuesto->Enumerate();
				$smarty->assign("resImpuesto", $resImpuesto);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/impuesto.tpl');
			}
		break;
}
?>
