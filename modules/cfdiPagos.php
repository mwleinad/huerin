<?php
	
	/* For Session Control - Don't remove this */
	$user->allowAccess();	
	/* End Session Control */
	
	$comprobantes = array();
	$comprobante->SetPage($_GET["p"]);
	
	if($_GET["id"])
	{
		$cfdi->AutorizarPago($_GET["id"]);
	}
	
	$comprobantes = $cfdi->SearchPagos($values);
	
	$comprobantes["pages"] = $result["pages"];

	$smarty->assign('comprobantes',$comprobantes);
		$smarty->assign('mainMnu','cfdi');

?>