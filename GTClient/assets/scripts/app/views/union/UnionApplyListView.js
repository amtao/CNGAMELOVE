var i = require("Initializer");
var n = require("List");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
        // applyNum: cc.Label,
        // toggle: cc.Toggle,
        // nodeScroll: cc.Node,
        // nodeTip: cc.Node,
        nodeIsEnter: cc.Node,
        allbtnOKs:cc.Node,
        allbtnRejs:cc.Node,
    },
    ctor() {},
    onLoad() {
        this.UPDATE_APPLY_LIST();
        facade.subscribe("UPDATE_APPLY_LIST", this.UPDATE_APPLY_LIST, this); ! i.unionProxy.memberInfo || (1 != i.unionProxy.memberInfo.post && 2 != i.unionProxy.memberInfo.post) || i.unionProxy.sendApplyList();
        //this.toggle.isChecked = 1 == i.unionProxy.clubInfo.isJoin;
        this.nodeIsEnter.active = i.unionProxy.clubInfo.isJoin == 1
    },
    UPDATE_APPLY_LIST() {
        var t = i.unionProxy.applyList;
        //this.nodeTip.active = null == t || 0 == t.length;
        //this.nodeScroll.active = t && t.length > 0;
        this.list.data = t;

        if(this.allbtnOKs && this.allbtnRejs){
            this.allbtnOKs.active = t && t.length > 0
            this.allbtnRejs.active = t && t.length > 0
        }
    },
    eventClose() {
        l.utils.closeView(this);
    },
    eventCheck(t) {
        this.nodeIsEnter.active = !this.nodeIsEnter.active
        i.unionProxy.sendAllowRandomJoin(this.nodeIsEnter.active ? 1 : 0);
    },
    onekeyReject() {
        i.unionProxy.sendReject(0);
    },
    onClickAccept(t, e) {
        var o = e.data;
        o && i.unionProxy.sendApplyJoin(o.id);
    },
    onClickReject(t, e) {
        var o = e.data;
        o && i.unionProxy.sendReject(o.id);
    },
    onClickAcceptAll(t,e){
        i.unionProxy.sendApplyJoin(0);
    },
    onClickRejectAll(t, e) {
        i.unionProxy.sendReject(0);
    },
});
