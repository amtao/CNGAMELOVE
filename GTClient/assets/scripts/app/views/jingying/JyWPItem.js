var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        face: n,
        nodeLock: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (null != t.islock) {
            this.nodeLock.active = 1 == t.islock;
            this.face.url = "";
        } else if (null != t.id) {
            this.nodeLock.active = !1;
            this.face.url = l.uiHelps.getServantHead(t.id);
        }
    },
    onClick(t, e) {
        r.utils.openPrefabView("JyWeipai", !1, {
            type: parseInt(e)
        });
    },
});
