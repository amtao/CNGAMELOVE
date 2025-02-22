var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        headImg: n,
        img: cc.Sprite,
        img1: cc.Sprite,
        qImg: cc.Node,
    },
    ctor() {},
    onClickHead() {
        this._data;
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_prisoner_pic, t.id);
            null != e ? (this.headImg.url = l.uiHelps.getCellHeadIcon(e.mod1)) : (this.node.active = !1);
        }
    },
});
