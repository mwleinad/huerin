<?php
    $user->allowAccess(1);  //level 1
    $user->allowAccess(251);//level 2
    $user->allowAccess(258);//level 2

    $inventory->setId($_GET["id"]);
    $info = $inventory->infoResource();
		
	$smarty->assign("responsables", $info["responsables"]);
	$smarty->assign("id", $_GET["id"]);
	$smarty->assign('mainMnu','catalogo');

?>