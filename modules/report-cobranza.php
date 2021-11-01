<?php
    /* Star Session Control Modules*/
    $user->allowAccess(7);  //level 1
    $user->allowAccess(215);//level 2
    /* end Session Control Modules*/
    $personal->isShowAll();
    $personal->setLevelRol(2);
    $gerentes = $personal->Enumerate();
    $gerentes = !is_array($gerentes) ? [] :  $gerentes;
    $personal->setLevelRol(3);
    $subgerentes = $personal->Enumerate();
    $subgerentes = !is_array($subgerentes) ? [] :  $subgerentes;
    $merge_subgerentes = array_merge($gerentes, $subgerentes);
    foreach($merge_subgerentes as $key => $merge) {
        if(!in_array($merge['departamentoId'], [21,22]))
            unset($merge_subgerentes[$key]);
    }
    $smarty->assign("personals", $merge_subgerentes);
    $smarty->assign("year", date('Y'));
	$smarty->assign('mainMnu','reportes');

?>