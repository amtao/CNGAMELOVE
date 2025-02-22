var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        herolist: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.qixiProxy.QIXI_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(l.qixiProxy.QIXI_SELECT_HERO, this.onSelectHero, this);
        this.onInfo();
        this.onDataUpdate();
    },
    onInfo() {
        this.herolist.selectHandle = function(t) {
            var e = t;
            if (null != e) {
                l.qixiProxy.selectHeroId = e.hid;
                facade.send(l.qixiProxy.QIXI_SELECT_HERO);
            }
        };
        this.herolist.data = l.qixiProxy.data.rwds;
        var t = l.qixiProxy.data.rwds[0];
        l.qixiProxy.selectHeroId = t ? t.hid: 0;
        this.herolist.selectData = t;
    },
    onClickRank() {
        n.utils.openPrefabView("qixi/QiXiView");
    },
    sortHuoDong(t, e) {
        return t.get != e.get ? t.get - e.get: t.id - e.id;
    },
    onSelectHero() {
        this.onDataUpdate();
    },
    onDataUpdate() {
        for (var t = l.qixiProxy.getHeroRwd(l.qixiProxy.selectHeroId), e = 0, o = 0; o < t.rwd.length; o++) e < t.rwd[o].items.length && (e = t.rwd[o].items.length);
        var i = Math.ceil(e / 4),
        n = 100 * i + 10 * (i - 1) + 150;
        this.list.setWidthHeight(475, n);
        this.list.data = t ? t.rwd: null;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
