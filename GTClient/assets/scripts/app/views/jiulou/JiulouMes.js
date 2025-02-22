var i = require("Utils");
var n = require("Initializer");
var l = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCount: cc.Label,
        list: l,
        lblAdd: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = n.jiulouProxy.win.yhnew;
        this.list.data = t.list;
        this.lblCount.string = i18n.t("JIU_LOU_MES_TXT", {
            num: n.jiulouProxy.win.yhnew.list.length
        });
        this.lblAdd.string = "+" + n.jiulouProxy.win.yhnew.allep;
    },
    onClickClost() {
        i.utils.closeView(this);
        n.jiulouProxy.win = null;
    },
});
