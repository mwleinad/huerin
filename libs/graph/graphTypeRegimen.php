<?php
require_once (DOC_ROOT.'/libs/graph/src/jpgraph.php');
require_once (DOC_ROOT.'/libs/graph/src/jpgraph_pie.php');
require_once (DOC_ROOT.'/libs/graph/src/jpgraph_pie3d.php');

// Some data
//
$sql = "select * from regimen ";
$db->setQuery($sql);
$regimenes = $db->GetResult();

$sql = "select count(contract.contractId) as totalPorRegimen, concat_ws(' ', regimen.tipoDePersona, regimen.nombreRegimen) as regimenName from contract 
        inner join regimen on contract.regimenId = regimen.regimenId
        where contract.activo = 'Si' group by contract.regimenId ";
$db->setQuery($sql);
$regimenes = $db->GetResult();

$data =array_column($regimenes, 'totalPorRegimen');

// Create the Pie Graph.
$graph = new PieGraph(500,350);
$graph->ClearTheme();
$graph->SetFrame(false);
//$graph->SetShadow();

// Set A title for the plot
$graph->title->Set("Grafica tipo de regimen");
$graph->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,14);
$graph->title->SetColor("brown");

// Create pie plot
$p1 = new PiePlot($data);
$p1->SetSliceColors(array('#009900','#0FF','#FC0','#FFFF99','#F00','#47B49F','#276459','#3D182F','#C86573','#33305F','#C7EC20' ));
//$p1->SetTheme("earth");

//$p1->value->SetFont(FF_ARIAL,FS_NORMAL,10);
// Set how many pixels each slice should explode
$p1->ShowBorder(false);
$p1->Explode(array(0,15,15,25,15,15,25,15,25,15,25,15,25,25,25,25,20));

// Move center of pie to the left to make better room
// for the legend
$p1->SetCenter(0.35,0.5);

// No border
$p1->ShowBorder(false);

// Label font and color setup
$p1->value->SetFont(FF_FONT1,FS_BOLD);
$p1->value->SetColor("darkred");

// Use absolute values (type==1)
$p1->SetLabelType(PIE_VALUE_ABS);

// Label format
$p1->value->SetFormat("%d");
$p1->value->HideZero();
$p1->value->Show();

// Size of pie in fraction of the width of the graph
$p1->SetSize(0.3);

// Legends
$leg = array_column($regimenes, 'regimenName');
$newArray = [];
foreach ($leg as  $var) {
    $newArray[] = substr($var, 0, 10);
}
$p1->SetLegends($newArray);
$graph->legend->Pos(0.05,0.2);

$graph->Add($p1);
$graph->Stroke(_IMG_HANDLER);

// Default is PNG so use ".png" as suffix
$fileName = "imagefile_regimen.png";
$fileNameComplete = DOC_ROOT."/sendFiles/charts/$fileName";
$graph->img->Stream($fileNameComplete);

//$graph->img->Headers();
//$graph->img->Stream();

?>
