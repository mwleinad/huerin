<?php
require_once (DOC_ROOT.'/libs/jpgraph-4.3.4/src/jpgraph.php');

require_once (DOC_ROOT.'/libs/jpgraph-4.3.4/src/jpgraph_bar.php');
class HuerinGraph {

    private $data;
    private $type;
    private $title;

    public function __construct($data = []) {
        $this->data = $data;
    }
    function generateGraph () {
        switch ($this->data['type']) {
            case 'Pie':
                $this->generateGraphPie();
                break;
            case 'BarGroup':
                $this->generateGraphBarGroup();
                break;
            case 'Bar':
                $this->generateGraphBar();
                break;
        }
    }

    function generateGraphPie () {
        $graph = new PieGraph(500,350);
        $graph->ClearTheme();
        $graph->SetFrame(false);
        $graph->title->Set($this->data['title']);
        $graph->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,14);
        $graph->title->SetColor("brown");

        $p1 = new PiePlot($this->data['values']);
        $p1->SetSliceColors(array('#009900','#0FF','#FC0','#FFFF99','#F00','#47B49F','#276459','#3D182F','#C86573','#33305F','#C7EC20' ));
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

        $p1->SetLegends($this->data['legends']);
        $graph->legend->Pos(0.05,0.2);

        $graph->Add($p1);
        $graph->Stroke(_IMG_HANDLER);

        // Default is PNG so use ".png" as suffix
        $fileName = $this->data['graphName'];
        $fileNameComplete = DOC_ROOT."/sendFiles/charts/$fileName";
        $graph->img->Stream($fileNameComplete);
    }

    function generateGraphBarGroup () {
        $graph =  new Graph(450, 300);
        $graph->ClearTheme();
        $graph->SetFrame(false);
        $graph->SetScale('textlin');
        $graph->SetShadow(false);
        $graph->img->SetMargin(40,30,50,90);


        $data1y=  $this->data['data1y'];
        $data2y = $this->data['data2y'];

        // Create the bar plots
        $b1plot = new BarPlot($data1y);
        $b1plot->SetFillColor("red");
        $b1plot->SetLegend('Bajas');
        $b1plot->value->SetFormat("%d");
        $b1plot->value->HideZero();
        $b1plot->value->Show();
        $b2plot = new BarPlot($data2y);
        $b2plot->SetFillColor("darkgreen");
        $b2plot->SetLegend('Altas');
        $b2plot->value->SetFormat("%d");
        $b2plot->value->HideZero();
        $b2plot->value->Show();

        // Create the grouped bar plot
        $gbplot = new GroupBarPlot(array($b1plot,$b2plot));
        // Setup legend
        $graph->legend->SetAbsPos(10,270,'left','bottom');
        $graph->legend->SetColumns(3);
        $graph->legend->SetFont(FF_FONT1,FS_NORMAL,12);

        $graph->title->Set($this->data['title']);
        $graph->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,12);
        $graph->title->SetColor("brown");
        $graph->xaxis->SetTickLabels($this->data['xAxis']);
        $graph->xaxis->title->Set($this->data['xTitle']);
        $graph->yaxis->title->Set($this->data['yTitle']);

        $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

        $gbplot->SetWidth(0.6);
        $graph->Add($gbplot);

        $fileName = $this->data['graphName'];
        $fileNameComplete = DOC_ROOT."/sendFiles/charts/$fileName";
        $graph->Stroke(_IMG_HANDLER);
        $graph->img->Stream($fileNameComplete);

    }
    function generateGraphBar () {
        $graph =  new Graph(450, 300);
        $graph->ClearTheme();
        $graph->SetFrame(false);
        $graph->SetScale('textlin');
        $graph->SetShadow(false);
        $graph->img->SetMargin(40,30,50,90);

        $data1y=  $this->data['data1y'];

        // Create the bar plots
        $b1plot = new BarPlot($data1y);
        $b1plot->SetFillColor("blue");
        $b1plot->value->SetFormat("%d");
        $b1plot->value->HideZero();
        $b1plot->value->Show();
        $b1plot->SetWidth(0.1);

        $graph->title->Set($this->data['title']);
        $graph->title->SetFont(FF_DV_SANSSERIF,FS_BOLD,12);
        $graph->title->SetColor("brown");
        $graph->xaxis->SetTickLabels($this->data['xAxis']);
        $graph->xaxis->title->Set($this->data['xTitle']);
        $graph->yaxis->title->Set($this->data['yTitle']);

        $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

        $graph->Add($b1plot);

        $fileName = $this->data['graphName'];
        $fileNameComplete = DOC_ROOT."/sendFiles/charts/$fileName";
        $graph->Stroke(_IMG_HANDLER);
        $graph->img->Stream($fileNameComplete);
    }
}
