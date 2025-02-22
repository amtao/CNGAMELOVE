var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        black: cc.Node,
        lblIndex: cc.Label,
        lblNum: cc.Label,
        newNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            if (n.spellProxy.cfg.debris) {
                var e = n.spellProxy.cfg.debris.indexOf(t) + 1;
                this.lblIndex.string = e + "";
            }
            this.lblNum.string = i18n.t("SPELL_HAVE_NUM", {
                num: t.num ? t.num: 0
            });
            this.black.active = null == t.num || 0 == t.num;
            var o, i = n.timeProxy.getLoacalValue("spell");
            if ((o = JSON.parse(i))) for (var l = 0; l < o.length; l++) {
                var r = o[l];
                r && r.id == t.id && (this.newNode.active = r.num < t.num);
            }
        }
    },
});
