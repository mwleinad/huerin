<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
ini_set("memory_limit","3G");
ini_set('max_execution_time', 7200);
ini_set('max_file_uploads', 256);

session_start();
switch($_POST["type"])
{
    case 'listTasksStep':
       $task->setStepId($_POST['stepId']);
       $task->setWorkflowId($_POST['idWorkFlow']);
       $data =  $task->checkTasksByStep();
       $data['isDrill'] = $_POST["drill"];

       $isDep =  $User['allow_any_departament'] || in_array($data['workflow']['departamentoId'], $User['moreDepartament']);
       $smarty->assign('isDep',$isDep);
       $smarty->assign('data',$data);
       $smarty->display(DOC_ROOT."/templates/lists/tasks-step.tpl");
    break;
    case 'viewFacturas':
          $datos = json_decode($_POST['datos']);
          $facturas = $workflow->getDetailCobranzaByContract($datos->contractId,$datos->year,$datos->mes);
          $smarty->assign('results',$facturas);
          $smarty->display(DOC_ROOT."/templates/boxes/detail-facturas-cobranza-popup.tpl");
    break;
    case 'openModalUpdateFilesWorkflow':
        $smarty->assign('post',$_POST);
        $smarty->display(DOC_ROOT."/templates/boxes/update-file-workflow-popup.tpl");
    break;
    case 'updateFilesWorkflow':
        //mover el archivo al servidor primero posteriormente descomprimirlo y actualizar
        $target_path =DIR_SWAP."files_workflow/";
        $compressed  =  new Compressed();
        $compressed->setCustomDir($target_path);
        $contract->setContractId($_POST['contractId']);
        $contrato =  $contract->Info();
        $fileName = str_replace(" ","",$contrato['name']);
        $fileName = strtoupper($fileName)."_".strtotime(date("Y-m-d H:i:s"));//agregar el sufijo del datetime de php para que no exista colision con los usuarios
        $ext = end(explode('.',$_FILES["file"]['name']));
        $fileName = $fileName.".".$ext;
        $compressed->setContractId($_POST['contractId']);
        $compressed->setNameDestiny($fileName);
        //si el zip se movio
        if($compressed->MoveFile($_FILES,true))
        {
            if($compressed->MoveFileToWorkflow()){
                echo "ok[#]";
                $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            }else{
                echo "fail[#]";
                $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            }

        }else{
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        }

    break;
    case 'resetFilesWorflow':
        $workflow->setInstanciaServicioId($_POST['id']);
        echo $workflow->resetFilesFromWorkflow() ? "ok" : "fail";
        echo "[#]";
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
    break;
}
