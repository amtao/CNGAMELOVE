let Utils = require("Utils");
let scList = require("List");
let Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        itemList: scList,
        scrollview:cc.ScrollView,
    },

    onLoad () {
        let listdata = localcache.getList(localdb.table_huafu);
        listdata.sort((a,b)=>{
            return a.lv < b.lv ? -1 : 1;
        })
        this.itemList.data = listdata;
        let idx = 0;
        for (var ii = 0; ii < listdata.length; ii++){
            let cg = listdata[ii];
            if (cg.lv >= Initializer.clotheProxy.pickLv){
                idx = ii;
                break;
            }
        }
        idx = idx * 200 - this.scrollview.node.height * 0.5;
        if (idx < 0){
            idx = 0;
        }
        if (idx > this.scrollview.content.height - this.scrollview.node.height){
            idx = this.scrollview.content.height - this.scrollview.node.height;
        }
        this.scheduleOnce(()=>{
            if (this == null || this.scrollview == null) return;
            this.scrollview.scrollToOffset(cc.v2(0,idx))
        },0.1)
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },


});
