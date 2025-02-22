var i = require("Initializer");
var n = require("RedDot");
var PrinceRecruitProxy = function() {

    this.data = null;
    this.PRINCE_DATA_UPDATE = "PRINCE_DATA_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.jshuodong.unlock,
            this.onDataUpdate,
            this
        );
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        n.change("prince", this.hasRed());
        facade.send(this.PRINCE_DATA_UPDATE);
    };
    this.sendOpenPrince = function() {
        JsonHttp.send(new proto_cs.huodong.hd6181Info());
    };
    this.sendGetReward = function(t) {
        var e = new proto_cs.huodong.hd6181Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.hasRed = function() {
        var t = !1;
        if (0 == this.data.get && this.data.cons >= this.data.cfg.need)
            for (var e = 0; e < this.data.cfg.heros.length; e++) {
                var o;
                o = this.data.cfg.heros[e];
                if (null == i.servantProxy.getHeroData(o)) {
                    t = !0;
                    break;
                }
            }
        return t;
    };
}
exports.PrinceRecruitProxy = PrinceRecruitProxy;
