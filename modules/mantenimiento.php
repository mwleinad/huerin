<?php

    /* Star Session Control Modules*/
    $user->allowAccess(1);  //level 1
    $user->allowAccess(56);//level 2
    /* end Session Control Modules*/
    header('Location:'.WEB_ROOT);
	$info = $empresa->Info();
	$smarty->assign("info", $info);

	$smarty->assign('mainMnu','catalogos');
?>