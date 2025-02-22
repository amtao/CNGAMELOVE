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
        eff_2: sp.Skeleton,
    },
    ctor() {
        this.flag = !1;
    },
    onLoad() {
        facade.subscribe(l.girlsDayProxy.GIRLS_DAY_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(l.girlsDayProxy.GIRLS_DAY_RECORDS, this.onRecords, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe("GIRLS_DAY_SHOW_RWD_END", this.onShowRwdEnd, this);
        l.girlsDayProxy.sendOpenGrilsDay();
        l.shopProxy.sendList(!1);
    },
    onDataUpdate() {
        var t = l.girlsDayProxy.data;
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
        if (i.timeUtil.second > l.girlsDayProxy.data.info.eTime) i.alertUtil.alert18n("ACTHD_OVERDUE");
        else if (this.flag) i.alertUtil.alert18n("GIRLS_DAY_ROLLING");
        else {
            var o = parseInt(e);
            if (l.bagProxy.getItemCount(l.girlsDayProxy.data.need) >= o) {
                this.flag = !0;
                l.girlsDayProxy.sendReard(o);
                this.eff_1.node.active = !0;
                this.eff_1.animation = "animation";
                this.scheduleOnce(this.onShowEff_1, 1);
                for (var n = 0; n < this.urlArr.length; n++) null != this.urlArr[n] && (this.urlArr[n].node.active = !1);
            } else i.alertUtil.alertItemLimit(l.girlsDayProxy.data.need);
        }
    },
    onShowEff_1() {
        if (l.girlsDayProxy.getShowItem()) {
            this.eff_2.node.active = !0;
            this.eff_2.animation = "animation";
            this.scheduleOnce(this.onShowItem, 1);
        } else {
            l.timeProxy.floatReward();
            this.onTrunRwd();
        }
    },
    onTrunRwd() {
        if (l.girlsDayProxy.trunRwd) for (var t = 0; t < l.girlsDayProxy.trunRwd.length; t++) {
            var e = l.girlsDayProxy.trunRwd[t].clothe,
            o = l.girlsDayProxy.trunRwd[t].item,
            n = localcache.getItem(localdb.table_userClothe, e.id),
            r = localcache.getItem(localdb.table_item, o.id);
            i.alertUtil.alert(i18n.t("GIRLS_TRUN_RWD_TXT", {
                clothe: n.name,
                name: r.name,
                num: o.count
            }));
        }
        l.girlsDayProxy.trunRwd = null;
    },
    onShowItem() {
        var t = l.girlsDayProxy.getShowItem();
        if (t) {
            this.eff_2.node.active = !1;
            for (var e = 0; e < this.urlArr.length; e++) if (null != this.urlArr[e]) {
                this.urlArr[e].node.active = e == t.part;
                this.urlArr[e].url = e == t.part ? l.girlsDayProxy.getItemUrl(t) : "";
                if (6 == e && t.location) {
                    this.urlArr[e].node.x = t.location.length > 0 ? t.location[0] : 0;
                    this.urlArr[e].node.y = t.location.length > 1 ? t.location[1] : 0;
                }
            }
            this.scheduleOnce(this.showReward, 1);
        }
    },
    showReward() {
        l.timeProxy.floatReward();
        this.onTrunRwd();
    },
    onRecords() {
        this.records.data = l.girlsDayProxy.records;
        this.scroll.scrollToBottom();
    },
    onClickClose() {
        i.utils.closeView(this);
    },
    onClickCloseWin() {
        this.rwdNode.active = !1;
        this.itemScroll.scrollToTop();
    },
    onItemUpdate() {
        if (l.girlsDayProxy.data) {
            var t = l.bagProxy.getItemCount(l.girlsDayProxy.data.need);
            this.lblNum.string = t + "";
        }
    },
    onClicTab(t, e) {
        if (1 != this.flag) if ("1" == e) i.utils.openPrefabView("wishingwell/WishingActivityShopView", null, l.girlsDayProxy.dhShop, null, true);
        else if ("2" == e) i.utils.openPrefabView("limitactivity/LimitActivityView", null, {
            type: l.limitActivityProxy.GIRLS_TYPE
        });
        else if ("3" == e) {
            if (l.girlsDayProxy.data) {
                this.rwdNode.active = !0;
                this.rwdList.data = l.girlsDayProxy.data.list;
            }
        } else "4" == e && l.shopProxy.openShopBuy(l.girlsDayProxy.data.need);
        else i.alertUtil.alert18n("GIRLS_DAY_ROLLING");
    },
    onClickItem() {
        var t = localcache.getItem(localdb.table_item, l.girlsDayProxy.data.need);
        i.utils.openPrefabView("ItemInfo", !1, t);
    },
    onShowRwdEnd() {
        this.scheduleOnce(this.onTimerEnd, 3);
    },
    onTimerEnd() {
        l.timeProxy.floatReward();
        this.flag = !1;
    },
    onClickAdd() {
        // l.shopProxy.openShopBuy(l.snowmanProxy.data.need);
        i.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: l.girlsDayProxy.shop[0],
            activityId: l.girlsDayProxy.data.info.id
        });
    },
});
