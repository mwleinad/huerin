<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');

switch ($_POST['type']) {
    case "openAddCompany":
        $data['title'] = "Agregar empresa";
        $data["form"] = "frm-company";
        $smarty->assign("data", $data);
        $prospect->setId($_POST['prospect_id']);
        $smarty->assign("prospect", $prospect->info());
        $smarty->assign("regimenes", $catalogue->EnumerateCatalogue('tipoRegimen'));
        $smarty->assign("actividades", $catalogue->EnumerateCatalogue('actividad_comercial'));
        $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        $json['services'] = $tipoServicio->EnumerateGroupByDepartament(true);
        echo json_encode($json);
        break;
    case "openEditCompany":
        $data['title'] = "Editar empresa";
        $data["form"] = "frm-company";

        $company->setId($_POST['id']);
        $companyRow = $company->info();
        $prospect->setId($companyRow['prospect_id']);
        $smarty->assign("prospect", $prospect->info());
        $smarty->assign("post", $companyRow);
        $smarty->assign("data", $data);
        $smarty->assign("regimenes", $catalogue->EnumerateCatalogue('tipoRegimen'));
        $smarty->assign("actividades", $catalogue->EnumerateCatalogue('actividad_comercial'));
        $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        $currentServices = is_array($companyRow['services']) ? array_column($companyRow['services'], 'service_id'): [];
        $catalogoServices = $tipoServicio->EnumerateGroupByDepartament(true);
        foreach($catalogoServices as $key => $val) {
            foreach($val['options'] as $kop => $option)
            if (in_array($option['value'], $currentServices)) {
                $catalogoServices[$key]['options'][$kop]['checked'] = true;
            }
        }
        $json['services'] = $catalogoServices;
        echo json_encode($json);
        break;

    case "saveCompany":
        $company->setProspectId($_POST['prospect_id']);
        $company->setName($_POST['name']);
        $company->setIsNewCompany(isset($_POST['is_new_company']) ? 1 : 0);
        if(!isset($_POST['is_new_company']))
            $company->setConstitutionDate($_POST['date_constitution']);

        if(!isset($_POST['is_new_company']))
            $company->setRfc($_POST['rfc']);

        $company->setEmail($_POST['email']);
        $company->setPhone($_POST['phone']);
        $company->setLegalRepresentative($_POST['legal_representative']);
        $company->setObservation($_POST['observation']);
        $company->setBusinessActivity($_POST['activity_id']);
        $company->setRegimenId($_POST['regimen_id']);
        echo $company->save() ? "ok" : "fail";
        echo "[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $company->setProspectId($_POST['prospect_id']);
        $smarty->assign('results', $company->enumerate());
        $smarty->display(DOC_ROOT . '/templates/lists/company.tpl');
        break;
    case "updateCompany":
        $company->setId($_POST['id']);
        $company->setName($_POST['name']);
        $company->setIsNewCompany(isset($_POST['is_new_company']) ? 1 : 0);
        if(!isset($_POST['is_new_company']))
            $company->setConstitutionDate($_POST['date_constitution']);

        if(!isset($_POST['is_new_company']))
            $company->setRfc($_POST['rfc']);

        $company->setEmail($_POST['email']);
        $company->setPhone($_POST['phone']);
        $company->setLegalRepresentative($_POST['legal_representative']);
        $company->setObservation($_POST['observation']);
        $company->setBusinessActivity($_POST['activity_id']);
        $company->setRegimenId($_POST['regimen_id']);
        echo $company->update() ? "ok":"fail";
        echo "[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $company->setProspectId($_POST['prospect_id']);
        $smarty->assign('results', $company->enumerate());
        $smarty->display(DOC_ROOT . '/templates/lists/company.tpl');
        break;
}
