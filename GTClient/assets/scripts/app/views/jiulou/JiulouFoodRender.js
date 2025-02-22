var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblname:cc.Label,
        lblscore:cc.Label,
        btnSelect:cc.Node,
        btnCancal:cc.Node,
        itemSlot:n,
    },

    ctor(){

        this.itemData = null;
        this.slotData = null;
    },
    
    showData() {
        this.itemData = this._data;
        if (this.itemData) {
            var t = localcache.getItem(localdb.table_item, this.itemData.id);
            this.slotData = new l.ItemSlotData();
            this.slotData.id = t.id;
            this.itemSlot.data = t;
            this.lblname.string = t.name;
            var e = localcache.getItem(localdb.table_feastFood, t.id);
            this.lblscore.string = i18n.t("JIU_LOU_JI_FEN_JIA_CHENG", {
                value: e.addition / 100
            });
            var o = r.jiulouProxy.selectFood.indexOf(this.itemData);
            this.btnSelect.active = o < 0;
            this.btnCancal.active = o >= 0;
        }
    },
    onClickSelect() {
        if (r.jiulouProxy.selectFood.length < 3) {
            r.jiulouProxy.selectFood.push(this.itemData);
            facade.send("JIU_LOU_SELECT_FOOD_UPDATE");
        } else a.alertUtil.alert(i18n.t("JIU_LOU_FOOD_MAX"));
    },
    onClickCancal() {
        r.jiulouProxy.selectFood.splice(r.jiulouProxy.selectFood.indexOf(this.itemData), 1);
        facade.send("JIU_LOU_SELECT_FOOD_UPDATE");
    },
});
