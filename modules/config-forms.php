<?php
$user->allowAccess(217);
$user->allowAccess(218);
/* End Session Control */
//clean session platillos if exist
$bd_user= SQL_USER;
$bd_host = SQL_HOST;
$bd_pass = SQL_PASSWORD;

$file = DOC_ROOT.'/sendFiles/list_bd.txt';
if(strpos(strtolower(php_uname()),'windows')!==false)
 $command = 'mysql -h'.$bd_host.' -u'.$bd_user.' -p'.$bd_pass.' -e "show databases where `Database` not in(\'mysql\',\'phpmyadmin\',\'information_schema\',\'performance_schema\',\'PLEASE_READ_ME_ZYX\') " >'.$file;
else
 $command =  "mysql -h$bd_host -u$bd_user -p$bd_pass -e 'show databases where `Database` not in(\"mysql\",\"phpmyadmin\",\"information_schema\",\"performance_schema\",\"PLEASE_READ_ME_ZYX\")'>".$file;


exec($command,$de);
$listDatabases = [];
if(file_exists($file)){
        $listDatabases = file($file,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        unset($listDatabases[0]);
}
$smarty->assign('listDatabases',$listDatabases);
$smarty->assign('mainMnu','configuracion');

?>