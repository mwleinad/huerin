<?php

$departamentos->SetPage($_GET["p"]);
$resDepartamentos = $departamentos->Enumerate();
$smarty->assign("resObligacion", $resDepartamentos);

	$smarty->assign('mainMnu','catalogos');
?>