var i = require("UrlLoad");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("List");
var a = require("Utils");
var s = require("JiBanShow");
var c = require("ServantStarShow");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblTotal: cc.Label,
        lblGet: cc.Label,
        roleImg: i,
        ep1: cc.Label,
        ep2: cc.Label,
        ep3: cc.Label,
        ep4: cc.Label,
        luckImg: s,
        lblJiBan: cc.Label,
        lblHuoQu: cc.Label,
        starShow: c,
        lblTitle: cc.Label,
        list: r,
        tablentNode: cc.Node,
        lblZz: cc.Label,
    },
    ctor() {
        this.obj = { p1: 0, p2: 0, p3: 0, p4: 0 };
    },

    onLoad() {
        facade.subscribe("UPDATE_HERO_JB", this.updateJiban, this);
        var t = this.node.openParam;
        if (t) {
            this.lblName.string = t.name;
            this.lblGet.string = t.txt ? t.txt : "";
            this.roleImg.url = l.uiHelps.getServantSmallSpine(t.heroid);
            for (var e = 0, o = 0; o < t.skills.length; o++) {
                var i = localcache.getItem(
                    localdb.table_epSkill,
                    t.skills[o].id
                );
                this.obj["p" + i.ep] += 10 * i.star;
                e += i.star;
            }
            this.lblTotal.string = i18n.t("SERVANT_PROP_TOTAL", {
                value: e
            });
            this.ep1.string = this.obj.p1 + "";
            this.ep2.string = this.obj.p2 + "";
            this.ep3.string = this.obj.p3 + "";
            this.ep4.string = this.obj.p4 + "";
            var r = n.jibanProxy.getHeroJbLv(t.heroid).level % 1e3;
            this.luckImg.setValue(5, r);
            var a = n.jibanProxy.getHeroNextJb(t.heroid, r);
            this.lblJiBan.string =
                n.jibanProxy.getHeroJB(t.heroid) + "/" + a.yoke;
            this.lblHuoQu.string = t.unlock;
            this.starShow.setValue(t.star);
            var s = n.servantProxy.getHeroData(t.heroid);
            this.lblTitle.string = s
                ? i18n.t("WIFE_WEI_YI_JIE_SHI")
                : i18n.t("WIFE_WEI_JIE_SHI");
            var c = [],
                _ = 0;
            for (o = 0; o < t.skills.length; o++) {
                i = localcache.getItem(
                    localdb.table_epSkill,
                    t.skills[o].id
                );
                var d = {};
                d.id = i.sid;
                d.level = d.hlv = 0;
                _ += i.star;
                c.push(d);
            }
            this.list.data = c;
            this.lblZz.string = i18n.t("SERVANT_TALENT") + _;
        }
    },
    updateJiban() {
        var t = this.node.openParam,
            e = n.jibanProxy.getHeroJbLv(t.heroid).level % 1e3;
        this.luckImg.setValue(5, e);
        var o = n.jibanProxy.getHeroNextJb(t.heroid, e);
        this.lblJiBan.string =
            n.jibanProxy.getHeroJB(t.heroid) + "/" + o.yoke;
    },
    onClickClose() {
        a.utils.closeView(this);
    },
    onClickGift() {
        var t = this.node.openParam;
        n.servantProxy.curSelectId = t.heroid;
        a.utils.openPrefabView("servant/ServantGiftView");
    },
    onClickLook() {
        this.tablentNode.active = !0;
    },
    onCloseTablent() {
        this.tablentNode.active = !1;
    },
});
