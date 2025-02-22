var i = require("TaofaItem");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblWave: cc.Label,
        items: [i],
        notFind: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.lblWave.string = n.taofaProxy.playerInfo.gid + "";
        this.notFind.active = null == localcache.getItem(localdb.table_taofaChaper, n.taofaProxy.playerInfo.gid + 1);
    },
});
