<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addTipoRequerimiento": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-tipoRequerimiento-popup.tpl');
		break;	
	case "saveAddTipoRequerimiento":
			$tipoRequerimiento->setNombre($_POST['nombre']);
			if(!$tipoRequerimiento->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resTipoRequerimiento = $tipoRequerimiento->Enumerate();
				$smarty->assign("resTipoRequerimiento", $resTipoRequerimiento);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoRequerimiento.tpl');
			}
		break;
	case "deleteTipoRequerimiento":
			$tipoRequerimiento->setTipoRequerimientoId($_POST['tipoRequerimientoId']);
			if($tipoRequerimiento->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resTipoRequerimiento = $tipoRequerimiento->Enumerate();
				$smarty->assign("resTipoRequerimiento", $resTipoRequerimiento);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoRequerimiento.tpl');
			}
		break;
	case "editTipoRequerimiento": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$tipoRequerimiento->setTipoRequerimientoId($_POST['tipoRequerimientoId']);
			$myTipoRequerimiento = $tipoRequerimiento->Info();
			$smarty->assign("post", $myTipoRequerimiento);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-tipoRequerimiento-popup.tpl');
		break;
	case "saveEditTipoRequerimiento":
			$tipoRequerimiento->setTipoRequerimientoId($_POST['tipoRequerimientoId']);
			$tipoRequerimiento->setNombre($_POST['nombre']);
			if(!$tipoRequerimiento->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resTipoRequerimiento = $tipoRequerimiento->Enumerate();
				$smarty->assign("resTipoRequerimiento", $resTipoRequerimiento);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoRequerimiento.tpl');
			}
		break;
}
?>
