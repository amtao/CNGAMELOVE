var i = require("ItemSlotUI");
var n = require("List");
var l = require("Initializer");
var r = require("Utils");
var a = require("UIUtils");
var s = require("ActivityShopItem");
let goldShow = require("GoldShow");
let tip = require("Tip");
import { EItemType } from 'GameDefine';

cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
        item: s,
        lblRemain: cc.Label,
        tabNodes: [cc.Node],
        bigItem: s,
        yzcList: n,
        dhscNode: cc.Node,
        yzcNode: cc.Node,
        tabNode: cc.Node,
        yzcTitle: cc.Label,
        scGoldShow: goldShow,
        scTip: tip,
    },
    ctor() {
        this.shopData = {};
    },
    onLoad() {
        this.tabIndex = 1; // 1为兑换商城  2为星月
        facade.subscribe(l.limitActivityProxy.ACTIVITY_SHOP_UPDATE, this.onDataUpdate, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.updateShow, this);
        this.shopData = this.node.openParam;
        this.isSort = this.node.isSort;
        l.limitActivityProxy.curExchangeId = this.shopData.hid;
        this.updateShow();
        this.updateRes();
    },

    onDataUpdate(t) {
        this.shopData = t;
        this.updateShow();
    },

    updateRes: function() {
        if(0 != l.limitActivityProxy.curExchangeId && this.scGoldShow) {
            let bChange = true;
            switch(this.shopData.hid) {
                case l.limitActivityProxy.MOON_BATTLE_ID:
                    this.scTip.itemId = this.scGoldShow.res = EItemType.MoonGold;
                    break;
                case l.limitActivityProxy.CRUSH_ACT_ID:
                    this.scTip.itemId = this.scGoldShow.res = EItemType.CrushBonus;
                    break;
                case l.limitActivityProxy.Tofu_ACT_ID:
                    this.scTip.itemId = this.scGoldShow.res = EItemType.Tofu;
                    break;
                default: 
                    bChange = false;
                break;
            }
            if(bChange) {
                this.scGoldShow.onLoad();
            }
        }
    },

    updateCount() {

    },

    updateShow() {
        if (null != this.shopData && null != this.shopData.rwd && this.shopData.rwd.length > 0) {
            var t = [];
            if (this.shopData.rwd) for (var e = 1; e < this.shopData.rwd.length; e++) {
                // find 不存在默认是兑换商城
                if (!this.shopData.rwd[e].find || this.shopData.rwd[e].find === 1) {
                    t.push(this.shopData.rwd[e]);
                }
            }
            if(!this.isSort)
            {
                t.sort(function(t, e) {
                    var o = t.count > t.buy || 0 == t.count;
                    return o != (e.count > e.buy || 0 == e.count) ? o ? -1 : 1 : t.id - e.id;
                });
            }
            var c = this;
            if (null == this.shopData.stime || this.shopData.stime < r.timeUtil.second) {
                this.lblRemain.string = i18n.t("ACTHD_OVERDUE");
                6240 == l.limitActivityProxy.curExchangeId && (this.lblRemain.string = "");
            } else a.uiUtils.countDown(this.shopData.stime, this.lblRemain,
            function() {
                c.lblRemain.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
            this.item.data = this.shopData.rwd[0];
            this.updateCount();
            l.timeProxy.floatReward();
            this.list.data = t
            this.updateYueZhiChen();
        } else {
            this.list.data = null;
            this.itemSlot.data = this.itemSlot1.data = null;
            this.lblRemain.string = i18n.t("ACTHD_OVERDUE");
            this.item.data = null;
        }
    },

    // 月之尘
    updateYueZhiChen () {
        if(null == this.tabNode) return;
        var firstItem = null;
        var itemList = [];
        if (!this.shopData.rwd) return;
        for (var i = 0; i < this.shopData.rwd.length; i++) {
            if (this.shopData.rwd[i].find !== 2) continue;
            if (!firstItem) {
                firstItem = this.shopData.rwd[i];
            } else {
                itemList.push(this.shopData.rwd[i]);
            }
        }

        if(!firstItem){
            this.bigItem && (this.bigItem.node.active = false);
            this.tabNode.active = false;
            return;
        }
        this.tabNode.active = true;
        this.bigItem && (this.bigItem.data = firstItem);
        this.yzcList.data = itemList;
        if (itemList.length <= 0) return;
    },

    toggleContent () {
        this.dhscNode.active = this.tabIndex === 1;
        this.yzcNode.active = this.tabIndex !== 1;
    },

    onClickClost() {
        r.utils.closeView(this);
    },


    onTabClick (e, data) {
        this.tabIndex = parseInt(data);
        this.tabNodes.forEach((item) => {
            var selected = item.getChildByName("selected");
            selected.active = item === e.target;
        })
        this.updateShow();
        this.toggleContent();
    }

});
