var i = require("Utils");
var n = require("List");
var l = require("Initializer");
let scItem = require("ItemSlotUI");
let scSelect = require("SelectMax");

cc.Class({
    extends: cc.Component,

    properties: {
        nodes: [cc.Node],
        lists: [n],
        nodeEmpty: cc.Node,
        nodeEmpty2: cc.Node,
        itemImg: cc.Sprite,
        mixImg: cc.Sprite,
        itemHechengBack: scItem,
        itemHechengAfter: scItem,
        select: scSelect,
        nHecheng: cc.Node,
        nPrice: cc.Node,
        nlbHecheng: cc.Node,
        lbPrice: cc.Node,
        lbLimit: cc.Label,
    },

    ctor() {},

    onLoad() {
        facade.subscribe(l.bagProxy.UPDATE_BAG_CHENGHAO, this.onUpdateChList, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_HECHENG, this.onUpdateHeList, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.onUpdateItemList, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        this.onClickTab(null, 1);
        let self = this;
        this.lists[1].selectHandle = function(data) {
            facade.send("BAG_COMPOSE_CHOOSE", data);
            self.showComposeDetail(data);
        };
    },

    onClickItem(t, e) {
        var o = e.data;
        if (o) {
            o.isNew = !1;
            e.showData();
            var n = l.bagProxy.getHecheng(o.id);
            null != n ? i.utils.openPrefabView("bag/BagHecheng", !1, n) : this.onShowBagItemDetail(o);
            for(let j = 0, jLen = this.lists.length; j < jLen; j++) {
                this.lists[j].updateRenders();
            }
        }
    },

    onShowBagItemDetail(o){
        let itemcfg = localcache.getItem(localdb.table_item, o.id + "");
        var n = 900 != o.id && 901 != o.id && 902 != o.id;
        if (itemcfg.type && "item" == itemcfg.type[0] || ("hero" == itemcfg.type[0] && n && o.heroId != null)){
            i.utils.openPrefabView("bag/BagUse", !1, o);
        }
        else{
            let count = l.bagProxy.getItemCount(o.id);
            i.utils.openPrefabView("ItemInfo", !1, {id:o.id,count:count,kind:1,openType:2});
        }
    },

    onClickTab(t, e) {
        for (var o = parseInt(e) - 1, i = 0; i < 2; i++) {
            this.nodes[i].active = i == o;
        }
        // this.lblItem.node.color = "1" == e ? this.seColor: this.norColor;
        // this.lblMix.node.color = "2" == e ? this.seColor: this.norColor;
        this.itemImg.node.active = "1" == e;
        this.mixImg.node.active = "2" == e;
        switch (o) {
        case 0:
            this.onUpdateItemList();
            break;
        case 1:
            this.onUpdateHeList();
            break;
        case 2:
            this.lists[2].data = l.bagProxy.chInfo.list;
        }
    },

    onUpdateItemList() {
        var t = l.bagProxy.getItemList();
        this.lists[0].data = t;
        this.nodeEmpty.active = 0 == l.bagProxy.itemList.length;
        this.onUpdateHeList();
    },

    onUpdateHeList() {
        null == l.bagProxy.heChengList && l.bagProxy.initHeChengList();
        let bHas = null != l.bagProxy.heChengList && l.bagProxy.heChengList.length > 0;
        this.nodeEmpty2.active = !bHas;
        this.nHecheng.active = bHas;
        if(bHas) {
            this.lists[1].data = l.bagProxy.heChengList;
            this.lists[1].selectIndex = 0;
        }
    },

    showComposeDetail(data) {
        this.composeData = data;
        if(data) {
            let need = data.need[0];
            this.itemHechengBack._data = {
                id: need.id,
            };
            this.itemHechengBack.showData();
            this.itemHechengAfter._data = {
                id: data.itemid,
            };
            this.itemHechengAfter.showData();
            this.select.max = Math.floor(l.bagProxy.getItemCount(need.id) / need.count);
            this.select.updateBtn();
        }
    },

    onClickCompose() {
        if(this.composeData) {
            l.bagProxy.sendCompose(this.composeData.itemid, this.select.curValue);
        }
    },

    onUpdateChList() {},

    onClickBlack() {
        facade.send("BAG_CLICK_BLANK");
    },

    onClickClose() {
        l.bagProxy.clearRedDot();
        i.utils.closeView(this, !0);
    },
});
