var i = require("Utils");
var n = require("RedDot");
var l = require("Initializer");
var r = require("TimeProxy");
var redDot = require("RedDot");
function LimitActivityProxy() {
    this.UPDATE_LIMIT_ACTIVE_SEVEN = "UPDATE_LIMIT_ACTIVE_SEVEN";
    this.UPDATE_DUIHUAN_HUODONG = "UPDATE_DUIHUAN_HUODONG";
    this.UPDATE_DAYDAY_HUODONG = "UPDATE_DAYDAY_HUODONG";
    this.UPDATE_DUIHUAN_SHOP = "UPDATE_DUIHUAN_SHOP";
    this.LIMIT_ACTIVITY_HUO_DONG_LIST = "LIMIT_ACTIVITY_HUO_DONG_LIST";
    this.UPDATE_BOSS_LIST = "UPDATE_BOSS_LIST";
    this.UPDATE_BOSS_INFO = "UPDATE_BOSS_INFO";
    this.UPDATE_ITEM_INFO = "UPDATE_ITEM_INFO";
    this.SUPER_RECHARGE_UPDATE = "SUPER_RECHARGE_UPDATE";
    this.ACTIVITY_SHOP_UPDATE = "ACTIVITY_SHOP_UPDATE";
    this.AT_LIST_RANK_UPDATE = "AT_LIST_RANK_UPDATE";
    this.AT_LIST_MY_RANK_UPDATE = "AT_LIST_MY_RANK_UPDATE";
    this.UPDATE_CLOTHES_SHOP = "UPDATE_CLOTHES_SHOP";//刷新服装商城信息
    this.UPDATE_ACTIVITY_GRID = "UPDATE_ACTIVITY_GRID";//刷新新春走格子信息
    this.UPDATE_FREE_BUY = "UPDATE_FREE_BUY";

    this.huodongList = null;
    this.curSelectData = null;
    this.curRushSelectData = null;
    this.sevenSign = null;
    this.cbRankList = null;
    this.cbMyRank = null;
    this.duihuan = null;
    this.dayday = null;
    this.duihuanShop = null;
    this.bossList = null;
    this.bossMyDmg = null;
    this.bossInfo = null;
    this.bossHit = null;
    this.bossRankList = null;
    this.superRecharge = null;
    this.clothShopInfo = null;//服装商城活动
    this.activityGridInfo = null;//春节走格子活动
    this.activityGridExchange = null;//春节走格子兑换商城
    this.activityGridStickShop = null;//春节走格子木棍商城
    this.activityGridMyRankInfo = null;//春节走格子自己排名信息
    this.activityGridRankInfo = null;//春节走格子排名信息

    this.curExchangeId = 0;
    this.SEVEN_DAY_ID = 287;
    this.SUPPORT_ID = 6136;
    this.DUIHUAN_ID = 6152;
    this.DAYDAY_ID = 6121;
    this.DUIHUANSHOP_ID = 6122;
    this.CLOTHEPVE_ID = 6123;
    this.CLOTHEPVP_ID = 6142;
    this.VOICE_ID = 6137;
    this.TRUN_TABLE_ID = 6169;
    this.DAILY_RECHARGE = 6168;
    this.PRINCE_ID = 6181;
    this.LEVEL_GIFT_ID = 6182;
    this.CZLB_ID = 6180;
    this.SHOPPING_CARNIVAL = 8004;
    this.SNOWMAN_ID = 6183;
    this.LXCZ_ID = 6184;
    this.GUO_LI_ID = 6187;
    this.LUCKY_BRAND_ID = 6188;
    this.LEI_TIAN_RECHARGE = 262;
    this.LANTERN_ID = 6189;
    this.ACT_BOSS_ID = 6010;
    this.JIE_QI_ID = 6211;
    this.LUCKY_CARP = 6214;
    this.TANG_YUAN_ID = 6015;
    this.GIRLS_DAY_ID = 6220;
    this.ARBOR_DAY_ID = 6221;
    this.QING_MING_ID = 6222;
    this.SPELL_ID = 6223;
    this.LION_ID = 6224;
    this.SUPER_RECHARGE_ID = 6225;
    this.SINGLE_RECHAGR_ID = 6226;
    this.LUCKY_TABLE_ID = 6227;
    this.READING_DAY_ID = 6228;
    this.LABOR_DAY_ID = 6229;
    this.DRAGON_BOAT_ID = 6230;
    this.GAO_DIAN_ID = 6231;
    this.BALLOON_ID = 6232;
    this.FOURKING_ID = 6233;
    this.HEDENG_ID = 6234;
    this.KUA_SHILI_ID = 313;
    this.KUA_LOV_ID = 314;
    this.SHILI_ID = 252;
    this.LOV_ID = 253;
    this.THIRTYDAYS_ID = 6500;
    this.QIXI_ID = 6241;
    this.ZHONGYUAN_ID = 6244;
    this.ACTIVITY_PRF_ID = 8002;
    this.ACTIVITY_WISHING_WELL_ID = 8003;
    this.CHRISTMAS_ID = 8005;
    this.COOKING_COMPETITION = 8006;
    this.VALENTINE_DAY = 8009;
    this.TIANCI_ID = 6242;
   // this.LIMIT_ACTIVITY_TYPE = 2;
    this.ACTIVITY_TYPE = 1;     // 活动类型
    this.SUPERBUY_TYPE = 2;     // 超值购类型
    this.GIFTBOX_TYPE = 3;      // 礼包类型
    this.BIG_ACTIVITY_TYPE = 4; // 大活动类型
    this.LIMIT_ACTIVITY_TYPE = 11;   // 限时活动类型
    this.RUSH2LIST_TYPE = 12;        // 冲榜活动类型
    this.FREEBUY_TYPE = 13;          // 0元购活动类型
   // this.AT_LIST_TYPE = 3;
    this.RECHARGE_TYPE = 4;
    this.SUPPORT_TYPE = 10;
    this.GIRLS_TYPE = 990;
    this.ARBOR_TYPE = 991;
    this.QING_MING_TYPE = 992;
    this.READING_TYPE = 993;
    this.SPRING_TYPE = 995;
    this.SNOWMAN_TYPE = 996;
    this.LABOR_TYPE = 997;
    this.DRAGON_BOAT_TYPE = 998;
    this.LUCKY_TABLE_TYPE = 999;
    this.BALLOON_TYPE = 1e3;
    this.HEDENG_TYPE = 1001;
    this.GRID_TYPE = 1006;//春节活动关联的活动类型
    this.QIXI_TYPE = 1002;
    this.KUA_CHONG_BANG_TYPE = 22;
    this.ZHONGYUAN_TYPE = 1003;
    this.WISH_WELL_TYPE = 1004;
    this.CHRISTMAS_TYPE = 1005;
    this.TOTAL_CHARGE = 261;
    this.GROUP_BUYING = 7010;
    this.ACTIVITY_RUSH2LIST = 116;
    this.CLOTHES_SHOP_ID = 8007;//服装商城活动
    this.ACTIVITY_GRID_ID = 8008;//走格子活动
    this.NOBLE_ORDER_ID = 8011;
    this.NOBLE_ORDER_NEW_ID = 8016;//新贵人令
    this.CRUSH_ACT_ID = 8018;//三消活动
    this.Tofu_ACT_ID = 8022;//豆腐公主活动
    this.MOON_BATTLE_ID = 8029;//大月亮
    this.BeachTreasureActID = 8026;//海滩夺宝活动
    this.MOON_BATTLE_TYPE = 1008;//打月亮 限时活动 type
    this.QING_MING_ID = 6222;//游山玩水

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.xshuodong.cash, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.amy, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.coin, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.juanzhou, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.qinmi, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.shili, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.zhengwu, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.login, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.yamen, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.lianyin, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.school, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.jingshang, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.nongchan, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.zhaomu, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.jishag2d, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.cjfanren, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.tiaozhanshu,this.onCash,this);
        JsonHttp.subscribe(proto_sc.xshuodong.zhenzai, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.tilidan, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.huolidan, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.meilizhi, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.fuyanhui, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.clubbosshit,this.onCash,this);
        JsonHttp.subscribe(proto_sc.xshuodong.clubbossjs,this.onCashthis);
        JsonHttp.subscribe(proto_sc.xshuodong.jiulouzf, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.xsRank, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.food, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.huolidan, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.treasure, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.qifu, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.jinglidan, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.chuyou, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.wenhou, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.jiaoji, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.yingyuan, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.xufang, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.lilian, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.pengren, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.dzlogin, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.stealdew, this.onCash, this);
        JsonHttp.subscribe(proto_sc.xshuodong.plant, this.onCash, this);
        JsonHttp.subscribe(proto_sc.czhuodong.onceRecharge,this.onCash,this);
        JsonHttp.subscribe(proto_sc.newPeopleBuy.buyinfo, this.onCash, this);
        JsonHttp.subscribe(proto_sc.czhuodong.day, this.onCash, this);
        JsonHttp.subscribe(proto_sc.czhuodong.total, this.onCash, this);
        JsonHttp.subscribe(proto_sc.czhuodong.leitian, this.onCash, this);
        JsonHttp.subscribe(proto_sc.huodonglist.all,this.onHuodongList,this);
        JsonHttp.subscribe(proto_sc.sevenSign.cfg, this.onSevenSign, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.shili, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.love, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.treasure, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.shililist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.lovelist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.treasurelist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myshiliRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myloveRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myTreaRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.duihuodong.duihuan,this.onDuihuan,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.guanqia, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.guanqialist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myguanqiaRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.yinliang, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.yinlianglist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myYinLiangRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.liangshi, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.liangshilist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myLiangShiRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.jiulou, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.jiuloulist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myJiuLouRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.shibing, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.shibinglist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myShiBingRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.shibing, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.shibinglist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myShiBingRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.herojb, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.herojblist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myHerojbRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.herozz, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.herozzlist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myHerozzRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.meili, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.meililist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myMeiLiRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.yamen, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.yamenlist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myyamenRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.clubyamen, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.clubyamenlist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myclubyamen,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.stealcl, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.stealcllist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myStealclRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.plants, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.plantslist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myPlantsRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.wifeskillexp,this.onClub,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.wifeskillexplist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.myWifeskillexpRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.sonshili, this.onClub, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.sonshililist,this.onCbRank,this);
        JsonHttp.subscribe(proto_sc.cbhuodong.mySonshiliRid,this.onMyCbRank,this);
        JsonHttp.subscribe(proto_sc.daydaybuy.dayday, this.onDayDay, this);
        JsonHttp.subscribe(proto_sc.duihuanshop.shop,this.onDuihuanShop,this);
        JsonHttp.subscribe(proto_sc.actboss.flist, this.onBossList, this);
        JsonHttp.subscribe(proto_sc.actboss.hit, this.onBossHit, this);
        JsonHttp.subscribe(proto_sc.actboss.info, this.onBossInfo, this);
        JsonHttp.subscribe(proto_sc.actboss.myDmg, this.onBossMyDmg, this);
        JsonHttp.subscribe(proto_sc.actboss.rankList,this.onBossRankList,this);
        JsonHttp.subscribe(proto_sc.cjttczhuodong.cjttcz,this.onSuperRecharge,this);
        JsonHttp.subscribe(proto_sc.zhenxiufang.zhenxiushizhuang,this.onClotheShop,this);
        JsonHttp.subscribe(proto_sc.xinchun.xinchungame,this.onActivityGrid,this);
        JsonHttp.subscribe(proto_sc.xinchun.exchange,this.onActivityGridExchange,this);
        JsonHttp.subscribe(proto_sc.xinchun.shop,this.onActivityGridStickShop,this);
        JsonHttp.subscribe(proto_sc.xinchun.myQxRid,this.onActivityGridMyRankInfo,this);
        JsonHttp.subscribe(proto_sc.xinchun.qxRank,this.onActivityGridRankInfo,this);
        JsonHttp.subscribe(proto_sc.fuli.zeroGift, this.updateZeroGiftData, this);        
        JsonHttp.subscribe(proto_sc.cbhuodong.guanqiaexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.shiliexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.loveexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.treasureexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.liangshiexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.yinliangexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.jiulouexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.shibingexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.herojbexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.herozzexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.meiliexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.yamenexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.clubyamenexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.stealclexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.plantsexchange, this.onExchange, this);
        JsonHttp.subscribe(proto_sc.cbhuodong.sonshiliexpexchange, this.onExchange, this);

    };    
    
    this.clearData = function() {
        this.huodongList = null;
        this.curSelectData = null;
        this.curRushSelectData = null;
        this.sevenSign = null;
        this.cbRankList = null;
        this.cbMyRank = null;
        this.duihuan = null;
        this.dayday = null;
        this.duihuanShop = null;
        this.bossList = null;
        this.bossInfo = null;
        this.bossHit = null;
        this.bossMyDmg = null;
        this.bossRankList = null;
        this.superRecharge = null;
        this.curExchangeId = null;
        this.clothShopInfo = null;//服装商城活动
        this.activityGridInfo = null;//春节走格子活动
        this.activityGridExchange = null;//春节走格子兑换商城
        this.activityGridStickShop = null;//春节走格子木棍商城
        this.activityGridMyRankInfo = null;//春节走格子自己排名信息
        this.activityGridRankInfo = null;//春节走格子排名信息
    };

    /**
     * 判定限时活动红点
     * @param actType 限时活动 type
    */
    this.checkLimitTimeActRed = function(actType){
        let limitActRedDot = false;
        let actData = this.getHuodongList(actType);
        for(let i = 0;i < actData.length;i++){
            if(1 == actData[i].news) {
                limitActRedDot = true;
                break;
            }
        }
        return limitActRedDot;
    }

    ////////春节走格子活动//////////////
    this.checkActivityGridRedDot = function(redID){
        if(this.activityGridInfo && this.activityGridInfo.rwd){
            let achievementRed = false;
            for(let i = 0;i < this.activityGridInfo.rwd.length;i++){
                let rwdItem = this.activityGridInfo.rwd[i];
                if(rwdItem.get == 0){
                    switch (rwdItem.type) {
                        case 1:{
                            if(this.activityGridInfo.play >= rwdItem.num){
                                achievementRed = true;
                            }
                        }break;
                        case 2:{
                            if(this.activityGridInfo.maxCons >= rwdItem.num){
                                achievementRed = true;
                            }
                        }break;
                        case 3:{
                            if(this.activityGridInfo.shake >= rwdItem.num){
                                achievementRed = true;
                            }
                        }break;
                        case 4:{
                            if(this.activityGridInfo.rank == 1){
                                achievementRed = true;
                            }
                        }break;
                        case 5:{
                            if(this.activityGridInfo.taozhuang == 1){
                                achievementRed = true;
                            }
                        }break;
                    }
                }
            }
            if(redID && redID == 1){//成就奖励
                return achievementRed;
            }
            if(redID && redID == 2){//关联活动
                return redDot._MAP['ActivityGridCorrelation'];
            }
            if(achievementRed || redDot._MAP['ActivityGridCorrelation']){
                return true;
            }
        }
        return false;
    },
    this.onActivityGridExchange = function(data) {
        this.activityGridExchange = data;
        facade.send(this.ACTIVITY_SHOP_UPDATE,this.getActivityGridExchange());
    };
    this.getActivityGridExchange = function() {
        let exchangeInfo = {};
        exchangeInfo.rwd = this.activityGridExchange;
        exchangeInfo.hid = this.ACTIVITY_GRID_ID;
        exchangeInfo.title = this.activityGridInfo.exchangeTitle;
        return exchangeInfo;
    };
    this.onActivityGridStickShop = function(data) {
        this.activityGridStickShop = data;
    };
    this.getActivityGridStickShop = function() {
        return this.activityGridStickShop;
    };
    this.onActivityGridMyRankInfo = function(data) {
        this.activityGridMyRankInfo = data;
    };
    this.onActivityGridRankInfo = function(data) {
        this.activityGridRankInfo = data;//春节走格子排名信息
    };
    this.sendActivityGrid = function() {
        JsonHttp.send(new proto_cs.huodong.hd8008Info());
    };
    this.onActivityGrid = function(data) {
        this.activityGridInfo = data;
        facade.send(this.UPDATE_ACTIVITY_GRID);
        redDot.change("activityGrid", this.checkActivityGridRedDot());
    };
    this.getActivityGridInfo = function(){
        return this.activityGridInfo;;
    }
    this.sendGridDraw = function() {
        JsonHttp.send(new proto_cs.huodong.hd8008Play());
    };
    this.sendRoleMove = function(roleIndex,cb) {
        let msg = new proto_cs.huodong.hd8008Move();
        msg.qizi = 'Q'+roleIndex;
        JsonHttp.send(msg,()=>{
            cb && cb();
        });
    };
    this.checkCanDraw = function() {
        if(this.activityGridInfo && this.activityGridInfo.dianshu){
            return (this.activityGridInfo.dianshu.length < 4);
        }
        return false;
    };
    this.getDrawPoint = function() {
        if(this.activityGridInfo && this.activityGridInfo.dianshu){
            return this.activityGridInfo.dianshu;
        }
        return null;
    };
    //获取同一位置上的角色数组
    this.getPartnerRole = function() {
        let rolePos = {};
        if(this.activityGridInfo && this.activityGridInfo.qizi){
            for(let i = 1;i <= 4;i++){
               let pos = this.activityGridInfo.qizi['Q'+i];
               if(rolePos[pos.toString()] == null){
                    rolePos[pos.toString()] = [];
               }
               if(pos >  0 && pos < 31){
                    rolePos[pos.toString()].push(i);
               }
            }
        }
        return rolePos;
    };
    //获取人偶当前位置-index-1,2,3,4
    this.getRolePos = function(index) {
        if(this.activityGridInfo && this.activityGridInfo.qizi){
            return this.activityGridInfo.qizi['Q'+index];
        }
        return 0;
    };
    //获取棋盘上该位置上所有的人偶
    this.getChessboardRole = function(pos) {
        let roleArray = [];
        if(this.activityGridInfo && this.activityGridInfo.qizi && (pos > 0)){
            for(let i = 1;i <= 4;i++){
                if(this.activityGridInfo.qizi['Q'+i] == pos){
                    roleArray.push(i);
                }
            }
        }
        return roleArray;
    };
    this.getRoleMoveStep = function() {
        if(this.activityGridInfo && this.activityGridInfo.move){
            return this.activityGridInfo.move;
        }
        return null;
    };
    this.getMoveBonusInfo = function(){
        let allBonusInfo = [];
        if(this.activityGridInfo){
            if(this.activityGridInfo.common && this.activityGridInfo.commonlist){
                let commonBonusInfo = {};
                commonBonusInfo.title = i18n.t('ACTIVITY_GRID_BONUS_TIP3');
                commonBonusInfo.item = this.activityGridInfo.common;
                commonBonusInfo.items = this.activityGridInfo.commonlist;
                allBonusInfo.push(commonBonusInfo);
            }
            if(this.activityGridInfo.corner && this.activityGridInfo.cornerlist){
                let cornerBonusInfo = {};
                cornerBonusInfo.title = i18n.t('ACTIVITY_GRID_BONUS_TIP4');
                cornerBonusInfo.item = this.activityGridInfo.corner;
                cornerBonusInfo.items = this.activityGridInfo.cornerlist;
                allBonusInfo.push(cornerBonusInfo);
            }
            if(this.activityGridInfo.end && this.activityGridInfo.endlist){
                let endBonusInfo = {};
                endBonusInfo.title = i18n.t('ACTIVITY_GRID_BONUS_TIP5');
                endBonusInfo.item = this.activityGridInfo.end;
                endBonusInfo.items = this.activityGridInfo.endlist;
                allBonusInfo.push(endBonusInfo);
            }
            if(this.activityGridInfo.finish && this.activityGridInfo.finishlist){
                let finishBonusInfo = {};
                finishBonusInfo.title = i18n.t('ACTIVITY_GRID_BONUS_TIP6');
                finishBonusInfo.item = this.activityGridInfo.finish;
                finishBonusInfo.items = this.activityGridInfo.finishlist;
                allBonusInfo.push(finishBonusInfo);
            }
        }
        return allBonusInfo;
    }
    this.sendGetReward = function(ID){
        let request = new proto_cs.huodong.hd8008Rwd();
        request.id = ID;
        JsonHttp.send(request, () => {
            l.timeProxy.floatReward();
        });
    }
    //////////////////活动排名相关///////////////////////////////////
    this.sendRankInfo = function (type,activityID,cb) {
        var request = new proto_cs.huodong['hd'+activityID+'paihang']();
        request.type = type;
        JsonHttp.send(request,()=>{
            cb && cb();
        });
    };
    this.geRankInfo = function (activityID) {
        let rankInfo = {};
        if(activityID == this.ACTIVITY_GRID_ID){
            rankInfo['myRankInfo'] = this.activityGridMyRankInfo;
            rankInfo['rankList'] = this.activityGridRankInfo;
            rankInfo['activiID'] = this.ACTIVITY_GRID_ID;
        }
        return rankInfo;
    };
    ///////////////////////////////////////////////////////////

    this.onExchange = function(t) {
        this.cbGbExchange = t;
        facade.send("RUSH2LIST_GIFTBOX_UPDATE", t);
    };

    this.onClotheShop = function(data) {
        this.clothShopInfo = data;
        if(this.clothShopInfo && this.clothShopInfo.refresh){
            this.clothShopInfo.refresh = this.clothShopInfo.refresh + i.timeUtil.second;
        }
        redDot.change("clotheShop", this.checkClotheShopRedDot());
        facade.send(this.UPDATE_CLOTHES_SHOP);
    };
    this.checkClotheShopRedDot = function(){
        if(this.clothShopInfo && this.clothShopInfo.shopList){
            for (let key in this.clothShopInfo.shopList) {
                if(this.clothShopInfo.shopList[key].is_new == 1){
                    return true;
                }
            }
        }
        return false;
    },
    this.checkClotheCanBuy = function(clothid){
        if(this.clothShopInfo && this.clothShopInfo.shopList){
            return (this.clothShopInfo.shopList[clothid] != null);
        }
        return false;
    },
    this.onDuihuanShop = function(t) {
        this.duihuanShop = t;
        n.change(
            "duihuanshop",
            null != t && t.info && 1 == t.info.news
        );
        facade.send(this.UPDATE_DUIHUAN_SHOP);
    };
    this.onDayDay = function(t) {
        this.dayday = t;
        n.change("dayday", null != t && t.info && 1 == t.info.news);
        facade.send(this.UPDATE_DAYDAY_HUODONG);
    };
    this.onDuihuan = function(t) {
        this.duihuan = t;
        n.change(
            "duihuan",
            null != t && t.info && 1 == t.info.news
        );
        facade.send(this.UPDATE_DUIHUAN_HUODONG);
    };
    this.onCash = function(t) {        
        facade.send("LIMIT_ACTIVITY_UPDATE", t);
    };
    this.onClub = function(t) {
        facade.send("AT_LIST_UPDATE", t);
    };
    this.onHuodongList = function(t) {
        null == this.huodongList
            ? (this.huodongList = t)
            : i.utils.copyList(this.huodongList, t);
        let  e = {};
        var limitactivty_reddot_flag = false;
        var superbuy_reddot_flag = false;
        for (let o = 0; o < this.huodongList.length; o++) {
            if (this.huodongList[o] && 1 == this.huodongList[o].news) {
                e[this.huodongList[o].type] = 1;
                e[this.huodongList[o].pindex] = 1;
            }
            if(l.playerProxy.userData && l.playerProxy.userData.name != null){
                if(l.playerProxy.userData.name != ''){
                    // 45天签到
                    if (this.huodongList[o].id === this.THIRTYDAYS_ID && !l.thirtyDaysProxy.data) {
                        l.thirtyDaysProxy.sendOpenActivity();
                    }
                    // 天赐活动
                    if(this.huodongList[o].id == this.TIANCI_ID && i.timeUtil.second >= this.huodongList[o].sTime 
                        && i.timeUtil.second <= this.huodongList[o].eTime)
                    {
                        let isOpen = r.funUtils.isOpenFun(r.funUtils.starHome)
                        redDot.change("tianci", isOpen);
                    }
                    //服装商城
                    if (this.huodongList[o].id === this.CLOTHES_SHOP_ID && !this.clothShopInfo) {
                        this.sendClotheShop();
                    }
                    //国力庆典
                    if (this.huodongList[o].id === this.GUO_LI_ID && !l.guoliPorxy.data) {
                        l.guoliPorxy.sendOpenActivity();
                    }
                    //春节走格子活动
                    if(this.huodongList[o].id === this.ACTIVITY_GRID_ID && !this.activityGridInfo){
                        this.sendActivityGrid();
                    }
                    if(this.huodongList[o].type === this.ACTIVITY_TYPE) {
                        if(this.huodongList[o].news) {
                            limitactivty_reddot_flag = true;
                        }
                        // 活动红点
                        redDot.change("limit_activity", limitactivty_reddot_flag);
                    }
                    if(this.huodongList[o].type === this.SUPERBUY_TYPE) {
                        if(this.huodongList[o].news) {
                            superbuy_reddot_flag = true;
                        }
                        // 超值购红点
                        redDot.change("superBuy", superbuy_reddot_flag);
                    }
                    //三消活动
                    if(this.huodongList[o].id === this.CRUSH_ACT_ID && l.crushProxy.data == null){
                        l.crushProxy.data = {};
                    }
                     //贵人令
                    if (this.huodongList[o].id === this.NOBLE_ORDER_ID || this.huodongList[o].id === this.NOBLE_ORDER_NEW_ID) {
                        l.nobleOrderProxy.setOrderActID(this.huodongList[o].id);
                    }
                }
            }
        }

        let list = localcache.getList(localdb.table_banner_title);
        for(let i = 0, len = list.length; i < len; i++) {
            let actData = list[i];
            if(actData.pindex != 0 && actData.binding) {
                let binding = JSON.parse(actData.binding);
                let val = null;
                switch(actData.pindex) {
                    case this.DUIHUAN_ID: {
                        val = this.duihuan;
                    } break;
                    case this.DAYDAY_ID: {
                        val = this.dayday;
                    } break;
                }
                for(let j = 0, jLen = binding.length; j < jLen; j++) {
                    val == null ? n.change(binding[j], 1 == e[actData.pindex])
                     : null == val && n.change(binding[j], 1 == e[actData.pindex]);
                }
            }
        }
      
        n.change("activityGrid", this.checkActivityGridRedDot());
        facade.send(this.LIMIT_ACTIVITY_HUO_DONG_LIST);
    };
    this.sendClotheShop = function() {
        JsonHttp.send(new proto_cs.huodong.hd8007Info());
    };
    this.sendClotheExchange = function(allBuyID,cb) {
        let msg = new proto_cs.huodong.hd8007exchange();
        msg.ids = allBuyID;
        JsonHttp.send(msg,()=>{
            cb && cb();
        });
    };
    this.sendClotheRefresh = function() {
        JsonHttp.send(new proto_cs.huodong.hd8007Refresh());
    };
    this.getGoldLeafBagList = function(){
        if(this.clothShopInfo && this.clothShopInfo.bagList){
            return this.clothShopInfo.bagList;
        }
        return null;
    },
    this.getClotheShopFreshTimes = function() {//获取服装商城刷新次数
        let freshTimes = 0;
        if(this.clothShopInfo && this.clothShopInfo.rNum){
            freshTimes = this.clothShopInfo.rNum;
        }
        return freshTimes;
    };
    this.getDiscountClothe = function() {
        let discountArray = [];
        if(this.clothShopInfo && this.clothShopInfo.sale){
            for (let discountID in this.clothShopInfo.sale) {
                discountArray.push(Number(discountID));
            }
        }
        return discountArray;
    };
    this.checkHaveBuy = function(clotheID){
        let haveBuy = false;
        if(this.clothShopInfo && this.clothShopInfo.shopList){
            if(this.clothShopInfo.shopList[clotheID]){
                haveBuy = this.clothShopInfo.shopList[clotheID].is_have == 1;
            }
        }
        return haveBuy;
    },
    this.getDiscountClotheInfo = function(clotheID) {
        if(this.clothShopInfo && this.clothShopInfo.sale){
            for (let discountID in this.clothShopInfo.sale) {
                if(Number(discountID) == clotheID){
                    return {
                        discount:this.clothShopInfo.sale[discountID],
                        countDown:this.clothShopInfo.refresh
                    };
                }
            }
        }
        return null;
    };

    this.isHaveTypeActive = function(t) {
        if (null == this.huodongList) return !1;
        for (var e = 0; e < this.huodongList.length; e++)
            if (
                this.huodongList[e].type == t &&
                i.timeUtil.second < this.huodongList[e].showTime
            )
                return !0;
        return !1;
    };
    this.isHaveIdActive = function(t) {
        if (null == this.huodongList) return !1;
        for (var e = 0; e < this.huodongList.length; e++)
            if (
                this.huodongList[e].id == t &&
                i.timeUtil.second < this.huodongList[e].showTime
            )
                return !0;
        return !1;
    };
    this.onCbRank = function(t) {
        this.cbRankList = t;
        facade.send(this.AT_LIST_RANK_UPDATE);
    };
    this.onMyCbRank = function(t) {
        this.cbMyRank = t;
        facade.send("AT_LIST_MY_RANK_UPDATE");
    };
    this.getHuodongList = function(t) {
        var e = [];
        if (null == this.huodongList) return e;
        for (var o = 0; o < this.huodongList.length; o++)
            this.huodongList[o].type == t
                ? e.push(this.huodongList[o])
                : this.huodongList[o].type == t &&
                  e.push(this.huodongList[o]);
        e.sort(this.sortHuodong);
        return e;
    };

    this.getHuodongListByTypeTab = function(type, tab) {
        var e = [];
        //console.error("this.huodongList:",this.huodongList)
        if (null == this.huodongList) return e;
        for (var o = 0; o < this.huodongList.length; o++) {
            if(this.huodongList[o].type == type) {
                let hudong = localcache.getGroup(localdb.table_banner_title, "pindex", this.huodongList[o].pindex);
                if(hudong[0].tab == tab) {
                    this.huodongList[o].title = hudong[0].title;
                    e.push(this.huodongList[o]);
                }
            }
        }            
        e.sort(this.sortHuodong);
        return e;
    };

    this.isShowTianCiAct = function(){
        if (this.huodongList == null) return false;
        for(var j = 0;j < this.huodongList.length;j++)
        {
            let data = this.huodongList[j];
            if(data.id == this.TIANCI_ID && i.timeUtil.second >= data.sTime 
                && i.timeUtil.second <= data.eTime)
            {
                return true;
            }
        }
        return false;
    };
    this.checkTianCiAct = function(){
        let isShow = this.isShowTianCiAct();
        let isOpen = r.funUtils.isOpenFun(r.funUtils.starHome)
        redDot.change("tianci", isShow && isOpen || false);
    };
    this.getTianCiTime = function(){
        if (this.huodongList == null) return 0;
        for(var j = 0;j < this.huodongList.length;j++)
        {
            let data = this.huodongList[j];
            if(data.id == this.TIANCI_ID && i.timeUtil.second >= data.sTime 
                && i.timeUtil.second <= data.eTime)
            {
                return data.eTime;
            }
        }
        return 0;
    };
    this.sortHuodong = function(t, e) {
        return t.news > e.news ? -1 : t.news == e.news ? t.id - e.id : 1;
    };
    this.sendLookActivityData = function(t, e) {
        void 0 === e && (e = null);
        JsonHttp.send(
            new proto_cs.huodong["hd" + t + "Info"](),
            function() {
                e && e();
            }
        );
    };
    this.sendGetActivityReward = function(t, e) {
        void 0 === e && (e = 0);
        var o = new proto_cs.huodong["hd" + t + "Rwd"]();
        0 != e && (o.id = e);
        JsonHttp.send(o, function() {
            l.timeProxy.floatReward();
        });
    };
    this.sendActivityShopExchange = function(t, e,count) {
        void 0 === e && (e = 0);
        var o = new proto_cs.huodong["hd" + t + "exchange"]();
        0 != e && (o.id = e);
        o.count = count;
        JsonHttp.send(o, function(){
            l.timeProxy.floatReward();
        });
    };
    this.onSevenSign = function(t) {
        this.sevenSign = t;
        for (var e = !1, o = 0; o < this.sevenSign.level.length; o++)
            if (1 == this.sevenSign.level[o].type) {
                e = !0;
                break;
            }
        n.change("sevenday", e);
        facade.send(this.UPDATE_LIMIT_ACTIVE_SEVEN);
    };
    this.sendSevenRwd = function(t) {
        var e = new proto_cs.huodong.hd287Get();
        e.id = t;
        JsonHttp.send(e, function() {
            l.timeProxy.floatReward();
        });
    };
    this.sendHdList = function() {
        JsonHttp.send(new proto_cs.huodong.hdList());
    };
    this.getActivityData = function(t) {
        var e = null;
        if (this.huodongList)
            for (var o = 0; o < this.huodongList.length; o++)
                if (t == this.huodongList[o].id) {
                    e = this.huodongList[o];
                    break;
                }
        return e;
    };
    this.onBossList = function(t) {
        this.bossList = t;
        facade.send(this.UPDATE_BOSS_LIST);
    };
    this.onBossHit = function(t) {
        this.bossHit = t;
    };
    this.onBossInfo = function(t) {
        this.bossInfo = t;
        facade.send(this.UPDATE_BOSS_INFO);
    };
    this.onBossMyDmg = function(t) {
        this.bossMyDmg = t;
    };
    this.onBossRankList = function(t) {
        this.bossRankList = t;
    };
    this.onSuperRecharge = function(t) {
        this.superRecharge = t;
        facade.send("SUPER_RECHARGE_UPDATE");
    };
    this.sendBossRank = function(t) {
        var e = this;
        JsonHttp.send(new proto_cs.huodong.hd6010Rank(), function() {
            var t = {};
            t.rank = e.bossMyDmg.g2dmyrank;
            t.value = e.bossMyDmg.g2dmydamage;
            i.utils.openPrefabView("RankCommon", null, {
                rankType: "ACTBOSS_RANK",
                list: e.bossRankList,
                mine: t
            });
        });
    };
    this.sendBossBack = function(t) {
        var e = new proto_cs.huodong.hd6010Add();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendBossHit = function(t, e) {
        var o = new proto_cs.huodong.hd6010Fight();
        o.id = t;
        o.type = e;
        JsonHttp.send(o, function() {
            l.timeProxy.floatReward();
        });
    };
    this.sendSpecialBuy = function(t, e, o) {
        var n = this.getActivityData(t);
        if (n && i.timeUtil.second > n.eTime)
            i.alertUtil.alert18n("ACTHD_OVERDUE");
        else {
            var r = new proto_cs.huodong["hd" + t + "buy"]();
            r.id = e;
            r.num = o;
            JsonHttp.send(r, ()=>{
                l.timeProxy.floatReward();
                facade.send(this.UPDATE_ITEM_INFO);
            });
        }
    };
    this.sendOpenSuperRecharge = function() {
        JsonHttp.send(new proto_cs.huodong.hd6225Info());
    };
    this.sendGetSuperRechargeRwd = function(t) {
        var e = new proto_cs.huodong.hd6225Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            l.timeProxy.floatReward();
        });
    };
    this.sendGetSuperRechargeTotal = function(t) {
        var e = new proto_cs.huodong.hd6225TotalRwd();
        e.id = t;
        JsonHttp.send(e, function() {
            l.timeProxy.floatReward();
        });
    };
    this.sendScoreChange = function(t, e) {
        var o = new proto_cs.huodong["hd" + t + "duihuan"]();
        o.id = e;
        JsonHttp.send(o, function() {
            l.timeProxy.floatReward();
        });
    };

    this.reqBuyFree = function(id) {
        let req = new proto_cs.fuli.buyZeroGift();
        req.id = id;
        JsonHttp.send(req, function() {
            l.timeProxy.floatReward();
        });
    };

    this.reqGetFree = function(id) {
        let req = new proto_cs.fuli.pickZeroRebate();
        req.id = id;
        JsonHttp.send(req, function() {
            l.timeProxy.floatReward();
        });
    };

    this.updateZeroGiftData = function(data) {
        this.freeBuyData = data.info;
        this.checkFreeBuyRed();
        facade.send(this.UPDATE_FREE_BUY);
    };

    this.checkFreeBuyRed = function() {
        let data = this.freeBuyData;
        if(null != data) {
            let bShow = false;
            let time = null;
            for(let kvp in data) {
                let endTime = data[kvp].endTime;
                if(endTime > 0 && data[kvp].pickTime <= 0) {
                    if(endTime > i.timeUtil.second) {
                        if(time == null || time > endTime) {
                            time = endTime;
                        }
                    } else {
                        bShow = true;
                        break;
                    }
                }
            }
            i.timeUtil.addCountEvent(!bShow && null != time, time, "freeBuy", () => {
                self.checkFreeBuyRed();
            });
            redDot.change("freeBuy", bShow);
        }
    };

    this.sendActivityInfo = function(t,cb) {
        JsonHttp.send(new proto_cs.huodong["hd" + t + "Info"](),()=>{
            cb && cb();
        });
    };
}
exports.LimitActivityProxy = LimitActivityProxy;

var ActivityUtils = function() {
    this._list = null;
    this.initLeftList = function() {//初始化活动按钮icon
        if (!this._list) {
            this._list = [];
            let list = localcache.getList(localdb.table_banner_title);
            for(let i = 0, len = list.length; i < len; i++) {
                let data = list[i];
                if(null != data.funitem) {
                    let tarData = { 
                        funitem: r.funUtils[data.funitem],
                        url: data.url,
                        binding: data.binding ? JSON.parse(data.binding) : null,
                        type: data.typelocation,
                        id: data.pindex,
                    }
                    this._list.push(tarData);
                }
            }
        }
    };
    Object.defineProperty(ActivityUtils.prototype, "activityList", {
        get: function() {
            null == this._list && this.initLeftList();
            return this._list;
        },
        enumerable: !0,
        configurable: !0
    });
}
exports.ActivityUtils = ActivityUtils;
var AcitityDataItem = function() {
    this.funitem = null;
    this.url = "";
    this.binding = [];
    this.id = 0;
    this.type = 1;
    this.isEff = 1;
};
exports.AcitityDataItem = AcitityDataItem;
exports.activityUtils = new ActivityUtils();
