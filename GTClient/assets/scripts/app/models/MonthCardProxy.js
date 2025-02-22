var i = require("Initializer");
var n = require("RedDot");
var MonthCardProxy = function() {

    this.cardData = null;
    this.data = null;
    this.MOON_CARD_UPDATE = "MOON_CARD_UPDATE",
    this.EXPERIENCE_ID = 1261;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.fuli.mooncard, this.onMoonCard, this);
    };
    this.clearData = function() {
        this.cardData = null;
    };
    this.onMoonCard = function(t) {
        this.cardData = t;
        n.change("monthCard", this.hasReward());
        facade.send(this.MOON_CARD_UPDATE);
    };
    this.sendGetMoonCard = function(t) {
        var e = new proto_cs.fuli.mooncard();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.getCardData = function(t) {
        for (var e = null, o = 0; o < this.cardData.length; o++)
            if (this.cardData[o].id == t) {
                e = this.cardData[o];
                break;
            }
        return e;
    };
    this.hasReward = function() {
        for (var t = !1, e = 0; e < this.cardData.length; e++) {
            if(this.cardData[e].id == 2) { //暂时不用年卡
                continue;
            }
            if (1 == this.cardData[e].type) {
                t = !0;
                break;
            }
        }
        return t;
    };
}
exports.MonthCardProxy = MonthCardProxy;
