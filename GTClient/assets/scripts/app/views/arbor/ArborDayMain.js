var i = require("UrlLoad");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("Config");
cc.Class({
    extends: cc.Component,
    properties: {
        lblZy:cc.Label,
        lblTree:cc.Label,
        lblState:cc.Label,
        head:i,
        lblKe:cc.Label,
        lblKun:cc.Label,
        lblDui:cc.Label,
        content:cc.Node,
    },

    ctor(){
        this.oldTree = 0;
        this.isFirst = true;
    },
    onLoad() {
        facade.subscribe(r.arborDayProxy.ARBOR_DAY_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(r.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe(r.arborDayProxy.ARBOR_DAY_MY_RID_UPDATE, this.showTree, this);
        this.head.url = l.uiHelps.getServantHead(r.arborDayProxy.data.selectID);
        var t = localcache.getItem(localdb.table_hero, r.arborDayProxy.data.selectID);
        this.lblZy.string = i18n.t("ARBOR_DAY_ZHEN_YING_TXT", {
            name: t.name
        });
        r.arborDayProxy.myRid ? (this.oldTree = r.arborDayProxy.myRid.score) : (this.oldTree = 0);
        this.onItemUpdate();
        this.onDataUpdate();
        this.showTree();
        r.shopProxy.sendList(!1);
    },
    onDataUpdate() {
        for (var t = null,
        e = null,
        o = 0; o < r.arborDayProxy.data.set.length; o++) r.arborDayProxy.data.set[o].pkID == r.arborDayProxy.data.selectID ? (t = r.arborDayProxy.data.set[o]) : (e = r.arborDayProxy.data.set[o]);
        this.lblTree.string = i18n.t("ARBOR_DAY_ZONG_ZHI_ZHI", {
            num: t.score
        });
        t.score > e.score ? (this.lblState.string = i18n.t("ARBOR_DAY_ZHUANG_TAI_1")) : t.score < e.score ? (this.lblState.string = i18n.t("ARBOR_DAY_ZHUANG_TAI_2")) : (this.lblState.string = "");
    },
    onItemUpdate() {
        this.lblKe.string = r.bagProxy.getItemCount(1007) + "";
        this.lblKun.string = r.bagProxy.getItemCount(1008) + "";
        this.lblDui.string = r.bagProxy.getItemCount(1009) + "";
    },
    onClickTab(t, e) {
        n.timeUtil.second > r.arborDayProxy.data.info.eTime ? n.alertUtil.alert18n("ACTHD_OVERDUE") : 0 != r.bagProxy.getItemCount(e) ? r.arborDayProxy.sendPlant(parseInt(e), r.arborDayProxy.data.selectID) : n.alertUtil.alertItemLimit(e);
    },
    showTree() {
        if (r.arborDayProxy.myRid) {
            for (var t = 1; t < r.arborDayProxy.myRid.score + 1; t++) {
                var e = localcache.getItem(localdb.table_treecoor, t);
                e && this.loadTree(e, t);
            }
            this.scheduleOnce(this.onTimer, 0.2);
        }
    },
    onTimer() {
        r.arborDayProxy.myRid ? (this.oldTree = r.arborDayProxy.myRid.score) : (this.oldTree = 0);
        this.isFirst && (this.isFirst = !1);
    },
    loadTree(t, e) {
        var o = (o = a.Config.skin + "/prefabs/tree/tree0" + t.mod),
        i = this; (!this.isFirst && e < this.oldTree) || cc.loader.loadRes(o,
        function(o, n) {
            if (null == o && n) {
                MemoryMgr.saveAssets(o);
                var l = cc.instantiate(n);
                if (l) {
                    var r = l.getChildByName("tree"),
                    a = l.getComponentInChildren(cc.Animation);
                    i.content.addChild(l);
                    l.x = t.x;
                    l.y = t.y;
                    l.scale = t.scale / 1e4;
                    if (e > i.oldTree) {
                        r.active = !1;
                        a.node.active = !0;
                        a.play();
                    } else {
                        r.active = !0;
                        a.node.active = !1;
                    }
                }
            }
        });
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
