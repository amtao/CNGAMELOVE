<?php
require_once dirname( __FILE__ ) . '/../public/common.inc.php';

//合服区间
//平台标识
$plat = 'eplocal';
$he = array(//合服编号id => 合服区间(按从小到大),
    1 => '3,4',

);
$address = CONFIG_DIR.'/'.$plat."/SevIdCfg.php";
$cfg = require($address);

foreach ($he as $hid => $v){
    $v = explode(',',$v);
    $heid = $v[0];
    foreach ($v as $id){
        $cfg[$id]['he'] = $heid;
        $cfg[$id]['ishe'] = $hid;
    }
}

$str = '<?php'."\n";
$str .= "//服务器ID配置\n";
//$str .= 'return $cfg = '.PHP_EOL;
//$str .= var_export($cfg,1);
$str .= '$cfg = array('.PHP_EOL;
foreach ($cfg as $id => $val){
    if($id == 999){
        $str .= "	999	=> array(
		//'sevid' => 999,//
		'he' => 999,	//合服的主服ID
		'kua' => 999,	//跨服的主服ID
	),\n";
    }else{
        $str .= "	{$id} => array('he' => {$val['he']} , 'kua' => {$val['kua']}";
        if($val['ishe']){
            $str .= " , 'ishe' => {$val['ishe']}";
        }
        $str .= "),\n";
    }
}
$str .= ');'."\n";

$str .= "\n".'return $cfg;';
$str .= "\n";

file_put_contents($address,$str);

