<?php

	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	
	$db->setQuery("SELECT contractId, rfc FROM contract");
	$result = $db->GetResult();
	
	foreach($result as $key => $value)
	{
		$realRfc = trim($value["rfc"]);
		$realRfc = str_replace("-","",$realRfc);
		$realRfc = str_replace(" ","",$realRfc);
		$realRfc = str_replace("NOLOTENGO","XAXX010101000",$realRfc);
		
		if(strlen($realRfc) < 12)
		{
			$realRfc = str_pad($realRfc, 12, "0");
		}

		if(strlen($realRfc) > 13)
		{
			$realRfc = substr($realRfc, 0, 13);
		}
		echo $realRfc;
		$db->setQuery("UPDATE contract SET rfc = '".$realRfc."' WHERE contractId = ".$value["contractId"]." LIMIT 1");
		$db->UpdateData();
		echo "\n";
	}

?>