var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        itemSlot: n,
        dark: cc.Node,
        light: cc.Node,
        eff: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.dark.active = null == t.items;
            this.light.active = null != t.items;
            this.eff.active = null != t.items;
            this.itemSlot.node.active = null != t.items;
            t.items && (this.itemSlot.data = t.items);
        }
    },
    onClickLight() {
        var t = this._data;
        if (t) {
            if (t.items) return;
            if (l.lanternProxy.data.light <= 0) {
                r.alertUtil.alert18n("LAN_TERN_LIMIT_COUNT");
                return;
            }
            if (12 == t.did && !this.isCanLight()) {
                r.alertUtil.alert18n("LAN_TERN_BIG_LIMIT_TXT");
                return;
            }
            l.lanternProxy.sendLightLantern(t.did);
        }
    },
    isCanLight() {
        for (var t = 0; t < l.lanternProxy.data.draw.length; t++) if (12 != l.lanternProxy.data.draw[t].did && null == l.lanternProxy.data.draw[t].items) return ! 1;
        return ! 0;
    },
});
