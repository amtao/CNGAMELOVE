var i = require("RenderListItem");
var n = require("Utils");
var l = require("UrlLoad");
var r = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblDate: cc.Label,
        lblTime: cc.Label,
        lblTitle: cc.Label,
        redNode: cc.Node,
        itemBg: l,
    },
    ctor() {
        this.hdData = null;
    },
    showData() {
        this.hdData = this._data;
        this.lblTitle.string = this.hdData.title;
        this.lblDate.string = i18n.t("COMMON_MAO_HAO", {
            l: i18n.t("ACTIVITY_DATE_TXT"),
            r: n.timeUtil.format(this.hdData.sTime, "MM-dd") + i18n.t("COMMON_ZHI") + n.timeUtil.format(this.hdData.eTime, "MM-dd")
        });
        this.redNode.active = 1 == this.hdData.news;
        this.hdData.skin && 0 != this.hdData.skin ? (this.itemBg.url = r.uiHelps.getLimitActivityBg(this.hdData.skin)) : (this.itemBg.url = r.uiHelps.getLimitActivityBg(this.hdData.id));
        var t = this;
        r.uiUtils.countDown(this.hdData.eTime, this.lblTime,
        function() {
            t.lblTime.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
    },
    onClickEnter(t, e) {
        n.timeUtil.second >= this.hdData.showTime ? n.alertUtil.alert(i18n.t("ACTIVITY_NOT_IN_TIME")) : "2" == e ? n.utils.openPrefabView("limitactivity/LimitActivityWindow", null, this.hdData) : "3" == e ? n.utils.openPrefabView("limitactivity/AtListWindow", null, this.hdData) : "4" == e && n.utils.openPrefabView("limitactivity/RechargeWindow", null, this.hdData);
    },
});
