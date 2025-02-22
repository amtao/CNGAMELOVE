var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        tipNode: cc.Node,
    },
    ctor() {},
    onLoad() {
        let listdata = []
        if (n.unionProxy.clubLog != null){
            listdata = n.unionProxy.clubLog.filter((data)=>{
                return data.type == 14;
            })
        }
        this.list.data = listdata;
        this.tipNode.active = 0 == listdata.length;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
