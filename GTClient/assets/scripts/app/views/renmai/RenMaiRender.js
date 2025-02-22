var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("Utils");
cc.Class({
    extends: i,
    properties: {
        qiyunValue:cc.Label,
        headIcon:n,
        line:cc.Node,
        lockNode:cc.Node,
        headNode:cc.Node,
        itemNode:cc.Node,
        jbValue:cc.Label,
        infoNode:cc.Node,
        effMask:cc.Node,
        arrowEff:sp.Skeleton,
        yuanEff:sp.Skeleton,
    },

    ctor(){

        this.newY = 0;
        this.posX = 0;
        this.newLine = 0;
    },
    showData() {
        var t = this._data.heroId,
        e = l.jibanProxy.getHeroSW(t),
        o = l.jibanProxy.getHeroJB(t);
        this.qiyunValue.string = i18n.t("SERVANT_ROLE_SW") + e;
        this.jbValue.string = i18n.t("SERVANT_JI_BAN_VALUE") + " " + l.jibanProxy.getHeroJB(t);
        this.headIcon.url = r.uiHelps.getServantHead(t);
        var i = parseInt(this._data.taskId);
        this.lockNode.active = !1;
        this.node.active = l.taskProxy.mainTask.id > i;
        this.line.active = l.jibanProxy.getHeroJB(t) > 0 && l.taskProxy.mainTask.id > i;
        this.headNode.active = this.infoNode.active = l.taskProxy.mainTask.id > i;
        var n = parseInt(this._data.oldJb),
        a = parseInt(this._data.oldQy);
        r.uiUtils.showNumChange(this.qiyunValue, a, e);
        r.uiUtils.showNumChange(this.jbValue, n, l.jibanProxy.getHeroJB(t));
        var s = l.jibanProxy.getHeroJB(t) / 2500 > 1 ? 1 : l.jibanProxy.getHeroJB(t) / 2500,
        c = 0.3 * s + 0.7;
        this.itemNode.scale = this.yuanEff.node.scale = c;
        this.infoNode.scale = 1 - 0.3 * s;
        var _ = 160 - 160 * (a / 999 < 1 ? a / 999 : 1),
        d = 160 * -(a / 999 < 1 ? a / 999 : 1);
        this.line.width = this.effMask.width = 160 + _;
        this.itemNode.y = d || 0;
        if (a != e) {
            this.newLine = 160 - 160 * (e / 999 < 1 ? e / 999 : 1);
            this.newY = -160 * (e / 999 < 1 ? e / 999 : 1);
            this.posX = this.itemNode.x;
            this.arrowEff.node.active = !0;
            this.arrowEff.animation = "animation";
            this.scheduleOnce(this.showChange, 1);
        }
        if (n != o) {
            this.yuanEff.node.active = !0;
            this.yuanEff.animation = "animation";
        }
    },
    showChange() {
        this.line.width = this.effMask.width = 160 + this.newLine;
        this.itemNode.runAction(cc.moveTo(1, new cc.Vec2(this.posX, this.newY)));
    },
    onClickItem() {
        var t = parseInt(this._data.taskId);
        if (l.taskProxy.mainTask.id <= t) {
            var e = localcache.getItem(localdb.table_mainTask, t);
            a.alertUtil.alert(i18n.t("REN_MAI_LOCK_HERO", {
                name: e.name
            }));
        } else {
            var o = this._data.heroId,
            i = localcache.getItem(localdb.table_hero, o);
            if (null == l.servantProxy.getHeroData(o)) a.utils.openPrefabView("servant/ServantInfo", !1, i);
            else {
                l.servantProxy.isRenMaiOpen = !0;
                a.utils.openPrefabView("servant/ServantView", !1, {
                    hero: i,
                    tab: 4
                });
            }
        }
    },
});
