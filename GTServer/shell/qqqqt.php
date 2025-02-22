<?php
//4皮数值 改为1皮数值
set_time_limit(6000);
ini_set('memory_limit','4000M');
require_once dirname(__FILE__) . '/../public/common.inc.php';
//$serverid = 4;
//$serverid = 1;
//$serverid = 2;
//$serverid = 3;
//$serverid = 5;
//$serverid = 6;
//$serverid = 8;
//$serverid = 7;
$serverid = 999;
exit;

$SevidCfg = Common::getSevidCfg($serverid);//子服ID


$db = Common::getDbBySevId($SevidCfg['sevid']);

$table_div = Common::get_table_div($SevidCfg['sevid']);

/*
//获取两份配置文件 进行对比

//define('GAME_SKIN', false);
//原来门客配置
$hero_cfg = getcfg('hero');
//原来技能配置
$hero_epskill_cfg = getcfg('hero_epskill');

//ID 不一样 //星级不一样
//print_r($hero_cfg);
//print_r($hero_epskill_cfg);

define('GAME_SKIN', 4);
$hero_cfg_4 = getcfg('hero');
//原来技能配置
$hero_epskill_cfg_4 = getcfg('hero_epskill');

//构建修改配置表
$cfg_data = array();

//遍历门客
foreach ($hero_cfg as $k_hero => $v_hero){
	echo $v_hero['heroid'].'	'.$v_hero['name']."\n";
	//遍历技能
	foreach ($v_hero['skills'] as $k_skill => $v_skill){
		//匹配技能ID
		if(!isset($hero_cfg_4[$k_hero]['skills'][$k_skill])){
			echo '删除技能ID :'.$v_skill['id']."\n";
			//构造配置
			$cfg_data[$v_hero['heroid']][$v_skill['id']] = array(
				'id' => 0,
			);
		}elseif ($v_skill['id'] != $hero_cfg_4[$k_hero]['skills'][$k_skill]['id']){
			//输出ID不一样的
			echo '改技能ID :'.$v_skill['id'].$hero_epskill_cfg[$v_skill['id']]['name'].' => '.$hero_cfg_4[$k_hero]['skills'][$k_skill]['id'].$hero_epskill_cfg_4[$hero_cfg_4[$k_hero]['skills'][$k_skill]['id']]['name']." ";
			
			
			//这些ID不一样的 是不是等级都不一样
			$lv = $hero_epskill_cfg[$v_skill['id']]['star'];
			//获取改技能的新等级
			$lv_4 = $hero_epskill_cfg_4[$hero_cfg_4[$k_hero]['skills'][$k_skill]['id']]['star'];
			if ($lv != $lv_4){
				//输出 ID一样 等级不一样的技能
				echo '	星级改变 :'.$lv.'星->'.$lv_4."星\n";
			}else{
				echo "一样----\n";
			}
			//构造配置
			$cfg_data[$v_hero['heroid']][$v_skill['id']] = array(
				'id' => $hero_cfg_4[$k_hero]['skills'][$k_skill]['id'],
				'start1' => $lv,
				'start2' => $lv_4,
			);
		}else{
			//是否有ID一样 等级不一样的
			//获取该技能的等级
			$lv = $hero_epskill_cfg[$v_skill['id']]['star'];
			//获取改技能的新等级
			$lv_4 = $hero_epskill_cfg_4[$v_skill['id']]['star'];
			if ($lv != $lv_4){
				//输出 ID一样 等级不一样的技能
				echo '技能星级被改 :'.$v_skill['id'].$hero_epskill_cfg[$v_skill['id']]['name'].'('.$hero_epskill_cfg[$v_skill['id']]['star'].'星)->'.$hero_epskill_cfg_4[$v_skill['id']]['name'].'('.$hero_epskill_cfg_4[$v_skill['id']]['star'].'星)'."\n";
				
				
				//构造配置
				$cfg_data[$v_hero['heroid']][$v_skill['id']] = array(
					'id' => $v_skill['id'],
					'start1' => $lv,
					'start2' => $lv_4,
				);
			}
		}
		//输出等级不一样的
		
		
		//计算出技能等级
	}
	echo "\n";
}
*/

//$cfg_data = array();
/*
array(
	//门客ID
	45 => array( //被改技能ID列表
		14 => array(
			'id' => 1,//目标技能ID / 0 则被删除
			'start1' => 1,//原技能星级
			'start2' => 1,//目标技能星级
		);
	);
);
*/

$cfg_data = array (
  1 => 
  array (
    3 => 
    array (
      'id' => 2,
      'start1' => 3,
      'start2' => 2,
    ),
    8 => 
    array (
      'id' => 7,
      'start1' => 3,
      'start2' => 2,
    ),
    13 => 
    array (
      'id' => 12,
      'start1' => 3,
      'start2' => 2,
    ),
    18 => 
    array (
      'id' => 17,
      'start1' => 3,
      'start2' => 2,
    ),
  ),
  2 => 
  array (
    8 => 
    array (
      'id' => 6,
      'start1' => 3,
      'start2' => 1,
    ),
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  3 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
    13 => 
    array (
      'id' => 12,
      'start1' => 3,
      'start2' => 2,
    ),
  ),
  4 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
    17 => 
    array (
      'id' => 16,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  5 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  6 => 
  array (
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  7 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  8 => 
  array (
    17 => 
    array (
      'id' => 16,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  9 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  10 => 
  array (
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  11 => 
  array (
    7 => 
    array (
      'id' => 6,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  12 => 
  array (
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  13 => 
  array (
    17 => 
    array (
      'id' => 16,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  14 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  15 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  16 => 
  array (
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  17 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  18 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  19 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  20 => 
  array (
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  21 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  22 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  23 => 
  array (
    2 => 
    array (
      'id' => 4,
      'start1' => 2,
      'start2' => 4,
    ),
    9 => 
    array (
      'id' => 7,
      'start1' => 4,
      'start2' => 2,
    ),
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  24 => 
  array (
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  25 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  26 => 
  array (
    17 => 
    array (
      'id' => 16,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  27 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
    3 => 
    array (
      'id' => 0,
    ),
  ),
  28 => 
  array (
    47 => 
    array (
      'id' => 47,
      'start1' => 5,
      'start2' => 6,
    ),
    48 => 
    array (
      'id' => 48,
      'start1' => 5,
      'start2' => 4,
    ),
    4 => 
    array (
      'id' => 0,
    ),
    12 => 
    array (
      'id' => 11,
      'start1' => 4,
      'start2' => 1,
    ),
  ),
  29 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
    49 => 
    array (
      'id' => 49,
      'start1' => 5,
      'start2' => 6,
    ),
    50 => 
    array (
      'id' => 50,
      'start1' => 5,
      'start2' => 4,
    ),
  ),
  30 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
    51 => 
    array (
      'id' => 51,
      'start1' => 5,
      'start2' => 6,
    ),
    52 => 
    array (
      'id' => 52,
      'start1' => 5,
      'start2' => 4,
    ),
  ),
  31 => 
  array (
    2 => 
    array (
      'id' => 1,
      'start1' => 2,
      'start2' => 1,
    ),
    53 => 
    array (
      'id' => 53,
      'start1' => 5,
      'start2' => 6,
    ),
    54 => 
    array (
      'id' => 54,
      'start1' => 5,
      'start2' => 4,
    ),
  ),
  32 => 
  array (
    56 => 
    array (
      'id' => 56,
      'start1' => 5,
      'start2' => 4,
    ),
  ),
  33 => 
  array (
    57 => 
    array (
      'id' => 57,
      'start1' => 5,
      'start2' => 4,
    ),
  ),
  34 => 
  array (
    58 => 
    array (
      'id' => 58,
      'start1' => 5,
      'start2' => 3,
    ),
  ),
  35 => 
  array (
    59 => 
    array (
      'id' => 59,
      'start1' => 5,
      'start2' => 3,
    ),
  ),
  36 => 
  array (
    60 => 
    array (
      'id' => 60,
      'start1' => 5,
      'start2' => 3,
    ),
  ),
  37 => 
  array (
    61 => 
    array (
      'id' => 61,
      'start1' => 5,
      'start2' => 3,
    ),
  ),
  38 => 
  array (
    4 => 
    array (
      'id' => 3,
      'start1' => 4,
      'start2' => 3,
    ),
  ),
  39 => 
  array (
    4 => 
    array (
      'id' => 3,
      'start1' => 4,
      'start2' => 3,
    ),
    7 => 
    array (
      'id' => 6,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  40 => 
  array (
    4 => 
    array (
      'id' => 3,
      'start1' => 4,
      'start2' => 3,
    ),
    8 => 
    array (
      'id' => 7,
      'start1' => 3,
      'start2' => 2,
    ),
  ),
  41 => 
  array (
    4 => 
    array (
      'id' => 3,
      'start1' => 4,
      'start2' => 3,
    ),
    8 => 
    array (
      'id' => 6,
      'start1' => 3,
      'start2' => 1,
    ),
  ),
  42 => 
  array (
    3 => 
    array (
      'id' => 2,
      'start1' => 3,
      'start2' => 2,
    ),
  ),
  43 => 
  array (
    7 => 
    array (
      'id' => 6,
      'start1' => 2,
      'start2' => 1,
    ),
    12 => 
    array (
      'id' => 11,
      'start1' => 2,
      'start2' => 1,
    ),
    17 => 
    array (
      'id' => 16,
      'start1' => 2,
      'start2' => 1,
    ),
  ),
  44 => 
  array (
    4 => 
    array (
      'id' => 3,
      'start1' => 4,
      'start2' => 3,
    ),
    19 => 
    array (
      'id' => 18,
      'start1' => 4,
      'start2' => 3,
    ),
  ),
  45 => 
  array (
    9 => 
    array (
      'id' => 8,
      'start1' => 4,
      'start2' => 3,
    ),
    14 => 
    array (
      'id' => 13,
      'start1' => 4,
      'start2' => 3,
    ),
  ),
  46 => 
  array (
    14 => 
    array (
      'id' => 13,
      'start1' => 4,
      'start2' => 3,
    ),
    19 => 
    array (
      'id' => 18,
      'start1' => 4,
      'start2' => 3,
    ),
  ),
  47 => 
  array (
    79 => 
    array (
      'id' => 79,
      'start1' => 5,
      'start2' => 4,
    ),
    11 => 
    array (
      'id' => 12,
      'start1' => 1,
      'start2' => 2,
    ),
    80 => 
    array (
      'id' => 80,
      'start1' => 6,
      'start2' => 5,
    ),
    81 => 
    array (
      'id' => 81,
      'start1' => 5,
      'start2' => 4,
    ),
  ),
);

//var_export($cfg_data);

//var_dump($cfg_data);
/*
$hero_cfg_4 = getcfg('hero');
//原来技能配置
$hero_epskill_cfg_4 = 
*/

//遍历构造的配置
foreach ($cfg_data as $heroid => $v_h){
	echo $heroid.':'.$hero_cfg[$heroid]['name']."\n";
	foreach ($v_h as $k_skill => $v_skill){
		if($v_skill['id'] == 0){
			echo '删除技能'.$k_skill.$hero_epskill[$k_skill]['name']."\n";
		}else{
			echo '技能 '.$k_skill.$hero_epskill_cfg[$k_skill]['name']."(".$hero_epskill_cfg[$k_skill]['star']."星".$v_skill['start1'].")->"
			.$v_skill['id'].$hero_epskill_cfg_4[$v_skill['id']]['name']."(".$hero_epskill_cfg_4[$v_skill['id']]['star']."星".$v_skill['start2'].")\n";
		}
	}
}

Common::loadModel('HeroModel');
//for ($i = 0 ; $i < 100 ; $i++){
for ($i = 0 ; $i < $table_div ; $i++){
    $table = 'user_'.Common::computeTableId($i);
    echo $table."\n";
    $sql = 'select uid from '.$table;
    $res = $db->fetchArray($sql);
     if(!empty($res)){
         foreach($res as $val){
         	
			/*
			if ($val['uid'] != 4000005){
				continue;
			}
			*/
           $HeroModel = new HeroModel($val['uid']);
           
		   echo $val['uid'].':';
		   //遍历门客列表
		   foreach ($HeroModel->info as $hero_info){
			   //是否是需要修改的门客
			   if(empty($cfg_data[$hero_info['heroid']])){
				   continue;
			   }
			   echo $hero_info['heroid'].' ';
			   $skill = array();
			   //遍历门客技能
			   //$skill = $hero_info['epskill'];
			   foreach ($hero_info['epskill'] as $skill_id => $skill_lv){
				   //是否是需要改变的技能
				   if(empty($cfg_data[$hero_info['heroid']][$skill_id])){
					   $skill[$skill_id] = $skill_lv;
					   continue;
				   }
				   //如果技能被删除
				   if ($cfg_data[$hero_info['heroid']][$skill_id]['id'] == 0){
					   continue;
				   }
				   //进行技能改变
				   //原技能等级
				   $lv_1 = $skill_lv;
				   //原技能星级
				   $static_1 = $cfg_data [$hero_info['heroid']] [$skill_id]['start1'];
				   //原技能资质
				   $zz_1 = $lv_1 * $static_1;
					
					//新技能 等级计算
					$lv_2 = ceil($zz_1/$cfg_data [$hero_info['heroid']] [$skill_id] ['start2']);//进一取整
					//新技能
					$skill[$cfg_data [$hero_info['heroid']] [$skill_id]['id']] = $lv_2;
					
			   }
			   //更新一个门客
			   $udate = array(
					'heroid' => $hero_info['heroid'],
					'epskill' => $skill,
				);
				$HeroModel->update($udate);
				//print_r($udate);
		   }
		   $HeroModel->destroy();
		   unset($HeroModel);
			//echo "\n";
		   //清缓存没成功 //强制重登
		   $cache = Common::getCacheByUid($val['uid']);
			$cache->delete($val['uid'].'_team');
			$cache->delete($val['uid'].'_token');
			// exit;
			echo "\n";
        }
    }
	unset($res);
}



/*
 * 获取配置文件
 */
function getcfg($filename){
		//静态遍历
		//如果多皮
		if(defined('GAME_SKIN') && GAME_SKIN){
			$require_file = CONFIG_DIR . '/game_'.GAME_SKIN.'/' . $filename . '.php';//需要包含的文件
		}else{
			 $require_file = CONFIG_DIR . '/game/' . $filename . '.php';//需要包含的文件
		}
		if (!file_exists($require_file)){//没有的话
			Master::error($filename.'_error');
	   }
	   $cfg_arr = include($require_file);//读取新配置
	   return $cfg_arr;
}
