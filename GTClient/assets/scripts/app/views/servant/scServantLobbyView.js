let scRole = require('scServantLobbyItem');
let scUtils = require("Utils");
var initializer = require("Initializer");
let scUIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        roles: [scRole],
        sv: cc.ScrollView,
        lbTravalTimes: cc.Label,
        lbGreetTimes: cc.Label, 
    },

    onLoad: function() {
        facade.subscribe("SERVANT_JING_LI", this.onJingliUpdate, this);
        facade.subscribe("SERVANT_JIA_QI", this.onJiaQiUpdate, this);
        facade.subscribe("servantClose", this.onClickBack, this);
        facade.subscribe("SERVANT_UP", this.showList, this);
        facade.subscribe("PLAYER_HERO_SHOW", this.showList, this);
        facade.subscribe("SERVANT_TOKEN_UPDATE", this.showList, this);
        facade.subscribe("SERVANT_TOKENFETTER_UPDATE", this.showList, this);
        this.onJingliUpdate();
        this.onJiaQiUpdate();

        this.showList();
    },

    showList: function() {
        let hasData = initializer.servantProxy.servantList;
        for(let i = 0, len = this.roles.length; i < len; i++) {
            let role = this.roles[i];
            let cfgData = localcache.getItem(localdb.table_hero, role.id);
            let tmpData = hasData.filter((data) => {
                return data.id == role.id;
            });
            role.setData(cfgData, tmpData && tmpData.length > 0, i);
        }
    },

    onJingliUpdate() {
        let vipData = localcache.getItem(localdb.table_vip, initializer.playerProxy.userData.vip);
        let jingliData = initializer.servantProxy.jingliData;
        jingliData.num < vipData.jingli ? scUIUtils.uiUtils.countDown(jingliData.next, this.lbGreetTimes, () => {
            initializer.playerProxy.sendAdok(jingliData.label);
        },
        0 == jingliData.num) : this.lbGreetTimes.unscheduleAllCallbacks();
        jingliData.num > 0 && (this.lbGreetTimes.string = i18n.t("COMMON_NUM", {
            f: jingliData.num,
            s: vipData.jingli
        }));
    },

    onJiaQiUpdate() {
        let vipData = localcache.getItem(localdb.table_vip, initializer.playerProxy.userData.vip);
        let jiaqiData = initializer.servantProxy.jiaqiData;
        jiaqiData.num < vipData.jiaqi ? scUIUtils.uiUtils.countDown(jiaqiData.next, this.lbTravalTimes, () => {
            initializer.playerProxy.sendAdok(jiaqiData.label);
        },
        0 == jiaqiData.num) : this.lbTravalTimes.unscheduleAllCallbacks();
        jiaqiData.num > 0 && (this.lbTravalTimes.string = i18n.t("COMMON_NUM", {
            f: jiaqiData.num,
            s: vipData.jiaqi
        }));
    },

    onClickLeft: function() {
        this.sv.scrollToLeft(0.1);
    },

    onClickRight: function() {
        this.sv.scrollToRight(0.1);
    },

    onCLickGreet: function() {
        if(initializer.servantProxy.jingliData.num <= 0) {
            let cost = scUtils.utils.getParamInt("hg_cost_item_jl");
            let count = initializer.bagProxy.getItemCount(cost);
            if (count <= 0) scUtils.alertUtil.alertItemLimit(cost);
            else {
                let itemData = localcache.getItem(localdb.table_item, cost);
                scUtils.utils.showConfirmItem(i18n.t("WIFE_USE_JING_LI_DAN", {
                    name: itemData.name,
                    num: 1
                }), cost, count, () => {
                    initializer.servantProxy.sendWeige();
                }, "WIFE_USE_JING_LI_DAN");
            }
        } else {
            initializer.servantProxy.sendSJXO();
        }
    },

    onClickTravel: function() {
        if(initializer.servantProxy.jiaqiData.num <= 0) {
            let cost = scUtils.utils.getParamInt("jiaqi_cost_item_chuyou");
            let count = initializer.bagProxy.getItemCount(cost);
            if (count <= 0) scUtils.alertUtil.alertItemLimit(cost);
            else {
                let itemData = localcache.getItem(localdb.table_item, cost);
                scUtils.utils.showConfirmItem(i18n.t("WIFE_USE_CHUYOU", {
                    name: itemData.name,
                    num: 1
                }), cost, count, () => {
                    initializer.servantProxy.sendJiaQi(1);
                },
                "WIFE_USE_CHUYOU");
            }
        } else {
            initializer.servantProxy.sendSJCY();
        }
    },

    onClickBack: function() {
        scUtils.utils.closeView(this, !0);
    },
});
