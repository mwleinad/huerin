<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');

switch ($_POST['type']) {
    case "openAddQuestion":
        if(isset($_SESSION['optionQuestion']))
            unset($_SESSION['optionQuestion']);

        $data['title'] = "Agregar pregunta";
        $data["form"] = "frm-question";
        $smarty->assign("data", $data);
        $smarty->assign("services", $catalogue->EnumerateCatalogue('tipoServicio'));
        $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        echo json_encode($json);
        break;
    case "openEditQuestion":
        if(isset($_SESSION['optionQuestion']))
            unset($_SESSION['optionQuestion']);

        $data['title'] = "Editar pregunta";
        $data["form"] = "frm-question";

        $question->setId($_POST['id']);
        $questionRow = $question->info();
        $smarty->assign("post", $questionRow);
        $smarty->assign("data", $data);
        $smarty->assign("services", $catalogue->EnumerateCatalogue('tipoServicio'));
        $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/boxes/general-popup.tpl");
        $json['info'] = $questionRow;
        $_SESSION['optionQuestion'] = $questionRow['answer'];
        echo json_encode($json);
        break;
    case "editOption":
        $item = (isset($_SESSION['optionQuestion']) && isset($_POST['key'])) ? $_SESSION['optionQuestion'][$_POST['key']] : null;
        echo json_encode($item);
        break;

    case "addOption":
        if((!isset($_POST['text']) || $_POST['price'] === '') || !(isset($_POST['text']) || $_POST['price'] === ''))
            $util->setError(0, "error", "Es necesario una respuesta y valor");
        if(isset($_POST['price']))
            $util->ValidateOnlyNumeric($_POST['price'], 'Valor');

        if($util->PrintErrors()) {
            $json['status'] = 'fail';
            $json['message'] = $smarty->fetch(DOC_ROOT. "/templates/boxes/status_on_popup.tpl");
        } else {
            if (!isset($_SESSION['optionQuestion']))
                $_SESSION['optionQuestion'] = [];

            end($_SESSION['optionQuestion']);
            $key = isset($_POST['key'])
                ? $_POST['key']
                : (empty($_SESSION['optionQuestion'])
                ? 0
                : key($_SESSION['optionQuestion']) + 1
                );
            $_SESSION['optionQuestion'][$key]['id'] = $_POST['key'] >= 0 ? $_SESSION['optionQuestion'][$key]['id'] : null;
            $_SESSION['optionQuestion'][$key]['text'] = $_POST['text'];
            $_SESSION['optionQuestion'][$key]['price'] = $_POST['price'];
            $smarty->assign('options', $_SESSION['optionQuestion']);
            $json['status'] = 'ok';
            $json['template'] = $smarty->fetch(DOC_ROOT . "/templates/lists/option-question.tpl");
            $json['options'] = $_SESSION['optionQuestion'];
        }
        echo json_encode($json);
        break;
    case "deleteOption":
        unset($_SESSION['optionQuestion'][$_POST['key']]);
        $smarty->assign('options', $_SESSION['optionQuestion']);
        $json['template'] = $smarty->fetch(DOC_ROOT."/templates/lists/option-question.tpl");
        $json['options'] = $_SESSION['optionQuestion'];
        echo json_encode($json);
        break;
}
