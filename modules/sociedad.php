<?php
$user->allowAccess(1);  //level 1
$user->allowAccess(19);//level 2
$sociedad->SetPage($_GET["p"]);
$resSociedad = $sociedad->Enumerate();
$smarty->assign("resSociedad", $resSociedad);

	$smarty->assign('mainMnu','catalogos');

?>