<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST["type"])
{
	case "addArchivo": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-archivo-popup.tpl');
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
	
			$archivo->setArchivoId($_POST['archivoId']);
			$info = $archivo->Info();
			
			if($archivo->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				
				//Checamos los permisos para eliminar DOCs y Archivos
	
				$permisoDel = array('Gerente','Socio','Asistente');
				
				$allowDelete = 0;
				if(in_array($User['tipoPersonal'], $permisoDel))
					$allowDelete = 1;
			
				$smarty->assign('allowDelete',$allowDelete);
				$archivo->setContractId($info["contractId"]);
				$archivos = $archivo->Enumerate();
				$smarty->assign("archivos", $archivos);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/archivo.tpl');
			}
		break;
	case "editArchivo": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$archivo->setArchivoId($_POST['archivoId']);
			$myArchivo = $archivo->Info();
			$smarty->assign("post", $myArchivo);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-archivo-popup.tpl');
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
