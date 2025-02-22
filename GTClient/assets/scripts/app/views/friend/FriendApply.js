var list = require("List");
var initializer = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        applyList :list,
        blankNode : cc.Node
    },
    onLoad () {
        facade.subscribe("FAPPLY_FRIEND", this.reflishList, this);
        facade.subscribe("FAPPLY_ID_LIST",this.reflishList,this)
    },

    start () {

    },
    reflishSeach(isV){
        if(isV){
            this.node.active = true
            initializer.friendProxy.sendGetFriendApply();
        }else{
            this.node.active = false
        }
    },
    reflishList () {
        this.blankNode.active = initializer.friendProxy.fapplyList<=0;
        this.applyList.data = initializer.friendProxy.fapplyList;
    },
});
