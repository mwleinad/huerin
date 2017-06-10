<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addTipoArchivo": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-tipoArchivo-popup.tpl');
		break;	
	case "saveAddTipoArchivo":
			$tipoArchivo->setDescripcion($_POST['descripcion']);
			if(!$tipoArchivo->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resTipoArchivo = $tipoArchivo->Enumerate();
				$smarty->assign("resTipoArchivo", $resTipoArchivo);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoArchivo.tpl');
			}
		break;
	case "deleteTipoArchivo":
			$tipoArchivo->setTipoArchivoId($_POST['tipoArchivoId']);
			if($tipoArchivo->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resTipoArchivo = $tipoArchivo->Enumerate();
				$smarty->assign("resTipoArchivo", $resTipoArchivo);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoArchivo.tpl');
			}
		break;
	case "editTipoArchivo": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$tipoArchivo->setTipoArchivoId($_POST['tipoArchivoId']);
			$myTipoArchivo = $tipoArchivo->Info();
			$smarty->assign("post", $myTipoArchivo);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-tipoArchivo-popup.tpl');
		break;
	case "saveEditTipoArchivo":
			$tipoArchivo->setTipoArchivoId($_POST['tipoArchivoId']);
			$tipoArchivo->setDescripcion($_POST['descripcion']);
			if(!$tipoArchivo->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resTipoArchivo = $tipoArchivo->Enumerate();
				$smarty->assign("resTipoArchivo", $resTipoArchivo);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoArchivo.tpl');
			}
		break;
}
?>
