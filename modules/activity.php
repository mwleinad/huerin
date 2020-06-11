<?php
    $user->allowAccess(1);  //level 1
    $user->allowAccess(272);//level 2

    if($_GET['p'])
        $activity->setPage($_GET['p']);
    $smarty->assign('data', $activity->enumerate());
	$smarty->assign('mainMnu','catalogos');
?>