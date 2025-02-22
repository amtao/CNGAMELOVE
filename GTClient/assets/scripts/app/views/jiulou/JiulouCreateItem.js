var i = require("RenderListItem");
var n = require("Utils");
var l = require("Initializer");
var r = require("ItemSlotUI");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblCount1: cc.Label,
        lblCount2: cc.Label,
        item1: r,
        item2: r,
        toggle: cc.Toggle,
        lblCount: cc.Label,
        cost: r,
        lblcost: cc.Label,
    },
    ctor() {},
    onClickOpen() {
        var t = this._data;
        if (t) {
            if (1 == t.id) {
                if (n.utils.getParamInt("jiulou_nor_count") - l.jiulouProxy.yhType.count <= 0 && !this.isEnough(t)) return;
            } else if (!this.isEnough(t)) return;
            var e = l.jiulouProxy.selectFood[0] ? l.jiulouProxy.selectFood[0].id: 0,
            o = l.jiulouProxy.selectFood[1] ? l.jiulouProxy.selectFood[1].id: 0,
            i = l.jiulouProxy.selectFood[2] ? l.jiulouProxy.selectFood[2].id: 0;
            l.jiulouProxy.sendYhHold(t.id, this.toggle && this.toggle.isChecked ? 1 : 0, e, o, i);
            n.utils.closeNameView("jiulou/JiulouCreate");
            n.utils.openPrefabView("jiulou/JiulouDinnce");
            l.jiulouProxy.selectFood = [];
        }
    },
    isEnough(t) {
        if (2 == t.pay.length) {
            var e = t.pay[0],
            o = t.pay[1],
            i = l.bagProxy.getItemCount(e.id),
            r = l.bagProxy.getItemCount(o.id);
            if (i < e.count) {
                n.alertUtil.alertItemLimit(e.id);
                return ! 1;
            }
            if (r < o.count) {
                n.alertUtil.alertItemLimit(o.id);
                return ! 1;
            }
            return ! 0;
        }
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = i18n.t("JIULOU_CREATE_ROLE_COUNT", {
                n: t.name,
                c: t.xiwei
            });
            if (2 == t.pay.length) {
                var e = n.utils.getParamInt("jiulou_nor_count") - l.jiulouProxy.yhType.count;
                if (1 == t.id && e > 0) return;
                var o = t.pay[0],
                i = t.pay[1];
                this.item1.data = o;
                this.item2.data = i;
                var r = l.bagProxy.getItemCount(o.id),
                a = l.bagProxy.getItemCount(i.id);
                this.lblCount1.string = i18n.t("COMMON_NEED", {
                    f: n.utils.formatMoney(r),
                    s: o.count
                });
                this.lblCount2.string = i18n.t("COMMON_NEED", {
                    f: n.utils.formatMoney(a),
                    s: i.count
                });
            }
        }
    },
});
