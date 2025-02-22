var i = require("RenderListItem");
var n = require("Initializer");
var l = require("TimeProxy");
var r = require("UserHeadItem");
var a = require("ChatBlankItem");
var s = require("ChengHaoItem");
var c = require("Config");
var UrlLoad = require("UrlLoad");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblNameRight: cc.Label,
        nodeMask: cc.Node,
        lblVip: cc.Label,
        lblVip1: cc.Label,
        nodeVip: cc.Node,
        nodeVip1: cc.Node,
        sysItem: a,
        leftItem: a,
        rightItem: a,
        leftchengHao: s,
        rightchengHao: s,
        leftChengHaoParentNode: cc.Node,
        head:UrlLoad,
    },
    ctor() {
        this.last = null;
    },
    onClickRole() {
        var t = this.data;
        if (t.user == null || t.user.uid == null) return;
        t && (t.user.uid == n.playerProxy.userData.uid ? n.playerProxy.sendGetOther(n.playerProxy.userData.uid) : n.playerProxy.sendGetOther(t.user.uid));
    },
    onClickContext() {
        var t = this.data;
        if (t && 3 == t.type && 0 == t.msg.indexOf("#")) {
            switch (t.msg.split("#")[1]) {
                case "tangyuan":
                    l.funUtils.openView(l.funUtils.tangyuan.id);
                    return;
                case "boite":
                    l.funUtils.openView(l.funUtils.jiulouView.id);
                    return;
                case "childMarry":
                    l.funUtils.openView(l.funUtils.marryView.id);
                    return;
                case "actqiandao":
                    l.funUtils.openView(l.funUtils.fuli.id);
                    return;
                case "worldtree":
                    l.funUtils.openView(l.funUtils.worldtree.id);
                    return;
                case "wishtree":
                    l.funUtils.openView(l.funUtils.wishingTree.id);
                    return;
            }
        }
    },
    showData() {
        var t = this.data;
        if (t) {
            if (this.last == t) return;
            this.last = t;
            this.leftItem.node.active = this.rightItem.node.active = this.nodeMask.active = null != t.user;
            //this.sysItem.node.active = null == t.user;
            var e = null != t.user && t.user.uid == n.playerProxy.userData.uid;
            this.nodeVip.active = this.nodeVip1.active = !1;
            if (t.user) {
                e = t.user.uid == n.playerProxy.userData.uid;
                this.nodeVip.active = this.nodeVip1.active = t.user.vip > 0;
                this.lblVip.string = this.lblVip1.string = i18n.t("COMMON_VIP_NAME", {
                    v: t.user.vip
                });
                this.nodeMask.x = (e ? 1 : -1) * Math.abs(this.nodeMask.x);
                this.leftItem.node.active = this.lblName.node.parent.active = !e;
                this.rightItem.node.active = this.lblNameRight.node.parent.active = e;
            }
            if (t.user == null) {
                this.leftItem.node.active = this.nodeMask.active = true;
                this.nodeMask.x = (e ? 1 : -1) * Math.abs(this.nodeMask.x);
                this.nodeVip.active = this.lblName.node.parent.active = true;
                this.lblVip.string = this.lblVip1.string = i18n.t("CHAT_SYS_TIP");
            }
            this.lblName.string = this.lblNameRight.string = t.user ? t.user.name : "";
            this.leftChengHaoParentNode.active = !1;
            if (t.user) {
                if (c.Config.isShowChengHao && l.funUtils.isOpenFun(l.funUtils.chenghao)) {
                    var o = localcache.getItem(localdb.table_fashion, t.user.chenghao);
                    this.leftChengHaoParentNode.active = null != o;
                    this.leftchengHao.data = o;
                    var i = localcache.getItem(localdb.table_fashion, t.user.chenghao);
                    this.rightchengHao.data = i;
                }
            } else {
                this.leftchengHao.data = null;
                this.rightchengHao.data = null;
            }
            this.delayShowHead();
            var r = n.chatProxy.getSpMsg(t.msg),
                a = this.node.width / 2;
            this.sysItem.node.active ? this.sysItem.setDest(r, 2 * Math.abs(this.sysItem.node.x)) : this.leftItem.node.active ? this.leftItem.setDest(r, a + Math.abs(this.leftItem.node.x)) : this.rightItem.node.active && this.rightItem.setDest(r, a + Math.abs(this.rightItem.node.x));
        }
    },
    delayShowHead() {
        var t = this.data;
        t && t.user && this.head && n.playerProxy.loadUserHeadPrefab(this.head, t.user.headavatar,{job:t.user.job,level:t.user.level,clothe:t.user.clothe},false);
        if (t && t.user == null) {
            n.playerProxy.loadUserHeadPrefab(this.head,null,null,false,true);
        }
    },
});
