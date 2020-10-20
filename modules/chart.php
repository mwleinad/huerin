<?php
$items = [];
include_once(DOC_ROOT.'/libs/graph/graphContract.php');
$item = [
    'url'=>$fileName,
    'name' => 'graph1'
];

array_push($items, $item);


include_once (DOC_ROOT.'/libs/graph/graphTypePerson.php');
$item = [
    'url'=>$fileName,
    'name' => 'graph2'
];
array_push($items, $item);

include_once (DOC_ROOT.'/libs/graph/graphTypeRegimen.php');
$item = [
    'url'=>$fileName,
    'name' => 'graph2'
];
array_push($items, $item);

include_once (DOC_ROOT.'/libs/graph/graphMonth13.php');
$item = [
    'url'=>$fileName,
    'name' => 'graph2'
];
array_push($items, $item);
$smarty->assign('charts', $items);
$smarty->assign('mainMnu', 'reportes');

