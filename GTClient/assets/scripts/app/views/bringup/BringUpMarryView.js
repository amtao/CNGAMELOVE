var i = require("Initializer");
var n = require("UIUtils");
var l = require("Utils");
var r = require("ChildSpine");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblShuXing: cc.Label,
        lblFather: cc.Label,
        checkBox1: cc.Toggle,
        checkBox2: cc.Toggle,
        childSpine: r,
        input: cc.EditBox,
        lblCost: cc.Label,
        checkBox3: cc.Toggle,
        lblShenfen: cc.Label,
    },
    ctor() {
        this.curData = null;
    },
    onLoad() {
        facade.subscribe(i.bagProxy.UPDATE_BAG_ITEM, this.showItemCount, this);
        this.input.placeholder = i18n.t("SON_TO_QIN_UID");
        this.curData = this.node.openParam;
        if (this.curData) {
            this.lblName.string = this.curData.name;
            var t = this.curData.ep.e1 + this.curData.ep.e2 + this.curData.ep.e3 + this.curData.ep.e4;
            this.lblShuXing.string = t + "";
            localcache.getItem(localdb.table_wife, this.curData.mom);
            this.lblFather.string = i.playerProxy.userData.name;
            this.childSpine.setKid(this.curData.id, this.curData.sex);
            this.adult = localcache.getItem(localdb.table_adult, this.curData.honor);
            new n.ItemSlotData().itemid = this.adult.itemid;
            var e = localcache.getItem(localdb.table_item, this.adult.itemid);
            this.lblCost.string = i18n.t("SON_MARRY_COST_ITEM", {
                str: e.name
            }) + i18n.t("COMMON_NEED", {
                f: i.bagProxy.getItemCount(e.id),
                s: 1
            });
            this.lblShenfen.string = i.sonProxy.getHonourStr(this.curData.honor);
        }
    },
    showItemCount() {
        var t = localcache.getItem(localdb.table_item, this.adult.itemid);
        this.lblCost.string = i18n.t("SON_MARRY_COST_ITEM", {
            str: t.name
        }) + i18n.t("COMMON_NEED", {
            f: i.bagProxy.getItemCount(t.id),
            s: 1
        });
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onTiQin() {
        if (!this.checkBox1.isChecked || ("" != this.input.string && null != this.input.string)) if (i.bagProxy.getItemCount(this.adult.itemid) <= 0) l.alertUtil.alertItemLimit(this.adult.itemid);
        else {
            var t = this.checkBox1.isChecked ? parseInt(this.input.string) : 0;
            i.sonProxy.sendTiQin(t, 2, this.curData.id, this.checkBox3.isChecked ? 1 : 0);
            l.utils.closeView(this);
        } else l.alertUtil.alert(i18n.t("SON_TO_QIN_UID"));
    },
    onClickCheckBox1() {
        // this.checkBox1.isChecked = !0;
        // this.checkBox2.isChecked = !1;
    },
    onClickCheckBox2() {
        // this.checkBox1.isChecked = !1;
        // this.checkBox2.isChecked = !0;
    },
});
