var i = require("Utils");
var n = require("UrlLoad");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        wifeTitles: cc.SpriteAtlas,
        bg: cc.Node,
        button: cc.Node,
        wifeItem: cc.Node,
        xxooView: cc.Node,
        Image_name: cc.Sprite,
        Image_role1: cc.Sprite,
        Image_role2: cc.Sprite,
        txt_talk: cc.Label,
        loader1: n,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("CLOSE_XXOO", this.closeBtn, this);
        var t = this.node.openParam;
        if (t) {
            var e = l.wifeProxy.getWifeData(t.wifeId),
            o = localcache.getItem(localdb.table_wife, e.id);
            this.txt_talk.string = this.txt_talk.string = 2 == l.playerProxy.userData.sex ? o.talk2[Math.floor(Math.random() * o.talk.length)] : o.talk[Math.floor(Math.random() * o.talk.length)];
            l.timeProxy.floatReward();
        }
    },
    closeBtn() {
        i.utils.closeView(this);
    },
});
