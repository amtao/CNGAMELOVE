var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: i,
    properties: {
        selectImg: cc.Node,
        icon: cc.Node,
        select:{
            set: function(t) {
                this.selectImg.active = t;
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {},
    onLoad() {},
    showData() {},
    onClickItem(t, e) {
        if (n.qingMingProxy.isSelf) l.alertUtil.alert18n("QING_MING_ZHENG_ZAI_ZI_DONG");
        else {
            var o = parseInt(e);
            if (n.qingMingProxy.vehicleIndex != o && n.qingMingProxy.cfg.vehicle.length > o) if (n.qingMingProxy.rollData.cons >= n.qingMingProxy.cfg.vehicle[o].score) {
                n.qingMingProxy.vehicleIndex = o;
                l.alertUtil.alert(i18n.t("QING_MING_QIE_HUAN", {
                    name: n.qingMingProxy.cfg.vehicle[o].name
                }));
                facade.send(n.qingMingProxy.QING_MING_UPDATE_VEHICLE);
            } else l.alertUtil.alert(i18n.t("QING_MING_SCORE_LOCK", {
                num: n.qingMingProxy.cfg.vehicle[o].score,
                name: n.qingMingProxy.cfg.vehicle[o].name
            }));
        }
    },
});
