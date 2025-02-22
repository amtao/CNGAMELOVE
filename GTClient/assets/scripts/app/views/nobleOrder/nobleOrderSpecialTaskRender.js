

var renderListItem = require("RenderListItem");
var initializer = require("Initializer");
var timeProxy = require("TimeProxy");

cc.Class({
    extends: renderListItem,

    properties: {
        taskName: cc.Label,
        taskExp:cc.Label,
        taskDescribeLabel: cc.Label,
        successNode: cc.Node,
        goToNode: cc.Node
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    showData () {
        var data = this._data;
        this.use = data.use;
        this.taskName.string = data.name ? data.name : "";
        var des = data.info ? data.info : "";
        var need = data.need;
        var progress = initializer.nobleOrderProxy.getTaskReachedTimes(data.id, true);
        this.taskDescribeLabel.string = des + " : " + progress + "/" + need;
        this.taskExp.string = data.awardexp ? data.awardexp : 0;
        var isReached =  progress >= need;
        //this.successNode.active = isReached;
        //this.goToNode.active = !isReached && data.use;


        //累计task_exp

        //名字taskName

        //taskDescribeLabel



    },

    onGoToClick () {
        if(!this.use) return;
        timeProxy.funUtils.openView(this.use);
    }

    // update (dt) {},
});
