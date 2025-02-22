var i = require("Utils");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("Config");
var a = require("SelectMax");
var s = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        itemSlot: n,
        lblName: cc.Label,
        lblEffect: cc.Label,
        lblOut: cc.Label,
        nodeUse: cc.Node,
        nodeSC: cc.Node,
        nodeGo: cc.Node,
        silder: a,
        nodeBg: cc.Node,
    },
    ctor() {
        this.itemInfo = null;
        //this.zwlid = 0;
    },
    onLoad() {
        this.itemInfo = this.node.openParam;
        this.nodeGo.active = false;
        this.updateShow();
        facade.subscribe("BAG_CLICK_BLANK", this.onClickClose, this);
        facade.subscribe("BAG_CLICK_HECHENG", this.onClickClose, this);
        facade.subscribe("BAG_CLICK_USE", this.onClickOther, this);
    },
    onClickOther(t) {
        this.itemInfo = t;
        this.updateShow();
    },
    updateShow() {
        var t = this.itemInfo;
        if (t) {
            //this.zwlid = i.utils.getParamInt("zw_cost_item_id");
            var e = localcache.getItem(localdb.table_item, t.id + "");
            this.itemSlot.data = t;
            if (e) {
                this.lblName.string = e.name + (r.Config.DEBUG ? "(" + t.id + ")": "");
                var o = e.explain.split("|");
                this.lblEffect.string = o.length > 1 ? o[1] : e.explain;
                this.lblOut.string = i.stringUtil.isBlank(e.source) ? i18n.t("COMMON_NULL") : e.source;
                var n = 900 != e.id && 901 != e.id && 902 != e.id;
                this.nodeUse.active = e.type && "item" == e.type[0] || ("hero" == e.type[0] && n);
                this.nodeSC.active = false;
                this.nodeGo.active = e.use && 0 != e.use && !this.nodeSC.active && !this.nodeUse.active;
            }
            var a = l.bagProxy.getItemCount(t.id);
            this.silder.node.active = this.nodeUse.active && 115 != t.id && 140 != t.id;
            this.silder.node.active && (this.silder.max = a);
            if(e.type && "hero" == e.type[0]) {
                this.silder.max = a < this.itemInfo.canUseCount ? a : this.itemInfo.canUseCount;
                if(null == this.itemInfo.heroId) {
                    this.silder.node.active = false;
                    this.nodeUse.active = false;
                }
            }
            this.nodeBg.height = this.nodeUse.active || this.nodeSC.active || this.nodeGo.active ? 360 : 260;
        }
    },
    onClickUse() {
        var t = this.itemInfo;
        if (t) if (115 == t.id) i.utils.openPrefabView("ChangeNameView", null, {
            type: 1
        });
        else if (140 == t.id) i.utils.openPrefabView("ChangeNameView", null, {
            type: 2
        });
        else if(this.silder.curValue > 0) {
            var e = localcache.getItem(localdb.table_item, t.id + "");
            if ("item" == e.type[0]) {
                if (l.bagProxy.getItemCount(t.id) < this.silder.curValue) {
                    i.alertUtil.alertItemLimit(t.id);
                    return;
                }
                l.bagProxy.sendUse(t.id, this.silder.curValue);
            } else if("hero" == e.type[0]) {
                l.bagProxy.sendUseItemHero(t.id, this.silder.curValue, this.itemInfo.heroId);
                i.alertUtil.alert(i18n.t("SERVANT_TRAIN_SUCCESS"));
            } 
            else {
                t.id == i.utils.getParamInt("zw_cost_item_id") && l.jingyingProxy.sendZwl(this.silder.curValue);
            }
        }
        this.onClickClose();
    },
    onClickGo() {
        var t = this.itemInfo;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.id + "");
            e.use && 0 != e.use && s.funUtils.openView(e.use);
        }
        i.utils.closeView(this);
        i.utils.closeNameView("bag/BagView");
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
