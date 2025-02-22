//this._data = jiaoyouId:this.jiaoyouData.id,cardid:allCard[i-1]

let RenderListItem = require("RenderListItem");
let List = require("List")
cc.Class({
    extends: RenderListItem,

    properties: {
        showList:List
    },

    onLoad () {

    },

    start () {

    },

    showData() {
        var t = this._data;
        if (t) {
            this.showList.data = t;
        }
    }
});
