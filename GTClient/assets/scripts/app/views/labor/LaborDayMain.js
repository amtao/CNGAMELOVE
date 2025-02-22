var i = require("UrlLoad");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Config");
var a = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblZy:cc.Label,
        lblTree:cc.Label,
        lblState:cc.Label,
        head:i,
        lblYm:cc.Label,
        lblMm:cc.Label,
        lblYmm:cc.Label,
        lblFsm:cc.Label,
        content:cc.Node,
        check:cc.Toggle,
    },

    ctor(){
        this.oldTree = 0;
        this.isFirst = true;
        this.arr = [1018, 1019, 1020, 1021];     // 春耕
        // this.arr = [1041, 1042, 1043, 1044];        // 万圣节
    },
    onLoad() {
        facade.subscribe(a.laborDayProxy.LABOR_DAY_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(a.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe(a.laborDayProxy.LABOR_DAY_MY_RID_UPDATE, this.onMyRid, this);
        this.head.url = l.uiHelps.getServantHead(a.laborDayProxy.data.selectID);
        var t = localcache.getItem(localdb.table_hero, a.laborDayProxy.data.selectID);
        this.lblZy.string = i18n.t("LABOR_DAY_ZHEN_YING_TXT", {
            name: t.name
        });
        a.laborDayProxy.myRid ? (this.oldTree = a.laborDayProxy.myRid.score) : (this.oldTree = 0);
        this.onItemUpdate();
        this.onDataUpdate();
        this.showTree();
        this.onMyRid();
        this.check.isChecked = a.laborDayProxy.isOneKey;
    },
    onDataUpdate() {
        for (var t = null,
        e = null,
        o = 0; o < a.laborDayProxy.data.set.length; o++) a.laborDayProxy.data.set[o].pkID == a.laborDayProxy.data.selectID ? (t = a.laborDayProxy.data.set[o]) : (e = a.laborDayProxy.data.set[o]);
        t.score > e.score ? (this.lblState.string = i18n.t("LABOR_DAY_ZHUANG_TAI_1")) : t.score < e.score ? (this.lblState.string = i18n.t("LABOR_DAY_ZHUANG_TAI_2")) : (this.lblState.string = "");
    },
    onMyRid() {
        this.lblTree.string = i18n.t("LABOR_DAY_ZONG_ZHI_ZHI", {
            num: a.laborDayProxy.myRid.score
        });
    },
    onItemUpdate() {
        this.lblYm.string = a.bagProxy.getItemCount(1018) + ""; //1041
        this.lblMm.string = a.bagProxy.getItemCount(1019) + ""; //1042
        this.lblYmm.string = a.bagProxy.getItemCount(1020) + ""; //1043
        this.lblFsm.string = a.bagProxy.getItemCount(1021) + ""; //1044
    },
    onClickTab(t, e) {
        console.error(a.laborDayProxy.data);
        if (n.timeUtil.second > a.laborDayProxy.data.info.eTime) n.alertUtil.alert18n("ACTHD_OVERDUE");
        else {
            var o = this.check.isChecked ? 10 : 1;
            if (a.bagProxy.getItemCount(this.arr[e]) < o) {
                n.alertUtil.alertItemLimit(this.arr[e]);
                n.utils.openPrefabView("ActivitySpecialBuy", null, {
                    data: a.laborDayProxy.shop[e],
                    activityId: a.laborDayProxy.data.info.id
                });
            } else a.laborDayProxy.sendPlant(this.arr[e], a.laborDayProxy.data.selectID, o);
        }
    },
    showTree() {
        if (a.laborDayProxy.myRid) {
            for (var t = localcache.getList(localdb.table_chungeng_point), e = 0; e < t.length; e++) t[e].cg_grade > a.laborDayProxy.data.level || this.loadTree(t[e]);
            this.scheduleOnce(this.onTimer, 0.2);
        }
    },
    onTimer() {
        a.laborDayProxy.myRid ? (this.oldTree = a.laborDayProxy.myRid.score) : (this.oldTree = 0);
        this.isFirst && (this.isFirst = !1);
    },
    loadTree(t) {
        var e = (e = r.Config.skin + "/prefabs/zw/zw_" + t.cg_type),
        o = this;
        cc.loader.loadRes(e,
        function(e, i) {
            if (null == e && i) {
                MemoryMgr.saveAssets(e);
                var n = cc.instantiate(i);
                if (n) {
                    o.content.addChild(n);
                    n.x = t.cg_x;
                    n.y = t.cg_y;
                }
            }
        });
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onClickCheck() {
        a.laborDayProxy.isOneKey = this.check.isChecked;
    },
});
