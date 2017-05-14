<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
 
$smarty->assign("permisos",$_SESSION['permisos2']);
$smarty->assign("nuevosPermisos",$_SESSION['nuevosPermisos2']);

switch($_POST["type"])
{
	case "producto": 

	$result= $tipoServicio->Suggest($_POST["value"]);
	//$result = $producto->Suggest($_POST["value"]);
	if(count($result) == 0)
	{
	?>
		<div style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#666; padding:3px; width:400px" class="closeSuggestProductoDiv">No hay productos (0)
	  </div>
<?php 		
	}
	foreach($result as $producto)
	{
/* 	<div class="suggestProductoDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:530px;" id="<?php echo $producto["noIdentificacion"] ?>" >
		<div class="suggestProductoDiv" id="<?php echo $producto["noIdentificacion"] ?>" style="float:left;width:150px; cursor:pointer"><?php echo $producto["noIdentificacion"] ?></div>
		<div class="suggestProductoDiv" id="<?php echo $producto["noIdentificacion"] ?>" style="float:left;width:300px; cursor:pointer"><?php echo urldecode($producto["descripcion"]) ?></div>
		<div class="closeSuggestProductoDiv" style="float:left;width:20px; cursor:pointer">X</div>
    <div style="clear:both"></div>
  </div> */
	?>
 
	<div class="suggestProductoDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:400px;" id="<?php echo $producto["noIdentificacion"] ?>" >
		<div class="suggestProductoDiv" id="<?php echo $producto["tipoServicioId"] ?>" style="float:left;width:50px; cursor:pointer"><?php echo $producto["tipoServicioId"] ?></div>
		<div class="suggestProductoDiv" id="<?php echo $producto["tipoServicioId"] ?>" style="float:left;width:300px; cursor:pointer"><?php echo urldecode($producto["nombreServicio"]) ?></div>
		<div class="closeSuggestProductoDiv" style="float:left;width:5px; cursor:pointer">X</div>
    <div style="clear:both"></div>
  </div>
  
	<?php
	}
	break;
	case "impuesto": 
	$result = $impuesto->Suggest($_POST["value"]);
	if(count($result) == 0)
	{
	?>
		<div style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#666; padding:3px; width:400px" class="closeSuggestImpuestoDiv">No hay impuestos
	  </div>
<?php 		
	}
	foreach($result as $impuesto)
	{
	?>

	<div class="suggestImpuestoDiv" style="border:solid; border-width:1px; border-color:#000; background-color:#FFFFE6; color:#333; padding:3px; width:530px;" id="<?php echo $impuesto["impuestoId"] ?>" >
		<div class="suggestImpuestoDiv" id="<?php echo $impuesto["impuestoId"] ?>" style="float:left;width:150px; cursor:pointer"><?php echo $impuesto["impuestoId"] ?></div>
		<div class="suggestImpuestoDiv" id="<?php echo $impuesto["impuestoId"] ?>" style="float:left;width:300px; cursor:pointer"><?php echo urldecode($impuesto["nombre"]) ?></div>
		<div class="closeSuggestImpuestoDiv" style="float:left;width:20px; cursor:pointer">X</div>
    <div style="clear:both"></div>
  </div>
  
	<?php
	}
	break;
}
?>
