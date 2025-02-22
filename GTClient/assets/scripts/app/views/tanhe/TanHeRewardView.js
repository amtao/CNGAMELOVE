var Initializer = require("Initializer");
var Utils = require("Utils");
var TimeProxy = require("TimeProxy");
var UIUtils = require("UIUtils");
var List = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        listview:List,
        scrollview:cc.ScrollView,
    },

    ctor(){
        
    },
    onLoad() {
        
        let listdata = localcache.getList(localdb.table_tanhe);
        this.listview.data = listdata;
        let idx = 0;
        for (var ii = 0; ii < listdata.length; ii++){
            let cg = listdata[ii];
            if (cg.id == Initializer.tanheProxy.baseInfo.maxCopy){
                idx = ii + 1;
                break;
            }
        }
        idx = idx * 124 - this.scrollview.node.height * 0.5;
        if (idx < 0){
            idx = 0;
        }
        if (idx > this.scrollview.content.height - this.scrollview.node.height){
            idx = this.scrollview.content.height - this.scrollview.node.height;
        }
        this.scheduleOnce(()=>{
            if (this == null || this.scrollview == null) return;
            this.scrollview.scrollToOffset(cc.v2(0,idx))
        },0.5)
    },


    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

});
