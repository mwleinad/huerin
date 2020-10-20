<?php
require_once (DOC_ROOT.'/libs/graph/src/jpgraph.php');
require_once (DOC_ROOT.'/libs/graph/src/jpgraph_pie.php');
require_once (DOC_ROOT.'/libs/graph/src/jpgraph_pie3d.php');



$sql = "select count(*) from customer where active ='1' and noFactura13 = 'Si' ";
$db->setQuery($sql);
$noFacturan = $db->GetSingle();

$sql = "select count(*) from customer where active ='1' and noFactura13 = 'No' ";
$db->setQuery($sql);
$siFacturan = $db->GetSingle();

$data =array($noFacturan, $siFacturan);

// Create the Pie Graph.
$graph = new PieGraph(500,350);
$graph->ClearTheme();
$graph->SetFrame(false);
//$graph->SetShadow();

// Set A title for the plot
$graph->title->Set("Generan factura mes 13");
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

$p1->SetLegends(array('No facturan mes 13', 'Facturan mes 13'));
$graph->legend->Pos(0.05,0.2);

$graph->Add($p1);
$graph->Stroke(_IMG_HANDLER);

// Default is PNG so use ".png" as suffix
$fileName = "imagefile_month13.png";
$fileNameComplete = DOC_ROOT."/sendFiles/charts/$fileName";
$graph->img->Stream($fileNameComplete);

//$graph->img->Headers();
//$graph->img->Stream();

?>
