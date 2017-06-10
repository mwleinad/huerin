<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
header("Content-Type: text/html; charset=utf-8");
//$user->setEmpresaId($_SESSION["empresaId"], 1);
//$user->setRfcId($user->getRfcActive());
$result = $customer->SuggestCustomerContract($_POST["value"]);
if(!$result)
{
?>
	<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="<?php echo $test["rfc"] ?>" >
		<div class="suggestUserDiv" id="<?php echo $test["contractId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $test["contractId"] ?>
    No hay clientes</div>
		<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
    <div style="clear:both"></div>
  </div>
<?php 		
}
else
{
foreach($result as $cliente)
{
		foreach($cliente["contracts"] as $user)
		{	//print_r($user);
?>
	<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="<?php echo $user["rfc"] ?>" >
		<div class="suggestUserDiv" id="<?php echo $user["contractId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $user["contractId"] ?></div>

		<div class="suggestUserDiv" id="<?php echo $user["contractId"] ?>" style="float:left;width:150px; font-weight:bold; cursor:pointer"><?php echo $cliente["nameContact"] ?></div>
		<div class="suggestUserDiv" id="<?php echo $user["contractId"] ?>" style="float:left;width:150px; cursor:pointer"><?php echo $user["rfc"] ?></div>
		<div class="suggestUserDiv" id="<?php echo $user["contractId"] ?>" style="float:left;width:300px; cursor:pointer"><?php echo $user["name"] ?></div>
		<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
    <div style="clear:both"></div>
  </div>
<?php
		}
}
}
?>
