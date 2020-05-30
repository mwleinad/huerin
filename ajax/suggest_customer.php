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
$result = $customer->SuggestCustomerFilter($filter, true);
if(!$result)
{
?>
	<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="" >
		<div class="suggestUserDiv" id="" style="float:left;width:60px; font-weight:bold; cursor:pointer">No hay clientes</div>
		<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
    <div style="clear:both"></div>
  </div>
<?php 		
}
else
{
    foreach($result as $contrato)
    {
    ?>
        <div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:680px;" id="<?php echo $contrato["rfc"] ?>" >
            <div class="suggestUserDiv" id="<?php echo $contrato["contractId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $contrato["contractId"] ?></div>

            <div class="suggestUserDiv" id="<?php echo $contrato["contractId"] ?>" style="float:left;width:150px; font-weight:bold; cursor:pointer"><?php echo $contrato["nameContact"] ?></div>
            <div class="suggestUserDiv" id="<?php echo $contrato["contractId"] ?>" style="float:left;width:150px; cursor:pointer"><?php echo $contrato["rfc"] ?></div>
            <div class="suggestUserDiv" id="<?php echo $contrato["contractId"] ?>" style="float:left;width:300px; cursor:pointer"><?php echo $contrato["name"] ?></div>
            <div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
        <div style="clear:both"></div>
      </div>
    <?php
    }
}
?>
