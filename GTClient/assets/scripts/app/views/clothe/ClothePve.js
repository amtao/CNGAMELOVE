var i = require("Utils");
var n = require("List");
var l = require("ClotheGateItem");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTime:cc.Label,
        list:n,
        nodeContext:cc.Node,
        item:l,
        lblCount:cc.Label,
        lblScore:cc.Label,
        nodeOvered:cc.Node,
    },

    ctor(){
        this._renders = [];
    },
    onLoad() {
        this.item.node.active = !1; (null == r.clothePveProxy.info || (r.clothePveProxy.base && r.clothePveProxy.base.lastTime < i.timeUtil.getTodaySecond())) && r.clothePveProxy.sendInfo();
        facade.subscribe(r.clothePveProxy.UPDATE_CLOTHE_INFO, this.updateShow, this);
        facade.subscribe(r.clothePveProxy.UPDATE_CLOTHE_BASE, this.updateCount, this);
        facade.subscribe(r.clothePveProxy.UPDATE_CLOTHE_LOGS, this.onUpdateLog, this);
        this.updateCount();
    },
    updateOver() {
        if (null != r.clothePveProxy.info) {
            var t = r.clothePveProxy.info;
            this.nodeOvered.active = t.info.eTime < i.timeUtil.second;
            for (var e = 0; e < this._renders.length; e++) this._renders[e].node.active = !1;
        }
    },
    updateShow() {
        if (null != r.clothePveProxy.info) {
            var t = r.clothePveProxy.info,
            e = this;
            this.updateOver();
            a.uiUtils.countDown(t.info.eTime, this.lblTime,
            function() {
                e.lblTime.string = i18n.t("ACTHD_OVERDUE");
                e.updateOver();
            },
            !0, "USER_REMAIN_TIME", "d");
            this.onUpdateLog();
            for (var o = this.item.node.width,
            i = this.item.node.height,
            n = 0; n < r.clothePveProxy.info.gate.length; n++) {
                var s = this._renders[n];
                if (null == s) {
                    var c = cc.instantiate(this.item.node);
                    s = c.getComponent(l);
                    c.active = !0;
                    this._renders.push(s);
                    this.nodeContext.addChild(c);
                    c.x = n * o;
                    c.y = n % 2 == 1 ? (5 * -i) / 4 : 0;
                }
                s.data = r.clothePveProxy.info.gate[n];
            }
            this.nodeContext.width = r.clothePveProxy.info.gate.length * o;
            r.clothePveProxy.base && (this.lblCount.string = i18n.t("CLOTHE_PVE_REMAIN", {
                d: r.clothePveProxy.info.count - r.clothePveProxy.base.use
            }));
        }
    },
    onUpdateLog() {
        this.list.data = r.clothePveProxy.logs;
    },
    onClickRank() {
        r.clothePveProxy.sendRank();
    },
    onClickDui() {
        i.utils.openPrefabView("clothe/ClotheDuihuan");
    },
    updateCount() {
        this.updateShow();
        null == r.clothePveProxy.base && (this.lblScore.string = r.clothePveProxy.base.score + "");
        null != r.clothePveProxy.info && (this.lblCount.string = i18n.t("CLOTHE_PVE_REMAIN", {
            d: r.clothePveProxy.info.count - r.clothePveProxy.base.use
        }));
    },
    onClickAdd() {
        if (r.clothePveProxy.info && r.clothePveProxy.info.info.eTime < i.timeUtil.second) i.alertUtil.alert18n("ACTHD_OVERDUE");
        else {
            var t = i.utils.getParamInt("clothe_cost");
            i.utils.showConfirmItem(i18n.t("CLOTHE_PVE_ADD_CONFIRM", {
                d: t
            }), 1, r.playerProxy.userData.cash,
            function() {
                r.playerProxy.userData.cash < t ? i.alertUtil.alertItemLimit(1) : r.clothePveProxy.sendAdd();
            },
            "CLOTHE_PVE_ADD_CONFIRM");
        }
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
