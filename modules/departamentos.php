<?php
$user->allowAccess('personal');	
$departamentos->SetPage($_GET["p"]);
$resDepartamentos = $departamentos->Enumerate();
$smarty->assign("resDepartamentos", $resDepartamentos);

	$smarty->assign('mainMnu','catalogos');
?>