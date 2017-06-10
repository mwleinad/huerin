<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

		$util->DB()->setQuery("SELECT name FROM personal
		WHERE personalId = '".$_GET["usuario"]."'");
		$miUsuario = $util->DB()->GetSingle();
	
		$util->DB()->setQuery("SELECT *, contract.name AS name, customer.nameContact AS customerName FROM contract
		JOIN customer ON customer.customerId = contract.customerId
		WHERE contract.customerId = '".$_GET["cliente"]."'");
		$contracts = $util->DB()->GetResult();

   	$personal->setPersonalId($_GET["usuario"]);
   	$subordinados = $personal->Subordinados();
		//print_r($subordinados);
		array_push($subordinados, array("personalId" => $_GET["usuario"]));
		
		$subs = array();
		$losSubordinados = array();
		foreach($subordinados as $key => $subordinado)
		{
			$subs[] = $subordinado["personalId"];
			$util->DB()->setQuery("SELECT name FROM personal
				WHERE personal.personalId = '".$subordinado["personalId"]."'");
			$usuario = $util->DB()->GetRow();
			$card["usuario"] = ($usuario["name"]) ? $usuario["name"] : "No existe en base de datos";
			$card["id"] = $subordinado["personalId"];
			$losSubordinados[$subordinado["personalId"]] = $card;
		}
		
		foreach($contracts as $keyContract => $myContract)
		{
			$usuario = array();
			$conPermiso = $contract->UsuariosConPermiso($myContract['permisos'], $myContract["responsableCuenta"]);
			foreach($conPermiso as $key => $value)
			{
				$util->DB()->setQuery("SELECT name FROM personal
					WHERE personal.personalId = '".$value."'");
				$usuario = $util->DB()->GetRow();
				$card["usuario"] = ($usuario["name"]) ? $usuario["name"] : "No existe en base de datos";
				$card["id"] = $value;
				$usuarios[$myContract["contractId"]]["conPermiso"][] = $card;
				$usuarios[$myContract["contractId"]]["name"] = $myContract["name"];
				$usuarios[$myContract["contractId"]]["customerName"] = $myContract["customerName"];
			}
		}
		echo "<pre>";
		
?>

Subordinados de <?php echo $miUsuario?> (Personas que deben poder ver la razon social)
<table border="1">
<tr>
	<td>Id  </td>
	<td>Nombre  </td>
</tr>
<?php
foreach($losSubordinados as $key => $subordinado)
{
?>
	<tr>
  	<td>    <?php echo $subordinado["id"] ?>     </td>
  	<td>    <?php echo $subordinado["usuario"] ?>     </td>
  </tr>
<?php		
}
?>
</table>

Razones Sociales a Revisar
<table border="1">
<tr>
	<td>Cliente  </td>
	<td>Razon Social  </td>
	<td>Quien Tiene Permiso  </td>
	<td>Existe para <?php echo $miUsuario?> o sus subordinados?  </td>
</tr>
<?php
foreach($usuarios as $key => $contract)
{
	foreach($contract["conPermiso"] as $keyContract => $value)
	{
?>
	<tr>
  	<td>    <?php echo $contract["customerName"] ?>     </td>
  	<td>    <?php echo $contract["name"] ?>     </td>
  	<td>    <?php echo $value["usuario"] ?>     </td>
  	<td>    <?php 

						if(in_array($value["id"], $subs))
						{
							echo "Existe y debe poder verlo";
						}
						else
						{
							echo "No existe por lo que no debe poder verlo";
						}
						?>     </td>
  </tr>
<?php		
	}
}
?>
</table>

