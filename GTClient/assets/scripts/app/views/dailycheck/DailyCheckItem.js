var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        day: cc.Label,
        item: n,
        yiqiandao: cc.Node,
        weiqiandao: cc.Node,
        eff: sp.Skeleton,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.day.string = i18n.t("THIRTY_DAY_NUM_DAY", {
                num: t.id
            });

            this.item.data = t.items[0];
            var e = l.thirtyDaysProxy.data.level[t.id - 1];
            this.weiqiandao.active = !(this.yiqiandao.active = 2 == e.type);
            if (this.eff) {
                this.eff.node.active = 1 == e.type && t.id == (l.thirtyDaysProxy.getCurrentItem().day);
            }

        }
    },
});
