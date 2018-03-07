<?php

$user->allowAccess('report-invoice');
if($User['roleId']!=1)
    header('Location: '.WEB_ROOT);

$series = $objectSerie->EnumerateOnePage();
$smarty->assign('series',$series);
$smarty->assign('mainMnu','reportes');
