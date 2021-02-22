<?php

$user->allowAccess(2);
$user->allowAccess(271);
$smarty->assign('results', $prospect->enumerate());
$smarty->assign('mainMnu', 'contratos');
