var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        unionName: cc.Label,
        humanShili: cc.Label,
        uniManifesto: cc.Label,
        list: i,
        lblLv: cc.Label,
        nodeApply: cc.Node,
        bg: cc.Node,
        mask: cc.Node,
        nodeLbl: cc.Node,
        nodeName: cc.Node,
        nodePos: cc.Node,
    },
    ctor() {
        this.unionId = 0;
    },
    eventClose() {
        n.utils.closeView(this);
    },
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            this.unionName.string = t.name;
            for (var e = 0,
            o = 0; o < t.members.length; o++) e += t.members[o].shili;
            this.humanShili.string = i18n.t("UNION_TOTAL_POWER") + "：" + n.utils.formatMoney(e || t.allShiLi);
            this.uniManifesto.string = i18n.has(t.outmsg) ? i18n.t(t.outmsg) : t.outmsg;
            this.list.data = t.members;
            this.lblLv.string = i18n.t("UNION_LEVEL_TXT", {
                num: t.level
            });
            this.nodeApply.active = null == l.unionProxy.memberInfo || 0 == l.unionProxy.memberInfo.cid;
            this.bg.height = null == l.unionProxy.memberInfo || 0 == l.unionProxy.memberInfo.cid ? 640 : 703;
            this.mask.height = null == l.unionProxy.memberInfo || 0 == l.unionProxy.memberInfo.cid ? 585 : 650;
            this.unionId = t.id;
            var i = n.stringUtil.isBlank(t.members[0].shili + "");
            this.nodeLbl.active = l.unionProxy.clubInfo && l.unionProxy.clubInfo.id == t.id && !i;
            if (i) {
                this.nodeName.x = -125;
                this.nodePos.x = 130;
            } else {
                this.nodeName.x = -195;
                this.nodePos.x = 226;
            }
        }
    },
    onClickApply() {
        l.unionProxy.sendApplyUnion(this.unionId);
        this.eventClose();
    },
});
