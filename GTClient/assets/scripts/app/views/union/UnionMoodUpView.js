let Utils = require("Utils");
let List = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        list: List,
    },

    onLoad: function() {
        this.showList();
        facade.subscribe("UNION_PARTY", this.onClickClose, this);
    },

    showList: function() {
        this.list.data = localcache.getList(localdb.table_party_buff);
    },

    onClickClose: function() {
        Utils.utils.closeView(this);
    },
    
});
