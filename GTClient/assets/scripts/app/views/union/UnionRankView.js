var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UnionRankItem");
cc.Class({
    extends: cc.Component,
    properties: {
        lblname: cc.Label,
        lblrank: cc.Label,
        list: i,
        item_1: r,
        item_2: r,
        item_3: r,
    },
    ctor() {},
    onLoad() {
        this.UPDATE_CLUB_RANK();
        this.UPDATE_MY_RANK();
    },
    eventClose() {
        n.utils.closeView(this);
    },
    onClickApply(t, e) {
        var o = e.data;
        if (o) {
            l.unionProxy.sendApplyUnion(o.id);
            this.eventClose();
        }
    },
    onClickName(t, e) {
        var o = e.data;
        if (o) {
            l.unionProxy.lookClubInfo = o;
            n.utils.openPrefabView("");
        }
    },
    UPDATE_MY_RANK() {
        this.lblname.string = l.unionProxy.myClubRank ? l.unionProxy.myClubRank.cName: "";
        this.lblrank.string = l.unionProxy.myClubRank ? l.unionProxy.myClubRank.cRid + "": "";
    },
    UPDATE_CLUB_RANK() {
        this.item_1.data = l.unionProxy.clubList.length >= 1 ? l.unionProxy.clubList[0] : null;
        this.item_2.data = l.unionProxy.clubList.length >= 2 ? l.unionProxy.clubList[1] : null;
        this.item_3.data = l.unionProxy.clubList.length >= 3 ? l.unionProxy.clubList[2] : null;
        var t = l.unionProxy.clubList.splice(3, l.unionProxy.clubList.length - 1);
        this.list.data = t;
    },
});
