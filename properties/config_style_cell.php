<?php
include(DOC_ROOT.'/libs/excel/PHPExcel.php');
$global_config_style_cell = array(
    'style_grantotal' => array(
        'numberformat' => [
            'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
        ],
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '808080')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        )
    ),
    'style_currency' => array(
        'numberformat' => [
            'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD,
        ],
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        )
    ),
    'style_porcent' => array(
        'numberformat' => [
            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE,
        ],
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        )
    ),
    'style_header' => array(
        'numberformat' => [
            'code' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,
        ],
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '808080')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        )
    ),
);
