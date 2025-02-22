<?php
require_once dirname( __FILE__ ) . '/../../public/common.inc.php';
Common::loadLib('PHPExcel/Classes/PHPExcel');
define("PROJECT_ROOT", dirname( __FILE__ ).'/../../../');
define("EXCEL_SERVER", PROJECT_ROOT.'01_design/excel/');
define('EXCEL_SERVER_CONFIG',PROJECT_ROOT.'04_server/config/game_WYFH/');
require_once dirname( __FILE__ ) . '/../../public/common.inc.php';
Common::loadLib("PHPExcel/Classes/PHPExcel");
Common::loadLib("PHPExcel/Classes/PHPExcel/Reader/Excel5");
Common::loadLib("PHPExcel/Classes/PHPExcel/IOFactory");
$objPHPExcel = new PHPExcel();
$filename = scandir(EXCEL_SERVER);

foreach($filename as $k=>$v){
    if($v == "." || $v == ".." || $v != "filter.xls"){
        continue;
    }
    $phpFileName =  str_replace('.xls','.php',$v);
    $data = get_excel_data($v);
    create_file($phpFileName,$data);
}

function get_excel_data($fileName,$getNum = 0){
    $filePath = EXCEL_SERVER.$fileName;
    $PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
    $PHPExcel = $PHPReader->load($filePath);//读取一张excel表，返回excel文件对象
    $currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
    $allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如，E
    $allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
    $end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
    $data = array();
    for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
    {
        //获取第一列
        $column = $currentSheet->getCell('A'.$currentRow)->getCalculatedValue();
        for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
            //过滤第 1 , 2 行  ;
            //由列数反转列名(0->'A')
            $col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
            $zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
            $zhi = preg_split('/(?<!^)(?!$)/u', $zhi);
            $p = implode('.{0,2}', $zhi);
            $p = str_replace(array('(',')','+','*','['.']','/','$','^','?'),array('（','）','\+','\*','\[','\]','\/','\$','\^','\?'),$p);
            $pa = "'/{$p}/ui',";
            $data[] = $pa;
        }
    }
    
    foreach($data as $v){
        $total .= "\t".$v.PHP_EOL;
    }
    return $total;
}

/*
 * 通用获取按ID排列的配置
 */
function create_file($filename,$cfg_data){
    if (!$cfg_data) {
        return true;
    }
    $require_file = EXCEL_SERVER_CONFIG . $filename;//需要包含的文件
    $file = fopen($require_file,'w');
    fwrite($file, "<?php\n return array(\n" . $cfg_data . ');');
    @chmod($require_file,0777);
    fclose($file);
    echo $filename."\n";
    return true;
}
