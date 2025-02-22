var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblNode: cc.Node,
        btnYJ: cc.Node,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("UPDATE_SON_TI_QIN", this.onTiQin, this);
        facade.subscribe(n.sonProxy.UPDATE_SON_SHOW_ERRECT, this.onClickClose, this);
        n.sonProxy.sendRefreshTiQin();
        this.onTiQin();
        n.sonProxy.tiQinObj.marryType = 2;
    },
    onTiQin() {
        this.list.data = n.sonProxy.tiQinData;
        this.lblNode.active = !n.sonProxy.tiQinData || 0 == n.sonProxy.tiQinData.length;
        this.btnYJ.active = !!n.sonProxy.tiQinData && n.sonProxy.tiQinData.length >= 2;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onOnkeyJuJue() {
        n.sonProxy.sendOneKeyJuJueTiQin();
    },
});
