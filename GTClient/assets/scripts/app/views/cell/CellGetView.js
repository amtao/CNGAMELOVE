var i = require("UrlLoad");
var n = require("UIUtils");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        roleImg: i,
        l_descText: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam.data;
        if (t) {
            var e = localcache.getItem(localdb.table_prisoner_pic, t.id);
            this.roleImg.url = n.uiHelps.getCellBody(e.mod1);
            this.l_descText.string = "";
        }
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
