let scUtils = require("Utils");
let scUrlLoad = require("UrlLoad");
let scInitializer = require("Initializer");
let scUIUtils = require("UIUtils");
let scConfig = require("Config");

cc.Class({
    extends: cc.Component,

    properties: {
        lbName: cc.Label,
        nToken2: cc.Node,
        urlHero: scUrlLoad,
        nTipsParent: cc.Node,
        nTips: cc.Node,
        lbTips: cc.Label,

        urlDress: scUrlLoad,
        urlToken1: scUrlLoad,
        urlToken2: scUrlLoad,
        lbToken1: cc.Label,
        lbToken2: cc.Label,
        lbDress: cc.Label,
    },

    ctor: function() {
        this.iChooseDress = 0;
        this.iChooseToken1 = 0;
        this.iChooseToken2 = 0;
    },

    onLoad () {
        let fuyueProxy = scInitializer.fuyueProxy;
        this.iChooseDress = fuyueProxy.iSelectHeroDress;
        this.iChooseToken1 = fuyueProxy.iSelectToken;
        this.iChooseToken2 = fuyueProxy.iSelectToken1;
        fuyueProxy.updateChooseDot(this.iChooseToken1);

        let themeId = fuyueProxy.pFuyueInfo.themeId;
        let zhutiInfo = localcache.getItem(localdb.table_zhuti, themeId);
        this.nToken2.active = zhutiInfo.xinwu_num > 1;

        let heroData = localcache.getItem(localdb.table_hero, scInitializer.fuyueProxy.getFriendID());
        this.lbName.string = heroData.name;
        this.updateSpine();
        this.showToken();
        this.showClothe();

        this.checkCondition();
        facade.subscribe(scInitializer.fuyueProxy.TEMP_REFRESH_SELECT, this.updateInfo, this);
        facade.subscribe(scInitializer.jibanProxy.UPDATE_HERO_JB, this.checkCondition, this);
        facade.subscribe(scInitializer.servantProxy.SERVANT_UP, this.checkCondition, this);
    },

    checkCondition: function() {
        let fuyueProxy = scInitializer.fuyueProxy;

        let tipStr = null;
        let type = fuyueProxy.conditionType.heroep;
        let condition = fuyueProxy.checkCondition(type);
        let tips = condition == fuyueProxy.conformType.None ? fuyueProxy.getConditionStr(type, condition)
         : fuyueProxy.getConditionStr(type, condition.val, condition.id);
        let bHasTip = tips != null;
        if(bHasTip) {
            tipStr = tips;
        }

        type = fuyueProxy.conditionType.jiban;
        condition = fuyueProxy.checkCondition(type);
        tips = fuyueProxy.getConditionStr(type, condition);
        let bHasTip2 = tips != null;
        if(bHasTip2) {
            tipStr = bHasTip ? (tipStr + "\n" + tips) : tips;
        }
        bHasTip = bHasTip || bHasTip2;

        type = fuyueProxy.conditionType.herolvl;
        condition = fuyueProxy.checkCondition(type);
        tips = fuyueProxy.getConditionStr(type, condition);
        let bHasTip3 = tips != null;
        if(bHasTip3) {
            tipStr = bHasTip ? (tipStr + "\n" + tips) : tips;
        }
        bHasTip = bHasTip || bHasTip3
        
        this.nTipsParent.active = bHasTip;
        this.lbTips.string = bHasTip ? tipStr : " ";
        this.nTips.getComponent(cc.Layout).updateLayout();
        if(bHasTip) {
            let ani = this.nTipsParent.getComponent(cc.Animation);
            ani && ani.play("fuyue_tip_ani");
        }
        let self = this;
        this.scheduleOnce(() => {
            self.nTipsParent.setContentSize(self.nTips.width, self.nTips.height);
        }, 0.2);
    },

    updateInfo: function(data) {
        switch(data.set) {
            case scInitializer.fuyueProxy.conditionType.token: {
                data.type == 1 ? (this.iChooseToken1 = data.id) : (this.iChooseToken2 = data.id);
                this.showToken();
                scInitializer.fuyueProxy.updateChooseDot(this.iChooseToken1);
            } break;
            case scInitializer.fuyueProxy.conditionType.herodress: {
                this.iChooseDress = data.id;
                this.updateSpine(data.data);
                this.showClothe();
            } break;
        }
    },

    showToken: function() {
        let heroId = scInitializer.fuyueProxy.getFriendID();
        this.showTokenSingle(heroId, this.iChooseToken1, this.urlToken1, this.lbToken1);
        this.showTokenSingle(heroId, this.iChooseToken2, this.urlToken2, this.lbToken2);
    },

    showTokenSingle: function(heroId, id, scUrl, label) {
        if(id != 0) {
            let tokenData = localcache.getItem(localdb.table_item, id);
            if(tokenData && tokenData.belong_hero[0] == heroId) {
                scUrl.url = scUIUtils.uiHelps.getItemSlot(tokenData.icon);
                label.string = tokenData.name;
                return;
            } 
        }
        scUrl.url = scConfig.Config.skin + "/res/ui/fuyue/fuyue_xinfeng_anniu_tubiao_1";
        label.string = i18n.t("XIN_WU");
    },

    updateSpine: function(data) {
        let friendId = scInitializer.fuyueProxy.getFriendID();  
        if(null != data) {
            let cfgData = data['cfg'];

            if(null != cfgData) {
                //修改伙伴图像显示
                this.urlHero.url = scUIUtils.uiHelps.getServantSkinSpine(cfgData.model);
                //let chooseDressID = Initializer.servantProxy.getHeroDress(this._curHero.id);
                //let isDressed = chooseDressID == cfgData.id;
                this.iChooseDress = cfgData.id;
                this.playVoice(cfgData);
            } else {
                this.iChooseDress = 0;
                this.urlHero.url = scUIUtils.uiHelps.getServantSpine(friendId, false);
            }
        } else {
            let friendDressId = scInitializer.fuyueProxy.getFriendDress();
            let dressData = localcache.getItem(localdb.table_heroDress, friendDressId);
            if(friendDressId != 0 && dressData && dressData.heroid == friendId) {
                let skinData = scInitializer.fuyueProxy.getHeroSkinData(friendId, friendDressId);
                this.urlHero.url = scUIUtils.uiHelps.getServantSkinSpine(skinData.model);
            } else {
                this.urlHero.url = scUIUtils.uiHelps.getServantSpine(friendId, false);
            }
        }
    },

    showClothe: function() {
        let heroId = scInitializer.fuyueProxy.getFriendID();
        if(this.iChooseDress != 0) {
            let clotheData = localcache.getItem(localdb.table_heroDress, this.iChooseDress);
            if(clotheData && clotheData.heroid == heroId) {
                this.urlDress.url = scUIUtils.uiHelps.getHeroDressIcon(clotheData.model);
                this.lbDress.string = clotheData.name;
                return;
            }
        }
        this.urlDress.url = scConfig.Config.skin + "/res/ui/fuyue/fuyue_xinfeng_anniu_tubiao_2";
        this.lbDress.string = i18n.t("COMMON_DEFAULT");
    },

    onClickStronger: function() {
        let heroData = localcache.getItem(localdb.table_hero, scInitializer.fuyueProxy.getFriendID());
        scUtils.utils.openPrefabView("servant/ServantView", !1, {
            hero: heroData,
            tab: 4
        });
    },

    onClickToken: function(event, param) {
        if(this.checkTokenEnough()) {
            let num = Number(param);
            scUtils.utils.openPrefabView("fuyue/FuyueTokenListViewNew", null, { open: num, friendId: scInitializer.fuyueProxy.getFriendID()
             , id1: this.iChooseToken1, id2: this.iChooseToken2 });
        }
    },

    onClickClothe: function() {
        scUtils.alertUtil.alert(i18n.t("COMMON_ZANWEIKAIQI"));
        return;
        scUtils.utils.openPrefabView("fuyue/FuyueHeroClotheView", null, { dress: this.iChooseDress });
    },

    onClickSave: function() {
        let fuyueProxy = scInitializer.fuyueProxy;
        fuyueProxy.iSelectHeroDress = this.iChooseDress;
        fuyueProxy.iSelectToken = this.iChooseToken1;
        fuyueProxy.iSelectToken1 = this.iChooseToken2;
        facade.send(scInitializer.fuyueProxy.REFRESH_FRIEND);
        this.onClickClose();
        scUtils.utils.closeNameView("fuyue/FuyueHeroSelect");
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },

    playVoice(cfgData) {
        if (!scUtils.audioManager.isPlayLastSound()) {
            if(cfgData && cfgData.voice != "" && cfgData.voice != "0") {
                let voiceArray = cfgData.voice.split('|');
                let chooseVoice = voiceArray[Math.floor(Math.random() * voiceArray.length)];
                if (chooseVoice) {
                    scUtils.audioManager.playSound(chooseVoice, !0, !0);
                }
            }else{
                let voiceSys = scInitializer.voiceProxy.randomHeroVoice(cfgData.heroid);
                if (voiceSys) {
                    scUtils.audioManager.playSound("servant/" + voiceSys.herovoice, !0, !0);
                }
            }
        }
    },

    // 判断伙伴是否有信物道具
    checkTokenEnough() {
        let heroData = scInitializer.servantProxy.getHeroData(scInitializer.fuyueProxy.iSelectHeroId);
        // let ls = scInitializer.servantProxy.getXinWuItemListByHeroid(heroData.id);
        let tokens = scInitializer.servantProxy.getTokensInfo(heroData.id);
        let count = 0;
        if (tokens != null) {
            for (let k in tokens) {
                if (tokens[k].isActivation == 1) {
                    count++;
                }
            }
        }
        if (count == 0) {
            scUtils.utils.showConfirm(i18n.t("HERO_HASNOTTOKEN2"), () => {
                // TimeProxy.funUtils.openView(l.funUtils.servantView.id);
                scUtils.utils.openPrefabView("partner/TokenListView", null, heroData);
            });
            return false;
        }
        return true;
    },
});
