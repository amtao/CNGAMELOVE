var i = require("Initializer");
var n = require("Utils");
var l = require("UIUtils");
var r = require("PlayerProxy");
var a = require("UrlLoad");
var s = require("JyWPItem");
var c = require("LabelShadow");
var _ = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblIntell: cc.Label,
        lblPolit: cc.Label,
        lblMeili: cc.Label,
        lblSilver: cc.Label,
        lblFood: cc.Label,
        lblSoldier: cc.Label,
        lblIntellGet: cc.Label,
        lblFoodGet: cc.Label,
        lblMeiliGet: cc.Label,
        lblCost: cc.Label,
        lblFoodTime: cc.Label,
        lblMeiliTime: cc.Label,
        lblIntellTime: cc.Label,
        nodeOneKeyBtn: cc.Node,
        lblSilverName: cc.Label,
        lblFoodName: cc.Label,
        lblSoldName: cc.Label,
        urlSilver: a,
        urlFood: a,
        urlSold: a,
        coinItems: [s],
        foodItems: [s],
        armyItems: [s],
        weipais: [cc.Node],
        btns: [cc.Button],
        btnOneKey: cc.Button,
        countdownColor: cc.Color,
        norColor: cc.Color,
    },
    ctor() {
        this.lastData = new r.RoleData();
    },
    onLoad() {
        facade.subscribe("PLAYER_USER_UPDATE", this.onUpdateUser, this);
        facade.subscribe(i.jingyingProxy.JINGYING_ARMY, this.onUpdateArmy, this);
        facade.subscribe(i.jingyingProxy.JINGYING_FOOD, this.onUpdateFood, this);
        facade.subscribe(i.jingyingProxy.JINGYING_COIN, this.onUpdateCoin, this);
        facade.subscribe(i.jingyingProxy.JINGYING_WIN, this.onUpdateWin, this);
        facade.subscribe(i.jingyingProxy.JINGYING_WEIPAI, this.updateWpItems, this);
        this.onUpdateUser();
        this.onUpdateArmy();
        this.onUpdateCoin();
        this.onUpdateFood();
        this.onUpdateWin();
        this.updateWpItems();
        this.btnOneKey.interactable = i.playerProxy.userData.level >= 8 || i.playerProxy.userData.vip > 0;
        var t = i.playerProxy.userData.level,
        e = i.timeProxy.getLoacalValue("JINGYING_SHOW_ITEM"),
        o = localcache.getItem(localdb.table_officer, t);
        if ((n.stringUtil.isBlank(e) || parseInt(e) < t) && t > 1 && !n.stringUtil.isBlank(o.workchange)) {
            this.showItem(t - 1);
            this.scheduleOnce(this.showEffect, 0.5);
            this.scheduleOnce(this.showCurItem, 0.7);
            n.alertUtil.alert(o.workchange);
        } else this.showCurItem();
    },
    showEffect() {
        n.utils.showNodeEffect(this.node, 2);
    },
    showCurItem() {
        var t = i.playerProxy.userData.level;
        this.showItem(t);
        i.timeProxy.saveLocalValue("JINGYING_SHOW_ITEM", t + "");
    },
    showItem(t) {
        for (var e = localcache.getGroup(localdb.table_jyBase, "guanid", t > 6 ? 6 : t), o = 0; e && o < e.length; o++) switch (e[o].type) {
        case 2:
            this.lblSilverName.string = e[o].name;
            this.urlSilver.url = l.uiHelps.getJYPic(e[o].pic);
            break;
        case 3:
            this.lblFoodName.string = e[o].name;
            this.urlFood.url = l.uiHelps.getJYPic(e[o].pic);
            break;
        case 4:
            this.lblSoldName.string = e[o].name;
            this.urlSold.url = l.uiHelps.getJYPic(e[o].pic);
        }
    },
    getText(t) {
        if (t) {
            return t[Math.floor(Math.random() * t.length)].txt;
        }
        return "";
    },
    onUpdateUser() {
        l.uiUtils.showNumChange(this.lblSilver, this.lastData.coin, i.playerProxy.userData.coin);
        l.uiUtils.showNumChange(this.lblFood, this.lastData.food, i.playerProxy.userData.food);
        l.uiUtils.showNumChange(this.lblSoldier, this.lastData.army, i.playerProxy.userData.army);
        this.lblIntell.string = n.utils.formatMoney(i.playerProxy.userEp.e2);
        this.lblPolit.string = n.utils.formatMoney(i.playerProxy.userEp.e3);
        this.lblMeili.string = n.utils.formatMoney(i.playerProxy.userEp.e4);
        this.lblCost.string = n.utils.formatMoney(i.playerProxy.userEp.e4);
        this.lastData.coin = i.playerProxy.userData.coin;
        this.lastData.food = i.playerProxy.userData.food;
        this.lastData.army = i.playerProxy.userData.army;
        this.updateGet();
    },
    updateGet() {
        this.lblIntellGet.string = n.utils.formatMoney(i.playerProxy.userEp.e2 + 1e3 + i.jingyingProxy.getWeipaiAdd(2));
        this.lblFoodGet.string = n.utils.formatMoney(i.playerProxy.userEp.e3 + 1e3 + i.jingyingProxy.getWeipaiAdd(3));
        this.lblMeiliGet.string = n.utils.formatMoney(i.playerProxy.userEp.e4 + 1e3 + i.jingyingProxy.getWeipaiAdd(4));
    },
    onUpdateArmy() {
        this.updateTime(i.jingyingProxy.army, this.lblMeiliTime, this.btns[2]);
    },
    onUpdateCoin() {
        this.updateTime(i.jingyingProxy.coin, this.lblIntellTime, this.btns[0]);
    },
    onUpdateFood() {
        this.updateTime(i.jingyingProxy.food, this.lblFoodTime, this.btns[1]);
    },
    updateTime(t, e, o) {
        t.num < t.max ? l.uiUtils.countDown(t.next, e,
        function() {
            i.playerProxy.sendAdok(t.label);
        },
        0 == t.num) : e.unscheduleAllCallbacks();
        o.node.active = t.num > 0;
        t.num > 0 && (e.string = i18n.t("COMMON_NUM", {
            f: t.num,
            s: t.max
        }));
        if (t.num > 0){
            e.node.color = this.norColor;
        }
        else{
            e.node.color = this.countdownColor;
        }
    },
    onClickClost() {
        // change new guide --2020.08.11
        // let max = i.taskProxy.mainTask.max;
        // let num = i.taskProxy.mainTask.num;
        // if(num >= max)
        // {
        //     facade.send(i.guideProxy.UPDATE_TRIGGER_GUIDE, {
        //         type: 4,
        //         value: i.taskProxy.mainTask.id
        //     });
        // }
        n.utils.closeView(this);
    },
    onClickJingying(t, e) {
        var o = parseInt(e) + 1;
        if (0 != i.jingyingProxy.getCount(o)) if (4 == o && i.playerProxy.userData.food < i.playerProxy.userEp.e4 + i.jingyingProxy.getWeipaiAdd(4)) n.alertUtil.alertItemLimit(3);
        else {
            i.jingyingProxy.sendJingying(o);
            var r = t.getLocation(),
            a = new cc.Vec2();
            a.x = r.x - 50;
            a.y = r.y + 460;
            l.getEffectUtils.show(a, r, l.uiHelps.getResIcon(o));
        } else {
            var s = n.utils.getParamInt("jy_cost_item_silver");
            3 == o && (s = n.utils.getParamInt("jy_cost_item_food"));
            4 == o && (s = n.utils.getParamInt("jy_cost_item_soldier"));
            var c = i.bagProxy.getItemCount(s);
            c < 1 ? n.alertUtil.alertItemLimit(s) : n.utils.showConfirmItemMore(i18n.t("ZHENGWU_USE_ITEM", {
                n: i.playerProxy.getKindIdName(1, s)
            }), s, c,
            function(t) {
                t <= c ? i.jingyingProxy.sendJYL(o, t) : n.alertUtil.alertItemLimit(s);
            });
        }
    },
    onClickOneKey() {
        i.playerProxy.userData.level < 8 && i.playerProxy.userData.vip < 1 ? n.alertUtil.alert18n("JINGYING_ONE_KEY_TIP") : i.jingyingProxy.getCount(4) > 0 && i.playerProxy.userData.food < i.playerProxy.userEp.e4 + i.jingyingProxy.getWeipaiAdd(4) ? n.alertUtil.alertItemLimit(3) : i.jingyingProxy.sendJingyingAll();
    },
    onUpdateWin() {
        var t = i.jingyingProxy.win;
        if (t && 0 != t.heroid && 0 == t.get && 0 != t.id) {
            for (var e = localcache.getItem(localdb.table_hero, t.heroid), o = localcache.getItem(localdb.table_jyevent, t.id), l = localcache.getList(localdb.table_jytext), r = [], a = 0; a < l.length; a++)(l[a].herochar != e.disposition && 0 != l[a].herochar) || l[a].type != o.type || r.push(l[a]);
            var s = null;
            1 == r.length ? (s = r[0]) : r.length > 1 && (s = r[Math.floor(Math.random() * r.length)]);
            if (s && !n.stringUtil.isBlank(s.story) && i.playerProxy.getStoryData(s.story)) {
                i.playerProxy.addStoryId(s.story);
                n.utils.openPrefabView("StoryView", !1, {
                    heroid: t.heroid,
                    type: 1
                });
            }
        }
    },
    updateWpItems() {
        this.updateGet();
        for (var t = this.getWpItems(2), e = !1, o = 0; o < t.length; o++) {
            this.coinItems[o].data = t[o];
            e = e || 0 == t[o].islock;
        }
        this.floatWeipai(this.weipais[0], e);
        e = !1;
        t = this.getWpItems(3);
        for (o = 0; o < t.length; o++) {
            this.foodItems[o].data = t[o];
            e = e || 0 == t[o].islock;
        }
        this.floatWeipai(this.weipais[1], e);
        e = !1;
        t = this.getWpItems(4);
        for (o = 0; o < t.length; o++) {
            this.armyItems[o].data = t[o];
            e = e || 0 == t[o].islock;
        }
        this.floatWeipai(this.weipais[2], e);
    },
    floatWeipai(t, e) {
        t.active = e;
        if (e) {
            t.stopAllActions();
            t.y = 0;
            l.uiUtils.floatPos(t, 0, 10, 2 * Math.random() + 2);
        }
    },
    getWpItems(t) {
        var e = [];
        if (null != i.jingyingProxy.weipai) {
            e = i.jingyingProxy.weipai.coin;
            3 == t ? (e = i.jingyingProxy.weipai.food) : 4 == t && (e = i.jingyingProxy.weipai.army);
        }
        for (var o = [], n = 0; n < 2; n++) if (n < e.length) {
            var l = i.servantProxy.getHeroData(e[n]);
            if (l) o.push(l);
            else {
                var r = {
                    islock: 0
                };
                o.push(r);
            }
        } else { (r = {}).islock = i.jingyingProxy.isCanOpenWeipai(n + 1, t) ? 0 : 1;
            o.push(r);
        }
        return o;
    },
    onClickGo() {
        _.funUtils.openView(_.funUtils.recharge.id, {
            type: 1
        });
    },
});
