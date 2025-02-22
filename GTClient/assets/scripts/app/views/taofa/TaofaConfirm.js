var i = require("Utils");
var n = require("SliderCount");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblSolder: cc.Label,
        lblCost: cc.Label,
        silderCount: n,
    },
    ctor() {
        this.waves = {};
        this.maxId = 0;
    },
    onLoad() {
        var t = l.playerProxy.userData;
        this.lblSolder.string = i.utils.formatMoney(t.army);
        var e = this;
        this.silderCount.changeHandler = function() {
            e.lblCost.string = e.waves[l.taofaProxy.playerInfo.gid + e.silderCount.curValue];
        };
    },
    updateCount() {
        this.silderCount.showMmin = l.taofaProxy.playerInfo.gid;
        this.silderCount.max = this.maxId - l.taofaProxy.playerInfo.gid;
        this.genMaxWave();
        this.lblCost.string = this.waves[l.taofaProxy.playerInfo.gid + this.silderCount.curValue];
    },
    onClickClost() {
        this.node.active = !1;
    },
    genMaxWave() {
        var t = l.playerProxy.userData,
        e = l.playerProxy.userEp.e1,
        o = localcache.getList(localdb.table_taofaChaper),
        i = 0;
        this.maxId = 0;
        for (var n = 0; n < o.length; n++) {
            var r = o[n];
            if (r.id >= l.taofaProxy.playerInfo.gid) {
                var a = parseFloat(r.army);
                i += Math.ceil(Math.max(((a * a) / e) * 0.101, 0.26 * a));
                if (t.army > i) {
                    this.waves[r.id] = i;
                    this.maxId = r.id > this.maxId ? r.id: this.maxId;
                }
            }
        }
    },
});
