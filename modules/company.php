<?php

$user->allowAccess(271);
$prospect->setId($_GET['id']);
$info = $prospect->info();
$company->setProspectId($_GET['id']);
$results = $company->enumerate();
$smarty->assign('results', $results);
$smarty->assign('prospect', $info);
$smarty->assign('mainMnu', 'contratos');
