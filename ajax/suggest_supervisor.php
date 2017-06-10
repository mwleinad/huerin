<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');


$result = $personal->ListSupervisoresAutoComplete($_POST["value"]);
if(!$result)
{
	?>
	<div style="border:solid; border-width:1px; border-color:#000; background-color:#FF6; color:#666; padding:3px; width:400px">No hay Clientes
	</div>
	<?php
}
else
{
	foreach($result as $user)
	{
		?>
		<div class="suggestUserDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:400px;" id="<?php echo $user["personalId"] ?>" >
			<div class="suggestUserDiv" id="<?php echo $user["personalId"] ?>" style="float:left;width:60px; font-weight:bold; cursor:pointer"><?php echo $user["personalId"] ?></div>

			<div class="suggestUserDiv" id="<?php echo $user["personalId"] ?>" style="float:left;width:250px; font-weight:bold; cursor:pointer"><?php echo $user["name"] ?></div>
			<div class="closeSuggestUserDiv" style="float:left;width:20px; cursor:pointer">X</div>
			<div style="clear:both"></div>
		</div>
		<?php
	}
}
?>
