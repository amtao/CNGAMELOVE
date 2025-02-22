var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("ShaderUtils");
var s = require("Utils");
cc.Class({
    extends: i,
    properties: {
        nodeUnlock: cc.Node,
        url: n,
        lblName: cc.Label,
        img: cc.Sprite,
        img2: cc.Sprite,
    },
    ctor() {},
    onClickInfo() {
        var t = this.data;
        t && s.utils.openPrefabView("ItemInfo", !1, t);
    },
    showData() {
        var t = this.data;
        if (t) {
            var e = r.playerProxy.isUnlockCloth(t.id),
            o = localcache.getItem(localdb.table_userClothe, t.id);
            a.shaderUtils.setImageGray(this.img, !e);
            a.shaderUtils.setImageGray(this.img2, !e);
            this.lblName.string = o ? o.name: "";
            this.nodeUnlock.active = !e;
            var i = o.model.split("|");
            this.url.url = l.uiHelps.getRolePart(i[0]);
        }
    },
});
