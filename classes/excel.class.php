<?php

class Excel
{

    public function ConvertToExcel($htmltable, $type, $debug = false,$fileName='exportar',$wrap=false,$finWrap='D')
    {
        if($debug === false) {
            $debug = false;
        }else{
            $debug = true;
            //  $handle = fopen("Auditlog/exportlog.txt", "w");
            //  fwrite($handle, "\nDebugging On...");
        }

        if(strlen($htmltable) == strlen(strip_tags($htmltable)) ) {
            echo "<br />Invalid HTML Table after Stripping Tags, nothing to Export.";
            exit;
        }

        $htmltable = strip_tags($htmltable, "<table><tr><th><thead><tbody><tfoot><td><br><br /><b><span>");
        $htmltable = str_replace("<br />", "\n", $htmltable);
        $htmltable = str_replace("<br/>", "\n", $htmltable);
        $htmltable = str_replace("<br>", "\n", $htmltable);
        $htmltable = str_replace("&nbsp;", " ", $htmltable);
        $htmltable = str_replace("\n\n", "\n", $htmltable);

        //
        //  Extract HTML table contents to array
        //

        $dom = new domDocument;
        $htmltable = utf8_decode($htmltable);
        @$dom->loadHTML($htmltable);
        if(!$dom) {
            echo "<br />Invalid HTML DOM, nothing to Export.";
            exit;
        }
        $dom->preserveWhiteSpace = false;             // remove redundant whitespace
        $tables = $dom->getElementsByTagName('table');
        if(!is_object($tables)) {
            echo "<br />Invalid HTML Table DOM, nothing to Export.";
            exit;
        }
        if($debug) {
            fwrite($handle, "\nTable Count: ".$tables->length);
        }
        $tbcnt = $tables->length - 1;                 // count minus 1 for 0 indexed loop over tables
        if($tbcnt > $limit) {
            $tbcnt = $limit;
        }

        //
        //
        // Create new PHPExcel object with default attributes
        //

        include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
        $tm = date(YmdHis);
        $pos = strpos($usermail, "@");
        $user = substr($usermail, 0, $pos);
        $user = str_replace(".","",$user);
        $tfn = $user."_".$tm."_".$tablevar.".xlsx";

        $fname = $tfn;
        $objPHPExcel->getProperties()->setCreator($username)
            ->setLastModifiedBy($username)
            ->setTitle("Automated Export")
            ->setSubject("Automated Report Generation")
            ->setDescription("Automated report generation.")
            ->setKeywords("Exported File")
            ->setCompany($usercompany)
            ->setCategory("Export");
        //
        // Loop over tables in DOM to create an array, each table becomes a worksheet
        //

        for($z=0;$z<=$tbcnt;$z++) {

            $maxcols = 0;
            $totrows = 0;
            $headrows = array();
            $bodyrows = array();
            $r = 0;
            $h = 0;
            $rows = $tables->item($z)->getElementsByTagName('tr');
            $totrows = $rows->length;
            if($debug) {
                fwrite($handle, "\nTotal Rows: ".$totrows);
            }

            foreach ($rows as $row) {
                $ths = $row->getElementsByTagName('th');
                if(is_object($ths)) {
                    if($ths->length > 0) {
                        $headrows[$h]['colcnt'] = $ths->length;
                        if($ths->length > $maxcols) {
                            $maxcols = $ths->length;
                        }
                        $nodes = $ths->length - 1;
                        for($x=0;$x<=$nodes;$x++) {
                            $thishdg = $ths->item($x)->nodeValue;
                            $headrows[$h]['th'][] = $thishdg;
                            $headrows[$h]['bold'][] = findBoldText(innerHTML($ths->item($x)));
                            if($ths->item($x)->hasAttribute('style')) {
                                $style = $ths->item($x)->getAttribute('style');
                                $stylecolor = findStyleColor($style);
                                if($stylecolor == '') {
                                    $headrows[$h]['color'][] = findSpanColor(innerHTML($ths->item($x)));
                                }else{
                                    $headrows[$h]['color'][] = $stylecolor;
                                }
                            }else{
                                $headrows[$h]['color'][] = findSpanColor(innerHTML($ths->item($x)));
                            }
                            if($ths->item($x)->hasAttribute('colspan')) {
                                $headrows[$h]['colspan'][] = $ths->item($x)->getAttribute('colspan');
                            }else{
                                $headrows[$h]['colspan'][] = 1;
                            }
                            if($ths->item($x)->hasAttribute('align')) {
                                $headrows[$h]['align'][] = $ths->item($x)->getAttribute('align');
                            }else{
                                $headrows[$h]['align'][] = 'left';
                            }
                            if($ths->item($x)->hasAttribute('valign')) {
                                $headrows[$h]['valign'][] = $ths->item($x)->getAttribute('valign');
                            }else{
                                $headrows[$h]['valign'][] = 'top';
                            }
                            if($ths->item($x)->hasAttribute('bgcolor')) {
                                $headrows[$h]['bgcolor'][] = str_replace("#", "", $ths->item($x)->getAttribute('bgcolor'));
                            }else if($ths->item($x)->hasAttribute('class')&& $wrap){

                                if(preg_match("/cabeceraTabla/i",$ths->item($x)->getAttribute('class')))
                                {$headrows[$h]['bgcolor'][] = '6b6b6b';}
                                elseif(preg_match("/divInside/i",$ths->item($x)->getAttribute('class')))
                                {$headrows[$h]['bgcolor'][] = '686DAB';}
                                elseif(preg_match("/greenBox/i",$ths->item($x)->getAttribute('class')))
                                {$headrows[$h]['bgcolor'][] = '009900';}
                                elseif(preg_match("/redBox/i",$ths->item($x)->getAttribute('class')))
                                {$headrows[$h]['bgcolor'][] = 'F00F00';}
                                else
                                 $headrows[$h]['bgcolor'][] = 'FFFFFF';

                            }
                            else
                            {
                                $headrows[$h]['bgcolor'][] = 'FFFFFF';
                            }
                        }
                        $h++;
                    }
                }
            }

            foreach ($rows as $row) {
                $tds = $row->getElementsByTagName('td');
                if(is_object($tds)) {
                    if($tds->length > 0) {
                        $bodyrows[$r]['colcnt'] = $tds->length;
                        if($tds->length > $maxcols) {
                            $maxcols = $tds->length;
                        }
                        $nodes = $tds->length - 1;
                        for($x=0;$x<=$nodes;$x++) {
                            $thistxt = $tds->item($x)->nodeValue;
                            $bodyrows[$r]['td'][] = trim($thistxt);
                            $bodyrows[$r]['bold'][] = findBoldText(innerHTML($tds->item($x)));
                            if($tds->item($x)->hasAttribute('style')) {
                                $style = $tds->item($x)->getAttribute('style');
                                $stylecolor = findStyleColor($style);
                                if($stylecolor == '') {
                                    $bodyrows[$r]['color'][] = findSpanColor(innerHTML($tds->item($x)));
                                }else{
                                    $bodyrows[$r]['color'][] = $stylecolor;
                                }
                            }else{
                                $bodyrows[$r]['color'][] = findSpanColor(innerHTML($tds->item($x)));
                            }
                            if($tds->item($x)->hasAttribute('colspan')) {
                                $bodyrows[$r]['colspan'][] = $tds->item($x)->getAttribute('colspan');
                            }else{
                                $bodyrows[$r]['colspan'][] = 1;
                            }
                            if($tds->item($x)->hasAttribute('align')) {
                                $bodyrows[$r]['align'][] = $tds->item($x)->getAttribute('align');
                            }else{
                                $bodyrows[$r]['align'][] = 'left';
                            }
                            if($tds->item($x)->hasAttribute('valign')) {
                                $bodyrows[$r]['valign'][] = $tds->item($x)->getAttribute('valign');
                            }else{
                                $bodyrows[$r]['valign'][] = 'center';
                            }

                            if($tds->item($x)->hasAttribute('bgcolor')) {
                                $bodyrows[$r]['bgcolor'][] = str_replace("#", "", $tds->item($x)->getAttribute('bgcolor'));
                            }else if($tds->item($x)->hasAttribute('class')){

                                if(preg_match("/stCompletoTardio/i",$tds->item($x)->getAttribute('class')))
                                {$bodyrows[$r]['bgcolor'][] = '01FFFF';}
                                elseif(preg_match("/stCompleto/i",$tds->item($x)->getAttribute('class')))
                                {$bodyrows[$r]['bgcolor'][] = '009900';}
                                else if(preg_match("/stPorCompletar/i",$tds->item($x)->getAttribute('class')))
                                {$bodyrows[$r]['bgcolor'][] = 'FFCC00';}
                                else if(preg_match("/stIniciado/i",$tds->item($x)->getAttribute('class')))
                                {$bodyrows[$r]['bgcolor'][] = 'FFFF99';}
                                else if(preg_match("/stPorIniciar/i",$tds->item($x)->getAttribute('class')))
                                {$bodyrows[$r]['bgcolor'][] = 'F00F00';}
                                else
                                {$bodyrows[$r]['bgcolor'][] = 'FFFFFF';}

                            }
                            else
                            {
                                $bodyrows[$r]['bgcolor'][] = 'FFFFFF';
                            }
                        }
                        $r++;
                    }
                }
            }

            if($z > 0) {
                $objPHPExcel->createSheet($z);
            }
            $suf = $z + 1;
            $tableid = $tablevar.$suf;
            $wksheetname = ucfirst($tableid);
            $objPHPExcel->setActiveSheetIndex($z);                      // each sheet corresponds to a table in html
            $objPHPExcel->getActiveSheet()->setTitle($wksheetname);     // tab name
            $worksheet = $objPHPExcel->getActiveSheet();                // set worksheet we're working on
            $style_overlay = array('font' =>
                array('color' =>
                    array('rgb' => '000000'),'bold' => false,),
                'fill' 	=>
                    array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCFF')),
                'alignment' =>
                    array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP),
                'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
            );
            $xcol = '';
            $xrow = 1;
            $usedhdrows = 0;
            $heightvars = array(1=>'42', 2=>'42', 3=>'48', 4=>'52', 5=>'58', 6=>'64', 7=>'68', 8=>'76', 9=>'82');
            for($h=0;$h<count($headrows);$h++) {
                $th = $headrows[$h]['th'];
                $colspans = $headrows[$h]['colspan'];
                $aligns = $headrows[$h]['align'];
                $valigns = $headrows[$h]['valign'];
                $bgcolors = $headrows[$h]['bgcolor'];
                $colcnt = $headrows[$h]['colcnt'];
                $colors = $headrows[$h]['color'];
                $bolds = $headrows[$h]['bold'];
                $usedhdrows++;
                $mergedcells = false;
                for($t=0;$t<count($th);$t++) {
                    if($xcol == '') {$xcol = 'A';}else{$xcol++;}
                    $thishdg = $th[$t];
                    $thisalign = $aligns[$t];
                    $thisvalign = $valigns[$t];
                    $thiscolspan = $colspans[$t];
                    $thiscolor = $colors[$t];
                    $thisbg = $bgcolors[$t];
                    $thisbold = $bolds[$t];
                    $strbold = ($thisbold==true) ? 'true' : 'false';
                    if($thisbg == 'FFFFFF') {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
                    }else{
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                    }
                    $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
                    $style_overlay['alignment']['horizontal'] = $thisalign;
                    $style_overlay['font']['color']['rgb'] = $thiscolor;
                    $style_overlay['font']['bold'] = $thisbold;
                    $style_overlay['fill']['color']['rgb'] = $thisbg;
                    $worksheet->setCellValue($xcol.$xrow, $thishdg);
                    $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);
                    if($debug) {
                        fwrite($handle, "\n".$xcol.":".$xrow." ColSpan:".$thiscolspan." Color:".$thiscolor." Align:".$thisalign." VAlign:".$thisvalign." BGColor:".$thisbg." Bold:".$strbold." cellValue: ".$thishdg);
                    }
                    if($thiscolspan > 1) {                                                // spans more than 1 column
                        $mergedcells = true;
                        $lastxcol = $xcol;
                        for($j=1;$j<$thiscolspan;$j++) {
                            $lastxcol++;
                            $worksheet->setCellValue($lastxcol.$xrow, '');
                            $worksheet->getStyle($lastxcol.$xrow)->applyFromArray($style_overlay);
                        }
                        $cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;
                        if($debug) {
                            fwrite($handle, "\nmergeCells: ".$xcol.":".$xrow." ".$lastxcol.":".$xrow);
                        }
                        $worksheet->mergeCells($cellRange);
                        $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
                        $num_newlines = substr_count($thishdg, "\n");                       // count number of newline chars
                        if($num_newlines > 1) {
                            $rowheight = $heightvars[1];                                      // default to 35
                            if(array_key_exists($num_newlines, $heightvars)) {
                                $rowheight = $heightvars[$num_newlines];
                            }else{
                                $rowheight = 75;
                            }
                            $worksheet->getRowDimension($xrow)->setRowHeight($rowheight);     // adjust heading row height
                        }
                        $xcol = $lastxcol;
                    }
                }
                $xrow++;
                $xcol = '';
            }
            //Put an auto filter on last row of heading only if last row was not merged
            if(!$mergedcells) {
                $worksheet->setAutoFilter("A$usedhdrows:" . $worksheet->getHighestColumn() . $worksheet->getHighestRow() );
            }
            if($debug) {
                fwrite($handle, "\nautoFilter: A".$usedhdrows.":".$worksheet->getHighestColumn().$worksheet->getHighestRow());
            }
            // Freeze heading lines starting after heading lines
            $usedhdrows++;
            $worksheet->freezePane("A$usedhdrows");
            if($debug) {
                fwrite($handle, "\nfreezePane: A".$usedhdrows);
            }
            //
            // Loop thru data rows and write them out
            //
            $xcol = '';
            $xrow = $usedhdrows;
            for($b=0;$b<count($bodyrows);$b++) {
                $td = $bodyrows[$b]['td'];
                $colcnt = $bodyrows[$b]['colcnt'];
                $colspans = $bodyrows[$b]['colspan'];
                $aligns = $bodyrows[$b]['align'];
                $valigns = $bodyrows[$b]['valign'];
                $bgcolors = $bodyrows[$b]['bgcolor'];
                $colors = $bodyrows[$b]['color'];
                $bolds = $bodyrows[$b]['bold'];
                for($t=0;$t<count($td);$t++) {
                    if($xcol == '') {$xcol = 'A';}else{$xcol++;}
                    $thistext = $td[$t];
                    $thisalign = $aligns[$t];
                    $thisvalign = $valigns[$t];
                    $thiscolspan = $colspans[$t];
                    $thiscolor = $colors[$t];
                    $thisbg = $bgcolors[$t];
                    $thisbold = $bolds[$t];
                    $strbold = ($thisbold==true) ? 'true' : 'false';
                    if($thisbg == 'FFFFFF') {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
                    }else{
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                    }
                    $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
                    $style_overlay['alignment']['horizontal'] = $thisalign;
                    $style_overlay['font']['color']['rgb'] = $thiscolor;
                    $style_overlay['font']['bold'] = $thisbold;
                    $style_overlay['fill']['color']['rgb'] = $thisbg;
                    if($thiscolspan == 1) {
                        if($wrap){
                            if($xcol>$finWrap)
                                $worksheet->getColumnDimension($xcol)->setWidth(12);
                            else
                                $worksheet->getColumnDimension($xcol)->setAutoSize(true);
                        }
                        else
                            $worksheet->getColumnDimension($xcol)->setWidth(10);
                    }
                    if($thistext[0]=='$')
                    {
                        $thistext= str_replace('$','',$thistext);
                        $style_overlay['numberformat']['code']=PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
                    }


                    $worksheet->setCellValue($xcol.$xrow, $thistext);
                    if($debug) {
                        fwrite($handle, "\n".$xcol.":".$xrow." ColSpan:".$thiscolspan." Color:".$thiscolor." Align:".$thisalign." VAlign:".$thisvalign." BGColor:".$thisbg." Bold:".$strbold." cellValue: ".$thistext);
                    }
                    $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);
                    if($wrap)
                        $worksheet->getRowDimension($xrow)->setRowHeight(-1);

                    if($thiscolspan > 1) {                                                // spans more than 1 column
                        $lastxcol = $xcol;
                        for($j=1;$j<$thiscolspan;$j++) {
                            $lastxcol++;
                        }
                        $cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;
                        if($debug) {
                            fwrite($handle, "\nmergeCells: ".$xcol.":".$xrow." ".$lastxcol.":".$xrow);
                        }
                        $worksheet->mergeCells($cellRange);
                        $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
                        $xcol = $lastxcol;
                    }
                }

                $xrow++;
                $xcol = '';
            }
            // autosize columns to fit data
            $azcol = 'A';
            for($x=1;$x==$maxcols;$x++) {
                $worksheet->getColumnDimension($azcol)->setAutoSize(true);
                $azcol++;
            }
            if($debug) {
                fwrite($handle, "\nHEADROWS: ".print_r($headrows, true));
                fwrite($handle, "\nBODYROWS: ".print_r($bodyrows, true));
            }
        } // end for over tables
        $objPHPExcel->setActiveSheetIndex(0);                      // set to first worksheet before close
//
// Write to Browser
//
        if($debug) {
            fclose($handle);
        }
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//header("Content-Disposition: attachment;filename=$fname");
//header('Cache-Control: max-age=0');
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save($fname);

//$objWriter->save(DOC_ROOT."/excel.xlsx");
//$objWriter->save('php://output');
        if($type == "pdf")
        {
//$rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
//	$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
            $rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
            //$rendererLibrary = 'tcPDF5.9';
            //$rendererLibrary = 'mPDF';
            $rendererLibrary = '';
            $rendererLibraryPath = DOC_ROOT.'/pdf/' . $rendererLibrary;
            PHPExcel_Settings::setPdfRenderer(
                $rendererName,
                $rendererLibraryPath);

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

            $objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save(DOC_ROOT."/exportar.pdf");
        }
        else
        {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            if($fileName=='exportar')
                $objWriter->save(DOC_ROOT."/exportar.xlsx");
            else
                $objWriter->save(DOC_ROOT."/sendFiles/".$fileName.".xlsx");
        }

    }

}

function innerHTML($node) {
    $doc = $node->ownerDocument;
    $frag = $doc->createDocumentFragment();
    foreach ($node->childNodes as $child) {
        $frag->appendChild($child->cloneNode(TRUE));
    }
    return $doc->saveXML($frag);
}
function findSpanColor($node) {
    $pos = stripos($node, "color:");       // ie: looking for style='color: #FF0000;'
    if ($pos === false) {                  //                        12345678911111
        return '000000';                     //                                 01234
    }
    $node = substr($node, $pos);           // truncate to color: start
    $start = "#";                          // looking for html color string
    $end = ";";                            // should end with semicolon
    $node = " ".$node;                     // prefix node with blank
    $ini = stripos($node,$start);          // look for #
    if ($ini === false) return "000000";   // not found, return default color of black
    $ini += strlen($start);                // get 1 byte past start string
    $len = stripos($node,$end,$ini) - $ini; // grab substr between start and end positions
    return substr($node,$ini,$len);        // return the RGB color without # sign
}
function findStyleColor($style) {
    $pos = stripos($style, "color:");      // ie: looking for style='color: #FF0000;'
    if ($pos === false) {                  //                        12345678911111
        return '';                           //                                 01234
    }
    $style = substr($style, $pos);           // truncate to color: start
    $start = "#";                          // looking for html color string
    $end = ";";                            // should end with semicolon
    $style = " ".$style;                     // prefix node with blank
    $ini = stripos($style,$start);          // look for #
    if ($ini === false) return "";         // not found, return default color of black
    $ini += strlen($start);                // get 1 byte past start string
    $len = stripos($style,$end,$ini) - $ini; // grab substr between start and end positions
    return substr($style,$ini,$len);        // return the RGB color without # sign
}
function findBoldText($node) {
    $pos = stripos($node, "<b>");          // ie: looking for bolded text
    if ($pos === false) {                  //                        12345678911111
        return false;                        //                                 01234
    }
    return true;                           // found <b>
}

?>