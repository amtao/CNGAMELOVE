var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        show: n,
        lblNum: cc.Label,
        lblName: cc.Label,
    },
    ctor() {},
    onClick() {
        r.utils.openPrefabView("treasure/TreasureDetail", !1, this._data);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.data.name;
            for (var e = 0,
            o = 0; o < t.items.length; o++) 1 == t.items[o].rwd && (e += 1);
            this.lblNum.string = i18n.t("COMMON_NUM", {
                f: e,
                s: t.items.length
            });
            this.show.url = l.uiHelps.getTreasureGroup(t.data.photo);
        }
    },
});
