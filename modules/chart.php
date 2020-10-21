<?php

$user->allowAccess(7);  //level 1
$user->allowAccess(277);//level 2

$dataGraph->chartAltasBajas();
$dataGraph->chartTypePerson();
$dataGraph->chartMonth13();
$dataGraph->chartContracts();

$smarty->assign('charts', $dataGraph->getData());
$smarty->assign('mainMnu', 'reportes');

