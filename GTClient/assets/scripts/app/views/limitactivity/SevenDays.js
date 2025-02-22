var i = require("RenderListItem");
var n = require("Utils");
var l = require("UrlLoad");
var r = require("UIUtils");
var Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lb_msg: cc.RichText,
        lb_day1: cc.Label,
        lb_day2: cc.Label,
        lb_day3: cc.Label,
        lb_day4: cc.Label,
        lb_day5: cc.Label,
        lb_day6: cc.Label,
        lb_day7: cc.Label,
    },
    ctor() {
        JsonHttp.subscribe(proto_sc.sevenCelebration.seveninfo, this.onSeveninfo, this);
    },

    onLoad() {
        this.initView();
        // var ss = new proto_cs.sevendays.sevenSign();
        // ss.signday = 1;
        // JsonHttp.send(ss, function(data) {
        //     console.log(JSON.stringify(data));
        // });
    },

    onSeveninfo(info) {
        console.log(JSON.stringify(info));
    },

    initView() {
        this.lb_msg.string = i18n.t("SEVEN_DAYS_MSG", {day: 10, hour:33, leftday:11, lefthour:22});
        this.lb_day1.string = i18n.t("SEVEN_DAYS_DAY", {day: 1});
        this.lb_day2.string = i18n.t("SEVEN_DAYS_DAY", {day: 2});
        this.lb_day3.string = i18n.t("SEVEN_DAYS_DAY", {day: 3});
        this.lb_day4.string = i18n.t("SEVEN_DAYS_DAY", {day: 4});
        this.lb_day5.string = i18n.t("SEVEN_DAYS_DAY", {day: 5});
        this.lb_day6.string = i18n.t("SEVEN_DAYS_DAY", {day: 6});
        this.lb_day7.string = i18n.t("SEVEN_DAYS_DAY", {day: 7});
        
    },
    onClickEnter(t, e) {
        n.timeUtil.second >= this.hdData.showTime ? n.alertUtil.alert(i18n.t("ACTIVITY_NOT_IN_TIME")) : "2" == e ? n.utils.openPrefabView("limitactivity/LimitActivityWindow", null, this.hdData) : "3" == e ? n.utils.openPrefabView("limitactivity/AtListWindow", null, this.hdData) : "4" == e && n.utils.openPrefabView("limitactivity/RechargeWindow", null, this.hdData);
    },

    onClickBack() {        
        n.utils.closeView(this);        
    },
});
