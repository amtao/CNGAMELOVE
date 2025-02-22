var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("ApiUtils");
var r = require("Initializer");
var a = require("Config");
var s = require("UIUtils");

cc.Class({
    extends: i,
    properties: {
        lblGold: cc.Label,
        lblCost: cc.Label,
        nodeDouble: cc.Node,
        url: n,
    },

    ctor() {},

    onClickItem() {
        var t = this._data;
        if(t)
        {
            l.apiUtils.recharge(
                r.playerProxy.userData.uid,
                a.Config.serId,
                t.diamond,
                t.ormb,
                t.diamond + r.playerProxy.getKindIdName(1, 1),
                0,
                null,
                t.cpId,
                t.dollar,
                t.dc
            );
        }
        // t && l.apiUtils.recharge(r.playerProxy.userData.uid, a.Config.serId, t.diamond, t.ormb, t.diamond + r.playerProxy.getKindIdName(1, 1), 0);
    },

    showData() {
        var t = this._data;
        if (t) {
            this.lblGold.string = t.diamond + "";
            this.lblCost.string = t.rmb + "";
            this.nodeDouble.active = 1 != t.beishu;
            this.url.url = s.uiHelps.getChargeItem(t.ormb);
        }
    },
});
