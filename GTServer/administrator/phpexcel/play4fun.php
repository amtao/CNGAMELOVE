<?php
//不限制执行时间
set_time_limit(0);
ini_set('memory_limit','1000M');
header("Content-Type: text/html; charset=utf-8");
define("PROJECT_ROOT", dirname( __FILE__ ).'/../../../');
$conf = isset($argv[1]) ? 'king'.$argv[1] : 'king';
define("PROJECT_CONF", PROJECT_ROOT.'GTDesign/');

error_reporting(E_ALL); 
ini_set('display_errors', '1'); 
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
//echo  dirname(__FILE__) . '/error_log.txt';
if (!isset($argv[1])) {
    exit('需要参数1');
}
$argvCfg = array( '1'=>array(
        'from'=>'excel/',
		'to'=>'game_WYFH',
		'enumto' => 'extend',
    ),
);
if (!isset($argvCfg[$argv[1]])) {
    exit('参数错误');
}
$GLOBALS['a_c'] = $argvCfg[$argv[1]];
function get_project_conf($filename)
{
    if (isset($GLOBALS['a_c']['other']) && in_array($filename, $GLOBALS['a_c']['other']['filename'])) {
        return PROJECT_CONF.$GLOBALS['a_c']['other']['from'];
    }
    return PROJECT_CONF.$GLOBALS['a_c']['from'];
}
function get_game_conf_dir($filename)
{
    if (isset($GLOBALS['a_c']['other']) && in_array($filename, $GLOBALS['a_c']['other']['filename'])) {
        return $GLOBALS['a_c']['other']['to'];
    }
    return $GLOBALS['a_c']['to'];
}

function get_game_conf_dir_enum($filename)
{
    if (isset($GLOBALS['a_c']['other']) && in_array($filename, $GLOBALS['a_c']['other']['filename'])) {
        return $GLOBALS['a_c']['other']['enumto'];
    }
    return $GLOBALS['a_c']['enumto'];
}

require_once dirname( __FILE__ ) . '/../../public/common.inc.php';
Common::loadLib("PHPExcel/Classes/PHPExcel");
Common::loadLib("PHPExcel/Classes/PHPExcel/Reader/Excel5");
Common::loadLib("PHPExcel/Classes/PHPExcel/IOFactory");
$objPHPExcel = new PHPExcel();


// -------------heropve.xls-------------
echo "\n\n\nheropve有1个表:\n";
$filename = 'heropve';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'hero_pve',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '特殊关卡', //文件中提示那个表
		'no_y' => array('D', 'E', 'H', 'I'), //不需要的列
	),
    1 => array(
        'name' => 'hero_jb_lv',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '特殊关卡等级', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    2 => array(
        'name' => 'hero_jb_prop',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '特殊关卡属性', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
     3 => array(
        'name' => 'hero_treegroup',  //形成文件的名字
        'geshi'=> array('D', 'G'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '许愿树分类', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------activityduanwu.xls-------------
echo "\n\n\nactivityduanwu:\n";
$filename = 'activityduanwu';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'activityduanwu_move',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '龙舟竞赛移动', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'activityduanwu_reward',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '随机奖励库', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------award.xls-------------
echo "\n\n\naward:\n";
$filename = 'award';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'award',  //形成文件的名字
        'geshi'=> array('B', 'C'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '特殊关卡', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------dafuweng.xls-------------
echo "\n\n\ndafuweng:\n";
$filename = 'dafuweng';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'dafuweng_step',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '踏青移动', //文件中提示那个表
        'no_y' => array("C", "D", "E"), //不需要的列
    ),
    1 => array(
        'name' => 'dafuweng_event',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '踏青事件', //文件中提示那个表
        'no_y' => array("B"), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------talkstory.xls-------------
echo "\n\n\ntalkstory:\n";
$filename = 'talkstory';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'herotalkstory',  //形成文件的名字
        'geshi'=> array('C', 'D'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => 'herotalkstory', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'wifetalkstory',  //形成文件的名字
        'geshi'=> array('C', 'D'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => 'wifetalkstory', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------xuanxiang.xls-------------
echo "\n\n\nxuanxiang:\n";
$filename = 'xuanxiang';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'xuanxiang',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '特殊关卡', //文件中提示那个表
        'no_y' => array('C', 'D', 'E', 'F', 'K'), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------jyevent.xls-------------
echo "\n\n\n有1个表:\n";
$filename = 'jyevent';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'jyevent',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '经营事件', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
    1 => array(
		'name' => 'jyevent_dialogue',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '经营事件对白', //文件中提示那个表
		'no_y' => array('C','D'), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------talk.xls-------------
echo "\n\n\n有1个表:\n";
$filename = 'talk';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'hero_talk',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '门客语音包', //文件中提示那个表
        'no_y' => array('D'), //不需要的列
    ),
    1 => array(
        'name' => 'wife_talk',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => 'wifi语音包', //文件中提示那个表
        'no_y' => array('D'), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------jybase.xls-------------
echo "\n\n\n有1个表:\n";
$filename = 'jybase';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'jyWeipai',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '经营委派', //文件中提示那个表
        'no_y' => array('A'), //不需要的列
        'key1' => 'B', //特殊结构主key
        'key2' => 'C', //特殊结构副key2
    ),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 0){
		$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------treasure.xls-------------
echo "\n\n\n有1个表:\n";
$filename = 'treasure';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'treaGroup',  //形成文件的名字
        'geshi'=> array('E'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '珍宝馆组', //文件中提示那个表
        'no_y' => array('B','C'), //不需要的列
    ),
    1 => array(   
		'name' => 'treasure',  //形成文件的名字
		'geshi'=> array('G', 'M'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '珍宝馆', //文件中提示那个表
		'no_y' => array('C','D','E','F'), //不需要的列
	),
	2 => array(   
		'name' => 'treaReward',  //形成文件的名字
		'geshi'=> array('B'), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '清扫奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
    3 => array(
        'name' => 'treaTidy',  //形成文件的名字
        'geshi'=> array('G', 'H'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '整理表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------kitchen.xls-------------
echo "\n\n\n有1个表:\n";
$filename = 'kitchen';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'kitchen',  //形成文件的名字
        'geshi'=> array('E'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '御膳房', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(   
		'name' => 'kitchen_cost',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '开火炉消耗表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
    2 => array(
        'name' => 'kitchen_shop',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '购买食材', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    3 => array(
        'name' => 'kitchen_wife',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '开火炉消耗表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    4 => array(
        'name' => 'kitchen_level',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 4,   //第几张表,从0开始
        'tip'  => '等级', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------base.xls-----------------
echo "\n\n\nbase有1个表:\n";
$filename = 'base';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'base',  //形成文件的名字
		'geshi'=> array('F'), //将json转成array()
		'biao' => 0,   //第几张表,从0开
		'tip'  => '基础表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 0){
		$cfg_data = get_base_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------vip.xls-----------------
echo "\nvip有1个表:\n";
$filename = 'vip';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'vip',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => 'VIP表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
    1 => array(
        'name' => 'fuli_vip',  //形成文件的名字
        'geshi'=> array('B', 'C'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => 'VIP奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    2 => array(
        'name' => 'vip2',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => 'VIP2表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------hero.xls-----------------
echo "\nhero有11个表:\n";
$filename = 'hero';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'hero',  //形成文件的名字
		'geshi'=> array('C','D','E','H'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => 'hero基础表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(    //资质技能表
		'name' => 'hero_epskill',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => 'hero资质技能表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(   //PK技能表
		'name' => 'hero_pkskill',  //形成文件的名字
		'geshi'=> array('G','J'), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => 'heroPK技能表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	3 => array(	//升级金币(门客升级费用配置表)
		'name' => 'hero_level',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 3,   //第几张表,从0开始
		'tip'  => 'hero升级金币(门客升级费用配置表)', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	4 => array(  //资质技能升级
		'name' => 'hero_epskill_level',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 4,   //第几张表,从0开始
		'tip'  => 'hero资质技能升级', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	5 => array(  // PK技能升级
		'name' => 'hero_pkskill_level',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 5,   //第几张表,从0开始
		'tip'  => 'heroPK技能升级', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	6 => array(  // 资质技能升级表
		'name' => 'hero_senior',  //形成文件的名字
		'geshi'=> array('D','H'), //将json转成array()
		'biao' => 6,   //第几张表,从0开始
		'tip'  => 'hero资质技能升级表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
    7 => array(  // 羁绊等级
        'name' => 'jinban_lv',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 8,   //第几张表,从0开始
        'tip'  => '羁绊等级', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
	8 => array(  // 羁绊等级
		'name' => 'hero_leaderexp',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 11,   //第几张表,从0开始
        'tip'  => '领袖经验', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
	9 => array(  // 羁绊等级
		'name' => 'hero_leaderat',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 12,   //第几张表,从0开始
        'tip'  => '领袖组合', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    10 => array(  // 羁绊等级
        'name' => 'hero_clothe',  //形成文件的名字
        'geshi'=> array('E', 'I'), //将json转成array()
        'biao' => 13,   //第几张表,从0开始
        'tip'  => '门客服装', //文件中提示那个表
        'no_y' => array('D','F','J','K'), //不需要的列
	), 
	11 => array(  // 羁绊等级
        'name' => 'hero_star',  //形成文件的名字
        'geshi'=> array('B', 'C'), //将json转成array()
        'biao' => 14,   //第几张表,从0开始
        'tip'  => '伙伴升星', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	12 => array(  // 羁绊等级解锁
        'name' => 'jiban_unlock',  //形成文件的名字
        'geshi'=> array('E','F'), //将json转成array()
        'biao' => 15,   //第几张表,从0开始
        'tip'  => '羁绊解锁', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'B', //特殊结构主key
        'key2' => 'C', //特殊结构副key2
	),
	13 => array(  // 羁绊等级解锁
        'name' => 'jiban_unlock_id',  //形成文件的名字
        'geshi'=> array('E','F'), //将json转成array()
        'biao' => 15,   //第几张表,从0开始
        'tip'  => '羁绊解锁idkey表', //文件中提示那个表
		'no_y' => array(), //不需要的列
    ),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 12){
		$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------wife.xls-----------------
echo "\nwife有3个表:\n";
$filename = 'wife';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'wife',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => 'wife基础表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'wife_skill',  //形成文件的名字
		'geshi'=> array('H'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => 'wife技能表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(
		'name' => 'wife_skill_id',  //形成文件的名字
		'geshi'=> array('H'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => 'wife对应的id表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	3 => array(
		'name' => 'wife_chuyou',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => 'wife对应的id表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 2){
		$cfg_data = get_wife_skill_id_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------pve.xls-----------------
echo "\npve有5个表:\n";
$filename = 'pve';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'pve_bmap',  //形成文件的名字
		'geshi'=> array('J','K'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '大关卡', //文件中提示那个表
		'no_y' => array('C','D','E','F','G','L','M'), //不需要的列
	),
	1 => array(
		'name' => 'pve_smap',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '小关卡', //文件中提示那个表
		'no_y' => array('D','E','F','J','K','M'), //不需要的列
	),
	2 => array(
		'name' => 'pve_boss',  //形成文件的名字
		'geshi'=> array('J','K'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '关卡boss', //文件中提示那个表
		'no_y' => array('B','C','D','E','F','G','L','M','N','Q'), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------prisoner.xls-----------------
echo "\nparam有1个表:\n";
$filename = 'param';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'param',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '参数', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------prisoner.xls-----------------
echo "\nprisoner有1个表:\n";
$filename = 'prisoner';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'pve_fanren',  //形成文件的名字
        'geshi'=> array('D', 'E'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '灵囿', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'prisoner_update',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '灵囿', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------guan.xls-----------------
echo "\nguan有1个表:\n";
$filename = 'guan';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'guan',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '官品升级,征收,政务表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1=> array(
		'name' => 'guanNeed',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '升级官品所需条件', //文件中提示那个表
		'no_y' => array('B','E'), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------zw.xls-----------------
echo "\nzw有1个表:\n";
$filename = 'zw';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'zw',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '政务表', //文件中提示那个表
		'no_y' => array('C','D','E','F'), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------son.xls-----------------
echo "\nson有个表:\n";
$filename = 'son';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'son_yn',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '幼年son', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'son_cn',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '成年son', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(
		'name' => 'son_exp',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '升级经验son', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	3 => array(
		'name' => 'son_seat',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 3,   //第几张表,从0开始
		'tip'  => '席位价格son', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	4 => array(
		'name' => 'son_type',  //形成文件的名字
		'geshi'=> array('B'), //将json转成array()
		'biao' => 5,   //第几张表,从0开始
		'tip'  => '孩子种类概率', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------son.xls-----------------
echo "\nepid有个表:\n";
$filename = 'epid';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'epid',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '属性id', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------school.xls-----------------
echo "\nschool有1个表:\n";
$filename = 'school';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'school',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '开学院价格表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
    1 => array(
        'name' => 'school_level',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '书院等级', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);

std_data_output($filename,$getNum );

// -------------monday.xls-----------------
echo "\nmonday有1个表:\n";
$filename = 'monday';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'monday',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '周一礼包', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------flower.xls-----------------
echo "\nflower有1个表:\n";
$filename = 'flower';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'flowerRain',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '晨露产出', //文件中提示那个表
        'no_y' => array('B', 'H', 'I'), //不需要的列
    ),
    1 => array(
        'name' => 'flowerCore',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '种子', //文件中提示那个表
        'no_y' => array('B'), //不需要的列
    ),
    2 => array(
        'name' => 'flowerLv',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '等级', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    3 => array(
        'name' => 'flowerFeild',  //形成文件的名字
        'geshi'=> array('E', 'F'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '花盆', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    4 => array(
        'name' => 'flowershell',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 5,   //第几张表,从0开始
        'tip'  => '保护罩', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

//---------------gift_bag------------
echo "\n gift_bag有个表:\n";
$filename = 'gift_bag';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'gift_bag',  //形成文件的名字
		'geshi'=> array('U'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '直购礼包', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------worldtree.xls-----------------
echo "\n worldtree有个表:\n";
$filename = 'worldtree';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'worldtree',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '世界树', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------qiandao.xls-----------------
echo "\nqiandao有1个表:\n";
$filename = 'qiandao';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'qiandao',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '签到奖励配置', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'fuli_fc',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '首充奖励', //文件中提示那个表
        'no_y' => array('C'), //不需要的列
    ),
    3 => array(
        'name' => 'fuli_card',  //形成文件的名字
        'geshi'=> array('E', 'F'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '月卡年卡', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	4 => array(
        'name' => 'fuli_fc_ex',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 5,   //第几张表,从0开始
        'tip'  => '连续充值', //文件中提示那个表
        'no_y' => array('D'), //不需要的列
    ),
);
std_data_output($filename,$getNum );

/*
// -------------onebuy.xls-----------------
echo "\nonebuy有1个表:\n";
$filename = 'onebuy';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'onebuy',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '单品限购', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
foreach($getNum as $num => $info){
    echo ($num+1).": ";
    $cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
    create_file($filename, $info['name'],$cfg_data,$info['tip']);
}*/

// -------------wordboss.xls-----------------
echo "\nwordboss有个表:\n";
$filename = 'wordboss';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'wordboss_mg',  //形成文件的名字
		'geshi'=> array('H'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '属性id', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'wordboss_shop',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '属性id', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(
		'name' => 'wordboss_mgrwd',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '蒙古随机奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	3 => array(
		'name' => 'wordboss_rankrwd',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 3,   //第几张表,从0开始
		'tip'  => '葛二蛋伤害排行奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------chenghao.xls-----------------
echo "\n chenghao有个表:\n";
$filename = 'chenghao';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'chenghao',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '称号列表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------wordboss.xls-----------------
echo "\n xunfang有个表:\n";
$filename = 'xunfang';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'xf_build',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '寻访-建筑', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'xf_lucky',  //形成文件的名字
		'geshi'=> array('B'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '寻访-幸运值范围', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(
		'name' => 'xf_event',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '寻访-触发事件', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	3 => array(
		'name' => 'xf_NPC',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 3,   //第几张表,从0开始
		'tip'  => '寻访-NPC', //文件中提示那个表
		'no_y' => array('B','C','D','E','F','J'), //不需要的列
	),
    4 => array(
		'name' => 'xf_clientevent',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 4,   //第几张表,从0开始
		'tip'  => '寻访-事件（客户端）', //文件中提示那个表
		'no_y' => array('B', 'C', 'L'), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------wordboss.xls-----------------
echo "\n club有个表:\n";
$filename = 'club';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'club',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '公会等级', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'club_building_up',  //形成文件的名字
		'geshi'=> array('D'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '公会建筑升级', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(
		'name' => 'club_power',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '公会权限', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	3 => array(
		'name' => 'club_boss',  //形成文件的名字
		'geshi'=> array('F'), //将json转成array()
		'biao' => 3,   //第几张表,从0开始
		'tip'  => '公会副本', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	4 => array(
		'name' => 'club_donate',  //形成文件的名字
		'geshi'=> array('B','D'), //将json转成array()
		'biao' => 4,   //第几张表,从0开始
		'tip'  => '公会贡献', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	5 => array(
		'name' => 'club_task',  //形成文件的名字
		'geshi'=> array('G'), //将json转成array()
		'biao' => 5,   //第几张表,从0开始
		'tip'  => '公会任务', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	6 => array(
		'name' => 'club_contribution',  //形成文件的名字
		'geshi'=> array('B','C'), //将json转成array()
		'biao' => 6,   //第几张表,从0开始
		'tip'  => '公会进贡', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	7 => array(
		'name' => 'club_shop',  //形成文件的名字
		'geshi'=> array('B','D'), //将json转成array()
		'biao' => 7,   //第几张表,从0开始
		'tip'  => '公会商店', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	9 => array(
		'name' => 'club_param',  //形成文件的名字
		'geshi'=> array('F'), //将json转成array()
		'biao' => 8,   //第几张表,从0开始
		'tip'  => '公会配置', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	10 => array(
		'name' => 'club_dailyRwd',  //形成文件的名字
		'geshi'=> array('C'), //将json转成array()
		'biao' => 9,   //第几张表,从0开始
		'tip'  => '公会活跃度', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	11 => array(
		'name' => 'boss_rank',  //形成文件的名字
		'geshi'=> array('D'), //将json转成array()
		'biao' => 10,   //第几张表,从0开始
		'tip'  => 'boss排行奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------wordboss.xls-----------------
echo "\n club有个表:\n";
$filename = 'club2';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'party',  //形成文件的名字
		'geshi'=> array('D','E'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '公会宴会', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'party_buff',  //形成文件的名字
		'geshi'=> array('D','E'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '公会宴会buff', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(
		'name' => 'party_task',  //形成文件的名字
		'geshi'=> array('B','C'), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '公会宴会任务', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	3 => array(
		'name' => 'party_music',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 3,   //第几张表,从0开始
		'tip'  => '公会宴会乐师', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	4 => array(
		'name' => 'party_roll',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 4,   //第几张表,从0开始
		'tip'  => '公会投壶', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------jiulou.xls-----------------
echo "\n jiulou:\n";
$filename = 'jiulou';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'jl_yanhui',  //形成文件的名字
		'geshi'=> array('D'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '酒楼-宴会信息', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'jl_shop',  //形成文件的名字
		'geshi'=> array('B','E'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '酒楼-兑换商店', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------boite.xls-----------------
echo "\n boite:\n";
$filename = 'boite';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'boite_yanhui',  //形成文件的名字
		'geshi'=> array('D'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '酒楼-宴会信息', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'boite_shop',  //形成文件的名字
		'geshi'=> array('B'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '酒楼-兑换商店', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
    2 => array(
        'name' => 'boite_add',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '酒楼-加成', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------reqiqiu.xls-----------------
echo "\n reqiqiu:\n";
$filename = 'reqiqiu';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'reqiqiu',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '酒楼-宴会信息', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------share.xls-----------------
echo "\n share:\n";
$filename = 'share';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'shareachievementrwd',  //形成文件的名字
		'geshi'=> array('C'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '分享档位奖励', //文件中提示那个表
		'no_y' => array('A'), //不需要的列
	),
	1 => array(
		'name' => 'sharedailyrwd',  //形成文件的名字
		'geshi'=> array('B'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '分享档位奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 1){
		$cfg_data = get_base_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------videorwd.xls-----------------
echo "\n videorwd:\n";
$filename = 'videorwd';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'videorwd',  //形成文件的名字
		'geshi'=> array('C'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '每日任务任务', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------dailyrwd.xls-----------------
echo "\n dailyrwd有2个表:\n";
$filename = 'dailyrwd';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'dailyrwd',  //形成文件的名字
		'geshi'=> array('D'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '每日任务任务', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'dailyrwd_rwd',  //形成文件的名字
		'geshi'=> array('D','E'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '每日任务奖励', //文件中提示那个表
		'no_y' => array('C'), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------yamen.xls-----------------
echo "\n yamen有1个表:\n";
$filename = 'yamen';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'yamen_buff',  //形成文件的名字
		'geshi'=> array('F'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '衙门增益表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'yamen_rwd',  //形成文件的名字
		'geshi'=> array('B'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '衙门连胜奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------chengjiu.xls-----------------
echo "\n chengjiu有2个表:\n";
$filename = 'chengjiu';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'cj_list',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '成就列表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'cj_info',  //形成文件的名字
		'geshi'=> array('D'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '成就详细信息', //文件中提示那个表
		'no_y' => array(), //不需要的列
        'key1' => 'E', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 1){
		$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------hanlin.xls-----------------
echo "\n hanlin有2个表:\n";
$filename = 'hanlin';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'hanlin_skill',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '翰林加成', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'hanlin_exp',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '翰林经验', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------email.xls-----------------
echo "\n email有1个表:\n";
$filename = 'email';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'emailGroup',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '书信组', //文件中提示那个表
        'no_y' => array('D', 'E'), //不需要的列
    ),
    1 => array(
        'name' => 'emailItem',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '书信', //文件中提示那个表
        'no_y' => array('B', 'D', 'E', 'F', 'G', 'H'), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------dailyrwd.xls-----------------
echo "\n task有1个表:\n";
$filename = 'task';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'task_main',  //形成文件的名字
		'geshi'=> array('E','F'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '主线任务', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------flow_php.xls-----------------
echo "\n flow_php有1个表:\n";
$filename = 'flow_php';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'flowConfig',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '流水配置', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------dailyrwd.xls-----------------
echo "\n order有3个表:\n";
$filename = 'order';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'order_platform',  //形成文件的名字
		'geshi'=> array('B','C','D','E','F','G','H','I'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '订单-平台', //文件中提示那个表
		'no_y' => array('A'), //不需要的列
	),
	1 => array(
		'name' => 'order_shop',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '订单-充值档次', //文件中提示那个表
		'no_y' => array('A'), //不需要的列
        'key1' => 'A', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
	2 => array(
		'name' => 'order_vip',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '订单-vip经验', //文件中提示那个表
		'no_y' => array('A'), //不需要的列
        'key1' => 'A', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
	3 => array(
		'name' => 'order_shop_k',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 3,   //第几张表,从0开始
		'tip'  => '订单', //文件中提示那个表
		'no_y' => array('A'), //不需要的列
        'key1' => 'A', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
	4 => array(
		'name' => 'order_shop_kapp',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 4,   //第几张表,从0开始
		'tip'  => '订单', //文件中提示那个表
		'no_y' => array('A'), //不需要的列
        'key1' => 'A', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
	5 => array(
		'name' => 'order_shop_gat',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 5,   //第几张表,从0开始
		'tip'  => '订单gat', //文件中提示那个表
		'no_y' => array('A'), //不需要的列
        'key1' => 'A', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num >= 1){
		$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------dailyrwd.xls-----------------
echo "\n order_shop_xm有3个表:\n";
$filename = 'order_shop_xm';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'order_shop_xm',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '新马订单', //文件中提示那个表
		'no_y' => array('A'), //不需要的列
		'key1' => 'A', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}

// -------------hunt.xls-----------------
echo "\n hunt有1个表:\n";
$filename = 'hunt';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'hunt',  //形成文件的名字
        'geshi'=> array('G','H'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '狩猎-关卡', //文件中提示那个表
        'no_y' => array('B','C','D'), //不需要的列
    ),
    1 => array(
        'name' => 'hunt_rwd',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '狩猎-全服奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------exam.xls-----------------
echo "\n exam有2个表:\n";
$filename = 'exam';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'exam_lv',  //形成文件的名字
        'geshi'=> array('D', 'E', 'F'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '科举等级', //文件中提示那个表
        'no_y' => array('C'), //不需要的列
    ),
    1 => array(
        'name' => 'exam_type',  //形成文件的名字
        'geshi'=> array('C','G'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '科举奖励', //文件中提示那个表
        'no_y' => array('B'), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------clothepve.xls-----------------
echo "\n clothepve有1个表:\n";
$filename = 'clothepve';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'clothepve',  //形成文件的名字
        'geshi'=> array('E'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '用户场景', //文件中提示那个表
        'no_y' => array('B','C','D'), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------chungeng.xls-----------------
echo "\n chungeng有1个表:\n";
$filename = 'chungeng';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'chungeng',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '用户场景', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------liondance.xls-----------------
echo "\n liondance有1个表:\n";
$filename = 'liondance';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'lion_task',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => 'reward', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------tree.xls-----------------
echo "\n tree有1个表:\n";
$filename = 'tree';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'tree_reward1',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => 'reward', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'tree_reward2',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => 'reward', //文件中提示那个表
        'no_y' => array('G'), //不需要的列
	),
	2 => array(
        'name' => 'tree_reward',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => 'reward', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------taofa.xls-----------------
echo "\n taofa有1个表:\n";
$filename = 'taofa';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'taofa',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '讨伐-关卡', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'taofa_monster',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '讨伐-怪物信息', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    2 => array(
        'name' => 'taofa_rwd',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '讨伐-随机奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------taofa.xls-----------------
echo "\n silkroad有1个表:\n";
$filename = 'silkroad';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'trade',  //形成文件的名字
        'geshi'=> array('E','F'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '丝绸之路-关卡', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'trade_rwd',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '丝绸之路-随机奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------guozijian.xls-----------------
echo "\n guozijian有4个表:\n";
$filename = 'guozijian';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'gzj_seat',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '国子监-席位', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'gzj_design',  //形成文件的名字
		'geshi'=> array('D','E'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '国子监-称号', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(
		'name' => 'gzj_daygift',  //形成文件的名字
		'geshi'=> array('C'), //将json转成array()
		'biao' => 2,   //第几张表,从0开始
		'tip'  => '国子监-每日礼包', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	3 => array(
		'name' => 'gzj_bribery',  //形成文件的名字
		'geshi'=> array('C','D'), //将json转成array()
		'biao' => 3,   //第几张表,从0开始
		'tip'  => '国子监-行贿', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	4 => array(
		'name' => 'gzj_jc',  //形成文件的名字
		'geshi'=> array('E'), //将json转成array()
		'biao' => 4,   //第几张表,从0开始
		'tip'  => '国子监-声望加成', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

echo "\n soncareer有2个表:\n";
$filename = 'soncareer';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'soncareer_expvalue',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => '子嗣升级经验', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	1 => array(
		'name' => 'soncareer_wifesetting',  //形成文件的名字
		'geshi'=> array(), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '子嗣妻妾盛鼎', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

//----------------------------------------------------
echo "\n practice有8个表:\n";
$filename = 'practice';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'practice_travel',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '出行', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'practice_luggage',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '行李', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    2 => array(
        'name' => 'practice_city',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '城市', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    3 => array(
        'name' => 'practice_score',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '品质得分', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    4 => array(
        'name' => 'practice_reward',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 4,   //第几张表,从0开始
        'tip'  => '得分', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    5 => array(
        'name' => 'practice_resume',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 5,   //第几张表,从0开始
        'tip'  => '', //文件中提示那个表
        'no_y' => array('E'), //不需要的列
    ),
    6 => array(
        'name' => 'practice_mail',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 6,   //第几张表,从0开始
        'tip'  => '徒弟信件', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    7 => array(
        'name' => 'practice_seat',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 7,   //第几张表,从0开始
        'tip'  => '席位', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

//-------------------七夕--------------------------------------
echo "\n practice有1个表:\n";
$filename = 'qixi';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'qixi_reward',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '档次', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),

);
std_data_output($filename,$getNum );

// -------------haoyou.xls-----------------
echo "\n haoyou有1个表:\n";
$filename = 'haoyou';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'qingyuan',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '好友-情缘', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'xuyuan',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '好友-许愿', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------card_pool.xls-----------------
echo "\n card有4个表:\n";
$filename = 'card';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'card',  //形成文件的名字
        'geshi'=> array('N'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '卡牌基础', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'card_pool',  //形成文件的名字
        'geshi'=> array('B','D'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '卡池表', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	2 => array(
        'name' => 'pool_items',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '卡池内物品表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
	3 => array(
        'name' => 'card_starup',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '卡牌升星表', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'B', //特殊结构主key
        'key2' => 'C', //特殊结构副key2
	),
	4 => array(
        'name' => 'card_lvlup',  //形成文件的名字
        'geshi'=> array('E'), //将json转成array()
        'biao' => 5,   //第几张表,从0开始
        'tip'  => '卡牌升级表', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'B', //特殊结构主key
        'key2' => 'C', //特殊结构副key2
	),
	5 => array(
        'name' => 'card_decompose',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 6,   //第几张表,从0开始
        'tip'  => '卡牌碎片分解表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	6 => array(
        'name' => 'card_yinhen',  //形成文件的名字
        'geshi'=> array('H','I','J','K','L','M'), //将json转成array()
        'biao' => 7,   //第几张表,从0开始
        'tip'  => '卡牌印痕升级表', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'C', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
	7 => array(
        'name' => 'card_flower',  //形成文件的名字
        'geshi'=> array('I'), //将json转成array()
        'biao' => 8,   //第几张表,从0开始
        'tip'  => '卡牌开花表', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'C', //特殊结构主key
        'key2' => 'B', //特殊结构副key2
	),
	8 => array(
        'name' => 'card_skill',  //形成文件的名字
        'geshi'=> array('B','G'), //将json转成array()
        'biao' => 9,   //第几张表,从0开始
        'tip'  => '卡牌羁绊', //文件中提示那个表
		'no_y' => array(), //不需要的列
    ),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 3 || $num == 4 || $num == 6 || $num == 7){
        $cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
        create_file($filename, $info['name'],$cfg_data,$info['tip']);
        continue;
    }
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	
	if(!empty($cfg_data))
	{
		echo "create_file".$info['name'];
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
	}else{
		echo $filename.$info['name']."fail";
	}
}

// -------------baowu_pool.xls-----------------
echo "\n baowu有4个表:\n";
$filename = 'baowu';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'baowu',  //形成文件的名字
        'geshi'=> array('N'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '四海奇珍基础', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'baowu_pool',  //形成文件的名字
        'geshi'=> array('B','D'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '四海奇珍表', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	2 => array(
        'name' => 'baowu_pool_items',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '四海奇珍奇珍内物品表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
	3 => array(
        'name' => 'baowu_starup',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '四海奇珍升星表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
foreach($getNum as $num => $info){
    echo ($num+1).": ";
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	
	if(!empty($cfg_data))
	{
		echo "create_file".$info['name'];
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
	}else{
		echo $filename.$info['name']."fail";
	}
}

// -------------card_pool.xls-----------------
echo "\n herodress 1:\n";
$filename = 'heroDress';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'hero_dress',  //形成文件的名字
        'geshi'=> array('K','M','O'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '英雄服装', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'hero_bg',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '伙伴空间背景', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	2 => array(
        'name' => 'hero_emojis',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '伙伴表情包', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------card_pool.xls-----------------
echo "\n heroShop 1:\n";
$filename = 'heroshop';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'hero_shop',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '伙伴商城', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------discount-----------------
echo "\n discount.xlsx 1:\n";
$filename = 'discount';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'discount',  //形成文件的名字
        'geshi'=> array('B','C'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '打折', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    
);
std_data_output($filename,$getNum );

// -------------clotheshop-----------------
echo "\n clotheshop.xlsx 1:\n";
$filename = 'clotheshop';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'clotheshop',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '服装商店', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    
);
std_data_output($filename,$getNum );

// -------------item.xls-----------------
echo "\nitem有2个表:\n";
$filename = 'item';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
		'name' => 'item',  //形成文件的名字
		'geshi'=> array('G'), //将json转成array()
		'biao' => 0,   //第几张表,从0开始
		'tip'  => 'item基础表', //文件中提示那个表
		'no_y' => array('D','E','F','H','J'), //不需要的列
	),
	1 => array(
		'name' => 'item_hc',  //形成文件的名字
		'geshi'=> array('C'), //将json转成array()
		'biao' => 1,   //第几张表,从0开始
		'tip'  => '道具合成表', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
// foreach($getNum as $num => $info){
// 	echo ($num+1).": ";
// 	if($num == 1){
// 		$cfg_data = get_item_hc_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
// 		create_file($filename, $info['name'],$cfg_data,$info['tip']);
// 		continue;
// 	}
// 	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
// 	create_file($filename, $info['name'],$cfg_data,$info['tip']);
// }
std_data_output($filename,$getNum );

// -------------iconopen.xls-------------
echo "\n\n\niconopen:\n";
$filename = 'iconopen';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'team_unlock',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '上阵位置解锁', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------user.xls-----------------
echo "\n user有1个表:\n";
$filename = 'user';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'user_back',  //形成文件的名字
        'geshi'=> array('G'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '用户场景', //文件中提示那个表
        'no_y' => array('B','E','I'), //不需要的列
    ),
    1 => array(
        'name' => 'use_clothe',  //形成文件的名字
        'geshi'=> array('H','K', 'N', 'V'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '用户换装', //文件中提示那个表
        'no_y' => array('D','G','I'), //不需要的列
	),
    2 => array(
        'name' => 'use_blank',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '用户换装', //文件中提示那个表
        'no_y' => array('C','D','F','G'), //不需要的列
    ),
    3 => array(
        'name' => 'use_head',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '用户换装', //文件中提示那个表
        'no_y' => array('B','C','D'), //不需要的列
    ),
    4 => array(
        'name' => 'clothe_suit',  //形成文件的名字
        'geshi'=> array('B','G'), //将json转成array()
        'biao' => 4,   //第几张表,从0开始
        'tip'  => '用户换装', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    5 => array(
        'name' => 'clothe_job',  //形成文件的名字
        'geshi'=> array('F','G'), //将json转成array()
        'biao' => 5,   //第几张表,从0开始
        'tip'  => '用户头像', //文件中提示那个表
        'no_y' => array('B', 'C','D','E'), //不需要的列
    ),
    6 => array(
        'name' => 'clothe_suit_prop',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 6,   //第几张表,从0开始
        'tip'  => '套装属性', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	7 => array(
        'name' => 'user_back_2',  //形成文件的名字
        'geshi'=> array('E'), //将json转成array()
        'biao' => 7,   //第几张表,从0开始
        'tip'  => '套装属性', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	8 => array(
        'name' => 'huafu',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 9,   //第几张表,从0开始
        'tip'  => '华服等级', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	9 => array(
        'name' => 'userSuitLv2',  //形成文件的名字
        'geshi'=> array('D','F'), //将json转成array()
        'biao' => 10,   //第几张表,从0开始
        'tip'  => '锦衣裁剪', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'B', //特殊结构主key
        'key2' => 'C', //特殊结构副key2
	),
	10 => array(
        'name' => 'cardSlot',  //形成文件的名字
        'geshi'=> array('B','C','D','E','F','G','H','J','K','L','M'), //将json转成array()
        'biao' => 11,   //第几张表,从0开始
        'tip'  => '卡牌槽位', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	11 => array(
        'name' => 'property',  //形成文件的名字
        'geshi'=> array('D','F','G'), //将json转成array()
        'biao' => 12,   //第几张表,从0开始
        'tip'  => '随机属性库', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 9){
		$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
    create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------xwup-----------------
echo "\n xwup.xlsx 2:\n";
$filename = 'xwup';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'tokenLvUp',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '信物升级', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'tokenFetters',  //形成文件的名字
        'geshi'=> array('D','E'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '信物羁绊', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    
);
std_data_output($filename,$getNum );

// -------------giftpack.xls-------------
echo "\n giftpack:\n";
$filename = 'giftpack';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'giftpack',  //形成文件的名字
        'geshi'=> array('D'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '通用奖励表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------sevendays-----------------
echo "\n sevendays.xlsx 2:\n";
$filename = 'sevendays';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'seven_sign',  //形成文件的名字
        'geshi'=> array('C','E'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '七日签到', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'seven_shop',  //形成文件的名字
        'geshi'=> array('E','F'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '七日超值购', //文件中提示那个表
        'no_y' => array('D'), //不需要的列
	),
	2 => array(
        'name' => 'seven_task',  //形成文件的名字
        'geshi'=> array('B','C'), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '七日任务', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    
);
std_data_output($filename,$getNum );

// -------------gongdou-----------------
echo "\n gongdou.xlsx 2:\n";
$filename = 'gongdou';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'gongdou_exchange',  //形成文件的名字
        'geshi'=> array('B','C'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '宫斗兑换', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'gongdou_rank',  //形成文件的名字
        'geshi'=> array('D'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '宫斗排行奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------cd-----------------
echo "\n cd.xlsx 2:\n";
$filename = 'cd';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'cd_consume',  //形成文件的名字
        'geshi'=> array('D'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '学院-历练 cd消耗', //文件中提示那个表
        'no_y' => array('A','B'), //不需要的列
        'key1' => 'B', //特殊结构主key
        'key2' => 'C', //特殊结构副key2
    ),
);
foreach($getNum as $num => $info){
    echo ($num+1).": ";
    $cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
    create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// -------------sevendays-----------------
// echo "\n mail_lang.xlsx 2:\n";
// $filename = 'mail_lang';  //名字
// $getNum = array(   //第一个表  从0开始  => 文件名字
// 	0 => array(
//         'name' => 'mail_lang',  //形成文件的名字
//         'geshi'=> array(), //将json转成array()
//         'biao' => 0,   //第几张表,从0开始
//         'tip'  => '邮件', //文件中提示那个表
//         'no_y' => array(), //不需要的列
// 	),
// );
// std_data_output_enum($filename,$getNum );

// -------------giftpack.xls-------------
echo "\n chuyou.xlsx 2:\n";
$filename = 'chuyou_event';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'chuyou_event',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '出游事件', //文件中提示那个表
        'no_y' => array(), //不需要的列
        'key1' => 'B', //特殊结构主key
        'key2' => 'C', //特殊结构副key2
    ),
);
foreach($getNum as $num => $info){
    echo ($num+1).": ";
    $cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
    if(!empty($cfg_data)){
        create_file($filename, $info['name'],$cfg_data,$info['tip']);    
    }
}

// -------------fuyue-----------------
echo "\n fuyue.xls 2:\n";
$filename = 'fuyue';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'fu_yue',  //形成文件的名字
        'geshi'=> array('B','C'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '赴约--战斗奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
    1 => array(
        'name' => 'zhu_ti',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '故事主题表', //文件中提示那个表
        'no_y' => array('B'), //不需要的列
    ),
    2 => array(
        'name' => 'zong_gu_shi',  //形成文件的名字
        'geshi'=> array('F','G','H','I'), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '故事随机', //文件中提示那个表
        'no_y' => array('B'), //不需要的列
        'key1' => 'D', //特殊结构主key
        'key2' => 'A', //特殊结构副key2
    ),
    3 => array(
        'name' => 'dui_huan',  //形成文件的名字
        'geshi'=> array('B','C','D'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '故事里的兑换商城', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	4 => array(
        'name' => 'ping_fen',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 5,   //第几张表,从0开始
        'tip'  => '计算完美度', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
foreach($getNum as $num => $info){
    echo ($num+1).": ";
    if($num == 2){
        $cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
        create_file($filename, $info['name'],$cfg_data,$info['tip']);
        continue;
    }
    $cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
    create_file($filename, $info['name'],$cfg_data,$info['tip']);
}

// -------------gushi-----------------
echo "\n gushi.xls 2:\n";
$filename = 'gushi';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'gushi',  //形成文件的名字
        'geshi'=> array('AB'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '赴约故事表', //文件中提示那个表
        'no_y' => array('B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y'), //不需要的列
    ),
);
std_data_output($filename,$getNum );


// -------------magnate-----------------
echo "\n magnate.xls 2:\n";
$filename = 'magnate';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'magnate_lv',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '贵人令-等级', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'magnate_task',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '贵人令-任务', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	2 => array(
        'name' => 'magnate_param',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '贵人令-参数', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	3 => array(
        'name' => 'magnate_rwd',  //形成文件的名字
        'geshi'=> array('C','F'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '贵人令-奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------magnate-----------------
echo "\n magnate_new.xls 2:\n";
$filename = 'magnate_new';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
	0 => array(
        'name' => 'magnate_new_lv',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '贵人令-等级', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'magnate_new_task',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '贵人令-任务', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	2 => array(
        'name' => 'magnate_new_param',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '贵人令-参数', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	3 => array(
        'name' => 'magnate_new_rwd',  //形成文件的名字
        'geshi'=> array('C','F'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '贵人令-奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
);
std_data_output($filename,$getNum );

// -------------xingshang-----------------
echo "\n xingshang.xls 2:\n";
$filename = 'xingshang';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'xs_chengshi',  //形成文件的名字
        'geshi'=> array('F','G','H','I','J'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '行商-城市表', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'xs_shangpiao',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '行商-商票表', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	2 => array(
        'name' => 'xs_jiangli',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '行商-奖励表', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	3 => array(
        'name' => 'xs_wupin',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '行商-物品表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------xingshang-----------------
echo "\n banchai.xls 2:\n";
$filename = 'banchai';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'bc_juqing',  //形成文件的名字
        'geshi'=> array('G','K','AB','AC','AD','AE'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '办差-剧情表', //文件中提示那个表
		'no_y' => array('E','F','M','N','O','U','V'), //不需要的列
		'key1' => 'C', //特殊结构主key
        'key2' => 'A', //特殊结构副key2
	),
	1 => array(
        'name' => 'bc_juqing_id',  //形成文件的名字
        'geshi'=> array('G','K'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '办差-剧情表', //文件中提示那个表
		'no_y' => array('E','F','M','N','O','U','V'), //不需要的列
		'key1' => 'C', //特殊结构主key
        'key2' => 'A', //特殊结构副key2
	),
	2 => array(
        'name' => 'bc_jiangli',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '办差-奖励表', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	3 => array(
        'name' => 'bc_jiesuo',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '办差-解锁表', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	4 => array(
        'name' => 'bc_jieju',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '办差-结局表', //文件中提示那个表
        'no_y' => array('C','D'), //不需要的列
	),
	5 => array(
        'name' => 'bc_cost',  //形成文件的名字
        'geshi'=> array('B'), //将json转成array()
        'biao' => 4,   //第几张表,从0开始
        'tip'  => '办差-结局表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 0){
		$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}else{
		$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
	}	
}
// std_data_output($filename,$getNum );

// -------------tanhe-----------------
echo "\n tanhe.xls 2:\n";
$filename = 'tanhe';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'tanhe',  //形成文件的名字
        'geshi'=> array('E','F','G','H','L','M'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '弹劾', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------game_visit-----------------
echo "\n game_visit.xls 2:\n";
$filename = 'game_visit';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'game_question',  //形成文件的名字
        'geshi'=> array('E'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '题库', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'visit_cost',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '消耗', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	2 => array(
        'name' => 'visit_rwd',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '答题奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
std_data_output($filename,$getNum );

// -------------game_visit-----------------
echo "\n games.xls 2:\n";
$filename = 'games';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'games',  //形成文件的名字
        'geshi'=> array('H','K','L','M'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '事件类型', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	1 => array(
        'name' => 'game_rwd',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '饮食垂钓积分奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'B',
		'key2' => 'A',
	),
	2 => array(
        'name' => 'game_item',  //形成文件的名字
        'geshi'=> array('J'), //将json转成array()
        'biao' => 4,   //第几张表,从0开始
        'tip'  => '饮食垂钓道具', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	3 => array(
        'name' => 'collection_rwd',  //形成文件的名字
        'geshi'=> array('D'), //将json转成array()
        'biao' => 5,   //第几张表,从0开始
        'tip'  => '收集奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'E',
		'key2' => 'B',
	),
	4 => array(
        'name' => 'max_rwd',  //形成文件的名字
        'geshi'=> array('E'), //将json转成array()
        'biao' => 6,   //第几张表,从0开始
        'tip'  => '最大奖励', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
	5 => array(
        'name' => 'collection_achieve',  //形成文件的名字
        'geshi'=> array('E','F'), //将json转成array()
        'biao' => 7,   //第几张表,从0开始
        'tip'  => '收集成就', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	6 => array(
        'name' => 'collection_rwdname',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 8,   //第几张表,从0开始
        'tip'  => '成就总览表', //文件中提示那个表
        'no_y' => array(), //不需要的列
	),
	7 => array(
        'name' => 'yuer',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 9,   //第几张表,从0开始
        'tip'  => '鱼饵表', //文件中提示那个表
        'no_y' => array(), //不需要的列
    ),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 1 || $num == 3){
		$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}
	$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
	create_file($filename, $info['name'],$cfg_data,$info['tip']);
}
// std_data_output($filename,$getNum );

// -------------jiaoyou-----------------
echo "\n jiaoyou.xls 2:\n";
$filename = 'jiaoyou';  //名字
$getNum = array(   //第一个表  从0开始  => 文件名字
    0 => array(
        'name' => 'jiaoyou',  //形成文件的名字
        'geshi'=> array('I','J','K','L','P','X'), //将json转成array()
        'biao' => 0,   //第几张表,从0开始
        'tip'  => '郊游-战斗', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'B', //特殊结构主key
        'key2' => 'D', //特殊结构副key2
	),
	1 => array(
        'name' => 'jiaoyou_star',  //形成文件的名字
        'geshi'=> array(), //将json转成array()
        'biao' => 1,   //第几张表,从0开始
        'tip'  => '郊游-星级', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
	2 => array(
        'name' => 'jiaoyou_guaji',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 2,   //第几张表,从0开始
        'tip'  => '郊游-获得道具', //文件中提示那个表
		'no_y' => array(), //不需要的列
		'key1' => 'B', //特殊结构主key
        'key2' => 'A', //特殊结构副key2
	),
	3 => array(
        'name' => 'jiaoyou_week',  //形成文件的名字
        'geshi'=> array('C'), //将json转成array()
        'biao' => 3,   //第几张表,从0开始
        'tip'  => '郊游-每周守护次数奖励', //文件中提示那个表
		'no_y' => array(), //不需要的列
	),
);
foreach($getNum as $num => $info){
	echo ($num+1).": ";
	if($num == 0 || $num == 2){
		$cfg_data = get_currency_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y'],$info['key1'],$info['key2']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
		continue;
	}else{
		$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
		create_file($filename, $info['name'],$cfg_data,$info['tip']);
	}	
}

exit();

//特殊配置表格解析
function std_data_output_enum($filename,$getNum){

	foreach($getNum as $num => $info){
		echo ($num+1).": ";
		$cfg_data = get_excel_data_enum($filename,$info['biao'],$info['geshi'],$info['no_y']);
		if(!empty($cfg_data))
		{
			create_file_enum($filename, $info['name'],$cfg_data,$info['tip']);
		}
		
	}
}

/*
 * 通用函数
 * 取excel表   第一列做下标 第2行为数据结构    第3行用作字段
 */
function get_excel_data_enum($filename,$getNum,$geshi,$no_y){
	//echo "a1\n";
	$filePath = get_project_conf($filename).$filename.'.xls';
	$arr = array();
	$temparr = array();
	$PHPExcel = new PHPExcel();
	if (!file_exists($filePath)) {
		 echo $filePath."\n";
		 echo $filename."_read_error\n";
		 return false;
	}
	$PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
	$PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
	$currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
	$allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
	$allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
	$end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
	$data = array();
	//echo "a2\n";
	for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
	{
		//echo "r".$currentRow."\n";
		//获取第一列
		$columnA = $currentSheet->getCell('A'.$currentRow)->getCalculatedValue();
		$columnB = $currentSheet->getCell('B'.$currentRow)->getCalculatedValue();
		$data[$columnA] = $columnB;
	}
	//echo "a3\n";
	if(empty($data)){
		echo $filename."_error\n";
		exit();
	}
	return $data;
}

//通用标准表格解析
function std_data_output($filename,$getNum){

	foreach($getNum as $num => $info){
		echo ($num+1).": ";
		$cfg_data = get_excel_data($filename,$info['biao'],$info['geshi'],$info['no_y']);
		if(!empty($cfg_data))
		{
			create_file($filename, $info['name'],$cfg_data,$info['tip']);
		}
		
	}
}
/*
 * 通用函数
 * 取excel表   第一列做下标 第2行为数据结构    第3行用作字段
 */
function get_excel_data($filename,$getNum,$geshi,$no_y){
	//echo "a1\n";
	$filePath = get_project_conf($filename).$filename.'.xls';
	$arr = array();
	$temparr = array();
	$PHPExcel = new PHPExcel();
	if (!file_exists($filePath)) {
		 echo $filePath."\n";
		 echo $filename."_read_error\n";
		 return false;
	}
	$PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
	$PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
	$currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
	$allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
	$allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
	$end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
	$data = array();
	//echo "a2\n";
	for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
	{
		//echo "r".$currentRow."\n";
		//获取第一列
	    $column = $currentSheet->getCell('A'.$currentRow)->getCalculatedValue();
	    for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
	    	//过滤第 1 , 2 ,3 行  ;
	    	if($currentRow <= 3 ){
		    	continue;
			}
			//echo "c".$currentColumn."\n";
		    //由列数反转列名(0->'A')
			$col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
			//echo "d"."\n";
			//获取第二行
			if(!empty($no_y) && in_array($col_name,$no_y)){
				continue;
			}
			//echo "e"."\n";
			$row = $currentSheet->getCell($col_name.'2')->getCalculatedValue();
			//echo "f"."\n";
			$type = $currentSheet->getCell($col_name.'3')->getCalculatedValue();
			//echo "t".$col_name.$currentRow."\n";
			//$zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
			$currentcell = $currentSheet->getCell($col_name.$currentRow);

			$zhi = $currentcell->getValue();
			if ($type =='string'){
				$zhi = $currentcell->getCalculatedValue();
			}else{
				if(strstr($zhi,'=')==true)
				{
					$zhi = $currentcell->getOldCalculatedValue();
				}
			}
			
			//echo "z".$zhi."\n";
	    	if((!empty($geshi) && in_array($col_name,$geshi)) || $type =='array'){
	    		$zhi = json_decode($zhi,1);
	    	}
	    	if(is_numeric($zhi)){
				if ($type =='float'){
					$zhi = floatval($zhi);
				}else{
					$zhi = intval($zhi);
				}
	    		
	    	}
	    	if($row == '' || (!is_numeric($column) && $column == '')){
	    		break;
	    	}
	        $data[$column][$row] = $zhi;

	    }
	}
	//echo "a3\n";
	if(empty($data)){
		echo $filename."_error\n";
		exit();
	}
	return $data;
}

/*
 * base.xls
 * $filename  :  形成文件的名字
 * $getNum  : 读取excel文件中的第几张工作表  下标从0开始
 * $geshi  :  哪一列 需要 json 转 array()  从 A 开始
 */
function get_base_excel_data($filename,$getNum,$geshi,$no_y){

	$filePath = get_project_conf($filename).$filename.'.xls';
	$arr = array();
	$temparr = array();
	$PHPExcel = new PHPExcel();
	if (!file_exists($filePath)) {
		 echo $filename."_read_error\n";
		 return false;
	}

	$PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
	$PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
	$currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
	$allColumn = $currentSheet->getHighestColumn();
	$end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);
	$allRow = $currentSheet->getHighestRow();

	$data = array();

	for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ ){
		$column = $currentSheet->getCell('A'.$currentRow)->getCalculatedValue();
		for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
			if($currentRow <= 3 ){
		    	continue;
			}
			$col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
			if(!empty($no_y) && in_array($col_name,$no_y)){
				continue;
			}
			$row = $currentSheet->getCell($col_name.'2')->getCalculatedValue();
			$zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
			if(!empty($geshi) && in_array($col_name,$geshi)){
	    		$zhi = json_decode($zhi,1);
			}
			if(is_numeric($zhi)){
				$zhi = intval($zhi);
			}
			$data[$row] = $zhi;
		}
	}
	// $point = array(2,3,4,5,7,9,12,14,16);
	// foreach($point as $pt ){
	// 	$row = $currentSheet->getCell('E'.$pt)->getCalculatedValue();
	// 	$zhi = $currentSheet->getCell('D'.$pt)->getCalculatedValue();
		
	// 	if($pt == 9){
	// 		$zhi = str_replace('[','',$zhi);
	// 		$zhi = str_replace(']','',$zhi);
	// 		$zhi = explode(',',$zhi);
	// 	}
	// 	if(empty($row)){
    // 		break;
    // 	}
	// 	if(is_numeric($zhi)){
    // 		$zhi = intval($zhi);
    // 	}
	// 	$data[$row] = $zhi;
	// }
	if(empty($data)){
		echo $filename."_error\n";
		exit();
	}
	return $data;
}


/*
 * wife_skill_id
 * 红颜拥有的技能列表
 */
function get_wife_skill_id_data($filename,$getNum,$geshi){
	$filePath = get_project_conf($filename).$filename.'.xls';
	$arr = array();
	$temparr = array();
	$PHPExcel = new PHPExcel();
	if (!file_exists($filePath)) {
		 echo $filename."_read_error\n";
		 return false;
	}
	$PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
	$PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
	$currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
	$allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
	$allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
	$end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
	$data = array();
	for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
	{
		if($currentRow <= 3 ){
	    	continue;
	    }
		$row = $currentSheet->getCell('B'.$currentRow)->getCalculatedValue();
	    $zhi = $currentSheet->getCell('A'.$currentRow)->getCalculatedValue();
		if(empty($row)){
    		break;
    	}
		if(is_numeric($zhi)){
    		$zhi = intval($zhi);
    	}
	    $data[$row][] = $zhi;
	}

	if(empty($data)){
		echo $filename."_error\n";
		exit();
	}
	return $data;
}

/*
 * item_hc
 * 取excel表   第一列做下标    第二行用作字段
 */
function get_item_hc_excel_data($filename,$getNum,$geshi,$no_y){
	$filePath = get_project_conf($filename).$filename.'.xls';
	$arr = array();
	$temparr = array();
	$PHPExcel = new PHPExcel();
	if (!file_exists($filePath)) {
		 echo $filename."_read_error\n";
		 return false;
	}
	$PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
	$PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
	$currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
	$allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
	$allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
	$end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
	$data = array();
	for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
	{
		//获取第一列
	    $column = $currentSheet->getCell('B'.$currentRow)->getCalculatedValue();
	    for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
	    	//过滤第 1 , 2 行  ;
	    	if($currentRow <= 2 ){
		    	continue;
		    }
		    //由列数反转列名(0->'A')
			$col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
			//获取第二行
			if(!empty($no_y) && in_array($col_name,$no_y)){
				continue;
			}
			$row = $currentSheet->getCell($col_name.'2')->getCalculatedValue();
	    	$zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
	    	if(!empty($geshi) && in_array($col_name,$geshi)){
	    		$zhi = json_decode($zhi,1);
	    	}
	    	if(is_numeric($zhi)){
	    		$zhi = intval($zhi);
	    	}
	    	if($row == '' || (!is_numeric($column) && $column == '')){
	    		break;
	    	}
	        $data[$column][$row] = $zhi;

	    }
	}
	if(empty($data)){
		echo $filename."_error\n";
		exit();
	}
	return $data;
}

function get_currency_excel_data($filename,$getNum,$geshi,$no_y,$key1,$key2){
    $filePath = get_project_conf($filename).$filename.'.xls';
    $arr = array();
    $temparr = array();
    $PHPExcel = new PHPExcel();
    if (!file_exists($filePath)) {
         echo $filename."_read_error\n";
         return false;
    }
    $PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
    $PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
    $currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
    $allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
    $allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
    $end_index  = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
	$data = array();
    for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
    {
        //获取第一列
		$column = $currentSheet->getCell($key1.$currentRow)->getCalculatedValue();
		$column2 = $currentSheet->getCell($key2.$currentRow)->getCalculatedValue();
        for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
            //过滤第 1 , 2 行  ;
            if($currentRow <= 3 ){
                continue;
            }
            //由列数反转列名(0->'A')
            $col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
            //获取第二行
            if(!empty($no_y) && in_array($col_name,$no_y)){
                continue;
			}
            $row = $currentSheet->getCell($col_name.'2')->getCalculatedValue();
            $zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
            $type = $currentSheet->getCell($col_name.'3')->getCalculatedValue();
            if(!empty($geshi) && in_array($col_name,$geshi)){
                $zhi = json_decode($zhi,1);
            }
            if(is_numeric($zhi)){
                if(intval($zhi) != $zhi && $type == 'float'){
                    $zhi = floatval($zhi);
                }else {
                    $zhi = intval($zhi);
                }
			}
            if($row == '' || (!is_numeric($column) && $column == '')){
                break;
			}
			$data[$column][$column2][$row] = $zhi;
		}
	}
	
    if(empty($data)){
        echo $filename."_error\n";
        exit();
	}
    return $data;
}

/*
 * cj_info
 * 取excel表   第一列做下标    第二列做二级下标
 */
function get_cj_info_excel_data($filename,$getNum,$geshi,$no_y){
	$filePath = get_project_conf($filename).$filename.'.xls';
	$arr = array();
	$temparr = array();
	$PHPExcel = new PHPExcel();
	if (!file_exists($filePath)) {
		 echo $filename."_read_error\n";
		 return false;
	}
	$PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
	$PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
	$currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
	$allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
	$allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
	$end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
	$data = array();
	for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
	{
		//获取第一列
	    $column = $currentSheet->getCell('E'.$currentRow)->getCalculatedValue();
	    $column2 = $currentSheet->getCell('B'.$currentRow)->getCalculatedValue();
	    for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
	    	//过滤第 1 , 2 行  ;
	    	if($currentRow <= 3 ){
		    	continue;
		    }
		    //由列数反转列名(0->'A')
			$col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
			//获取第二行
			if(!empty($no_y) && in_array($col_name,$no_y)){
				continue;
			}
			$row = $currentSheet->getCell($col_name.'2')->getCalculatedValue();
			$zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
			$type = $currentSheet->getCell($col_name.'3')->getCalculatedValue();
	    	if(!empty($geshi) && in_array($col_name,$geshi)){
	    		$zhi = json_decode($zhi,1);
	    	}
	    	if(is_numeric($zhi)){
				if(intval($zhi) != $zhi && $type == 'float'){
					$zhi = floatval($zhi);
				}else {
					$zhi = intval($zhi);
				}
	    	}
	    	if($row == '' || (!is_numeric($column) && $column == '')){
	    		break;
	    	}
	        $data[$column][$column2][$row] = $zhi;

	    }
	}
	if(empty($data)){
		echo $filename."_error\n";
		exit();
	}
	return $data;
}

function get_order_excel_data($filename,$getNum,$geshi,$no_y){
	$filePath = get_project_conf($filename).$filename.'.xls';
	$arr = array();
	$temparr = array();
	$PHPExcel = new PHPExcel();
	if (!file_exists($filePath)) {
		 echo $filename."_read_error\n";
		 return false;
	}
	$PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
	$PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
	$currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
	$allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
	$allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
	$end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
	$data = array();
	for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
	{
		//获取第一列
	    $column = $currentSheet->getCell('A'.$currentRow)->getCalculatedValue();
	    $column2 = $currentSheet->getCell('B'.$currentRow)->getCalculatedValue();
	    for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
	    	//过滤第 1 , 2 行  ;
	    	if($currentRow <= 3 ){
		    	continue;
		    }
		    //由列数反转列名(0->'A')
			$col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
			//获取第二行
			if(!empty($no_y) && in_array($col_name,$no_y)){
				continue;
			}
			$row = $currentSheet->getCell($col_name.'2')->getCalculatedValue();
			$zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
			$type = $currentSheet->getCell($col_name.'3')->getCalculatedValue();
	    	if(!empty($geshi) && in_array($col_name,$geshi)){
	    		$zhi = json_decode($zhi,1);
	    	}
	    	if(is_numeric($zhi)){
				if(intval($zhi) != $zhi && $type == 'float'){
					$zhi = floatval($zhi);
				}else {
					$zhi = intval($zhi);
				}
	    	}
	    	if($row == '' || (!is_numeric($column) && $column == '')){
	    		break;
	    	}
	        $data[$column][$column2][$row] = $zhi;

	    }
	}
	if(empty($data)){
		echo $filename."_error\n";
		exit();
	}
	return $data;
}

function get_jyWeiPai_excel_data($filename,$getNum,$geshi,$no_y){
	$filePath = get_project_conf($filename).$filename.'.xls';
	$arr = array();
	$temparr = array();
	$PHPExcel = new PHPExcel();
	if (!file_exists($filePath)) {
		 echo $filename."_read_error\n";
		 return false;
	}
	$PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
	$PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
	$currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
	$allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
	$allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
	$end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
	$data = array();
	for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
	{
		//获取第一列
	    $column = $currentSheet->getCell('B'.$currentRow)->getCalculatedValue();
	    $column2 = $currentSheet->getCell('C'.$currentRow)->getCalculatedValue();
	    for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
	    	//过滤第 1 , 2 行  ;
	    	if($currentRow <= 3 ){
		    	continue;
		    }
		    //由列数反转列名(0->'A')
			$col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
			//获取第二行
			if(!empty($no_y) && in_array($col_name,$no_y)){
				continue;
			}
			$row = $currentSheet->getCell($col_name.'2')->getCalculatedValue();
			$zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
			$type = $currentSheet->getCell($col_name.'3')->getCalculatedValue();
	    	if(!empty($geshi) && in_array($col_name,$geshi)){
	    		$zhi = json_decode($zhi,1);
	    	}
	    	if(is_numeric($zhi)){
				if(intval($zhi) != $zhi && $type == 'float'){
					$zhi = floatval($zhi);
				}else {
					$zhi = intval($zhi);
				}
	    	}
	    	if($row == '' || (!is_numeric($column) && $column == '')){
	    		break;
	    	}
	        $data[$column][$column2][$row] = $zhi;

	    }
	}
	if(empty($data)){
		echo $filename."_error\n";
		exit();
	}
	return $data;
}

function get_cd_consume_excel_data($filename,$getNum,$geshi,$no_y){
    $filePath = get_project_conf($filename).$filename.'.xls';
    $arr = array();
    $temparr = array();
    $PHPExcel = new PHPExcel();
    if (!file_exists($filePath)) {
         echo $filename."_read_error\n";
         return false;
    }
    $PHPReader = new PHPExcel_Reader_Excel5();//创建一个excel文件的读取对象
    $PHPExcel = $PHPReader->load($filePath);//读取一张excel表,返回excel文件对象
    $currentSheet = $PHPExcel->getSheet($getNum);//读取excel文件中的第一张工作表
    $allColumn = $currentSheet->getHighestColumn();//取得当前工作表最大的列号,如,E
    $allRow = $currentSheet->getHighestRow();//取得当前工作表一共有多少行
    $end_index     = PHPExcel_Cell::columnIndexFromString($allColumn);//由列名转为列数('AB'->28)
    $data = array();
    for($currentRow = 1 ; $currentRow <= $allRow ; $currentRow++ )
    {
        //获取第一列
        $column = $currentSheet->getCell('B'.$currentRow)->getCalculatedValue();
        $column2 = $currentSheet->getCell('C'.$currentRow)->getCalculatedValue();
        for($currentColumn=0;$currentColumn < $end_index;$currentColumn++){
            //过滤第 1 , 2 行  ;
            if($currentRow <= 3 ){
                continue;
            }
            //由列数反转列名(0->'A')
            $col_name = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
            //获取第二行
            if(!empty($no_y) && in_array($col_name,$no_y)){
                continue;
            }
            $row = $currentSheet->getCell($col_name.'2')->getCalculatedValue();
            $zhi = $currentSheet->getCell($col_name.$currentRow)->getCalculatedValue();
            $type = $currentSheet->getCell($col_name.'3')->getCalculatedValue();
            if(!empty($geshi) && in_array($col_name,$geshi)){
                $zhi = json_decode($zhi,1);
            }
            if(is_numeric($zhi)){
                if(intval($zhi) != $zhi && $type == 'float'){
                    $zhi = floatval($zhi);
                }else {
                    $zhi = intval($zhi);
                }
            }
            if($row == '' || (!is_numeric($column) && $column == '')){
                break;
            }
            $data[$column][$column2][$row] = $zhi;

        }
    }
    if(empty($data)){
        echo $filename."_error\n";
        exit();
    }
    return $data;
}


/*
 * 通用获取按ID排列的配置
 */
function create_file($fromFileName, $filename,$cfg_data,$tip = ''){

	$require_file = CONFIG_DIR . '/'.get_game_conf_dir($fromFileName).'/' . $filename . '.php';//需要包含的文件
	$file = fopen($require_file,'w');
	fwrite($file, "<?php\n//".$tip."\nreturn " . var_export($cfg_data,1) . ';');
	@chmod($require_file,0777);
	fclose($file);

	echo $filename."_success\n";
    return true;
}

function create_file_enum($fromFileName, $filename,$cfg_data,$tip = ''){

	$require_file = CONFIG_DIR . '/../administrator/'.get_game_conf_dir_enum($fromFileName).'/' . $filename . '.php';//需要包含的文件
	$file = fopen($require_file,'w');
	fwrite($file, "<?php\n//".$tip."\nreturn " . var_export($cfg_data,1) . ';');
	@chmod($require_file,0777);
	fclose($file);

	echo $filename."_success\n";
    return true;
}
