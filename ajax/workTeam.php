<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');

switch ($_POST["type"]) {
    case "addWorkTeam":
        $data['title'] = 'Agregar equipo de trabajo';
        $data['form'] = 'frm-work-team';
        $data['nameForm'] = 'frmWorkTeam';
        $smarty->assign("departaments", $departamentos->GetListDepartamentos());
        $smarty->assign("responsables", $personal->GetPersonalGroupByDepartament());
        $smarty->assign("data", $data);
        $smarty->display(DOC_ROOT . '/templates/boxes/general-popup.tpl');
    break;
    case "editWorkTeam":
        $data['title'] = 'Editar equipo de trabajo';
        $data['form'] = 'frm-work-team';
        $data['nameForm'] = 'frmWorkTeam';
        $workTeam->setId($_POST['id']);
        $smarty->assign("post", $workTeam->Info());
        $smarty->assign("departaments", $departamentos->GetListDepartamentos());
        $smarty->assign("responsables", $personal->GetPersonalGroupByDepartament());
        $smarty->assign("data", $data);
        $smarty->display(DOC_ROOT . '/templates/boxes/general-popup.tpl');
        break;
    case 'saveWorkTeam':
        $workTeam->setName($_POST['name']);
        echo $workTeam->save() ? 'ok[#]' :  'fail[#]';
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $smarty->assign('work_teams', $workTeam->Enumerate());
        $smarty->display(DOC_ROOT.'/templates/lists/work_team.tpl');
    break;
    case 'updateWorkTeam':
        $workTeam->setId($_POST['id']);
        $workTeam->setName($_POST['name']);
        echo $workTeam->update() ? 'ok[#]' :  'fail[#]';
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $smarty->assign('work_teams', $workTeam->Enumerate());
        $smarty->display(DOC_ROOT.'/templates/lists/work_team.tpl');
        break;
    case 'deleteWorkTeam':
        $workTeam->setId($_POST['id']);
        echo $workTeam->delete() ? 'ok[#]' :  'fail[#]';
        $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $smarty->assign('work_teams', $workTeam->Enumerate());
        $smarty->display(DOC_ROOT.'/templates/lists/work_team.tpl');
        break;
}
