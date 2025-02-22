var i = require("Utils");
var n = require("Initializer");
var l = require("ApiUtils");
var r = require("Config");
var a = require("List");
var s = require("RoleSpine");
var itemSlot = require("ItemSlotUI")

cc.Class({
    extends: cc.Component,
    properties: {
        btnMonth: cc.Node,
        btnYear: cc.Node,
        ylqMonth: cc.Node,
        ylqYear: cc.Node,
        buyMonth: cc.Node,
        buyYear: cc.Node,
        lblBuyYk: cc.Label,
        lblBuyNk: cc.Label,
        dailyCash: cc.Label,
        buyGetCash: cc.Label,
        yeadList: a,
        monList: a,
        nDailyCash: cc.Label,
        nBuyGetCash: cc.Label,
        lblYeadLess: cc.Label,
        lblMonLess: cc.Label,
        roleSpine: s,
        clotheRwd1: itemSlot,
        clotheRwd2: itemSlot,
    },
    ctor() {
        this.curIndex = 1;
        this.ykData = null;
        this.nkData = null;
    },
    onLoad() {
        facade.subscribe(n.monthCardProxy.MOON_CARD_UPDATE, this.onDataUpdate, this);
        n.welfareProxy.sendOrderBack();
        for (var t = 0; t < n.welfareProxy.rshop.length; t++) 2 == n.welfareProxy.rshop[t].type ? (this.ykData = n.welfareProxy.rshop[t]) : 3 == n.welfareProxy.rshop[t].type && (this.nkData = n.welfareProxy.rshop[t]);
        this.lblBuyYk.string = i18n.t("MONTH_CARD_PRICE", {
            value: this.ykData.rmb
        });
        this.lblBuyNk.string = i18n.t("MONTH_CARD_PRICE", {
            value: this.nkData.rmb
        });
        this.onClickTab();
        this.showCloth();
    },
    showCloth() {
        var t = n.playerProxy.userData,
        e = {};
        e.head = i.utils.getParamInt("clotheyear_head");
        e.ear = i.utils.getParamInt("clotheyear_ear");
        e.body = i.utils.getParamInt("clotheyear_body");
        e.animal = 0;
        e.effect = 0;
        this.roleSpine.setClothes(t.sex, t.job, t.level, e);
    },
    onClickTab() {
        var t, e, o = n.monthCardProxy.getCardData(1);
        this.buyMonth.active = null == o || 0 == o.type;
        this.btnMonth.active = o && 1 == o.type;
        this.ylqMonth.active = o && 2 == o.type;
        t = i.utils.getParamInt("mooncard_everyday");
        e = i.utils.getParamInt("mooncard_gain");
        this.dailyCash.string = t + "";
        this.buyGetCash.string = e + "";
        this.lblMonLess.string = o ? i18n.t("MONTH_CARD_LESS_DAY", {
            num: o.days
        }) : "";
        var l, r, a = n.monthCardProxy.getCardData(2);
        this.buyYear.active = null == a || 0 == a.type;
        this.btnYear.active = a && 1 == a.type;
        this.ylqYear.active = a && 2 == a.type;
        l = i.utils.getParamInt("yearcard_everyday");
        r = i.utils.getParamInt("yearcard_gain");
        this.nDailyCash.string = l + "";
        this.nBuyGetCash.string = r + "";
        this.lblYeadLess.string = a ? i18n.t("MONTH_CARD_LESS_DAY", {
            num: a.days
        }) : "";
        var s = localcache.getItem(localdb.table_yuekaReward, 1);
        if (s) {
            for (var c = null,
            _ = 0; _ < s.rwd.length; _++) {
                if (parseInt(s.rwd[_].moon) == (o && o.moon && 0 != o.moon ? o.moon: i.timeUtil.getCurMonth())) {
                    c = s.rwd[_];
                    break;
                }
            }
            var d = [];
            for (_ = 0; _ < s.rwdday.length; _++) d.push(s.rwdday[_]);
            c && !n.playerProxy.isHaveBlank(c.id) && d.push(c);
            this.monList.data = d;
            this.monList.node.x = -141 - this.monList.node.width / 2;
        }
        var u = localcache.getItem(localdb.table_yuekaReward, 2);
        if (u) {
            this.yeadList.data = u.rwdday;
            this.yeadList.node.x = 155 - this.yeadList.node.width / 2;
            this.clotheRwd1.data = u.rwd[0];
            this.clotheRwd2.data = u.rwd[1];
        }
    },
    
    onClickGetReward(t, e) {
        n.monthCardProxy.sendGetMoonCard(parseInt(e));
    },

    onBuyMonthCard() {
        if(this.ykData)
        {
            l.apiUtils.recharge(
                n.playerProxy.userData.uid,
                r.Config.serId,
                this.ykData.diamond,
                this.ykData.ormb,
                this.ykData.diamond + n.playerProxy.getKindIdName(1, 1),
                0,
                null,
                this.ykData.cpId,
                this.ykData.dollar,
                this.ykData.dc
            );
        }
        // this.ykData && l.apiUtils.recharge(n.playerProxy.userData.uid, r.Config.serId, this.ykData.diamond, this.ykData.ormb, this.ykData.diamond + n.playerProxy.getKindIdName(1, 1), 0);
    },

    onBuyYearCard() {
        if(this.nkData)
        {
            l.apiUtils.recharge(
                n.playerProxy.userData.uid,
                r.Config.serId,
                this.nkData.diamond,
                this.nkData.ormb,
                this.nkData.diamond + n.playerProxy.getKindIdName(1, 1),
                0,
                null,
                this.nkData.cpId,
                this.nkData.dollar,
                this.nkData.dc
            );
        }
        // this.nkData && l.apiUtils.recharge(n.playerProxy.userData.uid, r.Config.serId, this.nkData.diamond, this.nkData.ormb, this.nkData.diamond + n.playerProxy.getKindIdName(1, 1), 0);
    },

    onDataUpdate() {
        this.onClickTab();
    },

    onClickClose() {
        i.utils.closeView(this);
        facade.send(n.limitActivityProxy.UPDATE_CLOTHES_SHOP);
    },
});
