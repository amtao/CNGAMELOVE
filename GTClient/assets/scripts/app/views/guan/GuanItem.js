var i = require("RenderListItem");
var n = require("TimeProxy");
var l = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lbl: cc.Label,
    },
    ctor() {},
    onClick() {
        var t = this._data;
        if (t) {
            n.funUtils.openView(t.id);
            facade.send("CLOSE_GUAN");
        }
    },
    showData() {
        var t = this._data;
        if (t) switch (t.id) {
        case n.funUtils.JingYingView.id:
            var e = l.jingyingProxy.food.num + l.jingyingProxy.army.num + l.jingyingProxy.coin.num;
            this.lbl.string = i18n.t("GUAN_JINGYING", {
                c: e
            });
            break;
        case n.funUtils.zhengwuView.id:
            this.lbl.string = i18n.t("GUAN_ZHENGWU", {
                c: l.jingyingProxy.exp.cd.num
            });
            break;
        case n.funUtils.xunFangView.id:
            this.lbl.string = i18n.t("GUAN_XUNFANG", {
                c: l.lookProxy.xfinfo.num
            });
        }
    },
});
