var i = require("RenderListItem");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblTime: cc.Label,
        lblDes: cc.Label,
        lblNo: cc.Label,
    },
    ctor() {},
    onClickSteal() {
        var t = this.data;
        t && l.flowerProxy.sendStealCheck(t.uid);
    },
    showData() {
        var t = this.data;
        if (t) {
            this.lblNo.string = t.uid + "";
            this.lblTime.string = n.timeUtil.getDateDiff(t.time); - 1 == t.steal ? (this.lblDes.string = i18n.t("FLOWER_SHOU_HELP_TIP", {
                n: t.fname
            })) : (this.lblDes.string = i18n.t("FLOWER_STEAL_TIP", {
                n: t.fname,
                d: t.steal
            }));
        }
    },
});
