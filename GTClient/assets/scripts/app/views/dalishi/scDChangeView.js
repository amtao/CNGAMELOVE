let scUtils = require("Utils");
let scList = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        list: scList,
    },

    onLoad: function() {
        this.list.data = localcache.getList(localdb.table_exchange);
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },
});
