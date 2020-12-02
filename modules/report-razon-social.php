<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(167);//level 2

$personals = $personal->Enumerate();
$smarty->assign("personals", $personals);
$smarty->assign("mainMnu", "reportes");
