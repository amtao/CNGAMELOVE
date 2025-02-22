var i = require("ItemSlotUI");
var n = require("UIUtils");
var l = require("Initializer");
var r = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblGoldCost: cc.Label,
        lblHaveGold: cc.Label,
        lblBallCost: cc.Label,
        lblHaveBall: cc.Label,
        goldItem: i,
        ballItem: i,
    },
    ctor() {
        this.curData = null;
    },
    onLoad() {
        this.curData = this.node.openParam;
        if (this.curData) {
            this.adult = localcache.getItem(localdb.table_adult, this.curData.honor);
            var t = new n.ItemSlotData();
            t.itemid = 1;
            t.count = this.adult.zhaoqin;
            this.goldItem.data = t;
            this.lblGoldCost.string = i18n.t("SON_MARRY_COST_GOLD", {
                str: this.adult.zhaoqin
            });
            this.lblHaveGold.string = i18n.t("SON_MARRY_COST_COUNT", {
                value: l.playerProxy.userData.cash
            });
            var e = new n.ItemSlotData();
            e.itemid = this.adult.itemid;
            e.count = 1;
            var o = localcache.getItem(localdb.table_item, this.adult.itemid);
            this.lblBallCost.string = i18n.t("SON_MARRY_COST_ITEM", {
                str: o.name
            });
            this.lblHaveBall.string = i18n.t("SON_MARRY_COST_COUNT", {
                value: l.bagProxy.getItemCount(this.adult.itemid)
            });
        }
    },
    onClickGold() {
        l.playerProxy.userData.cash < this.adult.zhaoqin ? r.alertUtil.alertItemLimit(1) : this.onSendMSG(1);
    },
    onClickBall() {
        0 != l.bagProxy.getItemCount(this.adult.itemid) ? this.onSendMSG(2) : r.alertUtil.alertItemLimit(this.adult.itemid);
    },
    onSendMSG(t) {
        1 == l.sonProxy.tiQinObj.marryType ? l.sonProxy.sendJieHun(l.sonProxy.tiQinObj.tUid, t, l.sonProxy.tiQinObj.tSid, this.curData.id) : 2 == l.sonProxy.tiQinObj.marryType && l.sonProxy.sendAgree(l.sonProxy.tiQinObj.tUid, t, l.sonProxy.tiQinObj.tSid, this.curData.id);
        r.utils.closeView(this);
    },
});
