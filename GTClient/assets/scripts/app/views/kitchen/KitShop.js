var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblCost: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = localcache.getList(localdb.table_kitshop);
        this.list.data = t;
        facade.subscribe(n.playerProxy.PLAYER_USER_UPDATE, this.updateFood, this);
        this.updateFood();
    },
    updateFood() {
        this.lblCost.string = n.playerProxy.userData.food + "";
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
