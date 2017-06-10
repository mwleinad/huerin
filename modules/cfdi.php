<?php
	
	/* For Session Control - Don't remove this */
	$user->allowAccess();	
	/* End Session Control */
	
	$comprobantes = array();
	$comprobante->SetPage($_GET["p"]);

	$comprobantes = $cfdi->Search($values);
	
	$comprobantes["pages"] = $result["pages"];

	$smarty->assign('comprobantes',$comprobantes);
		$smarty->assign('mainMnu','cfdi');

?>