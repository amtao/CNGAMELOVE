var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var r = require("TimeProxy");
var CellProxy = function() {

    this.LAO_FANG_DATA_UPDATE = "LAO_FANG_DATA_UPDATE";
    this.LAO_FANG_MING_WAMG_UPDATE = "LAO_FANG_MING_WAMG_UPDATE";
    this.LAO_FANG_PET_LIST = "LAO_FANG_PET_LIST";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.laofang.laofang, this.onLaoFang, this);
        JsonHttp.subscribe(
            proto_sc.laofang.mingwang,
            this.onMingWang,
            this
        );
        JsonHttp.subscribe(proto_sc.laofang.win, this.onWindow, this);
        JsonHttp.subscribe(proto_sc.laofang.pets, this.onPets, this);
    };
    this.clearData = function() {
        this.laoFangData = null;
        this.mingWangData = null;
        this.pets = null;
    };
    this.updateCellRed = function() {
        if (
            null != this.mingWangData &&
            null != this.laoFangData &&
            null != n.taskProxy.mainTask
        ) {
            var t = r.funUtils.isOpenFun(r.funUtils.prisonView);
            l.change(
                "cellFeed",
                this.laoFangData.da < this.laoFangData.kaifang &&
                    this.mingWangData.mw > 0 &&
                    t
            );
        }
    };
    this.onLaoFang = function(t) {
        this.laoFangData = t;
        this.updateCellRed();
        facade.send(this.LAO_FANG_DATA_UPDATE);
    };
    this.onMingWang = function(t) {
        this.mingWangData = t;
        this.updateCellRed();
        facade.send(this.LAO_FANG_MING_WAMG_UPDATE);
    };
    this.onWindow = function(t) {
        if (t.shouyawin)
            localcache.getItem(localdb.table_prisoner, t.shouyawin);
    };
    this.onPets = function(t) {
        this.pets = t;
        facade.send(this.LAO_FANG_PET_LIST);
    };
    this.sendBianDa = function(t, e) {
        var o = new proto_cs.laofang.bianDa();
        o.type = t;
        o.id = e;
        var l = this.getPestInfo(e);
        JsonHttp.send(o, function() {
            n.timeProxy.floatReward(!1);
            var e = localcache.getItem(localdb.table_prisoner_pic, l.id);
            if (l && e) {
                var o = Math.floor(1e4 * Math.random());
                if (l.bjid == t) {
                    o < i.utils.getParamInt("pet_eat_happy") &&
                        i.alertUtil.alert(e.liketxt);
                } else {
                    o < i.utils.getParamInt("pet_eat_like") &&
                        i.alertUtil.alert(e["like" + l.bjid]);
                }
            }
        });
    };
    this.genItem = function() {
        var t = new s();
        t.time = i.timeUtil.second;
        t.ids = [];
        for (
            var e = localcache.getList(localdb.table_prisoner), o = 0;
            o < e.length;
            o++
        ) {
            var n = localcache.getGroup(
                localdb.table_prisoner_pic,
                "grid",
                e[o].id
            );
            if (null != n)
                if (n.length > 1) {
                    var l = Math.floor(Math.random() * n.length);
                    t.ids.push(n[l].id);
                } else t.ids.push(n[0].id);
        }
        return t;
    };
    this.getPestInfo = function(t) {
        if (this.pets)
            for (var e = 0; e < this.pets.length; e++)
                if (this.pets[e].id == t) return this.pets[e];
        return null;
    };
    this.getPetExp = function(t, e) {
        void 0 === e && (e = !1);
        var o = this.getPestInfo(t);
        if (o) {
            var i =
                1e3 *
                    localcache.getItem(localdb.table_prisoner, t).exptype +
                o.lv +
                (e ? 1 : 0);
            return localcache.getItem(localdb.table_prisoner_lv, i);
        }
        return null;
    };
    this.getTqStr = function(t) {
        if (null == t) return "";
        var e = "FEED_TE_QUAN_DES_" + t.pet_type;
        return 1 == t.pet_type || 2 == t.pet_type
            ? i18n.t(e, {
                  num: t.pet_data
              })
            : 3 == t.pet_type || 4 == t.pet_type
            ? i18n.t(e, {
                  num: t.pet_data / 100
              })
            : i18n.t(e);
    };
}
exports.CellProxy = CellProxy;
var s = function() {
    this.time = 0;
    this.ids = [];
};
