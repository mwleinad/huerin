<?php
$user->allowAccess(284);  //level 1
$user->allowAccess(285);//level 2

$inventory->setPage($_GET['p']);
$smarty->assign("empleados",$personal->EnumerateAll());
$smarty->assign('mainMnu','reportes');
