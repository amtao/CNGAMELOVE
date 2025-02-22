var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
        unown: cc.Node,
        seColor: cc.Color,
        norColor: cc.Color,
        selectImg: cc.SpriteFrame,
        servantImg: cc.Sprite,
        sonImg: cc.Sprite,
        sonFeigeTip: cc.Node,
        topBtns: cc.Node,
        btnDelete: cc.Node,
        btnOneKey: cc.Node,
        scroll: cc.ScrollView,
    },
    ctor() {
        this.curIndex = "0";
    },
    onLoad() {
        facade.subscribe(l.feigeProxy.UPDATE_READ, this.updateShow, this);
        facade.subscribe("UPDATE_READ_SON", this.updateSonFeige, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        this.topBtns.active = l.feigeProxy.getSonFeige().length > 0;
        var t = this.node.openParam;
        t && t.flag ? this.onClickTab(null, "1") : l.feigeProxy.hasSonFeige() && !l.feigeProxy.getIsHaveUnread() ? this.onClickTab(null, "1") : this.onClickTab(null, "0");
        for (var e = 0; e < 200; e++) Math.ceil(2 * Math.random());
    },
    updateShow() {
        this.list.data = l.feigeProxy.getOpenFeige();
    },
    onClickClost() {
        if(l.feigeProxy.readingSonMail) {
            i.alertUtil.alert18n("SON_IS_READING_MAIL");
        } else {
            i.utils.openPrefabView("card/ArchiveView");
            i.utils.closeView(this, !0);
        }
    },
    onClickTab(t, e) {
        if (l.feigeProxy.readingSonMail) i.alertUtil.alert18n("SON_IS_READING_MAIL");
        else {
            l.feigeProxy.lookSonFeige = "1" == e;
            this.servantImg.node.active = "0" == e;
            this.sonImg.node.active = "1" == e;
            if ("0" == e) this.updateShow();
            else if ("1" == e) {
                l.feigeProxy.getSonFeige().length > 100 && i.utils.showConfirm(i18n.t("SON_FEI_GE_SHAN_CHU"),
                function() {
                    l.sonProxy.sendDeleteMail();
                });
                this.updateSonFeige();
            }
            //this.sonFeigeTip.active = "1" == e && 0 == l.feigeProxy.getSonFeige().length;
            //this.btnDelete.active = "1" == e && l.feigeProxy.getSonFeige().length > 0 && this.hasReadedMail();
            //this.btnOneKey.active = "1" == e && l.feigeProxy.getSonFeige().length > 0 && null != l.feigeProxy.getUnReadSonMail();
            //this.unown.active = "0" == e && (null == this.list.data || 0 == this.list.data.length);
            this.scroll.scrollToTop();
        }
    },
    updateSonFeige() {
        this.list.data = l.feigeProxy.sonFeigeList;
        this.btnDelete.active = l.feigeProxy.getSonFeige().length > 0 && l.feigeProxy.lookSonFeige && this.hasReadedMail();
        this.btnOneKey.active = l.feigeProxy.getSonFeige().length > 0 && null != l.feigeProxy.getUnReadSonMail();
    },
    onClickDelete() {
        l.feigeProxy.readingSonMail ? i.alertUtil.alert18n("SON_IS_READING_MAIL") : l.sonProxy.sendDeleteMail();
    },
    hasReadedMail() {
        for (var t = !1,
        e = 0; e < l.feigeProxy.sonFeigeList.length; e++) if (l.feigeProxy.sonFeigeList[e].select.length > 0) {
            t = !0;
            break;
        }
        return t;
    },
    onClickOneKeyRead() {
        if(l.playerProxy.userData.vip < 5) {
            // unlock recharge and vip --2020.07.21
            // i.alertUtil.alert18n("COMMON_NOT_OPEN");
            i.alertUtil.alert18n("SON_FEI_GE_ONE_KEY_OPEN");
        } else {
            l.feigeProxy.sendOneKeyRead()
        }
    },
    randomSelect(t) {
        var e = l.playerProxy.getEmailGroup(t.id, "group"),
        o = Math.ceil(2 * Math.random());
        return e[0]["award" + o];
    },
});
