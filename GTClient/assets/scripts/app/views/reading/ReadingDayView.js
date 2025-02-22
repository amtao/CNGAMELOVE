var i = require("Utils");
var n = require("List");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("TimeProxy");
var s = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        lblNum: cc.Label,
        lblTime: cc.Label,
        records: n,
        rwdNode: cc.Node,
        rwdList: n,
        scroll: cc.ScrollView,
        itemScroll: cc.ScrollView,
        urlArr: [s],
        eff_1: sp.Skeleton,
        box_close: cc.Node,
        box_open: cc.Node,
    },
    ctor() {
        this.flag = !1;
        this._num = 0;
    },
    onLoad() {
        facade.subscribe(l.readingDayProxy.READING_DAY_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(l.readingDayProxy.READING_DAY_RECORDS, this.onReadingRecords, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        l.readingDayProxy.sendOpenReadingDay();
    },
    onDataUpdate() {
        var t = l.readingDayProxy.data;
        if (t) {
            this.onItemUpdate();
            var e = this;
            r.uiUtils.countDown(t.info.eTime, this.lblTime,
            function() {
                e.lblTime.string = i18n.t("ACTHD_OVERDUE");
            });
        }
    },
    onClickRwd(t, e) {
        if (i.timeUtil.second > l.readingDayProxy.data.info.eTime) i.alertUtil.alert18n("ACTHD_OVERDUE");
        else if (this.flag) i.alertUtil.alert18n("READING_DAY_ROLLING");
        else {
            var o = parseInt(e);
            if (l.bagProxy.getItemCount(l.readingDayProxy.data.need) >= o) {
                this.flag = !0;
                this.eff_1.node.active = !0;
                this.eff_1.animation = "animation";
                this.scheduleOnce(this.onShowEff_1, 1);
                l.timeProxy.floatReward();
                this._num = o;
            } else {
                i.alertUtil.alertItemLimit(l.readingDayProxy.data.need);
                i.utils.openPrefabView("ActivitySpecialBuy", null, {
                    data: l.readingDayProxy.shop[0],
                    activityId: l.readingDayProxy.data.info.id
                });
            }
        }
    },
    onShowEff_1() {
        this.box_close.active = !1;
        this.box_open.active = !0;
        this.scheduleOnce(this.onTimer2, 0.5);
    },
    onTimer2() {
        l.readingDayProxy.sendReard(this._num);
        this.scheduleOnce(this.onTimer3, 1);
    },
    onTimer3() {
        this.box_close.active = !0;
        this.box_open.active = !1;
        this.flag = !1;
    },
    showReward() {
        l.timeProxy.floatReward();
        this.flag = !1;
    },
    onReadingRecords() {
        this.records.data = l.readingDayProxy.records;
        this.scroll.scrollToBottom();
    },
    onClickClose() {
        i.utils.closeView(this);
        l.jibanProxy.sendWishInfo();
    },
    onClickCloseWin() {
        this.rwdNode.active = !1;
        this.itemScroll.scrollToTop();
    },
    onItemUpdate() {
        if (l.readingDayProxy.data) {
            var t = l.bagProxy.getItemCount(l.readingDayProxy.data.need);
            this.lblNum.string = t + "";
        }
    },
    onClicTab(t, e) {
        if (1 != this.flag) if ("1" == e) a.funUtils.openView(a.funUtils.duihuanShop.id);
        else if ("2" == e) i.utils.openPrefabView("limitactivity/LimitActivityView", null, {
            type: l.limitActivityProxy.READING_TYPE
        });
        else if ("3" == e) {
            if (l.readingDayProxy.data) {
                this.rwdNode.active = !0;
                this.rwdList.data = l.readingDayProxy.data.list;
            }
        } else "4" == e && i.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: l.readingDayProxy.shop[0],
            activityId: l.readingDayProxy.data.info.id
        });
        else i.alertUtil.alert18n("READING_DAY_ROLLING");
    },
    onClickItem() {
        var t = localcache.getItem(localdb.table_item, l.readingDayProxy.data.need);
        i.utils.openPrefabView("ItemInfo", !1, t);
    },
});
