<?php 
/**
 * 跨服帮会战脚本
 * 
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$Sev_Cfg = Common::getSevidCfg($serverID);//子服ID



//匹配
do_kuaclub_serv_50();



//将 主服的 clubkuajf_redis   分成两个   在  复制到各个主区去

//do_kuaclub_jifen();




Master::click_destroy();


echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();

function do_kuaclub_serv_50(){
    global $Sev_Cfg;

    $key = 50;
    $hcid = 1;
    $did = 76002;

    $data = array (
        350002 =>
            array (
                'fcid' => '50006',
                'fname' => '执笔画江山',
                'msevid' => 5,
            ),
        220002 =>
            array (
                'fcid' => '190049',
                'fname' => '皇城★崛起',
                'msevid' => 19,
            ),
        340004 =>
            array (
                'fcid' => '290002',
                'fname' => '神★阁',
                'msevid' => 29,
            ),
        200001 =>
            array (
                'fcid' => '250004',
                'fname' => '醉独楼丶',
                'msevid' => 25,
            ),
        190112 =>
            array (
                'fcid' => '250013',
                'fname' => '醉月楼',
                'msevid' => 25,
            ),
        330036 =>
            array (
                'fcid' => '270044',
                'fname' => '洪门',
                'msevid' => 27,
            ),
        340003 =>
            array (
                'fcid' => '400005',
                'fname' => '话说三国丶',
                'msevid' => 40,
            ),
        280023 =>
            array (
                'fcid' => '380091',
                'fname' => '第四特区',
                'msevid' => 38,
            ),
        60003 =>
            array (
                'fcid' => '290115',
                'fname' => '天门',
                'msevid' => 29,
            ),
        180075 =>
            array (
                'fcid' => '30006',
                'fname' => '麒麟世家',
                'msevid' => 3,
            ),
        150007 =>
            array (
                'fcid' => '210017',
                'fname' => '豪门情谊',
                'msevid' => 21,
            ),
        220006 =>
            array (
                'fcid' => '350014',
                'fname' => '一统天下',
                'msevid' => 35,
            ),
        270017 =>
            array (
                'fcid' => '320005',
                'fname' => '鸿蒙世界',
                'msevid' => 32,
            ),
        210017 =>
            array (
                'fcid' => '150007',
                'fname' => '歃血为盟',
                'msevid' => 15,
            ),
        130004 =>
            array (
                'fcid' => '300007',
                'fname' => '魔隐殿',
                'msevid' => 30,
            ),
        20009 =>
            array (
                'fcid' => '360032',
                'fname' => '疯狂战斗机',
                'msevid' => 36,
            ),
        130017 =>
            array (
                'fcid' => '380004',
                'fname' => '江南烟雨阁',
                'msevid' => 38,
            ),
        200030 =>
            array (
                'fcid' => '370006',
                'fname' => '星辰殿',
                'msevid' => 37,
            ),
        310001 =>
            array (
                'fcid' => '10005',
                'fname' => '永远的外交',
                'msevid' => 1,
            ),
        50002 =>
            array (
                'fcid' => '120005',
                'fname' => '天地会',
                'msevid' => 12,
            ),
        230001 =>
            array (
                'fcid' => '90001',
                'fname' => '傲世帝国',
                'msevid' => 9,
            ),
        320005 =>
            array (
                'fcid' => '270017',
                'fname' => '青云起',
                'msevid' => 27,
            ),
        80002 =>
            array (
                'fcid' => '300003',
                'fname' => '九天阁',
                'msevid' => 30,
            ),
        300057 =>
            array (
                'fcid' => '310113',
                'fname' => '荣誉',
                'msevid' => 31,
            ),
        190001 =>
            array (
                'fcid' => '360005',
                'fname' => '名人堂',
                'msevid' => 36,
            ),
        170011 =>
            array (
                'fcid' => '290016',
                'fname' => '天机',
                'msevid' => 29,
            ),
        10047 =>
            array (
                'fcid' => '240012',
                'fname' => '无聊帮',
                'msevid' => 24,
            ),
        370006 =>
            array (
                'fcid' => '200030',
                'fname' => '泥儿会',
                'msevid' => 20,
            ),
        420004 =>
            array (
                'fcid' => '100112',
                'fname' => '月舞花溪◆剑轻吟',
                'msevid' => 10,
            ),
        70002 =>
            array (
                'fcid' => '290009',
                'fname' => '听潮亭',
                'msevid' => 29,
            ),
        210007 =>
            array (
                'fcid' => '150001',
                'fname' => '皇城',
                'msevid' => 15,
            ),
        360009 =>
            array (
                'fcid' => '40031',
                'fname' => '七星阁',
                'msevid' => 4,
            ),
        240072 =>
            array (
                'fcid' => '40001',
                'fname' => '独孤神仙',
                'msevid' => 4,
            ),
        280021 =>
            array (
                'fcid' => '320045',
                'fname' => '天龙盟',
                'msevid' => 32,
            ),
        280048 =>
            array (
                'fcid' => '250144',
                'fname' => '聚、一家亲',
                'msevid' => 25,
            ),
        50006 =>
            array (
                'fcid' => '350002',
                'fname' => '逍遥半世丶丨',
                'msevid' => 35,
            ),
        350014 =>
            array (
                'fcid' => '220006',
                'fname' => '水泊梁山',
                'msevid' => 22,
            ),
        360005 =>
            array (
                'fcid' => '190001',
                'fname' => '战魂殿',
                'msevid' => 19,
            ),
        160007 =>
            array (
                'fcid' => '390004',
                'fname' => '乱世枭雄',
                'msevid' => 39,
            ),
        400004 =>
            array (
                'fcid' => '410036',
                'fname' => '☆*:.情谊永恒☆*:.',
                'msevid' => 41,
            ),
        90001 =>
            array (
                'fcid' => '230001',
                'fname' => '天庭灬万仙阁',
                'msevid' => 23,
            ),
        380091 =>
            array (
                'fcid' => '280023',
                'fname' => '炼狱',
                'msevid' => 28,
            ),
        380004 =>
            array (
                'fcid' => '130017',
                'fname' => '洪门',
                'msevid' => 13,
            ),
        80027 =>
            array (
                'fcid' => '240005',
                'fname' => '大秦帝国',
                'msevid' => 24,
            ),
        330001 =>
            array (
                'fcid' => '400029',
                'fname' => '判官',
                'msevid' => 40,
            ),
        250004 =>
            array (
                'fcid' => '200001',
                'fname' => '风云动九天',
                'msevid' => 20,
            ),
        140003 =>
            array (
                'fcid' => '300001',
                'fname' => '创世纪',
                'msevid' => 30,
            ),
        390030 =>
            array (
                'fcid' => '410012',
                'fname' => '牧府',
                'msevid' => 41,
            ),
        290009 =>
            array (
                'fcid' => '70002',
                'fname' => '天地会',
                'msevid' => 7,
            ),
        400005 =>
            array (
                'fcid' => '340003',
                'fname' => '生崽大队',
                'msevid' => 34,
            ),
        240005 =>
            array (
                'fcid' => '80027',
                'fname' => '刀锋',
                'msevid' => 8,
            ),
        70009 =>
            array (
                'fcid' => '140013',
                'fname' => '天命轩辕',
                'msevid' => 14,
            ),
        390004 =>
            array (
                'fcid' => '160007',
                'fname' => '風雲～天下',
                'msevid' => 16,
            ),
        190049 =>
            array (
                'fcid' => '220002',
                'fname' => '珈蓝神殿',
                'msevid' => 22,
            ),
        300003 =>
            array (
                'fcid' => '80002',
                'fname' => '繁星',
                'msevid' => 8,
            ),
        10171 =>
            array (
                'fcid' => '60001',
                'fname' => '第一帮',
                'msevid' => 6,
            ),
        360086 =>
            array (
                'fcid' => '260035',
                'fname' => '狼群',
                'msevid' => 26,
            ),
        290016 =>
            array (
                'fcid' => '170011',
                'fname' => '皇朝',
                'msevid' => 17,
            ),
        60001 =>
            array (
                'fcid' => '10171',
                'fname' => '樱琉霜雪思华年',
                'msevid' => 1,
            ),
        300001 =>
            array (
                'fcid' => '140003',
                'fname' => '天地会',
                'msevid' => 14,
            ),
        390001 =>
            array (
                'fcid' => '240013',
                'fname' => '_木易府丶',
                'msevid' => 24,
            ),
        400001 =>
            array (
                'fcid' => '380002',
                'fname' => '凌烟阁丶',
                'msevid' => 38,
            ),
        150001 =>
            array (
                'fcid' => '210007',
                'fname' => '黑风寨',
                'msevid' => 21,
            ),
        320045 =>
            array (
                'fcid' => '280021',
                'fname' => '别往',
                'msevid' => 28,
            ),
        250013 =>
            array (
                'fcid' => '190112',
                'fname' => '锦瑟庄',
                'msevid' => 19,
            ),
        120005 =>
            array (
                'fcid' => '50002',
                'fname' => '唐宋元明清',
                'msevid' => 5,
            ),
        40001 =>
            array (
                'fcid' => '240072',
                'fname' => '大唐~天下',
                'msevid' => 24,
            ),
        140011 =>
            array (
                'fcid' => '380034',
                'fname' => '仙魔殿',
                'msevid' => 38,
            ),
        100112 =>
            array (
                'fcid' => '420004',
                'fname' => '瓦岗山',
                'msevid' => 42,
            ),
        260035 =>
            array (
                'fcid' => '360086',
                'fname' => '君临天下',
                'msevid' => 36,
            ),
        40031 =>
            array (
                'fcid' => '360009',
                'fname' => '创世辉煌',
                'msevid' => 36,
            ),
        240012 =>
            array (
                'fcid' => '10047',
                'fname' => '青梅煮酒',
                'msevid' => 1,
            ),
        300007 =>
            array (
                'fcid' => '130004',
                'fname' => '风云会',
                'msevid' => 13,
            ),
        380002 =>
            array (
                'fcid' => '400001',
                'fname' => '众神殿',
                'msevid' => 40,
            ),
        140013 =>
            array (
                'fcid' => '70009',
                'fname' => '家国兴',
                'msevid' => 7,
            ),
        250140 =>
            array (
                'fcid' => '170009',
                'fname' => '君临天下',
                'msevid' => 17,
            ),
        290099 =>
            array (
                'fcid' => '250026',
                'fname' => '圣道盟',
                'msevid' => 25,
            ),
        250144 =>
            array (
                'fcid' => '280048',
                'fname' => '半夕阁',
                'msevid' => 28,
            ),
        400029 =>
            array (
                'fcid' => '330001',
                'fname' => '和甜钰',
                'msevid' => 33,
            ),
        450003 =>
            array (
                'fcid' => '230127',
                'fname' => '华夏',
                'msevid' => 23,
            ),
        310113 =>
            array (
                'fcid' => '300057',
                'fname' => '盐帮',
                'msevid' => 30,
            ),
        360032 =>
            array (
                'fcid' => '20009',
                'fname' => '慕容世家',
                'msevid' => 2,
            ),
        200010 =>
            array (
                'fcid' => '10310',
                'fname' => '友谊',
                'msevid' => 1,
            ),
        330172 =>
            array (
                'fcid' => '180128',
                'fname' => '114帮会',
                'msevid' => 18,
            ),
        270044 =>
            array (
                'fcid' => '330036',
                'fname' => '一品白衫',
                'msevid' => 33,
            ),
        290115 =>
            array (
                'fcid' => '60003',
                'fname' => '六扇门',
                'msevid' => 6,
            ),
        10005 =>
            array (
                'fcid' => '310001',
                'fname' => '莫逆之交',
                'msevid' => 31,
            ),
        180128 =>
            array (
                'fcid' => '330172',
                'fname' => '爱的世界',
                'msevid' => 33,
            ),
        250026 =>
            array (
                'fcid' => '290099',
                'fname' => '天地会',
                'msevid' => 29,
            ),
        10310 =>
            array (
                'fcid' => '200010',
                'fname' => '天王盖地虎',
                'msevid' => 20,
            ),
        190024 =>
            array (
                'fcid' => '10301',
                'fname' => '银魅',
                'msevid' => 1,
            ),
        380034 =>
            array (
                'fcid' => '140011',
                'fname' => '正义联盟',
                'msevid' => 14,
            ),
        240013 =>
            array (
                'fcid' => '390001',
                'fname' => '水之府',
                'msevid' => 39,
            ),
        410012 =>
            array (
                'fcid' => '390030',
                'fname' => '梦染江山',
                'msevid' => 39,
            ),
        290002 =>
            array (
                'fcid' => '340004',
                'fname' => '开心就好',
                'msevid' => 34,
            ),
        30006 =>
            array (
                'fcid' => '180075',
                'fname' => '隔壁神仙',
                'msevid' => 18,
            ),
        170009 =>
            array (
                'fcid' => '250140',
                'fname' => '聚缘楼',
                'msevid' => 25,
            ),
        230127 =>
            array (
                'fcid' => '450003',
                'fname' => '英雄★联盟',
                'msevid' => 45,
            ),
        10301 =>
            array (
                'fcid' => '190024',
                'fname' => '胜者王府',
                'msevid' => 19,
            ),
        30002 =>
            array (
                'fcid' => 240017,
                'fname' => '情义',
                'msevid' => 24,
            ),
        240017 =>
            array (
                'fcid' => 30002,
                'fname' => '九黎',
                'msevid' => 3,
            ),
        260003 =>
            array (
                'fcid' => 280002,
                'fname' => '镜花水月',
                'msevid' => 28,
            ),
        280002 =>
            array (
                'fcid' => 260003,
                'fname' => '地狱メ冥府',
                'msevid' => 26,
            ),
        110004 =>
            array (
                'fcid' => 180012,
                'fname' => '肥嘟嘟的嘟嘟帮',
                'msevid' => 18,
            ),
        180012 =>
            array (
                'fcid' => 110004,
                'fname' => '神殿',
                'msevid' => 11,
            ),
        20034 =>
            array (
                'fcid' => 380022,
                'fname' => '帝王朝',
                'msevid' => 38,
            ),
        380022 =>
            array (
                'fcid' => 20034,
                'fname' => '夜醉青楼',
                'msevid' => 2,
            ),
        320003 =>
            array (
                'fcid' => 370002,
                'fname' => '冰雪，银城',
                'msevid' => 37,
            ),
        370002 =>
            array (
                'fcid' => 320003,
                'fname' => '乱世之秋',
                'msevid' => 32,
            ),
        270015 =>
            array (
                'fcid' => 410001,
                'fname' => '舍我其谁雄霸天下',
                'msevid' => 41,
            ),
        410001 =>
            array (
                'fcid' => 270015,
                'fname' => '华夏帝国',
                'msevid' => 27,
            ),
        390003 =>
            array (
                'fcid' => 180014,
                'fname' => '天门',
                'msevid' => 18,
            ),
        180014 =>
            array (
                'fcid' => 390003,
                'fname' => '兄弟门',
                'msevid' => 39,
            ),
        90006 =>
            array (
                'fcid' => 60007,
                'fname' => '大唐不良人',
                'msevid' => 6,
            ),
        60007 =>
            array (
                'fcid' => 90006,
                'fname' => '过往云畑',
                'msevid' => 9,
            ),
        310090 =>
            array (
                'fcid' => 330007,
                'fname' => 'F丶AK丨葬爱灬',
                'msevid' => 33,
            ),
        330007 =>
            array (
                'fcid' => 310090,
                'fname' => '风云',
                'msevid' => 31,
            ),
        180034 =>
            array (
                'fcid' => 410023,
                'fname' => '至尊皇朝',
                'msevid' => 41,
            ),
        410023 =>
            array (
                'fcid' => 180034,
                'fname' => '倚剑听雨阁',
                'msevid' => 18,
            ),
        160037 =>
            array (
                'fcid' => 230043,
                'fname' => '皇朝陌千尘',
                'msevid' => 23,
            ),
        230043 =>
            array (
                'fcid' => 160037,
                'fname' => '蓬莱阁',
                'msevid' => 16,
            ),
        370003 =>
            array (
                'fcid' => 120042,
                'fname' => '天地会二舵',
                'msevid' => 12,
            ),
        120042 =>
            array (
                'fcid' => 370003,
                'fname' => '星辰堂',
                'msevid' => 37,
            ),
        360017 =>
            array (
                'fcid' => 320057,
                'fname' => '踏临九天',
                'msevid' => 32,
            ),
        320057 =>
            array (
                'fcid' => 360017,
                'fname' => '雪域王朝',
                'msevid' => 36,
            ),
        260001 =>
            array (
                'fcid' => 370009,
                'fname' => '逍遥王朝',
                'msevid' => 37,
            ),
        370009 =>
            array (
                'fcid' => 260001,
                'fname' => '至尊会所',
                'msevid' => 26,
            ),
        350017 =>
            array (
                'fcid' => 400011,
                'fname' => '阳光战队',
                'msevid' => 40,
            ),
        400011 =>
            array (
                'fcid' => 350017,
                'fname' => '观雨阁',
                'msevid' => 35,
            ),
        10107 =>
            array (
                'fcid' => 150024,
                'fname' => '傲天裂地',
                'msevid' => 15,
            ),
        150024 =>
            array (
                'fcid' => 10107,
                'fname' => '中央政治局',
                'msevid' => 1,
            ),
        120007 =>
            array (
                'fcid' => 250087,
                'fname' => '战狼世家',
                'msevid' => 25,
            ),
        250087 =>
            array (
                'fcid' => 120007,
                'fname' => '天地会三舵',
                'msevid' => 12,
            ),
        420001 =>
            array (
                'fcid' => 220017,
                'fname' => '碧海阁',
                'msevid' => 22,
            ),
        220017 =>
            array (
                'fcid' => 420001,
                'fname' => '惜缘',
                'msevid' => 42,
            ),
        320004 =>
            array (
                'fcid' => 390015,
                'fname' => '随心所欲',
                'msevid' => 39,
            ),
        390015 =>
            array (
                'fcid' => 320004,
                'fname' => '醉卧美人膝',
                'msevid' => 32,
            ),
        270041 =>
            array (
                'fcid' => 10016,
                'fname' => '烟雨醉红尘',
                'msevid' => 1,
            ),
        10016 =>
            array (
                'fcid' => 270041,
                'fname' => '起点',
                'msevid' => 27,
            ),
        80030 =>
            array (
                'fcid' => 340006,
                'fname' => '天庭丶锦衣卫',
                'msevid' => 34,
            ),
        340006 =>
            array (
                'fcid' => 80030,
                'fname' => '天上人间',
                'msevid' => 8,
            ),
        120041 =>
            array (
                'fcid' => 80055,
                'fname' => '战狼中队',
                'msevid' => 8,
            ),
        80055 =>
            array (
                'fcid' => 120041,
                'fname' => '罹烬王朝',
                'msevid' => 12,
            ),
        360008 =>
            array (
                'fcid' => 10001,
                'fname' => '夜澜￡风雨',
                'msevid' => 1,
            ),
        10001 =>
            array (
                'fcid' => 360008,
                'fname' => '兄弟帮',
                'msevid' => 36,
            ),
        300066 =>
            array (
                'fcid' => 50008,
                'fname' => '隐逸',
                'msevid' => 5,
            ),
        50008 =>
            array (
                'fcid' => 300066,
                'fname' => '神盾局',
                'msevid' => 30,
            ),
        250086 =>
            array (
                'fcid' => 250018,
                'fname' => '晨曦轩',
                'msevid' => 25,
            ),
        250018 =>
            array (
                'fcid' => 250086,
                'fname' => '官居',
                'msevid' => 25,
            ),
        390024 =>
            array (
                'fcid' => 430016,
                'fname' => '倾城々情缘',
                'msevid' => 43,
            ),
        430016 =>
            array (
                'fcid' => 390024,
                'fname' => '風雨同舟紫竹阁',
                'msevid' => 39,
            ),
        240008 =>
            array (
                'fcid' => 220008,
                'fname' => '鸿丰总督署',
                'msevid' => 22,
            ),
        220008 =>
            array (
                'fcid' => 240008,
                'fname' => '龍行天下',
                'msevid' => 24,
            ),
        210047 =>
            array (
                'fcid' => 10281,
                'fname' => '御风丶逆羽',
                'msevid' => 1,
            ),
        10281 =>
            array (
                'fcid' => 210047,
                'fname' => '女子宿舍',
                'msevid' => 21,
            ),
        70062 =>
            array (
                'fcid' => 290010,
                'fname' => '信仰',
                'msevid' => 29,
            ),
        290010 =>
            array (
                'fcid' => 70062,
                'fname' => 'hero ·',
                'msevid' => 7,
            ),
        390011 =>
            array (
                'fcid' => 260158,
                'fname' => '狼',
                'msevid' => 26,
            ),
        260158 =>
            array (
                'fcid' => 390011,
                'fname' => '乱世豪杰',
                'msevid' => 39,
            ),
        260041 =>
            array (
                'fcid' => 310018,
                'fname' => '飞璜腾达',
                'msevid' => 31,
            ),
        310018 =>
            array (
                'fcid' => 260041,
                'fname' => '兄弟盟',
                'msevid' => 26,
            ),
        190028 =>
            array (
                'fcid' => 10145,
                'fname' => '群雄会',
                'msevid' => 1,
            ),
        10145 =>
            array (
                'fcid' => 190028,
                'fname' => '锦绣河山',
                'msevid' => 19,
            ),
        310150 =>
            array (
                'fcid' => 370121,
                'fname' => '【梅苑山庄】',
                'msevid' => 37,
            ),
        370121 =>
            array (
                'fcid' => 310150,
                'fname' => '青&帮',
                'msevid' => 31,
            ),
        370115 =>
            array (
                'fcid' => 290224,
                'fname' => '魔阁',
                'msevid' => 29,
            ),
        290224 =>
            array (
                'fcid' => 370115,
                'fname' => '带你装逼带你飞',
                'msevid' => 37,
            ),
        140092 =>
            array (
                'fcid' => 380093,
                'fname' => '龙虎帮',
                'msevid' => 38,
            ),
        380093 =>
            array (
                'fcid' => 140092,
                'fname' => '潇湘苑',
                'msevid' => 14,
            ),
        410008 =>
            array (
                'fcid' => 90019,
                'fname' => '天下无双会',
                'msevid' => 9,
            ),
        90019 =>
            array (
                'fcid' => 410008,
                'fname' => '天使之翼',
                'msevid' => 41,
            ),
        440003 =>
            array (
                'fcid' => 410009,
                'fname' => '日出东方～我主沉浮',
                'msevid' => 41,
            ),
        410009 =>
            array (
                'fcid' => 440003,
                'fname' => '刚好遇见你',
                'msevid' => 44,
            ),
        130002 =>
            array (
                'fcid' => 420006,
                'fname' => '东辑事厂',
                'msevid' => 42,
            ),
        420006 =>
            array (
                'fcid' => 130002,
                'fname' => '乱世战狼',
                'msevid' => 13,
            ),
        340045 =>
            array (
                'fcid' => 330047,
                'fname' => '开封府',
                'msevid' => 33,
            ),
        330047 =>
            array (
                'fcid' => 340045,
                'fname' => '英雄帮',
                'msevid' => 34,
            ),
        240041 =>
            array (
                'fcid' => 390064,
                'fname' => '炸天帮',
                'msevid' => 39,
            ),
        390064 =>
            array (
                'fcid' => 240041,
                'fname' => '瓦岗寨',
                'msevid' => 24,
            ),
        270060 =>
            array (
                'fcid' => 350091,
                'fname' => '天下一统',
                'msevid' => 35,
            ),
        350091 =>
            array (
                'fcid' => 270060,
                'fname' => '无敌剑域',
                'msevid' => 27,
            ),
        320117 =>
            array (
                'fcid' => 150015,
                'fname' => '战四海',
                'msevid' => 15,
            ),
        150015 =>
            array (
                'fcid' => 320117,
                'fname' => '黄峰谷',
                'msevid' => 32,
            ),
        260014 =>
            array (
                'fcid' => 370035,
                'fname' => '星辰阁',
                'msevid' => 37,
            ),
        370035 =>
            array (
                'fcid' => 260014,
                'fname' => '康乾盛世',
                'msevid' => 26,
            ),
        220120 =>
            array (
                'fcid' => 450001,
                'fname' => '浪漫烟雨城',
                'msevid' => 45,
            ),
        450001 =>
            array (
                'fcid' => 220120,
                'fname' => '海龙王',
                'msevid' => 22,
            ),
        190153 =>
            array (
                'fcid' => 420023,
                'fname' => '太玄阁',
                'msevid' => 42,
            ),
        420023 =>
            array (
                'fcid' => 190153,
                'fname' => '胜者王府人才库',
                'msevid' => 19,
            ),
        310003 =>
            array (
                'fcid' => 210027,
                'fname' => '清渊阁',
                'msevid' => 21,
            ),
        210027 =>
            array (
                'fcid' => 310003,
                'fname' => '斗攻兼爱霸皇帝临',
                'msevid' => 31,
            ),
        370258 =>
            array (
                'fcid' => 250033,
                'fname' => '丐帮',
                'msevid' => 25,
            ),
        250033 =>
            array (
                'fcid' => 370258,
                'fname' => '小号会',
                'msevid' => 37,
            ),
        250016 =>
            array (
                'fcid' => 290100,
                'fname' => '兄弟情义盟',
                'msevid' => 29,
            ),
        290100 =>
            array (
                'fcid' => 250016,
                'fname' => '第一梦',
                'msevid' => 25,
            ),
        320134 =>
            array (
            ),
    );


    $value_text = json_encode($data);

    $db = Common::getMyDb();

    $sql = "update `sev_act` set `did`='{$did}',`value`='{$value_text}' where `key`='{$key}' and `hcid`='{$hcid}'";
    $db->fetchArray($sql);
}

/**
 * 1分为2
 */
function do_kuaclub_jifen(){
	
	global $Sev_Cfg;

    $key = 'clubkuajf_redis';   //要分的reis

    $key_1 = 'clubkuajf_redis_1_40';   //要分的reis

    $key_2 = 'clubkuajf_redis_41_998';   //要分的reis

    $point = 41 * 10000;  //区分的 区服id *10000

    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据

    foreach ( $rdata as $cid => $value ){

        if($cid < $point){  //小于区分的点 存 $key_1
            $redis->zAdd($key_1, $value, $cid );
        }else{  //大于等于区分的点 存 $key_2
            $redis->zAdd($key_2, $value, $cid );
        }

    }


}






