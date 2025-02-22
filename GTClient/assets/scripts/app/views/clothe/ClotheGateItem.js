var i = require("RenderListItem");
var n = require("Utils");
var l = require("Initializer");
var r = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        lblGate: cc.Label,
        lblName: cc.Label,
        nodeOver: cc.Node,
        nodeUnover: cc.Node,
        bg: cc.Sprite,
        bg1: cc.Sprite,
        sp: sp.Skeleton,
        nodeZhi: cc.Node,
    },
    ctor() {},
    onClickBtn() {
        var t = this.data;
        if (t) {
            if (l.clothePveProxy.info && l.clothePveProxy.info.info.eTime < n.timeUtil.second) {
                n.alertUtil.alert18n("ACTHD_OVERDUE");
                return;
            }
            n.utils.openPrefabView("clothe/ClotheItem", !1, t);
        }
    },
    showData() {
        var t = this.data;
        if (t) {
            var e = localcache.getItem(localdb.table_clothepve, t.gateid);
            this.lblGate.string = i18n.t("CLOTHE_PVE_GATE", {
                d: n.utils.getHanzi(t.id)
            });
            this.lblName.string = e ? e.name: "";
            if (null != l.clothePveProxy.base) {
                this.sp.node.active = this.nodeOver.active = t.id <= l.clothePveProxy.base.gate;
                this.nodeZhi.active = this.nodeUnover.active = t.id > l.clothePveProxy.base.gate;
                r.shaderUtils.setImageGray(this.bg, t.id > l.clothePveProxy.base.gate + 1);
                r.shaderUtils.setImageGray(this.bg1, t.id > l.clothePveProxy.base.gate + 1);
            }
        }
    },
});
