<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "datos": 
		$userId = $_POST["value"];
		$contract->setContractId($userId, 1);
		$result = $contract->Info();
		if(!$result)
		{
			exit();
		}
		echo $result["name"];
		
	break;
}

?>
