<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

$result =  $personal->suggestPersonal($_POST["value"]);
if(!$result)
{
    echo "empty[#]";
?>
  <div style="border:solid; border-width:1px; border-color:#000; background-color:#FF6; color:#666; padding:3px; width:400px">No se encontraron resultados
      <div class="closeSuggestResponsableDiv" style="float:right;width:10%; cursor:pointer">X</div>
      <div style="clear:both"></div>
  </div>
<?php 		
}
else
{
    echo "full[#]";
foreach($result as $user)
{
?>
	<div class="suggestionResponsable_" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:400px;" id="<?php echo $user["name"] ?>" >
		<div class="suggestionResponsable" id="<?php echo $user["name"] ?>" style="float:left;width:90%; font-weight:bold; cursor:pointer"><?php echo $user["name"] ?></div>
		<div class="closeSuggestResponsableDiv" style="float:left;width:10%; cursor:pointer">X</div>
    <div style="clear:both"></div>
  </div>
<?php
}
}
?>
