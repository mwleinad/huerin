<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
header("Content-Type: text/html; charset=utf-8");

if(!$_POST["responsableCuenta"])
{
	$page = "report-servicio";
}

if($User['tipoPersonal'] == 'Supervisor' && $_POST["responsableCuenta"] == 0){
	
	$personals = $personal->Enumerate();
				
	$result = array();
	foreach($personals as $res){
		$_POST['responsableCuenta'] = $res['personalId'];
		
		$User["userId"] = $_POST["responsableCuenta"];
					
		$personal->setPersonalId($_POST["responsableCuenta"]);
		$myUser = $personal->Info();
		$roleId = $personal->GetRoleId($myUser["tipoPersonal"]);
		
		if($_POST["responsableCuenta"]){
			$User["roleId"] = $roleId;
			$User["departamentoId"] = $myUser["departamentoId"];
		} 
	
		$clientes = $customer->SuggestCustomerCatalog($_POST["value"],$_POST["type"],0,$_POST["tipo"],true);
		$result = array_merge($result, $clientes);
		
	}//foreach

}else{

	if($_POST["responsableCuenta"])
	{
		$User["userId"] = $_POST["responsableCuenta"];
		$personal->setPersonalId($_POST["responsableCuenta"]);
	}
	else
	{
		//cambios 3/13/2016
		$_POST["responsableCuenta"] = $_SESSION["User"]["userId"];
		$_POST["subor"] = "subordinado";
		$User["userId"] = $_POST["responsableCuenta"];
		$personal->setPersonalId($User["userId"]);
	}
	
	$myUser = $personal->Info();
	$roleId = $personal->GetRoleId($myUser["tipoPersonal"]);
	
	if($_POST["responsableCuenta"])
	{
		$User["roleId"] = $roleId;
		$User["departamentoId"] = $myUser["departamentoId"];
	}
	$result = $customer->SuggestCustomerCatalog($_POST["value"],$_POST["type"],0,$_POST["tipo"],true);
	
}//else
	
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
	foreach($result as $cliente){
	
		if(count($cliente["contracts"]) == 0){
?>
	
   	<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="<?php echo $cliente["rfc"] ?>" >
		<div class="suggestUserDiv" id="<?php echo $cliente["customerId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $cliente["customerId"] ?></div>

		<div class="suggestUserDiv" id="<?php echo $cliente["customerId"] ?>" style="float:left;width:150px; font-weight:bold; cursor:pointer"><?php echo $cliente["nameContact"] ?></div>
		<div class="suggestUserDiv" id="<?php echo $cliente["customerId"] ?>" style="float:left;width:150px; cursor:pointer"><?php echo $cliente["rfc"] ?></div>
		<div class="suggestUserDiv" id="<?php echo $cliente["customerId"] ?>" style="float:left;width:300px; cursor:pointer"><?php echo $cliente["name"] ?></div>
		<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
    	<div style="clear:both"></div>
  </div>
    
<?php	
		}//if
		
		foreach($cliente["contracts"] as $user){
		
?>
	<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="<?php echo $user["rfc"] ?>" >
		<div class="suggestUserDiv" id="<?php echo $user["customerId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $user["customerId"] ?></div>

		<div class="suggestUserDiv" id="<?php echo $user["customerId"] ?>" style="float:left;width:150px; font-weight:bold; cursor:pointer"><?php echo $cliente["nameContact"] ?></div>
		<div class="suggestUserDiv" id="<?php echo $user["customerId"] ?>" style="float:left;width:150px; cursor:pointer"><?php echo $user["rfc"] ?></div>
		<div class="suggestUserDiv" id="<?php echo $user["customerId"] ?>" style="float:left;width:300px; cursor:pointer"><?php echo $user["name"] ?></div>
		<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
    	<div style="clear:both"></div>
  	</div>
<?php

		}//foreach
	}//foreach
}//else

?>
