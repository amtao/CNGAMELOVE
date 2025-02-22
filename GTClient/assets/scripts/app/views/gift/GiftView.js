var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        listView: i,
        txt_love: cc.Label,
        txt_charm: cc.Label,
    },
    ctor() {
        this.itemArr = [{ id: 91 }, { id: 92 }, { id: 93 }, { id: 94 }];
        this.itemArr2 = [{ id: 1 }, { id: 2 }, { id: 3 }, { id: 4 }];
    },
    onLoad() {
        facade.subscribe("UPDATE_WIFE_JB", this.showData, this);
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.showData, this);
        this.showData();
    },
    showData() {
        this.txt_love.string = n.jibanProxy.getWifeJB(n.wifeProxy.wifeGiftId) + "";
        var t = n.wifeProxy.getWifeData(n.wifeProxy.wifeGiftId),
        e = localcache.getItem(localdb.table_wife, n.wifeProxy.wifeGiftId),
        o = n.wifeProxy.getGiftList(null != t, e.type);
        this.listView.data = o;
    },
    closeBtn() {
        l.utils.closeView(this);
    },
});
