<?php
$user->allowAccess('personal');	
$regimen->SetPage($_GET["p"]);
$resRegimen = $regimen->Enumerate();
$smarty->assign("resRegimen", $resRegimen);

	$smarty->assign('mainMnu','catalogos');

?>