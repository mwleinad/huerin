<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

$_POST["deep"] = true;
$encargados = $personal->GetIdResponsablesSubordinados($_POST);
$filter['deep'] = $_POST['deep'];
$filter['like'] = $_POST['value'];
$filter['tipos'] = 'activos';
$filter['encargados'] = $encargados;
$result = $customer->EnumerateAllCustomer($filter, 15);
if(!$result)
{
?>
	<div style="border:solid; border-width:1px; border-color:#000; background-color:#FF6; color:#666; padding:3px; width:400px">No hay Clientes</div>
<?php 		
}
else
{
foreach($result as $res)
{
?>
	<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:400px;" id="<?php echo $res["clienteId"] ?>" >
		<div class="suggestUserDiv" id="<?php echo $res["clienteId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $res["clienteId"] ?></div>
		<div class="suggestUserDiv" id="<?php echo $res["clienteId"] ?>" style="float:left;width:250px; font-weight:bold; cursor:pointer"><?php echo $res["nameContact"] ?></div>
		<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
    <div style="clear:both"></div>
  </div>
<?php
}
}
?>
