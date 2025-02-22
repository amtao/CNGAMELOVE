var i = require("Initializer");
var ThirtyDaysProxy = function() {

    this.data = null;
    this.THIRTY_DAY_DATA_UPDATE = "THIRTY_DAY_DATA_UPDATE";
    this.THIRTY_DAY_SHOW_DATA = "THIRTY_DAY_SHOW_DATA";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.thirtyCheck.hdQianDaoConfig,
            this.onDataUpdate,
            this
        );
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        facade.send(this.THIRTY_DAY_DATA_UPDATE);
    };

    // this.clearData = function() {
    //     this.data = null;
    // };

    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6500Info());
    };

    this.sendGet = function(ID) {
        var t = this;
        var e = new proto_cs.huodong.hd6500Get();
        e.id = ID;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
            facade.send(t.THIRTY_DAY_SHOW_DATA);
        });
    };

    this.getCurrentItem = function () {
        var curItem = null;
        for (var k = 0; k < this.data.level.length; k++) {
            if(this.data.level[k].type != 2) {
                curItem = this.data.level[k];
                break;
            }
        }
        if (curItem == null) {
            curItem = this.data.level[this.data.level.length - 1];
        }
        return curItem;
    };

    // 是否已经领取完
    this.checkIsFinished = function () {
        if (!this.data) return false;
        if (!this.data.level) return false;
        return this.data.level[this.data.level.length - 1].type == 2;
    };

}
exports.ThirtyDaysProxy = ThirtyDaysProxy;
