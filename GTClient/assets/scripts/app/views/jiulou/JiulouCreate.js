var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("JiulouCreateItem");
var a = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
        item1:r,
        item2:r,
        itemArr:[a],
        jiaImg:cc.Node,
        guanImg:cc.Node,
        lblCount:cc.Label,
    },

    ctor(){
        this.jiaCount = 0;
        this.guanCount = 0;
    },
    onLoad() {
        facade.subscribe("JIU_LOU_SELECT_FOOD_UPDATE", this.onFoodUpdate, this);
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        this.item1.data = localcache.getItem(localdb.table_feast, 1);
        this.item2.data = localcache.getItem(localdb.table_feast, 2);
        this.item2.node.active = !1;
        this.jiaCount = i.utils.getParamInt("jiulou_nor_count");
        this.guanCount = i.utils.getParamInt("jiulou_guan_count");
        var t = this.jiaCount - n.jiulouProxy.yhType.count;
        this.lblCount.string = t > 0 ? i18n.t("JIU_LOU_JIA_YAN_FREE", {
            num: t
        }) : "";
    },
    onClickChange() {
        if (this.item1.node.active) {
            this.item1.node.active = !1;
            this.item2.node.active = !0;
            this.jiaImg.active = !1;
            this.guanImg.active = !0;
            this.lblCount.string = "";
        } else {
            this.item1.node.active = !0;
            this.item2.node.active = !1;
            this.jiaImg.active = !0;
            this.guanImg.active = !1;
            var t = this.jiaCount - n.jiulouProxy.yhType.count;
            this.lblCount.string = t > 0 ? i18n.t("JIU_LOU_JIA_YAN_FREE", {
                num: t
            }) : "";
        }
        n.jiulouProxy.selectFood = [];
        this.onFoodUpdate();
    },
    onFoodUpdate() {
        for (var t = 0; t < this.itemArr.length; t++) if (t < n.jiulouProxy.selectFood.length) {
            var e = new l.ItemSlotData();
            e.id = n.jiulouProxy.selectFood[t].id;
            this.itemArr[t].data = e;
        } else this.itemArr[t].data = null;
    },
    onClickFood() {
        i.utils.openPrefabView("jiulou/JiulouFoodSelect");
    },
    onClickClost() {
        n.jiulouProxy.selectFood = [];
        i.utils.closeView(this);
    },
    onItemUpdate() {
        this.item1.node.active ? (this.item1.data = localcache.getItem(localdb.table_feast, 1)) : this.item2.node.active && (this.item2.data = localcache.getItem(localdb.table_feast, 2));
    },
});
