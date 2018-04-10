<?php
$user->allowAccess(1);  //level 1
$user->allowAccess(52);//level 2
$departamentos->SetPage($_GET["p"]);
$resDepartamentos = $departamentos->Enumerate();
$smarty->assign("resDepartamentos", $resDepartamentos);

	$smarty->assign('mainMnu','catalogos');
?>