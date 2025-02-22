var i = require("RenderListItem");
var n = require("Initializer");
var l = require("List");
cc.Class({
    extends: i,
    properties: {
        btnYlq: cc.Node,
        btnGet: cc.Button,
        lblTxt: cc.Label,
        list: l,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            var e = localcache.getItem(localdb.table_officer, t.lv);
            this.lblTxt.string = i18n.t("LEVEL_GIFT_KE_GOU_MAI", {
                name: e ? e.name: ""
            });
            this.btnYlq.active = 1 == t.isget;
            this.btnGet.node.active = 0 == t.isget;
            this.btnGet.interactable = n.playerProxy.userData.level >= t.lv;
            this.list.data = t.items;
        }
    },
    onClickGet() {
        var t = this.data;
        n.levelGiftProxy.sendGetReward(t.lv);
    },
});
