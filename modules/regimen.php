<?php
/* Star Session Control Modules*/
$user->allowAccess(1);  //level 1
$user->allowAccess(14);//level 2
/* end Session Control Modules*/

$regimen->SetPage($_GET["p"]);
$resRegimen = $regimen->Enumerate();
$smarty->assign("resRegimen", $resRegimen);

	$smarty->assign('mainMnu','catalogos');

?>