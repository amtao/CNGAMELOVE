var i = require("ItemSlotUI");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        itemNode:cc.Node,
        childNode:cc.Node,
        expNode:cc.Node,
        itemSlot:i,
        lblItemName:cc.Label,
        lblTxt:cc.Label,
        lblPro:cc.Label,
        lblExp:cc.Label,
        lblLove:cc.Label,
        roleImg:a,
        content1:cc.Label,
        content2:cc.Label,
        lbLove: cc.Label,
    },

    ctor(){
        this.openData = null;
        this.curType = 0;
        this.flag = false;
    },
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.heroid);
            if (0 == t.type) {
                var o = localcache.getItem(localdb.table_item, t.itemid);
                this.lblItemName.string = o.name;
                var i = new l.ItemSlotData();
                i.itemid = o.id;
                this.itemSlot.data = i;
                this.curType = 0;
            } else if (1 == t.type) {
                this.lblTxt.string = i18n.t("WIFE_CHU_YOU_CHILD_" + t.babysex, {
                    name: e.name
                });
                this.openData = t;
                this.curType = 1;
            }
            this.roleImg.url = l.uiHelps.getServantSpine(t.heroid);
            var n = Math.ceil(2 * Math.random()),
            r = i18n.t("WIFE_CHU_YOU_ITEM_" + n).split("|");
            this.content1.string = r[0];
            this.content2.string = r[1];
            //this.lbLove.string = i18n.t("WIFE_CHU_YOU_SHU_QIN_MI", { name: e.name });
            this.lbLove.string = "";
            this.scheduleOnce(this.cdCountDonw, 1);
        }
    },
    showChild() {
        n.utils.openPrefabView("ChildShow", null, {child:1,cList:[this.openData]});
    },
    showItem() {
        r.timeProxy.floatReward();
    },
    cdCountDonw() {
        this.flag = !0;
    },
    onClickCiMing() {
        n.utils.openPrefabView("child/ChildView");
        n.utils.closeNameView("wife/WifeListView");
        n.utils.closeNameView("wife/WifeSelectView");
        n.utils.closeView(this);
    },
    onClickClose() {
        if (0 != this.flag) {
            0 == this.curType ? this.showItem() : 1 == this.curType && this.showChild(); - 1 == this.curType && n.utils.closeView(this);
            this.curType = -1;
        }
    },
});
