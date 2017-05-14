<?php
$user->allowAccess('personal');	
$sociedad->SetPage($_GET["p"]);
$resSociedad = $sociedad->Enumerate();
$smarty->assign("resSociedad", $resSociedad);

	$smarty->assign('mainMnu','catalogos');

?>