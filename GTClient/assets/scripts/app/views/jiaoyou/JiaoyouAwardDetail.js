//进入关卡ui
//9-18 HZW
//this.node.openParam:{fightInfo: 战斗角色数据,stageCfg: 战斗stage配置}
let Utils = require("Utils")
let ItemSlotUI = require("ItemSlotUI")
let Initializer = require("Initializer");
var List = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        listItem:List,
    },
    onLoad () {
        var listdata = this.node.openParam.listdata
        this.listItem.data = listdata;
        this.listItem.node.x = -this.listItem.node.width * 0.5
    },

    onClickBack: function() {
        Utils.utils.closeView(this);
    },

});
