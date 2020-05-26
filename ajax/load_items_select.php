<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch ($_POST['type']) {
    case 'sector':
        $result =  $catalogue->ListSectores();
        echo json_encode($result);
    break;
    case 'subsector':
        $result =  $catalogue->ListSubsectores($_POST['id']);
        echo json_encode($result);
    break;
    case 'actividad_comercial':
        $result = $catalogue->ListActividadesComerciales($_POST['id']);
        echo json_encode($result);
    break;
}