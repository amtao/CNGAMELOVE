var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblCount: cc.Label,
        lblTip: cc.Node,
        lblTime: cc.Label,
        lblTotal: cc.Label,
        recordNode: cc.Node,
        recordList: i,
        lblMax: cc.Label,
    },
    ctor() {
        this.flag = !1;
        this.isFirst = !0;
    },
    onLoad() {
        var t = this;
        facade.subscribe(l.luckyBrandProxy.LUCKY_BRAND_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(l.luckyBrandProxy.LUCKY_BRAND_RECORD_UPDATE, this.onRecord, this);
        this.list.selectHandle = function(e) {
            var o = e.data,
            i = e.index,
            r = l.luckyBrandProxy.getCount();
            if (o) 1 == l.luckyBrandProxy.data.reset && t.initList();
            else {
                if (r <= 0) {
                    n.alertUtil.alert18n("LUCKY_BRAND_COUNT_LIMIT");
                    return;
                }
                if (t.flag) return;
                t.flag = !0;
                t.scheduleOnce(t.onTimer, 0.5);
                if (n.timeUtil.second >= l.luckyBrandProxy.data.cfg.info.showTime) {
                    n.alertUtil.alert18n("ACTHD_OVERDUE");
                    return;
                }
                l.luckyBrandProxy.sendGetRewad(i);
            }
        };
        l.luckyBrandProxy.sendOpenActivity();
        this.schedule(this.onTimer2, 30);
    },
    onTimer() {
        this.flag = !1;
    },
    onDataUpdate() {
        for (var t = this,
        e = [], o = 0; o < 9; o++) {
            var i = {
                index: o,
                data: l.luckyBrandProxy.getIndexData(o)
            };
            e.push(i);
        }
        this.list.data = e;
        this.lblCount.string = i18n.t("LUCKY_BRAND_SHENG_YU", {
            num: l.luckyBrandProxy.getCount()
        });
        this.lblTip.active = 1 == l.luckyBrandProxy.data.reset && l.luckyBrandProxy.playList.length > 0;
        r.uiUtils.countDown(l.luckyBrandProxy.data.cfg.info.showTime, this.lblTime,
        function() {
            n.timeUtil.second >= l.luckyBrandProxy.data.cfg.info.showTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
        });
        this.lblTotal.string = i18n.t("LUCKY_TOTAL_RECHARGE", {
            value: l.luckyBrandProxy.data.cfg.need
        });
        if (this.isFirst) {
            this.onTimer2();
            this.isFirst = !1;
        }
        this.lblMax.string = i18n.t("LUCKY_BRAND_MAX_COUNT", {
            num: l.luckyBrandProxy.data.cfg.numMax
        });
    },
    initList() {
        l.luckyBrandProxy.playList = [];
        for (var t = [], e = 0; e < 9; e++) {
            var o = {
                index: e,
                data: null
            };
            t.push(o);
        }
        this.list.data = t;
    },
    onClickClose() {
        l.luckyBrandProxy.playList = [];
        n.utils.closeView(this);
    },
    onClickReset() {
        if (1 == l.luckyBrandProxy.data.reset && l.luckyBrandProxy.playList.length > 0) {
            this.initList();
            this.lblTip.active = !1;
        }
    },
    onRecord() {
        this.recordList.data = l.luckyBrandProxy.records;
    },
    onTimer2() {
        l.luckyBrandProxy.data && l.luckyBrandProxy.sendGetRecord();
    },
});
