var i = require("Utils");
var n = require("ItemSlotUI");
var l = require("List");
var r = require("UIUtils");
var a = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        itemSlot:n,
        lblName:cc.Label,
        lblEffect:cc.Label,
        lblTime:cc.Label,
        lblCount:cc.Label,
        list:l,
        nodeTime:cc.Node,
        nodeCount:cc.Node,
    },
    onLoad() {
        var t = this.node.openParam;
        this.onShowHe(t);
    },
    onShowHe(t) {
        this.node.active = null != t;
        this._curdata = t;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.itemid + "");
            if (e) {
                this.lblName.string = e.name;
                this.lblEffect.string = e.explain;
            }
            this.nodeTime.active = 0 != t.outtime;
            0 != t.outtime ? r.uiUtils.countDown(t.outtime, this.lblTime) : this.lblTime.unscheduleAllCallbacks();
            this.itemSlot.data = t;
            this.list.data = t.need;
            this.list.node.x = -this.list.node.width / 2;
            this.nodeCount.active = 0 != t.totonum;
            0 != t.totonum && (this.lblCount.string = i18n.t("COMMON_NUM", {
                f: t.times,
                s: t.totonum
            }));
        }
    },
    onClickHe() {
        if (this._curdata) {
            if (0 != this._curdata.totonum && this._curdata.times <= 0) {
                i.alertUtil.alert(i18n.t("BAG_COMPOSE_COUNT_LIMIT"));
                return;
            }
            for (var t = 0; t < this._curdata.need.length; t++) {
                var e = this._curdata.need[t];
                if (a.bagProxy.getItemCount(e.id) < e.count) {
                    i.alertUtil.alertItemLimit(e.id);
                    return;
                }
            }
            a.bagProxy.sendCompose(this._curdata.itemid);
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
