var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("formula");
var a = require("TreasureProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        list:i,
        lblGate:cc.Label,
        lblChnage:cc.Label,
        lblCost:cc.Label,
        btnWin:cc.Node,
        nodeOver:cc.Node,
        lblNext:cc.Label,
        lblResetCount:cc.Label,
        lblResetCost:cc.Label,
        nodeAddBtn:cc.Node,
        lblRank:cc.Label,
        lblCount:cc.Label,
        nodeBuy:cc.Node,
    },

    ctor(){
        this.datas = null;
        this.lastClick = -1;
        this.lastClick1 = -1;
        this.listIds = null;
        this.maxGate = 0;
        this.itemW = 160;
        this.itemH = 240;
    },
    onLoad() {
        l.treasureProxy.tidy.lastTime < n.timeUtil.getTodaySecond() && l.treasureProxy.sendInfo();
        this.maxGate = this.getCurMax();
        facade.subscribe(l.treasureProxy.UPDATE_TREASURE_TIDY, this.updateShow, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickBack, this);
        this.updateShow();
    },
    updateShow() {
        this.lastClick1 = this.lastClick = -1;
        var t = l.treasureProxy.tidy,
        e = localcache.getItem(localdb.table_treasureTidy, t.curgate);
        this.list.repeatX = e ? e.column: 4;
        var o = e ? 660 / e.column: this.itemW,
        i = (o * this.itemH) / this.itemW;
        i * (e ? e.row: 0) > 960 && (o = ((i = 960 / e.row) / this.itemH) * this.itemW);
        i = Math.floor(i);
        o = Math.floor(o);
        this.list.setWidthHeight(o, i);
        this.list.data = this.getDatas();
        this.list.node.x = -this.list.node.width / 2;
        this.list.node.y = this.list.node.height / 2;
        this.lblChnage.string = e ? i18n.t("TREASURE_REMAIN_SKIP", {
            v: e.chance + t.buyCount - t.curlost
        }) : "";
        this.lblGate.string = i18n.t("TREASURE_GATE", {
            f: t.curgate > this.maxGate ? this.maxGate: t.curgate,
            s: this.maxGate
        });
        this.lblCost.string = e ? e.pass + "": "";
        this.lblRank.string = (t.dayOver ? t.dayOver: 0) + "";
        this.btnWin.active = null != e;
        this.nodeAddBtn.active = !1;
        if (e && e.open <= l.treasureProxy.score && e.chance + t.buyCount > t.curlost) {
            this.nodeOver.active = !1;
            this.nodeAddBtn.active = e.chance + t.buyCount < t.curlost + 3;
        } else {
            this.nodeBuy.active = e && e.chance + t.buyCount <= t.curlost;
            this.nodeOver.active = !0;
            var a = n.utils.getParamInt("tidy_change_max");
            this.lblCount.string = i18n.t("TREASURE_BUY_TIP", {
                f: a - t.buyCount,
                s: a
            });
            this.lblResetCost.string = r.formula.linklink_times(t.count) + "";
            this.lblResetCount.string = i18n.t("TREASURE_RESET", {
                v: t.count
            });
            this.lblNext.string = null == e ? i18n.t("TREASURE_OVER_TIP") : e.chance <= t.curlost ? i18n.t("TREASURE_LOST_TIP") : i18n.t("TREASURE_TIDY_UNLOCK_TIP", {
                v: e.open
            });
        }
    },
    getIds() {
        var t = l.treasureProxy.tidy.pics.length / 2,
        e = localcache.getList(localdb.table_treasure),
        o = e.length,
        i = [],
        n = Math.floor(o / t);
        i.push(0);
        for (var r = 0; r < t; r++) {
            var a = r * n + Math.floor(Math.random() * n);
            a = a >= o ? o - 1 : a;
            i.push(e[a].id);
        }
        return i;
    },
    getDatas() {
        var t = l.treasureProxy.tidy; (null != this.listIds && this.listIds.length == t.pics.length / 2 + 1) || (this.listIds = this.getIds());
        this.datas = [];
        for (var e = 0; e < t.pics.length; e++) {
            var o = new a.TreasureTidyItem();
            o.id = this.listIds[parseInt(t.pics[e] + "")];
            o.isShow = e == this.lastClick || e == this.lastClick1;
            o.index = e;
            this.datas.push(o);
        }
        return this.datas;
    },
    getCurMax() {
        for (var t = localcache.getList(localdb.table_treasureTidy), e = 0; e < t.length; e++) if (t[e].open > l.treasureProxy.score) return t[e].barrier - 1;
        return t[t.length - 1].barrier;
    },
    onClickPass() {
        var t = l.treasureProxy.tidy,
        e = localcache.getItem(localdb.table_treasureTidy, t.curgate);
        null == e || ( - 1 != this.lastClick && -1 != this.lastClick1) || n.utils.showConfirmItem(i18n.t("TREASURE_OVER_CONFIRM", {
            v: e.pass
        }), 1, l.playerProxy.userData.cash,
        function() {
            l.playerProxy.userData.cash < e.pass ? n.alertUtil.alertItemLimit(1) : l.treasureProxy.sendWin();
        },
        "TREASURE_OVER_CONFIRM");
    },
    onClickReset() {
        var t = l.treasureProxy.tidy,
        e = r.formula.linklink_times(t.count);
        n.utils.showConfirmItem(i18n.t("TREASURE_RESET_CONFIRM", {
            v: e
        }), 1, l.playerProxy.userData.cash,
        function() {
            l.playerProxy.userData.cash < e ? n.alertUtil.alertItemLimit(1) : l.treasureProxy.sendReset();
        },
        "TREASURE_RESET_CONFIRM");
    },
    onClickClost() {
        n.utils.closeView(this);
        n.utils.closeNameView("treasure/TreasureView");
    },
    onClickBack() {
        n.utils.closeView(this);
    },
    onClickCard(t, e) {
        var o = e ? e.data: null;
        if (o) {
            if ( - 1 != this.lastClick1 || 0 == o.id) return;
            if ( - 1 == this.lastClick) {
                this.lastClick = o.index;
                this.datas[o.index].isShow = !0;
                this.list.updateItemShow();
            } else {
                if (this.lastClick == o.index) return;
                this.lastClick1 = o.index;
                this.datas[o.index].isShow = !0;
                this.list.updateItemShow();
                var i = this.lastClick,
                n = this.lastClick1;
                this.datas[this.lastClick].id == this.datas[this.lastClick1].id ? facade.send("TIME_RUN_FUN", {
                    fun: function() {
                        l.treasureProxy.sendTrun(i, n);
                    },
                    time: 0.3
                }) : facade.send("TIME_RUN_FUN", {
                    fun: function() {
                        l.treasureProxy.sendTrun(i, n);
                    },
                    time: 1
                });
            }
        }
    },
    onClickRank() {
        l.treasureProxy.sendTidyRank();
    },
    onClickBuy() {
        if (l.treasureProxy.tidy.buyCount >= n.utils.getParamInt("tidy_change_max")) n.alertUtil.alert18n("TREASURE_BUY_COUNT_LIMIT");
        else {
            var t = r.formula.tidy_chance_price(l.treasureProxy.tidy.buyCount);
            n.utils.showConfirmItem(i18n.t("TREASURE_BUY_COMFIRM", {
                d: t
            }), 1, l.playerProxy.userData.cash,
            function() {
                t > l.playerProxy.userData.cash ? n.alertUtil.alertItemLimit(1) : l.treasureProxy.sendBuyCount();
            },
            "TREASURE_BUY_COMFIRM");
        }
    },
});
