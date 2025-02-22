var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("Utils");
var s = require("ConfirmView");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblTotal: cc.Label,
        head: n,
        btnFY: cc.Node,
        yfyNode: cc.Node,
        lblYfy: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (null != t) {
            var e = r.servantProxy.getHeroData(t.hid);
            this._data.flag;
            if (e) {
                var o = localcache.getItem(localdb.table_hero, e.id);
                this.lblName.string = o.name;
                this.head.url = l.uiHelps.getServantHead(e.id);
                var i = e.aep.e1 + e.aep.e2 + e.aep.e3 + e.aep.e4;
                this.lblTotal.string = i + "";
                this.yfyNode.active = !1;
                this.btnFY.active = !0;
                this.lblYfy.string = i18n.t("JIU_LOU_JIN_RI_YFY", {
                    num: t.count
                });
            }
        }
    },
    onClickGoTo() {
        var t = this;
        r.playerProxy.userData && r.jiulouProxy.yhInfo && r.jiulouProxy.yhInfo.uid == r.playerProxy.userData.uid ? a.utils.showConfirm(i18n.t("JIU_LOU_JOIN_OWN_TIP"),
        function(e) {
            e != s.NO && t.onJoin();
        }) : this.onJoin();
    },
    onJoin() {
        var t = this._data,
        e = r.servantProxy.getHeroData(t.hid);
        if (t.count > 0) {
            var o = Math.pow(1.2, t.count - 1),
            i = Math.floor(50 * o);
            a.utils.showConfirmItem(i18n.t("JIU_LOU_FY_COST", {
                num: i
            }), 1, r.playerProxy.userData.cash,
            function() {
                if (r.playerProxy.userData.cash < i) a.alertUtil.alertItemLimit(1);
                else {
                    r.jiulouProxy.sendYhChi(r.jiulouProxy.yhInfo.uid, r.jiulouProxy.xwId, e.id);
                    a.utils.closeNameView("jiulou/JiulouHeroSelect");
                }
            },
            "JIU_LOU_FY_COST");
        } else {
            r.jiulouProxy.sendYhChi(r.jiulouProxy.yhInfo.uid, r.jiulouProxy.xwId, e.id);
            a.utils.closeNameView("jiulou/JiulouHeroSelect");
        }
    },
});
