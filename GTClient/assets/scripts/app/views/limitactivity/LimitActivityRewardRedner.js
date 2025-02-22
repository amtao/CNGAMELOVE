var i = require("RenderListItem");
let initializer = require("Initializer");
var l = require("Utils");
var r = require("List");

cc.Class({

    extends: i,

    properties: {
        lblTitle: cc.RichText,
        btnYLQ: cc.Node,
        btnGet: cc.Button,
        list: r,
        itemBg: cc.Node,
        //bottom: cc.Node,
    },

    ctor() {},

    showData() {
        let t = this._data;
        if (t) {
            let actProxy = initializer.limitActivityProxy;
            let e = actProxy.curSelectData;
            if(null == e) { //钱庄
                this.lblTitle.string = i18n.t("BANK_TITLE", { val: localcache.getItem(localdb.table_officer, t.set).name });
                let level = initializer.playerProxy.userData.level;
                let pickInfo = initializer.purchaseProxy.bankInfo.pickInfo;
                let bGot = null != pickInfo && pickInfo[t.id];
                this.btnGet.interactable = level >= t.set && !bGot && initializer.purchaseProxy.bankInfo.buyTime;
                this.btnGet.node.active = !bGot;
                this.btnYLQ.active = bGot;
                let listdata = t.rwd;
                this.list.data = listdata;
                //this.list.data = t.rwd;
            } else {
                if(e.cfg.info.type == 1) {
                    let o = e.cons >= t.need ? "LIMIT_NEED_VALUE_1": "LIMIT_NEED_VALUE_2";
                    this.lblTitle.string = i18n.t("LIMIT_REWARD_NUMBER", { value: t.id })
                     + i18n.t(o, {
                       name: e.cfg.info.title,
                       have: l.utils.formatMoney(e.cons),
                       need: l.utils.formatMoney(t.need)
                    });
                    e.cfg.info.type == actProxy.RECHARGE_TYPE && (this.lblTitle.string += i18n.t("COMMON_CASH"));
                    this.btnGet.interactable = t.id == e.rwd + 1 && e.cons >= t.need;
                    this.btnGet.node.active = !(t.id <= e.rwd);
                    this.btnYLQ.active = t.id <= e.rwd;
                } else {
                    switch(e.cfg.info.id) {
                        case actProxy.TOTAL_CHARGE: {
                            let o = e.cons >= t.need ? "LIMIT_NEED_VALUE_1": "LIMIT_NEED_VALUE_2";
                            this.lblTitle.string = i18n.t("LIMIT_REWARD_NUMBER", { value: t.id })
                             + i18n.t(o, {
                               name: e.cfg.info.title,
                               have: l.utils.formatMoney(e.cons),
                               need: l.utils.formatMoney(t.need)
                            });
                            e.cfg.info.type == actProxy.RECHARGE_TYPE && (this.lblTitle.string += i18n.t("COMMON_CASH"));
                            this.btnGet.interactable = t.id == e.rwd + 1 && e.cons >= t.need;
                            this.btnGet.node.active = !(t.id <= e.rwd);
                            this.btnYLQ.active = t.id <= e.rwd;
                        } break;
                        case actProxy.GROUP_BUYING: {
                            this.lblTitle.string = i18n.t("ACT_PEOPLE_TITLE", { val: l.utils.formatMoney(t.need) });
                            this.btnGet.interactable = t.people <= e.cons.rechargePeople && parseInt(e.cons.myPayMoney) >= t.need;
                            let bGot = null != e.rwd[t.id.toString()];
                            this.btnGet.node.active = !bGot;
                            this.btnYLQ.active = bGot;
                        } break;
                    }
                }
                let listdata = t.items;
                for (var ii = 0; ii < listdata.length;ii++){
                    listdata[ii].isActive = true;
                }
                this.list.data = listdata;
                //this.list.data = t.items;
            }
        }
    },

    onClickGet() {
        let actProxy = initializer.limitActivityProxy;
        if(null == actProxy.curSelectData) {
            initializer.purchaseProxy.reqBankRwd(this._data.id);
        } else {
            actProxy.sendGetActivityReward(actProxy.curSelectData.cfg.info.id, this._data.id);
        }
    },
});
