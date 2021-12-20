<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case 1:
		$data['title'] = $_POST['id'] ? "Editar tipo de clasificación" : 'Agregar tipo de clasificación';
		$data["form"] = "frm-clasificacion";

		$clasificacion->setId($_POST['id']);
		$row = $clasificacion->info();
		$smarty->assign("post", $row);
		$smarty->assign("data", $data);
		$json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
		echo json_encode($json);
		break;	
	case 2:
			$clasificacion->setId($_POST['id']);
			$clasificacion->setNombre($_POST['nombre']);
			if(!$clasificacion->saveOrUpdate())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$results = $clasificacion->EnumerateAll();
				$smarty->assign("results", $results);
				$smarty->display(DOC_ROOT.'/templates/lists/clasificacion.tpl');
			}
		break;
	case 3:
			$clasificacion->setId($_POST['id']);
			if($clasificacion->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$results = $clasificacion->EnumerateAll();
				$smarty->assign("results", $results);
				$smarty->display(DOC_ROOT.'/templates/lists/clasificacion.tpl');
			}
		break;
}
?>
