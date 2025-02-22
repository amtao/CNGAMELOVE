var i = require("Utils");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        nameUrl: n,
        roleUrl: n,
        lblArr: [cc.Label],
        lbltalk: cc.Label,
        talkNode: cc.Node,
        eff1: sp.Skeleton,
    },
    ctor() {
        this.talkArr = [];
    },
    onLoad() {
        facade.subscribe("UPDATE_BAG_ITEM", this.onBagItem, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        this.hero = this.node.openParam;
        this.nameUrl.url = r.uiHelps.getStoryRoleName(this.hero.heroid);
        this.roleUrl.url = r.uiHelps.getServantSpine(this.hero.heroid);
        var t = localcache.getItem(localdb.table_yingyuantalk, this.hero.heroid);
        this.talkArr = t.gift.split("|");
        this.talkNode.active = !1;
        this.onBagItem();
    },
    onClickClose() {
        i.utils.closeView(this);
    },
    onClickTab(t, e) {
        "1" == e ? i.utils.openPrefabView("support/SupportChangeShop") : "2" == e ? i.utils.openPrefabView("support/SupportBuyShop") : "3" == e && l.supportProxy.sendLookRank();
    },
    onClickSupport(t, e) {
        var o = l.supportProxy.cfg.info.eTime,
        n = l.supportProxy.cfg.info.sTime,
        r = i.timeUtil.second;
        if (n <= r && r < o) {
            var a = localcache.getItem(localdb.table_yingyuanBuyShop, e);
            localcache.getItem(localdb.table_item, a.itemid);
            if (0 == l.bagProxy.getItemCount(a.itemid)) {
                i.utils.openPrefabView("support/SupportBuyShop");
                return;
            }
            l.supportProxy.sendGift(a.itemid, this.hero.heroid);
            this.lbltalk.string = this.talkArr[Math.floor(Math.random() * this.talkArr.length)];
            l.supportProxy.sendLookRecord(l.supportProxy.cfg.info.id);
            this.talkNode.active = !0;
            this.eff1.animation = "animation";
            this.eff1.node.active = !0;
            i.alertUtil.alert(i18n.t("SUPPORT_REN_QI_ZENG_JIA", {
                name: this.hero.name,
                value1: a.gongxian,
                value2: a.gongxian
            }));
        } else i.alertUtil.alert18n("ACTIVITY_NOT_IN_TIME");
    },
    onBagItem() {
        for (var t = [1100, 1101, 1102, 1103], e = 0; e < t.length; e++) this.lblArr[e].string = l.bagProxy.getItemCount(t[e]) + "";
    },
});
