<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST["type"])
{
	case "addArchivo":
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->assign("post", $_POST);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-archivos-popup.tpl');
		break;
	case "saveArchivoDepartamento":
        $departamentos->setNameArchivo($_POST['name']);
        $departamentos->setDepartamentoId($_POST['depId']);
        $departamentos->isUploadFile($_FILES["path"]);
        if(!$departamentos->SubirArchivo())
        {
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }
        else
        {
            $departamentos->setDepartamentoId($_POST['depId']);
            $archivos = $departamentos->Archivos();
			$smarty->assign("isSameDepartament", $departamentos->isSameDepartament());
            echo "ok[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            echo "[#]";
            $smarty->assign("archivos", $archivos);
            $smarty->assign("id", $_POST["depId"]);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT.'/templates/lists/archivosDepartamento.tpl');
        }
		break;
	case "deleteArchivo":
			if($departamentos->DeleteArchivo($_POST['archivoId']))
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$departamentos->setDepartamentoId($_POST["depa"]);
				$archivos = $departamentos->Archivos();
				$smarty->assign("isSameDepartament", $departamentos->isSameDepartament());
                $smarty->assign("id", $_POST["depa"]);
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
	case "updateArchivoDepartamento":
			$departamentos->setDepArchivoId($_POST['departamentosArchivosId']);
			$departamentos->setNameArchivo($_POST['name']);
            $departamentos->setDepartamentoId($_POST['depId']);
			if(!$departamentos->ActualizarArchivo())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
                $departamentos->setDepartamentoId($_POST['depId']);
                $archivos = $departamentos->Archivos();
				$smarty->assign("isSameDepartament", $departamentos->isSameDepartament());
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
                $smarty->assign("archivos", $archivos);
                $smarty->assign("id", $_POST["depId"]);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/archivosDepartamento.tpl');
			}
		break;
}
?>
