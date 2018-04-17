<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "addNotice":
			$roles = $rol->GetRolesGroupByDep();
	        $noticeId = $notice->GetLast();
            $smarty->assign("roles", $roles);
			$smarty->assign("noticeId", $noticeId+1);
			$smarty->assign("userId", $_SESSION["User"]["userId"]);
			$smarty->assign("usuario", $_SESSION["User"]["username"]);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-aviso-popup.tpl');
			
		break;	

	case "addPendiente": 
	
	        $noticeId = $pendiente->GetLast();
			$smarty->assign("noticeId", $noticeId+1);
			$smarty->assign("userId", $_SESSION["User"]["userId"]);
			$smarty->assign("usuario", $_SESSION["User"]["username"]);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			
			//usuarios
			$usuarios = $personal->EnumerateAll();
			$smarty->assign("usuarios", $usuarios);
			
			$smarty->display(DOC_ROOT.'/templates/boxes/add-pendiente-popup.tpl');
			
		break;	

	case "addHistorialPendiente": 
	
			$pendiente->setNoticeId($_POST["noticeId"]);
      $notice = $pendiente->Info();
			$smarty->assign("notice", $notice);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			
			
      $comentarios = $pendiente->Comentarios();
			$smarty->assign("comentarios", $comentarios);
			
			$smarty->display(DOC_ROOT.'/templates/boxes/add-historialPendiente-popup.tpl');
			
		break;	
		
	case "saveAddPendiente":
	
	        $fecha  = date("Y-m-d");
			$pendiente->setFecha($fecha);
			$pendiente->setPrioridad($_POST['prioridad']);
			$pendiente->setDescription($_POST['descripcion']);
			$pendiente->setUsuario($_POST['usuario']);
			
			//$noticeId = $notice->Save();
			
			/*if(!$noticeId)
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{*/
				echo "ok[#]";
				echo $noticeId;				
			//}
			
		break;		
		
	case "saveAddNotice":
            $ruta = DOC_ROOT.'/archivos';
            $notice->setFecha(date("Y-m-d"));
			$notice->setPrioridad($_POST['prioridad']);
			$notice->setDescription($_POST['descripcion']);
			$notice->setUsuario($_POST['usuario']);
			if(!$notice->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
                $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
                $notice->SetPage($_GET["p"]);
                $resArchivo = $notice->Enumerate();
                foreach($resArchivo["items"] as $key =>$value)
                {
                    $card = $value;
                    $card["fecha"] = $util->ChangeDateFormat($value["fecha"]);
                    $card["description"] = nl2br($value["description"]);
                    $resArchivo["items"][$key] = $card;
                }
                echo "[#]";
                $smarty->assign("notices", $resArchivo);
                $smarty->display(DOC_ROOT.'/templates/lists/avisos.tpl');

                $pendiente->SetPage($_GET["p"]);
                $resArchivo = $pendiente->Enumerate();
                foreach($resArchivo["items"] as $key =>$value)
                {
                    $card = $value;
                    $card["fecha"] = $util->ChangeDateFormat($value["fecha"]);
                    $card["description"] = nl2br($value["description"]);
                    $resArchivo["items"][$key] = $card;
                }
                echo "[#]";
                $smarty->assign("pendientes", $resArchivo);
                $smarty->display(DOC_ROOT.'/templates/lists/pendientes.tpl');
			}
		break;
	
	case "deleteNotice":
	
			$notice->setNoticeId($_POST['noticeId']);
			$infN = $notice->Info();
						
			if(!$notice->Delete())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				@unlink(DOC_ROOT.'/archivos/'.$infN['url']);
				
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				
				$resArchivo = $notice->Enumerate();
				foreach($resArchivo["items"] as $key =>$value)
				{
				  $card = $value;
				  $card["fecha"] = $util->ChangeDateFormat($value["fecha"]);
				  $card["description"] = utf8_encode(nl2br($value["description"]));
				  $resArchivo["items"][$key] = $card;
				}				
				$smarty->assign("notices", $resArchivo);
				$smarty->display(DOC_ROOT.'/templates/lists/avisos.tpl');			
			}
			
		break;
		
	case "closePendiente":
	
			$pendiente->setNoticeId($_POST['noticeId']);
			$infN = $pendiente->Info();
						
			if(!$pendiente->Close())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				
				$resArchivo = $pendiente->Enumerate();
				foreach($resArchivo["items"] as $key =>$value)
				{
				  $card = $value;
				  $card["fecha"] = $util->ChangeDateFormat($value["fecha"]);
				  $card["description"] = utf8_encode(nl2br($value["description"]));
				  $resArchivo["items"][$key] = $card;
				}				
				$smarty->assign("pendientes", $resArchivo);
				$smarty->display(DOC_ROOT.'/templates/lists/pendientes.tpl');			
			}
			
		break;		
	case "saveAddComentarioPendiente":
	
			$pendiente->setNoticeId($_POST['noticeId']);
			$infN = $pendiente->Info();
						
			if(!$pendiente->SaveComentario())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				
				$resArchivo = $pendiente->Enumerate();
				foreach($resArchivo["items"] as $key =>$value)
				{
				  $card = $value;
				  $card["fecha"] = $util->ChangeDateFormat($value["fecha"]);
				  $card["description"] = utf8_encode(nl2br($value["description"]));
				  $resArchivo["items"][$key] = $card;
				}				
				$smarty->assign("pendientes", $resArchivo);
				$smarty->display(DOC_ROOT.'/templates/lists/pendientes.tpl');			
			}
			
		break;		
	
}
?>
