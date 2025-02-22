let Initializer = require("Initializer");
var List = require("List");
var Utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        listView:List,
    },

    ctor(){
        this.listdata = [];
    },

    onLoad: function() {
        facade.subscribe(Initializer.bagProxy.UPDATE_BAG_ITEM,this.refreshList,this);
        let listdata = localcache.getList(localdb.table_yuer);
        this.listdata = Utils.utils.clone(listdata);
        this.refreshList();
    },

    refreshList(){
        this.listView.data = this.listdata;
    },

    //关闭
    onClickClose: function() {
        Utils.utils.closeView(this, !0);
    },

    

});
