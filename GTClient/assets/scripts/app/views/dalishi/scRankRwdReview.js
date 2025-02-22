let scUtils = require("Utils");
let scList = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        content: scList,
    },

    onLoad: function() {
        let list = localcache.getList(localdb.table_rank);
        // for(let i = 0, len = list.length; i < len; i++) {
        //     let node = cc.instantiate(this.nRwdItem);
        //     node.parent = this.nRwdParent;
        //     node.active = true;
        //     let script = node.getComponent("scRankRwdItem");
        //     script.setData(list[i]); 
        // }
        this.content.data = list;
    },

    onClickClose() {
        scUtils.utils.closeView(this);
    },
});
