<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
    case "doBackup":

           $backup->setCustomNameBd(trim($_POST['name_bd']));
           $sufijo = date("Y-m-d H:i:s");
           $sufijo =  str_replace(" ","-",$sufijo);
           $sufijo =  str_replace(":","_",$sufijo);
           $sufijo =  $sufijo.".sql.gz";
           $nameBackup=trim($_POST['name_bd'])."_".$sufijo;
           $backup->setCustomNameBackup($nameBackup);
           if($backup->CreateBackup()){
               echo "ok[#]";
               $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
               echo "[#]";
               $web_dir_root = WEB_DIR_BACKUP;
               $web_root =  WEB_ROOT;
               echo $web_root."/download.php?file=$web_dir_root$nameBackup";
           }else{
               echo "fail[#]";
               $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
           }

    break;
}
?>