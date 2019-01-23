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
        if(strlen($this->customName)>0)
            $dbname = $this->customName;
        else
            $dbname = SQL_DATABASE;

        $dirBackup = DOC_DIR_BACKUP;

        $sufijo = date("Y-m-d H:i:s");
        $sufijo =  str_replace(" ","-",$sufijo);
        $sufijo =  str_replace(":","_",$sufijo);
        $sufijo =  $sufijo.".sql.gz";
        $nameBackup ="huerin_$sufijo";
        if(strlen($this->customNameBackup)>0)
            $nameBackup = $this->customNameBackup;

        $dir=$dirBackup.$nameBackup;
        $return_var = NULL;
        system("mysqldump  -h $dbhost -u$dbuser -p$dbpass --databases --add-drop-database $dbname|gzip>$dir",$return_var);
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


}