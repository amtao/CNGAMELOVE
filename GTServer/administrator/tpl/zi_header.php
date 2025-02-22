<?php
//ps: 因为有权限限制  所以key值 不可随意更改  
$zi_links = array(
        1 => array( //账号管理
            1 => array( 'title' => 'user信息' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=user&act=userChange' ),
            2 => array( 'title' => 'Ustr--Uid' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=user&act=findUid' ),
            3 => array( 'title' => '角色转移' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=user&act=transferRoleData' ),
            4 => array( 'title' => '用户基础信息' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=user&act=allinfo' ),
            5 => array( 'title' => '快速升级' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=user&act=ghostUp' ),
            6 => array( 'title' => '一键高级号' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=user&act=levelUp' ),
            7 => array( 'title' => '角色删除' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=user&act=del_uid' ),
            ),
        2 => array( //信息管理
            1 => array( 'title' => '查询缓存' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=showCache' ),
            2 => array( 'title' => '聊天记录' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=chat' ),
            6 => array( 'title' => '聊天系统' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=chatSystem' ),
            3 => array( 'title' => '禁言封号查询' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=transUser' ),
            7 => array( 'title' => '敏感字库' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=sensitive' ),
            4 => array( 'title' => '缓存监控' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=cache' ),
            5 => array( 'title' => '历史缓存数据' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=oldCache' ),
            
            9 => array( 'title' => '跨服聊天记录' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=chatkua' ),
            11=> array( 'title' => '封号系统' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=closure' ),
            12=> array( 'title' => '任务流失率' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=taskDrain' ),
            13=> array( 'title' => '付费注册间隔分布' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=registerToPay' ),
            14=> array( 'title' => '后台日志' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=admin_log' ),
            15=> array( 'title' => '超过限制ip监控' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=ipmonitor' ),
            16=> array( 'title' => '头像分布' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=header' ),
            17=> array( 'title' => '用户步骤流失率' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=userStep' ),
            18=> array( 'title' => '服装统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=fuzhuang' ),
            19 => array( 'title' => '知己统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=zhiji' ),
            20 => array( 'title' => '伙伴统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=huoban' ),
            21 => array( 'title' => '服装知己伙伴每日统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=huobanday' ),
            22 => array( 'title' => '道具统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=itemAll' ),
            24 => array( 'title' => '在线统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=onLine' ),
            30 => array( 'title' => '在线统计(内部)' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=onLineCompany' ),
            // 25 => array( 'title' => '客服自动回复' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=chatAuto' ),
            26 => array( 'title' => '用户丢失' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=lostUser' ),
            27 => array( 'title' => '活跃用户' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=activeUser' ),
            28 => array( 'title' => '活跃公会用户' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=activeClubUser' ),
            29 => array( 'title' => 'VIP充值查询' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=vipPaySearch' ),
            101=> array( 'title' => '注册任务进度' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=registerTask' ),
            102=> array( 'title' => '注册章节进度' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=registerStory' ),
            103 => array( 'title' => '自动禁言字库' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=autojinyanword' ),
        ),
        3 => array( //数据管理
            1 => array( 'title' => '充值查询(非后台)' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=paySearch' ),
            28 => array( 'title' =>'日充值统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=payInfo' ),
            2 => array( 'title' => '充值查询(后台)' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=showdata' ),
            3 => array( 'title' => '充值统计分类' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=totalType' ),
            4 => array( 'title' => '总览' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=totalCX' ),
            31 => array( 'title' => '总览简易版' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=pandect' ),
            37 => array( 'title' => '付费数据' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=totalCX3' ),
            39 => array( 'title' => '实时留存' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=totalCXLiuCun' ),
            32 => array( 'title' => '留存' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=remain' ),
            22 => array( 'title' => '日总付费-分区' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=total1' ),
            5 => array( 'title' => '身份分布' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=goverpost' ),
            6 => array( 'title' => '用户统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=totalUser' ),
            7 => array( 'title' => 'ltv' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=ltv' ),
            33 => array( 'title' => 'ltv(美元)' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=LtvUSD' ),
            /*31 => array( 'title' => '新ltv' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=newLtv' ),*/
            8 => array( 'title' => '每日用户数据' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=everydayData' ),
            20 => array( 'title' => '充值' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=qudaodata' ),
            21 => array( 'title' => '充值排序' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=paysort' ),
            26 => array( 'title' => '玩家充值详情' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=userPayInfo' ),
            27 => array( 'title' => '滚服数据统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=rollServerData' ),
            29 => array( 'title' => '充值异常信息' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=payAbnormal' ),
            30 => array( 'title' => '消费统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=consume' ),
            34 => array( 'title' => '消费人次统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=consumpTimes' ),
            35 => array( 'title' => '消费人数统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=consumeNums' ),
            36 => array( 'title' => '直充消费统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=zhichong' ),
            38 => array( 'title' => '档位充值查询' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=dangwei' ),
            40 => array( 'title' => '贵人令充值查询' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=guirenling' ),
            41 => array( 'title' => '礼包充值查询' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=data&act=giftbag' ),

        ),
        4 => array( //配置管理
            1 =>array( 'title'   =>  '基础配置' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=config&act=baseConfig' ),
            //2 =>array( 'title'   =>  '活动配置' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=config&act=actBaseConfig' ),
            3 =>array( 'title'   =>  '活动预览' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=effectiveList' ),
            4 =>array( 'title'   =>  '活动管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=passList&auditType=1' ),
            5 =>array( 'title'   =>  '基础配置管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=gameConfig&act=configList' ),
            6 =>array( 'title'   =>  '公告配置' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=config&act=ggConfig' ),
            7 =>array( 'title'   =>  '客服跑马灯' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=config&act=kefupmd' ),
        ),
        5 => array( //功能管理
            1 => array( 'title'  =>  '直 冲' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=recharge' ),
            13 => array( 'title'  => '补单' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=addOrder' ),
            6 => array( 'title'  =>  '充值福利' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=recharge_fuli' ),
            7 => array( 'title'  =>  '福利日志' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=fulidata' ),
            8 => array( 'title'  =>  '苹果订单跟踪' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=order' ),
            4 => array( 'title'  =>  '礼包配置' , 'src'  =>   '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=gifts' ),
            2 => array( 'title'  =>  '兑换码生成' , 'src'=>'?sevid='.$SevidCfg['sevid'].'&mod=fun&act=redeemCode' ),
            5 => array( 'title'  =>  '工具类' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=tool' ),
            9 => array( 'title'  =>  '功能测试' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=test' ),
            10 => array( 'title'  => '调时间' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=changeTime' ),
         11 => array( 'title'  => '兑换码查询' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=lookAcode' ),
            12 => array( 'title'  => 'Sev信息' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=sevInfo' ),
            14 => array( 'title'  =>  '月卡年卡' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=getCard' ),
            15 => array( 'title'  =>  '版本管理' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=getVersion' ),
            16 => array( 'title'  =>  '全服元宝查询' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=wingSearch' ),
            17 => array( 'title'  =>  '删除沙盒充值记录' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=delPay' ),
            // 18 => array( 'title'  =>  '白名单' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=whiteList' ),
            19 => array( 'title'  =>  '客服中心系统' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=serviceChat' ),
            // 20 => array( 'title'  =>  '充值退款' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=czjijin' ),
            // 21 => array( 'title'  =>  'deeplink' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=getDeeplink' ),
            22 => array( 'title'  =>  '全服元宝日统计' , 'src'  =>    '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=userDiamond' ),

        ),
        6 => array( //服务器管理
            1 => array( 'title'   =>  '服务器管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=servers&act=jsonToArray' ),
        ),
        7 => array( //排行榜
            1 => array( 'title'   =>  '势力' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=shili' ),//势力、好感、关卡、工会
            2 => array( 'title'   =>  '好感' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=qinmi' ),
            3 => array( 'title'   =>  '关卡' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=guanka' ),
            7 => array( 'title'   =>  '宫斗' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=yamen' ),
            4 => array( 'title'   =>  '宫殿' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=gonghui' ),
            5 => array( 'title'   =>  '查看宫殿' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=catgh' ),
            6 => array( 'title'   =>  '查看宫殿流水' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=clubFlow' ),
//            5 => array( 'title'   =>  '点赞' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=dianzan' ),
        ),
        8 => array( //邮件管理
            1 =>array( 'title'   =>  '发邮件' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=giveEmail' ),
            2 =>array( 'title'   =>  '发邮件(含物品)' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=giveItemEmail' ),
            3 =>array( 'title'   =>  '本服发邮件' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=serverEmailList&emailType=auditPass' ),
            4 =>array( 'title'   =>  '全服发邮件' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=allserverEmailList' ),
            6 =>array( 'title'   =>  '发邮件(需审核)' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=giveItemEmailAuditing' ),
            7 =>array( 'title'   =>  '邮件审核' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=emailAuditing' ),
            8 =>array( 'title'   =>  '邮件删除(本服和全服邮件)' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=delMail' ),
            9 =>array( 'title'   =>  '邮件列表' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=emailList' ),
            10 =>array( 'title'   =>  '物品模版' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=mailGift' ),
        ),
        9 => array( //权限管理
            1 =>array( 'title'   =>  '账户信息' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=auth&act=userAccount' ),
            2 =>array( 'title'   =>  '账户权限' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=auth&act=authConfig' ),
            3 =>array( 'title'   =>  '账户权限配置' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=auth&act=authChange' ),
        ),
        10 => array( //埋点需求
            1 => array( 'title' => '道具消耗统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=itemConsumeAll' ),
            2 => array( 'title' => '卡牌统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=cardAll' ),
            3 => array( 'title' => '四海奇珍统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=baowuAll' ),
            4 => array( 'title' => '兑换商城兑换' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=exchangeshop' ),
            5 => array( 'title' => '头像统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=headTotal' ),
            6 => array( 'title' => '伙伴信息统计' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=heroInfo' ),
            7 => array( 'title' => '出游-问候' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=travel' ),
            8 => array( 'title' => '任务档位领取信息' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=gearPick' ),
            9 => array( 'title' => '其他档位领取信息' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=otherGearPick' ),
            10 => array( 'title' => '购买礼包' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=buygift' ),
            11 => array( 'title' => '月卡-周卡' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=monthweek' ),
            12 => array( 'title' => '行商档位-消耗元宝' , 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=gearConsume' ),
        ),
);
if (!defined("SWITCH_GAME_ACT_FROM_DB") || !SWITCH_GAME_ACT_FROM_DB) {
    unset($zi_links[4][4]);
}
if (!defined("SWITCH_GAME_CONFIG_FROM_DB") || !SWITCH_GAME_CONFIG_FROM_DB) {
    unset($zi_links[4][5]);
}
?>
<?php
if(empty($_SESSION['zi_page'])){
    echo "<script>alert('33333333333333333333');</script>";
    header('HTTP/1.1 404 Not Found');
    exit();
}
$auth = $_SESSION['USER_POWER_LIST'];
echo '<div class="mytable">';

foreach ($zi_links[$_SESSION['zi_page']] as $k => $lk) {
//  有配置权限
    if(!empty($auth)){
        //第一级目录为空 过滤
        if(empty($auth['ml'][$_SESSION['zi_page']])){
            continue;
        }
        if(!in_array($k,$auth['ml'][$_SESSION['zi_page']])){
            continue;
        }
    }
    if( '?' . $_SERVER['QUERY_STRING'] == $lk['src'] || substr('?'.$_SERVER['QUERY_STRING'], 0, strlen($lk['src']) == $lk['src'])){
        echo "<a href='{$lk['src']}' data-btn='load' style='color:#F00;border: 1px solid #bda2a2;' class='backGroundColor'>{$lk['title']}</a>";
    }else{
        echo "<a href='{$lk['src']}' data-btn='load' style='border: 1px solid #bda2a2;' class='backGroundColor'>{$lk['title']}</a>";
    }

}
echo '</div>';
?>
<script>
    $(document).ready(function(){
        $("[data-btn='load']").on('click',function () {
            layer.load();
        });
    });
</script>
