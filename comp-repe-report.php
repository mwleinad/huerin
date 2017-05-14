<?php

	include_once('init.php');
	include_once('config.php');
	include_once('constants.php');
	include_once(DOC_ROOT.'/libraries.php');
	$db->setQuery("SELECT ack, fecha, contract.name, timbreFiscal, rfc, comp_repe.empresaId, comp_repe.noCertificado, folio, serie, comprobanteId FROM comp_repe 
	LEFT JOIN contract ON contract.contractId = comp_repe.userId");
	$result = $db->GetResult();
	?>
  <table style="font-size:10px" border="1">
  	<tr>
    	<td>Nombre Sistema Anterior</td>
    	<td>Nombre Sistema Nuevo</td>
    	<td>Folio</td>
    	<td>Fecha</td>
    	<td>UUID Anterior (se cancelo)</td>
    	<td>UUID Nuevo</td>
    	<td>ACK</td>
		</tr>
  <?php        
	foreach($result as $res)
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
    	<td><?php echo $timbre["UUID"]." ".$res["ack"]?></td>
		</tr>
	<?php		
	}
	?>
  </table>
