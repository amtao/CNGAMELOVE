<?php
require_once dirname( __FILE__ ) . '/../../public/common.inc.php';
Common::loadLib('PHPExcel/Classes/PHPExcel');
define("PROJECT_ROOT", dirname( __FILE__ ).'/../../../');
define("EXCEL_SERVER", PROJECT_ROOT.'01_design/ExcelServer/');
define('EXCEL_SERVER_CONFIG',dirname( __FILE__ ).'/../../config/game_excel');

$filename = scandir(EXCEL_SERVER_CONFIG);
$n = 0;
foreach($filename as $k=>$v){
    if($v == "." || $v == ".."){
        continue;
    }
    $data = require_once EXCEL_SERVER_CONFIG.'/'.$v;

    if ($v == 'filter.php' || $v == 'formula.php' || $v == 'TeamModel.php') {
        continue;
    }

    $excelFileName =  str_replace('.php','.xls',$v);
    $xlsTitles = array();
    $x = $y = 0;
    $dataArray = array();
    $isArray = true;
    
    /*活动转换
    $mKeys = array();
    $aKeys = array();

    foreach($data as $key => $value) {
        if(empty($mKeys[$key])){
            $mKeys[$key] = count($value);
        }
        if(is_array($value)){
            foreach($value as $k => $v){
                array_push($aKeys,$k);
            }
        }
    }
    $sKeys = array();
    foreach($mKeys as $k=>$v){
        for($i=1;$i<=$v;$i++){
            array_push($sKeys,$k);
        }
    }
    $result = array();
    $count = 0;
    foreach($data as $key => $value){
        if($y==0){
            $dataArray[$x][$y] = $sKeys;
        }
        if($count == 0){
            $y++;
            if($y==1){
                $dataArray[$x][$y] = $aKeys;
            }
        }
        $y++;
        $cCount = 0;
        foreach($dataArray[0][1] as $_key){
            if(!isset($value[$_key])){
                continue;
            }else {
                if($cCount < count($value)){
                    $val = $value[$_key];
                    if(is_array($val)){
                        $val = json_encode($val,JSON_UNESCAPED_UNICODE);
                    }
                    array_push($result,$val);
                    $cCount++;  
                }
            }
        }
        $count++;
    }
    $dataArray[$x][2] = $result;*/


    //通用
    foreach($data as $value) {
        if (!is_array($value)) {
            $isArray = false;
            $dataArray = array();
            break;
        }
        if (0 == $y) {
            $dataArray[$x][$y] = array_keys($value);
        }
        $y++;
        foreach($dataArray[0][0] as $key) {
            $val = $value[$key];
            if (is_array($val)) {
                $val = json_encode($val,true);
            }
            if ($val === 0) {
                $val = '0';
            }
            $dataArray[$x][$y][] = $val;
        }
    }
    //MAIL_LANG
    if (!$isArray) {
        foreach($data as $key=>$value){
            $order = array("\n");
            $value = str_replace($order,"\n",$value);
            $y++;
            $dataArray[$x][$y] = array($key,$value);
        }
    }
    // if (!$isArray) {
    //     $dataArray[$x][$y] = array_keys($data);
    //     $y++;
    //     foreach($dataArray[0][0] as $key) {
    //         $val = $data[$key];
    //         if (is_array($val)) {
    //             $val = json_encode($val,true);
    //         }
    //         if ($val === 0) {
    //             $val = '0';
    //         }
    //         $dataArray[$x][$y][] = $val;
    //     }
    // }
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