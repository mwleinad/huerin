<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');


switch($_POST["type"])
{
	case "search": 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			
			if(!$_POST["id"])
			{
				echo "Por favor selecciona un cliente";
			}
			$result = $balance->GenerarPorRazonSocial($_POST["id"], $_POST["month"], $_POST["year"]);
			//print_r($result);
			$smarty->assign("data", $result);
			
			$smarty->display(DOC_ROOT.'/templates/lists/balance.tpl');
		break;	
}
?>
