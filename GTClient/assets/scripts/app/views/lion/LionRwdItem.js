var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("ApiUtils");
var a = require("Config");
cc.Class({
    extends: i,
    properties: {
        lblIndex:cc.Label,
        lblNum:cc.Label,
        btnGet:cc.Button,
        btnYlq:cc.Button,
        btnLock:cc.Button,
        sliderItem:n,
        goldItems:[n],
        lockNode:cc.Node,
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_lion_rwd, t.id);
            this.lblIndex.string = e.name;
            this.lblNum.string = t.coin + "";
            if (l.lionProxy.isLockGold) {
                this.btnYlq.node.active = 1 == t.sGet && 1 == t.gGet;
                this.btnGet.node.active = 0 == t.sGet || 0 == t.gGet || null == t.gGt;
                this.btnGet.interactable = l.lionProxy.cfg.cons >= t.coin;
            } else {
                this.btnYlq.node.active = 1 == t.sGet;
                this.btnGet.node.active = 0 == t.sGet;
                this.btnGet.interactable = l.lionProxy.cfg.cons >= t.coin;
            }
            this.sliderItem.data = t.silver[0];
            for (var o = 0; o < this.goldItems.length; o++) o < t.gold.length && (this.goldItems[o].data = t.gold[o]);
            this.lockNode.active = !l.lionProxy.isLockGold;
            this.btnLock.node.active = !l.lionProxy.isLockGold;
        }
    },
    onClickLock() {
        for (var t = null,
        e = 0; e < l.purchaseProxy.gift.length; e++) if (l.purchaseProxy.gift[e].type == l.limitActivityProxy.LION_ID) {
            t = l.purchaseProxy.gift[e];
            break;
        }
        if (t) {
            l.lionProxy.isLockGold = !0;
            facade.send(l.lionProxy.LION_GOLD_LOCK);
            var o = 10 * t.grade + 1e6 + 1e4 * t.id;
            r.apiUtils.recharge(l.playerProxy.userData.uid, a.Config.serId, o, t.grade, i18n.t("LION_GOLD_RWD"), 0);
        }
    },
    onClickGet() {
        var t = this._data;
        l.lionProxy.sendGetRwd(t.id);
    },
});
