<?php
$facturas = [];
/* Star Session Control Modules*/
$user->allowAccess(4);  //level 1
$user->allowAccess(120);//level 2
/* end Session Control Modules*/

/*$directorio = opendir(DOC_ROOT."/sendFiles/facturas");
while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
{
    $cad = [];
    if(strpos($archivo,'zip')!==false || strpos($archivo,'COMPAGO')!==false || strpos($archivo,'SIGN')===false)
        continue;

    $pathXml =  DOC_ROOT."/sendFiles/facturas/".$archivo;
    $xml = simplexml_load_file($pathXml);
    $ns = $xml->getNamespaces(true);
    $xml->registerXPathNamespace('c',$ns['cfdi']);
    $xml->registerXPathNamespace('t',$ns['tfd']);

    $cfdi= $xml->xpath('//cfdi:Comprobante')[0];
    $data["emisor"] = $xml->xpath('//cfdi:Emisor')[0];
    $data["receptor"] = $xml->xpath('//cfdi:Receptor')[0];
    foreach($xml->xpath('//t:TimbreFiscalDigital') as $con){
        $data['timbreFiscal'] = $con;
    }
    $cad['total'] = (string)$cfdi['Total'];
    $cad['subtotal'] = (string)$cfdi['SubTotal'];
    $cad['folio'] =(string)$cfdi['Serie'].(string)$cfdi['Folio'];
    $cad['fecha'] = (string)$cfdi['Fecha'];
    $cad['receptorRfc'] = (string)$data['receptor']['Rfc'];
    $cad['receptorName'] = (string)$data['receptor']['Nombre'];
    $cad['emisorRfc'] = (string)$data['emisor']['Rfc'];
    $cad['emisorName'] =(string)$data['emisor']['Nombre'];
    $cad['uuid'] =(string)$data['timbreFiscal']['UUID'];
    //comprobar pagos realizados
    $cad['pagos'] = $comprobante->getSaldoFromXml($cad);
    $cad['saldo'] = $cad['total']-$cad['pagos'];
    $nameArchivo =  explode(".",$archivo);
    $cad['nameXml'] = $nameArchivo[0];
    $facturas[] =  $cad;
}*/
$smarty->assign('mainMnu','admin-folios');
$smarty->assign('facturas',$facturas);
$smarty->assign('year',date('Y'));






