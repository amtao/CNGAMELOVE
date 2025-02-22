var i = require("StateImg");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblOld: cc.Label,
        lblNew: cc.Label,
        luckImg: i,
        heroImg: n,
        wifeImg: n,
        jbNode: cc.Node,
        lblNum: cc.Label,
        nodeJb: cc.Node,
        nodeYQ: cc.Node,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            this.nodeJb.active = this.heroImg.node.active = 1 == t.type;
            this.nodeYQ.active = this.wifeImg.node.active = 2 == t.type;
            if (1 == t.type) this.heroImg.url = l.uiHelps.getServantSpine(t.id);
            else if (2 == t.type) {
                var e = localcache.getItem(localdb.table_wife, t.id);
                this.wifeImg.url = l.uiHelps.getWifeBody(e.res);
            }
            this.lblOld.string = i18n.t("COMMON_LEVEL_TXT", {
                lv: t.orgLv
            });
            this.lblNew.string = i18n.t("COMMON_LEVEL_TXT", {
                lv: t.lv
            });
            this.luckImg.total = 5;
            this.luckImg.value = t.lv;
            this.luckImg.node.active = t.lv <= 5;
            this.jbNode.active = t.lv > 5;
            this.lblNum.string = i18n.t("SERVANT_JI_BAN_FLOWER", {
                num: t.lv
            });
        }
    },
    onClickClose() {
        r.utils.closeView(this);
    },
});
