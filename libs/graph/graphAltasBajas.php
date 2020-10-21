<?php

$baseBajas = [];
$baseAltas = [];
for ($bb = 1; $bb <= 12; $bb++) {
    $cad['month'] = $monthsInt[$bb];
    $cad['total'] = 0;

    $baseAltas[$bb] = $cad;
    $baseBajas[$bb] = $cad;
}
$sql = "select date_format(fechaBaja, '%m') as mes, count(contractId) as total from contract
        where activo = 'No' and fechaBaja is not null and date_format(fechaBaja, '%Y') = date_format(CURRENT_DATE(), '%Y')
        group by date_format(fechaBaja, '%m')
        ";
$db->setQuery($sql);
$bajas =  $db->GetResult();

foreach($bajas as $var) {
    $keyB = (int)$var['mes'];
    $baseBajas[$keyB]['total'] = $var['total'];
}

$sql = "select date_format(fechaAlta, '%m') as mes, count(contractId) as total from contract
        where fechaAlta is not null and date_format(fechaAlta, '%Y') = date_format(CURRENT_DATE(), '%Y')
        group by date_format(fechaAlta, '%m')
        ";
$db->setQuery($sql);
$altas =  $db->GetResult();
foreach($altas as $var) {
    $keyA = (int)$var['mes'];
    $altas[$keyA]['total'] = $var['total'];
}
$data['type'] = 'Bar';
$data['title'] = 'Altas y bajas por mes';
$data['xTitle'] = 'Meses';
$data['yTitle'] = 'Altas y Bajas';
$data['data1y'] = array_column($baseBajas, 'total');
$data['data2y'] = array_column($baseAltas, 'total');
$data['xAxis'] = array_column($baseAltas, 'month');
$fileName = 'altas_bajas.png';
$data['graphName'] = $fileName;
$graph = new HuerinGraph($data);
$graph->generateGraph();
?>
