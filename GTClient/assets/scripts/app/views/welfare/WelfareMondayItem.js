var i = require("RenderListItem");
var n = require("List");
var l = require("Initializer");
var r = require("Utils");

cc.Class({
    extends: i,
    
    properties: {
        nBg: cc.Node,
        title: cc.Label,
        lblday: cc.Label,
        list: n,
        lbDouble: cc.Label,
    },

    ctor() {},

    showData() {
        var t = [],
        e = this.data.dayRwd;
        if (e) {
            for (var o = 0; o < e.length; o++) {
                var i = e[o];
                if (null != i.itemid) {
                    i.id = i.itemid;
                    t.push(i);
                }
            }
            this.list.data = t;
            var n = r.timeUtil.getCurData(),
            a = this.data.dayid;
            if (1 == l.welfareProxy.zhouqian.isrwd && n == a) {
                this.lblday.string = i18n.t("WELFARE_CAN_GET");
                this.nBg.active = true;
            } else if (2 == l.welfareProxy.zhouqian.isrwd && n == a) {
                this.lblday.string = i18n.t("WELFARE_CANT_GET");
                this.nBg.active = false;
            } else {
                var s = a - n;
                s < 0 && (s += 7);
                this.lblday.string = i18n.t("WELFARE_TIME_WEAK", {
                    day: s
                });
                this.nBg.active = false;
            }
            this.title.string = i18n.t("WELFARE_RWD_WEAK", {
                day: this.getWeekly(a)
            });
            this.lbDouble.node.parent.active = this.data.vip > 0;
            this.lbDouble.string = i18n.t("SEVEN_DAYS_DESC102", { num: this.data.vip });
        }
    },

    onClickItem() {
        var t = this.data,
        e = r.timeUtil.getCurData(),
        o = this.data.dayid;
        if (t && 1 == l.welfareProxy.zhouqian.isrwd && e == o) l.welfareProxy.senMonday();
        else if (2 == l.welfareProxy.zhouqian.isrwd && e == o) {
            var i = i18n.t("WELFARE_WEEK_LIMIT", {
                day: this.getWeekly(o)
            });
            r.alertUtil.alert18n(i);
        } else {
            i = i18n.t("WELFARE_LIMIT_WEAK", {
                day: this.getWeekly(o)
            });
            r.alertUtil.alert18n(i);
        }
    },

    getWeekly(t) {
        return i18n.t("WELFARE_WEEK").split("|")[t - 1];
    },
});
