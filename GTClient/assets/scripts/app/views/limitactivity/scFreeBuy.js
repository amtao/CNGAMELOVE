let scbaseAct = require("scActivityItem");
let scItem = require("scFreeBuyItem");

cc.Class({
    extends: scbaseAct,

    properties: {
        scItems: [scItem],
    },

    setData: function() {
        let data = localcache.getFilters(localdb.table_giftpack, "type", 1);
        for(let i = 0, len = this.scItems.length; i < len; i++) {
            this.scItems[i].setData(data[i]);
        }
    },

});
