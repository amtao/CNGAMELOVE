
let Initializer = require("Initializer");
let List = require("List");
let Utils = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        listItem:List,
    },
    ctor() {},
    onLoad() {
        let openParam = this.node.openParam;
        let listCfg = localcache.getGroup(localdb.table_userSuitLv2,"suit",openParam.suitid);
        this.listItem.data = listCfg;
    },

    onClickClost() {
        Utils.utils.closeView(this, !0);
    },
});
