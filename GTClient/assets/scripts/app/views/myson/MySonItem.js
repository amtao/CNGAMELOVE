var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("ChildSpine");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblShuXing: cc.Label,
        childSpine: r,
        lblShenFen: cc.Label,
        lblCost: cc.Label,
    },
    ctor() {
        this.itemSys = null;
    },
    showData() {
        var t = this._data;
        this.childSpine.clearKid();
        if (t) {
            this.lblName.string = t.name;
            var e = t.ep.e1 + t.ep.e2 + t.ep.e3 + t.ep.e4;
            this.lblShuXing.string = e + "";
            this.childSpine.setKid(t.id, t.sex);
            this.lblShenFen.string = n.sonProxy.getHonourStr(t.honor);
            this.curData = t;
            var o = localcache.getItem(localdb.table_adult, t.honor);
            this.itemSys = localcache.getItem(localdb.table_item, o.itemid);
            this.lblCost.string = i18n.t("SON_MARRY_COST_ITEM", {
                str: this.itemSys.name
            }) + i18n.t("COMMON_NEED", {
                f: n.bagProxy.getItemCount(this.itemSys.id),
                s: 1
            });
        }
    },
    onClickButton() {
        0 != n.bagProxy.getItemCount(this.itemSys.id) ? 1 == n.sonProxy.tiQinObj.marryType ? n.sonProxy.sendJieHun(n.sonProxy.tiQinObj.tUid, 2, n.sonProxy.tiQinObj.tSid, this.curData.id) : 2 == n.sonProxy.tiQinObj.marryType && n.sonProxy.sendAgree(n.sonProxy.tiQinObj.tUid, 2, n.sonProxy.tiQinObj.tSid, this.curData.id) : l.alertUtil.alertItemLimit(this.itemSys.id);
    },
});
