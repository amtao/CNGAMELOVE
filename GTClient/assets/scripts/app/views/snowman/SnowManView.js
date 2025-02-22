var i = require("Utils");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        lblBall:cc.Label,
        lblJindu:cc.Label,
        lblLevel:cc.Label,
        lblTime:cc.Label,
        proBar:cc.ProgressBar,
        snowUrl:n,
        snowEff:sp.Skeleton,
        effArr:[sp.Skeleton],
        giftEff:sp.Skeleton,
        tipNode:cc.Node,
        record:a,
        scroll:cc.ScrollView,
        springEff_1:sp.Skeleton,
        springEff_10:sp.Skeleton,
    },

    ctor(){
        this.snowNum = 0;
        this.snowing = false;
        this.curIndex = 0;
    },
    onLoad() {
        facade.subscribe(l.snowmanProxy.SNOWMAN_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe(l.snowmanProxy.SNOWMAN_RECORDS_UPDATE, this.onRecord, this);
        // r.uiUtils.scaleRepeat(this.tipNode, 0.9, 1.1);
        l.snowmanProxy.sendOpenSnowMan();
        l.shopProxy.sendList(!1);
        this.onItemUpdate();

    },
    onDataUpdate() {
        var t = this,
        e = l.snowmanProxy.data;
        if (e) {
            1 == l.snowmanProxy.data.info.hdtype ? (this.snowUrl.url = r.uiHelps.getSnowmanIcon(e.bossinfo.skin)) : 2 == l.snowmanProxy.data.info.hdtype && (this.snowUrl.url = r.uiHelps.getSpringBz(1));
            r.uiUtils.countDown(e.info.eTime, this.lblTime,
            function() {
                i.timeUtil.second >= e.info.eTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            });
            this.lblJindu.string = e.bossinfo.lv >= 10 ? i18n.t("SNOWMAN_ZUI_GAO_JI") : i18n.t("COMMON_NUM", {
                f: e.bossinfo.val,
                s: e.bossinfo.hp
            });
            this.proBar.progress = e.bossinfo.lv >= 10 ? 1 : e.bossinfo.val / e.bossinfo.hp;
            this.lblLevel.string = i18n.t("COMMON_LEVEL_TXT", {
                lv: e.bossinfo.lv
            });
            this.lblBall.string = l.bagProxy.getItemCount(l.snowmanProxy.data.need) + "";
            // this.tipNode.active = 0 == l.bagProxy.getItemCount(l.snowmanProxy.data.need);
            l.snowmanProxy.hasRed() ? (this.giftEff.animation = "tx") : (this.giftEff.animation = "zc");
        }
    },
    onItemUpdate() {
        if (l.snowmanProxy.data) {
            var t = l.bagProxy.getItemCount(l.snowmanProxy.data.need);
            // this.tipNode.active = 0 == t;
            this.lblBall.string = t + "";
        }
    },
    onClickSnow(t, e) {
        if (null != l.snowmanProxy.data) if (this.snowing) i.alertUtil.alert18n("SNOWMAN_BUILDING");
        else {
            this.snowNum = parseInt(e);
            if (l.bagProxy.getItemCount(l.snowmanProxy.data.need) >= this.snowNum) {
                if (1 == this.snowNum) {
                    if (1 == l.snowmanProxy.data.info.hdtype) {
                        this.snowEff.node.active = !0;
                        this.snowEff.animation = "animation";
                    } else if (2 == l.snowmanProxy.data.info.hdtype) {
                        this.springEff_1.node.active = !0;
                        this.springEff_1.animation = "animation";
                    }
                    this.scheduleOnce(this.onTimer, 1);
                } else if (10 == this.snowNum) if (1 == l.snowmanProxy.data.info.hdtype) this.schedule(this.onShowEff, 0.25);
                else if (2 == l.snowmanProxy.data.info.hdtype) {
                    this.springEff_10.node.active = !0;
                    this.springEff_10.animation = "animation";
                    this.scheduleOnce(this.onTimer2, 1);
                }
                this.snowing = !0;
            } else i.alertUtil.alertItemLimit(l.snowmanProxy.data.need);

        }
    },
    onTimer() {
        1 == this.snowNum && l.bagProxy.getItemCount(l.snowmanProxy.data.need) >= 1 && l.snowmanProxy.sendSnowManOnce();
        this.snowing = !1;
    },
    onShowEff() {
        for (var t = 0; t < this.effArr.length; t++) if (this.curIndex == t) {
            this.effArr[t].node.active = !0;
            this.effArr[t].animation = "animation";
        }
        this.curIndex++;
        if (this.curIndex >= this.effArr.length - 1) {
            l.snowmanProxy.sendSnowManTen();
            this.curIndex = 0;
            this.snowing = !1;
            this.unscheduleAllCallbacks();
        }
    },
    onTimer2() {
        this.snowing = !1;
        l.snowmanProxy.sendSnowManTen();
    },
    onClickGift() {
        i.utils.openPrefabView("snowman/SnowManReward");
    },
    onClickTab(t, e) {
        if ("0" == e) i.utils.openPrefabView("wishingwell/WishingActivityShopView", null, l.snowmanProxy.dhShop, null, true);
        else if ("1" == e) {
            var o = 1 == l.snowmanProxy.data.info.hdtype ? l.limitActivityProxy.SNOWMAN_TYPE: l.limitActivityProxy.SPRING_TYPE;
            i.utils.openPrefabView("limitactivity/LimitActivityView", null, {
                type: o
            });
        }
    },
    onClickAdd() {
        // l.shopProxy.openShopBuy(l.snowmanProxy.data.need);
        i.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: l.snowmanProxy.shop[0],
            activityId: l.snowmanProxy.data.info.id
        });
    },
    onClickClose() {
        i.utils.closeView(this);
    },
    onRecord() {
        if (this.record) {
            this.record.data = l.snowmanProxy.records;
            this.scroll.scrollToBottom();
        }
    },

    onClickRankRwd () {
        i.utils.openPrefabView("snowman/SnowRankRwd");
    }
});
