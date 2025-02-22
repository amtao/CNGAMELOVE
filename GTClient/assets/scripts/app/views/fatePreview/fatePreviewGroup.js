
var List = require("List");
var RenderListItem = require("RenderListItem");


cc.Class({
    extends: RenderListItem,

    properties: {
        content: List,
        titleList: [cc.Node]        // 0 普通 1 非凡 2 稀世
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {

    },

    start () {
    },

    showData () {
        var t =  this.data;
        if (t) {
            this.content.data = t.list;
            // var isShow = false;
            // for (var i = 0; i < t.info.length; i++) {
            //     if (t.index === t.info[i].startIndex) {
            //         var titleIndex = t.info[i].qualityID - 1;
            //         this.showTitle(titleIndex);
            //         isShow = true;
            //         return;
            //     }
            // }
            // if (!isShow){
            //     this.hideTitle();
            // }
        }
    },

    showTitle (titleIndex) {
        // this.node.setContentSize(720, 350);
        // this.content.node.position.y = -50;
        this.titleList[titleIndex].active = true;
        // this.titleList[titleIndex].position.y = -25
    },

    hideTitle () {
        // this.node.setContentSize(720, 300);
        // this.content.node.position.y = 0;
        for (var i = 0; i < this.titleList.length; i++) {
            this.titleList[i].active = false;
        }
    }

    // update (dt) {},
});
