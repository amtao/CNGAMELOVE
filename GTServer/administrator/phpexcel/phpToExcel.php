<?php
require_once dirname( __FILE__ ) . '/../../public/common.inc.php';
Common::loadLib('PHPExcel/Classes/PHPExcel');
define("PROJECT_ROOT", dirname( __FILE__ ).'/../../../');
define("EXCEL_SERVER", PROJECT_ROOT.'01_design/excel/');
define('EXCEL_SERVER_CONFIG',dirname( __FILE__ ).'/../../config/game_WYFH');

$filename = scandir(EXCEL_SERVER_CONFIG);
$n = 0;
foreach($filename as $k=>$v){
    if($v == "." || $v == ".."){
        continue;
    }
    $data = require_once EXCEL_SERVER_CONFIG.'/'.$v;

    if ($v != 'filter.php') {
        continue;
    }

    $excelFileName =  str_replace('.php','.xls',$v);
    $xlsTitles = array();
    $x = $y = 0;
    $dataArray = array();
    $isArray = true;
    
    foreach($data as $key=>$value){
        $order = array("\n");
        $value = str_replace($order,"\n",$value);
        $y++;
        $value = str_replace('.{0,2}','',$value);
        $value = str_replace('/ui','',$value);
        $value = str_replace('/','',$value);
        $dataArray[$x][$y] = array($value);
    }
    echo $excelFileName.PHP_EOL;
    exportExcel($dataArray,$excelFileName);
}

function exportExcel($dataArray,$fileName) {

    $objPHPExcel = new PHPExcel();
    // 设置文件属性
    $objPHPExcel->getProperties()
        ->setCreator('baba')
        ->setLastModifiedBy('baba')
        ->setTitle('baba')
        ->setSubject('baba')
        ->setDescription('baba')
        ->setKeywords('baba')
        ->setCategory('baba');

    if ( is_array($dataArray) ) {
        ini_set('memory_limit', '1024M');
        // 设置当前的sheet索引，用于后续的内容操作。
        // 一般只有在使用多个sheet的时候才需要显示调用。
        // 缺省情况下，PHPExcel会自动创建第一个sheet被设置SheetIndex=0
        $objPHPExcel->setActiveSheetIndex(0);
        $index = 0;
        foreach ($dataArray as $k => $v) {
            if ( 0 < $index ) {
                $objPHPExcel->createSheet();// 创建工作表
            }
            // 添加表记录
            $objPHPExcel->setActiveSheetIndex($index)->fromArray($v);

            // 设置过滤器
            $objPHPExcel->setActiveSheetIndex($index)->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

            $index++;
        }
        // 设置默认显示在excel的第一张表
        $objPHPExcel->setActiveSheetIndex(0);
        // Save Excel 95 file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(EXCEL_SERVER.$fileName);
    }
}