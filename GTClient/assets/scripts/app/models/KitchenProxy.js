var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var KitchenProxy = function() {

    this.UPDATE_KITCHEN_LIST = "UPDATE_KITCHEN_LIST";
    this.UPDATE_KITCHEN_BASE = "UPDATE_KITCHEN_BASE";
    this.UPDATE_KITCHEN_FOOD = "UPDATE_KITCHEN_FOOD";
    this.UPDATE_KITCHEN_LEVEL = "UPDATE_KITCHEN_LEVEL";
    this.list = null;
    this.base = null;
    this.foods = null;
    this.level = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.kitchen.list, this.onList, this);
        JsonHttp.subscribe(proto_sc.kitchen.base, this.onBase, this);
        JsonHttp.subscribe(proto_sc.kitchen.foods, this.onFoods, this);
        JsonHttp.subscribe(proto_sc.kitchen.level, this.onLevel, this);
    };
    this.clearData = function() {
        this.list = null;
        this.base = null;
        this.foods = null;
        this.level = null;
    };
    this.onLevel = function(t) {
        this.level = t;
        facade.send(this.UPDATE_KITCHEN_LEVEL);
    };
    this.onList = function(t) {
        null == this.list
            ? (this.list = t)
            : i.utils.copyList(this.list, t);
        this.updateDot();
        facade.send(this.UPDATE_KITCHEN_LIST);
    };
    this.updateDot = function() {
        for (var t = !1, e = 0; e < this.list.length; e++) {
            var o = this.list[e];
            if (
                o &&
                0 != o.wid &&
                (o.cd.next < i.timeUtil.second || 0 == o.cd.next)
            ) {
                t = !0;
                break;
            }
        }
        l.change("kitchen_get", t);
    };
    this.onBase = function(t) {
        this.base = t;
        this.createSeat();
        facade.send(this.UPDATE_KITCHEN_BASE);
    };
    this.onFoods = function(t) {
        this.foods = t;
        facade.send(this.UPDATE_KITCHEN_FOOD);
    };
    this.createSeat = function() {
        if (
            null == this.list ||
            0 == this.list.length ||
            this.list.length != this.base.stove
        ) {
            null == this.list && (this.list = []);
            for (var t = this.list.length; t < this.base.stove; t++) {
                var e = {};
                e.id = t + 1;
                e.wid = 0;
                e.itemId = 0;
                e.cd = {};
                e.cd.next = 0;
                e.cd.label = "kitchen";
                this.list.push(e);
            }
        }
    };
    this.hasFood = function(t) {
        if (null == this.foods) return !1;
        for (var e = 0; e < this.foods.length; e++)
            if (this.foods[e] == t) return !0;
        return !1;
    };
    this.sendAllOver = function() {
        JsonHttp.send(new proto_cs.kitchen.allover(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendBuyStove = function() {
        JsonHttp.send(new proto_cs.kitchen.buyStove());
    };
    this.sendOver = function(t) {
        var e = new proto_cs.kitchen.over();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendStart = function(t, e, o) {
        var i = new proto_cs.kitchen.food();
        i.id = e;
        i.wid = t;
        i.itemId = o;
        JsonHttp.send(i);
    };
    this.sendBuyItem = function(t, e, o) {
        void 0 === o && (o = !0);
        var i = new proto_cs.kitchen.buyFood();
        i.id = t;
        i.count = e;
        JsonHttp.send(i, function() {
            o && n.timeProxy.floatReward();
        });
    };
    this.sendOneKeyCook = function(t) {
        var e = new proto_cs.kitchen.allstart();
        e.arr = t;
        JsonHttp.send(e);
    };
    this.sendOneKeyFinish = function() {
        JsonHttp.send(new proto_cs.kitchen.allover(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.isHave = function(t) {
        for (var e = 0; e < this.list.length; e++)
            if (this.list[e].id == t && 0 != this.list[e].wid) return !0;
        return !1;
    };
}
exports.KitchenProxy = KitchenProxy;
