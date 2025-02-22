var Utils = require("Utils");
var n = require("List");
var r = require("Initializer");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
var _ = require("PlayerProxy");
var d = require("ServantStarShow");
var u = require("LangSprite");
let TimeProxy = require('TimeProxy');

cc.Class({
    extends: cc.Component,
    properties: {
        prg: cc.ProgressBar,
        lblExp: cc.Label,
        lblLv: cc.Label,
        lblName: cc.Label,
        lblEps: [cc.Label],
        //nodeUp: cc.Node,
        sFrame: [cc.SpriteFrame],
        servantShow: UrlLoad,
        aniLv: cc.Animation,
        lblShiLi: cc.Label,
        btnLvUp: cc.Node,
        btnTiBa: cc.Node,
        //skillNode: cc.Node,
        skillGhList: n,
        proNode: cc.Node,
        check: cc.Toggle,
        spine: u,
        // lblYueli: cc.Label,
        // lblGold: cc.Label,
        lblSkillName: cc.Label,
        starShow: d,
        lblTotalZZ: cc.Label,
        redTanlent: cc.Node,
        lbLove: cc.Label,
        redSkill: cc.Node,
        nArrow: cc.Node,
        redLvUp: cc.Node,
        redTiba: cc.Node,
        spaceDot: cc.Node,
        nodesuixing: cc.Node,
    },

    ctor() {
        //this.lastData = new _.RoleData();
        this.tabIndex = "1";
        this._curHero = null;
        this._curHeroDress = null;
        this._curIndex = 0;
        this._oldHeroLv = 0;
        this.voiceSys = null;
    },

    onLoad() {
        isInspecialSc = this;
        this.defaultRightY = this.servantShow.node.position.y;
        facade.subscribe("servantClose", this.onBack, this);
        facade.subscribe(r.playerProxy.PLAYER_USER_UPDATE, this.onResUpdate, this);
        facade.subscribe("PLAYER_HERO_SHOW", this.onHeroShow, this);
        facade.subscribe("UPDATE_HERO_JB", this.onJibanUpdate, this);
        facade.subscribe("SERVANT_TOKEN_UPDATE",this.onJibanUpdate,this);
        facade.subscribe("SERVANT_TOKENFETTER_UPDATE",this.onJibanUpdate,this);
        facade.subscribe("UPDATE_JIBANAWARD", this.onJibanUpdate, this);
        facade.subscribe("UPDATE_INVITE_INFO", this.onJibanUpdate, this);

        r.servantProxy.sortServantList();
        var e = this.node.openParam,
        o = e.hero,
        n = e.type;
        let bClicked = false;
        if(null != n) {
            var l = r.servantProxy.getHeroData(o);
            this.heroId = o;
            this._curHero = l;
            this._curIndex = r.servantProxy.servantList.indexOf(l);
            this._oldHeroLv = this._curHero.level;
            if(e.type == 2) {
                bClicked = true;
            }
        } else if (o) {
            var l = r.servantProxy.getHeroData(o.heroid);
            this.heroId = o.heroid;
            this._curHero = l;
            this._curIndex = r.servantProxy.servantList.indexOf(l);
            this._oldHeroLv = this._curHero.level;
        }
        this.showData();
        //this.onClickTab(null, n);
        facade.subscribe("SERVANT_UP", this.updateServant, this);
        this.updateServant();

        this.servantShow.loadHandle = () => {
            this.servantAnchorYPos(this.servantShow);              
        };

        this.servantShow.url = UIUtils.uiHelps.getServantSpine(this._curHero.id);
        this.starShow.setValue(this._curHero.star);
        this.onPlayVoice(null, null);
        this.onResUpdate();
        this.check.isChecked = r.servantProxy.isLevelupTen;
        e.isTrain && this.onClickTran();
        localcache.getGroup(localdb.table_heroClothe, "heroid", this._curHero.id);
        if(bClicked) {
            this.onClickTran();
        }
        
        this.nArrow.active = r.servantProxy.servantList.length > 1;

        //this.spaceDot.addBinding(["bookroom_token" + this._curHero.id], true);
        this.node.getComponent(cc.Animation).on("stop", () => {
            facade.send("GUIDE_ANI_FINISHED");
        });
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultRightY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },

    onClost() {
        r.servantProxy.curSelectId = 0;
        // Utils.audioManager.playSound("", !0);
        Utils.utils.closeView(this, !0);
        r.servantProxy.isRenMaiOpen && (r.servantProxy.isRenMaiOpen = !1);
        isInspecialSc = false;       
    },

    onBack() {
        r.servantProxy.curSelectId = 0;
        // Utils.audioManager.playSound("", !0);
        r.servantProxy.isRenMaiOpen ? (r.servantProxy.isRenMaiOpen = !1) : Utils.utils.openPrefabView("servant/ServantLobbyViewNew");
        Utils.utils.closeView(this);
    },

    onClickUp() {
        if (this._curHero) {
            var e = localcache.getItem(localdb.table_nobility, this._curHero.senior);
            var nextCfg = localcache.getItem(localdb.table_nobility, this._curHero.senior + 1);
            if (e && r.playerProxy.userData.level < e.player_level && this._curHero.level == e.max_level){
                let officeCfg = localcache.getItem(localdb.table_officer,e.player_level);
                let nameStr = officeCfg.name;
                Utils.alertUtil.alert(i18n.t("SERVANT_LEVELUP_TIPS",{name:nameStr}));
                return;
            }
            if (e && e.need == null && this._curHero.level == e.max_level && r.playerProxy.userData.level >= e.player_level && nextCfg != null){
                r.servantProxy.sendUpSenior(this._curHero.id,false);
            }
            var t = localcache.getItem(localdb.table_heroLvUp, this._curHero.level + "");
            // t && (t.cost, this._curHero.exp);
            if (this._curHero.level >= 400) {
                Utils.alertUtil.alert(i18n.t("SERVANT_LEVEL_MAX"));
                return;
            }
            if (0 == r.playerProxy.userData.coin) {
                Utils.alertUtil.alertItemLimit(2);
                return;
            }
            this.check.isChecked ? r.servantProxy.sendLvUpTen(this._curHero.id) : r.servantProxy.sendLvUp(this._curHero.id);
        }
    },

    onHideSpine() {
        this.spine.setActive = !1;
    },

    onClickAdd(t, e) {
        Utils.utils.openPrefabView("");
    },

    onClickJiBan() {
        Utils.utils.openPrefabView("jiban/JibanDetailView", !1, {
            heroid: this._curHero.id
        });
    },

    onClickRecharge: function() {
        let funUtils = TimeProxy.funUtils;
        funUtils.openView(funUtils.recharge.id);
    },
    // onClickTab(t, e) {
    //     var o = parseInt(e) - 1;
    //     this.tabIndex = e;
    //     0 == o ? this.tanlent.updateShow(this._curHero) : 1 == o && (this.skillList.data = this._curHero.pkskill);
    //     this.tanlentNode.active = "1" == e;
    //     this.skillNode.active = "2" == e;
    //     this.proNode.active = "4" == e;
    //     this.nodeHeroShow.active = this._curHero.id != r.playerProxy.heroShow;
    //     "1" == e && this.tablentScroll.scrollToLeft();
    // },
    updateServant() {
        var t = this;
        this._curHero = r.servantProxy.servantList[this._curIndex];
        var e = localcache.getItem(localdb.table_nobility, this._curHero.senior),
        o = localcache.getItem(localdb.table_nobility, this._curHero.senior + 1);
        this.btnLvUp.active = this._curHero.level < e.max_level || e.need == null;
        this.btnTiBa.active = this._curHero.level == e.max_level && null != o && e.need != null;
        this.showData();
        //this.skillList.data = this._curHero.pkskill;
        if (this._oldHeroLv < this._curHero.level) {
            var n = this;
            UIUtils.uiUtils.showPrgChange(this.prg, 0, 1, 1, 10,
            function() {
                n.prg.progress = 0;
                t.spine.animation = "animation";
                t.spine.setActive = !0;
                t.scheduleOnce(t.onHideSpine, 2);
                Utils.audioManager.playSound("levelup", !0, !0);
            });
        }
        this._oldHeroLv = this._curHero.level;
        this.redTanlent.active = r.servantProxy.getTanlentUp(this._curHero);
        this.redSkill.active = r.servantProxy.getSkillUp(this._curHero);
        this.redLvUp.active =  r.servantProxy.getLevelUp(this._curHero);
        this.redTiba.active =  r.servantProxy.isCanTiBa(this._curHero);
        this.onJibanUpdate();
        this.onHeroShow();
    },

    onJibanUpdate() {
        // var t = r.jibanProxy.getHeroJbLv(this._curHero.id).level % 1e3,
        // e = r.jibanProxy.getHeroNextJb(this._curHero.id, t);
        // //this.luckImg.setValue(5, t);
        // var o = r.jibanProxy.getHeroJbLv(this._curHero.id),
        // i = t > 1 ? " (" + i18n.t("COMMON_PROP5") + "+" + o.prop / 100 + "%)": "";
        //this.lblJbValue.string = null == e ? i: r.jibanProxy.getHeroJB(this._curHero.id) + "/" + (e ? e.yoke: "") + i;
        //this.lbLove.string = r.jibanProxy.getHeroJbLv(this._curHero.id).level%1000;
        let jibanLevelData = r.jibanProxy.getHeroJbLv(this._curHero.id);   
        this.spaceDot.active = r.servantProxy.servantJiBanRoadRed[this._curHero.id] || r.servantProxy.dicTokenRed[this._curHero.id]
         || (null != r.servantProxy.inviteBaseInfo && r.servantProxy.inviteBaseInfo.inviteCount > 0 && (jibanLevelData.fish == 1 || jibanLevelData.food == 1));
    },

    showData() {
        var t = localcache.getItem(localdb.table_hero, this._curHero.id + ""),
        e = localcache.getItem(localdb.table_heroLvUp, this._curHero.level + "");
        r.jibanProxy.getJibanType(1, this._curHero.id);
        if (t) {
            this.lblName.string = t.name;
            //t.spec;
            // var o = t.spec[0];
            // // this.imgSpe1.url = UIUtils.uiHelps.getLangSp(o);
            // // this.imgSpe2.node.active = t.spec.length > 1;
            // if (t.spec.length > 1) {
            //     o = t.spec[1];
            //     this.imgSpe2.url = UIUtils.uiHelps.getLangSp(o);
            // }
        }
        for (var n = 0,
        l = 0; l < this.lblEps.length; l++) {
            var a = l + 1;
            n += this._curHero.aep["e" + a];
            this.lblEps[l].string = this._curHero.aep["e" + a];
        }
        var c = e ? e.cost - this._curHero.exp: 0;
        this.lblExp.string = 0 != c ? i18n.t("SERVANT_UP_NEED", {
            exp: Utils.utils.formatMoney(c)
        }) : i18n.t("SERVANT_LV_MAX");
        this.prg.progress = e ? this._curHero.exp / e.cost: 1;
        this.lblLv.string = i18n.t("COMMON_LV", {
            lv: this._curHero.level
        });
        this.lblShiLi.string = n;
        var _ = this._curHero.zz.e1 + this._curHero.zz.e2 + this._curHero.zz.e3 + this._curHero.zz.e4;
        this.lblTotalZZ.string = _;
        r.servantProxy.curSelectId = this._curHero.id;
        
    },
    
    showClickData(t) {
        this._curIndex += t;
        this._curIndex = this._curIndex < 0 ? r.servantProxy.servantList.length - 1 : this._curIndex;
        this._curIndex = this._curIndex > r.servantProxy.servantList.length - 1 ? 0 : this._curIndex;
        this._curHero = r.servantProxy.servantList[this._curIndex];
        this._oldHeroLv = this._curHero.level;
        this.servantShow.url = UIUtils.uiHelps.getServantSpine(this._curHero.id);
        this.starShow.setValue(this._curHero.star);
        this.showData();
        this.updateServant();
        //this.onClickTab(null, this.tabIndex);
        this.onPlayVoice(null, null);
        localcache.getGroup(localdb.table_heroClothe, "heroid", this._curHero.id);
    },

    onClickTiBa() {
        if (this._curHero) {
            var e = localcache.getItem(localdb.table_nobility, this._curHero.senior);
            if (r.playerProxy.userData.level < e.player_level){
                let officeCfg = localcache.getItem(localdb.table_officer,e.player_level);
                let nameStr = officeCfg.name;
                Utils.alertUtil.alert(i18n.t("SERVANT_LEVELUP_TIPS2",{name:nameStr}));
                return;
            }
        }
        Utils.utils.openPrefabView("servant/ServantAdvance", null, this._curHero);
    },

    onClickTran() {
        //r.servantProxy.curSelectId = this._curHero.id;
        Utils.utils.openPrefabView("servant/ServantTrainView", null, this._curHero);
    },

    onClickSetHero() {
        if (this._curHero) {
            // if (r.xianyunProxy.isXianYun(this._curHero.id)) {
            //     Utils.alertUtil.alert18n("XIAN_YUN_ZHENG_ZAI_DU_JIA");
            //     return;
            // }
            r.playerProxy.sendHeroShow(this._curHero.id);
            var t = localcache.getItem(localdb.table_hero, this._curHero.id);
            Utils.alertUtil.alert(i18n.t("SERVANT_GUAN_SHI", {
                name: t.name
            }));
        }
    },

    onPlayVoice(t, e) {
        if ("1" != e || !Utils.audioManager.isPlayLastSound()) {
            let chooseDressID = r.servantProxy.getHeroDress(this._curHero.id);
            let cfgData = (chooseDressID && chooseDressID > 0)?localcache.getFilter(localdb.table_heroDress, "id", chooseDressID):null;
            if(cfgData && cfgData.voice != "" && cfgData.voice != "0"){
                let voiceArray = cfgData.voice.split('|');
                let chooseVoice = voiceArray[Math.floor(Math.random() * voiceArray.length)];
                if (chooseVoice) {
                    // Utils.audioManager.playSound(chooseVoice, !0, !0);
                }
            }else{
                this.voiceSys = r.voiceProxy.randomHeroVoice(this._curHero.id);
                if (this.voiceSys) {
                    // this.voiceSys.herovoice && Utils.audioManager.playSound("servant/" + this.voiceSys.herovoice, !0, !0);
                }
            }
        }
    },

    onResUpdate() {
        // UIUtils.uiUtils.showNumChange(this.lblYueli, this.lastData.coin, r.playerProxy.userData.coin);
        // UIUtils.uiUtils.showNumChange(this.lblGold, this.lastData.cash, r.playerProxy.userData.cash);
        // this.lastData.coin = r.playerProxy.userData.coin;
        // this.lastData.cash = r.playerProxy.userData.cash;

        if(null != this.heroId) {
            this._curHero = r.servantProxy.getHeroData(this.heroId);
            this.starShow.setValue(this._curHero.star);
        }
    },

    onClickZhuanJi() {
        Utils.utils.openPrefabView("servant/ServantZhuanJi", null, this._curHero);
    },

    onClickZhiJi() {
        var t = localcache.getGroup(localdb.table_wifeSkill, "heroid", this._curHero.id)[0].wid,
        e = r.wifeProxy.getWifeData(t);
        if (null != e) 0 != e.skill.length ? Utils.utils.openPrefabView("servant/ServantZhiJiSkill", null, e) : Utils.alertUtil.alert(i18n.t("SERVANT_WITHOUT_WIFE"));
        else {
            var o = localcache.getItem(localdb.table_wife, t);
            Utils.alertUtil.alert(i18n.t("SERVANT_WITHOUT_NAME", {
                name: o.wname2
            }));
            Utils.utils.openPrefabView("wife/WifeInfo", !1, o);
        }
    },

    onHeroShow() {
        {//伙伴时装更新
            this.servantShow.url = UIUtils.uiHelps.getServantSpine(this._curHero.id);       
            let heroDressInfo = r.servantProxy.getHeroAllDress(this._curHero.id);
            this.nodesuixing.active =  this._curHero.id != r.playerProxy.heroShow;
        }
    },
    
    onClickCheck() {
        r.servantProxy.isLevelupTen = this.check.isChecked;
    },

    onClickTalk() {
        r.servantProxy.sendHeroTalk(this._curHero.id);
    },

    talkData(t) {
        if (t) if (0 == t.chatType) this.onPlayVoice(null, null);
        else {
            r.playerProxy.addStoryId(t.stroyid);
            Utils.utils.openPrefabView("StoryView", !1, {
                heroid: this._curHero.id,
                type: 4,
                talkType: 1
            });
        }
    },

    onClickDetail() {
        Utils.utils.openPrefabView("servant/ServantProDetail", null, this._curHero);
    },
    
    onClickLeader() {
        Utils.utils.openPrefabView("servant/ServantLeader", null, this._curHero);
    },

    onClickHuanZhuang() {
        Utils.utils.openPrefabView("servant/ServantClothes", null, { id: this._curHero.id });
    },

    onClickSpace() {
        Utils.utils.openPrefabView("partner/PartnerZoneView", null, { id: this._curHero.id });
    },

    onClickLove: function() {
        Utils.utils.openPrefabView("servant/ServantLove", null, this._curHero);
    },

    onClickAptitude: function() {
        Utils.utils.openPrefabView("servant/BookUpLv", null, this._curHero.epskill);
    },

    onClickSpeciality: function() {
        Utils.utils.openPrefabView("servant/ServantSkillUp", null, {
            _skill: this._curHero.pkskill,
            _hero: this._curHero
        })
    },

    onClickGift: function() {
        Utils.utils.openPrefabView("servant/ServantGiftView");
    },

    onClickStarUp: function() {
        Utils.utils.openPrefabView("servant/ServantStarUp", null, this._curHero);
    },

    checkHero: function(id, bAdd) {
        let heroId = parseInt(id + "");
        bAdd ? heroId++ : heroId--;
        let heroList = localcache.getList(localdb.table_hero);
        heroList.sort((a, b) => {
            return a.heroid - b.heroid;
        });
        let lastId = heroList[heroList.length - 1].heroid;
        if(heroId <= 0) {
            heroId = lastId;
        } else if(heroId > lastId) {
            heroId = 1; 
        }
        let heroData = r.servantProxy.getHeroData(heroId);
        if(null != heroData) {
            this.heroId = heroId;
            this._curHero = heroData;
            this._curIndex = r.servantProxy.servantList.indexOf(heroData);
            this._oldHeroLv = this._curHero.level;
            this.updateServant();
            this.servantShow.url = UIUtils.uiHelps.getServantSpine(this._curHero.id);
            this.starShow.setValue(this._curHero.star);
            this.onPlayVoice(null, null);
        } else {
            return this.checkHero(heroId, bAdd);
        }
    },

    onClickLeft: function() {
        this.checkHero(this._curHero.id, false);
    },

    onClickRight: function() {
        this.checkHero(this._curHero.id, true);
    },
});
