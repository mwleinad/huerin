<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
header("Content-Type: text/html; charset=utf-8");

$_POST["deep"] = true;
$encargados = $personal->GetIdResponsablesSubordinados($_POST);
$filter['deep'] = true;
$filter['like'] = $_POST['value'];
$filter['tipos'] = $_POST['tipo'];
$filter['encargados'] = $encargados;
$result = $customer->SuggestCustomerFilter($filter, true);
if(!$result)
{
?>
	<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="<?php echo $test["rfc"] ?>" >
		<div class="suggestUserDiv" id="<?php echo $test["contractId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $test["contractId"] ?>No hay clientes</div>
		<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
        <div style="clear:both"></div>
  </div>
<?php 		
}
else
{
    foreach($result as  $var) {
    ?>
        <div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="<?php echo $var["rfc"] ?>" >
            <div class="suggestUserDiv" id="<?php echo $var["clienteId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $var["clienteId"] ?></div>
            <div class="suggestUserDiv" id="<?php echo $var["clienteId"] ?>" style="float:left;width:150px; font-weight:bold; cursor:pointer"><?php echo $var["nameContact"] ?></div>
            <div class="suggestUserDiv" id="<?php echo $var["clienteId"] ?>" style="float:left;width:150px; cursor:pointer"><?php echo $var["rfc"] ?></div>
            <div class="suggestUserDiv" id="<?php echo $var["clienteId"] ?>" style="float:left;width:300px; cursor:pointer"><?php echo $var["name"] ?></div>
            <div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
            <div style="clear:both"></div>
        </div>
    <?php
    }
}//else
?>