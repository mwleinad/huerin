<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case "addPersonal": 
			$departamentos = $personal->ListDepartamentos();			
			$smarty->assign("departamentos", $departamentos);

			$miPersonal = $personal->ListAll();			
			$smarty->assign("personal", $miPersonal);
			
			$contadores = $personal->ListContadores();			
			$smarty->assign("contadores", $contadores);

			$supervisores = $personal->ListSupervisores();			
			$smarty->assign("supervisores", $supervisores);

			$gerentes = $personal->ListGerentes();			
			$smarty->assign("gerentes", $gerentes);

			$socios = $personal->ListSocios();			
			$smarty->assign("socios", $socios);

            $roles = $rol->GetListRoles();
        	$smarty->assign("roles", $roles);

       		$expedientes = $expediente->Enumerate();
        	foreach($expedientes as $key => $value){
        		if(!strpos(strtolower($value['name']),'fonacot'))
                    $expedientes[$key]['find']=true;
			}



            $smarty->assign("expedientes", $expedientes);
			
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-personal-popup.tpl');

		
		break;	
		
	case "saveAddPersonal":
			$personal->setName($_POST['name']);			
			$personal->setPhone($_POST['phone']);
			$personal->setEmail($_POST['email']);
			$personal->setUsername($_POST['username']);
			$personal->setPasswd($_POST['passwd']);

			$personal->setAspel($_POST['aspel']);
			$personal->setExt($_POST['ext']);
			$personal->setCelphone($_POST['celphone']);
			$personal->setSkype($_POST['skype']);
			$personal->setPuesto($_POST['puesto']);
			$personal->setHorario($_POST['horario']);
			$personal->setSueldo($_POST['sueldo']);
			$personal->setGrupo($_POST['grupo']);
			$personal->setComputadora($_POST['computadora']);

			$personal->setTipoPersonal($_POST['tipoPersonal']);
			$rol->setTitulo($_POST['tipoPersonal']);
			$roleId=$rol->GetIdByName();
			$personal->setRole($roleId);

			$personal->setDepartamentoId($_POST['departamentoId']);
			$fechaIngreso = ($_POST['fechaIngreso'] == '') ? '' : date('Y-m-d',strtotime($_POST['fechaIngreso']));			
			$personal->setFechaIngreso($fechaIngreso);
						
			if($_POST['active'])
				$personal->setActive(1);
			else
				$personal->setActive(0);
			
			if(!$personal->Save())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resPersonals = $personal->Enumerate();
				//$personals = $util->EncodeResult($resPersonals);
				
				$smarty->assign("personals", $resPersonals);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/personal.tpl');
			}
			
		break;
		
	case "deletePersonal":
			
			$personal->setPersonalId($_POST['personalId']);
			if($personal->Delete())
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');
				echo "[#]";
				$resPersonals = $personal->Enumerate();
				//$personals = $util->EncodeResult($resPersonals);
				
				$smarty->assign("personals", $resPersonals);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/personal.tpl');
			}
			
		break;
		
	case "editPersonal":

			$miPersonal = $personal->ListAll();			
			$smarty->assign("personal", $miPersonal);
	 
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$personal->setPersonalId($_POST['personalId']);
			$myPersonal = $personal->Info();
			
			$info = $myPersonal;
			
			if($info['fechaIngreso'])
				$info['fechaIngreso'] = date('d-m-Y',strtotime($info['fechaIngreso']));
			
			$smarty->assign("post", $info);
			
			$departamentos = $personal->ListDepartamentos();			
			$smarty->assign("departamentos", $departamentos);
			
			$contadores = $personal->ListContadores();			
			$smarty->assign("contadores", $contadores);

			$supervisores = $personal->ListSupervisores();			
			$smarty->assign("supervisores", $supervisores);

			$gerentes = $personal->ListGerentes();			
			$smarty->assign("gerentes", $gerentes);

			$socios = $personal->ListSocios();			
			$smarty->assign("socios", $socios);
            //comprobar si se encuentra configurado el empleado con sus expedientes
        	$db->setQuery('select * from personalExpedientes where personalId="'.$myPersonal['personalId'].'" ');
       		$resExp = $db->GetResult();

			$roles = $rol->GetListRoles();
			$smarty->assign("roles", $roles);

			$expedientes = $expediente->Enumerate();
			if(empty($resExp)){
				//si no se encuentra configurado se muestran chekeados todos.
                foreach($expedientes as $key => $value){
                    if(!strpos(strtolower($value['name']),'fonacot'))
                        $expedientes[$key]['find']=true;
                }
                $smarty->assign("msgExp", 'Es necesario guardar cambios, para que los expedientes queden registrados.');
			}else{
                foreach($expedientes as $key => $value){
                    $db->setQuery('select * from personalExpedientes where personalId="'.$myPersonal['personalId'].'" AND expedienteId="'.$value['expedienteId'].'"');
                    $find = $db->GetRow();
                    if(!empty($find)){
                        $expedientes[$key]['find']=true;
                    }
                    else
                        $expedientes[$key]['find']=false;
                }
			}

			$smarty->assign("expedientes", $expedientes);
			
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-personal-popup.tpl');
		
		break;
		
	case "saveEditPersonal":

			$personal->setPersonalId($_POST['personalId']);
			$personal->setName($_POST['name']);			
			$personal->setPhone($_POST['phone']);
			$personal->setEmail($_POST['email']);
			$personal->setUsername($_POST['username']);
			$personal->setPasswd($_POST['passwd']);

			$personal->setAspel($_POST['aspel']);
			$personal->setExt($_POST['ext']);
			$personal->setCelphone($_POST['celphone']);
			$personal->setSkype($_POST['skype']);
			$personal->setPuesto($_POST['puesto']);
			$personal->setHorario($_POST['horario']);
			$personal->setSueldo($_POST['sueldo']);
			$personal->setGrupo($_POST['grupo']);
			$personal->setComputadora($_POST['computadora']);

			$personal->setTipoPersonal($_POST['tipoPersonal']);
			$rol->setTitulo($_POST['tipoPersonal']);
        	$roleId=$rol->GetIdByName();
        	$personal->setRole($roleId);

			$personal->setDepartamentoId($_POST['departamentoId']);
			$personal->setJefeInmediato($_POST['jefeInmediato']);
			$fechaIngreso = ($_POST['fechaIngreso'] == '') ? '' : date('Y-m-d',strtotime($_POST['fechaIngreso']));			
			$personal->setFechaIngreso($fechaIngreso);
			
			if($_POST['active'])
				$personal->setActive(1);
			else
				$personal->setActive(0);
			
			if(!$personal->Edit())
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				echo "ok[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				$resPersonals = $personal->Enumerate();
				
				
				$smarty->assign("personals", $resPersonals);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/personal.tpl');
			}
			
		break;
    case "showFile":

        $personal->setPersonalId($_POST['personalId']);
        $myPersonal = $personal->Info();
		$expedientes = $personal->GetExpedientes();

        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->assign("info", $myPersonal);
        $smarty->assign("expedientes", $expedientes);
        $smarty->display(DOC_ROOT.'/templates/boxes/show-file-personal-popup.tpl');

        break;
		
}
?>
