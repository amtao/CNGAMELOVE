var i = require("UrlLoad");
var n = require("UIUtils");
var l = require("Initializer");
var r = require("ChatBlankItem");
cc.Class({
    extends: cc.Component,
    properties: {
        head: i,
        blank: i,
        bItem: r,
        bItem1: r,
        isLoadPlayer:{ default:false, tooltip: "是否加载主角头像" },
    },
    ctor() {},
    onLoad() {
        this.isLoadPlayer && this.updateUserHead();
    },
    updateUserHead() {
        var t = 1,
        e = 1;
        if (null != l.playerProxy.headavatar) {
            e = l.playerProxy.headavatar.blank;
            var o = localcache.getItem(localdb.table_userhead, l.playerProxy.headavatar.head);
            t = 0 == l.playerProxy.headavatar.head || (o && 0 != o.job && o.job != l.playerProxy.userData.job) ? l.playerProxy.userData.job + 1e4: l.playerProxy.headavatar.head;
        } else t = l.playerProxy.userData.job + 1e4;
        this.setHead(t, e);
    },
    setUserHead(t, e) {
        var o = localcache.getItem(localdb.table_userhead, e.head),
        i = null == e || null == e.head || 0 == e.head || (null != o && o.job != t && 0 != o.job) ? t + 1e4: e.head,
        n = null != e && null != e.blank && 0 != e.blank ? e.blank: 1;
        this.setHead(i, n);
    },
    setHead(t, e) {
        var o = localcache.getItem(localdb.table_userblank, e),
        i = localcache.getItem(localdb.table_userhead, t);
        this.head.url = n.uiHelps.getAvatar(i ? i.id: 1);
        this.blank.url = n.uiHelps.getBlank(o ? o.blankmodel: 1);
        this.bItem && this.bItem.node.active && o && this.bItem.setBlank(o.chatblank, o.minwidth, o.minheight, o.spine, o.chatcolor);
        this.bItem1 && this.bItem1.node.active && o && this.bItem1.setBlank(o.chatblank, o.minwidth, o.minheight, o.spine, o.chatcolor);
    },

    setLocalchatImageToHead(){
        this.head.url = n.uiHelps.getChatLocalSprite("chat_sys")
    }
});
