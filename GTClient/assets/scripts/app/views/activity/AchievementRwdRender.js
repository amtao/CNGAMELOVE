let RenderListItem = require("RenderListItem");
let List = require("List");
let AchievementRwd = require('AchievementRwd');
let Initializer = require("Initializer");

cc.Class({
    extends: RenderListItem,

    properties: {
        lblTitle: cc.Label,
        finishInfo:cc.Label,
        itemInfo:cc.Label,
        btnGet: cc.Button,
        btnYlq: cc.Node,
        content: List,
        target:AchievementRwd,
    },
    showData () {
        let data = this._data;
        if (data) {
            this.content.data = data.items;
            if (data.get === 1) {//1是已经领取--0-未领取，但条件需要根据type判断
                this.btnYlq.active = true;
                this.btnGet.node.active = false;
            }else{
                this.btnGet.interactable = this.target.checkCanGet(data.type,data.num);
            }
            this.lblTitle.string = data.title?data.title:"";
            this.itemInfo.string = data.info?data.info:"";
            this.finishInfo.string = this.target.getProcessInfo(data.type);
        }
    },
    onGetBtn () {
        this._data && Initializer.limitActivityProxy.sendGetReward(this._data.id);
    },
});
