<?php
$user->allowAccess('bitacora');
if($User['roleId']!=1)
    header('Location: '.WEB_ROOT);

$smarty->assign('mainMnu','reportes');