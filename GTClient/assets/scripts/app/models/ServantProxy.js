var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var UIUtils = require("UIUtils");
var ServantProxy = function() {

    this.servantList = [];
    this.servantMap = new Map();
    this.curServantList = [];
    this.heroStoryList = null;
    this.allHeroDress = null;//所有解锁的伙伴服装ID-
    this.heroDressMap = null;//各个伙伴装备的时装map
    this.isRenMaiOpen = !1;
    this.isLevelupTen = !1;
    this.nobility = null;
    this.skin = null;
    this.needItemNum = null;
    this.jingliData = null;
    this.jiaqiData = null;
    this.SERVANT_TALK_TDA = "SERVANT_TALK_TDA";
    this.SERVANT_UP = "SERVANT_UP";
    this.SERVANT_CLOTH_UPDATE = "SERVANT_CLOTH_UPDATE";//伙伴时装更新
    this.SERVANT_JING_LI = "SERVANT_JING_LI";
    this.SERVANT_JIA_QI = "SERVANT_JIA_QI";
    this.SERVANT_VISIT = "SERVANT_VISIT"; //伙伴问候更新
    this.SERVANT_RIGHT = "SERVANT_RIGHT";
    this.WIFE_WINDOW = "WIFE_WINDOW";
    this.xinWuItemDic = {};
    this.tokenFetterData = null;
    this.dicTokenRed = [];
    this.servanetJiBanAward = null;
    this.heroShopData = null;
    this.servantJiBanRoadRed = {};
    this.recordFingerGuessDic = {};
    this.inviteEventData = null;
    this.fishBaseInfo = null;
    this.foodBaseInfo = null;
    this.collectInfo = null;
    this.inviteBaseInfo = null;
    this.collectAwardInfo = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.hero.heroList, this.onHeroList, this);
        JsonHttp.subscribe(proto_sc.hero.heroChat, this.onHeroChat, this);
        JsonHttp.subscribe(proto_sc.hero.skin, this.onHeroSkin, this);
        JsonHttp.subscribe(proto_sc.hero.heroDress, this.onHeroDress, this);
        JsonHttp.subscribe(proto_sc.hero.setClothe, this.onHeroDress, this);
        JsonHttp.subscribe(proto_sc.hero.jingLi, this.onJingLi, this);
        JsonHttp.subscribe(proto_sc.hero.jiaQi, this.onJiaQi, this);
        JsonHttp.subscribe(proto_sc.hero.travel, this.onTravel, this);
        JsonHttp.subscribe(proto_sc.hero.hello, this.onHello, this);
        JsonHttp.subscribe(proto_sc.hero.activationMail, this.onActivationMail, this);
        JsonHttp.subscribe(proto_sc.hero.tokenFetters, this.onTokenFetters, this);
        JsonHttp.subscribe(proto_sc.hero.useItem, this.onUseItem, this);
        JsonHttp.subscribe(proto_sc.hero.jibanAward, this.onjibanAward, this);
        JsonHttp.subscribe(proto_sc.hero.heroshop, this.onHeroshop, this);
        JsonHttp.subscribe(proto_sc.hero.heroBlank, this.onHeroBlank, this);
        JsonHttp.subscribe(proto_sc.hero.heroEmoji, this.onHeroEmoji, this);
        JsonHttp.subscribe(proto_sc.hero.visit, this.onVisit, this);
        JsonHttp.subscribe(proto_sc.hero.right, this.onVisitAnswer, this);
        JsonHttp.subscribe(proto_sc.hero.invite, this.onInvite, this);
        JsonHttp.subscribe(proto_sc.hero.inviteInfo, this.onInviteInfo, this);
        JsonHttp.subscribe(proto_sc.hero.fish, this.onFishInfo, this);
        JsonHttp.subscribe(proto_sc.hero.food, this.onFoodInfo, this);
        JsonHttp.subscribe(proto_sc.hero.collect, this.onCollectInfo, this);
        JsonHttp.subscribe(proto_sc.hero.buy, this.onBuyInviteInfo, this);
        facade.subscribe(n.jibanProxy.UPDATE_HERO_JB, this.onMiniCountUpdate, this);
    };

    this.clearData = function() {
        this.jingliData = null;
        this.jiaqiData = null;
        this.curSelectId = 0;
        this.heroStoryList = null;
        this.allHeroDress = null;
        this.heroDressMap = null;
        this.servantList = [];
        this.servantMap = new Map();
        this.curServantList = [];
        this.isLevelupTen = !1;
        this.skin = null;
        this.xinWuItemDic = {};
        this.tokenServerData = null;
        this.tokenFetterData = null;
        this.dicTokenRed = [];
        this.servanetJiBanAward = null;
        this.heroShopData = null;
        this.servantJiBanRoadRed = {};
        this.recordFingerGuessDic = {};
        this.inviteEventData = null;
        this.fishBaseInfo = null;
        this.foodBaseInfo = null;
        this.collectInfo = null;
        this.inviteBaseInfo = null;
        this.collectAwardInfo = null;
    };
    //伙伴服装信息
    this.onHeroDress = function(dressData){
        if(dressData){
            this.allHeroDress = new Array();
            this.heroDressMap = new Map();
            if(dressData.clothes){
                for(let i = 0,len = dressData.clothes.length;i < len;i++){
                    this.allHeroDress.push(dressData.clothes[i]);
                }
            }
            if(dressData.herodress){
                for (let heroID in dressData.herodress) {
                    this.heroDressMap[heroID] = dressData.herodress[heroID];
                }
            }
            //更新UI显示
            facade.send("PLAYER_HERO_SHOW");
        }
    };
    //获取伙伴选的时装ID
    this.getHeroDress = function(heroID){
        if(this.heroDressMap && this.heroDressMap[heroID]){
            return this.heroDressMap[heroID];
        }
        return 0;
    };
    this.checkDressHaveGet = function(dressID){
        for(let j = 0,len = this.allHeroDress.length;j < len;j++){
            if(this.allHeroDress[j] == dressID){
                return true;
            }
        }
        return false;
    };
    //获取伙伴拥有的所有时装
    this.getHeroAllDress = function(heroID) {
        let dressInfoMap = new Map();
        let ownerDress = new Array();//已经获取的
        let noHaveDress = new Array();//未获取的
        if(this.allHeroDress){
            //获取hero可以获得的所有时装ID
            let heroDresssArray = localcache.getFilters(localdb.table_heroDress, "heroid", heroID);
            if(heroDresssArray){
                for(let i = 0;i < heroDresssArray.length;i++){//遍历可以穿的时装ID
                    let haveGet = false;
                    let cfgData = heroDresssArray[i];
                    for(let j = 0,len = this.allHeroDress.length;j < len;j++){
                        let dressID = this.allHeroDress[j];
                        if(dressID == cfgData.id){
                            ownerDress.push(cfgData);
                            haveGet = true;
                            break;
                        }
                    }
                    if(!haveGet){
                        noHaveDress.push(cfgData);
                    }
                }
            }
        }
        dressInfoMap['ownerDress'] = ownerDress;
        dressInfoMap['noHaveDress'] = noHaveDress;
        return dressInfoMap;
    };
    this.onHeroList = function(t) {
        if (null == this.servantList) {
            this.servantList = [];
            this.servantMap = new Map();
        }
        for (var e = !1, o = 0; o < t.length; o++) {
            var n = t[o];
            if (n) {
                this.servantMap[n.id] = n;
                e ||
                    (e =
                        this.getLevelUp(t[o]) ||
                        this.getTanlentUp(t[o]) ||
                        this.getSkillUp(t[o])) ||
                        this.isCanTiBa(t[o]) || this.dicTokenRed[t[o].id] || this.servantJiBanRoadRed[t[o].id];
            }
        }
        l.change("servant", e);
        i.utils.copyData(this.servantList, t);
        facade.send("SERVANT_UP");
        facade.send("PLAYER_USER_UPDATE");
        if(t.length == 1) {
            this.lastHero = t[0].id;
        }
    };

    this.servantDamage = function() {
        var damage = 0;
        for(var i=0; i<this.servantList.length; i++) {
            damage += this.servantList[i].aep["e1"];
        } 
        return damage;   
    };

    this.onUseItem = function(ret) {
        if (null == this.useItemList) {
            this.useItemList = {};
        }
        i.utils.copyData(this.useItemList, ret.useInfo);
        facade.send("PLAYER_USER_UPDATE");
    };
    this.onHeroChat = function(t) {
        facade.send(this.SERVANT_TALK_TDA, t);
    };
    this.onHeroSkin = function(t) {
        this.skin = t;
        facade.send("SERVANT_CHUANZHUANG");
    };

    //伙伴信物的信息
    this.onActivationMail = function(adata) {
        //console.error("adata:",adata);
        this.tokenServerData = adata.tokens;
        this.checkTokenRed();
        facade.send("SERVANT_TOKEN_UPDATE");
    };

    //信物羁绊的数据
    this.onTokenFetters = function(adata) {    
        this.tokenFetterData = adata.fetterInfo;
        //console.error("onTokenFetters:",adata)
        this.checkTokenRed();
        facade.send("SERVANT_TOKENFETTER_UPDATE");
    };

    this.getTokenFettersInfo = function(t) {
        if (this.tokenFetterData == null || this.tokenFetterData[t] == null) return null;
        return this.tokenFetterData[t];
    };

    this.checkTokenRed = function() {
        let heros = localcache.getList(localdb.table_hero);
        for(let j = 0, jLen = heros.length; j < jLen; j++) {
            let id = heros[j].heroid;
            l.change("bookroom_token" + id, false);
            this.dicTokenRed[id] = false;
            if (this.tokenServerData == null || this.tokenServerData[id] == null)
                continue;
            let _tokenidarr = [];
            let bContinue = false;
            for (let kk in this.tokenServerData[id]) {
                let mm = this.tokenServerData[id][kk];
                let count = mm.count;
                //可以激活
                if (mm.isActivation == 0 && count > 0) {
                    l.change("bookroom_token" + id, true);
                    this.dicTokenRed[id] = true;
                    bContinue = true;
                    break;
                }
                let lv = mm.lv;
                let db_l = localcache.getItem(localdb.table_tokenlvup, lv + 1);
                if (db_l != null) {
                    //可以升级
                    if (count >= db_l.cost) {
                        l.change("bookroom_token" + id, true);
                        this.dicTokenRed[id] = true;
                        bContinue = true;
                        break;
                    }
                }
                _tokenidarr.push(Number(kk));
            }
            if(bContinue) {
                continue;
            }
            let ps = this.getTokenFettersInfo(id);
            if (ps != null && ps[id] != null) {
                let db_ = localcache.getList(localdb.table_tokenfetters);
                for (let _hh of db_) {
                    let flag = true;
                    let iscont = false;
                    for (let _kk of _hh.xinwuid) {
                        if (!this.isActiveToken(_kk)){
                            flag = false;
                        }
                        if (_tokenidarr.indexOf(_kk) != -1){
                            iscont = true;
                        }
                    }
                    if (flag && iscont && ps[id] != null && ps[id].indexOf(_hh.id) == -1) {
                        this.dicTokenRed[id] = true;
                        l.change("bookroom_token" + id, true);
                        break;
                    }
                }
            }
        }

        for (let key in this.dicTokenRed){
            if (this.dicTokenRed[key]){
                l.change("servant", true);
                break;
            }
        }
    }

    //根据伙伴id获取所有的信物数据
    this.getTokensInfo = function(t){
        if (this.tokenServerData == null || this.tokenServerData[t] == null) return null;
        return this.tokenServerData[t]
    };

    this.isActiveToken = function(t){
        if (this.tokenServerData == null) return false;
        for (let ll in this.tokenServerData){
            if (this.tokenServerData[ll][t] != null && this.tokenServerData[ll][t].isActivation == 1){
                return true;
            }
        }
        return false;
    };


    this.isActiveFetter = function(hid,t){
        if (this.tokenFetterData == null || this.tokenFetterData[hid] == null || t == null) return false;
        if (this.tokenFetterData[hid].indexOf(t) != -1){
            return true;
        }
        return false;
    }

    this.getPropName = function(t){
        if (t == 1){
            return i18n.t("COMMON_PROP1");
        }
        else if(t == 2){
            return i18n.t("COMMON_PROP2");
        }
        else if(t == 3){
            return i18n.t("COMMON_PROP3");
        }
        else{
            return i18n.t("COMMON_PROP4");
        }
    };


    
    this.getAllHeroEp = function(t) {
        for (var e = 0, o = 0; o < this.servantList.length; o++)
            e += this.servantList[o].aep["e" + t];
        return e;
    };
    this.sendUpSenior = function(t,isShow = true) {
        var e = new proto_cs.hero.upsenior();
        e.id = t;
        JsonHttp.send(e, function() {
            if (isShow)
                i.utils.openPrefabView("servant/ServantAdvanceWindow");
        });
    };
    this.sendUpPkSkill = function(t, e) {
        var o = new proto_cs.hero.uppkskill();
        o.id = t;
        o.sid = e;
        JsonHttp.send(o);
    };
    this.sortServantList = function() {
        this.servantList.sort(function(t, e) {
            if (null == t || null == e) return 1;
            var o = t.zz.e1 + t.zz.e2 + t.zz.e3 + t.zz.e4,
                i = e.zz.e1 + e.zz.e2 + e.zz.e3 + e.zz.e4;
            return o != i ? i - o : t.id - e.id;
        });
    };
    this.sendLvUp = function(t) {
        var e = new proto_cs.hero.upgrade();
        e.id = t;
        JsonHttp.send(e, function() {
            facade.send("SERVANT_LV_UP");
        });
    };
    this.sendUpZzSkill = function(t, e, o) {
        var i = new proto_cs.hero.upzzskill();
        i.id = t;
        i.sid = e;
        i.type = o;
        JsonHttp.send(i);
    };
    this.sendLvUpTen = function(t) {
        var e = new proto_cs.hero.upgradeTen();
        e.id = t;
        JsonHttp.send(e, function() {
            facade.send("SERVANT_LV_UP");
        });
    };
    this.sendHz = function(t, e) {
        var o = new proto_cs.hero.heroDress();
        o.hid = t;
        o.id = e;
        JsonHttp.send(o);
    };
    this.sendHeroGift = function(t, items) {
        if(!items.length || items.length <= 0) {
            return;
        }
        let reqData = new proto_cs.hero.giveGift();
        reqData.id = t;
        let str = "";
        for(let i = 0, len = items.length; i < len; i++) {
            str += items[i].gid + "," + items[i].num + ";";
        }
        reqData.items = str.substr(0, str.length - 1);
        JsonHttp.send(reqData);
    };
    this.sendHeroTalk = function(t) {
        var e = new proto_cs.hero.hchat();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendLeaderUp = function(t) {
        var e = new proto_cs.hero.upcharisma();
        e.id = t;
        JsonHttp.send(e);
    };

    this.sendXXOO = function(t) {
        var e = new proto_cs.hero.xxoo();
        e.id = t;
        JsonHttp.send(e, function(t) {});
    };
    this.sendXXOOnoBaby = function(t) {
        var e = new proto_cs.hero.xxoonobaby();
        e.id = t;
        JsonHttp.send(e, function(t) {});
    };

    this.sendSJXO = function() {
        var t = new proto_cs.hero.sjxo();
        JsonHttp.send(t);
    };

    this.sendSJCY = function() {
        var t = new proto_cs.hero.sjcy();
        JsonHttp.send(t);
    };


    this.isHaveServantLv = function(t) {
        for (var e = 0; e < this.servantList.length; e++)
            if (this.servantList[e].level >= t) return !0;
        return !1;
    };
    this.getHeroList = function(t) {
        for (
            var e = [], o = localcache.getList(localdb.table_hero), i = 0;
            i < o.length;
            i++
        ) {
            var l = this.getHeroData(o[i].heroid);
            t && l
                ? e.push(o[i])
                : !t &&
                  null == l &&
                  n.jibanProxy.getHeroJB(o[i].heroid) > 0 &&
                  e.push(o[i]);
        }
        return e;
    };

    this.getHeroRandomTalk = function(t){
        if (t == null) return "";
        var a = localcache.getItem(localdb.table_hero,String(t));
        let listdata = i.utils.clone(a.talk);
        let listcfg  = localcache.getFilters(localdb.table_hero_yoke_unlock,"hero_id",t);
        let listcfg2 = listcfg.filter((data)=>{
            return data.type == 5;
        });
        for (var ii = 0; ii < listcfg2.length;ii++){
            let cg = listcfg2[ii].set;
            for (let jj = 0; jj < cg.length;jj++){
                let cfg = localcache.getItem(localdb.table_herotalk,cg[jj]);
                listdata.push(cfg.talk);
            }
        }
        var len = listdata.length;
        var str = listdata[Math.floor(Math.random() * len)];
        return str;
    }
    this.getHeroData = function(t) {
        for (var e = 0; e < this.servantList.length; e++)
            if (this.servantList[e] && this.servantList[e].id == t)
                return this.servantList[e];
        return null;
    };
    this.getAllLove = function() {
        let result = 0;
        if(null != this.servantList) {
            for (let i = 0, len = this.servantList.length; i < len; i++) {
                if(null != this.servantList[i].love) {
                    result += this.servantList[i].love;
                }
            }
        }
        return result;
    }
    /**是否可以提拔*/
    this.isCanTiBa = function(t) {
        var e,
            o = localcache.getItem(localdb.table_nobility, t.senior),
            i = localcache.getItem(localdb.table_nobility, t.senior + 1);
        if (null == i) return !1;
        e =
            t.level == o.max_level &&
            n.playerProxy.userData.level >= o.player_level &&
            null != i && o.need != null;
        let l = !0;

        if (o.need != null){
            for (var r = 0; r < o.need.length; r++)
                if (n.bagProxy.getItemCount(o.need[r]) <= 0) {
                    l = !1;
                    break;
                }
        }

        return e && l;
    };

    
    this.getLevelUp = function(t) {
        if (t == null || t.senior == null) return false;
        var cfg = localcache.getItem(localdb.table_nobility, t.senior);
        if (cfg.max_level > t.level){
            let herolvupcfg = localcache.getItem(localdb.table_heroLvUp,t.level);
            if (n.bagProxy.getItemCount(2) >= herolvupcfg.cost){
                return true;
            }
        }
        return false;
    }
    this.getTanlentUp = function(t) {
        for (var e = !1, o = 0; o < t.epskill.length; o++)
            if (this.tanlentIsEnoughUp(t, t.epskill[o])) {
                e = !0;
                break;
            }
        return e;
    };
    this.tanlentIsEnoughUp = function(t, e) {
        var o = localcache.getItem(localdb.table_epSkill, e.id),
            i = localcache.getItem(localdb.table_epLvUp, o.star),
            l = null;
        1 == o.ep
            ? (l = localcache.getItem(localdb.table_item, 61))
            : 2 == o.ep
            ? (l = localcache.getItem(localdb.table_item, 62))
            : 3 == o.ep
            ? (l = localcache.getItem(localdb.table_item, 63))
            : 4 == o.ep && (l = localcache.getItem(localdb.table_item, 64));
        var r = n.bagProxy.getItemCount(l.id);
        let max = null;
        if(!!t) {
            max = localcache.getItem(localdb.table_nobility, t.senior).maxeplv;
        }
        return !!t && (t.zzexp >= i.exp || r >= i.quantity) && max > e.level;
    };
    this.getSkillUp = function(t) {
        for (var e = !1, o = 0; o < t.pkskill.length; o++)
            if (this.skillIsEnouhghUp(t, t.pkskill[o])) {
                e = !0;
                break;
            }
        return e;
    };
    this.skillIsEnouhghUp = function(t, e) {
        if(null == t || null == e) return false;
        var o = localcache.getItem(localdb.table_pkSkill, e.id),
            i = localcache.getItem(localdb.table_pkLvUp, e.level);
        return e.level < o.maxLevel && t.pkexp >= i.exp;
    };
    this.getBossServantList = function(t) {
        for (var e = [], o = 0; o < this.servantList.length; o++)
            this.servantList[o].id != t && e.push(this.servantList[o]);
        return e;
    };
    this.sortList = function(t, e) {
        var o = n.bossPorxy.getServantHitCount(t.id) > 0 ? 1 : 0,
            i = n.bossPorxy.getServantHitCount(e.id) > 0 ? 1 : 0;
        return o != i ? i - o : t.aep.e4 > e.aep.e4 ? -1 : 1;
    };
    this.sortServantEp = function(t, e) {
        t.aep.e1,
            t.aep.e2,
            t.aep.e3,
            t.aep.e4,
            e.aep.e1,
            e.aep.e2,
            e.aep.e3,
            e.aep.e4;
        var o = n.unionProxy.getHeroFightData(t.id),
            i = n.unionProxy.getHeroFightData(e.id),
            l = o && 1 != o.f && 0 == o.h ? 1 : 0,
            r = i && 1 != i.f && 0 == i.h ? 1 : 0;
        return l != r ? l - r : e.aep.e1 - t.aep.e1;
    };
    this.getQishiSys = function() {
        for (var t = null, e = 0; e < this.servantList.length; e++) {
            localcache
                .getItem(localdb.table_hero, this.servantList[e].id)
                .spec.indexOf(1) >= 0 &&
                (null == t
                    ? (t = this.servantList[e])
                    : t.level < this.servantList[e].level &&
                      (t = this.servantList[e]));
        }
        if (null == t)
            for (e = 0; e < this.servantList.length; e++)
                null == t
                    ? (t = this.servantList[e])
                    : t.aep.e1 < this.servantList[e].aep.e1 &&
                      (t = this.servantList[e]);
        return localcache.getItem(localdb.table_hero, t.id);
    };
    this.isActivedLeader = function(t) {
        var e = this.getLeadSys(t);
        if (e) {
            for (var o in e.activation) {
                if (null == this.getHeroData(e.activation[o])) return !1;
            }
            return !0;
        }
        return !1;
    };
    this.getLeadActivieStr = function(t) {
        var e = "",
            o = this.getLeadSys(t);
        if (o)
            for (var i in o.activation) {
                var n = localcache.getItem(
                    localdb.table_hero,
                    o.activation[i]
                );
                this.getHeroData(n.heroid);
                e += "[" + n.name + "]";
            }
        return i18n.t("LEADER_ACTIVITE_DES_2", {
            str: e
        });
    };
    this.getLeadSys = function(t) {
        var e = localcache.getItem(localdb.table_hero, t);
        return localcache.getItem(localdb.table_leaderAt, e.leaderid);
    };
    this.getLeadLv = function(t, e, o) {
        void 0 === o && (o = !1);
        for (
            var i = localcache.getList(localdb.table_leaderAt),
                n = 0,
                l = 0;
            l < i.length;
            l++
        )
            if (i[l].activation.indexOf(t) >= 0) {
                n = i[l].star;
                break;
            }
        var r = o ? 1e3 * n + e + 1 : 1e3 * n + e;
        return localcache.getItem(localdb.table_leaderExp, r);
    };
    this.getSink = function(t) {
        if (this.skin)
            for (var e = 0, o = this.skin; e < o.length; e++) {
                var i = o[e];
                if (t == i.hid) return i;
            }
        return null;
    };
    this.getHeroUseSkin = function(t) {
        var e = this.getSink(t);
        return null == e ? 0 : e.dress ? e.dress : 0;
    };
    this.getServantList = function() {
        if (
            null == n.xianyunProxy.heroList ||
            0 == n.xianyunProxy.heroList.length
        )
            return this.servantList;
        for (var t = [], e = 0; e < this.servantList.length; e++)
            this.isXianyun(this.servantList[e].id) ||
                t.push(this.servantList[e]);
        return t;
    };
    this.isXianyun = function(t) {
        for (var e = 0; e < n.xianyunProxy.heroList.length; e++)
            if (n.xianyunProxy.heroList[e].hid == t) return !0;
        return !1;
    };
    this.getFourKingActivieStr = function(t) {
        var e = "",
            o = this.getLeadSys(t);
        if (o)
            for (var i in o.activation) {
                var n = localcache.getItem(
                    localdb.table_hero,
                    o.activation[i]
                );
                this.getHeroData(n.heroid);
                e += "[" + n.name + "]";
            }
        return i18n.t("LEADER_ACTIVITE_DES_3", {
            str: e
        });
    };

    this.getXinWuItemListByHeroid = function(id){
        if (this.xinWuItemDic[id] == null){
            var m = localcache.getFilters(localdb.table_item,"kind",200);
            if (m == null){
                console.error("信物数据有问题");
                return [];
            }
            for (var k = 0;k < m.length;k++){
                var ls = m[k];
                var b = ls.belong_hero;
                for (let ss of b){
                    if (this.xinWuItemDic[ss] == null ) this.xinWuItemDic[ss] = new Array();
                    this.xinWuItemDic[ss].push(ls);                       
                }
            }
            //console.error("this.xinWuItemDic:",this.xinWuItemDic)
        }
        return this.xinWuItemDic[id];
    }

    this.onJingLi = function(val) {
        this.jingliData = val;
        //l.change("wife_jingli", val.num > 0);
        //facade.send(this.SERVANT_JING_LI);
    }

    this.onJiaQi = function(val) {
        this.jiaqiData = val;
        //l.change("wife_jiaqi", t.num > 0);
        facade.send(this.SERVANT_JIA_QI);
    };

    this.sendWeige = function() {
        var t = new proto_cs.hero.weige();
        JsonHttp.send(t);
    };

    this.sendJiaQi = function(t) {
        var e = new proto_cs.hero.hfjiaqi();
        e.num = t;
        JsonHttp.send(e, function() {
            i.alertUtil.alert18n("WIFE_CHE_MA_LING");
        });
    };

    this.sendTokenUpLv = function(t,s){
        var e = new proto_cs.hero.tokenUpLv();
        e.heroId = t;
        e.tokenId = s;
        JsonHttp.send(e);
    };

    this.sendFetterActivation = function(t,s){
        var e = new proto_cs.hero.fetterActivation();
        e.heroId = t;
        e.fetterId = s;
        JsonHttp.send(e);
    };

    this.sendTokenActivation = function(t,s){       
        var e = new proto_cs.hero.tokenActivation();
        e.heroId = t;
        e.tokenId = s;
        JsonHttp.send(e);
    }

    this.onHello = function(t) {
        i.utils.openPrefabView("wife/WifeWenHouView", !1, t);
        facade.send(this.WIFE_WINDOW);
    };

    this.onTravel = function(data) {
        let travelData = localcache.getItem(localdb.table_chuyouEvent, data.storyId);
        n.playerProxy.addStoryId(travelData.storystartid);
        i.utils.openPrefabView("StoryView", !1, {
            type: 91,
            canSkip: data.isRand == 1,
            extraParam: data
        });
        //i.utils.openPrefabView("wife/WifeChuYouView", !1, t);
        facade.send(this.WIFE_WINDOW);
    };

    this.sendStarUp = function(id) {
        let reqData = new proto_cs.hero.upStar();
        reqData.heroId = id;
        JsonHttp.send(reqData);
    };

    this.getTokenShili = function(tokenId) {
        if(null == this.tokenServerData || null == tokenId) {
            return 0;
        }
        let tokenData = null;
        for(let heroId in this.tokenServerData) {
            let tokenDatas = this.tokenServerData[heroId];
            for(let token in tokenDatas) {
                if(token == tokenId) {
                    tokenData = tokenDatas[token];
                    break;
                }
            }
            if(null != tokenData) {
                break;
            }
        }

        if(null == tokenData || (null != tokenData && tokenData.isActivation == 0)) { //信物没激活
            return 0;
        }
        
        let result = 0;
        let rad = 1;
        let lv = tokenData.lv;
        if (lv >= 1) {
            for (let i = 2; i <= lv + 1; i++) {
                let temp = localcache.getItem(localdb.table_tokenlvup, i);
                if (temp == null) {
                    break;
                }
                if (i <= lv) {
                    rad *= (1 + temp.attri / 100);
                }
            }          
        }
        let proplist = localcache.getItem(localdb.table_item, tokenId).type[2];
        for(let j = 0, jLen = proplist.length; j < jLen; j++) {
            result += Math.ceil(proplist[j].value * rad);
        }
        return result;
    }

    this.getMaxTokenShiliId = function(heroId) {
        let array = this.getXinWuItemListByHeroid(heroId);
        if(null != array) {
            let self = this;
            array.sort((a, b) => {
                return self.getTokenShili(b.id) - self.getTokenShili(a.id);
            });
            return array[0].id;
        } else {
            return 0;
        }
    };

    this.getMaxClotheShiliId = function(heroId) {
        let map = this.getHeroAllDress(heroId);
        let array = map['ownerDress'];
        array.sort((a, b) => {
            let shiliA = 0;
            for(let i = 0, len = a.prop.length; i < len; i++) {
                shiliA += a.prop[i].value;
            }
            let shiliB = 0;
            for(let i = 0, len = b.prop.length; i < len; i++) {
                shiliB += b.prop[i].value;
            }
            return shiliB - shiliA;
        });
        return array.length > 0 ? array[0].id : 0;
    };

    this.isHasHeroClothe = function(heroId, id) {
        let map = this.getHeroAllDress(heroId);
        let array = map['ownerDress'];
        let bHas = array.filter((data) => {
            return data.id == id;
        });
        return bHas && bHas.length > 0;
    };

    /**羁绊等级达成请求领取道具*/
    this.sendPickJibanAward = function(id){
        var e = new proto_cs.hero.pickJibanAward();
        e.id = id;
        JsonHttp.send(e,()=>{
            n.timeProxy.floatReward();
        });
    };

    /**刷新羁绊等级的奖励*/
    this.onjibanAward = function(data){
        this.servanetJiBanAward = data;
        this.updateServantJiBanRoadRed();
        facade.send("UPDATE_JIBANAWARD");
    };

    this.updateServantJiBanRoadRed = function () {
        let listdata = localcache.getFilters(localdb.table_hero_yoke_unlock,"type",3);
        let tmp = {};
        for (var ii = 0; ii < 6;ii++){
            tmp[ii+1] = n.jibanProxy.getHeroJbLv(ii+1).level;
        }        
        let listdata2 = listdata.filter((cfg)=>{
            return cfg.yoke_level <= tmp[cfg.hero_id];
        });
        this.servantJiBanRoadRed = {};
        for (var ii = 0; ii < listdata2.length;ii++){
            let cg = listdata2[ii];
            if (this.servanetJiBanAward == null || this.servanetJiBanAward.pickInfo == null || this.servanetJiBanAward.pickInfo.indexOf(cg.id) == -1){
                this.servantJiBanRoadRed[cg.hero_id] = true;
            }
        }
    };



    /**伙伴羁绊商店购买*/
    this.sendBuyShopItem = function(id){
        var e = new proto_cs.hero.buyShopItem();
        e.id = id;
        JsonHttp.send(e,()=>{
            n.timeProxy.floatReward();
        });
    };

    /**返回伙伴羁绊商店数据*/
    this.onHeroshop = function(data){
        this.heroShopData = data;
        facade.send("UPDATE_HEROSHOP");
    };

    /**返回伙伴使用的空间背景数据*/
    this.onHeroBlank = function (data) {
        this.heroBlankData = data;
        facade.send("UPDATE_HEROBLANK");
    };

    /**返回伙伴专属表情包数据*/
    this.onHeroEmoji = function(data){
        this.heroEmojiData = data;
    };

    /**使用背景*/
    this.sendSetBlanks = function(id,blankid){
        var e = new proto_cs.hero.setBlanks();
        e.id = id;
        e.blankid = blankid;
        JsonHttp.send(e,()=>{
            i.alertUtil.alert18n("PARYNER_ROOMTIPS31");
        });
    };

    /**获取当前伙伴的背景id*/
    this.getServantBgId = function(heroid){
        if (this.heroBlankData == null || this.heroBlankData.useBlanks == null || this.heroBlankData.useBlanks[heroid] == null){
            return this.heroBlankData.blanks[heroid][0];
        }
        return this.heroBlankData.useBlanks[heroid]
    };

    /**获取伙伴培养物品的上限*/
    this.getTrainItemMaxNum = function (heroid) {
        let listdata  = localcache.getFilters(localdb.table_hero_yoke_unlock,"hero_id",heroid);
        let jibanlevel = n.jibanProxy.getHeroJbLv(heroid).level;
        let listdata2 = [];
        for (let ii = 0; ii < listdata.length;ii++){
            let cg = listdata[ii];
            if (cg.type == 6){
                listdata2.push(cg);
            }
        }
        listdata2.sort((a,b)=>{
            return a.yoke_level > b.yoke_level ? -1 : 1;
        })
        for (let ii = 0; ii < listdata2.length;ii++){
            if (listdata2[ii].yoke_level <= jibanlevel){
                return listdata2[ii].set;
            }
        }
        return listdata2[listdata2.length -1].set;
    };

    /**获取伙伴羁绊之路的icon图片路径*/
    this.getHeroJiBanRoadIconUrl = function(cfg){
        if (cfg == null) return "";
        switch(cfg.type){
            case 0:{
                return "";
            }
            break;
            case 1:{
                return UIUtils.uiHelps.getSmallServantBgImg(cfg.set[0]);
            }   
            break;
            case 3:{
                return UIUtils.uiHelps.getItemSlot(cfg.jiangli[0].id);
            }   
            break;
            case 7:{
                return UIUtils.uiHelps.getItemSlot(cfg.set[0]);
            }   
            break;
        }
        if (cfg.icon && cfg.icon != ""){
            return UIUtils.uiHelps.getServantJiBanRoadImg(cfg.icon)
        }
    };


    //请求问候 (如果heroId和为null则是随机问候)
    this.reqVisit = function(cb, heroId, type) {
        let req = null == heroId ? new proto_cs.hero.randVisit() : new proto_cs.hero.visit();
        req.heroId = heroId;
        req.type = type;
        JsonHttp.send(req, (data) => {
            cb && cb();
        });
    };

    //问候的答案 index: 答案的索引 id: 配置表id(猜拳为null)
    this.chooseVisitAnswer = function(index, id, callback) {
        let req = new proto_cs.hero.chooseAnswer();
        req.index = index;
        req.id = id;
        JsonHttp.send(req, () => {
            callback && callback();
        });
    };

    //结束拜访游戏
    this.endVisitGame = function(cb, bNotShow) {
        let req = new proto_cs.hero.endGame();
        JsonHttp.send(req, () => {
            if(!bNotShow) {
                n.timeProxy.floatReward();
            }
            cb && cb();
        });
    };

    //拜访回调
    this.onVisit = function(data) {
        this.visitData = data;
        facade.send(this.SERVANT_VISIT);
    };

    //拜访答案回调
    this.onVisitAnswer = function(data) {
        this.rightData = data;
        facade.send(this.SERVANT_RIGHT);
    };

    //检查之前游戏是否结束
    this.checkGameEnd = function(callback) {
        if(null == this.rightData) {
            return true;
        }
        if(this.rightData.qaType == 0) {
            return true;
        } else {
            this.endVisitGame(callback, true);
            return false;
        }
    };

    /**返回邀约的事件信息*/
    this.onInvite = function(data) {
        this.inviteEventData = data;
        let cd = i.utils.getParamInt("game_refresh");
        let nextTime = this.inviteBaseInfo.refreshTime + cd;
        if(i.timeUtil.second > nextTime) {
            n.playerProxy.sendAdok("invite_event");
        } else {
            i.timeUtil.addCountEvent(true, nextTime, "invite_event", () => {
                n.playerProxy.sendAdok("invite_event");
            });
        }
        facade.send("UPDATE_CITY_INVITE_INFO");
    };

    /**返回邀约的基本信息，次数，恢复时间*/
    this.onInviteInfo = function(data){
        this.inviteBaseInfo = data;
        let cd = i.utils.getParamInt("game_addtime");
        let nextTime = this.inviteBaseInfo.lastRefreshTime + cd;
        if(i.timeUtil.second > nextTime) {
            if(this.inviteBaseInfo.inviteCount < 3) {
                n.playerProxy.sendAdok("invite_count");
            }
        } else {
            i.timeUtil.addCountEvent(this.inviteBaseInfo.inviteCount < 3, nextTime, "invite_count", () => {
                n.playerProxy.sendAdok("invite_count");
            });
        }
        this.onMiniCountUpdate();
        facade.send("UPDATE_INVITE_INFO");
    };

    this.onMiniCountUpdate = function() {
        if(null == this.inviteBaseInfo) {
            return;
        }
        let HeroDatas = localcache.getList(localdb.table_hero);
        let bUnlock = false;
        for(let i = 0, len = HeroDatas.length; i < len; i++) {
            let jibanLevelData = n.jibanProxy.getHeroJbLv(HeroDatas[i].heroid);
            bUnlock = jibanLevelData.fish == 1 || jibanLevelData.food == 1;
            if(bUnlock) {
                break;
            }
        }
        l.change("mini_times", this.inviteBaseInfo.inviteCount > 0 && bUnlock);       
    };

    this.onFishInfo = function(data){
        console.error("onFishInfo:",data)
        this.fishBaseInfo = data;
        facade.send("UPDATE_FISHINFO");
    };

    this.onFoodInfo = function(data){
        this.foodBaseInfo = data;
        facade.send("UPDATE_FOODINFO");
	};

    this.onCollectInfo = function(data){
        this.collectInfo = data;
        this.collectAwardInfo = data.collectAward;
        let fengwuzhiFishRedFlag = false;
        let fengwuzhiFoodRedFlag = false;
        for (let key in data.maxScore){
            let cg = data.maxScore[key];
            let cfg = localcache.getItem(localdb.table_max_rwd,key);
            if (cfg && cg.num >= cfg.maxweight && cg.pick == 0){
                if (cfg.type == 1){
                    fengwuzhiFishRedFlag = true;
                }
                else{
                    fengwuzhiFoodRedFlag = true;
                }
            }
        }
        if (!fengwuzhiFishRedFlag || !fengwuzhiFoodRedFlag){
            let listcfg = localcache.getList(localdb.table_game_item);
            for (var ii = 0; ii < listcfg.length; ii++){
                let cg = listcfg[ii];
                if (cg.id == 30000) continue;
                let cData = this.collectAwardInfo[String(cg.id)]
                if (cData){
                    let cfg = localcache.getFilter(localdb.table_collection_rwd,"rid",cData.rwd+1,"type",cg.id);
                    if (cfg && cData.num >= cfg.need){
                        if (cg.type == 1){
                            fengwuzhiFishRedFlag = true;
                        }
                        else{
                            fengwuzhiFoodRedFlag = true;
                        }
                    }
                }
            }
        }
        l.change("fengwuzhi_fish",fengwuzhiFishRedFlag);
        l.change("fengwuzhi_food",fengwuzhiFoodRedFlag);
        facade.send("UPDATE_COLLECT_BASEINFO");
    };

    /**返回邀约的购买信息**/
    this.onBuyInviteInfo = function(data){
        this.buyInviteInfo = data;
    };

    /**是否可以使用邀请*/
    this.isCanUseInvite = function(){
        if (this.inviteBaseInfo.inviteCount <= 0){
            let inviteItemId = i.utils.getParamInt("game_item");
            let count = n.bagProxy.getItemCount(inviteItemId);
            let vipCfg = localcache.getItem(localdb.table_vip,n.playerProxy.userData.vip);
            let data = this.buyInviteInfo;
            if (count > 0 && data.useItem < vipCfg.invite_item){
                i.utils.showConfirmItem(
                    i18n.t("FISH_TIPS34"),
                    inviteItemId,
                    n.bagProxy.getItemCount(inviteItemId),
                    function() {
                        if (n.bagProxy.getItemCount(inviteItemId) < 1){
                            n.timeProxy.showItemLimit(inviteItemId);
                            return;
                        }
                        n.miniGameProxy.sendBuyCount(1);
                    },
                    "COMMON_YES"
                );
                return false;  
            }
            if (data.useCash >= vipCfg.invite_cash){
                i.alertUtil.alert18n("FISH_TIPS36");
                return false;
            }
            let needCash = (data.useCash + 1) * 100;
            // i.utils.showConfirmItem(
            //     i18n.t("FISH_TIPS35",{v1:needCash}),
            //     1,
            //     n.bagProxy.getItemCount(1),
            //     function() {
            //         if (n.bagProxy.getItemCount(1) < needCash){
            //             n.timeProxy.showItemLimit(1);
            //             return;
            //         }
            //         n.miniGameProxy.sendBuyCount(0);
            //     },
            //     "COMMON_YES"
            // );
            i.utils.showConfirm(i18n.t("FISH_TIPS35",{v1:needCash}), () => {
                if (n.bagProxy.getItemCount(1) < needCash){
                    n.timeProxy.showItemLimit(1);
                    return;
                }
                n.miniGameProxy.sendBuyCount(0);
            });  
            return false;
        }
        else{
            return true;
        }
    };

}

exports.ServantProxy = ServantProxy;
