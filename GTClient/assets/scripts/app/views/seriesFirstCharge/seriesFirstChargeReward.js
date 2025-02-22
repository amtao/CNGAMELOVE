
var a = require("UIUtils");
var i = require("ItemSlotUI");
var r = require("UrlLoad");
var List = require("List");
var initializer = require("Initializer");
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        rewardID: 0,                // 对应 qiandao.json “fuli_fc_ex”
        specialItems:[i],
        roleImg1: r,
        roleImg2: r,
        lblZZ: cc.Label,
        itemRewardList: List,
        icon_1: r,
        lbProp1: cc.Label,
        lbPropVal1: cc.Label,
        icon_2: r,
        lbProp2: cc.Label,
        lbPropVal2: cc.Label,
        lbName: cc.Label,
        roleSpine: r,
        lbMyCharge: cc.Label,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.data = null;
        this.showReward();
        
        let self = this;
        facade.subscribe(initializer.welfareProxy.CHARGE_FINISHED, () => {
            if(self.lbMyCharge) {
                self.lbMyCharge.string = i18n.t("MY_CHARGE") + initializer.seriesFirstChargeProxy.data.money;
            }
        }, this);
    },

    start () {

    },

    onEnable () {
        if (this.data && this.data.display) {
            this.playVoice(this.data.display);
        }
    },

    showReward() {
        var r = localcache.getItem(localdb.table_fuli_fc_ex, this.rewardID);
        if (!r) return;
        this.data = r;
        var s = [];
        var c = [];
        for (var i = 0; i < r.firstRwd.length; i++) {
            let rewardData = r.firstRwd[i];
            var _ = new a.ItemSlotData();
            _.id = rewardData.id;
            _.count = rewardData.count;
            _.kind = rewardData.kind;
            switch(rewardData.kind) {
                case 1: {
                    s.push(_);
                } break;
                case 95: {
                    c.push(_);
                } break;
                case 99: {
                    this.lbName.string = localcache.getItem(localdb.table_card, rewardData.id).name;
                } break;
                case 111: {
                    this.lbName.string = localcache.getItem(localdb.table_heroDress, rewardData.id).name;
                    c.push(_);
                } break;
            }
        }
        
        // let userClothe = initializer.playerProxy.userClothe;
        // let roleInfo = {
        //     body: userClothe.body,
        //     ear: userClothe.ear,
        //     head: userClothe.head,
        //     animal: 0,
        //     effect: userClothe.effect,
        // };
        for (var i = 0; i < c.length; i++) {
            var itemSlot = this.specialItems[i];
            itemSlot.node.active = true;
            itemSlot.data = c[i];
            // if(c[i].kind == 2) {
            //     this.resetClothePart(c[i].id, roleInfo);
            // } else if(i == 3) {
            //     this.resetClothePart(c[i].id, roleInfo);
            // }
        }

        this.itemRewardList.data = s;
        this.showSpine(r.display);

        this.lbMyCharge.string = i18n.t("MY_CHARGE") + initializer.seriesFirstChargeProxy.data.money;
    },

    resetClothePart(itemID, roleInfo) {
        var clothesTable = localcache.getItem(localdb.table_userClothe, itemID);
        if (!clothesTable) return;
        switch (clothesTable.part) {
            case 1:
                roleInfo.head = itemID;
                break;
            case 2:
                roleInfo.body = itemID;
                break;
            case 3:
                roleInfo.ear = itemID;
                break;
        }
    },

    // kind 7 是wife, kind 8 是hero, kind 95是clothes
    // 最多两个
    showSpine (displayData) {
        if(displayData && this.roleSpine) {
            let suitId = displayData[0].id;
            //this.roleSpine.setSuitClothe(suitId);
            initializer.playerProxy.loadPlayerSpinePrefab(this.roleSpine,{suitId:suitId});
            let suitData = localcache.getItem(localdb.table_usersuit, suitId);
            this.lbName.string = suitData.name;
        }
        if (!displayData || !this.roleImg1) return;
        if (displayData.length > 1 && this.roleImg2) {
            this.roleImg1.node.active = true;
            this.roleImg2.node.active = true;
            this.roleImg1.url = this.getSpineUrl(displayData[0]);
            this.roleImg2.url = this.getSpineUrl(displayData[1]);
        } else {
            this.roleImg1.node.active = true;
            this.roleImg1.url = this.getSpineUrl(displayData[0]);
        }
    },

    getSpineUrl (data) {
        var url = "";
        switch (data.kind) {
            case 7:
                url = a.uiHelps.getServantSpine(data.id);
                this.showServantInfo(data.id);
                break;
            // case 8:
            //     var res = localcache.getItem(localdb.table_wife, data.id).res;
            //     url = a.uiHelps.getWifeBody(res);
            //     break;
            // case 95:
            //     var i = localcache.getItem(localdb.table_userClothe, data.id).model.split("|");
            //     url = a.uiHelps.getRoleSpinePart(i[0]);
            //     break;
            case 111: 
                let servantDress = localcache.getItem(localdb.table_heroDress, data.id);
                url = a.uiHelps.getServantSkinSpine(servantDress.model);
            break;
        }
        return url;
    },

    showServantInfo (heroId) {
        var heroData = localcache.getItem(localdb.table_hero, heroId);    
        if(this.lbName) {
            this.lbName.string = heroData.name;
        }
        let props = {};
        var skillCount = 0;
        for (var i = 0; i < heroData.skills.length; i++) {
            let skillData = localcache.getItem(localdb.table_epSkill, heroData.skills[i].id);
            skillCount += skillData.star;
            props["p" + skillData.ep] += 10 * skillData.star;
        }
        this.showSpecialityList(heroData, props);
        if (this.lblZZ) {
            this.lblZZ.string = i18n.t("SERVANT_ZHZZ", {
                zz: skillCount
            });
        }
    },

    // 特长最多两个
    showSpecialityList (heroData, props) {
        let func = (spec, val) => {
            if(spec) {
                this["icon_" + val].node.active = !0;
                this["icon_" + val].url = a.uiHelps.getLangSp(spec);
                this["lbProp" + val].string = a.uiHelps.getPinzhiStr(spec);
                this["lbPropVal" + val].string = spec > 4 ? "" : props["p" + spec];
            } else {
                this["icon_" + val].node.active = !1;
            }
        }
        this.icon_1 && func(heroData.spec[0], 1);
        this.icon_2 && func(heroData.spec[1], 2);
    },

    playVoice (displayData) {
        if (displayData.length <= 1) {
            var data = displayData[0];
            if (data.kind !== 7) return;
            var voiceSys = initializer.voiceProxy.randomHeroVoice(data.id);
            if (voiceSys && voiceSys.herovoice) {
                utils.audioManager.playSound("", !0);
                utils.audioManager.playSound("servant/" + voiceSys.herovoice, !0, !0);
            }
            return;
        }
        if (displayData.length === 2) {
            var kind1 = displayData[0].kind;
            var kind2 = displayData[1].kind;
            if (kind1 !== 8 || kind2 !== 7) return;
            var wifeVoiceSys = initializer.voiceProxy.randomWifeVoice(displayData[0].id);
            var heroVoiceSys = initializer.voiceProxy.randomHeroVoice(displayData[1].id);
            if (wifeVoiceSys && wifeVoiceSys.wifevoice) {
                utils.audioManager.playSound("", !0);
                utils.audioManager.playSound("wife/" + wifeVoiceSys.wifevoice, !0, !0, () => {
                    if (heroVoiceSys && heroVoiceSys.herovoice) {
                        utils.audioManager.playSound("servant/" + heroVoiceSys.herovoice, !0, !0);
                    }
                });
            }
        }
    }

    // update (dt) {},
});
