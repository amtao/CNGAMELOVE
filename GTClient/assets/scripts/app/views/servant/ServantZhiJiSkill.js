var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        skillGhList: i,
        lblname: cc.Label,
        lblLv: cc.Label,
        lblEff: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        l.wifeProxy.skillWifeId = t.id;
        this.skillGhList.data = t.skill;
        var e = localcache.getItem(localdb.table_wife, t.id);
        this.lblname.string = e.wname2;
        var o = l.jibanProxy.getWifeJbLv(l.wifeProxy.skillWifeId);
        this.lblLv.string = i18n.t("WIFE_YOU_QING_LV", {
            lv: o.level % 100
        });
        this.lblEff.string = i18n.t("WIFE_SKILL_EFF", {
            num: o.prop / 100
        });
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
