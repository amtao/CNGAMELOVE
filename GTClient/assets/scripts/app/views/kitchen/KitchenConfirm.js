var i = require("ItemSlotUI");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        item:i,
        face:n,
        nodeCook:cc.Node,
        nodeItem:cc.Node,
        nodeWife:cc.Node,
        btnCancel:cc.Button,
        btnWife:cc.Button,
        nodeAddWife:cc.Node,
        btnItem:cc.Button,
        nodeAddItem:cc.Node,
    },

    ctor(){
        this.curItem = null;
        this.curWife = 0;
        this.curStove = null;
        this._saveSelect = {};
    },
    onLoad() {
        facade.subscribe("KITCHEN_SELECT_WIFE", this.onSelectWife, this);
        facade.subscribe("KITCHEN_SELECT_ITEM", this.onSelectItem, this);
        facade.subscribe("KITCHEN_SELECT_STOVE", this.onSelectStove, this);
        var t = r.timeProxy.getLoacalValue("KITCHEN_WIFE_SELECT");
        this._saveSelect = l.stringUtil.isBlank(t) ? {}: JSON.parse(t);
        this.item.data = null;
    },
    onSelectStove(t) {
        this.curItem = null;
        this.curStove = t;
        this.node.active = !0;
        this.onShow();
    },
    onSelectWife(t) {
        var e = localcache.getItem(localdb.table_wife, t);
        e && (this.face.url = a.uiHelps.getWifeHead(e.res));
        this.node.active = !0;
        this.curWife = t;
        this._saveSelect && this._saveSelect[t] && this.onSelectItem(this._saveSelect[t], !1);
        this.updateX();
    },
    updateX() {
        this.nodeItem.active = 0 != this.curWife || (this.curStove && 0 != this.curStove.itemId);
        this.nodeWife.x = (null == this.curStove && 0 != this.curStove.wid) || 0 != this.curWife ? -this.nodeItem.x: 0;
    },
    onSelectItem(t, e) {
        void 0 === e && (e = !0);
        this.curItem = {
            id: t
        };
        var o = this.curItem ? localcache.getItem(localdb.table_kitchen, t) : null;
        this.item.data = {
            id: o ? o.itemid: t
        };
        this.node.active = !0;
        this.nodeAddItem.active = 0 == this.curItem.id;
        if (this._saveSelect[this.curWife] != t && e) {
            this._saveSelect[this.curWife] = t;
            r.timeProxy.saveLocalValue("KITCHEN_WIFE_SELECT", JSON.stringify(this._saveSelect));
        }
        e && this.onClickCook();
    },
    onShow() {
        var t = "";
        if (this.curStove) {
            this.curItem = 0 != this.curStove.itemId ? {
                id: this.curStove.itemId
            }: null;
            var e = localcache.getItem(localdb.table_wife, this.curStove.wid);
            t = 0 != this.curStove.wid && e ? a.uiHelps.getWifeHead(e.res) : "";
            this.curWife = this.curStove.wid;
        }
        var o = this.curItem ? localcache.getItem(localdb.table_kitchen, this.curItem.id) : null;
        o && (this.item.data = {
            id: o.itemid
        });
        this.curItem;
        var i = this.curStove && 0 != this.curStove.wid && null != this.curStove.wid;
        this.nodeAddWife.active = this.btnItem.interactable = this.btnWife.interactable = this.nodeCook.active = !i;
        this.updateX();
        this.nodeAddItem.active = null == this.curStove || 0 == this.curStove.itemId || 0 == this.curItem.id;
        this.btnCancel.node.x = i ? 0 : this.nodeItem.x;
        this.face.url = t;
    },
    onClickFood() {
        l.utils.openPrefabView("kitchen/KitItemSelect", !1, {
            id: this.curWife
        });
    },
    onClickOepn(t, e) {
        l.utils.openPrefabView(e);
    },
    onClickCook() {
        if (null != this.curItem) if (0 != this.curWife) {
            if (null != localcache.getItem(localdb.table_kitchen, this.curItem.id)) {
                r.kitchenProxy.sendStart(this.curWife, this.curStove.id, this.curItem ? this.curItem.id: 0);
                var t = r.timeProxy.getLoacalValue("KITCHEN_PARAM"),
                e = JSON.parse(t);
                null == e && (e = {});
                for (var o in e) null != e[o] && e[o].wid == this.curWife && (e[o] = null);
                var i = {};
                i.wid = this.curWife;
                i.itemId = this.curItem ? this.curItem.id: 0;
                e[this.curStove.id] = i;
                r.timeProxy.saveLocalValue("KITCHEN_PARAM", JSON.stringify(e));
                this.onClickClost();
            } else l.alertUtil.alert18n("KITCHEN_SELECT_ITEM_COUNR_LIMIT");
        } else l.alertUtil.alert18n("KITCHEN_SELECT_WIFE_LIMIT");
        else l.alertUtil.alert18n("KITCHEN_SELECT_ITEM_LIMIT");
    },
    onClickClost() {
        this.item.data = null;
        this.curItem = null;
        this.curWife = 0;
        this.curStove = null;
        this.node.active = !1;
    },
});
