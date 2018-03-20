<?php

if($User['roleId']!=1)
header('Location: '.WEB_ROOT);

$roles = $rol->Enumerate();
$smarty->assign('roles',$roles);
$smarty->assign('mainMnu','catalogos');