<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(304);//level 2

$smarty->assign("year", date('Y'));
$smarty->assign('mainMnu','reportes');

?>
