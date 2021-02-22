<?php

$user->allowAccess(2);
$user->allowAccess(271);
$smarty->assign('prospect', $_GET['id']);
$company->setProspectId($_GET['id']);
$results = $company->enumerate();
$smarty->assign('results', $results);
$smarty->assign('mainMnu', 'contratos');
