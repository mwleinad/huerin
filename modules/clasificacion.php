<?php
    $user->allowAccess(1);  //level 1
    $user->allowAccess(288);  //level 1
    if($_GET['p'])
        $clasificacion->setPage($_GET['p']);
    $smarty->assign('results', $clasificacion->EnumerateAll());
	$smarty->assign('mainMnu','catalogos');
?>