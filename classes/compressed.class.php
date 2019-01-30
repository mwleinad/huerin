<?php


class Compressed extends  Main
{
    private $customDir=DIR_SWAP;
    private $filesToUp=[];
    public function setCustomDir($dir){
        if(!is_dir($dir))
            mkdir($dir,0777);

        $this->customDir=$dir;
    }
    private $nameFolderUnzip;
    public function setNameFolderUnzip($value){
        $this->nameFolderUnzip=$value;
    }
    private $nameDestiny;
    public function setNameDestiny($value){
        $this->nameDestiny=$value;
    }
    function __construct()
    {
        //crear si no esta creada la carpeta
        if(!is_dir(DIR_SWAP))
            mkdir(DIR_SWAP,0777);
    }
    public function MoveFile($FILES=[],$unzip=false){
        $destiny =$this->customDir.$this->nameDestiny;
        if(move_uploaded_file($FILES["file"]['tmp_name'], $destiny)){
            if($unzip){
                $folder = substr($this->nameDestiny,0,-4);
                $this->setNameFolderUnzip($folder);
                $this->UnzipFile();
            }
            return true;
        }
        else
        {
            $this->Util()->setError(0,"complete","Error al subir archivo, intente nevamente");
            $this->Util()->PrintErrors();
            return false;
        }


    }
    public function UnzipFile(){
        $zip =  new ZipArchive();
        $file =  $zip->open($this->customDir.$this->nameDestiny);
        if($file){
            $zip->extractTo($this->customDir.$this->nameFolderUnzip);
            $zip->close();
        }
    }
    public function ConstructArrayFiles(){
        if(!empty($this->filesToUp))
            $this->filesToUp = [];

        $zip =  zip_open($this->customDir.$this->nameDestiny);
        if($zip){
            while ($zip_entry = zip_read($zip)) {
                $zen = zip_entry_name($zip_entry);
                $is_dir = substr($zen, -1) == '/';
                $zen_splitted = explode('/', $zen);
                if (!$is_dir){//colocar en array solo los que son archivos
                    $this->filesToUp[] = $zen;
                }
            }
            zip_close($zip);
        }
        unlink($this->customDir.$this->nameDestiny);
    }
    /*
     * funcion MoveFileToWorkflow, mueve multiples archivos a  la carpeta de servicios que tiene un cliente
     * esta funcion puede llamarse directamente sin usar ComprobarZip, ya que se valida en la funcion que archivos cumplen
     * las reglas definidas al crear el zip. los que no cumplan seran ignoradas.
     * retorna falso o verdadero ademas de setear los mensajes que se debe mostrar al usuario.
     */
    public function MoveFileToWorkflow(){
        global $contract,$monthsComplete;
        $this->ConstructArrayFiles();
        if(!$this->ComprobarZip()){
            $this->Util()->delete_files($this->customDir.$this->nameFolderUnzip."/");
            return false;
        }
        //recorrer cada archivo y comprobar su estructura si no es la correcta se ignora el archivo, de lo contrario se sube al servidor
        $uploaded = 0;
        $ignored = 0;
        foreach($this->filesToUp as $file){
            $idTasks = [];
            $fileExplode =  explode("/",$file);
            $name = explode('_',$this->nameDestiny);
            $contract->setName($name[0]);
            $contrato = $contract->InfoByName();
            $year = $fileExplode[0];
            if(!is_numeric($year))
            {
                $ignored++;
                continue;
            }
            $mes = array_search(ucfirst(strtolower($fileExplode[2])),$monthsComplete);
            if((int)$mes<1||(int)$mes>12){
                $ignored++;
                continue;
            }
            $this->Util()->DB()->setQuery("select tipoServicioId from tipoServicio where upper(trim(nombreServicio))='".strtoupper(trim($fileExplode[1]))."' and status='1' ");
            $tipoServicioId = $this->Util()->DB()->GetSingle();
            if(!$tipoServicioId)
            {
                $ignored++;
                continue;
            }

            $this->Util()->DB()->setQuery("select stepId from step where upper(trim(nombreStep))='".strtoupper(trim($fileExplode[3]))."' and servicioId='".$tipoServicioId."' ");
            $stepId = $this->Util()->DB()->GetSingle();

            if(!$stepId)
            {
                $ignored++;
                continue;
            }
            $numOrderTask =(int)explode('-',$fileExplode[4])[0];
            //obtener id tareas en 1 dimension
            $this->Util()->DB()->setQuery("select taskId from task where stepId='".$stepId."' order by taskId asc");
            $idTasks = $this->Util()->DB()->GetResult();
            $idTasks = $this->Util()->ConvertToLineal($idTasks,'taskId');

            if(empty($idTasks)){
                $ignored++;
                continue;
            }
            if($idTasks[$numOrderTask-1]<=0){
                $ignored++;
                continue;
            }
            $extension = end(explode('.',$fileExplode[4]));
            $this->Util()->DB()->setQuery("select * from task where taskId='".$idTasks[$numOrderTask-1]."' ");
            $rowTask= $this->Util()->DB()->GetRow();
            //dd($rowTask);
            //comprobar extension, si esta vacio se acepta cualquier archivo
            if($rowTask['extensiones']!=''){
                $extExplode = explode(',',str_replace(".","",$rowTask['extensiones']));
                if(!in_array($extension,$extExplode)){
                   $ignored++;
                   continue;
                }
            }
            //encontrar servicio
            $sql = "select servicioId from servicio where tipoServicioId='".$tipoServicioId."' and status in('activo','bajaParcial')  and contractId='".$contrato['contractId']."' ";
            $this->Util()->DB()->setQuery($sql);
            $servicioId= $this->Util()->DB()->GetSingle();
            if(!$servicioId)
                continue;
            //encontrar id de workflow
            $sql = "select instanciaServicioId from instanciaServicio where  servicioId='".$servicioId."' and month(date)='".$mes."' and year(date)='".$year."' and status!='baja'  order by instanciaServicioId asc ";
            $this->Util()->DB()->setQuery($sql);
            $instanciaServicioId= $this->Util()->DB()->GetSingle();
            //si no hay instancia creada se ignora archivo, no es error de estructura
            if(!$instanciaServicioId)
                continue;

            $sql = "select max(version) from taskFile where
					servicioId = '".$instanciaServicioId."' and
					stepId = '".$stepId."' and
					taskId = '".$rowTask['taskId']."' ";
            $this->Util()->DB()->setQuery($sql);
            $version = $this->Util()->DB()->GetSingle()+ 1;

            //validar y crear directorios base solo si no estan creados
           if(!is_dir(DIR_FILES_WORKFLOW))
               mkdir(DIR_FILES_WORKFLOW,0777);

           $dir_files_workflow_cliente_lev1 = DIR_FILES_WORKFLOW."/".strtolower(str_replace(' ','',$contrato['name']));
           if(!is_dir($dir_files_workflow_cliente_lev1))
                mkdir($dir_files_workflow_cliente_lev1,0777);

            $dir_files_workflow_cliente_lev2 = $dir_files_workflow_cliente_lev1."/".$year;
            if(!is_dir($dir_files_workflow_cliente_lev2))
                mkdir($dir_files_workflow_cliente_lev2,0777);

            $dir_files_workflow_cliente_lev3 = $dir_files_workflow_cliente_lev2."/".strtolower(str_replace(' ','',$fileExplode[1]));
            if(!is_dir($dir_files_workflow_cliente_lev3))
                mkdir($dir_files_workflow_cliente_lev3,0777);

            $dir_files_workflow_cliente_lev4 = $dir_files_workflow_cliente_lev3."/".strtolower(str_replace(' ','',$fileExplode[2]));
            if(!is_dir($dir_files_workflow_cliente_lev4))
                mkdir($dir_files_workflow_cliente_lev4,0777);

            $dir_files_workflow_cliente_lev5 = $dir_files_workflow_cliente_lev4."/".strtolower(str_replace(' ','',$fileExplode[3]));
            if(!is_dir($dir_files_workflow_cliente_lev5))
                mkdir($dir_files_workflow_cliente_lev5,0777);

            $file_name_in_workflow = strtolower(str_replace(' ','',$rowTask['nombreTask']))."_".$year."_".$mes."_v".$version.".".$extension;
            //echo $this->customDir.$this->nameFolderUnzip."/".$file.chr(13);
            //echo $dir_files_workflow_cliente_lev5."/".$file_name_in_workflow.chr(13);
            //hasta este punto todas las validaciones ya se han realizado es hora de mover el archivo
            //echo $dir_files_workflow_cliente_lev5."/".$file_name_in_workflow.chr(13);
           // echo strpos($dir_files_workflow_cliente_lev5."/".$file_name_in_workflow,'/tasks').chr(13);
            $ruta_file = substr($dir_files_workflow_cliente_lev5."/".$file_name_in_workflow,strlen(DIR_FILES_WORKFLOW)).chr(13);
            $data_file = [];
            $data_file['origen'] = $this->customDir.$this->nameFolderUnzip."/".$file;
            $data_file['destino'] = $dir_files_workflow_cliente_lev5."/".$file_name_in_workflow;
            $data_file['ruta_file'] = $ruta_file;
            $data_file['instanciaServicioId'] = $instanciaServicioId;
            $data_file['stepId'] = $stepId;
            $data_file['taskId'] = $rowTask['taskId'];
            $data_file['control'] = 1;
            $data_file['version'] = $version;
            $data_file['extension'] =  $extension;
           if($this->SaveFileInFolderWorkflow($data_file)){
                $uploaded++;
           }
        }
        $this->Util()->delete_files($this->customDir.$this->nameFolderUnzip."/");
        unset($this->filesToUp);//limpiar buffer de array creado
        if($ignored>0)
            $this->Util()->setError(0,'error',"$ignored archivos ignorados por no cumplir condiciones establecidas" );
        if($uploaded>0)
            $this->Util()->setError(0,'complete',"$uploaded archivos cargados correctamente" );

        $this->Util()->PrintErrors();
        return true;

    }
    public function  SaveFileInFolderWorkflow($data=[]){
        global $workflow;
        if(!is_array($data)||empty($data))
            return false;
        if(rename($data['origen'],$data['destino']))
        {
            $sql = "INSERT INTO `taskFile` 
					(
                    `servicioId`, 
					`stepId`, 
					`taskId`, 
					`control`, 
					`version`, 
					`ext`, 
					`date`,
					`ruta`
					) 
					VALUES
                    (
                        '" . $data['instanciaServicioId'] . "',
                        '" . $data['stepId'] . "',
                        '" . $data['taskId'] . "',
                        '" . $data["control"] . "',
                        '" . $data['version'] . "',
                        '" . $data['extension'] . "',
                        '" . date("Y-m-d") . "',
                        '" . trim($data['ruta_file']) . "'
                    )";
            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->InsertData();

            $statusWorkflow=$workflow->StatusById($data['instanciaServicioId']);
            $this->Util()->DB()->setQuery("UPDATE instanciaServicio SET class = '".$statusWorkflow["class"]."' 
                WHERE instanciaServicioId = '".$data['instanciaServicioId']."' ");
            $this->Util()->DB()->UpdateData();

            return true;
        }else
            return false;


    }
    /*
     * funcion ComprobarZip(9
     * comprueba la ruta de cada unos los archivos que contiene el zip
     * se valida asta la carpeta de pasos
     * por cada  archivo no se valida la tarea si existe o no, se deja pasar en la funcion MoveFileToWorkflow se ignoran.
     * Esta funcion es opcional llamarlo ya que en MoveFileToWorkflow se realiza la misma validacion.
     * Esta funcion sirve para mostrar los errores en pantalla.
     */
    public function ComprobarZip(){
        global $contract,$monthsComplete;
        if(empty($this->filesToUp))
        {
            $this->Util()->setError(0,"complete","Error en el archivo");
            return false;
        }
        $isValid =  true;
        foreach($this->filesToUp as $file){
            $idTasks = [];
            $fileExplode =  explode("/",$file);
            $name = substr($this->nameDestiny,0,-4);
            $contract->setName($name);
            $contrato = $contract->InfoByName();
            $year = $fileExplode[0];
            if(!is_numeric($year))
            {
                $this->Util()->setError(0,"error","Nombre de carpeta principal no valida:".$year);
                $isValid =  false;
                break;
            }
            $mes = array_search(ucfirst(strtolower($fileExplode[2])),$monthsComplete);
            if((int)$mes<1||(int)$mes>12){
                $this->Util()->setError(0,"error","Nombre del mes no valido, en la siguiente ruta : ".$file);
                $isValid =  false;
                break;
            }
            $this->Util()->DB()->setQuery("select tipoServicioId from tipoServicio where upper(trim(nombreServicio))='".strtoupper(trim($fileExplode[1]))."' and status='1' ");
            $tipoServicioId = $this->Util()->DB()->GetSingle();
            if(!$tipoServicioId)
            {
                $this->Util()->setError(0,"error","Nombre de servicio no valido, en la siguiente ruta : ".$file);
                $isValid =  false;
                break;
            }

            $this->Util()->DB()->setQuery("select stepId from step where upper(trim(nombreStep))='".strtoupper(trim($fileExplode[3]))."' and servicioId='".$tipoServicioId."' ");
            //echo $this->Util()->DB()->getQuery().chr(13);
            $stepId = $this->Util()->DB()->GetSingle();

            if(!$stepId)
            {
                $this->Util()->setError(0,"error","Nombre de paso no valido, en la siguiente ruta : ".$file);
                $isValid =  false;
                break;
            }
            //archivos alojados en carpetas que no tengan prefijos numericos, se ignoran en la validacion.
            $numOrderTask =(int)explode('-',$fileExplode[4])[0];
            //obtener id tareas en 1 dimension
            $this->Util()->DB()->setQuery("select taskId from task where stepId='".$stepId."' order by taskId asc");
            //echo $this->Util()->DB()->getQuery().chr(13);
            $idTasks = $this->Util()->DB()->GetResult();
            $idTasks = $this->Util()->ConvertToLineal($idTasks,'taskId');
            //dd($idTasks);
            //en la comprobacion si no tiene paso no es error, se va ignorar al mover el archivo
            if(empty($idTasks)){
                continue;
            }
            if($idTasks[$numOrderTask-1]<=0){
                continue;
            }
            $extension = end(explode('.',$fileExplode[4]));
            $this->Util()->DB()->setQuery("select * from task where taskId='".$idTasks[$numOrderTask-1]."' ");
            //echo $this->Util()->DB()->getQuery().chr(13);
            $rowTask= $this->Util()->DB()->GetRow();
            //dd($rowTask);
            //comprobar extension, si esta vacio se acepta cualquier archivo
            if($rowTask['extensiones']!=''){
                $extExplode = explode(',',str_replace(".","",$rowTask['extensiones']));
                if(!in_array($extension,$extExplode)){
                    $this->Util()->setError(0,"error","Tipo de archivo no permitido en : ".$file.", revisar archivos permitidos para la tarea ".$rowTask['nombreTask']);
                    $isValid =  false;
                    break;
                }
            }


        }
        $this->Util()->PrintErrors();
        return $isValid;
    }

}