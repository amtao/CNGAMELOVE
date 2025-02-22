var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("LanternItemRender");
var a = require("UIUtils");
var s = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCount: cc.Label,
        lblTRecharge: cc.Label,
        lblDes: cc.Label,
        lblTime: cc.Label,
        items: [r],
        records: i,
        scroll: cc.ScrollView,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.lanternProxy.LANTERN_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(l.lanternProxy.LANTERN_RECORD_UPDATE, this.onRecords, this);
        l.lanternProxy.sendOpenActivity();
    },
    onDataUpdate() {
        if (l.lanternProxy.data) {
            for (var t = 0; t < l.lanternProxy.data.draw.length; t++) t < this.items.length && (this.items[t].data = l.lanternProxy.data.draw[t]);
            this.lblCount.string = i18n.t("LANTERN_LIGHT_COUNT", {
                num: l.lanternProxy.data.light
            });
            this.lblDes.string = i18n.t("LAN_TERN_DES_TXT", {
                num: l.lanternProxy.data.need
            });
            var e = this;
            a.uiUtils.countDown(l.lanternProxy.data.info.showTime, this.lblTime,
            function() {
                e.lblTime.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
        }
    },
    onRecords() {
        this.records.data = l.lanternProxy.records;
        this.scroll.scrollToBottom();
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onClickRecharge() {
        s.funUtils.openView(s.funUtils.recharge.id);
    },
});
