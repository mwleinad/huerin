<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();
switch($_POST["type"])
{
    case 'listTasksStep':
       $task->setStepId($_POST['stepId']);
       $task->setWorkflowId($_POST['idWorkFlow']);
       $data =  $task->checkTasksByStep();

       //comprobar departamento del usuario logueado
        $fltDeps = [8,24];
        //asignar permiso de borrar y actualizar solo si la instancia pertenece ala misma area del usuario activo
        switch($User['tipoPers']){
            case 'Admin':
            case 'Coordinador':
            case 'Socio':
                $isDep = true;
                break;
            default:
                if($User['departamentoId']==$data['workflow']['departamentoId'])
                    $isDep= true;
                elseif(in_array($User['departamentoId'],$fltDeps) && in_array($data['workflow']['departamentoId'],$fltDeps))
                    $isDep= true;
                else
                    $isDep=false;
                break;
       }
       $smarty->assign('isDep',$isDep);
       $smarty->assign('data',$data);
       $smarty->display(DOC_ROOT."/templates/lists/tasks-step.tpl");
    break;
}