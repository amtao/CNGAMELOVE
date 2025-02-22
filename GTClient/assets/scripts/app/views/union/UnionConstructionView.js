var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblGold: cc.Label,
        lblDj: cc.Label,
        lblGj: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("UPDATE_MEMBER_INFO", this.UPDATE_MEMBER_INFO, this);
        facade.subscribe("PLAYER_USER_UPDATE", this.UserInfoUp, this);
        facade.subscribe("UPDATE_BAG_ITEM", this.itemUpdate, this);
        this.itemUpdate();
        this.UPDATE_MEMBER_INFO();
        this.UserInfoUp();
    },
    eventClose() {
        n.utils.closeView(this);
    },
    UserInfoUp() {
        this.lblGold.string = l.playerProxy.userData.cash + "";
    },
    UPDATE_MEMBER_INFO() {
        var t = localcache.getList(localdb.table_construction);
        this.list.data = t;
    },
    itemUpdate() {
        this.lblDj.string = l.bagProxy.getItemCount(128) + "";
        this.lblGj.string = l.bagProxy.getItemCount(132) + "";
    },
});
