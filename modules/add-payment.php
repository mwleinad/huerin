<?php

/* Start Session Control - Don't Remove This */
$user->allowAccess();
/* End Session Control */

$smarty->assign('mainMnu', 'cxc');

if ($_GET['id']) {
    $id_comprobante = $_GET['id'];
    $compInfo = $comprobante->GetInfoComprobante($id_comprobante);
} else {
    $id_comprobante = $_GET['isid'];
    $compInfo = $comprobante->GetInfoComprobante($id_comprobante, true);
}

$_SESSION['fromPayments'] = true;


if ($_GET['id']) {
    $serie = $compInfo['serie'];
    $folio = $compInfo['folio'];
    $user->setUserId($compInfo['userId'], 1);
} else {
    $user->setUserId($compInfo['contractId'], 1);
    $serie = "E";
    $folio = $compInfo['instanciaServicioId'];
}


$usr = $user->GetUserInfo();
$nomRfc = $usr['rfc'];

$formasDePago = $catalogo->formasDePago();
$smarty->assign("formasDePago", $formasDePago);
$tiposDeMoneda = $main->ListTipoDeMoneda33();
$smarty->assign("tiposDeMoneda", $tiposDeMoneda);
$monedaComprobante = current(array_filter($tiposDeMoneda, function ($moneda) use ($compInfo) {
    return strtolower($moneda['moneda']) == $compInfo['tipoDeMoneda'];
}));
$smarty->assign("monedaComprobante", $monedaComprobante);

$smarty->assign('id_comprobante', $id_comprobante);
$smarty->assign('post', $compInfo);
$smarty->assign('usr', $usr);
$smarty->assign('rfc', $nomRfc);
$smarty->assign('serie', $serie);
$smarty->assign('folio', $folio);
$smarty->assign('DOC_ROOT', DOC_ROOT);

$info = $user->Info();
$smarty->assign("info", $info);

$fecha = date("Y-m-d");
$smarty->assign("fecha", $fecha);
?>