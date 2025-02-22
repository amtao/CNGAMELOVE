var i = require("List");
var n = require("RenderListItem");
var l = require("Utils");
var r = require("Initializer");
cc.Class({
    extends: n,
    properties: {
        lblTxt: cc.Label,
        ylqButton: cc.Node,
        getButton: cc.Node,
        btn: cc.Button,
        list: i,
        scroll: cc.ScrollView,
        bgNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = r.continuityRechargeProxy.data;
        this._curData = t;
        var e = this._data;
        if (e) {
            this.lblTxt.string = i18n.t("CONTINUITY_RECHARGE_XU_ZONG", {
                d: e.day,
                num: l.utils.formatMoney(e.need)
            });
            this.list.data = e.items;
            this.ylqButton.active = 1 == e.get;
            this.btn.interactable = t.day >= e.day && t.total >= e.need;
            this.getButton.active = !(1 == e.get);
        }
    },
    onClickGetItem() {
        var t = this.data,
        e = this._curData;
        t && (e.day >= t.day && e.total >= t.need ? r.continuityRechargeProxy.sendGetTotalReward(t.dc) : l.utils.openPrefabView("continuityrecharge/ContinuityRechargeItemView", !1, t));
    },
    setWidthHeigth(t, e) {
        this.node.height = e;
        this.getButton.y = this.ylqButton.y = 10 - (this.node.height - 58);
        this.bgNode.height = e;
    },
});
