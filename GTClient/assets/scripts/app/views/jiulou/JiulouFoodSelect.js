var i = require("List");
var n = require("ItemSlotUI");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list:i,
        lblScore:cc.Label,
        itemArr:[n],
        lblArr:[cc.Label],
        tipNode:cc.Node,
    },

    onLoad() {
        facade.subscribe("JIU_LOU_SELECT_FOOD_UPDATE", this.onFoodUpdate, this);
        this.list.data = r.bagProxy.getFoodList();
        this.tipNode.active = 0 == r.bagProxy.getFoodList().length;
        this.onFoodUpdate();
    },
    onFoodUpdate() {
        for (var t = 0,
        e = 0; e < this.itemArr.length; e++) if (e < r.jiulouProxy.selectFood.length) {
            var o = new a.ItemSlotData();
            o.id = r.jiulouProxy.selectFood[e].id;
            this.itemArr[e].data = o;
            var i = localcache.getItem(localdb.table_feastFood, r.jiulouProxy.selectFood[e].id);
            this.lblArr[e].string = i.addition / 100 + "%";
            t += i.addition;
        } else {
            this.lblArr[e].string = "";
            this.itemArr[e].data = null;
        }
        this.lblScore.string = i18n.t("JIU_LOU_JI_FEN_JIA_CHENG_2", {
            value: t / 100
        });
        this.list.updateItemShow();
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
