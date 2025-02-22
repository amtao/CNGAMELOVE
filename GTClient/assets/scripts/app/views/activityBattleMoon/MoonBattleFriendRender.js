
var renderListItem = require("RenderListItem");
var itemSlotUI = require("ItemSlotUI");
var uiUtils = require("UIUtils");
var initializer = require("Initializer");
var userHeadItem = require("UserHeadItem");
var urlLoad = require("UrlLoad");
cc.Class({
    extends: renderListItem,

    properties: {
        nameLab: cc.Label,
        haoganLab: cc.Label,
        userHead : userHeadItem,
        sendBtn: cc.Button,
        receiveBtn: cc.Button,
        sentNode: cc.Node,
        receivedNode: cc.Node,
        friendNode: cc.Node,
        npcHead: urlLoad,
    },

    showData(){
        let d = this._data;
        if (!!d) {
            this.receivedNode.active = false;
            this.sentNode.active = d.toggleType == 0 && d.isShell;
            this.sendBtn.node.active = d.toggleType == 0 && !d.isShell;
            this.receiveBtn.node.active = d.toggleType == 1;
            this.userHead.node.active = this.friendNode.active = !d.isNPC;
            this.npcHead.node.active = !!d.isNPC;
            this.sendBtn.interactable = initializer.moonBattleProxy.getRwMainFriendSendTimes() > 0;

            if (d.isNPC) {
                let wife = localcache.getItem(localdb.table_wife, 2);
                this.npcHead.url = uiUtils.uiHelps.getServantHead(wife.res);
                this.nameLab.string = initializer.playerProxy.getWifeName(2);
            }else{
                var o = localcache.getItem(localdb.table_officer, d.level);
                this.nameLab.string = `${d.name}  ${o ? o.name: ""}`;
                this.haoganLab.string = "+" + d.love + "";
                this.userHead.setUserHead(d.job,d.headavatar);
            }
        }
    },

    onClickSend(){
        if (!!this._data) {
            initializer.moonBattleProxy.sendShell(this._data.uid);
        }
    },

    onClickGet(){
        if (!!this._data) {
            initializer.moonBattleProxy.sendGetShellRwd(this._data.shellIndex);
        }
    },

    onClickFriendHead : function(){
        if (!!this._data) {
            initializer.friendProxy.sendGetOther(this._data.uid);
        }
    }
});
