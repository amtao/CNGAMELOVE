let Utils = require("Utils");
let scList = require("List");
let Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        itemList: scList,
    },

    onLoad () {
        let openParam = this.node.openParam;
        let suitid = openParam.suitid;
        let cfg = localcache.getItem(localdb.table_usersuit,suitid);
        let listData = []
        for (let ii = 0; ii < 5; ii++){
            let storyname = cfg[`storyname${ii+1}`];
            if (storyname == null || storyname == "") continue;
            listData.push({type:cfg[`unlocktype${ii+1}`],para:cfg[`para${ii+1}`],storyname:storyname,story:cfg[`story${ii+1}`],suitid:suitid})
        }
        this.itemList.data = listData;
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },


});
