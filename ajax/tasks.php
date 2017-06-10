<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addTask": 
			
//			$tiposDeServicio = $tipoServicio->EnumerateAll();
//			$smarty->assign("tiposDeServicio", $tiposDeServicio);
			$smarty->assign("stepId", $_POST["stepId"]);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-task-popup.tpl');
		
		break;	
		
	case "saveAddTask":
			$task->setNombreTask($_POST['nombreTask']);
			$task->setDiaVencimiento($_POST['diaVencimiento']);			
			$task->setProrroga($_POST['prorroga']);			
			$task->setControl($_POST['control']);			
			$task->setControl2($_POST['control2']);			
			$task->setControl3($_POST['control3']);			
			$task->setStepId($_POST['stepId']);
			if(!$task->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$step->setStepId($_POST['stepId']);
				$info = $step->Info();
				
				$step->setServicioId($info["servicioId"]);
				$steps = $step->Enumerate();
					
				$smarty->assign("steps", $steps);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/steps.tpl');
			}
			
		break;
		
	case "deleteTask":
			
			$task->setTaskId($_POST['taskId']);
			if($task->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$step->setServicioId($_POST["servicioId"]);
				$steps = $step->Enumerate();
					
				$smarty->assign("steps", $steps);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/steps.tpl');
			}
			
		break;
		
	case "editTask":
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$task->setTaskId($_POST['taskId']);
			$myTask = $task->Info();
			$smarty->assign("post", $myTask);
			$smarty->assign("servicioId", $_POST["servicioId"]);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-task-popup.tpl');
		
		break;
		
	case "saveEditTask":
			$task->setNombreTask($_POST['nombreTask']);
			$task->setDiaVencimiento($_POST['diaVencimiento']);			
			$task->setProrroga($_POST['prorroga']);			
			$task->setControl($_POST['control']);			
			$task->setControl2($_POST['control2']);			
			$task->setControl3($_POST['control3']);			
			$task->setTaskId($_POST['taskId']);
			//$myStep = $step->Info();
			
			if(!$task->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$step->setServicioId($_POST["servicioId"]);
				$steps = $step->Enumerate();
					
				$smarty->assign("steps", $steps);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/steps.tpl');
			}
			
		break;
		
}
?>
