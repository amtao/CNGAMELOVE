

var renderListItem = require("RenderListItem");
var initializer = require("Initializer");
var timeProxy = require("TimeProxy");

cc.Class({
    extends: renderListItem,

    properties: {
        titleLabel: cc.Label,
        reachedTimes: cc.Label,
        expLabel:cc.Label,
        taskDescribeLabel: cc.Label,
        goToNode: cc.Node
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    showData () {
        var data = this._data;
        this.use = data.use;
        this.titleLabel.string = data.name ? data.name : "";
        this.reachedTimes.string = initializer.nobleOrderProxy.getTaskReachedTimes(data.id, false);
        this.expLabel.string = data.awardexp ? data.awardexp : 0;
        this.taskDescribeLabel.string = data.info ? data.info : "";
        this.goToNode.active = data.use;
    },

    onGoToClick () {
        if(!this.use) return;
        timeProxy.funUtils.openView(this.use);
    }


    // update (dt) {},
});
