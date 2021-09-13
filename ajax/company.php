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
        $json['services'] = '';
        $primaryService = $tipoServicio->EnumerateServiceGroupByDepForSelect2(1);
        $json['listServices'] = $primaryService;
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
        $json['services'] = implode(',', $currentServices);
        $primaryService = $tipoServicio->EnumerateServiceGroupByDepForSelect2(1);
        $json['listServices'] = $primaryService;
        echo json_encode($json);
        break;
    case "saveCompany":
        $company->setProspectId($_POST['prospect_id']);
        $company->setTaxPurpose($_POST['tax_purpose']);
        $company->setName($_POST['name']);
        $company->setIsNewCompany(isset($_POST['is_new_company']) ? 1 : 0);

        if(!isset($_POST['is_new_company']) && $_POST['tax_purpose'] !== '') {
            $company->setRfc($_POST['rfc']);
            $company->setConstitutionDate($_POST['date_constitution']);
            $company->setBusinessActivity($_POST['activity_id']);
            $company->setRegimenId($_POST['regimen_id']);
        }

        //$company->setEmail($_POST['email']);
        //$company->setPhone($_POST['phone']);
        $company->setLegalRepresentative($_POST['legal_representative']);
        $company->setObservation($_POST['observation']);

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
        $company->setTaxPurpose($_POST['tax_purpose']);
        $company->setName($_POST['name']);
        $company->setIsNewCompany(isset($_POST['is_new_company']) ? 1 : 0);

        if(!isset($_POST['is_new_company']) && $_POST['tax_purpose'] !== '') {
            $company->setRfc($_POST['rfc']);
            $company->setConstitutionDate($_POST['date_constitution']);
            $company->setBusinessActivity($_POST['activity_id']);
            $company->setRegimenId($_POST['regimen_id']);
        }

        //$company->setEmail($_POST['email']);
        //$company->setPhone($_POST['phone']);
        $company->setLegalRepresentative($_POST['legal_representative']);
        $company->setObservation($_POST['observation']);
        echo $company->update() ? "ok":"fail";
        echo "[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        echo "[#]";
        $company->setProspectId($_POST['prospect_id']);
        $smarty->assign('results', $company->enumerate());
        $smarty->display(DOC_ROOT . '/templates/lists/company.tpl');
        break;
    case "generarCotizacion":
        $data['title'] = "Generar Cotizacion";
        $data["form"] = "frm-cotizacion";

        $company->setId($_POST['id']);
        $companyRow = $company->info();
        $prospect->setId($companyRow['prospect_id']);
        $smarty->assign("prospect", $prospect->info());
        $smarty->assign("post", $companyRow);
        $smarty->assign("data", $data);
        $smarty->assign("services", $companyRow['services']);
        $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        echo json_encode($json);
    break;
    case "openValidateQuote":
        $data['title'] = "Validar cotización";
        $data["form"] = "frm-validate-cotizacion";

        $company->setId($_POST['id']);
        $companyRow = $company->info(true);
        $prospect->setId($companyRow['prospect_id']);
        $smarty->assign("prospect", $prospect->info());
        $smarty->assign("post", $companyRow);
        $smarty->assign("data", $data);
        $smarty->assign("services", $companyRow['services']);
        $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        echo json_encode($json);
        break;
    case "openSendToMain":
        $data['title'] = "Aceptar o declinar";
        $data["form"] = "frm-accept-quote";

        $company->setId($_POST['id']);
        $companyRow = $company->info(true);
        $prospect->setId($companyRow['prospect_id']);
        $smarty->assign("prospect", $prospect->info());
        $smarty->assign("post", $companyRow);
        $smarty->assign("data", $data);
        $smarty->assign("services", $companyRow['services']);
        $smarty->assign("partners", $catalogue->ListAssociated());
        $smarty->assign("emisores", $rfc->listEmisores());
        $smarty->assign("sociedades", $sociedad->EnumerateAll());
        $smarty->assign("metodoPagos", $catalogue->EnumerateCatalogue('c_FormaPago'));
        $smarty->assign("regimenes", $catalogue->EnumerateCatalogue('tipoRegimen'));
        $smarty->assign("actividades", $catalogue->EnumerateCatalogue('actividad_comercial'));
        $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        echo json_encode($json);
        break;
    case 'sendToMain':
        $company->setId($_POST['id']);
        echo $company->processSendToMain() ? "ok":"fail";
        echo "[#]";
        $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
    break;
}
