<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addTipoDocumento": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-tipoDocumento-popup.tpl');
		break;	
	case "saveAddTipoDocumento":
			$tipoDocumento->setNombre($_POST['nombre']);
			if(!$tipoDocumento->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resTipoDocumento = $tipoDocumento->Enumerate();
				$smarty->assign("resTipoDocumento", $resTipoDocumento);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoDocumento.tpl');
			}
		break;
	case "deleteTipoDocumento":
			$tipoDocumento->setTipoDocumentoId($_POST['tipoDocumentoId']);
			if($tipoDocumento->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resTipoDocumento = $tipoDocumento->Enumerate();
				$smarty->assign("resTipoDocumento", $resTipoDocumento);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoDocumento.tpl');
			}
		break;
	case "editTipoDocumento": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$tipoDocumento->setTipoDocumentoId($_POST['tipoDocumentoId']);
			$myTipoDocumento = $tipoDocumento->Info();
			$smarty->assign("post", $myTipoDocumento);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-tipoDocumento-popup.tpl');
		break;
	case "saveEditTipoDocumento":
			$tipoDocumento->setTipoDocumentoId($_POST['tipoDocumentoId']);
			$tipoDocumento->setNombre($_POST['nombre']);
			if(!$tipoDocumento->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resTipoDocumento = $tipoDocumento->Enumerate();
				$smarty->assign("resTipoDocumento", $resTipoDocumento);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/tipoDocumento.tpl');
			}
		break;
}
?>
