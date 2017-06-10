<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "datos": 
		$userId = $_POST["value"];
		$customer->setCustomerId($userId);
		$result = $customer->Info();
		if(!$result)
		{
			exit();
		}
		echo $result["nameContact"];
		
	break;
  
  case "datosRazon":
  	$userId = $_POST["value"];
		$contract->setContractId($userId);
		$result = $contract->Info();
		if(!$result)
		{
			exit();
		}
		echo $result["name"];
  break;
}

?>