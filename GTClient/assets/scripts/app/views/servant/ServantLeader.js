var i = require("Utils");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("ServantLeaderItem");
var a = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCurLv:cc.Label,
        lblCurEff:cc.Label,
        lblNextLv:cc.Label,
        lblNextEff:cc.Label,
        lblNum:cc.Label,
        lblDes:cc.Label,
        itemSlot:n,
        info:cc.Node,
        lock:cc.Node,
        upNode:cc.Node,
        lblMaxLv:cc.Label,
        btnUp:cc.Button,
        leaderItems:[r],
        danyao:cc.Node,
        shuxing:cc.Node,
    },

    ctor() {
        this.curHero = null;
    },
    onLoad() {
        facade.subscribe("PLAYER_USER_UPDATE", this.showData, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.updateCount, this);
        this.curHero = this.node.openParam;
        var t = l.servantProxy.isActivedLeader(l.servantProxy.curSelectId);
        this.info.active = t;
        this.lock.active = !t;
        if (t) this.showData();
        else {
            var e = l.servantProxy.getLeadSys(l.servantProxy.curSelectId);
            this.lblDes.string = i18n.t("LEADER_ACTIVITE_DES", {
                num: e.activation.length
            });
            if (e) for (var o = 0; o < e.activation.length; o++) if (o < this.leaderItems.length) {
                var i = localcache.getItem(localdb.table_hero, e.activation[o]);
                this.leaderItems[o].data = i;
            } else this.leaderItems[o].data = null;
        }
    },
    showData() {
        if (this.curHero) {
            var t = l.servantProxy.getLeadLv(this.curHero.id, this.curHero.leadlv);
            if (null == t) return;
            var e = l.servantProxy.getLeadLv(this.curHero.id, this.curHero.leadlv, !0);
            this.lblCurLv.string = t.level + "";
            this.lblCurEff.string = "" + t.ep;
            // this.lblCurAdd.string = t.drug / 100 + "%";
            this.lblNextLv.string = e ? e.level + "": i18n.t("LEADER_MAX_LEVEL");
            this.lblNextEff.string = e ? "" + e.ep: "" + t.ep;
            // this.lblNextAdd.string = e ? e.drug / 100 + "%": t.drug / 100 + "%";
            var o = localcache.getItem(localdb.table_item, t.itemid);
            this.itemSlot.data = o;
            var i = l.bagProxy.getItemCount(t.itemid);
            this.upNode.active = null != e;
            this.lblMaxLv.node.active = null == e;
            this.lblNum.string = e ? i18n.t("LEADER_NEED_NUM", {
                num: e ? e.cost: 0,
                count: i
            }) : i18n.t("LEADER_MAX_LEVEL");
            if (e) {
                this.shuxing.active = 0 != e.ep && 0 != t.ep;
                this.danyao.active = 0 != e.drug && 0 != t.drug;
            }
        }
    },
    updateCount() {
        var t = l.servantProxy.getLeadLv(this.curHero.id, this.curHero.leadlv);
        if (null != t) {
            var e = l.servantProxy.getLeadLv(this.curHero.id, this.curHero.leadlv, !0),
            o = l.bagProxy.getItemCount(t.itemid);
            this.lblNum.string = e ? i18n.t("LEADER_NEED_NUM", {
                num: e ? e.cost: 0,
                count: o
            }) : i18n.t("LEADER_MAX_LEVEL");
        }
    },
    onClickUp() {
        var t = l.servantProxy.getLeadLv(this.curHero.id, this.curHero.leadlv);
        if (null != t) {
            var e = l.servantProxy.getLeadLv(this.curHero.id, this.curHero.leadlv, !0);
            l.bagProxy.getItemCount(t.itemid) < e.cost ? i.alertUtil.alertItemLimit(t.itemid) : l.servantProxy.sendLeaderUp(this.curHero.id);
        }
    },
    onClickGo() {
        var t = l.servantProxy.getLeadSys(this.curHero.id);
        a.funUtils.openView(t.iconopen);
        i.utils.closeView(this);
        i.utils.closeNameView("servant/ServantView");
        i.utils.closeNameView("servant/ServantListView");
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
