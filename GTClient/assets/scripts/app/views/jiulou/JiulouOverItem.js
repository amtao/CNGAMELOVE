var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblScore: cc.Label,
        lblIndex: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblIndex.string = n.jiulouProxy.win.yhnew.list.indexOf(t) + 1 + "";
            this.lblName.string = t.name;
            this.lblScore.string = i18n.t("JIU_LOU_YAN_HUI_SHU_XING") + " " + t.ep;
        }
    },
});
