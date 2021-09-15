<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');

switch ($_POST["type"]) {
    case 'doLogin':
        $username = strip_tags($_POST['username']);
        $passwd = strip_tags($_POST['passwd']);

        $user->setUsername($username);
        $user->setPassword($passwd);
        $_SESSION['User'] = array();
        unset($_SESSION['User']);
        $_SESSION['Usr'] = array();
        unset($_SESSION['Usr']);

        if ($user->doLogin()) {
            echo "ok[#]";
        } else {
            echo "fail[#]";
            $smarty->display(DOC_ROOT . '/templates/boxes/status_on_popup.tpl');
        }

        break;
}
