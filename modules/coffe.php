<?php
$user->allowAccess(213);
$user->allowAccess(214);
/* End Session Control */
//clean session platillos if exist
unset($_SESSION['platillos']);
$coffe = new Coffe();
$coffe->SetPage($_GET["p"]);
$menus = $coffe->Enumerate();
$smarty->assign("menus", $menus);
$smarty->assign('mainMnu','coffe');

?>