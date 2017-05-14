<?php

	include_once('init.php');
	include_once('config.php');
	include_once('constants.php');
	include_once(DOC_ROOT.'/libraries.php');
	$db->setQuery("SELECT fecha, contract.name, timbreFiscal, rfc, comp_repe.empresaId, comp_repe.noCertificado, folio, serie, comprobanteId FROM comp_repe 
	LEFT JOIN contract ON contract.contractId = comp_repe.userId
	WHERE comp_repe.ack = ''");
	$result = $db->GetResult();
	?>
  <table style="font-size:10px">
  	<tr>
    	<td>Nombre Sistema Anterior</td>
    	<td>Nombre Sistema Nuevo</td>
    	<td>Folio</td>
    	<td>Fecha</td>
    	<td>UUID Anterior (se cancelara)</td>
    	<td>UUID Nuevo</td>
		</tr>
  <?php        
/*	foreach($result as $res)
	{
		$timbre = unserialize(urldecode($res["timbreFiscal"]));
		
		$db->setQuery("SELECT contract.name, timbreFiscal, rfc, comprobante.empresaId, comprobante.noCertificado, folio, serie, comprobanteId FROM comprobante 
	LEFT JOIN contract ON contract.contractId = comprobante.userId WHERE serie = '".$res["serie"]."' AND folio = '".$res["folio"]."'");
		$nuevo = $db->GetRow();
		$timbreNuevo = unserialize(urldecode($nuevo["timbreFiscal"]));

		?>
  	<tr>
    	<td><?php echo $res["name"]?></td>
    	<td><?php echo $nuevo["name"]?></td>
    	<td><?php echo $res["serie"].$res["folio"]?></td>
    	<td><?php echo $res["fecha"]?></td>
    	<td><?php echo $timbre["UUID"]?></td>
    	<td><?php echo $timbreNuevo["UUID"]?></td>
		</tr>
	<?php		
	}*/
	?>
  </table>
  <br /><br />
  Timbres a Cancelar
  <br /><br />
  <?php 	
	foreach($result as $key => $res)
	{
		$timbre = unserialize(urldecode($res["timbreFiscal"]));
		echo $timbre["UUID"];
		echo "<br>";
		
		if($res["empresaId"] == 15)
		{
			$rfc = "BHU120320CQ1";
			$pfx = DOC_ROOT."/empresas/15/certificados/1/00001000000200751201.cer.pfx";
			$pfxPassword = "BHU120320";
		}
		else
		{
			$rfc = "BABJ701019LD7";
			$pfx = DOC_ROOT."/empresas/20/certificados/29/00001000000201027108.cer.pfx";
			$pfxPassword = "BABJ701019";
		}
		$response = $pac->CancelaCfdi(USER_PAC, PW_PAC, $rfc, $timbre["UUID"], $pfx, $pfxPassword);
		if(is_array($response["cancelaCFDiReturn"]))
		{
			$db->setQuery("UPDATE comp_repe SET ack = '".$response["cancelaCFDiReturn"]["ack"]."'
			WHERE comp_repe.comprobanteId = '".$res["comprobanteId"]."'");
			$nuevo = $db->UpdateData();
		}
		else
		{
			echo "falla";
		}
	}
?>