var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblCount: cc.Label,
        lblEffect: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            this.lblCount.string = i18n.t("WELFARE_COUNT_TIP", {
                c: n.welfareProxy.getShenjiCount(t.id)
            });
            this.lblEffect.string = t.content;
        }
    },
});
