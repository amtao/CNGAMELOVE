var i = require("Utils");
var n = require("Initializer");
var l = require("UserUpTip");
var a = require("UserOfficeItem");
var s = require("TimeProxy");
var _ = require("List");
var d = require("ChengHaoItem");
var u = require("Config");
var UrlLoad = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        lblId: cc.Label,
        lblName: cc.Label,
        lblShili: cc.Label,
        lblWuli: cc.Label,
        lblZhili: cc.Label,
        lblZhengzhi: cc.Label,
        lblMeili: cc.Label,
        lblExp: cc.Label,
        progress: cc.ProgressBar,
        scroll: cc.ScrollView,
        tip: l,
        roleSpine: UrlLoad,
        itemNode: cc.Node,
        officeItem: a,
        headItem: UrlLoad,
        detail: cc.Node,
        list: _,
        chengHaoItem: d,
        lblWuNode: cc.Node,
        chenghaoNode: cc.Node,
        chenghaoParentNode: cc.Node,
    },
    ctor() {
        this.officeItems = null;
        this.isFirst = !0;
    },
    onLoad() {
        facade.subscribe(n.playerProxy.PLAYER_USER_UPDATE, this.onUseData, this);
        facade.subscribe(n.playerProxy.PLAYER_EP_UPDATE, this.updateEpShow, this);
        facade.subscribe("USER_CLICK_OFFICETYPE", this.onClickOfficeType, this);
        facade.subscribe("USER_CLICK_OFFICE", this.onClickOfficeItem, this);
        facade.subscribe(n.playerProxy.PLAYER_LEVEL_UPDATE, this.updateRoleShow, this);
        facade.subscribe(n.playerProxy.PLAYER_SHOW_CHANGE_UPDATE, this.updateRoleShow, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        facade.subscribe(n.playerProxy.PLAYER_ADDITION_UPDATE, this.onProDetail, this);
        this.tip.node.active = !1;
        this.officeItem.node.active = !1;
        //this.chenghaoParentNode.active = u.Config.isShowChengHao && s.funUtils.isOpenFun(s.funUtils.chenghao);
        this.itemNode.removeAllChildren();
        this.onUseData();
        this.updateEpShow();
        // this.roleSpineIcon.updatePlayerShow();
        n.playerProxy.loadUserHeadPrefab(this.headItem,n.playerProxy.headavatar);
        this.updateRoleShow();
    },

    updateRoleShow() {
        n.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
    },
    updateItems() {
        null == this.officeItems && (this.officeItems = []);
        if (this.officeItems.length >= n.playerProxy.userData.level + 1) {
            this.updateItemShow();
            this.unscheduleAllCallbacks();
            this.scroll.scrollToTop();
        } else {
            var t = localcache.getItem(localdb.table_officer, this.officeItems.length + 1);
            if (null != t) {
                if (this.officeItems.length < n.playerProxy.userData.level + 1 && null != t) {
                    var e = cc.instantiate(this.officeItem.node),
                    o = e.getComponent(a);
                    if (o) {
                        o.data = t;
                        e.active = !0;
                        this.itemNode.addChild(e);
                        this.officeItems.push(o);
                    }
                }
                this.scroll.scrollToTop();
            } else this.unscheduleAllCallbacks();
        }
    },
    updateItemShow() {
        if (null != this.officeItems) for (var t = 0; t < this.officeItems.length; t++) this.officeItems[t].data = this.officeItems[t].data;
    },
    onClickOfficeType(t) {
        if (t) {
            this.tip.node.active = !0;
            this.tip.setOne(t);
        }
    },
    onClickOfficeItem(t) {
        if (t) {
            this.tip.node.active = !0;
            var e = localcache.getItem(localdb.table_officer, t.id - 1);
            this.tip.setMode(e || t);
        }
    },
    onUseData() {

        let vip2 = localcache.getItem(localdb.table_vip2, n.playerProxy.userData.vip);
        this.lblName.string = vip2.vip_name

        var t = n.playerProxy.userData,
        e = localcache.getItem(localdb.table_officer, t.level);
        this.lblName.string = t.name;
        this.lblId.string = t.uid + "";
        this.lblExp.string = i18n.t("COMMON_NUM", {
            f: t.exp,
            s: e.need_exp
        });
        var o = t.exp / e.need_exp;
        o = o > 1 ? 1 : o;
        this.progress.progress = o;
        // if (u.Config.isShowChengHao && s.funUtils.isOpenFun(s.funUtils.chenghao)) {
        //     var i = localcache.getItem(localdb.table_fashion, t.chenghao);
        //     this.chenghaoNode.active = null != i;
        //     this.lblWuNode.active = !this.chenghaoNode.active;
        //     this.chengHaoItem.data = i;
        // }
        this.updateItemShow();
        this.schedule(this.updateItems, 0.05);
    },
    updateEpShow() {
        var t = n.playerProxy.userEp;
        this.lblMeili.string = t.e4 + "";
        this.lblWuli.string = t.e1 + "";
        this.lblZhili.string = t.e2 + "";
        this.lblZhengzhi.string = t.e3 + "";
        this.lblShili.string = t.e1 + t.e2 + t.e3 + t.e4 + "";
    },
    onClickOpen(t, e) {
        s.funUtils.openViewUrl(e);
    },
    onClickUp() {
        var t = n.playerProxy.userData,
        e = localcache.getItem(localdb.table_officer, t.level);
        if (null != e) {
            for (var o = i.stringUtil.isBlank(e.condition) ? [] : e.condition.split("|"), l = 0; l < o.length; l++) {
                var r = localcache.getItem(localdb.table_officerType, o[l]);
                if (!n.playerProxy.officeLvIsOver(r)) {
                    n.playerProxy.officeOpen(r);
                    i.alertUtil.alert(n.playerProxy.getOfficeLvError(r));
                    return;
                }
            }
            null == e || t.exp < e.need_exp ? i.alertUtil.alert18n("USER_MW_VALUE") : t.level != n.playerProxy.getMaxLv() ? n.playerProxy.sendUserUp() : i.alertUtil.alert18n("USER_MW_MAX");
        } else i.alertUtil.alert18n("COMMON_DATA_ERROR");
    },
    onClickClost() {
        i.utils.closeView(this, !0);
    },
    onClickRenMai() {
        //i.utils.openPrefabView("renmai/RenMaiView");
    },
    onClickWeiWang() {
        i.utils.openPrefabView("stronger/LevelUpView");
    },
    onClickDetail() {
        n.playerProxy.sendAddition();
    },
    onClickCloseDetail() {
        this.detail.active = !1;
    },
    onProDetail() {
        this.list.data = [{
            type: 1,
            aep: n.playerProxy.addition.hero
        },
        {
            type: 2,
            aep: n.playerProxy.addition.son
        },
        {
            type: 3,
            aep: n.playerProxy.addition.clothe
        },
        {
            type: 4,
            aep: n.playerProxy.addition.card
        },
        {
            type: 5,
            aep: n.playerProxy.addition.baowu
        }];
        this.detail.active = !0;
    },
});
