var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UrlLoad");
var a = require("UIUtils");
var s = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblZZ: cc.Label,
        lblTC: cc.Label,
        list: i,
        btnRecharge: cc.Button,
        btnGet: cc.Button,
        roleImg: r,
        pro_1: cc.Node,
        pro_2: cc.Node,
        cList: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("FIRST_RECHARGE_UPDATE", this.onFirstRechargeUpdate, this);
        this.onFirstRechargeUpdate();
        a.uiUtils.scaleRepeat(this.pro_1, 1, 1.25);
        a.uiUtils.scaleRepeat(this.pro_2, 1, 1.25);
    },
    onFirstRechargeUpdate() {
        if (l.firstRechargeProxy.data) {
            this.btnRecharge.node.active = 0 == l.firstRechargeProxy.data.type;
            this.btnGet.node.active = 1 == l.firstRechargeProxy.data.type;
            var t = n.utils.getParamInt("first_recharge_hero"),
            e = localcache.getItem(localdb.table_hero, t);
            this.roleImg.url = a.uiHelps.getServantSpine(t);
            // this.lblTC.string = i18n.t("SERVANT_TE_CHANG") + "：" + e.specMsg;
            for (var o = 0,
            i = 0; i < e.skills.length; i++) {
                o += localcache.getItem(localdb.table_epSkill, e.skills[i].id).star;
            }
            this.lblZZ.string = i18n.t("SERVANT_ZHZZ", {
                zz: o
            });
            var r = localcache.getItem(localdb.table_shouchongReward, 1),
            s = [],
            c = [];
            for (i = 0; i < r.firstRwd.length; i++) {
                var _ = new a.ItemSlotData();
                _.id = r.firstRwd[i].id;
                _.count = r.firstRwd[i].count;
                _.kind = r.firstRwd[i].kind;
                1 == r.firstRwd[i].kind ? s.push(_) : 95 == r.firstRwd[i].kind && c.push(_);
            }
            this.list.data = s;
            this.cList.data = c;
            var d = l.voiceProxy.randomHeroVoice(t);
            d && n.audioManager.playSound("servant/" + d.herovoice, !0, !0);
        }
    },
    onClickGetReward() {
        l.firstRechargeProxy.sendGetReward();
    },
    onClickRecharge() {
        s.funUtils.openView(s.funUtils.recharge.id);
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
