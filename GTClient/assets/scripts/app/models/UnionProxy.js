var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
import { DAY_SECOND, UNION_BOSS_CD,MINIGAMEOWNER_TYPE,MINIGAMETYPE } from "GameDefine";

var UnionProxy = function() {

    this.memberInfo = null;
    this.clubInfo = null;
    this.clubList = null;
    this.lookClubInfo = null;
    this.applyList = null;
    this.transList = null;
    this.shopList = null;
    this.bossInfo = null;
    this.fightRank = null;
    this.myFightRankInfo = null;
    this.myClubRank = null;
    this.bossFtList = null;
    this.heroLog = null;
    this.clubLog = null;
    this.dialogParam = null;
    this.changePosParam = null;
    this.openCopyParam = null;
    this.curSelectId = 0;
    this.fighting = !1;
    this.clubTask = null;
    this.clubActive = null;
    this.tmp = {};
    this.banquetId = 120; //宴会资源id
    this.partyResourceData = null;
    this.partyData = null
    this.throwPotData = null;
    this.portPotinfo = null;
    this.requestRedBagFlag = false;
    this.redBagData = null;
    this.randUserData = null;
    this.myCurCid = 0;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.club.clubList, this.onClubList, this);
        JsonHttp.subscribe(proto_sc.club.clubInfo, this.onClubInfo, this);
        JsonHttp.subscribe(proto_sc.club.shopList, this.onShopList, this);
        JsonHttp.subscribe(proto_sc.club.applyList, this.onApplyList, this);
        JsonHttp.subscribe(proto_sc.club.bossInfo, this.onBossInfo, this);
        JsonHttp.subscribe(proto_sc.club.bossft, this.onBossFg, this);
        JsonHttp.subscribe(proto_sc.club.clubLog, this.onClubLog, this);
        JsonHttp.subscribe(proto_sc.club.transInfo, this.onTransInfo, this);
        JsonHttp.subscribe(
            proto_sc.club.memberInfo,
            this.onMemberInfo,
            this
        );
        JsonHttp.subscribe(proto_sc.club.win, this.onWin, this);
        JsonHttp.subscribe(proto_sc.club.uidLog, this.onUidLog, this);
        JsonHttp.subscribe(proto_sc.club.myClubRid, this.onClubRank, this);
        JsonHttp.subscribe(proto_sc.club.heroLog, this.onHeroLog, this);

        JsonHttp.subscribe(proto_sc.club.clubTask, this.onClubTask, this);

        JsonHttp.subscribe(proto_sc.club.active, this.onActive, this);

        JsonHttp.subscribe(proto_sc.club.partyResource, this.onPartyResource, this);
        JsonHttp.subscribe(proto_sc.club.party, this.onParty, this);
        JsonHttp.subscribe(proto_sc.club.redBag, this.onRedBag, this);
        JsonHttp.subscribe(proto_sc.club.throwPot, this.onThrowPot, this);
        JsonHttp.subscribe(proto_sc.club.randUser, this.onRandUser, this);
        JsonHttp.subscribe(proto_sc.club.myCid, this.onMyCid, this);

    };
    this.clearData = function() {
        this.memberInfo = null;
        this.clubInfo = null;
        this.clubList = null;
        this.lookClubInfo = null;
        this.applyList = null;
        this.transList = null;
        this.shopList = null;
        this.bossInfo = null;
        this.fightRank = null;
        this.myFightRankInfo = null;
        this.myClubRank = null;
        this.dialogParam = null;
        this.changePosParam = null;
        this.openCopyParam = null;
        this.bossFtList = null;
        this.curSelectId = 0;
        this.heroLog = null;
        this.clubLog = null;
        this.clubTask = null;
        this.clubActive = null;
        l.change("unionCopy", !1);
        l.change("unionApply", !1);
        this.banquetId = 120;
        this.partyResourceData = null;
        this.partyData = null;
        this.throwPotData = null;
        this.portPotinfo = null;
        this.requestRedBagFlag = false;
        this.redBagData = null;
        this.randUserData = null;
        this.myCurCid = 0;
    };





    //---------------add by xujs----------------
    this.getPotAward = function(){
        this.sendPickAward()
    }

    this.isEnterPotOrPotInfo = function(){
        let state = this.partyState()
        if(state === 3){
            i.utils.openPrefabView("union/FlowerPot");
        }else if(state === 4 || state === 1){
            i.utils.openPrefabView("union/FlowerInfo");
        }
    }

    this.get00time = function(){
        if(this.partyResourceData && this.partyResourceData.startTime !== 0){
            let _00time = new Date(new Date(this.partyResourceData.startTime*1000).setHours(0, 0, 0, 0)).getTime()/1000
            return _00time
        }
    }


    //获取当前宴会的状态
    //返回值 1未开启，2已开启但不能进入，3开启并且可进入，4已关闭
    this.partyState = function(curent){
        if(!this.partyResourceData || this.partyResourceData.startTime == 0){
            //返回状态未开启
            return 1
        }
        let now =  curent || i.timeUtil.second
        let _00time = this.get00time()
        let cbegan = _00time+(60*60*25)
        let endtime = _00time+(60*60*47.5)
        let eeend = _00time+(60*60*48)
        let state = 1
        now>=eeend?state=1:now>=endtime?state=4:now>=cbegan?state=3:state=2
        return state
    }

    //可以进入宴会剩余时间  返回时间单位秒
    this.partyEnTRemTime = function(){
        let _00time = this.get00time()
        let cbegan = _00time+(60*60*25)
        let now = i.timeUtil.second
        let ct = cbegan-now
        return ct>0?ct:0
    }   


    //宴会结束剩余时间  返回时间单位秒
    this.partyEndRemTime = function(){
        let _00time = this.get00time()
        let endtime = _00time+(60*60*47.5)
        let now = i.timeUtil.second
        let ct = endtime-now
        return ct>0?ct:0
    }   
    //---------------end------------------



    /**宴会资源数据*/
    this.onPartyResource = function(data){
        if (this.partyResourceData == null){
            this.partyResourceData = data;
        }
        else{
            for (let key in data){
                this.partyResourceData[key] = data[key];
            }
        }
        this.initPartyRed();
        this.initEnterPartyRed();
        facade.send("UNION_RESOURCEBASE")
    };

    this.initPartyRed = function(){
        if (this.partyResourceData == null || this.memberInfo == null || this.memberInfo.cid == 0){
            l.change("union_party_red",false);
            return;
        }
        let state = this.partyState();
        if (state == 1 && (this.memberInfo.post == 1 || this.memberInfo.post == 2)){
            if (n.bagProxy.getItemCount(this.banquetId) >= i.utils.getParamInt("club_partyRes")){
                l.change("union_party_red",true);
                return;
            }                    
        }
        l.change("union_party_red",false);
    };

    /**宴会数据*/
    this.onParty = function(data){
        if (this.partyData == null){
            this.partyData = data;
        }
        else{
            for (let key in data){
                this.partyData[key] = data[key];
            }
        }
        this.initEnterPartyRed();
        facade.send("UNION_PARTY")
    };

    this.initEnterPartyRed = function(){
        if (this.partyResourceData == null || this.partyData == null || this.memberInfo == null || this.memberInfo.cid == 0 || this.partyData.isHookPick == 1){
            l.change("union_enter",false);
            return;
        }
        let state = this.partyState();
        let club_partyOnhookTime = i.utils.getParamInt("club_partyOnhookTime");
        let flag = state == 3 && this.partyData.hookStart + club_partyOnhookTime <= i.timeUtil.second
        l.change("union_enter",flag);
        if (state == 3 && this.partyData.hookStart + club_partyOnhookTime > i.timeUtil.second){
            let self = this;
            i.timeUtil.addCountEvent(this.memberInfo && this.memberInfo.cid != 0, this.partyData.hookStart + club_partyOnhookTime, "union_enter", () => {
                self.initEnterPartyRed();
            });
        }
        
    };

    this.onMyCid = function(data){
        this.myCurCid = data.cid;
    };

    /**红包数据*/
    this.onRedBag = function(data){
        this.redBagData = data;
        facade.send("UNION_REDBAG_DATA")
    };

    /**投壶数据*/
    this.onThrowPot = function(data){
        this.throwPotData = data
    };

    /**随机做小游戏的玩家*/
    this.onRandUser = function(data){
        this.randUserData = data;
    };

    this.onWin = function(t) {
        this.fightBossInfo = t.cbosspkwin;
    };
    this.onHeroLog = function(t) {
        this.heroLog = t;
        this.heroLog.sort(this.sortHeroLog);
        facade.send("UNION_RECORD_UPDATE");
    };
    this.sortHeroLog = function(a, b) {
        return b.hit - a.hit;
    };
    this.onClubLog = function(t) {
        this.clubLog = t;
        //this.clubLog.sort(this.sortHeroLog);
        facade.send("UNION_CLUB_LOG_UPDATE");
    };
    this.onBossFg = function(t) {
        this.bossFtList = t;
        this.updateCopyRed();
        facade.send("UNION_FT_LIST_UPDATE");
    };
    this.onUidLog = function(t) {
        this.fightRank = t;
        for (var e = 0; e < t.list.length; e++) {
            t.list[e].rid = e + 1;
            t.list[e].uid == n.playerProxy.userData.uid &&
                (this.myFightRankInfo = t.list[e]);
        }
        if (null == this.myFightRankInfo) {
            var o = {};
            o.name = n.playerProxy.userData.name;
            o.hit = 0;
            o.rid = 0;
            o.gx = 0;
            this.myFightRankInfo = o;
        }
    };
    this.onBossInfo = function(t) {
        this.bossInfo = t;
        this.updateCopyRed();
        facade.send("UPDATE_BOSS_INFO");
    };
    //公会战红点
    this.updateCopyRed = function() {
        let result = false;
        if(null == this.memberInfo || this.memberInfo.cid == 0) { //不在公会
            result = false;
        } else if (!this.checkNewTime()) { //新人进来24小时不能打
            result = false;
            let self = this;
            i.timeUtil.addCountEvent(this.memberInfo && this.memberInfo.cid != 0, this.memberInfo.inTime + DAY_SECOND, "unionCopy", () => {
                self.updateCopyRed();
            });
        } else {
            let bossInfo = this.bossInfo;
            let bStart = bossInfo.startBossTime != 0 && bossInfo.startBossTime + UNION_BOSS_CD > i.timeUtil.second;
            if(bStart) { //公会战开始时间内i
                if(null == this.bossInfo || this.bossInfo.bosshp <= 0) { //公会boss已被打死
                    result = false;
                } else {
                    let tmpArray = [];
                    let servantList = n.servantProxy.getServantList();
                    for (var j = 0, jLen = servantList ? servantList.length : 0; j < jLen; j++) {
                        let heroInfo = servantList[j];
                        if (null != heroInfo) {
                            tmpArray.push(1);
                        }
                    }
                    if(null != this.bossFtList && this.bossFtList.length >= tmpArray.length) {
                        for(let k = 0, kLen = this.bossFtList.length; k < kLen; k++) {
                            if(this.bossFtList[k].h == 1) { //有伙伴活着
                                result == true;
                                break;
                            }
                        }
                    } else {
                        result = true; // 没全死光
                    }
                }
            }
        }
        l.change("unionCopy", result);
    };
    this.onMemberInfo = function(t) {
        this.memberInfo = t;
        0 == t.cid && l.change("unionCopy", !1);
        this.initPartyRed();
        this.initEnterPartyRed();
        this.initInteriorRed();
        if (t.cid == 0){
            this.clubInfo = null;
        }
        else
            facade.send("UPDATE_MEMBER_INFO");
    };

    /**返回帮会每日活跃数据*/
    this.onActive = function(data){
        this.clubActive = data;
        this.checkUnionRed();
        facade.send("UPDATE_CLUB_TODAYACTIVE");
    };

    /**获取当前类型捐献的次数*/
    this.getCurrentDonateTimesById = function(id){
        let data = this.memberInfo;
        if (data && data.donate && data.donate[id] != null){
            return data.donate[id]
        }
        return 0;
    };

    /**检测内务府捐献是否有足够银两*/
    this.initInteriorRed = function(){
        if (this.memberInfo == null || this.memberInfo.cid == 0){
            l.change("union_donate",false);
            return;
        }
        let cfg = localcache.getItem(localdb.table_donate, 1);
        let count = n.bagProxy.getItemCount(cfg.pay[0].id);
        let curnum = this.getCurrentDonateTimesById(cfg.id);
        let renum = cfg.time - curnum;
        if (renum > 0 && count >= cfg.pay[0].count){
            l.change("union_donate",true);
            return;
        }
        l.change("union_donate",false);   
    };

    this.onClubInfo = function(t) {
        this.clubInfo = t;
        this.tmp = {}
        for (let ii = 0; ii < t.members.length; ii++){
            this.tmp[t.members[ii].id] = t.members[ii]
        }
        facade.send("UPDATE_SEARCH_INFO");
    };
    this.onClubList = function(t) {
        this.clubList = t;
        if (this.clubInfo)
            for (var e = 0; e < this.clubList.length; e++)
                if (this.clubList[e].id == this.clubInfo.id) {
                    this.lookClubInfo = this.clubList[e];
                    break;
                }
        facade.send("UPDATE_CLUB_RANK");
    };
    this.onApplyList = function(t) {
        this.applyList = t;
        var e = t ? t.length : 0;
        if (this.memberInfo && this.memberInfo.cid > 0 && this.memberInfo.post == 1){
            l.change("unionApply", e > 0);
        }
        else{
            l.change("unionApply", false);
        }
        facade.send("UPDATE_APPLY_LIST");
    };
    this.onTransInfo = function(t) {
        this.transList = t;
        facade.send("UPDATE_TRANS_LIST");
    };
    this.onClubRank = function(t) {
        this.myClubRank = t;
        facade.send("UPDATE_MY_RANK");
    };
    this.onShopList = function(t) {
        if (t && t.length > 0){
            t.sort((a,b)=>{
                if (a.lock == b.lock){
                    return a.id < b.id ? -1 : 1;
                }
                else{               
                    return a.lock < b.lock ? -1 : 1;
                }
            })
        }       
        this.shopList = t;
        facade.send("UPDATE_SHOP_LIST");
    };

    /**帮会任务*/
    this.onClubTask = function(vo){
        this.clubTask = vo;
        // this.checkTaskTaskRed();
        // this.checkTaskDonateRed();
        // this.checkTaskTributeRed();
        this.checkUnionRed();
        facade.send("UPDATE_CLUB_TASK");
    };

    /**当前任务的完成数量*/
    this.getUnionTaskFinishNumById = function(id){
        let data = this.clubTask;
        let listDic = data.list ? data.list : {};
        return listDic[id] ? listDic[id] : 0;
    };

    /**是否已经完成任务*/
    this.isFinishedByTask = function(id){
        let data = this.clubTask;
        let listGet = data.get ? data.get : {};
        return listGet[id] != null;
    };

    /**请求领取任务*/
    this.sendGetTaskRwd = function(dcid){
        var o = new proto_cs.club.getTaskRwd();
        o.dcid = dcid;
        JsonHttp.send(o, function() {
            n.timeProxy.floatReward();
        });       
    };

    /**请求是否有公会*/
    this.sendGetMyCid = function (callback) {
        var o = new proto_cs.club.getMyCid();
        JsonHttp.send(o, function() {
            callback && callback();
        });       
    }

    /**领取每日活跃*/
    this.sendPickActiveAward = function(id){
        var o = new proto_cs.club.pickActiveAward();
        o.id = id;
        JsonHttp.send(o, function() {
            n.timeProxy.floatReward();
        });           
    };

    /**请求建筑升级*/
    this.sendCluBuildingUp = function(bId){
        var o = new proto_cs.club.cluBuildingUp();
        o.bId = bId;
        JsonHttp.send(o); 
    };

    this.sendTran = function(t, e) {
        var o = new proto_cs.club.transWang();
        o.fuid = e;
        o.password = t;
        JsonHttp.send(o);
    };
    this.sendSearchUnion = function(t) {
        var e = new proto_cs.club.clubFind();
        e.cid = t;
        JsonHttp.send(e);
    };
    this.sendAllowRandomJoin = function(t) {
        var e = new proto_cs.club.isJoin();
        e.join = t;
        JsonHttp.send(e);
    };
    this.sendReject = function(t) {
        void 0 === t && (t = 0);
        var e = new proto_cs.club.noJoin();
        e.fuid = t;
        JsonHttp.send(e);
    };
    this.sendApplyJoin = function(t) {
        var e = new proto_cs.club.yesJoin();
        e.fuid = t;
        JsonHttp.send(e);
    };
    this.sendInfoMod = function(t, e, o, i) {
        var n = new proto_cs.club.clubInfoSave();
        n.qq = t;
        n.laoma = e;
        n.notice = o;
        n.outmsg = i;
        JsonHttp.send(n);
    };

    this.sureSendJiesan = function(t) {
        var e = new proto_cs.club.delClub();
        e.password = t;
        let self = this;
        JsonHttp.send(e, function() {
            i.utils.closeNameView("union/NewUnionMain");
            n.chatProxy.clubMsg = [];
            self.clubInfo = null;
            l.change("union_red",false);
            l.change("unionCopy",false);
            l.change("union_party_red",false);
            l.change("unionApply",false);
            l.change("union_enter",false);
            l.change("union_donate",false);
        });
    };

    this.sendJiesan = function(t) {
        let e = null
        if(this.clubInfo.dissolutionTime === 0){
            e = new proto_cs.club.pre_delClub();
        }else{
            e = new proto_cs.club.cancel_delClub();
        }
        JsonHttp.send(e, (m)=>{
            m && (this.clubInfo.dissolutionTime = m.a.club.clubInfo.dissolutionTime);
            facade.send("UPDATE_SEARCH_INFO")
        });


    };
    this.sendModifyName = function(t, e) {
        var o = new proto_cs.club.clubName();
        o.name = t;
        o.type = e;
        var n = this;
        JsonHttp.send(o, function() {
            n.clubInfo.name == t &&
                i.alertUtil.alert18n("UNION_CHANGE_SUCCESS");
        });
    };
    this.sendCovert = function(t) {
        var e = new proto_cs.club.shopBuy();
        e.id = t;
        JsonHttp.send(e, function() {
            facade.send("UNION_SHOP_UPDATE");
            n.timeProxy.floatReward();
        });
    };
    this.sendChangePos = function(t, e) {
        var o = new proto_cs.club.memberPost();
        o.fuid = t;
        o.postid = e;
        var n = this.changePosParam;
        JsonHttp.send(o, function(t) {
            1 == t.s &&
                i.alertUtil.alert("union_change_tip" + e, {
                    name: n.name
                });
        });
    };
    this.sendReqOpen = function(type) {
        var o = new proto_cs.club.clubBossOpen();
        //o.cbid = t;
        o.type = type;
        JsonHttp.send(o, function() {
            facade.send("UNION_OPEN_COPY_RESULT");
        });
    };
    this.sendBuild = function(t) {
        var e = new proto_cs.club.dayGongXian();
        e.dcid = t;
        JsonHttp.send(e, function(e) {
            // if (1 == e.s) {
            //     var o = localcache.getItem(localdb.table_construction, t);
            //     i.alertUtil.alert(
            //         i18n.t("UNION_EXP_TXT_2") + "+" + o.get.exp
            //     );
            //     i.alertUtil.alert(
            //         i18n.t("UNION_MONEY_TXT_2") + "+" + o.get.fund
            //     );
            //     i.alertUtil.alert(
            //         i18n.t("UNION_GE_REN_GONG_XIAN_2", {
            //             num: o.get.gx
            //         })
            //     );
            // }
        });
    };

    /**请求帮会贡献*/
    this.sendDayGongXian = function(dcid){
        var e = new proto_cs.club.dayGongXian();
        e.dcid = dcid;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };

    this.reqRankList = function() {
        var reqData = new proto_cs.club.clubBosslog();
        JsonHttp.send(reqData, () => {
            i.utils.openPrefabView("union/UnionCopyRank");
        });
    };

    this.sendHitList = function(t) {
        var e = new proto_cs.club.clubBossHitList();
        e.id = t;
        JsonHttp.send(e, function() {
            i.utils.openPrefabView("union/UnionHurtRank");
        });
    };
    this.sendTranList = function() {
        var t = new proto_cs.club.transList();
        JsonHttp.send(t);
    };
    this.sendApplyUnion = function(t) {
        var e = this,
            o = new proto_cs.club.clubApply();
        o.cid = t;
        JsonHttp.send(o, function() {
            if (e.memberInfo && 0 != e.memberInfo.cid) {
                i.utils.isOpenView("union/UnionView") &&
                    i.utils.closeNameView("union/UnionView");
                i.utils.isOpenView("union/UnionInfo") &&
                    i.utils.closeNameView("union/UnionInfo");
                i.utils.isOpenView("union/UnionSearch") &&
                    i.utils.closeNameView("union/UnionSearch");
                i.utils.isOpenView("union/UnionRank") &&
                    i.utils.closeNameView("union/UnionRank");
                i.utils.openPrefabView("union/UnionMain");
            }
        });
    };
    this.sendCreateUnion = function(t, e, o, i, l, r) {
        var a = new proto_cs.club.clubCreate();
        a.isJoin = r ? 1 : 0;
        a.laoma = o;
        a.name = t;
        a.outmsg = l;
        a.qq = e;
        a.password = i;
        var s = this;
        JsonHttp.send(a, function(data) {
            if(null != data && null != data.a && null != data.a.system && null != data.a.system.errror) {
                return;
            }
            s.enterUnion();
            n.chatProxy.sendChat(i18n.t("SYS_HELLO_CHAT"), 1);
        });
    };
    this.sendOut = function() {
        var t = this;
        JsonHttp.send(new proto_cs.club.outClub(), function() {
            t.enterUnion();
            n.chatProxy.clubMsg = [];
            l.change("union_red",false);
            l.change("unionCopy",false);
            l.change("union_party_red",false);
            l.change("unionApply",false);
            l.change("union_enter",false);
            l.change("union_donate",false);
        });
        1 != this.memberInfo.post && (this.clubInfo = null);
    };
    this.sendRankList = function(t) {
        var e = new proto_cs.club.clubList();
        e.cid = t;
        JsonHttp.send(e, function() {
            i.utils.openPrefabView("union/UnionRank");
        });
    };
    this.sendRandomAdd = function() {
        var t = new proto_cs.club.clubRand(),
            e = this;
        JsonHttp.send(t, function() {
            e.enterUnion();
        });
    };
    this.sendShopList = function(callback) {
        var t = new proto_cs.club.shopList();
        JsonHttp.send(t, function() {
            callback && callback();
        });
    };
    this.sendBossList = function() {
        var t = new proto_cs.club.clubBossInfo();
        JsonHttp.send(t, function() {
            i.utils.openPrefabView("union/UnionBossLobbyView");
        });
    };
    this.sendHeroFuhuo = function(t) {
        var e = new proto_cs.club.clubHeroCone();
        e.id = t;
        JsonHttp.send(e, function(t) {
            1 == t.s && i.alertUtil.alert18n("UNION_FU_HUO_SUCCESS");
        });
    };
    this.sendFightBoss = function(bossId, heroId) {
        var reqData = new proto_cs.club.clubBossPK();
        reqData.cbid = bossId;
        reqData.id = heroId;
        JsonHttp.send(reqData, (rspData) => {
            if(null == rspData.a.system || (null != rspData.a.system && null == rspData.a.system.errror)) {
                facade.send("UNION_BOSS_ATK");
            }
        });
    };
    this.sendApplyList = function() {
        JsonHttp.send(new proto_cs.club.applyList());
    };
    this.sendGetMemberInfo = function(t) {
        var e = new proto_cs.club.clubMemberInfo();
        e.cid = t;
        JsonHttp.send(e);
    };
    this.sendGetBossRecord = function(t) {
        var e = new proto_cs.club.clubBossPKLog();
        e.cbid = t;
        JsonHttp.send(e);
    };
    this.enterUnion = function() {
        if (this.memberInfo && this.memberInfo.cid > 0) {
            i.utils.openPrefabView("union/NewUnionMain");
            facade.send("UNION_CREATE_SUCESS");
        } else i.utils.openPrefabView("union/UnionView");
    };
    this.getMengzhu = function(t) {
        for (var e = 0; e < t.length; e++) if (1 == t[e].post) return t[e];
        return null;
    };
    this.getUnionLvMaxCount = function(t) {
        var e = this.getUnionData(t);
        return e ? e.maxMember : 0;
    };
    this.getUnionLvExp = function(t) {
        for (var e = 0, o = 1; o < t + 1; o++) {
            var i = this.getUnionData(o);
            e += i ? i.exp : 0;
        }
        return e;
    };
    this.getUnionData = function(t) {
        return localcache.getItem(localdb.table_union, t);
    };
    this.getPostNum = function(t) {
        var e = 0;
        if (null == this.clubInfo) return 0;
        var o = this.clubInfo.members;
        if (0 == o.length) return 0;
        for (var i = 0; i < o.length; i++) o[i].post == t && e++;
        return e;
    };

    this.getPostIsPp = function(t,id) {
        var e = 0;
        if (null == this.clubInfo) return 0;
        var o = this.clubInfo.members;
        if (0 == o.length) return 0;
        for (var i = 0; i < o.length; i++){
            if(id == o[i].id && o[i].post == t){
                return 1
            }
        }
        return e;
    };

    this.getPostion = function(t) {
        switch(t) {
            case 1:
            case 2:
                return i18n.t("union_pos" + t);
            default:
                return i18n.t("union_pos3");
        }
    };
    
    this.getAllShili = function(t) {
        for (var e = 0, o = 0; o < t.length; o++)
            e += t[o] ? t[o].shili : 0;
        return e;
    };
    this.getHeroFightData = function(t) {
        for (var e = null, o = 0; o < this.bossFtList.length; o++)
            if (this.bossFtList[o].id == t) {
                e = this.bossFtList[o];
                break;
            }
        return e;
    };
    this.getClubLog = function(t) {
        var e = "";
        switch (t.type) {
            case 1:
                var o = localcache.getItem(
                    localdb.table_construction,
                    t.num1
                );
                e = i18n.t("UNION_JIN_XING_YI_CI", {
                    str: o.msg,
                    exp: o.get.exp,
                    rich: o.get.fund,
                    gx: o.get.gx
                });
                break;

            case 2:
                e = i18n.t("UNION_GENG_GAI_GONG_GAO");
                break;

            case 3:
                var i = localcache.getItem(localdb.table_unionBoss, t.num1);
                e = i18n.t("UNION_JI_SHA_LE", {
                    name: i.name,
                    exp: i.rwd.exp
                });
                break;

            case 4:
                var n = localcache.getItem(localdb.table_unionBoss, t.num1);
                e = i18n.t("UNION_KAI_QI_FU_BEN_TXT", {
                    name: n.name
                });
                break;

            case 5:
                var l = this.getPostion(t.num2);
                e = i18n.t("UNION_ZHI_WEI_BIAN_GENG", {
                    name1: t.name,
                    name2: l
                });
                break;

            case 6:
                e = i18n.t("UNION_GENG_GAI_MING_ZI");
                break;

            case 7:
                e = i18n.t("UNION_ZHU_CHU_LIAN_MENG", {
                    name: t.name
                });
                break;

            case 8:
                e = i18n.t("UNION_GONG_DIAN_SHENG_JI", {
                    num: t.num1
                });
                break;

            case 9:
                e = i18n.t("UNION_JIA_RU_TXT");
                break;

            case 10:
                e = i18n.t("UNION_LI_KAI_GONG_DIAN");
                break;

            case 11:
                e = i18n.t("UNION_JIA_RU_TXT");
                break;
            case 13:{//建筑升级
                //%{position} %{name} 将 %{item} 升级至 %{num2}级.
                let buildname = ""
                if (t.num2 == 1){
                    buildname = i18n.t("UNION_BUILDING_2")
                }
                else if(t.num2 == 2){
                    buildname = i18n.t("UNION_BUILDING_5")
                }
                e = i18n.t("UNION_LOG_TYPE_13",{
                    position: this.getPostion(t.num1),
                    name: t.name,
                    item:buildname,
                    num2:t.num3
                })
            }break;
            case 14:{
                //%{name} 捐献了 %{num1} 个 %{item},宫殿资金增加%{num2}

                e = i18n.t("UNION_TIPS15",{v1:`<img src='currency_${t.num2}' />` + t.num1});
            }
            break;
        }
        return e;
    };

    //进入 工会主界面
    this.enterUnionMainView = function(){
        let self = this;
        this.sendGetMyCid(function(){
            if (self.myCurCid != 0){
                self.sendGetUserClubInfo(function(){
                    self.enterUnion();
                })
            }
            else{
                i.utils.openPrefabView("union/UnionView");
            }
        })
    }
    //获取 工会最新数据
    this.sendGetUserClubInfo = function(callback){
        var e = new proto_cs.club.getUserClubInfo();
        JsonHttp.send(e, callback);
    };

    /**检测帮会的红点*/
    this.checkUnionRed = function(){
        l.change("union_red",false);
        let clubActive = this.clubActive;
        let score = clubActive ? clubActive.score : 0;
        let listGet = clubActive ? clubActive.get : [];
        let listCfg = localcache.getList(localdb.table_union_dailyRwd);
        for (let ii = 0; ii < listCfg.length;ii++){
            let cg = listCfg[ii];
            if (score >= cg.need && listGet.indexOf(cg.id) == -1){
                l.change("union_red",true);
                return;
            }
        }

        let listTaskCfg = localcache.getList(localdb.table_union_task);
        for (let ii = 0; ii < listTaskCfg.length;ii++){
            let cg = listTaskCfg[ii];
            let cNum = this.getUnionTaskFinishNumById(cg.id);
            let isFinish = this.isFinishedByTask(cg.id);
            if (!isFinish && cNum >= cg.set[0]){
                l.change("union_red",true);
                return;
            }
        }
        
    };

    /**获取资源的基础信息*/
    this.sendGetResourceBaseInfo = function(){
        var e = new proto_cs.club.getResourceBaseInfo();
        JsonHttp.send(e);
    };


    /**获取宴会基础信息*/
    this.sendGetPartyBaseInfo = function(funcs){
        var e = new proto_cs.club.getPartyBaseInfo();
        JsonHttp.send(e,(t)=>{
            console.log(t)
            if(funcs){
                funcs()
            }
        });
    };

    /**提交资源*/
    this.sendSubmitResource = function(){
        var e = new proto_cs.club.submitResource();
        JsonHttp.send(e,function(){
            n.timeProxy.floatReward();
        });
    };

    /**购买提交资源的次数*/
    this.sendBuyCount = function(){
        var e = new proto_cs.club.buyCount();
        JsonHttp.send(e);
    };

    /**刷新资提交资源列表*/
    this.sendRefreshList = function(){
        var e = new proto_cs.club.refreshList();
        JsonHttp.send(e);
    };

    /**开启宴会
    *@param id 开启宴会的档次
    */
    this.sendOpenParty = function(id,callback){
        console.log('sendOpenParty')
        var e = new proto_cs.club.openParty();
        e.id = id;
        JsonHttp.send(e,function(rspData){
            if (null != rspData && null != rspData.a && null != rspData.a.system && null != rspData.a.system.errror){
                return;
            }
            n.timeProxy.itemReward = null;
            callback && callback();
        });
    };


    //判断是否可以领取奖励
    this.checkIsCanGet = function(){
        let state = this.partyState()
        if( (state === 4 || state ===1 ) && (this.portPotinfo && this.portPotinfo.isPick === 0 && this.portPotinfo.isThrow === 1)){
            this.getPotAward()
        }
    }

    /**
     * getThrowInfo 获取投壶是否奖励
     */
    this.sendgetThrowInfo = function(){
        let state = this.partyState()
        if(state === 2 || state === 3){
            return
        }
        var e = new proto_cs.club.getThrowInfo();
        JsonHttp.send(e,(t)=>{
            this.portPotinfo = t.a.club.party
            this.checkIsCanGet()
        });
    };
    

    /**参加宴会*/
    this.sendJoinParty = function(callback){
        var e = new proto_cs.club.joinParty();
        JsonHttp.send(e,function(rspData){
            if (null != rspData && null != rspData.a && null != rspData.a.system && null != rspData.a.system.errror){
                return;
            }
            callback && callback();
        });
    };

    /**更换乐师
    *@param id 乐师id
    */
    this.sendChangeMusician = function(id){
        var e = new proto_cs.club.changeMusician();
        e.id = id;
        JsonHttp.send(e);
    };

    /**购买特效
    *@param id 购买的特效id
    */
    this.sendBuyBuff = function(id){
        var e = new proto_cs.club.buyBuff();
        e.id = id;
        JsonHttp.send(e);
    };


    /**抢红包
    */
    this.sendRobRedBag = function(callback){
        var e = new proto_cs.club.robRedBag();
        JsonHttp.send(e,function(rspData){
            //n.timeProxy.floatReward();
            if (null != rspData && null != rspData.a && null != rspData.a.system && null != rspData.a.system.errror){
                return;
            }
            callback && callback();
        });
    };

    /**开始挂机*/
    this.sendStartHook = function(callback){
        var e = new proto_cs.club.startHook();
        JsonHttp.send(e,function(rspData){
            if (null != rspData && null != rspData.a && null != rspData.a.system && null != rspData.a.system.errror){
                return;
            }
            callback && callback();
        });
    };

    /**领取挂机奖励*/
    this.sendPickHookAward = function(){
        var e = new proto_cs.club.pickHookAward();
        JsonHttp.send(e,function(){
            n.timeProxy.floatReward();
        });
    };

    /**投壶*/
    this.sendThrowPot = function(id,func){
        let e = new proto_cs.club.throwPot();
        e.id = id
        JsonHttp.send(e,()=>{
            if(func){
                func()
            }
        });
    };

    /**领取投壶奖励*/
    this.sendPickAward = function(){
        var e = new proto_cs.club.pickAward();
        JsonHttp.send(e,()=>{
            i.utils.openPrefabView("union/FlowerInfo");
        });
    };

    /**随机玩家*/
    this.sendRandGameUser = function(callback){
        var e = new proto_cs.club.randGameUser();
        JsonHttp.send(e,function(rspData){
            callback && callback()
        });
    };

    /**领取游戏奖励*/
    this.sendPickGamesAward = function(){
        var e = new proto_cs.club.pickGamesAward();
        JsonHttp.send(e,function(){
            n.timeProxy.floatReward();
        });
    };

    /**刷新红包消息*/
    this.sendUpdateClubInfo = function(callback){
        var e = new proto_cs.club.updateClubInfo();
        JsonHttp.send(e,function(){
            callback && callback();
        });      
    }

    //获取boss上阵列表 prop: 当前打的boss的属性
    this.getCanFightBoss = function(prop) {
        let tmpArray = [];
        let servantList = n.servantProxy.getServantList();
        for (var j = 0, jLen = servantList ? servantList.length : 0; j < jLen; j++) {
            let heroInfo = servantList[j];
            if (null != heroInfo) {
                let data = {};
                i.utils.copyData(data, heroInfo);
                tmpArray.push(data);
            }
        }
        if(null != this.bossFtList && this.bossFtList.length > 0) {
            for(let k = 0, kLen = tmpArray.length; k < kLen; k++) {
                let fightInfo = this.bossFtList.filter((tmpData) => {
                    return tmpData.id == tmpArray[k].id;
                });
                tmpArray[k].fightInfo = null != fightInfo && fightInfo.length > 0 ? fightInfo[0] : null;
            }
        }

        let sortFunc = (a, b) => {
            return b.aep["e" + prop] - a.aep["e" + prop];
        }
        //活着的按属性从大到小排序
        let result = tmpArray.filter((tmpData) => {
            return null == tmpData.fightInfo || tmpData.fightInfo.h == 1;
        });
        result.sort(sortFunc);
        //死了的按属性从大到小排序
        let deathArray = tmpArray.filter((tmpData) => {
            return null != tmpData.fightInfo && tmpData.fightInfo.h != 1;
        });
        deathArray.sort(sortFunc);
        result = result.concat(deathArray);
        return result;
    };

    /**判断当前宴会是否已开启*/
    this.checkPartyOpen = function(){
        let data = this.partyResourceData;
        console.log(data)
        if (data == null || data.startTime == null) return false;
        if (data.startTime == 0) return false;
        let num = i.timeUtil.getSecondToZeroByFixTime(data.startTime);
        let rNum = i.timeUtil.second - data.startTime
        if (rNum > num + 3600 && rNum < num + 23 * 3600 + 1800){
            return true;
        }
        return false;
    };
    /**获取开启或结束的时间
    *@param isopen 是否为开启时间
    */
    this.getOpenOrEndTime = function(isOpen){
        let data = this.partyResourceData;
        if (data == null || data.startTime == null || data.startTime == 0) return 0;
        let num = i.timeUtil.getSecondToZeroByFixTime(data.startTime);
        if (isOpen){
            return num + 3600 + data.startTime;
        }
        else{
            return num + 23.5 * 3600 + data.startTime;
        }
    };

    //判断是否新加入公会24小时内
    this.checkNewTime = function() {
        if(null == this.memberInfo) {
            return false;
        }
        return i.timeUtil.second >= (this.memberInfo.inTime + DAY_SECOND);
    };

    /**检测当前是否有红包*/
    this.getRedBagNum = function(){
        let data = this.redBagData;
        if (data == null) return 0;
        let getNum = 0
        if (data.robLog){
            for (let key in data.robLog){
                let cg = data.robLog[key]
                for (let ii = 0; ii < cg.length; ii++){
                    if (cg[ii].uid == n.playerProxy.userData.uid){
                        getNum++;
                    }
                }
            }
        }
        
        let maxNum = i.utils.getParamInt("club_giftTimes");
        if (getNum >= maxNum) return 0;
        let sum = 0;
        for (let uid in data.redlist){
            if (data.robLog[uid] == null){
                sum++;
            }
            else{
                if (data.robLog[uid].length < data.redlist[uid].items.length && !this.checkHasGetRedBag(data.robLog[uid])){
                    sum++;
                }
            }
        }
        return sum;
    };

    /**检测自己是否已经领取过红包*/
    this.checkHasGetRedBag = function(data){
        let flag = false;
        for (let ii = 0; ii < data.length;ii++){
            let cg = data[ii];
            if (cg.uid == n.playerProxy.userData.uid){
                flag = true;
                break;
            }
        }
        return flag;
    };

    /**随机一个小游戏*/
    this.getRandomMiniGame = function(randomIdx,data){
        let servantList = n.servantProxy.getServantList();
        let randomHeroData = servantList[Math.floor(Math.random() * servantList.length)];
        if (randomIdx == 1){
            n.servantProxy.rightData = {heroId:randomHeroData.id,qaType:MINIGAMETYPE.CAIMI}
            i.utils.openPrefabView("spaceGame/QuestionView",null,{type:MINIGAMEOWNER_TYPE.UNION_PARTY,player:data});
        }
        else if(randomIdx == 2){
            n.servantProxy.rightData = {heroId:randomHeroData.id,qaType:MINIGAMETYPE.DUISHI}
            i.utils.openPrefabView("spaceGame/QuestionView",null,{type:MINIGAMEOWNER_TYPE.UNION_PARTY,player:data});
        }
        else{
            i.utils.openPrefabView("partner/UIFingerGuessGame",null,{id:randomHeroData.id,type:MINIGAMEOWNER_TYPE.UNION_PARTY,player:data});
        }
    };

    this.getRandomMinGameIdxAndName = function(){
        let randomIdx = Math.floor(Math.random() * 100);
        if (randomIdx <= 33){
            return [1,i18n.t("MINIGAME_CAIMI")];
        }
        else if(randomIdx <= 66){
            return [2,i18n.t("MINIGAME_DUISHI")];
        }
        else{
            return [3,i18n.t("MINIGAME_CAIQUAN")];
        }
    };

    this.getPartyLvDes = function(){
        switch(this.partyResourceData.partyLv){
            case 1:{//初级宴会
                return "primary";
            }
            case 2:{//中级宴会
                return "interme";
            }
            case 3:{//高级宴会
                return "senior";
            }
        }
    };

    /**宴会结束关闭宴会界面*/
    this.closeParty =function(){
        n.guideProxy.guideUI.clearRedBagTime();
        i.utils.closeNameView("union/UIUnionPartyMain");
        i.utils.closeNameView("union/UnionMoodUpView");
        i.utils.closeNameView("union/UnionRedPackage");
        i.utils.closeNameView("spaceGame/QuestionView");
        i.utils.closeNameView("partner/UIFingerGuessGame");
        i.utils.closeNameView("union/FlowerPot");
        i.utils.closeNameView("union/FlowerInfo");               
        l.change("union_party_red",false);
        l.change("union_enter",false);
        i.alertUtil.alert18n("UNION_TIPS36");
    }

}
exports.UnionProxy = UnionProxy;
