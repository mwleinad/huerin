<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "addWallmart": 
			
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-wallmart-popup.tpl');
		
		break;	
		
	case "saveAddWallmart":
			
			$wallmart->setName($_POST['name']);			
			$wallmart->setPhone($_POST['phone']);
			$wallmart->setEmail($_POST['email']);
			$wallmart->setUsername($_POST['username']);
			$wallmart->setPasswd($_POST['passwd']);
						
			if($_POST['active'])
				$wallmart->setActive(1);
			else
				$wallmart->setActive(0);
			
			if(!$wallmart->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resWallmarts = $wallmart->Enumerate();
				$wallmarts = $util->EncodeResult($resWallmarts);
				
				$smarty->assign("wallmarts", $wallmarts);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/wallmart.tpl');
			}
			
		break;
		
	case "deleteWallmart":
			
			$wallmart->setWallmartId($_POST['wallmartId']);
			if($wallmart->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resWallmarts = $wallmart->Enumerate();
				$wallmarts = $util->EncodeResult($resWallmarts);
				
				$smarty->assign("wallmarts", $wallmarts);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/wallmart.tpl');
			}
			
		break;
		
	case "editWallmart":
	 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$wallmart->setWallmartId($_POST['wallmartId']);
			$myWallmart = $wallmart->Info();
			
			$info = $util->EncodeRow($myWallmart);
			
			$smarty->assign("post", $info);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-wallmart-popup.tpl');
		
		break;
		
	case "saveEditWallmart":
			
			$wallmart->setWallmartId($_POST['wallmartId']);
			$wallmart->setName($_POST['name']);			
			$wallmart->setPhone($_POST['phone']);
			$wallmart->setEmail($_POST['email']);
			$wallmart->setUsername($_POST['username']);
			$wallmart->setPasswd($_POST['passwd']);
			
			if($_POST['active'])
				$wallmart->setActive(1);
			else
				$wallmart->setActive(0);
			
			if(!$wallmart->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resWallmarts = $wallmart->Enumerate();
				$wallmarts = $util->EncodeResult($resWallmarts);
				
				$smarty->assign("wallmarts", $wallmarts);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/wallmart.tpl');
			}
			
		break;
		
}
?>
