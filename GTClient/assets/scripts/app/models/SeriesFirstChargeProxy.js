
var initializer = require("Initializer");
let redDot = require("RedDot");

var SeriesFirstChargeProxy = function () {
    this.data = null;

    this.ctor = function () {
        JsonHttp.subscribe(proto_sc.fuli.fexchofuli, this.onFirstRecharge, this);
    };

    this.clearData = function() {
        this.data = null;
    };

    // num :每个档位对应的钻石数； rwd: 已领取id集合
    this.onFirstRecharge = function(t) {
        this.data = t;
        this.checkRedDot();
        facade.send("SERIES_FIRST_CHARGE_UPDATE");
    };

    this.checkRedDot = function() {
        for(let i = 1; i <= 4; i++) {
            redDot.change("firstRecharge" + i, this.checkCanGet(i));
        }    
    }

    this.sendGetReward = function(ID) {
        var request = new proto_cs.fuli.fcho_ex();
        request.id = ID;
        JsonHttp.send(request, function() {
            // TODO
            initializer.timeProxy.floatReward();
        });
    };

    // ID : 充值档位ID从1开始
    this.checkIsGot = function (ID) {
        var flag = false;
        if (!this.data) return false;
        return this.data.rwd[ID] === 1;
        // this.data.rwd.forEach((isGotID) => {
        //     if (isGotID === ID) flag = true;
        // })
        return flag;
    };

    // 第一档充值任意金额领取
    this.checkCanGet = function (ID) {
        var flag = false;
        if (!this.data) return false;
        var needMoney = localcache.getItem(localdb.table_shouchongMoney, ID);
        flag = needMoney && this.data.num >= needMoney.num && !this.checkIsGot(ID);
        return flag;
    };

    // 所有档位已经领取
    this.checkIsAllGot = function () {
        if (!this.data) return false
        return this.data.rwd[1] === 1 && this.data.rwd[2] === 1 && this.data.rwd[3] === 1 && this.data.rwd[4] === 1;
    }
}
exports.SeriesFirstChargeProxy = SeriesFirstChargeProxy;