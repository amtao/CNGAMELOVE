var i = require("Utils");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        //nodeArr: [cc.Node],
        roleUrl: n,
        content1: cc.Label,
        content2: cc.Label,
        kuang: cc.Node,
        urlBg: n,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.heroid),
            o = t.type,
            n = [];
            // if (100 * Math.random() <= 50) o = e.type;
            // else {
                for (var a = 1; a < 5; a++) a != e.type && n.push(a + "");
                o = parseInt(n[Math.floor(Math.random() * n.length)]);
            //}
            Math.random();
            var s = "WIFE_WEN_HOU_TYPE_" + t.heroid + "_";
            //0 != t.isgad && i.alertUtil.alert18n("WIFE_IS_GAD");
            //var c = i18n.t(s).split("|");
            this.content1.string = i18n.t(s + "1");
            this.content2.string = i18n.t(s + "2");
            this.urlBg.url = l.uiHelps.getPartnerZoneBgImg(t.heroid);
            //for (a = 0; a < this.nodeArr.length; a++) this.nodeArr[a].active = a == o - 1;
            this.roleUrl.url = l.uiHelps.getServantSpine(t.heroid);
            r.timeProxy.floatReward();
            // i.alertUtil.alert(i18n.t("WIFE_WEN_HOU_QIN_MI", {
            //     name: e.name
            // }));
            //this.kuang.height = c[0].length > 11 ? 950 : 450;
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
