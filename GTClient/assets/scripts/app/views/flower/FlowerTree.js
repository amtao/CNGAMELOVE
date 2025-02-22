var i = require("Utils");
var n = require("Initializer");
var l = require("LabelShadow");
var r = require("UIUtils");
var a = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        lblDes:l,
        lblLv:cc.Label,
        lblPer:cc.Label,
        prg:cc.ProgressBar,
        nodeFloat:cc.Node,
        url:a,
        lblChenlu:cc.Label,
        sp:sp.Skeleton,
        nodeShui:cc.Node,
    },
    onLoad() {
        facade.subscribe(n.flowerProxy.UPDATE_FLOWER_TREE, this.onShow, this);
        facade.subscribe(n.flowerProxy.UPDATE_FLOWER_LEVEL, this.onChenlu, this);
        r.uiUtils.floatPos(this.nodeFloat, 0, 10, 3);
        this.onShow();
        this.onChenlu();
    },
    onChenlu() {
        this.lblChenlu.string = i.utils.formatMoney(n.flowerProxy.level.chenlu);
    },
    onShow() {
        var t = n.flowerProxy.worldTree,
        e = localcache.getItem(localdb.table_worldtree, t.level);
        this.lblDes.string = i18n.t("FLOWER_TREE_DUIHUAN", {
            d: e.point
        });
        this.lblLv.string = i18n.t("COMMON_LV", {
            lv: t.level
        });
        this.lblPer.string = 0 == e.dew ? i18n.t("COMMON_MAX") : i18n.t("COMMON_NUM", {
            f: t.all,
            s: e.dew
        });
        this.prg.progress = t.all / e.dew;
        this.url.url = r.uiHelps.getWorldTree(t.level);
    },
    onClickRank() {
        n.flowerProxy.sendTreeRank();
    },
    onClickGX() {
        var t = this,
        e = this;
        i.utils.showConfirmItemMore(i18n.t("FLOWER_GX_CONFIRM"), 10001, n.flowerProxy.level.chenlu,
        function(o) {
            if (100 * o > n.flowerProxy.level.chenlu) i.alertUtil.alert18n("FLOWER_CHENLU_GX_LIMIT");
            else {
                n.flowerProxy.sendWorldTree(100 * o);
                e.nodeShui.active = !1;
                e.sp.node.active = !0;
                e.sp.animation = "animation";
                e.scheduleOnce(t.onClostShui, 1.2);
            }
        },
        null, null, null, null, 100);
    },
    onClostShui() {
        this.nodeShui.active = !0;
        this.sp.node.active = !1;
        n.timeProxy.floatReward();
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
