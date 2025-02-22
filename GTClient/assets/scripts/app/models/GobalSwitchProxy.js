var i = require("Utils");
var GobalSwitchProxy = function() {

    this.switchInfo = new l();

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.derail.list, this.onSettings, this);
    };
    this.clearData = function() {
        this.switchInfo = null;
    };
    this.onSettings = function(t) {
        if (!i.stringUtil.isBlank(t.switch))
            for (var e = t.switch.split(";"), o = 0; o < e.length; o++) {
                var n = e[o].split(":");
                switch (n[0]) {
                    case "wxqq":
                    case "zoumadeng":
                    case "unionOpenLv":
                    case "clubBossNum":
                    case "mainTask":
                        this.switchInfo[n[0]] = parseInt(n[1]);
                        break;

                    default:
                        this.switchInfo[n[0]] = 1 == parseInt(n[1]);
                }
            }
    };
}
exports.GobalSwitchProxy = GobalSwitchProxy;

var SwitchInfo = function() {
    this.status = !1;
    this.hunt = !1;
    this.taofa = !1;
    this.blacklist = !1;
    this.wxqq = 0;
    this.hanlin = !1;
    this.jyOpen = !1;
    this.silkroad = !1;
    this.buildingGray = !1;
    this.isKuaRankOpen = !1;
    this.isKuaRankOpne_day = !1;
    this.isKuaRankOpne_time = !1;
    this.crossChat = !1;
    this.mengZhan = !1;
    this.advertise = !1;
    this.btnOneTen = !1;
    this.btnSkip = !1;
    this.isHideForCheck = !1;
    this.isProForCheck = !1;
    this.isHideNoDress = !1;
    this.openHomeSkin = !1;
    this.impCollege = !1;
    this.isNotice = !1;
    this.clubBoss = !1;
    this.clubBossNum = 0;
    this.reminderPrompt = !1;
    this.oneKeyStudy = !1;
    this.hideNewKing = !1;
    this.mainTask = 0;
    this.exile = !1;
    this.unionOpenLv = 0;
    this.zoumadeng = 0;
}
exports.SwitchInfo = SwitchInfo;
