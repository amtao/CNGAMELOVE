var utils = require("Utils");
var tabUtils = require("TabUtils");
var friendSeach = require("FriendSeach")
var friendApply = require("FriendApply")
cc.Class({
    extends: cc.Component,

    properties: {
        tabs:[tabUtils],
        tabId:0,
        seachNode:friendSeach,
        fapplyNode:friendApply,
        editBox: cc.EditBox,
    },

    onLoad () {
        this.editBox.placeholder = i18n.t("FRIEND_SEARCH_NO_ID");
        // this.seachNode.init(this.node["openParam"])
    },

    start () {
        this.reflishTab()
    },
    onClickClose : function () {
        utils.utils.closeView(this, true,1);
    },
    onClickTab : function (e,id) {
        this.tabId = id-1;
        this.reflishTab()
    },
    reflishTab : function (){
        for(var i=0;i<this.tabs.length;i++){
            this.tabs[i].setCheck(i == this.tabId)
        }
        this.seachNode.reflishSeach(this.tabId == 0);
        this.fapplyNode.reflishSeach(this.tabId == 1);
    }
});
