var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        giftList: i,
        buyList: i,
        lblTime: cc.Label,
        btns: [cc.Button],
        scroll1: cc.Node,
        scroll2: cc.Node,
        red: cc.Node,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.levelGiftProxy.LEVEL_GIFT_DATA_UPDATE, this.onDataUpdate, this);
        l.levelGiftProxy.sendOpenActivity();
        this.onClickTab(null, "0");
    },
    onDataUpdate() {
        var t = this,
        e = l.levelGiftProxy.data;
        if (e) {
            var o = [],
            i = [];
            for (var a in e.free) o.push(e.free[a]);
            for (var a in e.charge) i.push(e.charge[a]);
            o.sort(function(t, e) {
                return t.isget == e.isget ? t.lv - e.lv: t.isget - e.isget;
            });
            this.giftList.data = o;
            this.buyList.data = i;
            r.uiUtils.countDown(e.info.showTime, this.lblTime,
            function() {
                n.timeUtil.second >= e.info.showTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            });
            this.red.active = l.levelGiftProxy.hasRed();
        }
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onClickTab(t, e) {
        for (var o = 0; o < this.btns.length; o++) this.btns[o].interactable = o != parseInt(e);
        this.scroll1.active = "0" == e;
        this.scroll2.active = "1" == e;
    },
});
