var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblBall: cc.Label,
        lblTime: cc.Label,
        tipNode: cc.Node,
        item: cc.Node,
        oneDengEff: sp.Skeleton,
    },
    ctor() {
        this.dengNum = 0;
        this.bClick = !1;
        this.denging = !1;
    },
    onLoad() {
        facade.subscribe(n.qixiProxy.QIXI_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(n.qixiProxy.QIXI_ITEM_RED, this.onItemRed, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        l.uiUtils.scaleRepeat(this.tipNode, 0.9, 1.1);
        n.qixiProxy.sendOpenQIXI();
        n.shopProxy.sendList(!1);
        this.onItemUpdate();
    },
    onDataUpdate() {
        var t = this,
        e = n.qixiProxy.data;
        if (e) {
            l.uiUtils.countDown(e.info.eTime, this.lblTime,
            function() {
                i.timeUtil.second >= e.info.eTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            });
            this.lblBall.string = n.bagProxy.getItemCount(n.qixiProxy.data.need) + "";
            this.tipNode.active = 0 == n.bagProxy.getItemCount(n.qixiProxy.data.need);
        }
        if (this.bClick) {
            this.bClick = !1;
            i.utils.openPrefabView("qixi/QiXiChouQian");
        }
    },
    onActData() {},
    onItemRed() {},
    onItemUpdate() {
        if (n.qixiProxy.data) {
            var t = n.bagProxy.getItemCount(n.qixiProxy.data.need);
            this.tipNode.active = 0 == t;
            this.lblBall.string = t + "";
        }
    },
    onClickQian(t, e) {
        if (this.denging) i.alertUtil.alert18n("QIXI_BUILDING");
        else if (null != n.qixiProxy.data) {
            this.dengNum = parseInt(e);
            if (n.bagProxy.getItemCount(n.qixiProxy.data.need) >= this.dengNum) {
                this.denging = !0;
                this.bClick = !0;
                this.oneDengEff.node.active = !0;
                this.oneDengEff.animation = "animation";
                this.scheduleOnce(this.onTimer, 1);
            } else {
                i.alertUtil.alertItemLimit(n.qixiProxy.data.need);
                this.onClickAdd();
            }
        }
    },
    onTimer() {
        this.denging = !1;
        n.qixiProxy.sendQIXI(this.dengNum);
    },
    onClickTab(t, e) {
        switch (e) {
        case "0":
            i.utils.openPrefabView("ActivityShopView", null, n.qixiProxy.dhShop);
            break;
        case "1":
            var o = n.limitActivityProxy.QIXI_TYPE;
            i.utils.openPrefabView("limitactivity/LimitActivityView", null, {
                type: o
            });
            break;
        case "2":
        case "3":
            n.qixiProxy.sendPaiHang(e);
        }
    },
    onClickAdd() {
        i.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: n.qixiProxy.shop[0],
            activityId: n.qixiProxy.data.info.id
        });
        n.shopProxy.openShopBuy(n.qixiProxy.data.need);
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
