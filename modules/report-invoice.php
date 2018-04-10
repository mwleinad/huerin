<?php

/* Star Session Control Modules*/
$user->allowAccess(7);  //level 1
$user->allowAccess(153);//level 2
/* end Session Control Modules*/

$series = $objectSerie->EnumerateOnePage();
$smarty->assign('series',$series);
$smarty->assign('mainMnu','reportes');
