var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        rwdList: i,
        taskList: i,
        lblTime: cc.Label,
        lblChange: cc.Label,
        lblNum: cc.Label,
        welcome: cc.Node,
    },
    ctor() {
        this.oldNum = 0;
    },
    onLoad() {
        facade.subscribe(l.lionProxy.LION_DATA_UPDATE, this.onLionData, this);
        facade.subscribe(l.lionProxy.LION_GOLD_LOCK, this.onLionData, this);
        this.welcome.active = l.lionProxy.isFirst;
        l.lionProxy.isFirst && (l.lionProxy.isFirst = !1);
        l.purchaseProxy.sendOpenPrince();
        l.lionProxy.sendOpenActivity();
    },
    onLionData() {
        if (null != l.lionProxy.cfg) {
            1 == l.lionProxy.cfg.isGold && (l.lionProxy.isLockGold = !0);
            var t = this;
            r.uiUtils.countDown(l.lionProxy.cfg.info.eTime, this.lblTime,
            function() {
                t.lblTime.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
            l.lionProxy.cfg.rwd.sort(function(t, e) {
                if (l.lionProxy.isLockGold) {
                    var o = 1 == t.sGet && 1 == t.gGt ? 1 : 0,
                    i = 1 == e.sGet && 1 == e.gGt ? 1 : 0;
                    if (o != i) return o - i;
                } else {
                    var n = 1 == t.sGet ? 1 : 0,
                    r = 2 == e.sGet ? 1 : 0;
                    if (n != r) return n - r;
                }
                return t.id - e.id;
            });
            this.rwdList.data = l.lionProxy.cfg.rwd;
            l.lionProxy.cfg.task.sort(function(t, e) {
                var o = localcache.getItem(localdb.table_lion_task, t.id),
                i = localcache.getItem(localdb.table_lion_task, e.id),
                n = t.num >= o.num ? 0 : 1,
                l = e.num >= i.num ? 0 : 1;
                return t.get != e.get ? t.get - e.get: n - l;
            });
            this.taskList.data = l.lionProxy.cfg.task;
            this.lblNum.string = l.lionProxy.cfg.cons + "";
            if (0 != this.oldNum) {
                var e = l.lionProxy.cfg.cons - this.oldNum;
                e > 0 && n.alertUtil.alert(i18n.t("DRAGON_BOAT_XIU_QIU_ADD", {
                    num: e
                }));
            }
            this.oldNum = l.lionProxy.cfg.cons;
        }
    },
    onClickChange() {
        n.utils.showConfirmItem(i18n.t("LION_GENG_HUAN_TASK"), 1, l.playerProxy.userData.cash,
        function(t) {
            l.playerProxy.userData.cash >= 50 && l.lionProxy.sendChangeTask();
        });
    },
    onClickClose() {
        1 != l.lionProxy.cfg.isGold && (l.lionProxy.isLockGold = !1);
        n.utils.closeView(this);
    },
    onClickStart() {
        this.welcome.active = !1;
    },
});
