var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblBall: cc.Label,
        lblTime: cc.Label,
        oneDengEff: sp.Skeleton,
        tenDenEff: sp.Skeleton,
        tipNode: cc.Node,
        item: cc.Node,
    },
    ctor() {
        this.dengNum = 0;
        this.denging = !1;
    },
    onLoad() {
        facade.subscribe(n.hedengProxy.HEDENG_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(n.hedengProxy.HEDENG_ITEM_RED, this.onItemRed, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        l.uiUtils.scaleRepeat(this.tipNode, 0.9, 1.1);
        n.hedengProxy.sendOpenHeDeng();
        n.shopProxy.sendList(!1);
        this.onItemUpdate();
    },
    onDataUpdate() {
        var t = this,
        e = n.hedengProxy.data;
        if (e) {
            l.uiUtils.countDown(e.info.eTime, this.lblTime,
            function() {
                i.timeUtil.second >= e.info.eTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            });
            this.lblBall.string = n.bagProxy.getItemCount(n.hedengProxy.data.need) + "";
            this.tipNode.active = 0 == n.bagProxy.getItemCount(n.hedengProxy.data.need);
        }
        this.onActData();
    },
    onActData() {
        if (n.hedengProxy.isFirst && (0 == n.hedengProxy.data.cons || null == n.hedengProxy.data.cons)) {
            var t = i.utils.getParamStr("hedanri_story_id");
            n.playerProxy.addStoryId(t);
            i.utils.openPrefabView("StoryView", !1, {
                type: 3
            });
            n.hedengProxy.isFirst = !1;
        }
    },
    onItemRed() {
        this.item.active = !1;
        var t = n.hedengProxy.data;
        if (t) for (var e = 0; e < t.total.length; e++) if (t.cons >= t.total[e].need && !t.total[e].get) {
            this.item.active = !0;
            break;
        }
    },
    onItemUpdate() {
        if (n.hedengProxy.data) {
            var t = n.bagProxy.getItemCount(n.hedengProxy.data.need);
            this.tipNode.active = 0 == t;
            this.lblBall.string = t + "";
        }
    },
    onClickDeng(t, e) {
        if (null != n.hedengProxy.data) if (this.denging) i.alertUtil.alert18n("HEDENG_BUILDING");
        else {
            this.dengNum = parseInt(e);
            if (n.bagProxy.getItemCount(n.hedengProxy.data.need) >= this.dengNum) {
                if (1 == this.dengNum) {
                    this.oneDengEff.node.active = !0;
                    this.oneDengEff.animation = "animation";
                    this.scheduleOnce(this.onTimer, 1);
                } else if (10 == this.dengNum) {
                    this.tenDenEff.node.active = !0;
                    this.tenDenEff.animation = "animation";
                    this.scheduleOnce(this.onTimer2, 1);
                }
                this.denging = !0;
            } else {
                i.alertUtil.alertItemLimit(n.hedengProxy.data.need);
                this.onClickAdd();
            }
        }
    },
    onTimer() {
        1 == this.dengNum && n.bagProxy.getItemCount(n.hedengProxy.data.need) >= 1 && n.hedengProxy.sendHeDengOnce();
        this.denging = !1;
    },
    onTimer2() {
        this.denging = !1;
        n.hedengProxy.sendHeDengTen();
    },
    onClickTab(t, e) {
        switch (e) {
        case "0":
            i.utils.openPrefabView("ActivityShopView", null, n.hedengProxy.dhShop);
            break;
        case "1":
            var o = n.limitActivityProxy.HEDENG_TYPE;
            i.utils.openPrefabView("limitactivity/LimitActivityView", null, {
                type: o
            });
            break;
        case "2":
        case "3":
            n.hedengProxy.sendPaiHang(e);
        }
    },
    onClickAdd() {
        i.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: n.hedengProxy.shop[0],
            activityId: n.hedengProxy.data.info.id
        });
        n.shopProxy.openShopBuy(n.hedengProxy.data.need);
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
