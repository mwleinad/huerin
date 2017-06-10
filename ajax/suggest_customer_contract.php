<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

//$user->setEmpresaId($_SESSION["empresaId"], 1);
//$user->setRfcId($user->getRfcActive());
$result = $customer->SuggestCustomerContract($_POST["value"],$_POST["tipo"]);

if(!$result)
{
	echo '
	<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="1" >
		<div class="suggestUserDiv" id="1" style="float:left;width:60px; font-weight:bold; cursor:pointer">No hay clientes</div>
		<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
		<div style="clear:both"></div>
	</div> ';
}

if(is_array($result))
{
	foreach($result as $cliente)
	{

		foreach($cliente['contracts'] as $user)
		{

				echo '
				<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="'.$user["rfc"].'" >
					<div class="suggestUserDiv" id="'.$user["contractId"].'" style="float:left;width:60px; font-weight:bold; cursor:pointer">'.$user["customerId"].'</div>

					<div class="suggestUserDiv" id="'.$user["contractId"].'" style="float:left;width:150px; font-weight:bold; cursor:pointer">'.$cliente["nameContact"].'</div>
					<div class="suggestUserDiv" id="'.$user["contractId"].'" style="float:left;width:150px; cursor:pointer">'.$user["rfc"].'</div>
					<div class="suggestUserDiv" id="'.$user["contractId"].'" style="float:left;width:300px; cursor:pointer">'.$user["name"].'</div>
					<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
					<div style="clear:both"></div>
				</div>';
		}
	}
}
?>
