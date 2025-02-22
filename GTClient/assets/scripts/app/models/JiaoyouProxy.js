// 9/17 HZW
// 郊游数据结构
let Utils = require("Utils")
let Initializer = require("Initializer")
var RedDot = require("RedDot");
import { CARD_SLOT_SKILL_TYPE } from "GameDefine";

var JiaoyouProxy = function() {

    this.ctor = function() {
        this.STAGE_FIGHT = 1 //正在打关卡
        this.STAGE_CLEAR = 2 //已完成
        this.STAGE_CLOSE = 3 //未开启

        //heroid:stageid
        this.copyInfo = {}

        this.cashBuy = 0; //int
        this.defendCount = 0; //int
        this.weekAwardPick = []; //[]
        this.weekdefendCount = 0; //int

        //选中的守护卡片
        this.shouhuChooseCard = []
        /**伙伴可以进行到的最大关卡*/
        this.maxStageDic = {};

        JsonHttp.subscribe(proto_sc.jiaoyou.jiaoyou, this.onJiaoyou, this);
        JsonHttp.subscribe(proto_sc.jiaoyou.list, this.onJiaoyouList, this);
        JsonHttp.subscribe(proto_sc.jiaoyou.fightInfo, this.onFightInfo, this);
    };

    this.clearData = function() {
        this.copyInfo = {} //伙伴当前打过的最大stage  {5:1}
        this.cashBuy = 0; //购买的次数
        this.defendCount = 0; //今日守护次数
        this.weekAwardPick = []; //[] 记录领取过的宝箱
        this.weekdefendCount = 0; //本周守护次数
        this.maxStageDic = {};
    };

    this.onJiaoyou = function(e){
        if(e.cashBuy)  this.cashBuy = e.cashBuy; //int
        if(e.defendCount) this.defendCount = e.defendCount; //int
        if(e.weekAwardPick) this.weekAwardPick = e.weekAwardPick; //[]
        if(e.weekdefendCount) this.weekdefendCount = e.weekdefendCount; //int
        if(e.copyInfo) this.copyInfo = e.copyInfo;
        var jiaoyouRed = false
        for (var ii = 0; ii < Initializer.servantProxy.servantList.length;ii++){
            let cg = Initializer.servantProxy.servantList[ii];
            if (this.checkShouhuReward(cg.id)){
                jiaoyouRed = true;
                break;
            }
        }
        RedDot.change("jiaoyou",jiaoyouRed || this.checkBoxReward());

        facade.send("ON_JIAOYOU_INFO")
    };

    this.onFightInfo = function(data){
        this.fightInfo = data;
    };

    //伙伴郊游守护数据 guardList {1：{xx:{id:,award:,equipCard:,id,refreshTime:,star:},xx:{}}，2：{}}
    this.onJiaoyouList = function(e){
        this.guardList = e.guardList
        var jiaoyouRed = false
        for (var ii = 0; ii < Initializer.servantProxy.servantList.length;ii++){
            let cg = Initializer.servantProxy.servantList[ii];
            if (this.checkShouhuReward(cg.id)){
                jiaoyouRed = true;
                break;
            }
        }
        RedDot.change("jiaoyou",jiaoyouRed || this.checkBoxReward());
        facade.send("REFRESH_JIAOYOU_GUARD");
    }

    /** 伙伴该stage状态
     * servantId：伙伴id
     * stageId：关卡id
     * return 1:正在打   2:已经打过   3:未开启
     * **/
    this.stageType = function(servantId,stageId){
        if (this.getServantMaxStage(servantId) == stageId){
            return this.STAGE_CLEAR
        }
        var nowStage = this.getServantStage(servantId)
        if(nowStage == stageId){
            return this.STAGE_FIGHT
        }else if(nowStage > stageId){
            return this.STAGE_CLEAR
        }else{
            return this.STAGE_CLOSE
        }
    }

    /**获取伙伴正在打的关卡id
     * servantId：伙伴id
     * **/
    this.getServantStage = function(servantId){
        if(!this.copyInfo[servantId]){
            return 1;
        }
        var servantStageAry = localcache.getGroup(localdb.table_jiaoyou,"heroType",servantId)
        var nextStage = this.copyInfo[servantId] + 1
        for(var i=0;i<servantStageAry.length;i++){
            if(servantStageAry[i].stage == nextStage){
                return nextStage
            }
        }
        return this.copyInfo[servantId]
    };

    /**获取伙伴正在打的章节
     * servantId：伙伴id
     * **/
    this.getServantChapter = function(servantId){
        if(!this.copyInfo[servantId]){
            return 1;
        }
        var stage = this.getServantStage(servantId)
        var servantStageAry = localcache.getGroup(localdb.table_jiaoyou,"heroType",servantId)
        for(var i=0;i<servantStageAry.length;i++){
            if(servantStageAry[i].stage == stage){
                return servantStageAry[i].chapter
            }
        }
        return 1;
    };

    /**获取伙伴可打到的最大关卡*/
    this.getServantMaxStage = function (servantId) {
        if (this.maxStageDic[servantId] == null){
            var servantStageAry = localcache.getGroup(localdb.table_jiaoyou,"heroType",servantId);
            servantStageAry.sort((a,b)=>{
                return a.stage < b.stage ? -1 : 1;
            })
            this.maxStageDic[servantId] = servantStageAry[servantStageAry.length - 1].stage;
        }
        return this.maxStageDic[servantId];
    }


    /**获取伙伴某个章节所有的小关卡
     * servantId：伙伴id
     * chapterId: 章节id
     * **/
    this.getChapterAllStage = function(servantId,chapterId){
        var stages = []
        var servantStageAry = localcache.getGroup(localdb.table_jiaoyou,"heroType",servantId)
        for(var i=0;i<servantStageAry.length;i++){
            if(servantStageAry[i].chapter == chapterId){
                stages.push(servantStageAry[i])
            }
        }
        return stages
    }

    //伙伴守护列表
    this.getShouhuList = function(servantId){
        if(!this.guardList || !this.guardList[servantId]){
            return []
        }
        var ary = [];
        for(var i in this.guardList[servantId]){
            var shouhuData = this.guardList[servantId][i]
            shouhuData.shouhuServerId = i
            ary.push(shouhuData)
        }
        return ary;
    }

    /**获取当前id的守护数据*/
    this.getShouHuDataById = function(servantId,id){
        if(!this.guardList || !this.guardList[servantId]){
            return null;
        }
        return this.guardList[servantId][id];
    };

    //周保护最高次数
    this.getMaxWeekCt = function(){
        if(this.maxCt){
            return this.maxCt
        }
        this.maxCt = 0
        var servantStageAry = localcache.getList(localdb.table_jiaoyouWeek)
        for(var i=0;i<servantStageAry.length;i++){
            if(servantStageAry[i].cishu > this.maxCt){
                this.maxCt = servantStageAry[i].cishu
            }
        }
        return this.maxCt;
    }

    //每日守护次数
    this.getDayShouhuCt = function(){
        var jiaoyou_guaji_cishu = Utils.utils.getParamInt("jiaoyou_guaji_cishu");
        let buyInfo = Initializer.monthCardProxy.getCardData(1);
        if(buyInfo && buyInfo.type > 0){
            jiaoyou_guaji_cishu += Utils.utils.getParamInt("jiaoyou_guaji_yueka");
        }
        jiaoyou_guaji_cishu += this.cashBuy
        return jiaoyou_guaji_cishu
    }

    //效率提升：(100 + 总卡牌品质*15 + 总卡牌星级*5)/100
    this.shouhuXiaolv = function(cards){
        var allQuality = 0
        var allStar = 0
        for(var i=0;i<cards.length;i++){
            var cardCfg = localcache.getItem(localdb.table_card,cards[i])
            if(cardCfg){
                var cardMap = Initializer.cardProxy.cardMap[cards[i]]
                allQuality+=parseInt(cardCfg.quality)
                if (cardMap && cardMap.star != null)
                    allStar+=parseInt(cardMap.star)
            }
        }
        return (100+allQuality*15+allStar*5)/100
    }

    /**
    *guajiCfg:jiaoyouGuaji配置
    *jiaoyouCfg：jiaoyou配置
    *cards ：[选择的卡牌id]
    */
    this.getShouhuAwdNum = function(guajiCfg,jiaoyouCfg,starCfg,cards){
        //产量计算：int(output参数 * this.shouhuXiaolv() * base参数)
        let xiaolv = this.shouhuXiaolv(cards)
        let output = jiaoyouCfg["output"][guajiCfg["output"]]==0?1:jiaoyouCfg["output"][guajiCfg["output"]]
        let base = guajiCfg["baseNum"]
        let percent = Initializer.clotheProxy.getClotheSuitCardSlotRewardValue(CARD_SLOT_SKILL_TYPE.JIAOYOU_OUTPUT_ADDPERCENT);
        return Math.floor(((percent + 100)/100) * output * xiaolv * base * starCfg.starOutput / 100);
    }

    this.sendGetInfo = function(servantId){
        var req = new proto_cs.jiaoyou.getBaseInfo()
        JsonHttp.send(req,(e)=>{
            Utils.utils.openPrefabView("jiaoyou/JiaoyouChapterView", !1, {
                servantId: servantId
            });
        });
    };

    this.removeChooseCardByIndex = function(index){
        if(this.shouhuChooseCard[index]) this.shouhuChooseCard[index] = 0
        facade.send("JIAOYOU_SHOUHU_CARD")
    }

    //卡片添加到选择守护的列表
    this.addCardByChoose = function(jiaoyouId,cardId){
        if(this.shouhuChooseCard.indexOf(cardId) >= 0){
            var pos = this.shouhuChooseCard.indexOf(cardId)
            this.shouhuChooseCard[pos] = 0
            facade.send("JIAOYOU_SHOUHU_CARD")
            return
        }
        if(this.shouhuChooseCard.indexOf(0) >= 0){
            var pos = this.shouhuChooseCard.indexOf(0)
            this.shouhuChooseCard[pos] = cardId
            facade.send("JIAOYOU_SHOUHU_CARD")
            return
        }
        var jiaoyouCfg = localcache.getItem(localdb.table_jiaoyou,jiaoyouId)
        var cardNum = jiaoyouCfg.cardNum
        if(cardNum > this.shouhuChooseCard.length){
            this.shouhuChooseCard.push(cardId)
            facade.send("JIAOYOU_SHOUHU_CARD")
            return
        }
        this.shouhuChooseCard[0] = cardId
        facade.send("JIAOYOU_SHOUHU_CARD")
    }

    //是否需要打开刷新确认界面
    this.isOpenRefreshView = function(){
        var dayStr = Utils.timeUtil.format(Utils.timeUtil.second,"yyyy-MM-dd")
        var isOpen = Initializer.timeProxy.getLoacalValue(dayStr+"jiaoyou_shouhu") != 1;
        return isOpen
    }

    this.saveOpenRefreshView = function(){
        var dayStr = Utils.timeUtil.format(Utils.timeUtil.second,"yyyy-MM-dd")
        Initializer.timeProxy.saveLocalValue(dayStr+"jiaoyou_shouhu",1);
    }

    //请求伙伴战斗属性
    this.sendgetFightInfo = function(heroId,stageCfg){
        var req = new proto_cs.jiaoyou.getFightInfo()
        req.heroId = heroId;
        JsonHttp.send(req,(e)=>{
            if(e.a.jiaoyou && e.a.jiaoyou.fightInfo){
                var fightInfo = e.a.jiaoyou.fightInfo;

                this.fightInfo = fightInfo

                Utils.utils.openPrefabView("jiaoyou/JiaoyouStageView", !1, {
                    fightInfo: fightInfo,
                    stageCfg: stageCfg
                });
                facade.send("FIGHT_GAME_CT_FIGHT");
            }
        });
    };

    this.sendFight = function(cardId,callback){
        var e = new proto_cs.jiaoyou.fight()
        e.cardId = cardId
        JsonHttp.send(e,(rep)=>{
            callback && callback(rep);
        })
    };

    //刷新守护列表
    this.sendRefreshGuardList = function(heroId){
        var e = new proto_cs.jiaoyou.refreshGuardList()
        e.heroId = heroId
        JsonHttp.send(e)
    }

    /**开始守护
    *  heroId   copyId    cardEquips（string 用 | 连接，上阵的卡牌id）
    **/
    this.sendStartGuard = function(heroId,copyId,cardNum){
        var num = 0
        var cardEquips = ""
        for(var i=0;i<this.shouhuChooseCard.length;i++){
            if(this.shouhuChooseCard[i] > 0){
                cardEquips = cardEquips+this.shouhuChooseCard[i]+"|"
                num++
            }
        }
        if(cardNum != num){
            Utils.alertUtil.alert(i18n.t("SHOUHU_CARD_NUM_ERROR"));
            return false
        }
        cardEquips = cardEquips.substring(0, cardEquips.lastIndexOf('|'));  
        var e = new proto_cs.jiaoyou.startGuard()
        e.heroId = heroId
        e.copyId = copyId
        e.cardEquips = cardEquips
        JsonHttp.send(e)
        return true
    }

    //领取守护奖励
    this.sendPickGuardAward = function(heroId,copyId){
        var e = new proto_cs.jiaoyou.pickGuardAward()
        e.heroId = heroId
        e.copyId = copyId
        JsonHttp.send(e,function(){
            Initializer.timeProxy.floatReward();
        })
    }

    //花费元宝购买次数
    this.sendCashBuyCount = function(){
        var yuanbao = Utils.utils.getParamStrs("jiaoyou_guaji_yuanbao")
        if(yuanbao[this.cashBuy]){
            var needYuanbao = yuanbao[this.cashBuy][1]
            if(Initializer.playerProxy.userData.cash < needYuanbao){
                Utils.alertUtil.alert(i18n.t("COMMON_GOLD_NOT_ENOUGH"));
                return
            }
        }
        Utils.utils.showConfirm(i18n.t("BUY_JIAOYOU_SHOUHU_CT"), () => {
            var e = new proto_cs.jiaoyou.cashBuyCount()
            JsonHttp.send(e,function(){
                Utils.alertUtil.alert(i18n.t("BUG_SCUESS"));
            })
        });
    }

    //领取每周守护次数奖励
    this.snedPickGuardWeekAward = function(id){
        var e = new proto_cs.jiaoyou.pickGuardWeekAward()
        e.id = id
        JsonHttp.send(e,function(){
            Initializer.timeProxy.floatReward();
        })
    }

    /**检测守护是否可领奖*/
    this.checkShouhuReward = function(heroid){
        let showChapterId = this.getServantChapter(heroid)
        if (showChapterId <= 1){
            let stage = this.getServantStage(heroid);
            if (stage <= 5){
                return false
            }
        }
        let listdata = this.getShouhuList(heroid);
        let rFlag = false;
        for (var ii = 0; ii < listdata.length;ii++){
            let cg = listdata[ii];
            var starCfg = localcache.getItem(localdb.table_jiaoyouStar,cg.star);
            if (starCfg && cg.refreshTime != 0){
                if ((cg.refreshTime + starCfg.shijian) - Utils.timeUtil.second <= 0){
                    return true;
                }
            }
            else if(starCfg && cg.refreshTime == 0){
                rFlag = true;
            }
        }
        if (rFlag){
            var dayCt = this.getDayShouhuCt();
            if (dayCt > this.defendCount){
                return true;
            }
        }
        return false;
    };

    /**检测箱子奖励*/
    this.checkBoxReward = function () {
        let listdata = localcache.getList(localdb.table_jiaoyouWeek);
        for (var ii = 0; ii < listdata.length;ii++){
            let cg = listdata[ii];
            if (this.weekAwardPick.indexOf(cg.id) == -1 && Initializer.jiaoyouProxy.weekdefendCount >= cg.cishu){
                return true;
            }
        }
        return false;
    };



}

exports.JiaoyouProxy = JiaoyouProxy;