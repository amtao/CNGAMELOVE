var i = require("Utils");
var n = require("List");
var l = require("Initializer");
var r = require("SelectMax");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblGate:cc.Label,
        lblScore:cc.Label,
        lblCount:cc.Label,
        list:n,
        lblDes:cc.RichText,
        lblTime:cc.Label,
        lblHighScore:cc.Label,
        select:r,
        nodeClear:cc.Node,
        nodeFight:cc.Node,
        nodeUnopen:cc.Node,
    },
    onLoad() {
        this._curData = this.node.openParam;
        facade.subscribe(l.clothePveProxy.UPDATE_CLOTHE_BASE, this.updateCount, this);
        this.updateShow();
    },
    updateShow() {
        var t = [],
        e = 0;
        this.nodeClear.active = l.clothePveProxy.base.gate >= this._curData.id;
        this.nodeClear.active && (this.select.max = l.clothePveProxy.info.count - l.clothePveProxy.base.use);
        this.lblTime.node.active = Math.ceil((i.timeUtil.second - l.clothePveProxy.info.info.sTime) / 86400) < this._curData.day;
        this.nodeUnopen.active = l.clothePveProxy.base.gate + 1 < this._curData.id && !this.lblTime.node.active;
        this.nodeFight.active = l.clothePveProxy.base.gate + 1 == this._curData.id && !this.lblTime.node.active;
        if (this.lblTime.node.active) {
            var o = this,
            n = l.clothePveProxy.info.info.sTime + 86400 * (this._curData.day - 1);
            a.uiUtils.countDown(n, this.lblTime,
            function() {
                o.updateShow();
            },
            !0, "CLOTHE_PVE_OPEN_REMAIN", "d");
        }
        for (var r = i.utils.getParamInt("clothe_score_item"), s = 0; s < this._curData.rwds.length; s++) this._curData.rwds[s].id == r ? (e = this._curData.rwds[s].count) : t.push(this._curData.rwds[s]);
        for (s = 0; s < this._curData.prop_rwds.length; s++) t.push(this._curData.prop_rwds[s]);
        var c = localcache.getItem(localdb.table_clothepve, this._curData.gateid);
        this.lblDes.string = c.intro;
        var _ = i18n.t("CLOTHE_PVE_GATE", {
            d: i.utils.getHanzi(this._curData.id)
        });
        this.lblGate.string = i18n.t("COMMON_ADD_2", {
            n: _,
            c: c.name
        });
        this.lblScore.string = i18n.t("CLOTHE_PVE_SCORE_ADD", {
            d: e
        });
        this.list.data = t;
        this.list.node.x = -this.list.node.width / 2;
        this.lblHighScore.string = i18n.t("CLOTHE_PVE_MY_HIGH", {
            d: l.clothePveProxy.getIdScore(this._curData.id)
        });
        this.updateCount();
    },
    onClickAdd() {
        if (l.clothePveProxy.info && l.clothePveProxy.info.info.eTime < i.timeUtil.second) i.alertUtil.alert18n("ACTHD_OVERDUE");
        else {
            var t = i.utils.getParamInt("clothe_cost");
            i.utils.showConfirmItem(i18n.t("CLOTHE_PVE_ADD_CONFIRM", {
                d: t
            }), 1, l.playerProxy.userData.cash,
            function() {
                l.playerProxy.userData.cash < t ? i.alertUtil.alertItemLimit(1) : l.clothePveProxy.sendAdd();
            },
            "CLOTHE_PVE_ADD_CONFIRM");
        }
    },
    updateCount() {
        this.lblCount.string = i18n.t("CLOTHE_PVE_REMAIN", {
            d: l.clothePveProxy.info.count - l.clothePveProxy.base.use
        });
        if (this.nodeClear.active) {
            this.select.max = l.clothePveProxy.info.count - l.clothePveProxy.base.use;
            this.select.curValue = this.select.curValue > this.select.max ? this.select.max: this.select.curValue < 1 ? 1 : this.select.curValue;
        }
    },
    onClickFight() {
        if (l.clothePveProxy.info && l.clothePveProxy.info.info.eTime < i.timeUtil.second) i.alertUtil.alert18n("ACTHD_OVERDUE");
        else if (l.clothePveProxy.info.count - l.clothePveProxy.base.use <= 0 && l.clothePveProxy.base.gate >= this._curData.id) i.alertUtil.alert18n("CLOTHE_COUNT_LIMIT");
        else {
            i.utils.openPrefabView("clothe/ClotheChange", !1, this._curData);
            this.onClickClost();
        }
    },
    onClickFast() {
        l.clothePveProxy.info && l.clothePveProxy.info.info.eTime < i.timeUtil.second ? i.alertUtil.alert18n("ACTHD_OVERDUE") : l.clothePveProxy.info.count - l.clothePveProxy.base.use < this.select.curValue || 0 == this.select.curValue ? i.alertUtil.alert18n("CLOTHE_COUNT_LIMIT") : this._curData.id > l.clothePveProxy.base.gate || l.clothePveProxy.sendClear(this._curData.id, this.select.curValue);
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
