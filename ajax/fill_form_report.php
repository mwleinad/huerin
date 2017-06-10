<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "datos":
		$userId = $_POST["value"];
		$customer->setCustomerId($userId, 1);
		$result = $customer->Info();
		if(!$result)
		{
			exit();
		}
		echo $result["nameContact"];

	break;
	case "datosSupervisor":
		$userId = $_POST["value"];
		$personal->setPersonalId($userId);
		$result = $personal->Info();

		if(!$result)
		{
			exit();
		}
		echo $result["name"];

	break;
}

?>
