var i = require("Initializer");
var n = require("Utils");
var l = require("TimeProxy");

import { FIGHTBATTLETYPE ,BATTLE_ATTACK_OWNER} from "GameDefine";


var FightProxy = function() {

    this.battleData = null;
    this.bossData = null;
    this.CTData = null;
    this.isBoss = !1;
    this.bChapterEnd = false;
    this.fTroops = null;
    this.jTroops = null;
    this.pveBaseData = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.user.win, this.onSetWinData, this);
        JsonHttp.subscribe(proto_sc.user.pvb, this.onPvbList, this);
        JsonHttp.subscribe(proto_sc.fight.team, this.onTeamList, this);
        JsonHttp.subscribe(proto_sc.user.pvewin, this.onPveData, this);       
    };
    this.clearData = function() {
        this.pvb2Data = null;
        this.pvbData = null;
        this.pveData = null;
        this.pvbList = null;
        this.CTData = null;
        this.battleData = null;
        this.bossData = null;
        this.isBoss = !1;
        this.bChapterEnd = false;
        this.fTroops = null;
        this.jTroops = null;
        this.pveBaseData = null;
    };
    this.onSetWinData = function(t) {
        null != t.pvb2win && (this.pvb2Data = t.pvb2win);
        null != t.pvbwin && (this.pvbData = t.pvbwin);
        null != t.pvewin && (this.pveData = t.pvewin);
    };

    /**pve战斗的数据*/
    this.onPveData = function(data){
        this.pveBaseData = data;
    };

    this.onPvbList = function(t) {
        this.pvbList = t;
        facade.send("BATTLE_BOSS_LIST");
    };
    this.getCanFight = function() {
        for (
            var t = [], e = 0;
            i.servantProxy.getServantList() &&
            e < i.servantProxy.getServantList().length;
            e++
        ) {
            var o = !1,
                n = i.servantProxy.getServantList()[e];
            if (null != n) {
                for (
                    var l = 0;
                    this.pvbList && l < this.pvbList.length;
                    l++
                ) {
                    var r = this.pvbList[l];
                    r.id == n.id && 0 == r.h && (o = !0);
                }
                o || t.push(n);
            }
        }
        t.sort(function(t, e) {
            return e.aep.e1 - t.aep.e1;
        });
        return t;
    };
    this.getMaxHid = function() {
        for (
            var t = 0, e = 0, o = 0;
            i.servantProxy.getServantList() &&
            o < i.servantProxy.getServantList().length;
            o++
        )
            if (i.servantProxy.getServantList()[o].aep.e1 > t) {
                t = i.servantProxy.getServantList()[o].aep.e1;
                e = i.servantProxy.getServantList()[o].id;
            }
        return e;
    };
    this.needArmy = function() {
        var t = i.playerProxy.userData,
            e = localcache.getItem(localdb.table_smallPve, t.smap + 1),
            o = e.army - t.mkill,
            n = i.playerProxy.userEp.e1,
            l = Math.round((o / n) * e.ep1);
        e.bmap <= 10 && (l = Math.round(o / 2 + ((o / 2) * e.ep1) / n));
        return (l = l > 0 ? l : 1);
    };

    this.needArmyByMap = function(id) {
        var t = i.playerProxy.userData,
            e = localcache.getItem(localdb.table_smallPve, id),
            o = e.army - t.mkill,
            n = i.playerProxy.userEp.e1,
            l = Math.round((o / n) * e.ep1);
        e.bmap <= 10 && (l = Math.round(o / 2 + ((o / 2) * e.ep1) / n));
        return (l = l > 0 ? l : 1);
    };

    this.isEnoughArmy = function() {
        return i.playerProxy.userData.army >= this.needArmy();
    };
    
    this.sendEnemyFight = function(t) {
        void 0 === t && (t = !1);
        var e = this,
            o = i.playerProxy.userData.army;
        JsonHttp.send(new proto_cs.user.pvenew(), function(n) {
            if (e.battleData) {
                if (null == e.pveData) {
                    i.playerProxy.userData.army = n.u.user.user.army;
                    facade.send("BATTLE_ENEMY_OVER");                    
                    return;
                } else {
                    var l = e.pveData ? parseInt(e.pveData.deil + "") : 0;
                    e.battleData.leftKill = l || 0;
                    e.battleData.leftKill =
                        e.battleData.leftKill > e.battleData.leftArmy
                            ? e.battleData.leftArmy
                            : e.battleData.leftKill;
                    e.battleData.rightKill =
                        0 == e.pveData.kill || 0 == i.playerProxy.userData.mkill
                            ? e.battleData.rightArmy
                            : e.pveData.kill;
                    e.battleData.rightKill =
                        e.battleData.rightKill > e.battleData.rightArmy
                            ? e.battleData.rightArmy
                            : e.battleData.rightKill;
                }
            }
            if (t) {
                var r = o - i.playerProxy.userData.army;
                r > 0 && facade.send("STORY_SHOW_ARMY", r);
            }
            facade.send("BATTLE_ENEMY_OVER");
        });
    };
    // 获取爬塔数据信息
    this.sendCTInfo = function(level) {
        var proto = new proto_cs.tanhe.getTanheInfo();
        proto.copyId = level;
        let self = this;
        JsonHttp.send(proto, (n)=> {
            self.CTData.tanhe = n.a.tanhe;
            facade.send("FIGHT_GAME_CT_GET");
        });
    };

    // 小战斗调整1
    this.sendPveFight1 = function(t,callback) {
        void 0 === t && (t = !1);
        var e = this,
            o = i.playerProxy.userData.army;
        JsonHttp.send(new proto_cs.user.pve(), function(n) {
            if (e.battleData) {
                if (null == e.pveData) {
                    i.playerProxy.userData.army = n.u.user.user.army;
                    facade.send("BATTLE_ENEMY_OVER");                    
                    return;
                } else {
                    var l = e.pveData ? parseInt(e.pveData.deil + "") : 0;
                    e.battleData.leftKill = l || 0;
                    e.battleData.leftKill =
                        e.battleData.leftKill > e.battleData.leftArmy
                            ? e.battleData.leftArmy
                            : e.battleData.leftKill;
                    e.battleData.rightKill =
                        0 == e.pveData.kill || 0 == i.playerProxy.userData.mkill
                            ? e.battleData.rightArmy
                            : e.pveData.kill;
                    e.battleData.rightKill =
                        e.battleData.rightKill > e.battleData.rightArmy
                            ? e.battleData.rightArmy
                            : e.battleData.rightKill;
                }
            }
            if (t) {
                var r = o - i.playerProxy.userData.army;
                r > 0 && facade.send("STORY_SHOW_ARMY", r);
            }
            facade.send("BATTLE_ENEMY_OVER");
            callback && callback();
        });
    };
    // 小战斗调整2
    this.sendPveFight2 = function(selectId) {
        var e = this,
            o = i.playerProxy.userData.army;
        var proto = new proto_cs.user.pveRestraint();
        proto.id = selectId;
        JsonHttp.send(proto, function(n) {
            if (e.battleData) {
                if (null == e.pveData) return;
                var l = e.pveData ? parseInt(e.pveData.deil + "") : 0;
                e.battleData.leftKill = l || 0;
                e.battleData.leftKill =
                    e.battleData.leftKill > e.battleData.leftArmy
                        ? e.battleData.leftArmy
                        : e.battleData.leftKill;
                e.battleData.rightKill =
                    0 == e.pveData.kill || 0 == i.playerProxy.userData.mkill
                        ? e.battleData.rightArmy
                        : e.pveData.kill;
                e.battleData.rightKill =
                    e.battleData.rightKill > e.battleData.rightArmy
                        ? e.battleData.rightArmy
                        : e.battleData.rightKill;
            }          
            facade.send("BATTLE_ENEMY_RESTRAINT_OVER");
        });
    };

    // this.sendTanheFight = function(id) {
    //     var proto = new proto_cs.tanhe.fight();
    //     proto.epId = id;
    //     let self = this;
    //     JsonHttp.send(proto, (n)=> {
    //         self.CTData.tanhe = n.a.tanhe;
    //         facade.send("FIGHT_GAME_CT_FIGHT");
    //     });
    // };

    this.sendBattleInit = function(callback){
        var e = new proto_cs.user.battleInit();
        JsonHttp.send(e,function(){
            callback && callback();
        });
    };

    this.sendBattleRes = function(id,callback){
        var e = new proto_cs.user.battleRes();
        e.id = id;
        JsonHttp.send(e,function(rspData){
            callback && callback(rspData);
        });
    };

    this.sendBossFight = function(t) {
        var e = new proto_cs.user.pvb();
        e.id = t;
        var self = this;
        JsonHttp.send(e, function(e) {
            self.bChapterEnd = true;
            facade.send("BATTLE_BOSS_OVER", t);
        });
    };
    this.sendBackHid = function(t) {
        var e = new proto_cs.user.comeback();
        e.id = t;
        JsonHttp.send(e, function() {
            facade.send("BATTLE_BACK_HID");
        });
    };
    this.sendSpecBoss = function(t, e) {
        facade.send("BATTLE_ENEMY_OVER");
        facade.send("BATTLE_BOSS_OVER");
    };
    this.initSmapData = function() {
        var t;
        this.battleData = null;
        var e = i.playerProxy.userData;
        if (
            null !=
            (t = localcache.getItem(localdb.table_smallPve, e.smap + 1))
        ) {
            var o = localcache.getItem(
                localdb.table_bigPve,
                i.playerProxy.userData.bmap
            );
            this.battleData = new BattleData();
            this.battleData.leftArmy = parseInt(e.army + "");
            this.battleData.leftEp = i.playerProxy.userEp.e1;
            this.battleData.leftSex = e.sex;
            this.battleData.leftJob = e.job;
            this.battleData.rightArmy = parseFloat(t.xueliang + "");
            this.battleData.rightEp = parseFloat(t.ep1 + "");
            this.battleData.rightSex = t.index;
            this.battleData.rightJob = t.action;
            this.battleData.index = t.sindex;
            this.battleData.bname = o.name;
            this.battleData.storyId = t.endStoryId + "";
            this.battleData.context = t.content;
        }
    };
    this.initBMapBossData = function() {
        var t;
        this.bossData = null;
        var e = i.playerProxy.userData;
        if (
            null != (t = localcache.getItem(localdb.table_bigPve, e.bmap))
        ) {
            this.bossData = new BossData();
            this.bossData.bname = t.name;
            this.bossData.bossName = t.bossname;
            this.bossData.id = t.id;
            this.bossData.maxHp = t.hp;
            this.bossData.photo = t.poto;
            this.bossData.storyId = t.endStoryId + "";
            this.bossData.bossCharacter = t.character;
            //let cfg = localcache.getItem(localdb.table_battlePve,t.battleId);
            //if (cfg)
                //this.bossData.battleMode = cfg.battleMode;
        }
    };
    this.initClimbingTower = function() {
        var t;
        this.CTData = {};
        var e = i.playerProxy.userData;
        if (
            null !=
            (t = localcache.getItem(localdb.table_smallPve, e.smap + 1))
        ) {
            var o = localcache.getItem(
                localdb.table_bigPve,
                i.playerProxy.userData.bmap
            );
            this.CTData.leftArmy = parseInt(e.army + "");
            this.CTData.leftEp = i.playerProxy.userEp.e1;
            this.CTData.leftSex = e.sex;
            this.CTData.leftJob = e.job;           
        }
    };
    this.isFirstmMap = function() {
        var t = i.playerProxy.userData,
            e = localcache.getItem(localdb.table_smallPve, t.smap + 1);
        if (null == e) return !0;
        var o = localcache.getGroup(localdb.table_smallPve, "mmap", e.mmap);
        o.sort(function(t, e) {
            return t.id - e.id;
        });
        return o[0].id > t.smap;
    };
    this.isFirstBMap = function() {
        var t = i.playerProxy.userData,
            e = localcache.getItem(localdb.table_midPve, t.mmap);
        if (null == e) return !0;
        var o = localcache.getGroup(localdb.table_midPve, "bmap", e.bmap);
        o.sort(function(t, e) {
            return t.id - e.id;
        });
        return o[0].id >= e.id;
    };

    this.showEnemyShow = function(t) {
        void 0 === t && (t = 0);
        //l.funUtils.openView(l.funUtils.FightNew.id, 0 != t ? { id: t } : null);
        n.utils.openPrefabView("battle/BattleBaseView", null, {type:FIGHTBATTLETYPE.NONE});    
        //l.funUtils.openView(l.funUtils.FightNew.id, 0 != t ? { id: t } : null);
    };

    this.showBossShow = function(t) {
        void 0 === t && (t = 0);
        10 * Math.random() < 5
            ? l.funUtils.openView(l.funUtils.battleBossView.id)
            : n.utils.openPrefabView(
                  "battle/FightBossSay",
                  !1,
                  0 != t
                      ? {
                            id: t
                        }
                      : null
              );
    };
    this.playerRandomHit = function() {
        n.audioManager.playSound(
            "hit" + (Math.floor(3 * Math.random()) + 1),
            !0
        );
    };
    this.isCanFight = function(t, e) {
        var o = !0,
            r = "";
        if (0 == t || n.stringUtil.isBlank(e)) return !0;
        switch (t) {
            case 1:
                o = i.playerProxy.userData.level >= parseInt(e);
                var a = localcache.getItem(localdb.table_officer, e);
                r = i18n.t("FIGHT_USER_LV_LIMIT", {
                    n: a.name
                });
                break;

            case 2:
                 o = i.taskProxy.mainTask.id > parseInt(e);
                // if (i.taskProxy.mainTask.id == parseInt(e)) {
                //     var s = localcache.getItem(localdb.table_mainTask, e);
                //     r = i18n.t("FIGHT_TASK_LIMIT", {
                //         n: s.name
                //     });
                // } else r = i18n.t("FIGHT_TASK_OVER_TIP");
                if (!o){
                    l.funUtils.openView(l.funUtils.mainTask.id);
                    return false;
                }
                
                break;

            case 3:
                if ((c = e.split("|")).length > 1) {
                    o = i.playerProxy.userEp["e" + c[0]] >= parseInt(c[1]);
                    r = i18n.t("FIGHT_EP_LIMIT", {
                        n: i18n.t("COMMON_PROP" + c[0]),
                        c: c[1]
                    });
                    o ||
                        n.utils.showConfirm(r, function() {
                            l.funUtils.openView(l.funUtils.servantView.id);
                        });
                } else o = !0;
                break;

            case 4:
                var c;
                if ((c = e.split("|")).length > 1) {
                    var _ = i.servantProxy.getHeroData(parseInt(c[0])),
                        d = localcache.getItem(localdb.table_hero, c[0]);
                    o = _ && _.level >= parseInt(c[1]);
                    r = i18n.t("FIGHT_HERO_LIMIT", {
                        n: d.name,
                        c: c[1]
                    });
                    o ||
                        n.utils.showConfirm(r, function() {
                            l.funUtils.openView(l.funUtils.servantView.id);
                        });
                } else o = !0;
        }
        o || n.alertUtil.alert(r);
        return o;
    };

    this.checkStory = function() {
        var t = 0,
        e = i.playerProxy.userData,
        o = 0,
        l = 0;
        if (this.checkIsBoss()) {
            var r = localcache.getItem(localdb.table_bigPve, e.bmap);
            if (!this.isCanFight(r.type, r.condition + "")) return ! 0;
            var a = i.timeProxy.getLoacalValue("FIGHT_BOSS_ID" + i.playerProxy.userData.uid);
            o = n.stringUtil.isBlank(a) ? 0 : parseInt(a);
            t = r ? r.bossStoryId : 0;
        } else if (this.isFirstmMap()) {
            var s = localcache.getItem(localdb.table_midPve, e.mmap);
            if (!this.isCanFight(s.type, s.condition)) return ! 0;
            var c = i.timeProxy.getLoacalValue("FIGHT_ENEMY_ID" + i.playerProxy.userData.uid);
            l = n.stringUtil.isBlank(c) ? 0 : parseInt(c);
            t = s ? s.storyId : 0;
        }
        var _ = (0 == o && l < parseInt(i.playerProxy.userData.smap + "") + 1) || (0 == l && o < i.playerProxy.userData.bmap);
        if (0 != t && i.playerProxy.getStoryData(t) && _) {
            i.playerProxy.addStoryId(t);
            n.utils.openPrefabView("StoryView");
            return ! 0;
        }
        return !1;
    };

    this.getCurrentStoryId = function() {
        var t = 0,
        e = i.playerProxy.userData,
        o = 0,
        l = 0;
        if (this.checkIsBoss()) {
            var r = localcache.getItem(localdb.table_bigPve, e.bmap);
            // if (!this.isCanFight(r.type, r.condition + "")) return ! 0;
            // var a = i.timeProxy.getLoacalValue("FIGHT_BOSS_ID");
            // o = n.stringUtil.isBlank(a) ? 0 : parseInt(a);
            t = r ? r.bossStoryId : 0;
        } else if (this.isFirstmMap()) {
            var s = localcache.getItem(localdb.table_midPve, e.mmap);
            // if (!this.isCanFight(s.type, s.condition)) return ! 0;
            // var c = i.timeProxy.getLoacalValue("FIGHT_ENEMY_ID");
            // l = n.stringUtil.isBlank(c) ? 0 : parseInt(c);
            t = s ? s.storyId : 0;
        }        
        return t;
    };

    this.getPveStoryBg = function() {
        var t = 0,
        e = i.playerProxy.userData;
        var info = null;
        if (this.checkIsBoss()) {
            info = localcache.getItem(localdb.table_bigPve, e.bmap);
        } else if (this.isFirstmMap()) {
            info = localcache.getItem(localdb.table_midPve, e.mmap);
        }   
        if(info != null)
            t = info.bg;
        return t;
    };

    this.checkIsBoss = function() {
        return (localcache.getItem(localdb.table_midPve, i.playerProxy.userData.mmap).bmap > i.playerProxy.userData.bmap);
    };

    this.checkFight = function() {
        if (this.checkIsBoss()) {
            i.timeProxy.saveLocalValue("FIGHT_BOSS_ID" + i.playerProxy.userData.uid, i.playerProxy.userData.bmap + "");
            this.initBMapBossData();
            if (null == this.bossData) {
                n.alertUtil.alert(i18n.t("FIGHT_NOT_FIND_BOSS"));
                return;
            }
            l.funUtils.openView(l.funUtils.battleBossView.id);
            //if (this.bossData.battleMode == 2){
                //l.funUtils.openView(l.funUtils.battleBossView.id);
            //}
            //else{
                //n.utils.openPrefabView("battle/BattleBaseView", null, {type:FIGHTBATTLETYPE.SPECIAL_BOSS});
            //}
        } else {
            i.timeProxy.saveLocalValue("FIGHT_ENEMY_ID" + i.playerProxy.userData.uid, parseInt(i.playerProxy.userData.smap + "") + 1 + "");
            this.initSmapData();
            if (null == this.battleData) {
                n.alertUtil.alert(i18n.t("FIGHT_NOT_FIND"));
                return;
            }
            this.showEnemyShow();
        }
    };

    // 属性克制关系 
    // a克制b为true
    // b克制a为false
    this.propertyRestrain = function(a, b) {
        var restrain = [["3","2","4"],["2","4","3"],["4","3","2"]];
        for(var i=0; i<restrain.length; i++) {
            var item = restrain[i];
            if(a == item[1]) {
                for(var j=0; j<item.length; j++) {
                    if(b == item[j]) {
                        if(j > 1)
                            return false;
                        else
                            return true;
                    }
                }
            }
        }
    };

    // 查找克制npc属性的属性
    this.findRestrainProperty = function(prop) {
        for(var i=2; i<5; i++) {
            if(this.propertyRestrain(i, prop)) {
                return i; 
            }                
        }
    };

	this.getTeamArray = function(type, heroid) {
        let teamArr = [];
        switch(type) {
            case FIGHTBATTLETYPE.JIAOYOU:
                if(null != this.jTroops[heroid]) {
                    teamArr = this.jTroops[heroid];
                }
                break;
            default:
                teamArr = this.fTroops;
                break;
        }
        return teamArr;
    };

    this.sendTeam = function(teams, heroId) {
        let str = "";
        for(let j = 0, len = teams.length; j < len; j++) {
            str += (j == len - 1 ? teams[j] : teams[j] + "|");
        }
        let reqData = new proto_cs.teams.setTeams();
        reqData.teams = str;
        reqData.heroId = heroId;
        JsonHttp.send(reqData, (data) => {
            if(null != data && null != data.a && null != data.a.system && null != data.a.system.errror) {
                i.cardProxy.tmpTeamList = null;
                facade.send("TEAM_CARD_UPDATE"); //保存失败编队还原
                return;
            }
            i.playerProxy.updateTeamRed(heroId);
            n.alertUtil.alert(i18n.t("SAVE_SUCCESS"));
        });
    };

    this.onTeamList = function(data) {
        this.fTroops = data.fTroops; //剧情、弹劾编队
        this.jTroops = data.jTroops; //郊游编队
        facade.send("TEAM_CARD_UPDATE");
    };

    /**获取当前战斗的数据
    *@param type 战斗的类型 （小战斗，弹劾，郊游）
    */
    this.getBattlePlayerHp = function(type,heroId=0){
        let listdata = this.getTeamArray(type,heroId);
        let sum = 0;
        for (let ii = 0; ii < listdata.length;ii++){
            sum += i.cardProxy.getCardCommonPropValue(listdata[ii],1);
        }
        return sum;
    };

    /**发起攻击*/
    this.sendFightByKind = function(type,cardId,callback){
        switch(type){
            case FIGHTBATTLETYPE.NONE:{//小战斗
                i.playerProxy.sendUserPveRestraint(cardId,callback);
            }
            break;
            case FIGHTBATTLETYPE.TANHE:{//弹劾战斗
                i.tanheProxy.sendTanheFight(cardId,callback);
            }
            break;
            case FIGHTBATTLETYPE.JIAOYOU:{//郊游战斗
                i.jiaoyouProxy.sendFight(cardId,callback);
            }
            break;
			 case FIGHTBATTLETYPE.FURNITURE:{
                //发起攻击
                i.famUserHProxy.sendFight(cardId,callback);
            }
            break;
            case FIGHTBATTLETYPE.SPECIAL_BOSS:{//小战斗模式的大战斗
                this.sendBattleRes(cardId,callback)
            }
            break;
        }
    };

    /**获取卡牌战斗目标位置列表*/
    this.getFightCardDstArr = function(num){
        switch(num){
            case 1:
            return [0];
            case 2:
            return [-150,150];
            case 3:
            return [-300,0,300];
            case 4:
            return [-450,-150,150,450];
        }
    };

    /**获取战斗返回的数据*/
    this.getFightBaseData = function(type){
        switch(type){
            case FIGHTBATTLETYPE.NONE:
            case FIGHTBATTLETYPE.SPECIAL_BOSS:
            {//小战斗
                return this.pveBaseData;
            }
            case FIGHTBATTLETYPE.TANHE:{//弹劾战斗
                return i.tanheProxy.tanHeFightInfo;
            }
            case FIGHTBATTLETYPE.JIAOYOU:{//郊游战斗
                return i.jiaoyouProxy.fightInfo;
            }
            case FIGHTBATTLETYPE.FURNITURE:{
                //发起攻击
                return i.famUserHProxy.battleInfo
            }
        }
    }


    //检查编队卡牌数是否足够可以进入战斗
    this.checkTeamCanFight = function(type, heroid) {
        let result = false;
        let num = n.utils.getParamInt("team_min");
        switch(type) {
            case FIGHTBATTLETYPE.JIAOYOU:
                result = null != this.jTroops[heroid] && this.jTroops[heroid].length >= num;
                break;
            default:
                result = null != this.fTroops && this.fTroops.length >= num;
                break;
        }
        return result;
    };

    this.checkCardInTeam = function(cardId) {
        if(null != this.fTroops && this.fTroops.indexOf(cardId) > -1) {
            return true;
        } else if(null != this.jTroops) {
            for(let heroId in this.jTroops) {
                let array = this.jTroops[heroId];
                if(null != array && array.indexOf(cardId) > -1) {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    };

}

exports.FightProxy = FightProxy;

var BattleData = function() {
    this.index = 0;
    this.storyId = "";
    this.context = 0;
}
exports.BattleData = BattleData;

var BossData = function() {
    this.storyId = "";
    this.battleMode = 2;
}
exports.BossData = BossData;

var EnemyDataItem = function() {
    this.isGray = 0;
}
exports.EnemyDataItem = EnemyDataItem;
