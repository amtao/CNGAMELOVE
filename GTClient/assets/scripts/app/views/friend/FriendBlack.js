var utils = require("Utils");
var list = require("List");
var initializer = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        friendList :list,
        blankNode : cc.Node
    },

    onLoad () {
        facade.subscribe("FRIEND_BLACK_LIST",this.onreflishList, this);
        this.onreflishList();
    },

    start () {

    },
    onClickClose : function () {
        utils.utils.closeView(this, true,1);
    },
    onreflishList : function () {
        this.blankNode.active = initializer.chatProxy.blackList.length<=0
        this.friendList.data = initializer.chatProxy.blackList
    }
});
