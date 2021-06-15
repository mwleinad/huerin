<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');
switch ($_POST["type"]) {
    case 'customer':
        $result = $customer->SuggestAutoComplete($_POST["query"]);
        $json = [];
        foreach ($result as $cust) {
            $card = [];
            $card["key"] = $cust["customerId"];
            $card["value"] = $cust["nameContact"];
            $card["customer_exists"] = $cust["customerId"];
            $card["phone"] = $cust["phone"];
            $card["email"] = $cust["email"];
            $card["observation"] = $cust["observacion"];
            $json[] = $card;
        }
        echo json_encode($json);
        break;
    case 'contract':
        $contract->setCustomerId($_POST['parent_id']);
        $result = $_POST['parent_id'] > 0
                  ? $contract->SuggestContractPureAutocomplete($_POST["query"])
                  : [];
        $json = [];
        foreach ($result as $cust) {
            $card = [];
            $card["key"] = $cust["contractId"];
            $card["value"] = $cust["name"];
            $card["contract_exists"] = $cust["contractId"];
            $card["activity_id"] = $cust["actividadComercialId"];
            $card["regimen_id"] = $cust["regimenId"];
            $card["legal_representative"] = $cust["nameRepresentanteLegal"];
            $card["rfc"] = $cust["rfc"];
            $json[] = $card;
        }
        echo json_encode($json);
    break;
}
