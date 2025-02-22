var i = require("TrunTableItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("List");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTime: cc.Label,
        items: [i],
        lblNun: cc.Label,
        records: r,
        scroll: cc.ScrollView,
    },
    ctor() {
        this.curIndex = 0;
        this.roundIndex = 0;
        this.isFirst = !0;
        this.oldScore = 0;
    },
    onLoad() {
        facade.subscribe(n.luckyTableProxy.LUCKY_TABLE_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe(n.luckyTableProxy.LUCKY_TABLE_RECORDS, this.onRecords, this);
        n.luckyTableProxy.sendOpenActivity();
    },
    onDataUpdate() {
        if (n.luckyTableProxy.data.prize && n.luckyTableProxy.data.prize.length > 0) {
            this.curIndex = n.luckyTableProxy.data.prize[0].dc;
            this.showEff(0);
        } else if (n.luckyTableProxy.data) {
            this.onItemUpdate();
            for (var t = 0; t < this.items.length; t++) if (t < n.luckyTableProxy.data.list.length) {
                this.items[t].data = n.luckyTableProxy.data.list[t];
                this.items[t].select = 0 == t;
            }
            var e = this;
            a.uiUtils.countDown(n.luckyTableProxy.data.info.eTime, this.lblTime,
            function() {
                e.lblTime.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
        }
        if (this.isFirst) this.isFirst = !1;
        else if (!l.utils.isOpenView("ActivityScoreChange")) {
            var o = n.luckyTableProxy.data.cons - this.oldScore;
            0 != o && l.alertUtil.alert(i18n.t("LUCKY_TABLE_YUAN_ADD", {
                num1: o,
                num2: o
            }));
        }
        this.oldScore = n.luckyTableProxy.data.cons;
    },
    showEff(t) {
        this.unscheduleAllCallbacks();
        this.schedule(this.showSelect, t);
    },
    showSelect() {
        for (var t = this.roundIndex % 10,
        e = 0; e < this.items.length; e++) this.items[e].select = t == e;
        this.roundIndex++;
        if (this.roundIndex >= 10 && this.roundIndex < 20) this.showEff(0.03);
        else if (this.roundIndex >= 20 && this.roundIndex < 30) this.showEff(0.03 + (this.roundIndex - 20) / 200);
        else if (this.roundIndex >= 30 + this.curIndex && this.roundIndex < 100) {
            this.roundIndex = 0;
            this.curIndex = 0;
            this.unscheduleAllCallbacks();
            n.timeProxy.floatReward();
        }
    },
    onClickRoll(t, e) {
        if (l.timeUtil.second > n.luckyTableProxy.data.info.eTime) l.alertUtil.alert18n("ACTHD_OVERDUE");
        else if (0 == this.curIndex) {
            var o = parseInt(e);
            if (n.bagProxy.getItemCount(n.luckyTableProxy.data.need) < o) {
                l.alertUtil.alertItemLimit(n.luckyTableProxy.data.need);
                l.utils.openPrefabView("ActivitySpecialBuy", null, {
                    data: n.luckyTableProxy.shop[0],
                    activityId: n.luckyTableProxy.data.info.id
                });
            } else n.luckyTableProxy.sendRoll(o);
        } else l.alertUtil.alert18n("TRUN_TABLE_IS_ROLLING");
    },
    onClickRank(t, e) {
        l.utils.openPrefabView("luckytable/LuckyTableRwd", null, {
            type: parseInt(e)
        });
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onItemUpdate() {
        if (n.luckyTableProxy.data) {
            var t = n.bagProxy.getItemCount(n.luckyTableProxy.data.need);
            this.lblNun.string = t + "";
        }
    },
    onClickAdd() {
        l.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: n.luckyTableProxy.shop[0],
            activityId: n.luckyTableProxy.data.info.id
        });
    },
    onRecords() {
        this.records.data = n.luckyTableProxy.records;
        this.scroll.scrollToBottom();
    },
    onClickScoreChange() {
        l.utils.openPrefabView("ActivityScoreChange", null, {
            activityId: n.luckyTableProxy.data.info.id,
            list: n.luckyTableProxy.scoreChange,
            score: n.luckyTableProxy.data.cons
        });
    },
    onClickActivity() {
        l.utils.openPrefabView("limitactivity/LimitActivityView", null, {
            type: n.limitActivityProxy.LUCKY_TABLE_TYPE
        });
    },
});
