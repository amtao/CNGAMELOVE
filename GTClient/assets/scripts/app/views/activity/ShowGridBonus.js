let List = require("List");
let Initializer = require("Initializer");
let Utils = require('Utils');
cc.Class({
    extends: cc.Component,
    properties: {
        list: List,
    },
    onLoad() {
        this.onDataUpdate();
    },
    onDataUpdate() {
        this.list.data = Initializer.limitActivityProxy.getMoveBonusInfo();
    },
    onClickClose() {
        Utils.utils.closeView(this);
    },
});
