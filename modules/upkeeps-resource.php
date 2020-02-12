<?php
    $user->allowAccess(1);  //level 1
    $user->allowAccess(251);//level 2
    $user->allowAccess(263);//level 2
    $inventory->setId($_GET["id"]);
    $result = $inventory->enumerateUpKeeps();
	$smarty->assign("upkeeps", $result);
	$smarty->assign("id", $_GET["id"]);
	$smarty->assign('mainMnu','catalogo');

?>