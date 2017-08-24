<?php
//ESTO ES UNA PRUEBA DEL BRANCHH HDHDH DRILL
// SE AGARA COMMUT EN ESTA RAMA
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST["type"])
{
	case "addArchivo": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->assign("id", $_POST["id"]);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-archivos-popup.tpl');
		break;	
	case "saveAddArchivo":
			$archivo->setContractId($_POST['contractId']);
			$archivo->setTipoArchivoId($_POST['tipoArchivoId']);
			$archivo->setDate($_POST['datef']);
			$archivo->setPath($_POST['path']);
			if(!$archivo->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resArchivo = $archivo->Enumerate();
				$smarty->assign("resArchivo", $resArchivo);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/archivo.tpl');
			}
		break;
	case "deleteArchivo":
	
			if($departamentos->DeleteArchivo($_POST['archivoId']))
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				
				if($User['roleId'] <= 2)
				{
					$smarty->assign("allowDelete", 1);
				}

				$smarty->assign("id", $_POST["depa"]);
				$departamentos->setDepartamentoId($_POST["depa"]);
				$archivos = $departamentos->Archivos();
				$smarty->assign("archivos", $archivos);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/archivosDepartamento.tpl');
			}
		break;
	case "editArchivo": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$id = $_POST['archivoId'];
			$myArchivo = $departamentos->InfoArchivo($id);
			$smarty->assign("post", $myArchivo);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-archivos-popup.tpl');
		break;
	case "editArchivoFecha": 
			$archivo->setArchivoId($_POST['archivoId']);
			$myArchivo = $archivo->Info();
			$smarty->assign("post", $myArchivo);
      $smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-archivo-fecha-popup.tpl');
		break;
	case "saveEditArchivoFecha":
			$archivo->setArchivoId($_POST['archivoId']);
      $archivo->setContractId($_POST['contractId']);
			$archivo->setDate($_POST['datef']);
			if(!$archivo->EditFecha())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
        
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
        $archivos = $archivo->Enumerate();
				$smarty->assign("archivos", $archivos);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/archivo.tpl');
			}
		break;
	case "saveEditArchivo":
			$archivo->setArchivoId($_POST['archivoId']);
			$archivo->setContractId($_POST['contractId']);
			$archivo->setTipoArchivoId($_POST['tipoArchivoId']);
			$archivo->setDate($_POST['date']);
			$archivo->setPath($_POST['path']);
			if(!$archivo->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resArchivo = $archivo->Enumerate();
				$smarty->assign("resArchivo", $resArchivo);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/archivo.tpl');
			}
		break;
}
?>
