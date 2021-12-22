<?php

class Backup extends main
{
    private $customName;
    public function setCustomNameBd($value){
        $this->customName =  $value;
    }
    private $customNameBackup;
    public function setCustomNameBackup($value){
        $this->customNameBackup =  $value;
    }
    public function CreateBackup(){
        $dbhost = SQL_HOST;
        $dbuser = SQL_USER;
        $dbpass = SQL_PASSWORD;
        $dbname = strlen($this->customName)>0
                    ? $this->customName
                    : SQL_DATABASE;

        $baseDirBackup = DOC_ROOT.DIR_BACKUP;
        if(!is_dir($baseDirBackup)) {
            mkdir($baseDirBackup, 0775);
        }
        $sufijo = date("Y-m-d H:i:s");
        $sufijo =  str_replace(" ","_",$sufijo);
        $sufijo =  str_replace(":","_",$sufijo);
        $sufijo =  $sufijo.".sql.gz";
        $nameBackup ="bk_bh_$sufijo";
        if(strlen($this->customNameBackup)>0)
            $nameBackup = $this->customNameBackup;

        $dir=$baseDirBackup."/".$nameBackup;
        $return_var = NULL;
        system("mysqldump  -h $dbhost -u$dbuser -p$dbpass --databases --add-drop-database --routines $dbname|gzip>$dir",$return_var);
        //system("aws s3 cp $dir s3://backup-huerin/$sufijo");
        if($return_var===0){
            $this->Util()->setError(0,'complete','Respaldo realizado completamente');
            $this->Util()->PrintErrors();
            return true;
        }else{
            $this->Util()->setError(0,'error','Hubo un error al crear respaldo intente nuevamente');
            $this->Util()->PrintErrors();
            return false;
        }

    }
    public function SendBackupToEmail(){
        $send =  new SendMail();
        $mails = ['isc061990@gmail.com'=>"Hector", "isc061990@outlook.com"=>'Dev'];
        $send->Prepare("Confirmacion de resplado de bd","",EMAILCOORDINADOR,"Coordinador",DOC_DIR_BACKUP.$this->customNameBackup,$this->customNameBackup,"","","admin@braunhuerin.com.mx","Respaldo BD BH");
    }


}
